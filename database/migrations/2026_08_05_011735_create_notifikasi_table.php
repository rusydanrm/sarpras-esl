<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('notifikasi', function (Blueprint $table) {
            $table->id('notifikasi_id');
            $table->foreignId('barang_id')->constrained('barang', 'barang_id')->onDelete('cascade');
            $table->foreignId('user_id')->nullable()->constrained('users', 'user_id')->onDelete('cascade');
            $table->string('pesan', 255);
            $table->date('tanggal_jatuh_tempo');
            $table->boolean('sudah_dibaca')->default(false);
            $table->timestamp('created_at')->useCurrent();

            $table->index('sudah_dibaca');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('notifikasi');
    }
};