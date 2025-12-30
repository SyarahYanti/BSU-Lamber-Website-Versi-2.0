<?php

namespace App\Http\Controllers;

use App\Models\Nasabah;
use App\Models\Tabungan;
use Illuminate\Http\Request;

class TabunganController extends Controller
{
    public function index(Request $request)
    {
        $query = Tabungan::with('nasabah');

        if ($request->filled('search')) {
            $search = $request->search;
            $query->whereHas('nasabah', function($q) use ($search) {
                $q->where('nama_nasabah', 'like', "%{$search}%")
                  ->orWhere('no_induk', 'like', "%{$search}%");
            });
        }

        if ($request->filled('tanggal_dari')) {
            $query->whereDate('tanggal_transaksi', '>=', $request->tanggal_dari);
        }

        if ($request->filled('tanggal_sampai')) {
            $query->whereDate('tanggal_transaksi', '<=', $request->tanggal_sampai);
        }

        if ($request->filled('jenis')) {
            $query->where('jenis', $request->jenis);
        }

        $tabungans = $query->orderBy('tanggal_transaksi', 'desc')
                           ->orderBy('created_at', 'desc')
                           ->paginate(20)
                           ->withQueryString();

        $nasabahs = Nasabah::aktif()->orderBy('nama_nasabah')->get();

        return view('tabungan.index', compact('tabungans', 'nasabahs'));
    }

    public function show($id)
    {
        $tabungan = Tabungan::with('nasabah')->findOrFail($id);
        
        return view('tabungan.show', compact('tabungan'));
    }

    public function tarik()
    {
        $nasabahs = Nasabah::aktif()->orderBy('nama_nasabah')->get();
        
        foreach ($nasabahs as $nasabah) {
            $nasabah->saldo = Tabungan::getSaldoTerakhir($nasabah->id);
        }

        return view('tabungan.tarik', compact('nasabahs'));
    }

    public function storeTarik(Request $request)
    {
        $request->validate([
            'nasabah_id' => 'required|exists:nasabahs,id',
            'tanggal_transaksi' => 'required|date',
            'jumlah' => 'required|integer|min:1',
        ]);

        $nasabah = Nasabah::findOrFail($request->nasabah_id);
        $saldoSekarang = Tabungan::getSaldoTerakhir($request->nasabah_id);

        if ($request->jumlah > $saldoSekarang) {
            return back()
                ->withErrors(['jumlah' => 'Jumlah penarikan melebihi saldo tabungan!'])
                ->withInput();
        }

        $saldoBaru = $saldoSekarang - $request->jumlah;

        Tabungan::create([
            'nasabah_id' => $request->nasabah_id,
            'tanggal_transaksi' => $request->tanggal_transaksi,
            'jenis' => 'tarik',
            'debit' => 0,
            'kredit' => $request->jumlah,
            'saldo' => $saldoBaru,
        ]);

        return redirect()
            ->route('tabungan.index')
            ->with('success', 'Penarikan tabungan berhasil! Nasabah: ' . $nasabah->nama_nasabah);
    }

    // METHOD BARU: Hapus transaksi tabungan
    public function destroy($id)
    {
        $tabungan = Tabungan::findOrFail($id);
        $tabungan->delete();

        return back()->with('success', 'Transaksi berhasil dihapus!');
    }

    public function getSaldo($nasabah_id)
    {
        $saldo = Tabungan::getSaldoTerakhir($nasabah_id);
        return response()->json(['saldo' => $saldo]);
    }
}