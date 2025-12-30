@extends('layouts.app')
@section('title', 'Tambah Kategori Sampah')

@section('content')
<div class="container py-4">
    <div class="d-flex align-items-center gap-2 mb-4">
        <a href="{{ route('kelola_harga.index') }}" class="text-dark">
            <i class="fas fa-arrow-left fa-2x"></i>
        </a>
        <h3 class="mb-0">Tambah Kategori Sampah</h3>
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

    <div class="card shadow" style="max-width: 800px; margin: 0 auto;">
        <div class="card-body p-4">
            <form action="{{ route('kelola_harga.store-kategori') }}" method="POST">
                @csrf

                <div class="mb-3">
                    <label class="form-label">
                        Kode Kategori <span class="text-danger">*</span>
                    </label>
                    <input type="text" 
                           name="kode" 
                           class="form-control @error('kode') is-invalid @enderror" 
                           placeholder="Contoh: P, L, K"
                           value="{{ old('kode') }}"
                           maxlength="10"
                           required>
                    <small class="text-muted">Kode singkat 1-2 huruf (P = Plastik, L = Logam, dll)</small>
                    @error('kode')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>

                <div class="mb-3">
                    <label class="form-label">
                        Nama Kategori <span class="text-danger">*</span>
                    </label>
                    <input type="text" 
                           name="nama" 
                           class="form-control @error('nama') is-invalid @enderror" 
                           placeholder="Contoh: Plastik, Logam, Kertas"
                           value="{{ old('nama') }}"
                           required>
                    @error('nama')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>

                <div class="mb-3">
                    <label class="form-label">
                        Warna Badge <span class="text-danger">*</span>
                    </label>
                    <select name="warna" 
                            class="form-select @error('warna') is-invalid @enderror" 
                            required>
                        <option value="">-- Pilih Warna --</option>
                        <option value="danger" {{ old('warna') == 'danger' ? 'selected' : '' }}>
                            🔴 Merah (Danger)
                        </option>
                        <option value="secondary" {{ old('warna') == 'secondary' ? 'selected' : '' }}>
                            ⚫ Abu-abu (Secondary)
                        </option>
                        <option value="info" {{ old('warna') == 'info' ? 'selected' : '' }}>
                            🔵 Biru (Info)
                        </option>
                        <option value="success" {{ old('warna') == 'success' ? 'selected' : '' }}>
                            🟢 Hijau (Success)
                        </option>
                        <option value="warning" {{ old('warna') == 'warning' ? 'selected' : '' }}>
                            🟡 Kuning (Warning)
                        </option>
                    </select>
                    <small class="text-muted">Warna akan muncul di header tabel</small>
                    @error('warna')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>

                <hr class="my-4">

                <div class="d-flex justify-content-between">
                    <button type="submit" class="btn btn-primary">
                        <i class="fas fa-save"></i> Simpan Kategori
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection