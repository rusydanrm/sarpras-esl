<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('servis_kendaraan', function (Blueprint $table) {
            $table->id('servis_id');
            $table->foreignId('kendaraan_id')->constrained('kendaraan', 'kendaraan_id')->onDelete('cascade');
            $table->foreignId('user_id')->nullable()->constrained('users', 'user_id')->onDelete('set null');
            $table->date('tanggal_servis');
            $table->enum('jenis_servis', [
                'Servis Rutin',
                'Ganti Oli',
                'Perbaikan Mesin',
                'Ganti Ban/Aki',
                'Uji Emisi / KIR',
                'Lainnya'
            ])->default('Servis Rutin');
            $table->unsignedInteger('odometer_km')->nullable();
            $table->decimal('biaya', 15, 2)->default(0);
            $table->string('bengkel_pelaksana', 150)->nullable();
            $table->text('catatan')->nullable();
            $table->timestamps();

            $table->index(['kendaraan_id', 'tanggal_servis']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('servis_kendaraan');
    }
};
