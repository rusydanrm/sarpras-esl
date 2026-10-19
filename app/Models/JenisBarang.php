<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class JenisBarang extends Model
{
    protected $table = 'jenis_barang';
    protected $primaryKey = 'jenis_id';

    protected $fillable = ['nama_jenis', 'masa_pakai_tahun'];

    protected function casts(): array
    {
        return [
            'masa_pakai_tahun' => 'integer',
        ];
    }

    public function barang(): HasMany
    {
        return $this->hasMany(Barang::class, 'jenis_id', 'jenis_id');
    }
}