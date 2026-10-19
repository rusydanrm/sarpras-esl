<?php

namespace App\Models;

use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class User extends Authenticatable
{
    use Notifiable;

    protected $primaryKey = 'user_id';

    protected $fillable = [
        'nama',
        'email',
        'password',
        'role',
        'unit_id',
        'status',
    ];

    protected $hidden = [
        'password',
        'remember_token',
    ];

    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'password' => 'hashed',
        ];
    }

    public function unit(): BelongsTo
    {
        return $this->belongsTo(UnitBagian::class, 'unit_id', 'unit_id');
    }

    public function ruanganDikelola(): \Illuminate\Database\Eloquent\Relations\BelongsToMany
    {
        return $this->belongsToMany(Ruangan::class, 'ruangan_koordinator', 'user_id', 'ruangan_id');
    }

    public function pengajuan(): HasMany
    {
        return $this->hasMany(Pengajuan::class, 'user_id', 'user_id');
    }

    public function pengajuanDiverifikasi(): HasMany
    {
        return $this->hasMany(Pengajuan::class, 'verifikator_id', 'user_id');
    }

    public function pemeliharaanDikerjakan(): HasMany
    {
        return $this->hasMany(Pemeliharaan::class, 'teknisi_id', 'user_id');
    }

    public function peminjaman(): HasMany
    {
        return $this->hasMany(Peminjaman::class, 'user_id', 'user_id');
    }

    public function importLogs(): HasMany
    {
        return $this->hasMany(ImportLog::class, 'user_id', 'user_id');
    }

    public function isAdmin(): bool
    {
        return $this->role === 'admin';
    }

    public function isDosenTendik(): bool
    {
        return $this->role === 'dosen_tendik';
    }

    public function isTeknisi(): bool
    {
        return $this->role === 'teknisi';
    }

    public function isPimpinan(): bool
    {
        return $this->role === 'pimpinan';
    }
}