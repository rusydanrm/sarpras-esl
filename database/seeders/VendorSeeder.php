<?php

namespace Database\Seeders;

use App\Models\Vendor;
use Illuminate\Database\Seeder;

class VendorSeeder extends Seeder
{
    public function run(): void
    {
        Vendor::insert([
            ['nama_vendor' => 'CV Sejuk Teknik (Servis AC)', 'kontak' => '0812-xxxx-xxxx', 'created_at' => now(), 'updated_at' => now()],
            ['nama_vendor' => 'Toko Elektronik Bogor Jaya', 'kontak' => '0813-xxxx-xxxx', 'created_at' => now(), 'updated_at' => now()],
        ]);
    }
}