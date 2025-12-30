<?php

namespace App\Http\Controllers;

use App\Models\KategoriSampah;
use App\Models\JenisSampah;
use App\Models\HargaSampah;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class KelolaHargaController extends Controller
{
    /**
     * Display list kategori dan sub-jenis
     */
    public function index()
    {
        $kategoris = KategoriSampah::with(['jenisSampahAktif' => function($query) {
            $query->with('hargaSekarang');
        }])->aktif()->get();

        return view('kelola_harga.index', compact('kategoris'));
    }

    /**
     * Form tambah kategori
     */
    public function createKategori()
    {
        return view('kelola_harga.create-kategori');
    }

    /**
     * Store kategori baru
     */
    public function storeKategori(Request $request)
    {
        $request->validate([
            'kode' => 'required|string|max:10|unique:kategori_sampahs,kode',
            'nama' => 'required|string|max:100',
            'warna' => 'required|in:danger,secondary,info,success,warning,primary',
        ]);

        KategoriSampah::create($request->all());

        return redirect()->route('kelola_harga.index')
                        ->with('success');
    }

    /**
     * Form tambah sub-jenis
     */
    public function createSubJenis(Request $request)
    {
        $kategoris = KategoriSampah::aktif()->get();
        $kategoriId = $request->kategori_id;

        return view('kelola_harga.create-sub-jenis', compact('kategoris', 'kategoriId'));
    }

    /**
     * Store sub-jenis baru
     */
    public function storeSubJenis(Request $request)
    {
        $request->validate([
            'kategori_id' => 'required|exists:kategori_sampahs,id',
            'kode' => 'required|string|max:20|unique:jenis_sampahs,kode',
            'nama' => 'required|string|max:255',
            'contoh_produk' => 'nullable|string',
            'satuan' => 'required|in:kg,pcs',
            'harga' => 'required|integer|min:0',
        ]);

        DB::beginTransaction();
        try {
            // Buat sub-jenis
            $jenisSampah = JenisSampah::create([
                'kategori_id' => $request->kategori_id,
                'kode' => $request->kode,
                'nama' => $request->nama,
                'contoh_produk' => $request->contoh_produk,
                'satuan' => $request->satuan,
            ]);

            // Buat harga untuk bulan ini
            HargaSampah::create([
                'jenis_sampah_id' => $jenisSampah->id,
                'harga_per_kg' => $request->harga,
                'tahun' => now()->year,
                'bulan' => now()->month,
            ]);

            DB::commit();

            return redirect()->route('kelola_harga.index')
                            ->with('success');
        } catch (\Exception $e) {
            DB::rollback();
            return back()->withErrors(['error' => 'Terjadi kesalahan: ' . $e->getMessage()])
                        ->withInput();
        }
    }

    /**
     * Form edit sub-jenis dan harga
     */
    public function edit($id)
    {
        $jenisSampah = JenisSampah::with(['kategori', 'semuaHarga'])->findOrFail($id);
        $kategoris = KategoriSampah::aktif()->get();

        return view('kelola_harga.edit', compact('jenisSampah', 'kategoris'));
    }

    /**
     * Update sub-jenis dan harga
     */
    public function update(Request $request, $id)
    {
        $jenisSampah = JenisSampah::findOrFail($id);

        $request->validate([
            'kategori_id' => 'required|exists:kategori_sampahs,id',
            'kode' => 'required|string|max:20|unique:jenis_sampahs,kode,' . $id,
            'nama' => 'required|string|max:255',
            'contoh_produk' => 'nullable|string',
            'satuan' => 'required|in:kg,pcs',
            'harga' => 'required|integer|min:0',
            'tahun' => 'required|integer|min:2020|max:2100',
            'bulan' => 'required|integer|min:1|max:12',
        ]);

        DB::beginTransaction();
        try {
            // Update sub-jenis
            $jenisSampah->update([
                'kategori_id' => $request->kategori_id,
                'kode' => $request->kode,
                'nama' => $request->nama,
                'contoh_produk' => $request->contoh_produk,
                'satuan' => $request->satuan,
            ]);

            // Update/Create harga untuk bulan tertentu
            HargaSampah::updateOrCreate(
                [
                    'jenis_sampah_id' => $jenisSampah->id,
                    'tahun' => $request->tahun,
                    'bulan' => $request->bulan,
                ],
                [
                    'harga_per_kg' => $request->harga,
                ]
            );

            DB::commit();

            return redirect()->route('kelola_harga.index')
                            ->with('success');
        } catch (\Exception $e) {
            DB::rollback();
            return back()->withErrors(['error' => 'Terjadi kesalahan: ' . $e->getMessage()])
                        ->withInput();
        }
    }

    /**
     * Delete sub-jenis
     */
    public function destroySubJenis($id)
    {
        try {
            $jenisSampah = JenisSampah::findOrFail($id);
            $jenisSampah->delete();

            return back()->with('success');
        } catch (\Exception $e) {
            return back()->withErrors(['error' => 'Tidak dapat menghapus. Mungkin masih ada transaksi terkait.']);
        }
    }

    /**
     * Delete kategori
     */
    public function destroyKategori($id)
    {
        try {
            $kategori = KategoriSampah::findOrFail($id);
            $kategori->delete();

            return back()->with('success');
        } catch (\Exception $e) {
            return back()->withErrors(['error' => 'Tidak dapat menghapus. Masih ada sub-jenis di kategori ini.']);
        }
    }
}