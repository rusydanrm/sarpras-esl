<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasOne;

class Pengajuan extends Model
{
    protected $table = 'pengajuan';
    protected $primaryKey = 'pengajuan_id';

    protected $fillable = [
        'user_id', 'barang_id', 'ruangan_id', 'jenis_pengajuan',
        'deskripsi', 'status', 'verifikator_id', 'catatan_verifikasi',
        'tanggal_pengajuan', 'tanggal_verifikasi',
    ];

    protected function casts(): array
    {
        return [
            'tanggal_pengajuan' => 'datetime',
            'tanggal_verifikasi' => 'datetime',
        ];
    }

    public function pemohon(): BelongsTo
    {
        return $this->belongsTo(User::class, 'user_id', 'user_id');
    }

    public function verifikator(): BelongsTo
    {
        return $this->belongsTo(User::class, 'verifikator_id', 'user_id');
    }

    public function barang(): BelongsTo
    {
        return $this->belongsTo(Barang::class, 'barang_id', 'barang_id');
    }

    public function ruangan(): BelongsTo
    {
        return $this->belongsTo(Ruangan::class, 'ruangan_id', 'ruangan_id');
    }

    public function pemeliharaan(): HasOne
    {
        return $this->hasOne(Pemeliharaan::class, 'pengajuan_id', 'pengajuan_id');
    }
}