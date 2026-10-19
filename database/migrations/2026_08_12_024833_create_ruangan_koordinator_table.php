<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('ruangan_koordinator', function (Blueprint $table) {
            $table->id();
            $table->foreignId('ruangan_id')->constrained('ruangan', 'ruangan_id')->onDelete('cascade');
            $table->foreignId('user_id')->constrained('users', 'user_id')->onDelete('cascade');
            $table->timestamps();

            $table->unique(['ruangan_id', 'user_id']);
        });

        $ruanganLama = DB::table('ruangan')->whereNotNull('koordinator_id')->get();
        foreach ($ruanganLama as $r) {
            DB::table('ruangan_koordinator')->insert([
                'ruangan_id' => $r->ruangan_id,
                'user_id' => $r->koordinator_id,
                'created_at' => now(),
                'updated_at' => now(),
            ]);
        }

        Schema::table('ruangan', function (Blueprint $table) {
            $table->dropForeign(['koordinator_id']);
            $table->dropColumn('koordinator_id');
        });
    }

    public function down(): void
    {
        Schema::table('ruangan', function (Blueprint $table) {
            $table->foreignId('koordinator_id')->nullable()->constrained('users', 'user_id')->onDelete('set null');
        });

        Schema::dropIfExists('ruangan_koordinator');
    }
};