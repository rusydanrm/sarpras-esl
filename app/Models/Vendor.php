<?php
// app/Models/Vendor.php
namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Vendor extends Model
{
    protected $table = 'vendor';
    protected $primaryKey = 'vendor_id';

    protected $fillable = ['nama_vendor', 'kontak'];

    public function pemeliharaan(): HasMany
    {
        return $this->hasMany(Pemeliharaan::class, 'vendor_id', 'vendor_id');
    }
}