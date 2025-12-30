@extends('layouts.app')
@section('title', 'Edit Sub-Jenis Sampah')

@section('content')
<div class="container py-4">
    <div class="d-flex align-items-center gap-2 mb-4">
        <a href="{{ route('kelola_harga.index') }}" class="text-dark">
            <i class="fas fa-arrow-left fa-2x"></i>
        </a>
        <h3 class="mb-0">Edit Sub-Jenis Sampah</h3>
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

    <!-- Form Edit -->
    <div class="card shadow mb-4">
        <div class="card-header bg-primary text-white">
            <h5 class="mb-0">
                <i class="fas fa-edit"></i> Edit Data Sub-Jenis
            </h5>
        </div>
        <div class="card-body p-4">
            <form action="{{ route('kelola_harga.update', $jenisSampah->id) }}" method="POST">
                @csrf
                @method('PUT')

                <div class="row">
                    <div class="col-md-6">
                        <div class="mb-3">
                            <label class="form-label">
                                Kategori <span class="text-danger">*</span>
                            </label>
                            <select name="kategori_id" 
                                    class="form-select @error('kategori_id') is-invalid @enderror" 
                                    required>
                                @foreach($kategoris as $kat)
                                    <option value="{{ $kat->id }}" 
                                            {{ old('kategori_id', $jenisSampah->kategori_id) == $kat->id ? 'selected' : '' }}>
                                        {{ $kat->kode }} - {{ $kat->nama }}
                                    </option>
                                @endforeach
                            </select>
                            @error('kategori_id')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                    </div>

                    <div class="col-md-3">
                        <div class="mb-3">
                            <label class="form-label">
                                Kode <span class="text-danger">*</span>
                            </label>
                            <input type="text" 
                                   name="kode" 
                                   class="form-control @error('kode') is-invalid @enderror" 
                                   value="{{ old('kode', $jenisSampah->kode) }}"
                                   maxlength="20"
                                   required>
                            @error('kode')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                    </div>

                    <div class="col-md-3">
                        <div class="mb-3">
                            <label class="form-label">
                                Satuan <span class="text-danger">*</span>
                            </label>
                            <select name="satuan" 
                                    class="form-select @error('satuan') is-invalid @enderror" 
                                    required>
                                <option value="kg" {{ old('satuan', $jenisSampah->satuan) == 'kg' ? 'selected' : '' }}>
                                    Kilogram (kg)
                                </option>
                                <option value="pcs" {{ old('satuan', $jenisSampah->satuan) == 'pcs' ? 'selected' : '' }}>
                                    Pieces (pcs)
                                </option>
                                <option value="liter" {{ old('satuan', $jenisSampah->satuan) == 'liter' ? 'selected' : '' }}>
                                    Liter (L)
                                </option>
                            </select>
                            @error('satuan')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                    </div>
                </div>

                <div class="mb-3">
                    <label class="form-label">
                        Nama <span class="text-danger">*</span>
                    </label>
                    <input type="text" 
                           name="nama" 
                           class="form-control @error('nama') is-invalid @enderror" 
                           value="{{ old('nama', $jenisSampah->nama) }}"
                           required>
                    @error('nama')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>

                <div class="mb-3">
                    <label class="form-label">Contoh Produk</label>
                    <textarea name="contoh_produk" 
                              class="form-control @error('contoh_produk') is-invalid @enderror" 
                              rows="2">{{ old('contoh_produk', $jenisSampah->contoh_produk) }}</textarea>
                    @error('contoh_produk')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>

                <hr class="my-4">

                <h6 class="mb-3">
                    <i class="fas fa-dollar-sign"></i> Update Harga
                </h6>

                <div class="row">
                    <div class="col-md-3">
                        <div class="mb-3">
                            <label class="form-label">Tahun</label>
                            <select name="tahun" class="form-select" required>
                                @for($y = now()->year; $y >= 2020; $y--)
                                    <option value="{{ $y }}" 
                                            {{ old('tahun', now()->year) == $y ? 'selected' : '' }}>
                                        {{ $y }}
                                    </option>
                                @endfor
                            </select>
                        </div>
                    </div>

                    <div class="col-md-3">
                        <div class="mb-3">
                            <label class="form-label">Bulan</label>
                            <select name="bulan" class="form-select" required>
                                @for($m = 1; $m <= 12; $m++)
                                    <option value="{{ $m }}" 
                                            {{ old('bulan', now()->month) == $m ? 'selected' : '' }}>
                                        {{ \Carbon\Carbon::create()->month($m)->translatedFormat('F') }}
                                    </option>
                                @endfor
                            </select>
                        </div>
                    </div>

                    <div class="col-md-6">
                        <div class="mb-3">
                            <label class="form-label">
                                Harga Baru <span class="text-danger">*</span>
                            </label>
                            <div class="input-group">
                                <span class="input-group-text">Rp</span>
                                <input type="number" 
                                       name="harga" 
                                       class="form-control @error('harga') is-invalid @enderror" 
                                       value="{{ old('harga', $jenisSampah->harga_per_kg) }}"
                                       min="0"
                                       required>
                                <span class="input-group-text">/ {{ $jenisSampah->satuan }}</span>
                            </div>
                            @error('harga')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                    </div>
                </div>

                <div class="alert alert-info mb-4">
                    <i class="fas fa-info-circle"></i>
                    <small>
                        Harga akan disimpan untuk periode yang dipilih. 
                        Jika harga untuk periode tersebut sudah ada, akan diganti dengan harga baru.
                    </small>
                </div>

                <div class="d-flex justify-content-between">
                    <a href="{{ route('kelola_harga.index') }}" class="btn btn-secondary">
                        <i class="fas fa-times"></i> Batal
                    </a>
                    <button type="submit" class="btn btn-primary">
                        <i class="fas fa-save"></i> Simpan Perubahan
                    </button>
                </div>
            </form>
        </div>
    </div>

    <!-- Riwayat Harga (Di Bawah Form) -->
    <div class="card shadow">
        <div class="card-header bg-secondary text-white">
            <h5 class="mb-0">
                <i class="fas fa-history"></i> Riwayat Harga
            </h5>
        </div>
        <div class="card-body p-0">
            @if($jenisSampah->semuaHarga->count() > 0)
                <div class="table-responsive">
                    <table class="table table-hover mb-0 align-middle">
                        <thead class="table-light">
                            <tr>
                                <th width="30%">Periode</th>
                                <th width="40%" class="text-end">Harga</th>
                                <th width="30%" class="text-center">Status</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($jenisSampah->semuaHarga as $harga)
                                <tr>
                                    <td>
                                        <strong>{{ \Carbon\Carbon::create($harga->tahun, $harga->bulan)->translatedFormat('F Y') }}</strong>
                                    </td>
                                    <td class="text-end">
                                        <h6 class="mb-0 text-primary">
                                            Rp {{ number_format($harga->harga_per_kg, 0, ',', '.') }}
                                        </h6>
                                        <small class="text-muted">per {{ $jenisSampah->satuan }}</small>
                                    </td>
                                    <td class="text-center">
                                        @if($harga->tahun == now()->year && $harga->bulan == now()->month)
                                            <span class="badge bg-success px-3 py-2">
                                                <i class="fas fa-check-circle"></i> Harga Aktif
                                            </span>
                                        @else
                                            <span class="badge bg-secondary px-3 py-2">
                                                Histori
                                            </span>
                                        @endif
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            @else
                <div class="text-center py-5 text-muted">
                    <i class="fas fa-inbox fa-3x mb-3"></i>
                    <p class="mb-0">Belum ada riwayat harga</p>
                </div>
            @endif
        </div>
    </div>
</div>
@endsection