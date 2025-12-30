@extends('layouts.app')
@section('title', 'Tambah Setoran Baru')

@section('content')
<div class="container py-4">
    <!-- Header dengan Panah Kembali -->
    <div class="d-flex align-items-center gap-2 mb-4">
        <a href="{{ route('penjualan.index') }}" class="text-dark">
            <i class="fas fa-arrow-left fa-2x"></i>
        </a>
        <h3 class="mb-0">Tambah Setoran Baru</h3>
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

    <div class="card shadow" style="max-width: 1200px; margin: 0 auto;">
        <div class="card-body p-3">
            <form action="{{ route('penjualan.store') }}" method="POST" id="form-penjualan">
                @csrf

                <div class="row mb-3">
                    <div class="col-md-6">
                        <label class="form-label">Nama Nasabah <span class="text-danger">*</span></label>
                        <select name="nasabah_id" class="form-control form-control-sm" required>
                            <option value="">-- Pilih Nasabah --</option>
                            @foreach($nasabahs as $nasabah)
                                <option value="{{ $nasabah->id }}" {{ old('nasabah_id') == $nasabah->id ? 'selected' : '' }}>
                                    {{ $nasabah->nama_nasabah }}
                                </option>
                            @endforeach
                        </select>
                    </div>

                    <div class="col-md-6">
                        <label class="form-label">Tanggal Transaksi <span class="text-danger">*</span></label>
                        <input type="date" name="tanggal_transaksi" class="form-control form-control-sm" 
                               value="{{ old('tanggal_transaksi', now()->format('Y-m-d')) }}" required>
                    </div>
                </div>

                <hr class="my-3">

                <div class="mb-3">
                    <label class="form-label fw-bold">Detail Sampah</label>
                    
                    @forelse($kategoris as $kategori)
                        <div class="card mb-3 kategori-container" data-kategori-id="{{ $kategori->id }}">
                            <div class="card-header {{ $kategori->color_badge }} text-white d-flex justify-content-between align-items-center">
                                <div>
                                    <strong>{{ $kategori->nama }}</strong>
                                    <span class="badge bg-white text-dark ms-2">{{ $kategori->jenisSampahAktif->count() }} sub-jenis tersedia</span>
                                </div>
                                <button type="button" class="btn btn-sm btn-light btn-add-item" data-kategori-id="{{ $kategori->id }}">
                                    <i class="fas fa-plus"></i> Tambah Item
                                </button>
                            </div>
                            
                            <div class="card-body p-2">
                                <div class="item-list" data-kategori-id="{{ $kategori->id }}">
                                    <!-- Items akan ditambahkan di sini via JavaScript -->
                                </div>
                                <div class="text-end mt-2 pe-2">
                                    <small class="text-muted">Subtotal Kategori:</small>
                                    <strong class="text-success ms-2 kategori-subtotal">Rp 0</strong>
                                </div>
                            </div>
                        </div>
                    @empty
                        <div class="alert alert-warning">
                            Belum ada kategori sampah. Silakan tambahkan di menu Kelola Harga terlebih dahulu.
                        </div>
                    @endforelse
                </div>

                <hr class="my-3">

                <div class="bg-light p-3 rounded mb-3">
                    <div class="row">
                        <div class="col-md-6">
                            <h6 class="mb-0">Total Berat: <span id="total-berat" class="text-primary">0</span> <span id="satuan-display">kg</span></h6>
                        </div>
                        <div class="col-md-6 text-md-end">
                            <h5 class="mb-0 text-success">Total: Rp <span id="grand-total">0</span></h5>
                        </div>
                    </div>
                </div>

                <div class="mb-3">
                    <label class="form-label fw-bold">Tipe Pembayaran <span class="text-danger">*</span></label>
                    <div class="form-check">
                        <input class="form-check-input" type="radio" name="tipe_pembayaran" 
                               value="tabungan" id="tabungan" 
                               {{ old('tipe_pembayaran', 'tabungan') == 'tabungan' ? 'checked' : '' }}>
                        <label class="form-check-label" for="tabungan">
                            💰 Simpan ke Tabungan
                        </label>
                    </div>
                    <div class="form-check">
                        <input class="form-check-input" type="radio" name="tipe_pembayaran" 
                               value="tunai" id="tunai"
                               {{ old('tipe_pembayaran') == 'tunai' ? 'checked' : '' }}>
                        <label class="form-check-label" for="tunai">
                            💵 Tunai
                        </label>
                    </div>
                </div>

                <div class="text-end">
                    <button type="submit" class="btn btn-success">
                        <i class="fas fa-save"></i> Buat Setoran
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- Data sub-jenis untuk JavaScript -->
<script>
const subJenisData = {!! json_encode($kategoris->mapWithKeys(function($kat) {
    return [$kat->id => $kat->jenisSampahAktif->map(function($jenis) {
        return [
            'id' => $jenis->id,
            'kode' => $jenis->kode,
            'nama' => $jenis->nama,
            'contoh_produk' => $jenis->contoh_produk,
            'harga' => $jenis->harga_per_kg,
            'satuan' => $jenis->satuan,
        ];
    })];
})) !!};
</script>

<script>
// Tracking
let itemCounter = 0;
const MAX_ITEMS_PER_KATEGORI = 20;

// Format Rupiah
function formatRupiah(angka) {
    return new Intl.NumberFormat('id-ID').format(Math.round(angka));
}

// Template HTML untuk 1 item baru
function createItemHTML(kategoriId, itemIndex) {
    const subJenisList = subJenisData[kategoriId] || [];
    
    let optionsHTML = '<option value="">-- Pilih Sub-Jenis --</option>';
    subJenisList.forEach(sj => {
        optionsHTML += `<option value="${sj.id}" 
                               data-harga="${sj.harga}" 
                               data-satuan="${sj.satuan}"
                               data-contoh="${sj.contoh_produk || ''}">
                            (${sj.kode}) ${sj.nama} - Rp ${formatRupiah(sj.harga)}/${sj.satuan}
                        </option>`;
    });
    
    return `
        <div class="item-row mb-2 p-2 border rounded" data-item-index="${itemIndex}">
            <div class="row align-items-center">
                <!-- Dropdown Sub-Jenis -->
                <div class="col-md-4">
                    <select name="items[${kategoriId}][${itemIndex}][sub_jenis_id]" 
                            class="form-select form-select-sm sub-jenis-select" 
                            required>
                        ${optionsHTML}
                    </select>
                    <small class="text-muted contoh-produk"></small>
                </div>

                <!-- Nama Produk Spesifik (Opsional) -->
                <div class="col-md-3">
                    <input type="text" 
                           name="items[${kategoriId}][${itemIndex}][nama_produk]" 
                           class="form-control form-control-sm" 
                           placeholder="Nama produk (opsional)">
                </div>

                <!-- Berat -->
                <div class="col-md-3">
                    <div class="input-group input-group-sm">
                        <button type="button" class="btn btn-outline-secondary btn-minus-berat">
                            <i class="fas fa-minus"></i>
                        </button>
                        <input type="number" 
                               name="items[${kategoriId}][${itemIndex}][berat]" 
                               class="form-control text-center berat-input" 
                               value="0" 
                               min="0" 
                               step="0.1"
                               required>
                        <button type="button" class="btn btn-outline-secondary btn-plus-berat">
                            <i class="fas fa-plus"></i>
                        </button>
                        <span class="input-group-text satuan-display">kg</span>
                    </div>
                </div>

                <!-- Subtotal -->
                <div class="col-md-1 text-end">
                    <strong class="item-subtotal text-success small">Rp 0</strong>
                </div>

                <!-- Tombol Hapus -->
                <div class="col-md-1 text-end">
                    <button type="button" class="btn btn-sm btn-danger btn-remove-item" title="Hapus Item">
                        <i class="fas fa-times"></i>
                    </button>
                </div>
            </div>
        </div>
    `;
}

// Tambah item baru
function addItem(kategoriId) {
    const container = document.querySelector(`.item-list[data-kategori-id="${kategoriId}"]`);
    
    // Cek apakah ada sub-jenis
    if (!subJenisData[kategoriId] || subJenisData[kategoriId].length === 0) {
        alert('Belum ada sub-jenis untuk kategori ini. Silakan tambahkan di menu Kelola Harga.');
        return;
    }
    
    const itemIndex = itemCounter++;
    
    // Insert HTML
    container.insertAdjacentHTML('beforeend', createItemHTML(kategoriId, itemIndex));
    
    // Attach event listeners
    attachItemEvents(container.lastElementChild, kategoriId);
}

// Hapus item
function removeItem(itemElement) {
    itemElement.remove();
    updateTotals();
}

// Attach event listeners
function attachItemEvents(itemElement, kategoriId) {
    // Event: Dropdown sub-jenis berubah
    const selectSubJenis = itemElement.querySelector('.sub-jenis-select');
    selectSubJenis.addEventListener('change', function() {
        const selectedOption = this.options[this.selectedIndex];
        const harga = parseFloat(selectedOption.getAttribute('data-harga')) || 0;
        const satuan = selectedOption.getAttribute('data-satuan') || 'kg';
        const contoh = selectedOption.getAttribute('data-contoh') || '';
        
        // Update satuan display
        itemElement.querySelector('.satuan-display').textContent = satuan;
        
        // Show contoh produk
        const contohEl = itemElement.querySelector('.contoh-produk');
        contohEl.textContent = contoh ? `Contoh: ${contoh}` : '';
        
        // Set harga ke elemen (untuk kalkulasi)
        itemElement.setAttribute('data-harga', harga);
        
        updateTotals();
    });
    
    // Event: Tombol + berat
    itemElement.querySelector('.btn-plus-berat').addEventListener('click', function(e) {
        e.preventDefault();
        const input = itemElement.querySelector('.berat-input');
        const currentValue = parseFloat(input.value) || 0;
        input.value = (currentValue + 0.1).toFixed(1);
        updateTotals();
    });
    
    // Event: Tombol - berat
    itemElement.querySelector('.btn-minus-berat').addEventListener('click', function(e) {
        e.preventDefault();
        const input = itemElement.querySelector('.berat-input');
        const currentValue = parseFloat(input.value) || 0;
        if (currentValue > 0) {
            input.value = Math.max(0, currentValue - 0.1).toFixed(1);
            updateTotals();
        }
    });
    
    // Event: Input manual berat
    itemElement.querySelector('.berat-input').addEventListener('input', function() {
        if (parseFloat(this.value) < 0) this.value = 0;
        updateTotals();
    });
    
    itemElement.querySelector('.berat-input').addEventListener('change', function() {
        const value = parseFloat(this.value) || 0;
        this.value = value.toFixed(1);
        updateTotals();
    });
    
    // Event: Tombol hapus
    itemElement.querySelector('.btn-remove-item').addEventListener('click', function() {
        if (confirm('Hapus item ini?')) {
            removeItem(itemElement);
        }
    });
}

// Update semua total
function updateTotals() {
    let grandTotal = 0;
    let totalBerat = 0;
    let satuans = new Set(); // Track unique satuans
    
    document.querySelectorAll('.kategori-container').forEach(kategoriContainer => {
        let kategoriSubtotal = 0;
        
        kategoriContainer.querySelectorAll('.item-row').forEach(itemRow => {
            const harga = parseFloat(itemRow.getAttribute('data-harga')) || 0;
            const beratInput = itemRow.querySelector('.berat-input');
            const berat = parseFloat(beratInput.value) || 0;
            const subtotal = berat * harga;
            
            // Get satuan from selected option
            const selectSubJenis = itemRow.querySelector('.sub-jenis-select');
            if (selectSubJenis && selectSubJenis.value) {
                const selectedOption = selectSubJenis.options[selectSubJenis.selectedIndex];
                const satuan = selectedOption.getAttribute('data-satuan') || 'kg';
                satuans.add(satuan);
            }
            
            // Update subtotal item
            itemRow.querySelector('.item-subtotal').textContent = 'Rp ' + formatRupiah(subtotal);
            
            kategoriSubtotal += subtotal;
            totalBerat += berat;
        });
        
        // Update subtotal kategori
        kategoriContainer.querySelector('.kategori-subtotal').textContent = 'Rp ' + formatRupiah(kategoriSubtotal);
        
        grandTotal += kategoriSubtotal;
    });
    
    // Update grand total
    document.getElementById('grand-total').textContent = formatRupiah(grandTotal);
    document.getElementById('total-berat').textContent = totalBerat.toFixed(2);
    
    // Update satuan display
    const satuanDisplay = document.getElementById('satuan-display');
    if (satuans.size === 0) {
        satuanDisplay.textContent = 'kg';
    } else if (satuans.size === 1) {
        satuanDisplay.textContent = Array.from(satuans)[0];
    } else {
        satuanDisplay.innerHTML = '<span class="badge bg-secondary">Mixed</span>';
    }
}

// Event: Saat halaman load
document.addEventListener('DOMContentLoaded', function() {
    // Event: Tombol "Tambah Item"
    document.querySelectorAll('.btn-add-item').forEach(btn => {
        btn.addEventListener('click', function() {
            const kategoriId = this.getAttribute('data-kategori-id');
            addItem(kategoriId);
        });
    });
    
    // Validasi sebelum submit
    document.getElementById('form-penjualan').addEventListener('submit', function(e) {
        const totalItems = document.querySelectorAll('.item-row').length;
        
        if (totalItems === 0) {
            e.preventDefault();
            alert('Silakan tambahkan minimal 1 item sampah!');
            return false;
        }
        
        // Cek apakah semua item sudah pilih sub-jenis
        let hasEmptySubJenis = false;
        document.querySelectorAll('.sub-jenis-select').forEach(select => {
            if (!select.value) {
                hasEmptySubJenis = true;
            }
        });
        
        if (hasEmptySubJenis) {
            e.preventDefault();
            alert('Semua item harus memilih sub-jenis sampah!');
            return false;
        }
        
        // Cek berat > 0
        let hasZeroBerat = false;
        document.querySelectorAll('.berat-input').forEach(input => {
            if (parseFloat(input.value) <= 0) {
                hasZeroBerat = true;
            }
        });
        
        if (hasZeroBerat) {
            e.preventDefault();
            alert('Semua item harus memiliki berat lebih dari 0!');
            return false;
        }
    });
});
</script>

<style>
.kategori-container {
    transition: all 0.2s;
}

.item-row {
    background-color: #f8f9fa;
    transition: all 0.2s;
}

.item-row:hover {
    background-color: #e9ecef;
    transform: translateX(3px);
}

.berat-input {
    font-weight: bold;
    max-width: 80px;
}

.btn-minus-berat, .btn-plus-berat {
    padding: 0.25rem 0.5rem;
}

.card-header {
    padding: 0.75rem 1rem;
}

.item-list:empty::after {
    content: "Klik tombol 'Tambah Item' untuk menambahkan produk";
    display: block;
    text-align: center;
    color: #6c757d;
    padding: 2rem;
    font-style: italic;
}

.contoh-produk {
    display: block;
    margin-top: 2px;
    font-size: 0.75rem;
}
</style>
@endsection