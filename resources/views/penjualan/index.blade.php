@extends('layouts.app')
@section('title', 'Data Setoran')

@section('content')
<div class="container py-4">
    <!-- Header -->
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h1 class="h3 mb-0">Setoran</h1>
        <div class="input-group" style="max-width: 300px;">
            <input type="text" 
                   class="form-control" 
                   placeholder="Cari Nama Nasabah" 
                   id="searchInput"
                   value="{{ request('search') }}">
            <button class="btn btn-success" type="button" onclick="doSearch()">
                <i class="fas fa-search"></i>
            </button>
        </div>
    </div>

    @if(session('success'))
        <div class="alert alert-success alert-dismissible fade show" role="alert">
            <i class="fas fa-check-circle"></i> {{ session('success') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    @endif

    <!-- Action Buttons -->
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div class="d-flex gap-2">
            <a href="{{ route('penjualan.create') }}" class="btn btn-success">
                <i class="fas fa-plus"></i> Tambah Setoran
            </a>
            <a href="{{ route('penjualan.laporan') }}" class="btn btn-success">
                <i class="fas fa-download"></i> Rekapitulasi Setoran
            </a>
        </div>
        <button type="button" 
                class="btn btn-success" 
                data-bs-toggle="modal" 
                data-bs-target="#filterModal">
            <i class="fas fa-filter"></i> Filter
        </button>
    </div>

    <!-- Active Filters -->
    @if(request()->hasAny(['search', 'tanggal_dari', 'tanggal_sampai', 'tipe_pembayaran']))
    <div class="mb-3">
        <small class="text-muted">Filter aktif:</small>
        <div class="d-flex flex-wrap gap-2 mt-2">
            @if(request('search'))
                <span class="badge bg-secondary">Pencarian: {{ request('search') }}</span>
            @endif
            @if(request('tanggal_dari'))
                <span class="badge bg-info">Dari: {{ request('tanggal_dari') }}</span>
            @endif
            @if(request('tanggal_sampai'))
                <span class="badge bg-info">Sampai: {{ request('tanggal_sampai') }}</span>
            @endif
            @if(request('tipe_pembayaran'))
                <span class="badge bg-primary">Tipe: {{ ucfirst(request('tipe_pembayaran')) }}</span>
            @endif
            <a href="{{ route('penjualan.index') }}" class="badge bg-danger text-decoration-none">
                <i class="fas fa-times"></i> Hapus Filter
            </a>
        </div>
    </div>
    @endif

    <!-- Table -->
    <div class="card shadow">
        <div class="card-body p-0">
            @if($penjualans->count() > 0)
                <div class="table-responsive">
                    <table class="table table-hover align-middle mb-0">
                        <thead class="table-light">
                            <tr>
                                <th>Id</th>
                                <th>Nasabah</th>
                                <th>Tgl Transaksi</th>
                                <th>Detail Sampah</th>
                                <th class="text-end">Total Jual</th>
                                <th class="text-end">Berat Total</th>
                                <th>Pembayaran</th>
                                <th class="text-center">Aksi</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($penjualans as $p)
                            <tr>
                                <td><strong>{{ $p->id }}</strong></td>
                                <td>{{ $p->nasabah->nama_nasabah ?? '-' }}</td>
                                <td>{{ $p->tanggal_transaksi->format('d-m-Y') }}</td>
                                <td>
                                    <div class="d-flex flex-column gap-1">
                                        @foreach($p->details->take(3) as $detail)
                                            <div class="small">
                                                <strong>{{ $detail->jenisSampah->kategori->nama ?? '' }}</strong>
                                                - {{ $detail->jenisSampah->nama ?? '-' }}
                                                @if($detail->nama_produk)
                                                    <span class="text-muted">({{ $detail->nama_produk }})</span>
                                                @endif
                                                <br>
                                                <span class="text-muted" style="font-size: 0.85rem;">
                                                    {{ number_format($detail->berat_kg, 2) }} {{ $detail->satuan }}
                                                    × Rp {{ number_format($detail->harga_per_kg, 0, ',', '.') }}
                                                </span>
                                            </div>
                                        @endforeach
                                        @if($p->details->count() > 3)
                                            <small class="text-primary">
                                                <i class="fas fa-plus-circle"></i> 
                                                +{{ $p->details->count() - 3 }} item lainnya
                                            </small>
                                        @endif
                                    </div>
                                </td>
                                <td class="text-end">
                                    <strong class="text-success">
                                        Rp {{ number_format($p->total_jual, 0, ',', '.') }}
                                    </strong>
                                </td>
                                <td class="text-end">
                                    @php
                                        $beratPerSatuan = $p->details->groupBy('satuan')->map(function($items) {
                                            return $items->sum('berat_kg');
                                        });
                                    @endphp
                                    @foreach($beratPerSatuan as $satuan => $totalBerat)
                                        <strong>{{ number_format($totalBerat, 2) }} {{ strtolower($satuan) }}</strong>
                                        @if(!$loop->last)<br>@endif
                                    @endforeach
                                </td>
                                <td>
                                    @if($p->tipe_pembayaran == 'tabungan')
                                        <span class="badge bg-primary">Tabungan</span>
                                    @else
                                        <span class="badge bg-success">Tunai</span>
                                    @endif
                                </td>
                                <td class="text-center">
                                    <div class="btn-group btn-group-sm">
                                        <a href="{{ route('penjualan.show', $p->id) }}" 
                                           class="btn btn-info" 
                                           title="Lihat Detail">
                                            <i class="fas fa-eye"></i>
                                        </a>
                                        <button type="button"
                                                class="btn btn-danger"
                                                onclick="confirmDelete({{ $p->id }})"
                                                title="Hapus">
                                            <i class="fas fa-trash"></i>
                                        </button>
                                    </div>
                                    
                                    <!-- Hidden Delete Form -->
                                    <form id="delete-form-{{ $p->id }}" 
                                          action="{{ route('penjualan.destroy', $p->id) }}" 
                                          method="POST" 
                                          style="display: none;">
                                        @csrf
                                        @method('DELETE')
                                    </form>
                                </td>
                            </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>

                <!-- Pagination -->
                 <div class="d-flex justify-content-between align-items-center mt-3">
                    <div class="text-muted">
                        Menampilkan {{ $penjualans->firstItem() ?? 0 }} - {{ $penjualans->lastItem() ?? 0 }} dari {{ $penjualans->total() }} data
                    </div>
                    <div>
                        {{ $penjualans->appends(request()->query())->links('pagination::bootstrap-5') }}
                    </div>
                </div>
            @else
                <!-- Empty State -->
                <div class="text-center py-5">
                    <i class="fas fa-inbox fa-3x text-muted mb-3"></i>
                    <h5 class="text-muted">
                        @if(request()->hasAny(['search', 'tanggal_dari', 'tanggal_sampai', 'tipe_pembayaran']))
                            Tidak ada setoran yang sesuai dengan filter
                        @else
                            Belum ada data setoran
                        @endif
                    </h5>
                    <p class="text-muted mb-3">
                        @if(request()->hasAny(['search', 'tanggal_dari', 'tanggal_sampai', 'tipe_pembayaran']))
                            Coba ubah atau hapus filter pencarian
                        @else
                            Klik tombol "Tambah Setoran" untuk membuat transaksi baru
                        @endif
                    </p>
                </div>
            @endif
        </div>
    </div>
</div>

<!-- Filter Modal -->
<div class="modal fade" id="filterModal" tabindex="-1" aria-labelledby="filterModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-header bg-success text-white">
                <h5 class="modal-title" id="filterModalLabel">
                    <i class="fas fa-filter"></i> Filter Data
                </h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
            </div>
            <form action="{{ route('penjualan.index') }}" method="GET" id="filterForm">
                <div class="modal-body">
                    <input type="hidden" name="search" value="{{ request('search') }}">
                    
                    <div class="mb-3">
                        <label class="form-label fw-semibold">Dari Tanggal</label>
                        <input type="date" 
                               name="tanggal_dari" 
                               class="form-control"
                               value="{{ request('tanggal_dari') }}">
                    </div>

                    <div class="mb-3">
                        <label class="form-label fw-semibold">Sampai Tanggal</label>
                        <input type="date" 
                               name="tanggal_sampai" 
                               class="form-control"
                               value="{{ request('tanggal_sampai') }}">
                    </div>

                    <div class="mb-3">
                        <label class="form-label fw-semibold">Tipe Pembayaran</label>
                        <select name="tipe_pembayaran" class="form-select">
                            <option value="">Semua Tipe</option>
                            <option value="tabungan" {{ request('tipe_pembayaran') == 'tabungan' ? 'selected' : '' }}>
                                Tabungan
                            </option>
                            <option value="tunai" {{ request('tipe_pembayaran') == 'tunai' ? 'selected' : '' }}>
                                Tunai
                            </option>
                        </select>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="submit" class="btn btn-success w-50">
                        <i class="fas fa-check"></i> Terapkan
                    </button>
                    <button type="button" class="btn btn-secondary w-50" onclick="resetFilter()">
                        <i class="fas fa-redo"></i> Reset
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

<style>
.pagination {
    margin: 0;
}

.pagination .page-link {
    color: #1f5121;
    border-color: #dee2e6;
    padding: 0.375rem 0.75rem;
}

.pagination .page-link:hover {
    color: #fff;
    background-color: #1f5121;
    border-color: #1f5121;
}

.pagination .page-item.active .page-link {
    background-color: #1f5121;
    border-color: #1f5121;
}

.pagination .page-item.disabled .page-link {
    color: #6c757d;
    pointer-events: none;
    background-color: #fff;
    border-color: #dee2e6;
}

/* Fix untuk icon arrow yang terlalu besar */
.pagination .page-link svg {
    width: 1rem;
    height: 1rem;
    vertical-align: middle;
}
</style>

<script>
// Search functionality
function doSearch() {
    const searchValue = document.getElementById('searchInput').value;
    const currentUrl = new URL(window.location.href);
    
    if (searchValue) {
        currentUrl.searchParams.set('search', searchValue);
    } else {
        currentUrl.searchParams.delete('search');
    }
    
    window.location.href = currentUrl.toString();
}

// Enter key on search
document.getElementById('searchInput').addEventListener('keypress', function(e) {
    if (e.key === 'Enter') {
        doSearch();
    }
});

// Reset filter
function resetFilter() {
    const searchValue = document.querySelector('input[name="search"]').value;
    if (searchValue) {
        window.location.href = "{{ route('penjualan.index') }}?search=" + searchValue;
    } else {
        window.location.href = "{{ route('penjualan.index') }}";
    }
}

// Confirm delete
function confirmDelete(id) {
    if (confirm('Yakin ingin menghapus setoran ini?\nData tidak dapat dikembalikan.')) {
        document.getElementById('delete-form-' + id).submit();
    }
}
</script>

<style>
.table th {
    font-weight: 600;
}

.badge {
    font-weight: 500;
    font-size: 0.85rem;
}

.btn-group-sm .btn {
    padding: 0.25rem 0.5rem;
}
</style>
@endsection