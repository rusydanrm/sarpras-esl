<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Gedung extends Model
{
    protected $table = 'gedung';
    protected $primaryKey = 'gedung_id';

    protected $fillable = ['nama_gedung'];

    public function ruangan(): HasMany
    {
        return $this->hasMany(Ruangan::class, 'gedung_id', 'gedung_id');
    }
}