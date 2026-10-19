<?php

namespace App\Http\Controllers;

use App\Models\Kendaraan;
use App\Models\ServisKendaraan;
use Illuminate\Http\Request;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Support\Facades\Storage;

class TransportasiController extends Controller
{
    public function index(Request $request)
    {
        $filter = $request->only(['q', 'jenis_kendaraan', 'status', 'status_servis']);

        $allKendaraan = Kendaraan::with(['servis.user'])->orderBy('nama_kendaraan')->get();

        // Hitung Ringkasan KPI
        $totalKendaraan = $allKendaraan->count();
        $kendaraanTersedia = $allKendaraan->where('status', 'Tersedia')->count();
        $kendaraanPerluServis = $allKendaraan->filter(function ($k) {
            return in_array($k->status_servis['prioritas'], [1, 2]);
        })->count();
        $totalBiayaServisTahunIni = ServisKendaraan::whereYear('tanggal_servis', now()->year)->sum('biaya');

        // Filter data
        $filtered = $allKendaraan;

        if (!empty($filter['q'])) {
            $keyword = strtolower($filter['q']);
            $filtered = $filtered->filter(function ($k) use ($keyword) {
                return str_contains(strtolower($k->nama_kendaraan), $keyword)
                    || str_contains(strtolower($k->plat_nomor), $keyword)
                    || str_contains(strtolower($k->merek_tipe ?? ''), $keyword)
                    || str_contains(strtolower($k->penanggung_jawab ?? ''), $keyword);
            });
        }

        if (!empty($filter['jenis_kendaraan'])) {
            $filtered = $filtered->filter(function ($k) use ($filter) {
                return $k->jenis_kendaraan === $filter['jenis_kendaraan'];
            });
        }

        if (!empty($filter['status'])) {
            $filtered = $filtered->filter(function ($k) use ($filter) {
                return $k->status === $filter['status'];
            });
        }

        if (!empty($filter['status_servis'])) {
            $filtered = $filtered->filter(function ($k) use ($filter) {
                return $k->status_servis['status'] === $filter['status_servis'];
            });
        }

        // Urutkan berdasarkan prioritas servis (Jatuh tempo -> Mendekati -> Aman)
        $sorted = $filtered->sortBy([
            fn($a, $b) => $a->status_servis['prioritas'] <=> $b->status_servis['prioritas'],
            fn($a, $b) => strcmp($a->nama_kendaraan, $b->nama_kendaraan),
        ])->values();

        $perPage = 10;
        $currentPage = LengthAwarePaginator::resolveCurrentPage();
        $currentItems = $sorted->slice(($currentPage - 1) * $perPage, $perPage)->values();
        $kendaraanList = new LengthAwarePaginator(
            $currentItems,
            $sorted->count(),
            $perPage,
            $currentPage,
            ['path' => route('transportasi.index'), 'query' => $request->query()]
        );

        if ($request->ajax()) {
            return view('transportasi._table', compact('kendaraanList'));
        }

        return view('transportasi.index', compact(
            'kendaraanList',
            'filter',
            'totalKendaraan',
            'kendaraanTersedia',
            'kendaraanPerluServis',
            'totalBiayaServisTahunIni'
        ));
    }

    public function store(Request $request)
    {
        $this->authorizeAdmin();

        $validated = $request->validate([
            'nama_kendaraan' => 'required|string|max:150',
            'plat_nomor' => 'required|string|max:30|unique:kendaraan,plat_nomor',
            'nup' => 'nullable|string|max:50',
            'kode_barang' => 'nullable|string|max:50',
            'jenis_kendaraan' => 'required|in:Mobil,Motor,Bus/Minibus,Lainnya',
            'model_kendaraan' => 'nullable|string|max:100',
            'merek_tipe' => 'nullable|string|max:100',
            'no_rangka' => 'nullable|string|max:100',
            'no_mesin' => 'nullable|string|max:100',
            'tahun_perolehan' => 'nullable|integer|min:1980|max:' . (date('Y') + 1),
            'masa_manfaat' => 'nullable|integer|min:1|max:30',
            'tgl_pembuatan' => 'nullable|date',
            'tgl_perolehan' => 'nullable|date',
            'harga_perolehan' => 'nullable|numeric|min:0',
            'sumber_dana' => 'nullable|string|max:50',
            'kondisi' => 'required|in:Baik,Rusak Ringan,Rusak Berat',
            'status' => 'required|in:Tersedia,Digunakan,Dalam Perawatan',
            'penanggung_jawab' => 'nullable|string|max:100',
            'interval_servis_bulan' => 'required|integer|min:1|max:36',
            'tanggal_servis_terakhir' => 'nullable|date',
            'tanggal_pajak_stnk' => 'nullable|date',
            'keterangan' => 'nullable|string|max:1000',
            'foto' => 'nullable|image|mimes:jpeg,png,jpg,webp|max:3072',
        ]);

        if ($request->hasFile('foto')) {
            $path = $request->file('foto')->store('kendaraan', 'public');
            $validated['foto_url'] = '/storage/' . $path;
        }

        Kendaraan::create($validated);

        return redirect()->route('transportasi.index')->with('success', 'Kendaraan berhasil ditambahkan.');
    }

    public function update(Request $request, Kendaraan $kendaraan)
    {
        $this->authorizeAdmin();

        $validated = $request->validate([
            'nama_kendaraan' => 'required|string|max:150',
            'plat_nomor' => 'required|string|max:30|unique:kendaraan,plat_nomor,' . $kendaraan->kendaraan_id . ',kendaraan_id',
            'nup' => 'nullable|string|max:50',
            'kode_barang' => 'nullable|string|max:50',
            'jenis_kendaraan' => 'required|in:Mobil,Motor,Bus/Minibus,Lainnya',
            'model_kendaraan' => 'nullable|string|max:100',
            'merek_tipe' => 'nullable|string|max:100',
            'no_rangka' => 'nullable|string|max:100',
            'no_mesin' => 'nullable|string|max:100',
            'tahun_perolehan' => 'nullable|integer|min:1980|max:' . (date('Y') + 1),
            'masa_manfaat' => 'nullable|integer|min:1|max:30',
            'tgl_pembuatan' => 'nullable|date',
            'tgl_perolehan' => 'nullable|date',
            'harga_perolehan' => 'nullable|numeric|min:0',
            'sumber_dana' => 'nullable|string|max:50',
            'kondisi' => 'required|in:Baik,Rusak Ringan,Rusak Berat',
            'status' => 'required|in:Tersedia,Digunakan,Dalam Perawatan',
            'penanggung_jawab' => 'nullable|string|max:100',
            'interval_servis_bulan' => 'required|integer|min:1|max:36',
            'tanggal_servis_terakhir' => 'nullable|date',
            'tanggal_pajak_stnk' => 'nullable|date',
            'keterangan' => 'nullable|string|max:1000',
            'foto' => 'nullable|image|mimes:jpeg,png,jpg,webp|max:3072',
        ]);

        if ($request->hasFile('foto')) {
            if ($kendaraan->foto_url) {
                $oldPath = str_replace('/storage/', '', $kendaraan->foto_url);
                if (Storage::disk('public')->exists($oldPath)) {
                    Storage::disk('public')->delete($oldPath);
                }
            }
            $path = $request->file('foto')->store('kendaraan', 'public');
            $validated['foto_url'] = '/storage/' . $path;
        }

        $kendaraan->update($validated);

        return back()->with('success', 'Data kendaraan berhasil diperbarui.');
    }

    public function destroy(Kendaraan $kendaraan)
    {
        $this->authorizeAdmin();

        if ($kendaraan->foto_url) {
            $oldPath = str_replace('/storage/', '', $kendaraan->foto_url);
            if (Storage::disk('public')->exists($oldPath)) {
                Storage::disk('public')->delete($oldPath);
            }
        }

        $kendaraan->delete();

        return redirect()->route('transportasi.index')->with('success', 'Kendaraan berhasil dihapus.');
    }

    public function show(Kendaraan $kendaraan)
    {
        $kendaraan->load(['servis.user']);
        return view('transportasi.show', compact('kendaraan'));
    }

    public function catatServis(Request $request, Kendaraan $kendaraan)
    {
        $this->authorizePetugas();

        $validated = $request->validate([
            'tanggal_servis' => 'required|date',
            'jenis_servis' => 'required|in:Servis Rutin,Ganti Oli,Perbaikan Mesin,Ganti Ban/Aki,Uji Emisi / KIR,Lainnya',
            'odometer_km' => 'nullable|integer|min:0',
            'biaya' => 'nullable|numeric|min:0',
            'bengkel_pelaksana' => 'nullable|string|max:150',
            'catatan' => 'nullable|string|max:1000',
        ]);

        ServisKendaraan::create([
            ...$validated,
            'kendaraan_id' => $kendaraan->kendaraan_id,
            'user_id' => auth()->id(),
            'biaya' => $validated['biaya'] ?? 0,
        ]);

        return back()->with('success', "Servis kendaraan {$kendaraan->nama_kendaraan} ({$kendaraan->plat_nomor}) berhasil dicatat.");
    }

    public function riwayatServis(Kendaraan $kendaraan)
    {
        $riwayat = $kendaraan->servis()->with('user')->get();
        return response()->json([
            'kendaraan' => $kendaraan,
            'riwayat' => $riwayat,
            'total_biaya' => $riwayat->sum('biaya'),
        ]);
    }

    private function authorizeAdmin(): void
    {
        if (!in_array(auth()->user()->role, ['admin'])) {
            abort(403, 'Hanya Admin yang dapat mengelola data kendaraan.');
        }
    }

    private function authorizePetugas(): void
    {
        if (!in_array(auth()->user()->role, ['admin', 'teknisi', 'dosen_tendik'])) {
            abort(403, 'Anda tidak memiliki hak akses untuk mencatat servis kendaraan.');
        }
    }
}
