<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('jenis_barang', function (Blueprint $table) {
            $table->unsignedSmallInteger('masa_pakai_tahun')->default(5)->after('nama_jenis');
        });
    }

    public function down(): void
    {
        Schema::table('jenis_barang', function (Blueprint $table) {
            $table->dropColumn('masa_pakai_tahun');
        });
    }
};
