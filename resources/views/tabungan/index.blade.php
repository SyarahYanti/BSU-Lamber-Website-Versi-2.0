@extends('layouts.app')
@section('title', 'Tabungan Nasabah')

@section('content')
<div class="container py-4">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h1 class="h3 mb-0">Tabungan Nasabah</h1>

        <form action="{{ route('tabungan.index') }}" method="GET" id="searchForm">
            <div class="input-group">
                <input type="text" name="search" class="form-control" 
                       placeholder="Cari Nama Nasabah" 
                       value="{{ request('search') }}">
                <button type="submit" class="btn btn-success">
                    <i class="fas fa-search"></i>
                </button>
            </div>
            <!-- Hidden inputs untuk filter -->
            <input type="hidden" name="tanggal_dari" value="{{ request('tanggal_dari') }}">
            <input type="hidden" name="tanggal_sampai" value="{{ request('tanggal_sampai') }}">
            <input type="hidden" name="jenis" value="{{ request('jenis') }}">
        </form>
    </div>

    @if(session('success'))
        <div class="alert alert-success alert-dismissible fade show" role="alert">
            <i class="fas fa-check-circle"></i> {{ session('success') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    @endif

    <!-- Search & Filter Bar -->
    <div class="row g-3 align-items-center mb-4">
        <div class="col-md-3">
            <a href="{{ route('tabungan.tarik') }}" class="btn btn-success">
                <i class="fas fa-hand-holding-usd"></i> Tarik Tabungan
            </a>
        </div>
        <div class="col-md-7"></div>
        <div class="col-md-2">
            <button type="button" class="btn btn-success w-100" data-bs-toggle="modal" data-bs-target="#filterModal">
                <i class="fas fa-filter"></i> Filter
            </button>
        </div>
    </div>

    <!-- Active Filters Display -->
    @if(request()->hasAny(['tanggal_dari', 'tanggal_sampai', 'jenis']))
    <div class="mb-3">
        <small class="text-muted">Filter aktif:</small>
        <div class="d-flex flex-wrap gap-2 mt-2">
            @if(request('tanggal_dari'))
                <span class="badge bg-info">Dari: {{ request('tanggal_dari') }}</span>
            @endif
            @if(request('tanggal_sampai'))
                <span class="badge bg-info">Sampai: {{ request('tanggal_sampai') }}</span>
            @endif
            @if(request('jenis'))
                <span class="badge bg-info">Transaksi: {{ ucfirst(request('jenis')) }}</span>
            @endif
            <a href="{{ route('tabungan.index') }}" class="badge bg-danger text-decoration-none">
                <i class="fas fa-times"></i> Hapus Filter
            </a>
        </div>
    </div>
    @endif

    <!-- Tabel Transaksi Tabungan -->
    <div class="card shadow">
        <div class="card-body">
            @if($tabungans->count() > 0)
                <div class="table-responsive">
                    <table class="table table-hover align-middle">
                        <thead class="table-light">
                            <tr>
                                <th>Nama Nasabah</th>
                                <th>Tgl Transaksi</th>
                                <th class="text-end">Debit</th>
                                <th class="text-end">Kredit</th>
                                <th class="text-end">Saldo</th>
                                <th class="text-center" width="150">Aksi</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($tabungans as $t)
                            <tr>
                                <td>{{ $t->nasabah->nama_nasabah ?? '-' }}</td>
                                <td>{{ $t->tanggal_transaksi->format('d-m-Y') }}</td>
                                <td class="text-success text-end fw-semibold">
                                    @if($t->debit > 0)
                                        Rp {{ number_format($t->debit, 0, ',', '.') }}
                                    @else
                                        -
                                    @endif
                                </td>
                                <td class="text-danger text-end fw-semibold">
                                    @if($t->kredit > 0)
                                        Rp {{ number_format($t->kredit, 0, ',', '.') }}
                                    @else
                                        -
                                    @endif
                                </td>
                                <td class="fw-bold text-end">Rp {{ number_format($t->saldo, 0, ',', '.') }}</td>
                                <td class="text-center">
                                    <div class="btn-group" role="group">
                                        <a href="{{ route('tabungan.show', $t->id) }}" 
                                            class="btn btn-info btn-sm" 
                                            title="Lihat Detail">
                                            <i class="fas fa-eye"></i>
                                        </a>
                                        <form action="{{ route('tabungan.destroy', $t->id) }}" 
                                              method="POST" 
                                              class="d-inline"
                                              onsubmit="return confirm('Yakin ingin menghapus transaksi ini?')">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit" class="btn btn-danger btn-sm" title="Hapus">
                                                <i class="fas fa-trash"></i>
                                            </button>
                                        </form>
                                    </div>
                                </td>
                            </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>

                <div class="d-flex justify-content-between align-items-center mt-3">
                    <div class="text-muted">
                        Menampilkan {{ $tabungans->firstItem() ?? 0 }} - {{ $tabungans->lastItem() ?? 0 }} dari {{ $tabungans->total() }} data
                    </div>
                    <div>
                        {{ $tabungans->appends(request()->query())->links('pagination::bootstrap-5') }}
                    </div>
                </div>
            @else
                <div class="text-center py-5">
                    <i class="fas fa-inbox fa-3x text-muted mb-3"></i>
                    <h5 class="text-muted">
                        @if(request()->hasAny(['search', 'tanggal_dari', 'tanggal_sampai', 'jenis']))
                            Tidak ada transaksi yang sesuai dengan filter
                        @else
                            Belum ada transaksi tabungan
                        @endif
                    </h5>
                </div>
            @endif
        </div>
    </div>
</div>

<!-- Filter Modal -->
<div class="modal fade" id="filterModal" tabindex="-1" aria-labelledby="filterModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header bg-primary text-white">
                <h5 class="modal-title" id="filterModalLabel">
                    <i class="fas fa-filter"></i> Filter Data
                </h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <form action="{{ route('tabungan.index') }}" method="GET" id="filterForm">
                <div class="modal-body">
                    <input type="hidden" name="search" value="{{ request('search') }}">
                    
                    <div class="mb-3">
                        <label class="form-label fw-semibold">Dari Tanggal</label>
                        <input type="date" name="tanggal_dari" class="form-control" 
                               value="{{ request('tanggal_dari') }}">
                    </div>

                    <div class="mb-3">
                        <label class="form-label fw-semibold">Sampai Tanggal</label>
                        <input type="date" name="tanggal_sampai" class="form-control" 
                               value="{{ request('tanggal_sampai') }}">
                    </div>

                    <div class="mb-3">
                        <label class="form-label fw-semibold">Jenis Transaksi</label>
                        <select name="jenis" class="form-select">
                            <option value="">Semua</option>
                            <option value="setor" {{ request('jenis') == 'setor' ? 'selected' : '' }}>Setor</option>
                            <option value="tarik" {{ request('jenis') == 'tarik' ? 'selected' : '' }}>Tarik</option>
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

<script>
function resetFilter() {
    document.getElementById('filterForm').reset();
    const searchValue = document.querySelector('input[name="search"]').value;
    if (searchValue) {
        window.location.href = "{{ route('tabungan.index') }}?search=" + searchValue;
    } else {
        window.location.href = "{{ route('tabungan.index') }}";
    }
}
</script>

<style>
.badge {
    font-weight: 500;
    font-size: 0.875rem;
    padding: 0.35em 0.65em;
}

.modal-header {
    border-bottom: none;
}

.modal-footer {
    border-top: 1px solid #dee2e6;
    padding: 1rem;
}

.form-select, .form-control {
    border-radius: 0.375rem;
}

.btn-group .btn {
    border-radius: 0;
}

.btn-group .btn:first-child {
    border-top-left-radius: 0.25rem;
    border-bottom-left-radius: 0.25rem;
}

.btn-group .btn:last-child {
    border-top-right-radius: 0.25rem;
    border-bottom-right-radius: 0.25rem;
}

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
@endsection