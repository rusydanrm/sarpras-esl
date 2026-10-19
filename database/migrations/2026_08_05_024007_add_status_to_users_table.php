<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->enum('status', ['pending', 'aktif', 'nonaktif'])->default('pending')->after('role');
            $table->enum('role', ['admin', 'dosen_tendik', 'teknisi', 'pimpinan'])->nullable()->default(null)->change();
        });

        // Akun yang sudah ada (dari seeder) otomatis dianggap aktif
        \DB::table('users')->whereNotNull('role')->update(['status' => 'aktif']);
    }

    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->dropColumn('status');
        });
    }
};