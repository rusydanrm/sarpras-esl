<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('kendaraan', function (Blueprint $table) {
            $table->id('kendaraan_id');
            $table->string('nama_kendaraan', 150);
            $table->string('plat_nomor', 30)->unique();
            $table->enum('jenis_kendaraan', ['Mobil', 'Motor', 'Bus/Minibus', 'Lainnya'])->default('Mobil');
            $table->string('merek_tipe', 100)->nullable();
            $table->smallInteger('tahun_perolehan')->nullable();
            $table->enum('kondisi', ['Baik', 'Rusak Ringan', 'Rusak Berat'])->default('Baik');
            $table->enum('status', ['Tersedia', 'Digunakan', 'Dalam Perawatan'])->default('Tersedia');
            $table->string('penanggung_jawab', 100)->nullable();
            $table->unsignedSmallInteger('interval_servis_bulan')->default(6);
            $table->date('tanggal_servis_terakhir')->nullable();
            $table->date('tanggal_pajak_stnk')->nullable();
            $table->text('keterangan')->nullable();
            $table->string('foto_url')->nullable();
            $table->timestamps();

            $table->index('jenis_kendaraan');
            $table->index('status');
            $table->index(['interval_servis_bulan', 'tanggal_servis_terakhir']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('kendaraan');
    }
};
