@extends('layouts.app')

@section('content')
<div class="container-fluid">
    <!-- Header dengan Pencarian -->
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h1 class="h3 mb-0 text-gray-800">Nasabah</h1>

        <div class="d-flex gap-2">
            <!-- Form Pencarian -->
            <form class="d-flex" method="GET" action="{{ route('nasabah.index') }}">
                <input type="hidden" name="tanggal_dari" value="{{ request('tanggal_dari') }}">
                <input type="hidden" name="tanggal_sampai" value="{{ request('tanggal_sampai') }}">
                
                <input type="text" name="cari" class="form-control" style="width: 250px;" 
                       placeholder="Cari nama atau no. induk..."
                       value="{{ request('cari') }}">
                <button class="btn btn-success ms-2" type="submit">
                    <i class="fas fa-search"></i>
                </button>
            </form>
        </div>
    </div>

    <!-- Tombol Tambah dan Filter -->
    <div class="d-flex justify-content-between align-items-center mb-4">
        <!-- Tombol Tambah Nasabah (Kiri) -->
        <a href="{{ route('nasabah.create') }}" class="btn btn-success">
            <i class="fas fa-plus"></i> Tambah Nasabah
        </a>

        <!-- Tombol Filter (Kanan) -->
        <div class="position-relative">
            <button type="button" class="btn btn-success" id="toggleFilter">
                <i class="fas fa-filter"></i> Filter
                @if(request()->hasAny(['tanggal_dari', 'tanggal_sampai']))
                    <span class="badge bg-danger rounded-pill">●</span>
                @endif
            </button>

            <!-- Card Filter (Hidden by default) -->
            <div id="filterCard" class="card shadow-lg position-absolute end-0 mt-2" style="display: none; width: 400px; z-index: 1000;">
                <div class="card-header bg-success text-white d-flex justify-content-between align-items-center">
                    <span><i class="fas fa-filter"></i> Filter Tanggal Daftar</span>
                    <button type="button" class="btn-close btn-close-white" id="closeFilter"></button>
                </div>
                <div class="card-body">
                    <form action="{{ route('nasabah.index') }}" method="GET" id="filterForm">
                        <input type="hidden" name="cari" value="{{ request('cari') }}">
                        
                        <div class="mb-3">
                            <label class="form-label">Dari Tanggal</label>
                            <input type="date" name="tanggal_dari" class="form-control" 
                                   value="{{ request('tanggal_dari') }}">
                        </div>

                        <div class="mb-3">
                            <label class="form-label">Sampai Tanggal</label>
                            <input type="date" name="tanggal_sampai" class="form-control" 
                                   value="{{ request('tanggal_sampai') }}">
                        </div>

                        <div class="d-flex gap-2">
                            <button type="submit" class="btn btn-primary flex-grow-1">
                                <i class="fas fa-check"></i> Terapkan
                            </button>
                            <a href="{{ route('nasabah.index') }}" class="btn btn-secondary">
                                <i class="fas fa-redo"></i> Reset
                            </a>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>

    <!-- Tabel Data -->
    <div class="card shadow mb-4">
        <div class="card-body">
            <div class="table-responsive">
                <table class="table table-bordered">
                    <thead class="table-light">
                        <tr>
                            <th>Nama Nasabah</th>
                            <th>No. Induk</th>
                            <th>Alamat</th>
                            <th>Tanggal Daftar</th>
                            <th>Status</th>
                            <th width="180">Aksi</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($nasabahs as $nasabah)
                        <tr>
                            <td>{{ $nasabah->nama_nasabah }}</td>
                            <td>{{ $nasabah->no_induk }}</td>
                            <td>{{ $nasabah->alamat }}</td>
                            <td>{{ $nasabah->tanggal_daftar->format('d-m-Y') }}</td>
                            <td>
                                <form action="{{ route('nasabah.toggle', $nasabah) }}" method="POST" class="d-inline">
                                    @csrf
                                    @method('POST')
                                    <button type="submit" class="btn {{ $nasabah->status ? 'btn-success' : 'btn-danger' }} btn-sm">
                                        {{ $nasabah->status ? 'Aktif' : 'Tidak Aktif' }}
                                    </button>
                                </form>
                            </td>
                            <td>
                                @if($nasabah->status)
                                    <!-- Nasabah Aktif - Tombol berfungsi -->
                                    <a href="{{ route('nasabah.riwayat', $nasabah) }}" class="btn btn-info btn-sm">
                                        <i class="fas fa-history"></i> Riwayat
                                    </a>
                                    <a href="{{ route('nasabah.edit', $nasabah) }}" class="btn btn-warning btn-sm">
                                        <i class="fas fa-edit"></i>
                                    </a>
                                    <form action="{{ route('nasabah.destroy', $nasabah) }}" method="POST" class="d-inline">
                                        @csrf @method('DELETE')
                                        <button type="submit" class="btn btn-danger btn-sm" onclick="return confirm('Yakin hapus?')">
                                            <i class="fas fa-trash"></i>
                                        </button>
                                    </form>
                                @else
                                    <!-- Nasabah Tidak Aktif - Tombol disabled -->
                                    <button class="btn btn-secondary btn-sm" disabled title="Nasabah tidak aktif">
                                        <i class="fas fa-history"></i> Riwayat
                                    </button>
                                    <button class="btn btn-secondary btn-sm" disabled title="Nasabah tidak aktif">
                                        <i class="fas fa-edit"></i>
                                    </button>
                                    <button class="btn btn-secondary btn-sm" disabled title="Nasabah tidak aktif">
                                        <i class="fas fa-trash"></i>
                                    </button>
                                @endif
                            </td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="6" class="text-center">
                                @if(request()->hasAny(['cari', 'tanggal_dari', 'tanggal_sampai']))
                                    Tidak ada data yang sesuai dengan filter
                                @else
                                    Belum ada data nasabah
                                @endif
                            </td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>
                <!-- Pagination dengan styling yang benar -->
                <div class="d-flex justify-content-between align-items-center mt-3">
                    <div class="text-muted">
                        Menampilkan {{ $nasabahs->firstItem() ?? 0 }} - {{ $nasabahs->lastItem() ?? 0 }} dari {{ $nasabahs->total() }} data
                    </div>
                    <div>
                        {{ $nasabahs->appends(request()->query())->links('pagination::bootstrap-5') }}
                    </div>
                </div>
            </div>
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
    document.addEventListener('DOMContentLoaded', function() {
        const toggleBtn = document.getElementById('toggleFilter');
        const filterCard = document.getElementById('filterCard');
        const closeBtn = document.getElementById('closeFilter');

        // Toggle filter card
        toggleBtn.addEventListener('click', function() {
            filterCard.style.display = filterCard.style.display === 'none' ? 'block' : 'none';
        });

        // Close filter card
        closeBtn.addEventListener('click', function() {
            filterCard.style.display = 'none';
        });

        // Close when clicking outside
        document.addEventListener('click', function(event) {
            if (!toggleBtn.contains(event.target) && !filterCard.contains(event.target)) {
                filterCard.style.display = 'none';
            }
        });
    });
</script>
@endsection