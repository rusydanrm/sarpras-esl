<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('notifikasi', function (Blueprint $table) {
            $table->foreignId('barang_id')->nullable()->change();
            $table->foreignId('kendaraan_id')->nullable()->after('barang_id')->constrained('kendaraan', 'kendaraan_id')->onDelete('cascade');
            $table->string('tipe', 50)->default('pemeliharaan_barang')->after('kendaraan_id');
            $table->string('link_url', 255)->nullable()->after('pesan');
        });
    }

    public function down(): void
    {
        Schema::table('notifikasi', function (Blueprint $table) {
            $table->dropForeign(['kendaraan_id']);
            $table->dropColumn(['kendaraan_id', 'tipe', 'link_url']);
            $table->foreignId('barang_id')->nullable(false)->change();
        });
    }
};
