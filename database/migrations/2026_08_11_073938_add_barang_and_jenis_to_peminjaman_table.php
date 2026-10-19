<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::table('peminjaman', function (Blueprint $table) {
            $table->dropForeign(['ruangan_id']);
        });

        Schema::table('peminjaman', function (Blueprint $table) {
            $table->unsignedBigInteger('ruangan_id')->nullable()->change();
        });

        Schema::table('peminjaman', function (Blueprint $table) {
            $table->foreign('ruangan_id')->references('ruangan_id')->on('ruangan')->onDelete('cascade');
            $table->foreignId('barang_id')->nullable()->after('ruangan_id')
                ->constrained('barang', 'barang_id')->onDelete('cascade');
            $table->enum('jenis_peminjaman', ['Ruangan', 'Barang'])->default('Ruangan')->after('barang_id');
        });
    }

    public function down(): void
    {
        Schema::table('peminjaman', function (Blueprint $table) {
            $table->dropForeign(['barang_id']);
            $table->dropColumn(['barang_id', 'jenis_peminjaman']);
            $table->unsignedBigInteger('ruangan_id')->nullable(false)->change();
        });
    }
};