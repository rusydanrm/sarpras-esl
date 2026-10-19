<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('import_logs', function (Blueprint $table) {
            $table->id('import_log_id');
            $table->foreignId('user_id')->constrained('users', 'user_id')->onDelete('restrict');
            $table->string('nama_file', 255);
            $table->enum('status', ['sukses', 'sebagian', 'gagal']);
            $table->unsignedInteger('jumlah_baris_sukses')->default(0);
            $table->unsignedInteger('jumlah_baris_gagal')->default(0);
            $table->text('catatan_error')->nullable();
            $table->timestamp('tanggal_upload')->useCurrent();

            $table->index('user_id');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('import_logs');
    }
};