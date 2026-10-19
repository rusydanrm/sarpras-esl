<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('kendaraan', function (Blueprint $table) {
            $table->string('nup', 50)->nullable()->after('plat_nomor');
            $table->string('kode_barang', 50)->nullable()->after('nup');
            $table->string('model_kendaraan', 100)->nullable()->after('jenis_kendaraan');
            $table->string('no_rangka', 100)->nullable()->after('merek_tipe');
            $table->string('no_mesin', 100)->nullable()->after('no_rangka');
            $table->unsignedSmallInteger('masa_manfaat')->default(7)->after('tahun_perolehan');
            $table->date('tgl_pembuatan')->nullable()->after('masa_manfaat');
            $table->date('tgl_perolehan')->nullable()->after('tgl_pembuatan');
            $table->decimal('harga_perolehan', 15, 2)->nullable()->after('tgl_perolehan');
            $table->string('sumber_dana', 50)->nullable()->after('harga_perolehan');
            $table->string('kode_akun', 50)->nullable()->after('sumber_dana');
            $table->string('kode_rkat', 50)->nullable()->after('kode_akun');
            $table->string('dokumen', 100)->nullable()->after('kode_rkat');
            $table->string('referensi', 100)->nullable()->after('dokumen');
        });
    }

    public function down(): void
    {
        Schema::table('kendaraan', function (Blueprint $table) {
            $table->dropColumn([
                'nup',
                'kode_barang',
                'model_kendaraan',
                'no_rangka',
                'no_mesin',
                'masa_manfaat',
                'tgl_pembuatan',
                'tgl_perolehan',
                'harga_perolehan',
                'sumber_dana',
                'kode_akun',
                'kode_rkat',
                'dokumen',
                'referensi',
            ]);
        });
    }
};
