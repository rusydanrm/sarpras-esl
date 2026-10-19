<?php

namespace Database\Seeders;

use App\Models\JenisBarang;
use Illuminate\Database\Seeder;

class JenisBarangSeeder extends Seeder
{
    public function run(): void
    {
        $kategori = [
            'AC' => 5,
            'Laptop' => 4,
            'Printer' => 5,
            'PC Komputer' => 4,
            'Scanner' => 5,
            'Handphone' => 3,
            'Harddisk Eksternal' => 3,
            'LCD Proyektor' => 5,
            'Bangku/Kursi' => 8,
            'Dispenser' => 5,
            'Lemari' => 10,
            'Meja' => 10,
            'Kamera' => 5,
            'Campuran' => 5,
        ];

        foreach ($kategori as $nama => $masaPakai) {
            JenisBarang::updateOrCreate(
                ['nama_jenis' => $nama],
                ['masa_pakai_tahun' => $masaPakai]
            );
        }

        // Hapus kategori lama jika tidak memiliki relasi barang
        JenisBarang::whereIn('nama_jenis', ['Elektronik', 'Furnitur', 'Mekanikal', 'Peralatan Kantor'])
            ->doesntHave('barang')
            ->delete();

        // Auto-assign jenis_id dan interval_pemeliharaan_bulan untuk barang yang belum diset
        $items = \App\Models\Barang::all();
        foreach ($items as $b) {
            $jenisId = \App\Models\Barang::deteksiJenisId($b->nama_barang);
            if ($jenisId && !$b->jenis_id) {
                $b->jenis_id = $jenisId;
            }
            if ($b->interval_pemeliharaan_bulan === null) {
                $interval = \App\Models\Barang::deteksiIntervalRekomendasi($b->nama_barang, $b->jenis_id);
                if ($interval) {
                    $b->interval_pemeliharaan_bulan = $interval;
                }
            }
            $b->saveQuietly();
        }
    }
}