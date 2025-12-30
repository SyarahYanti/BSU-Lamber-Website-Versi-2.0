@extends('layouts.app')
@section('title', 'Riwayat Pesanan - ' . $nasabah->nama_nasabah)

@section('content')
<div class="container py-4">
    <!-- Header Kembali + Judul -->
    <div class="d-flex align-items-center mb-4">
        <a href="{{ url()->previous() }}" class="me-4 text-dark">
            <i class="fas fa-arrow-left fa-2x"></i>
        </a>
        <h2 class="mb-0">Riwayat Setoran</h2>
    </div>

    <!-- Info Nasabah -->
    <div style="margin-left: 56px; margin-bottom: 30px;">
        <div class="mb-2">
            <strong>Nama Nasabah</strong> : {{ $nasabah->nama_nasabah }}
        </div>
        <div class="mb-2">
            <strong>Nomor Induk</strong> : {{ $nasabah->no_induk ?? '-' }}
        </div>
        <div class="mb-2">
            <strong>Alamat</strong> : {{ $nasabah->alamat ?? '-' }}
        </div>
    </div>

    <!-- Filter Bulan -->
    <div class="mb-4" style="margin-left: 56px;">
        <form action="{{ route('nasabah.riwayat', $nasabah->id) }}" method="GET" class="d-inline-flex align-items-center gap-2">
            <label class="mb-0"><strong>Filter Bulan:</strong></label>
            <input type="month" 
                   name="bulan" 
                   class="form-control w-auto" 
                   value="{{ request('bulan', date('Y-m')) }}"
                   onchange="this.form.submit()">
            @if(request('bulan'))
                <a href="{{ route('nasabah.riwayat', $nasabah->id) }}" class="btn btn-sm btn-secondary">
                    <i class="fas fa-redo"></i> Reset
                </a>
            @endif
        </form>
    </div>

    <!-- Tabel Riwayat -->
    <div class="card shadow">
        <div class="card-body p-0">
            <div class="table-responsive">
                <table class="table table-hover mb-0 align-middle">
                    <thead class="table-success">
                        <tr>
                            <th width="15%">Tgl Transaksi</th>
                            <th width="8%">Tipe</th>
                            <th width="10%">Kategori</th>
                            <th width="25%">Sub-Jenis & Produk</th>
                            <th width="12%" class="text-center">Berat</th>
                            <th width="15%" class="text-end">Subtotal</th>
                            <th width="15%" class="text-end">Total</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($penjualans as $penjualan)
                            @php
                                $rowspan = $penjualan->details->count();
                            @endphp
                            @foreach($penjualan->details as $index => $detail)
                                <tr>
                                    @if($index === 0)
                                        <td rowspan="{{ $rowspan }}" class="align-middle">
                                            <strong>{{ $penjualan->tanggal_transaksi->format('d-m-Y') }}</strong>
                                        </td>
                                        <td rowspan="{{ $rowspan }}" class="align-middle">
                                            @if($penjualan->tipe_pembayaran == 'tabungan')
                                                <span class="badge bg-primary">Tabungan</span>
                                            @else
                                                <span class="badge bg-success">Tunai</span>
                                            @endif
                                        </td>
                                    @endif
                                    
                                    <td>
                                        <strong>{{ $detail->jenisSampah->kategori->nama ?? '-' }}</strong>
                                    </td>
                                    
                                    <td>
                                        <strong>{{ $detail->jenisSampah->nama ?? '-' }}</strong>
                                        @if($detail->nama_produk)
                                            <br>
                                            <small class="text-muted">
                                                <i class="fas fa-tag"></i> {{ $detail->nama_produk }}
                                            </small>
                                        @endif
                                        <br>
                                        <span class="badge bg-secondary">
                                            {{ $detail->jenisSampah->kode ?? '-' }}
                                        </span>
                                    </td>
                                    
                                    <td class="text-center">
                                        <strong>{{ number_format($detail->berat_kg, 2) }}</strong> 
                                        <span class="text-muted">{{ $detail->satuan }}</span>
                                        <br>
                                        <small class="text-muted">
                                            @ Rp {{ number_format($detail->harga_per_kg, 0, ',', '.') }}
                                        </small>
                                    </td>
                                    
                                    <td class="text-end">
                                        <strong class="text-success">
                                            Rp {{ number_format($detail->subtotal, 0, ',', '.') }}
                                        </strong>
                                    </td>
                                    
                                    @if($index === 0)
                                        <td rowspan="{{ $rowspan }}" class="align-middle text-end bg-light">
                                            <div class="mb-1">
                                                <small class="text-muted d-block">Total Berat:</small>
                                                @php
                                                    $beratPerSatuan = $penjualan->details->groupBy('satuan')->map(function($items) {
                                                        return $items->sum('berat_kg');
                                                    });
                                                @endphp
                                                @foreach($beratPerSatuan as $satuan => $totalBerat)
                                                    <strong>{{ number_format($totalBerat, 2) }} {{ strtolower($satuan) }}</strong>
                                                    @if(!$loop->last)<br>@endif
                                                @endforeach
                                            </div>
                                            <hr class="my-2">
                                            <div>
                                                <h6 class="mb-0 text-success">
                                                    Rp {{ number_format($penjualan->total_jual, 0, ',', '.') }}
                                                </h6>
                                            </div>
                                        </td>
                                    @endif
                                </tr>
                            @endforeach
                        @empty
                            <tr>
                                <td colspan="7" class="text-center py-5">
                                    <i class="fas fa-inbox fa-3x text-muted mb-3"></i>
                                    <h5 class="text-muted">
                                        @if(request('bulan'))
                                            Tidak ada riwayat setoran untuk bulan {{ request('bulan') }}
                                        @else
                                            Belum ada riwayat setoran
                                        @endif
                                    </h5>
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                    
                   @if($penjualans->count() > 0)
                        <tfoot class="table-light">
                            <tr>
                                <th colspan="4" class="text-end">GRAND TOTAL:</th>
                                <th class="text-center">
                                    @php
                                        // Kumpulkan semua detail dari semua penjualan
                                        $allDetails = collect();
                                        foreach($penjualans as $p) {
                                            $allDetails = $allDetails->merge($p->details);
                                        }
                                        
                                        // Group by satuan dan sum berat_kg
                                        $grandTotalPerSatuan = $allDetails->groupBy('satuan')->map(function($items) {
                                            return $items->sum('berat_kg');
                                        });
                                    @endphp
                                    @foreach($grandTotalPerSatuan as $satuan => $totalBerat)
                                        <strong>{{ number_format($totalBerat, 2) }} {{ strtolower($satuan) }}</strong>
                                        @if(!$loop->last)<br>@endif
                                    @endforeach
                                </th>
                                <th></th>
                                <th class="text-end">
                                    <h6 class="mb-0 text-success">
                                        Rp {{ number_format($penjualans->sum('total_jual'), 0, ',', '.') }}
                                    </h6>
                                </th>
                            </tr>
                        </tfoot>
                    @endif
                </table>
            </div>
        </div>
    </div>

    <!-- Pagination -->
    <div class="mt-4">
        {{ $penjualans->appends(['bulan' => request('bulan')])->links() }}
    </div>
</div>

<style>
.table td {
    padding: 12px;
}

.badge {
    font-size: 0.75rem;
    font-weight: 500;
}

.bg-light {
    background-color: #f8f9fa !important;
}

.table thead th {
    font-weight: 600;
}
</style>
@endsection