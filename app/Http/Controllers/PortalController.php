<?php

namespace App\Http\Controllers;

use App\Models\Ruangan;
use App\Models\Gedung;
use App\Models\Barang;
use Illuminate\Http\Request;

class PortalController extends Controller
{
    public function index(Request $request)
    {
        $query = Ruangan::with('gedung')->withCount('barang')->withSum('barang', 'total_harga');

        if ($request->filled('gedung_id')) {
            $query->where('gedung_id', $request->gedung_id);
        }

        if ($request->filled('cari')) {
            $query->where(function ($q) use ($request) {
                $q->where('nama_ruang', 'like', '%' . $request->cari . '%')
                    ->orWhere('kode_ruang', 'like', '%' . $request->cari . '%');
            });
        }

        $ruanganList = $query->orderBy('nama_ruang')->paginate(12)->withQueryString();
        $gedungList = Gedung::orderBy('nama_gedung')->get();

        if ($request->ajax()) {
            return view('portal._grid', compact('ruanganList'));
        }

        $totalRuangan = Ruangan::count();
        $totalBarang = Barang::sum('jumlah');
        $totalNilaiSeluruh = Barang::sum('total_harga');

        return view('portal.index', compact('ruanganList', 'gedungList', 'totalRuangan', 'totalBarang', 'totalNilaiSeluruh'));
    }

    public function show(Request $request, Ruangan $ruangan)
    {
        $ruangan->load('gedung');

        $query = $ruangan->barang();
        if ($request->filled('kondisi')) {
            if ($request->kondisi === 'Baik') {
                $query->where('kondisi_baik', '>', 0);
            } elseif ($request->kondisi === 'Rusak Ringan') {
                $query->where('kondisi_rusak_ringan', '>', 0);
            } elseif ($request->kondisi === 'Rusak Berat') {
                $query->where('kondisi_rusak_berat', '>', 0);
            }
        }
        $barang = $query->orderBy('nama_barang')->paginate(10)->withQueryString();

        $totalItem = $ruangan->barang()->sum('jumlah');
        $totalNilai = $ruangan->barang()->sum('total_harga');

        if ($request->ajax()) {
            return view('portal._barang_table', compact('barang'));
        }

        return view('portal.show', compact('ruangan', 'barang', 'totalItem', 'totalNilai'));
    }
}