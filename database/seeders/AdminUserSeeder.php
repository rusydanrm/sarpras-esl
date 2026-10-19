<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class AdminUserSeeder extends Seeder
{
    public function run(): void
    {
        User::create([
            'nama' => 'Admin Sarpras',
            'email' => 'admin@apps.ipb.ac.id',
            'password' => Hash::make('eslfem123'), // ganti setelah login pertama
            'role' => 'admin',
        ]);
    }
}