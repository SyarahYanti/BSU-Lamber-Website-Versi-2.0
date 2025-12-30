@extends('layouts.app')
@section('title', 'Tambah Sub-Jenis Sampah')

@section('content')
<div class="container py-4">
    <div class="d-flex align-items-center gap-2 mb-4">
        <a href="{{ route('kelola_harga.index') }}" class="text-dark">
            <i class="fas fa-arrow-left fa-2x"></i>
        </a>
        <h3 class="mb-0">Tambah Sub-Jenis Sampah</h3>
    </div>

    @if($errors->any())
        <div class="alert alert-danger alert-dismissible fade show" role="alert">
            <strong>Terjadi Kesalahan!</strong>
            <ul class="mb-0 mt-2">
                @foreach($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    @endif

    <div class="card shadow" style="max-width: 900px; margin: 0 auto;">
        <div class="card-body p-4">
            <form action="{{ route('kelola_harga.store-sub-jenis') }}" method="POST">
                @csrf

                <div class="mb-3">
                    <label class="form-label">
                        Kategori <span class="text-danger">*</span>
                    </label>
                    <select name="kategori_id" 
                            class="form-select @error('kategori_id') is-invalid @enderror" 
                            required>
                        <option value="">-- Pilih Kategori --</option>
                        @foreach($kategoris as $kat)
                            <option value="{{ $kat->id }}" 
                                    {{ old('kategori_id', $kategoriId) == $kat->id ? 'selected' : '' }}>
                                {{ $kat->kode }} - {{ $kat->nama }}
                            </option>
                        @endforeach
                    </select>
                    @error('kategori_id')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>

                <div class="row">
                    <div class="col-md-4">
                        <div class="mb-3">
                            <label class="form-label">
                                Kode Sub-Jenis <span class="text-danger">*</span>
                            </label>
                            <input type="text" 
                                   name="kode" 
                                   class="form-control @error('kode') is-invalid @enderror" 
                                   placeholder="Contoh: P01B, L01"
                                   value="{{ old('kode') }}"
                                   maxlength="20"
                                   required>
                            <small class="text-muted">Kode unik untuk sub-jenis ini</small>
                            @error('kode')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                    </div>

                    <div class="col-md-8">
                        <div class="mb-3">
                            <label class="form-label">
                                Nama Sub-Jenis <span class="text-danger">*</span>
                            </label>
                            <input type="text" 
                                   name="nama" 
                                   class="form-control @error('nama') is-invalid @enderror" 
                                   placeholder="Contoh: PP Gelas Bening Bersih"
                                   value="{{ old('nama') }}"
                                   required>
                            @error('nama')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                    </div>
                </div>

                <div class="mb-3">
                    <label class="form-label">Contoh Produk</label>
                    <textarea name="contoh_produk" 
                              class="form-control @error('contoh_produk') is-invalid @enderror" 
                              rows="2"
                              placeholder="Contoh: Aqua, Club, JS tanpa label">{{ old('contoh_produk') }}</textarea>
                    <small class="text-muted">Daftar contoh produk yang termasuk dalam sub-jenis ini</small>
                    @error('contoh_produk')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>

                <div class="row">
                    <div class="col-md-6">
                        <div class="mb-3">
                            <label class="form-label">
                                Harga <span class="text-danger">*</span>
                            </label>
                            <div class="input-group">
                                <span class="input-group-text">Rp</span>
                                <input type="number" 
                                       name="harga" 
                                       class="form-control @error('harga') is-invalid @enderror" 
                                       placeholder="0"
                                       value="{{ old('harga') }}"
                                       min="0"
                                       required>
                                @error('harga')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                            <small class="text-muted">
                                Harga untuk bulan <strong>{{ now()->translatedFormat('F Y') }}</strong>
                            </small>
                        </div>
                    </div>

                    <div class="col-md-6">
                        <div class="mb-3">
                            <label class="form-label">
                                Satuan <span class="text-danger">*</span>
                            </label>
                            <select name="satuan" 
                                    class="form-select @error('satuan') is-invalid @enderror" 
                                    required>
                                <option value="kg" {{ old('satuan', 'kg') == 'kg' ? 'selected' : '' }}>
                                    Kilogram (kg)
                                </option>
                                <option value="pcs" {{ old('satuan') == 'pcs' ? 'selected' : '' }}>
                                    Pieces (pcs)
                                </option>
                            </select>
                            @error('satuan')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                    </div>
                </div>

                <div class="alert alert-info">
                    <i class="fas fa-info-circle"></i>
                    <strong>Info:</strong> Harga yang Anda input akan berlaku untuk bulan {{ now()->translatedFormat('F Y') }}. 
                    Anda bisa mengubah harga untuk bulan lain melalui menu edit.
                </div>

                <hr class="my-4">

                <div class="d-flex justify-content-between">
                    <button type="submit" class="btn btn-success">
                        <i class="fas fa-save"></i> Simpan Sub-Jenis
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection