<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class PenjualanDetail extends Model
{
    use HasFactory;

    protected $fillable = [
        'penjualan_id',
        'jenis_sampah_id', // Ini sekarang berisi ID sub-jenis
        'nama_produk',
        'berat_kg',
        'satuan',
        'harga_per_kg',
        'subtotal',
    ];

    protected $casts = [
        'berat_kg' => 'decimal:2',
    ];

    /**
     * Relasi ke Penjualan
     */
    public function penjualan()
    {
        return $this->belongsTo(Penjualan::class);
    }

    /**
     * Relasi ke JenisSampah (sub-jenis)
     * Note: Kolom bernama jenis_sampah_id tapi sekarang isinya sub-jenis
     */
    public function jenisSampah()
    {
        return $this->belongsTo(JenisSampah::class, 'jenis_sampah_id');
    }

    /**
     * Accessor: Nama lengkap produk
     */
    public function getNamaLengkapAttribute()
    {
        $nama = $this->jenisSampah->nama ?? 'Tidak diketahui';
        
        if ($this->nama_produk) {
            $nama .= ' - ' . $this->nama_produk;
        }
        
        return $nama;
    }

    /**
     * Accessor: Format subtotal
     */
    public function getSubtotalFormatAttribute()
    {
        return 'Rp ' . number_format($this->subtotal, 0, ',', '.');
    }
}