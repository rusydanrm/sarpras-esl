<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Peminjaman extends Model
{
    protected $table = 'peminjaman';
    protected $primaryKey = 'peminjaman_id';

    protected $fillable = [
        'ruangan_id',
        'barang_id',
        'jenis_peminjaman',
        'user_id',
        'status',
        'tanggal_mulai',
        'tanggal_selesai',
        'keterangan',
    ];

    protected function casts(): array
    {
        return [
            'tanggal_mulai' => 'datetime',
            'tanggal_selesai' => 'datetime',
        ];
    }

    public function ruangan(): BelongsTo
    {
        return $this->belongsTo(Ruangan::class, 'ruangan_id', 'ruangan_id');
    }

    public function barang(): BelongsTo
    {
        return $this->belongsTo(Barang::class, 'barang_id', 'barang_id');
    }

    public function peminjam(): BelongsTo
    {
        return $this->belongsTo(User::class, 'user_id', 'user_id');
    }

    // Nama objek yang dipinjam, dipakai di tabel/laporan supaya tidak perlu cek null berulang
    public function getNamaObjekAttribute(): string
    {
        return $this->jenis_peminjaman === 'Ruangan'
            ? ($this->ruangan->nama_ruang ?? '-')
            : ($this->barang->nama_barang ?? '-');
    }
}