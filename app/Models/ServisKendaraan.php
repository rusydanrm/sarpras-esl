<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class ServisKendaraan extends Model
{
    protected $table = 'servis_kendaraan';
    protected $primaryKey = 'servis_id';

    protected $fillable = [
        'kendaraan_id',
        'user_id',
        'tanggal_servis',
        'jenis_servis',
        'odometer_km',
        'biaya',
        'bengkel_pelaksana',
        'catatan',
    ];

    protected function casts(): array
    {
        return [
            'tanggal_servis' => 'date',
            'odometer_km' => 'integer',
            'biaya' => 'decimal:2',
        ];
    }

    protected static function booted(): void
    {
        static::saved(function (ServisKendaraan $servis) {
            $kendaraan = $servis->kendaraan;
            if ($kendaraan) {
                // Perbarui tanggal servis terakhir jika servis ini lebih baru atau belum ada
                if (!$kendaraan->tanggal_servis_terakhir || $servis->tanggal_servis->gte($kendaraan->tanggal_servis_terakhir)) {
                    $kendaraan->updateQuietly(['tanggal_servis_terakhir' => $servis->tanggal_servis]);
                }
            }
        });
    }

    public function kendaraan(): BelongsTo
    {
        return $this->belongsTo(Kendaraan::class, 'kendaraan_id', 'kendaraan_id');
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class, 'user_id', 'user_id');
    }
}
