<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class UnitBagian extends Model
{
    protected $table = 'unit_bagian';
    protected $primaryKey = 'unit_id';

    protected $fillable = ['nama_unit'];

    public function users(): HasMany
    {
        return $this->hasMany(User::class, 'unit_id', 'unit_id');
    }
}