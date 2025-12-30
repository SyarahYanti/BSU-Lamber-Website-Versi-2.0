<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class JenisSampah extends Model
{
    protected $table = 'jenis_sampahs';
    
    protected $fillable = [
        'kategori_id',
        'kode',
        'nama',
        'contoh_produk',
        'satuan',
        'is_active',
    ];

    protected $casts = [
        'is_active' => 'boolean',
    ];

    /**
     * Relasi ke KategoriSampah
     */
    public function kategori()
    {
        return $this->belongsTo(KategoriSampah::class, 'kategori_id');
    }

    /**
     * Relasi ke HargaSampah untuk bulan ini
     */
    public function hargaSekarang()
    {
        return $this->hasOne(HargaSampah::class, 'jenis_sampah_id')
                    ->where('tahun', now()->year)
                    ->where('bulan', now()->month);
    }

    /**
     * Relasi ke semua HargaSampah
     */
    public function semuaHarga()
    {
        return $this->hasMany(HargaSampah::class, 'jenis_sampah_id')
                    ->orderBy('tahun', 'desc')
                    ->orderBy('bulan', 'desc');
    }

    /**
     * Relasi ke PenjualanDetail
     */
    public function penjualanDetails()
    {
        return $this->hasMany(PenjualanDetail::class, 'jenis_sampah_id');
    }

    /**
     * Scope: Hanya yang aktif
     */
    public function scopeAktif($query)
    {
        return $query->where('is_active', true);
    }

    /**
     * Scope: Filter by kategori
     */
    public function scopeByKategori($query, $kategoriId)
    {
        return $query->where('kategori_id', $kategoriId);
    }

    /**
     * Accessor: Harga per kg bulan ini
     */
    public function getHargaPerKgAttribute()
    {
        // Coba ambil harga bulan ini
        $harga = $this->hargaSekarang;
        
        // Jika tidak ada, ambil harga terbaru
        if (!$harga) {
            $harga = $this->semuaHarga()->first();
        }
        
        return $harga ? $harga->harga_per_kg : 0;
    }

    /**
     * Accessor: Nama lengkap dengan kode
     */
    public function getNamaLengkapAttribute()
    {
        return "({$this->kode}) {$this->nama}";
    }

    /**
     * Accessor: Format harga
     */
    public function getHargaFormatAttribute()
    {
        return 'Rp ' . number_format($this->harga_per_kg, 0, ',', '.');
    }

    /**
     * Method: Update harga untuk bulan tertentu
     */
    public function updateHargaBulan($hargaBaru, $tahun = null, $bulan = null)
    {
        $tahun = $tahun ?? now()->year;
        $bulan = $bulan ?? now()->month;

        return HargaSampah::updateOrCreate(
            [
                'jenis_sampah_id' => $this->id,
                'tahun' => $tahun,
                'bulan' => $bulan,
            ],
            [
                'harga_per_kg' => $hargaBaru,
            ]
        );
    }
}