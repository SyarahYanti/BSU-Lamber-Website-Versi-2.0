@extends('layouts.app')
@section('title', 'Detail Setoran')

@section('content')
<div class="container py-4">
    <!-- Header dengan Tombol Kembali -->
    <div class="d-flex align-items-center mb-4">
        <a href="{{ route('penjualan.index') }}" class="me-3 text-dark">
            <i class="fas fa-arrow-left fa-2x"></i>
        </a>
        <h2 class="mb-0">Detail Setoran</h2>
    </div>

    <!-- Card Bukti Penjualan -->
    <div class="row justify-content-center">
        <div class="col-lg-10">
            <div class="card shadow-lg border-0" style="border-radius: 20px; overflow: hidden;">
                <!-- Header Bukti -->
                <div class="card-body p-4" style="background: linear-gradient(135deg, #86a386ff 30%, #1f5121ff 100%);">
                    <div class="row align-items-center">
                        <div class="col-md-8">
                            <div class="text-white">
                                <h3 class="fw-bold mb-3">BUKTI SETORAN</h3>
                                <div>
                                    <strong>Tanggal Setoran</strong> : {{ $penjualan->tanggal_transaksi->format('d M Y') }} | 
                                    {{ $penjualan->tanggal_transaksi->format('l') }}
                                </div>
                            </div>
                        </div>
                        <div class="col-md-4 text-end">
                            <div class="text-white">
                                <h4 class="mb-0 fw-bold">BANK SAMPAH</h4>
                                <h5 class="mb-0">UNIT LAMBER</h5>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Info Nasabah & Pembayaran -->
                <div class="card-body p-5 bg-white">
                    <div class="row mb-4">
                        <div class="col-md-8">
                            <div class="mb-2">
                                <strong>Nama Nasabah</strong> : {{ $penjualan->nasabah->nama_nasabah ?? '-' }}
                            </div>
                            <div class="mb-2">
                                <strong>Nomor Induk</strong> : {{ $penjualan->nasabah->no_induk ?? '-' }}
                            </div>
                            <div class="mb-2">
                                <strong>Alamat</strong> : {{ $penjualan->nasabah->alamat ?? '-' }}
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="mb-2">
                                <strong>Detail Pembayaran</strong> : 
                                @if($penjualan->tipe_pembayaran == 'tabungan')
                                    <span class="badge bg-primary px-3 py-2">Simpan ke tabungan</span>
                                @else
                                    <span class="badge bg-success px-3 py-2">Tunai</span>
                                @endif
                            </div>
                        </div>
                    </div>

                    <hr class="my-4">

                    <h5 class="fw-bold mb-3">DETAIL SETORAN</h5>
                    
                    <div class="table-responsive">
                        <table class="table table-borderless">
                            <thead>
                                <tr style="border-bottom: 2px solid #000;">
                                    <th>No</th>
                                    <th>Kategori</th>
                                    <th>Sub-Jenis Sampah</th>
                                    <th>Produk</th>
                                    <th class="text-center">Berat</th>
                                    <th class="text-end">Harga</th>
                                    <th class="text-end">Total (Rp)</th>
                                </tr>
                            </thead>
                            <tbody>
                                @php
                                    $no = 1;
                                @endphp
                                @foreach($penjualan->details as $detail)
                                    <tr>
                                        <td>{{ $no++ }}</td>
                                        <td>
                                            <strong>{{ $detail->jenisSampah->kategori->nama ?? '-' }}</strong>
                                        </td>
                                        <td>
                                            <span class="badge bg-secondary" style="font-size: 0.7rem;">
                                                {{ $detail->jenisSampah->kode ?? '-' }}
                                            </span>
                                            <br>
                                            {{ $detail->jenisSampah->nama ?? '-' }}</td>
                                        <td>
                                            @if($detail->nama_produk)
                                                {{ $detail->nama_produk }}
                                            @else
                                                <span class="text-muted">-</span>
                                            @endif
                                        </td>
                                        <td class="text-center">
                                            {{ number_format($detail->berat_kg, 2) }} {{ $detail->satuan }}
                                            <br>
                                            <small class="text-muted">@ Rp {{ number_format($detail->harga_per_kg, 0, ',', '.') }}</small>
                                        </td>
                                        <td class="text-end">
                                            Rp {{ number_format($detail->harga_per_kg, 0, ',', '.') }}
                                        </td>
                                        <td class="text-end">
                                            <strong>Rp {{ number_format($detail->subtotal, 0, ',', '.') }}</strong>
                                        </td>
                                    </tr>
                                @endforeach
                            </tbody>
                            <tfoot>
                                <tr style="border-top: 2px solid #000;">
                                    <td colspan="6" class="text-end pt-3">
                                        <h6 class="mb-0"><strong>TOTAL BERAT</strong></h6>
                                    </td>
                                    <td class="text-end pt-3">
                                        @php
                                            $beratPerSatuan = $penjualan->details->groupBy('satuan')->map(function($items) {
                                                return $items->sum('berat_kg');
                                            });
                                        @endphp
                                        <h6 class="mb-0">
                                            @foreach($beratPerSatuan as $satuan => $totalBerat)
                                                <strong>{{ number_format($totalBerat, 2) }} {{ strtolower($satuan) }}</strong>
                                                @if(!$loop->last)<br>@endif
                                            @endforeach
                                        </h6>
                                    </td>
                                </tr>
                                <tr style="border-top: 2px solid #000;">
                                    <td colspan="6" class="text-end pt-3">
                                        <h5 class="mb-0"><strong>TOTAL SETORAN</strong></h5>
                                    </td>
                                    <td class="text-end pt-3">
                                        <h5 class="mb-0 text-success">
                                            <strong>Rp {{ number_format($penjualan->total_jual, 0, ',', '.') }}</strong>
                                        </h5>
                                    </td>
                                </tr>
                            </tfoot>
                        </table>
                    </div>

                    <!-- Tombol Download -->
                    <div class="text-center mt-3">
                        <a href="{{ route('penjualan.download-bukti', $penjualan->id) }}" 
                           class="btn btn-success btn-lg px-4 py-3"
                           style="border-radius: 50px; font-weight: 600;">
                            <i class="fas fa-download me-2"></i> Download Bukti PDF
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<style>
.table thead th {
    font-weight: 600;
    padding-bottom: 1rem;
}

.table tbody td {
    padding: 1rem 0.75rem;
    vertical-align: middle;
}

.badge {
    font-weight: 500;
}

.card {
    transition: all 0.3s ease;
}

.btn-lg {
    font-size: 1.1rem;
}
</style>
@endsection