<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('barang', function (Blueprint $table) {
            $table->id('barang_id');
            $table->foreignId('ruangan_id')->constrained('ruangan', 'ruangan_id')->onDelete('cascade');
            $table->foreignId('jenis_id')->nullable()->constrained('jenis_barang', 'jenis_id')->onDelete('set null');
            $table->string('kode_barang', 50)->nullable();
            $table->string('nama_barang', 150);
            $table->string('nup', 50)->nullable();
            $table->string('merek_tipe', 100)->nullable();
            $table->smallInteger('tahun_perolehan')->nullable();
            $table->decimal('harga_satuan', 15, 2)->default(0);
            $table->unsignedInteger('jumlah')->default(1);
            $table->string('satuan', 20)->nullable();
            $table->decimal('total_harga', 18, 2)->storedAs('harga_satuan * jumlah');
            $table->string('sumber_dana', 100)->nullable();
            $table->enum('kondisi', ['Baik', 'Rusak Ringan', 'Rusak Berat'])->default('Baik');
            $table->string('foto_url')->nullable();
            $table->string('qr_code', 100)->nullable();
            $table->unsignedSmallInteger('interval_pemeliharaan_bulan')->nullable();
            $table->date('tanggal_pemeliharaan_terakhir')->nullable();
            $table->text('keterangan')->nullable();
            $table->timestamps();

            $table->index('ruangan_id');
            $table->index('kondisi');
            $table->index('tahun_perolehan');
            $table->index(['interval_pemeliharaan_bulan', 'tanggal_pemeliharaan_terakhir']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('barang');
    }
};