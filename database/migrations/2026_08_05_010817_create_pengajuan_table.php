<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('pengajuan', function (Blueprint $table) {
            $table->id('pengajuan_id');
            $table->foreignId('user_id')->constrained('users', 'user_id')->onDelete('restrict');
            $table->foreignId('barang_id')->nullable()->constrained('barang', 'barang_id')->onDelete('cascade');
            $table->foreignId('ruangan_id')->nullable()->constrained('ruangan', 'ruangan_id')->onDelete('cascade');
            $table->enum('jenis_pengajuan', ['Pengadaan', 'Perbaikan', 'Pemeliharaan', 'Penggantian', 'Peminjaman']);
            $table->text('deskripsi');
            $table->enum('status', ['Diajukan', 'Diverifikasi', 'Disetujui', 'Ditolak', 'Diproses', 'Selesai'])->default('Diajukan');
            $table->foreignId('verifikator_id')->nullable()->constrained('users', 'user_id')->onDelete('set null');
            $table->text('catatan_verifikasi')->nullable();
            $table->timestamp('tanggal_pengajuan')->useCurrent();
            $table->timestamp('tanggal_verifikasi')->nullable();
            $table->timestamps();

            $table->index('status');
            $table->index('jenis_pengajuan');
            $table->index('user_id');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('pengajuan');
    }
};