<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    public function run(): void
    {
        $this->call([
            UnitBagianSeeder::class,
            GedungSeeder::class, // tambahkan ini
            JenisBarangSeeder::class,
            VendorSeeder::class,
            UserRoleSeeder::class,
        ]);
    }
}