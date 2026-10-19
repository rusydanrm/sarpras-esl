<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('unit_bagian', function (Blueprint $table) {
            $table->id('unit_id');
            $table->string('nama_unit', 150);
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('unit_bagian');
    }
};