@extends('layouts.app')
@section('title', 'Kelola Harga Sampah')

@section('content')
<div class="container py-4">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h1 class="h3 mb-0">
            <i class="fas fa-tags"></i> Kelola Harga Sampah
        </h1>
        <div>
            <a href="{{ route('kelola_harga.create-kategori') }}" class="btn btn-primary">
                <i class="fas fa-plus"></i> Tambah Kategori
            </a>
            <a href="{{ route('kelola_harga.create-sub-jenis') }}" class="btn btn-success">
                <i class="fas fa-plus"></i> Tambah Sub-Jenis
            </a>
        </div>
    </div>

    @if(session('success'))
        <div class="alert alert-success alert-dismissible fade show" role="alert">
            <i class="fas fa-check-circle"></i> {{ session('success') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    @endif

    @if($errors->any())
        <div class="alert alert-danger alert-dismissible fade show" role="alert">
            <strong>Error!</strong>
            <ul class="mb-0 mt-2">
                @foreach($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    @endif

    <!-- Info Box -->
    <div class="alert alert-info mb-4">
        <i class="fas fa-info-circle"></i> 
        <strong>Info:</strong> Harga yang ditampilkan adalah harga untuk bulan <strong>{{ now()->translatedFormat('F Y') }}</strong>
    </div>

    @forelse($kategoris as $kategori)
        <div class="card shadow mb-4">
            <div class="card-header {{ $kategori->color_badge }} text-white d-flex justify-content-between align-items-center">
                <div>
                    <h5 class="mb-0">
                        <i class="fas fa-folder-open"></i>
                        <strong>{{ $kategori->kode }}</strong> - {{ $kategori->nama }}
                    </h5>
                </div>
                <div class="btn-group">
                    <a href="{{ route('kelola_harga.create-sub-jenis', ['kategori_id' => $kategori->id]) }}" 
                       class="btn btn-sm btn-light">
                        <i class="fas fa-plus"></i> Tambah Item
                    </a>
                </div>
            </div>
            
            <div class="card-body p-0">
                @if($kategori->jenisSampahAktif->count() > 0)
                    <div class="table-responsive">
                        <table class="table table-hover mb-0">
                            <thead class="table-light">
                                <tr>
                                    <th width="10%">Kode</th>
                                    <th width="25%">Nama Sub-Jenis</th>
                                    <th width="30%">Contoh Produk</th>
                                    <th width="10%" class="text-center">Satuan</th>
                                    <th width="15%" class="text-end">Harga</th>
                                    <th width="10%" class="text-center">Aksi</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($kategori->jenisSampahAktif as $jenis)
                                    <tr>
                                        <td>
                                            <span class="badge bg-secondary">{{ $jenis->kode }}</span>
                                        </td>
                                        <td>
                                            <strong>{{ $jenis->nama }}</strong>
                                            @if(!$jenis->is_active)
                                                <span class="badge bg-warning text-dark ms-1">Nonaktif</span>
                                            @endif
                                        </td>
                                        <td>
                                            <small class="text-muted">
                                                {{ $jenis->contoh_produk ?? '-' }}
                                            </small>
                                        </td>
                                        <td class="text-center">
                                            <span class="badge bg-info">{{ strtolower($jenis->satuan) }}</span>
                                        </td>
                                        <td class="text-end">
                                            @if($jenis->harga_per_kg > 0)
                                                <strong class="text-success">
                                                    Rp {{ number_format($jenis->harga_per_kg, 0, ',', '.') }}
                                                </strong>
                                                <small class="text-muted">/{{ $jenis->satuan }}</small>
                                            @else
                                                <span class="badge bg-warning text-dark">Belum ada harga</span>
                                            @endif
                                        </td>
                                        <td class="text-center">
                                            <div class="btn-group btn-group-sm">
                                                <a href="{{ route('kelola_harga.edit', $jenis->id) }}" 
                                                   class="btn btn-warning" title="Edit">
                                                    <i class="fas fa-edit"></i>
                                                </a>
                                                <form action="{{ route('kelola_harga.destroy-sub-jenis', $jenis->id) }}" 
                                                      method="POST" 
                                                      onsubmit="return confirm('Yakin ingin menghapus sub-jenis ini?')">
                                                    @csrf
                                                    @method('DELETE')
                                                    <button type="submit" class="btn btn-danger" title="Hapus">
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
                @else
                    <div class="text-center py-4 text-muted">
                        <i class="fas fa-inbox fa-2x mb-2"></i>
                        <p class="mb-0">Belum ada sub-jenis di kategori ini</p>
                        <a href="{{ route('kelola_harga.create-sub-jenis', ['kategori_id' => $kategori->id]) }}" 
                           class="btn btn-sm btn-primary mt-2">
                            <i class="fas fa-plus"></i> Tambah Sub-Jenis
                        </a>
                    </div>
                @endif
            </div>
        </div>
    @empty
        <div class="card shadow">
            <div class="card-body text-center py-5">
                <i class="fas fa-folder-open fa-4x text-muted mb-3"></i>
                <h5 class="text-muted mb-3">Belum Ada Kategori Sampah</h5>
                <p class="text-muted">Silakan tambahkan kategori terlebih dahulu</p>
                <a href="{{ route('kelola_harga.create-kategori') }}" class="btn btn-primary">
                    <i class="fas fa-plus"></i> Tambah Kategori
                </a>
            </div>
        </div>
    @endforelse
</div>

<style>
.card {
    transition: all 0.2s;
}

.btn-group-sm .btn {
    padding: 0.25rem 0.5rem;
    font-size: 0.875rem;
}

.table td {
    vertical-align: middle;
}
</style>
@endsection