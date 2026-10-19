<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class UserRoleSeeder extends Seeder
{
    public function run(): void
    {
        $akun = [
            ['nama' => 'Admin Sarpras', 'email' => 'admin@apps.ipb.ac.id', 'role' => 'admin'],
            ['nama' => 'Budi Santoso, S.T.', 'email' => 'budi.dosen@apps.ipb.ac.id', 'role' => 'dosen_tendik'],
            ['nama' => 'Andi Teknisi', 'email' => 'andi.teknisi@apps.ipb.ac.id', 'role' => 'teknisi'],
            ['nama' => 'Dr. Ir. Heru Pimpinan', 'email' => 'heru.pimpinan@apps.ipb.ac.id', 'role' => 'pimpinan'],
        ];

        foreach ($akun as $a) {
            User::create([
                'nama' => $a['nama'],
                'email' => $a['email'],
                'password' => Hash::make('password123'),
                'role' => $a['role'],
                'unit_id' => 3, // Bagian Sarana Prasarana
            ]);
        }
    }
}