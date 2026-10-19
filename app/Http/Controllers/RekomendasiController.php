<?php

namespace App\Http\Controllers;

use App\Models\Barang;
use App\Models\Gedung;
use App\Models\JenisBarang;
use App\Models\Ruangan;
use App\Models\User;
use App\Models\Vendor;
use App\Exports\RekomendasiBarangExport;
use Illuminate\Http\Request;
use Illuminate\Pagination\LengthAwarePaginator;
use Maatwebsite\Excel\Facades\Excel as ExcelFacade;
use Barryvdh\DomPDF\Facade\Pdf;

class RekomendasiController extends Controller
{
    public function index(Request $request)
    {
        $filter = $request->only(['gedung_id', 'jenis_id', 'kondisi', 'usia_min', 'q']);

        // Ambil semua barang untuk KPI & monitoring
        $allItems = Barang::with([
            'ruangan.gedung',
            'ruangan.koordinator',
            'jenis',
            'pemeliharaan.teknisi',
            'pemeliharaan.vendor'
        ])->get();

        // Hitung Metrik Monitoring & Pemeliharaan (MR KPI)
        $totalAset = $allItems->count();
        $totalUnit = $allItems->sum('jumlah');
        $totalBaik = (int) $allItems->sum('kondisi_baik');
        $totalRusakRingan = (int) $allItems->sum('kondisi_rusak_ringan');
        $totalRusakBerat = (int) $allItems->sum('kondisi_rusak_berat');
        $totalPerluPerbaikan = $totalRusakRingan + $totalRusakBerat;
        $totalBiayaServis = $allItems->sum(fn($b) => $b->pemeliharaan->sum('biaya'));
        $totalRiwayatServis = $allItems->sum(fn($b) => $b->pemeliharaan->count());

        // Terapkan filter pada collection
        $filteredItems = $allItems;

        if (!empty($filter['gedung_id'])) {
            $filteredItems = $filteredItems->filter(function ($b) use ($filter) {
                return $b->ruangan?->gedung_id == $filter['gedung_id'];
            });
        }

        if (!empty($filter['jenis_id'])) {
            $filteredItems = $filteredItems->filter(function ($b) use ($filter) {
                return $b->jenis_id == $filter['jenis_id'];
            });
        }

        if (!empty($filter['kondisi'])) {
            $k = $filter['kondisi'];
            $filteredItems = $filteredItems->filter(function ($b) use ($k) {
                if ($k === 'Baik') return $b->kondisi_baik > 0;
                if ($k === 'Rusak Ringan') return $b->kondisi_rusak_ringan > 0;
                if ($k === 'Rusak Berat') return $b->kondisi_rusak_berat > 0;
                if ($k === 'Perlu Perbaikan') return ($b->kondisi_rusak_ringan + $b->kondisi_rusak_berat) > 0;
                return true;
            });
        }

        if (!empty($filter['usia_min'])) {
            $usiaMin = (int) $filter['usia_min'];
            $filteredItems = $filteredItems->filter(function ($b) use ($usiaMin) {
                return ($b->usia_tahun ?? 0) >= $usiaMin;
            });
        }

        if (!empty($filter['q'])) {
            $keyword = strtolower($filter['q']);
            $filteredItems = $filteredItems->filter(function ($b) use ($keyword) {
                return str_contains(strtolower($b->nama_barang), $keyword)
                    || str_contains(strtolower($b->kode_barang ?? ''), $keyword)
                    || str_contains(strtolower($b->nup ?? ''), $keyword)
                    || str_contains(strtolower($b->merek_tipe ?? ''), $keyword)
                    || str_contains(strtolower($b->ruangan?->nama_ruang ?? ''), $keyword);
            });
        }

        // Urutkan: Barang rusak berat/ringan di atas, lalu nama_barang
        $sortedItems = $filteredItems->sortBy([
            fn($a, $b) => ($b->kondisi_rusak_berat + $b->kondisi_rusak_ringan) <=> ($a->kondisi_rusak_berat + $a->kondisi_rusak_ringan),
            fn($a, $b) => strcmp($a->nama_barang, $b->nama_barang),
        ])->values();

        // Pagination manual untuk collection
        $perPage = 10;
        $currentPage = LengthAwarePaginator::resolveCurrentPage();
        $currentItems = $sortedItems->slice(($currentPage - 1) * $perPage, $perPage)->values();
        $preview = new LengthAwarePaginator(
            $currentItems,
            $sortedItems->count(),
            $perPage,
            $currentPage,
            ['path' => route('rekomendasi.index'), 'query' => $request->query()]
        );

        $gedungList = Gedung::orderBy('nama_gedung')->get();
        $jenisList = JenisBarang::orderBy('nama_jenis')->get();
        $barangList = Barang::with('ruangan')->orderBy('nama_barang')->get();
        $vendorList = \App\Models\Vendor::orderBy('nama_vendor')->get();
        $teknisiList = \App\Models\User::where('role', 'teknisi')->where('status', 'aktif')->orderBy('nama')->get();
        $pengajuanSelesai = \App\Models\Pengajuan::where('status', 'Selesai')
            ->whereIn('jenis_pengajuan', ['Perbaikan', 'Pemeliharaan', 'Penggantian'])
            ->whereDoesntHave('pemeliharaan')
            ->with('barang')
            ->get();

        if ($request->ajax()) {
            return view('rekomendasi._table', compact('preview'));
        }

        return view('rekomendasi.index', compact(
            'preview',
            'gedungList',
            'jenisList',
            'filter',
            'totalAset',
            'totalUnit',
            'totalBaik',
            'totalRusakRingan',
            'totalRusakBerat',
            'totalPerluPerbaikan',
            'totalBiayaServis',
            'totalRiwayatServis',
            'barangList',
            'vendorList',
            'teknisiList',
            'pengajuanSelesai'
        ));
    }

    public function eksporExcel(Request $request)
    {
        $filter = $request->only(['gedung_id', 'jenis_id', 'kondisi', 'usia_min', 'q']);

        return ExcelFacade::download(
            new RekomendasiBarangExport($filter),
            'laporan-monitoring-repair-mr-' . now()->format('Ymd-His') . '.xlsx'
        );
    }

    public function eksporPdf(Request $request)
    {
        $filter = $request->only(['gedung_id', 'jenis_id', 'kondisi', 'usia_min', 'q']);

        $export = new RekomendasiBarangExport($filter);
        $data = $export->collection();

        $totalBiayaServis = $data->sum(fn($b) => $b->pemeliharaan->sum('biaya'));
        $totalRusakRingan = $data->sum('kondisi_rusak_ringan');
        $totalRusakBerat = $data->sum('kondisi_rusak_berat');
        $totalPerluPerbaikan = $totalRusakRingan + $totalRusakBerat;

        $pdf = Pdf::loadView('rekomendasi.pdf', [
            'data' => $data,
            'tanggal' => now()->format('d/m/Y H:i'),
            'totalBiayaServis' => $totalBiayaServis,
            'totalRusakRingan' => $totalRusakRingan,
            'totalRusakBerat' => $totalRusakBerat,
            'totalPerluPerbaikan' => $totalPerluPerbaikan,
            'filter' => $filter,
        ])->setPaper('a4', 'landscape');

        return $pdf->download('laporan-monitoring-repair-mr-' . now()->format('Ymd-His') . '.pdf');
    }

    public function show(Barang $barang)
    {
        $barang->load([
            'ruangan.gedung',
            'ruangan.koordinator',
            'jenis',
            'pemeliharaan.teknisi',
            'pemeliharaan.vendor',
        ]);

        $teknisiList = \App\Models\User::where('role', 'teknisi')->where('status', 'aktif')->orderBy('nama')->get();
        $vendorList = \App\Models\Vendor::orderBy('nama_vendor')->get();
        $gedungList = Gedung::orderBy('nama_gedung')->get();
        $jenisList = JenisBarang::orderBy('nama_jenis')->get();
        $ruanganList = Ruangan::with('gedung')->orderBy('nama_ruang')->get();

        return view('rekomendasi.show', compact('barang', 'teknisiList', 'vendorList', 'gedungList', 'jenisList', 'ruanganList'));
    }
}
