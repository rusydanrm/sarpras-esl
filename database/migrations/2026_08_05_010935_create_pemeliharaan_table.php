<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('pemeliharaan', function (Blueprint $table) {
            $table->id('pemeliharaan_id');
            $table->foreignId('barang_id')->constrained('barang', 'barang_id')->onDelete('cascade');
            $table->foreignId('pengajuan_id')->nullable()->constrained('pengajuan', 'pengajuan_id')->onDelete('set null');
            $table->foreignId('teknisi_id')->nullable()->constrained('users', 'user_id')->onDelete('set null');
            $table->foreignId('vendor_id')->nullable()->constrained('vendor', 'vendor_id')->onDelete('set null');
            $table->enum('jenis_tindakan', ['Perbaikan', 'Pemeliharaan Rutin', 'Penggantian']);
            $table->text('kerusakan')->nullable();
            $table->decimal('biaya', 15, 2)->nullable()->default(0);
            $table->date('tanggal_tindakan');
            $table->text('keterangan')->nullable();
            $table->timestamp('created_at')->useCurrent();

            $table->index('barang_id');
            $table->index('tanggal_tindakan');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('pemeliharaan');
    }
};