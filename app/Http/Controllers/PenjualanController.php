<?php

namespace App\Http\Controllers;

use App\Models\JenisSampah;
use App\Models\Nasabah;
use App\Models\Penjualan;
use App\Models\PenjualanDetail;
use App\Models\Tabungan;
use App\Models\KategoriSampah;
use Illuminate\Http\Request;
use Barryvdh\DomPDF\Facade\Pdf;
use App\Exports\PenjualanExport;
use Maatwebsite\Excel\Facades\Excel;
use Carbon\Carbon;

class PenjualanController extends Controller
{
    public function index(Request $request)
    {
        $query = Penjualan::with('nasabah');

        // Fitur Pencarian
        if ($request->filled('search')) {
            $search = $request->search;
            $query->whereHas('nasabah', function($q) use ($search) {
                $q->where('nama_nasabah', 'like', "%{$search}%");
            })->orWhere('id', 'like', "%{$search}%");
        }

        // Fitur Filter Tanggal
        if ($request->filled('tanggal_dari')) {
            $query->whereDate('tanggal_transaksi', '>=', $request->tanggal_dari);
        }

        if ($request->filled('tanggal_sampai')) {
            $query->whereDate('tanggal_transaksi', '<=', $request->tanggal_sampai);
        }

        // Fitur Filter Tipe Pembayaran
        if ($request->filled('tipe_pembayaran')) {
            $query->where('tipe_pembayaran', $request->tipe_pembayaran);
        }

        $penjualans = $query->latest()->paginate(10)->withQueryString();

        // Hitung statistik untuk ditampilkan
        $totalPenjualan = $query->sum('total_jual');
        $totalBerat = $query->sum('berat_total');
        $jumlahTransaksi = $query->count();

        return view('penjualan.index', compact('penjualans', 'totalPenjualan', 'totalBerat', 'jumlahTransaksi'));
    }

    public function create()
    {
        $nasabahs = Nasabah::orderBy('nama_nasabah')->get();
        $kategoris = KategoriSampah::with(['jenisSampahAktif' => function($query) {
            $query->with('hargaSekarang');
        }])->aktif()->get();
        
        return view('penjualan.create', compact('nasabahs', 'kategoris'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'nasabah_id' => 'required|exists:nasabahs,id',
            'tanggal_transaksi' => 'required|date',
            'items' => 'required|array',
            'items.*' => 'required|array',
            'items.*.*' => 'required|array',
            'items.*.*.sub_jenis_id' => 'required|exists:jenis_sampahs,id',
            'items.*.*.nama_produk' => 'nullable|string|max:255',
            'items.*.*.berat' => 'required|numeric|min:0.1',
            'tipe_pembayaran' => 'required|in:tabungan,tunai',
        ]);

        // Cek apakah ada item
        $totalItems = 0;
        foreach ($request->items as $kategoriItems) {
            $totalItems += count($kategoriItems);
        }

        if ($totalItems === 0) {
            return back()->withErrors(['items' => 'Minimal harus ada satu item sampah!'])->withInput();
        }

        // Buat transaksi penjualan
        $penjualan = Penjualan::create([
            'nasabah_id' => $request->nasabah_id,
            'tanggal_transaksi' => $request->tanggal_transaksi,
            'tipe_pembayaran' => $request->tipe_pembayaran,
            'total_jual' => 0,
            'berat_total' => 0,
        ]);

        $totalJual = 0;
        $beratTotal = 0;
        $detailSampah = [];

        // Loop setiap kategori
        foreach ($request->items as $kategoriId => $items) {
            // Loop setiap item dalam kategori
            foreach ($items as $item) {
                $subJenisId = $item['sub_jenis_id'];
                $namaProduk = $item['nama_produk'] ?? null;
                $berat = floatval($item['berat']);

                if ($berat <= 0) {
                    continue;
                }

                // Ambil data sub-jenis
                $jenisSampah = JenisSampah::with('kategori')->find($subJenisId);
                
                if (!$jenisSampah) {
                    continue;
                }

                $harga = $jenisSampah->harga_per_kg;
                
                if (!$harga || $harga <= 0) {
                    continue;
                }

                $subtotal = $berat * $harga;
                $totalJual += $subtotal;
                $beratTotal += $berat;

                // Simpan ke penjualan_details
                PenjualanDetail::create([
                    'penjualan_id' => $penjualan->id,
                    'jenis_sampah_id' => $subJenisId,
                    'nama_produk' => $namaProduk,
                    'berat_kg' => $berat,
                    'satuan' => $jenisSampah->satuan,
                    'harga_per_kg' => $harga,
                    'subtotal' => $subtotal,
                ]);

                // Format untuk tabungan
                $detailText = $jenisSampah->kategori->nama . ' - ' . $jenisSampah->nama;
                if ($namaProduk) {
                    $detailText .= ' (' . $namaProduk . ')';
                }
                $detailText .= ': ' . $berat . ' ' . $jenisSampah->satuan;
                
                $detailSampah[] = $detailText;
            }
        }

        // Validasi: pastikan ada yang tersimpan
        if ($beratTotal == 0) {
            $penjualan->delete();
            return back()->withErrors(['items' => 'Tidak ada item dengan berat yang valid!'])->withInput();
        }

        // Update total penjualan
        $penjualan->update([
            'total_jual' => $totalJual,
            'berat_total' => $beratTotal,
        ]);

        // AUTO CREATE TABUNGAN
        if ($request->tipe_pembayaran === 'tabungan') {
            $saldoSebelumnya = Tabungan::getSaldoTerakhir($request->nasabah_id);
            $saldoBaru = $saldoSebelumnya + $totalJual;

            Tabungan::create([
                'nasabah_id' => $request->nasabah_id,
                'penjualan_id' => $penjualan->id,
                'tanggal_transaksi' => $request->tanggal_transaksi,
                'jenis' => 'setor',
                'jenis_sampah' => implode(', ', $detailSampah),
                'berat_kg' => $beratTotal,
                'debit' => $totalJual,
                'kredit' => 0,
                'saldo' => $saldoBaru,
                'keterangan' => 'Setoran ID #' . $penjualan->id,
            ]);
        }

        return redirect()->route('penjualan.index')->with('success');
    }
    
    public function show(Penjualan $penjualan)
    {
        $penjualan->load([
            'nasabah', 
            'details.jenisSampah.kategori'
        ]);
        
        return view('penjualan.show', compact('penjualan'));
    }

    public function destroy(Penjualan $penjualan)
    {
        // Hapus tabungan terkait (jika ada)
        if ($penjualan->tipe_pembayaran === 'tabungan') {
            Tabungan::where('penjualan_id', $penjualan->id)->delete();
        }

        // Hapus detail terlebih dahulu
        $penjualan->details()->delete();
        
        // Hapus penjualan
        $penjualan->delete();
        
        return back()->with('success');
    }

    public function downloadBukti(Penjualan $penjualan)
    {
        $penjualan->load(['nasabah', 'details.jenisSampah']);
        
        $pdf = Pdf::loadView('penjualan.bukti', compact('penjualan'));
        $pdf->setPaper('a4', 'portrait');
        
        return $pdf->download('bukti-setoran-' . $penjualan->id . '.pdf');
    }

    // FITUR BARU: Laporan
    public function laporan(Request $request)
    {
        return view('penjualan.laporan');
    }

    // FITUR BARU: Download PDF Laporan
    public function downloadLaporanPdf(Request $request)
    {
        $request->validate([
            'periode' => 'required|in:bulan,tahun',
            'bulan' => 'required_if:periode,bulan|nullable|integer|min:1|max:12',
            'tahun' => 'required|integer|min:2024',
        ]);

        $query = Penjualan::with(['nasabah', 'details.jenisSampah']);

        if ($request->periode == 'bulan') {
            $query->whereMonth('tanggal_transaksi', $request->bulan)
                  ->whereYear('tanggal_transaksi', $request->tahun);
            
            // PERBAIKAN
            $judulPeriode = Carbon::createFromDate($request->tahun, $request->bulan, 1)->translatedFormat('F Y');
        } else {
            $query->whereYear('tanggal_transaksi', $request->tahun);
            $judulPeriode = 'Tahun ' . $request->tahun;
        }

        $penjualans = $query->orderBy('tanggal_transaksi', 'desc')->get();

        // Hitung statistik
        $totalPenjualan = $penjualans->sum('total_jual');
        $totalBerat = $penjualans->sum('berat_total');
        $jumlahTransaksi = $penjualans->count();

        // Rekap per jenis sampah
        $rekapJenis = [];
        foreach ($penjualans as $p) {
            foreach ($p->details as $detail) {
                $namaJenis = $detail->jenisSampah->nama ?? 'Tidak diketahui';
                
                if (!isset($rekapJenis[$namaJenis])) {
                    $rekapJenis[$namaJenis] = [
                        'berat' => 0,
                        'total' => 0,
                    ];
                }
                
                $rekapJenis[$namaJenis]['berat'] += $detail->berat_kg;
                $rekapJenis[$namaJenis]['total'] += $detail->subtotal;
            }
        }

        $pdf = Pdf::loadView('penjualan.laporan-pdf', compact(
            'penjualans', 
            'judulPeriode', 
            'totalPenjualan', 
            'totalBerat', 
            'jumlahTransaksi',
            'rekapJenis'
        ));
        
        $pdf->setPaper('a4', 'landscape');
        
        $filename = 'rekapitulasi-setoran-' . strtolower(str_replace(' ', '-', $judulPeriode)) . '.pdf';
        return $pdf->download($filename);
    }

    // FITUR BARU: Download Excel Laporan
    public function downloadLaporanExcel(Request $request)
    {
        $request->validate([
            'periode' => 'required|in:bulan,tahun',
            'bulan' => 'required_if:periode,bulan|nullable|integer|min:1|max:12',
            'tahun' => 'required|integer|min:2024',
        ]);

        if ($request->periode == 'bulan') {
            // PERBAIKAN
            $judulPeriode = Carbon::createFromDate($request->tahun, $request->bulan, 1)->translatedFormat('F-Y');
        } else {
            $judulPeriode = 'Tahun-' . $request->tahun;
        }

        $filename = 'rekapitulasi-setoran-' . strtolower($judulPeriode) . '.xlsx';
        
        return Excel::download(
            new PenjualanExport($request->periode, $request->bulan, $request->tahun), 
            $filename
        );
    }
}