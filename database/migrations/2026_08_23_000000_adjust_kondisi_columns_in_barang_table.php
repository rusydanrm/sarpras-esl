<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('barang', function (Blueprint $table) {
            $table->unsignedInteger('kondisi_baik')->default(0)->after('sumber_dana');
            $table->unsignedInteger('kondisi_rusak_ringan')->default(0)->after('kondisi_baik');
            $table->unsignedInteger('kondisi_rusak_berat')->default(0)->after('kondisi_rusak_ringan');
        });

        // Migrasi data kondisi eksisting ke kolom kuantitas masing-masing
        if (Schema::hasColumn('barang', 'kondisi')) {
            DB::table('barang')->where('kondisi', 'Baik')->update(['kondisi_baik' => DB::raw('jumlah')]);
            DB::table('barang')->where('kondisi', 'Rusak Ringan')->update(['kondisi_rusak_ringan' => DB::raw('jumlah')]);
            DB::table('barang')->where('kondisi', 'Rusak Berat')->update(['kondisi_rusak_berat' => DB::raw('jumlah')]);

            Schema::table('barang', function (Blueprint $table) {
                $table->dropIndex(['kondisi']);
                $table->dropColumn('kondisi');
            });
        }
    }

    public function down(): void
    {
        Schema::table('barang', function (Blueprint $table) {
            $table->enum('kondisi', ['Baik', 'Rusak Ringan', 'Rusak Berat'])->default('Baik')->after('sumber_dana');
            $table->index('kondisi');
        });

        DB::table('barang')->where('kondisi_rusak_berat', '>', 0)->update(['kondisi' => 'Rusak Berat']);
        DB::table('barang')->where('kondisi_rusak_ringan', '>', 0)->where('kondisi_rusak_berat', 0)->update(['kondisi' => 'Rusak Ringan']);
        DB::table('barang')->where('kondisi_baik', '>', 0)->where('kondisi_rusak_ringan', 0)->where('kondisi_rusak_berat', 0)->update(['kondisi' => 'Baik']);

        Schema::table('barang', function (Blueprint $table) {
            $table->dropColumn(['kondisi_baik', 'kondisi_rusak_ringan', 'kondisi_rusak_berat']);
        });
    }
};
