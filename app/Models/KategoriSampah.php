<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class KategoriSampah extends Model
{
    use HasFactory;

    protected $table = 'kategori_sampahs';

    protected $fillable = [
        'kode',
        'nama',
        'warna',
        'aktif',
    ];

    protected $casts = [
        'aktif' => 'boolean',
    ];

    /**
     * Relasi ke JenisSampah (sub-jenis)
     */
    public function jenisSampah()
    {
        return $this->hasMany(JenisSampah::class, 'kategori_id');
    }

    /**
     * Relasi ke JenisSampah yang aktif saja
     */
    public function jenisSampahAktif()
    {
        return $this->hasMany(JenisSampah::class, 'kategori_id')->where('is_active', true);
    }

    /**
     * Scope: Hanya kategori aktif
     */
    public function scopeAktif($query)
    {
        return $query->where('aktif', true);
    }

    /**
     * Accessor: Badge color untuk Bootstrap
     */
    public function getColorBadgeAttribute()
    {
        $colors = [
            'danger' => 'bg-danger',
            'secondary' => 'bg-secondary',
            'info' => 'bg-info',
            'success' => 'bg-success',
            'warning' => 'bg-warning',
        ];

        return $colors[$this->warna] ?? 'bg-primary';
    }
}