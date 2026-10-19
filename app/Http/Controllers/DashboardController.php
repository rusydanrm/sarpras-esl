<?php

namespace App\Http\Controllers;

use App\Models\Barang;
use App\Models\Ruangan;
use App\Models\Gedung;
use Illuminate\Support\Facades\DB;

class DashboardController extends Controller
{
    public function index()
    {
        $totalRuangan = Ruangan::count();
        $totalBarang = Barang::sum('jumlah');
        $totalNilaiAset = Barang::sum('total_harga');

        // 1 query saja untuk hitung semua kondisi sekaligus
        $kondisiSum = Barang::selectRaw('
            COALESCE(SUM(kondisi_baik), 0) as total_baik,
            COALESCE(SUM(kondisi_rusak_ringan), 0) as total_rusak_ringan,
            COALESCE(SUM(kondisi_rusak_berat), 0) as total_rusak_berat
        ')->first();

        $jumlahBaik = (int) ($kondisiSum->total_baik ?? 0);
        $jumlahRusakRingan = (int) ($kondisiSum->total_rusak_ringan ?? 0);
        $jumlahRusakBerat = (int) ($kondisiSum->total_rusak_berat ?? 0);
        $barangRusakBerat = $jumlahRusakBerat;

        // 1 query saja untuk jumlah barang per gedung (total dan kondisi baik untuk dual bar chart)
        $perGedung = Gedung::select(
            'gedung.nama_gedung',
            DB::raw('COALESCE(SUM(barang.jumlah), 0) as total_jumlah'),
            DB::raw('COALESCE(SUM(barang.kondisi_baik), 0) as total_baik')
        )
            ->leftJoin('ruangan', 'ruangan.gedung_id', '=', 'gedung.gedung_id')
            ->leftJoin('barang', 'barang.ruangan_id', '=', 'ruangan.ruangan_id')
            ->groupBy('gedung.gedung_id', 'gedung.nama_gedung')
            ->orderBy('gedung.nama_gedung')
            ->get();

        $labelGedung = $perGedung->pluck('nama_gedung');
        $dataGedung = $perGedung->pluck('total_jumlah');
        $dataGedungBaik = $perGedung->pluck('total_baik');

        $barangTerbaru = Barang::with('ruangan')->latest()->limit(5)->get();

        // Hitung persentase kondisi untuk progress overview
        $persenBaik = $totalBarang > 0 ? round(($jumlahBaik / $totalBarang) * 100) : 0;
        $persenRR = $totalBarang > 0 ? round(($jumlahRusakRingan / $totalBarang) * 100) : 0;
        $persenRB = $totalBarang > 0 ? round(($jumlahRusakBerat / $totalBarang) * 100) : 0;

        return view('dashboard', compact(
            'totalRuangan',
            'totalBarang',
            'totalNilaiAset',
            'barangRusakBerat',
            'labelGedung',
            'dataGedung',
            'dataGedungBaik',
            'jumlahBaik',
            'jumlahRusakRingan',
            'jumlahRusakBerat',
            'persenBaik',
            'persenRR',
            'persenRB',
            'barangTerbaru'
        ));
    }
}