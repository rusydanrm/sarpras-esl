<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class ImportLog extends Model
{
    protected $table = 'import_logs';
    protected $primaryKey = 'import_log_id';
    public $timestamps = false;

    protected $fillable = [
        'user_id', 'nama_file', 'status', 'jumlah_baris_sukses',
        'jumlah_baris_gagal', 'catatan_error', 'tanggal_upload',
    ];

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class, 'user_id', 'user_id');
    }
}