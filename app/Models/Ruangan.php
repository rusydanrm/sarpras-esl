<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Ruangan extends Model
{
    protected $table = 'ruangan';
    protected $primaryKey = 'ruangan_id';

    protected $fillable = [
        'gedung_id',
        'kode_ruang',
        'nama_ruang',
        'wing',
        'lantai',
        'lokasi',
        'panjang_m',
        'lebar_m',
        'keterangan',
    ];
    // koordinator_id DIHAPUS dari fillable, sudah tidak ada kolomnya

    public function gedung(): BelongsTo
    {
        return $this->belongsTo(Gedung::class, 'gedung_id', 'gedung_id');
    }

    public function koordinator(): BelongsToMany
    {
        return $this->belongsToMany(User::class, 'ruangan_koordinator', 'ruangan_id', 'user_id');
    }

    // Helper: cek apakah user tertentu termasuk koordinator ruangan ini
    public function dikoordinasiOleh(int $userId): bool
    {
        return $this->koordinator->contains('user_id', $userId);
    }

    public function barang(): HasMany
    {
        return $this->hasMany(Barang::class, 'ruangan_id', 'ruangan_id');
    }

    public function pengajuan(): HasMany
    {
        return $this->hasMany(Pengajuan::class, 'ruangan_id', 'ruangan_id');
    }

    public function peminjaman(): HasMany
    {
        return $this->hasMany(Peminjaman::class, 'ruangan_id', 'ruangan_id');
    }

    public function getTotalNilaiAsetAttribute(): float
    {
        return $this->barang()->sum('total_harga');
    }
}