<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('ruangan', function (Blueprint $table) {
            $table->id('ruangan_id');
            $table->foreignId('gedung_id')->constrained('gedung', 'gedung_id')->onDelete('restrict');
            $table->foreignId('koordinator_id')->nullable()->constrained('users', 'user_id')->onDelete('set null');
            $table->string('kode_ruang', 50)->nullable();
            $table->string('nama_ruang', 150);
            $table->string('wing', 20)->nullable();
            $table->smallInteger('lantai')->default(1);
            $table->string('lokasi', 50)->nullable();
            $table->decimal('panjang_m', 6, 2)->default(0);
            $table->decimal('lebar_m', 6, 2)->default(0);
            $table->decimal('luas_m2', 8, 2)->storedAs('panjang_m * lebar_m');
            $table->string('keterangan', 100)->nullable();
            $table->timestamps();

            $table->index('gedung_id');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('ruangan');
    }
};