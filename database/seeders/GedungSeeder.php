<?php

namespace Database\Seeders;

use App\Models\Gedung;
use Illuminate\Database\Seeder;

class GedungSeeder extends Seeder
{
    public function run(): void
    {
        Gedung::insert([
            ['nama_gedung' => 'Gedung FEM', 'created_at' => now(), 'updated_at' => now()],
        ]);
    }
}