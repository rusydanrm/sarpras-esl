<?php

namespace Database\Seeders;

use App\Models\UnitBagian;
use Illuminate\Database\Seeder;

class UnitBagianSeeder extends Seeder
{
    public function run(): void
    {
        UnitBagian::insert([
            ['nama_unit' => 'Bagian Tata Usaha', 'created_at' => now(), 'updated_at' => now()],
            ['nama_unit' => 'Bagian Akademik', 'created_at' => now(), 'updated_at' => now()],
            ['nama_unit' => 'Bagian Sarana Prasarana', 'created_at' => now(), 'updated_at' => now()],
        ]);
    }
}