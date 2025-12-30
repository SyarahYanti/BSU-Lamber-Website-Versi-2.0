@extends('layouts.app')
@section('title', 'Detail Transaksi Tabungan')

@section('content')
<div class="container py-4">
    <div class="d-flex align-items-center gap-2 mb-4">
        <a href="{{ route('tabungan.index') }}" class="text-dark">
            <i class="fas fa-arrow-left fa-2x"></i>
        </a>
        <h3 class="mb-0">
            Detail Transaksi Tabungan
        </h3>
    </div>

    <div class="row">
        <div class="col-md-8 mx-auto">
            <div class="card shadow">
                <div class="card-header bg-success text-white">
                    <h5 class="mb-0">
                        <i class="fas fa-file-invoice-dollar"></i> 
                        Informasi Transaksi
                    </h5>
                </div>
                <div class="card-body">
                    <!-- Info Nasabah -->
                    <div class="mb-4 p-3 bg-light rounded">
                        <h6 class="text-muted mb-3">
                            <i class="fas fa-user"></i> Data Nasabah
                        </h6>
                        <div class="row">
                            <div class="col-md-6 mb-2">
                                <strong>Nama Nasabah:</strong>
                                <p class="mb-0">{{ $tabungan->nasabah->nama_nasabah ?? '-' }}</p>
                            </div>
                            <div class="col-md-6 mb-2">
                                <strong>No Induk:</strong>
                                <p class="mb-0">{{ $tabungan->nasabah->no_induk ?? '-' }}</p>
                            </div>
                        </div>
                    </div>

                    <!-- Info Transaksi -->
                    <table class="table table-borderless">
                        <tbody>
                            <tr>
                                <th width="40%" class="ps-0">
                                    <i class="fas fa-calendar text-muted"></i> Tanggal Transaksi
                                </th>
                                <td>
                                    <strong>{{ $tabungan->tanggal_transaksi->format('d F Y') }}</strong>
                                </td>
                            </tr>
                            <tr>
                                <th class="ps-0">
                                    <i class="fas fa-exchange-alt text-muted"></i> Jenis Transaksi
                                </th>
                                <td>
                                    @if($tabungan->jenis == 'setor')
                                        <span class="badge bg-success px-3 py-2">
                                            <i class="fas fa-arrow-down"></i> SETOR
                                        </span>
                                    @else
                                        <span class="badge bg-danger px-3 py-2">
                                            <i class="fas fa-arrow-up"></i> TARIK
                                        </span>
                                    @endif
                                </td>
                            </tr>

                            @if($tabungan->jenis_sampah)
                            <tr>
                                <th class="ps-0">
                                    <i class="fas fa-recycle text-muted"></i> Jenis Sampah
                                </th>
                                <td>
                                    <div class="d-flex flex-column gap-1">
                                        @foreach(explode(', ', $tabungan->jenis_sampah) as $jenis)
                                            <span class="badge bg-secondary text-start">{{ $jenis }}</span>
                                        @endforeach
                                    </div>
                                </td>
                            </tr>
                            @endif

                            @if($tabungan->berat_kg)
                            <tr>
                                <th class="ps-0">
                                    <i class="fas fa-weight text-muted"></i> Total Berat
                                </th>
                                <td>
                                    @if($tabungan->penjualan_id && $tabungan->penjualan && $tabungan->penjualan->details->count() > 0)
                                        @php
                                            $beratPerSatuan = $tabungan->penjualan->details->groupBy('satuan')->map(function($items) {
                                                return $items->sum('berat_kg');
                                            });
                                        @endphp
                                        @foreach($beratPerSatuan as $satuan => $totalBerat)
                                            <strong>{{ number_format($totalBerat, 2) }} {{ strtolower($satuan) }}</strong>
                                            @if(!$loop->last) + @endif
                                        @endforeach
                                    @else
                                        <strong>{{ number_format($tabungan->berat_kg, 2) }} kg</strong>
                                    @endif
                                </td>
                            </tr>
                            @endif

                            <tr class="border-top">
                                <th class="ps-0 pt-3">
                                    <i class="fas fa-arrow-down text-success"></i> Debit (Uang Masuk)
                                </th>
                                <td class="pt-3">
                                    @if($tabungan->debit > 0)
                                        <span class="text-success fw-bold fs-5">
                                            Rp {{ number_format($tabungan->debit, 0, ',', '.') }}
                                        </span>
                                    @else
                                        <span class="text-muted">-</span>
                                    @endif
                                </td>
                            </tr>

                            <tr>
                                <th class="ps-0">
                                    <i class="fas fa-arrow-up text-danger"></i> Kredit (Uang Keluar)
                                </th>
                                <td>
                                    @if($tabungan->kredit > 0)
                                        <span class="text-danger fw-bold fs-5">
                                            Rp {{ number_format($tabungan->kredit, 0, ',', '.') }}
                                        </span>
                                    @else
                                        <span class="text-muted">-</span>
                                    @endif
                                </td>
                            </tr>

                            <tr class="border-top bg-light">
                                <th class="ps-0 pt-3">
                                    <i class="fas fa-wallet text-primary"></i> Saldo Akhir
                                </th>
                                <td class="pt-3">
                                    <span class="text-primary fw-bold fs-5">
                                        Rp {{ number_format($tabungan->saldo, 0, ',', '.') }}
                                    </span>
                                </td>
                            </tr>

                            @if($tabungan->penjualan_id)
                            <tr class="border-top">
                                <th class="ps-0 pt-3">
                                    <i class="fas fa-link text-muted"></i> Referensi Setoran
                                </th>
                                <td class="pt-3">
                                    <a href="{{ route('penjualan.show', $tabungan->penjualan_id) }}" 
                                       class="btn btn-sm btn-outline-primary">
                                        <i class="fas fa-external-link-alt"></i> 
                                        Lihat Setoran
                                    </a>
                                </td>
                            </tr>
                            @endif
                        </tbody>
                    </table>
                </div>
                <div class="card-footer bg-white">
                    <div class="d-flex justify-content-between align-items-center">
                        <small class="text-muted">
                            <i class="fas fa-clock"></i> 
                            Dibuat: {{ $tabungan->created_at->format('d/m/Y H:i') }}
                        </small>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<style>
.badge {
    font-weight: 500;
    font-size: 0.9rem;
}

.card {
    border: none;
    border-radius: 0.5rem;
}

.card-header {
    border-top-left-radius: 0.5rem;
    border-top-right-radius: 0.5rem;
}

.table th {
    font-weight: 600;
    color: #495057;
}
</style>
@endsection