<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Pemeliharaan extends Model
{
    protected $table = 'pemeliharaan';
    protected $primaryKey = 'pemeliharaan_id';
    public $timestamps = false;

    protected $fillable = [
        'barang_id', 'pengajuan_id', 'teknisi_id', 'vendor_id',
        'jenis_tindakan', 'kerusakan', 'biaya', 'tanggal_tindakan', 'keterangan',
    ];

    protected function casts(): array
    {
        return ['tanggal_tindakan' => 'date'];
    }

    public function barang(): BelongsTo
    {
        return $this->belongsTo(Barang::class, 'barang_id', 'barang_id');
    }

    public function pengajuan(): BelongsTo
    {
        return $this->belongsTo(Pengajuan::class, 'pengajuan_id', 'pengajuan_id');
    }

    public function teknisi(): BelongsTo
    {
        return $this->belongsTo(User::class, 'teknisi_id', 'user_id');
    }

    public function vendor(): BelongsTo
    {
        return $this->belongsTo(Vendor::class, 'vendor_id', 'vendor_id');
    }
}