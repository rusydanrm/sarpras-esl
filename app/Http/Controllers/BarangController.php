<?php

namespace App\Http\Controllers;

use App\Models\Barang;
use App\Models\Ruangan;
use App\Models\Gedung;
use App\Models\JenisBarang;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class BarangController extends Controller
{

    public function index(Request $request)
    {
        $totalPerNama = Barang::select('nama_barang', DB::raw('SUM(jumlah) as total_group'))
            ->groupBy('nama_barang');

        $query = Barang::with([
            'ruangan.gedung',
            'ruangan.koordinator',
            'jenis',
            'pemeliharaan.teknisi',
            'pemeliharaan.vendor'
        ])
            ->leftJoinSub($totalPerNama, 'summary_barang', function ($join) {
                $join->on('barang.nama_barang', '=', 'summary_barang.nama_barang');
            })
            ->select('barang.*');

        if ($request->filled('jenis_id')) {
            $query->where('barang.jenis_id', $request->jenis_id);
        }

        if ($request->filled('gedung_id')) {
            $query->whereHas('ruangan', fn($q) => $q->where('gedung_id', $request->gedung_id));
        }

        if ($request->filled('kondisi')) {
            if ($request->kondisi === 'Baik') {
                $query->where('barang.kondisi_baik', '>', 0);
            } elseif ($request->kondisi === 'Rusak Ringan') {
                $query->where('barang.kondisi_rusak_ringan', '>', 0);
            } elseif ($request->kondisi === 'Rusak Berat') {
                $query->where('barang.kondisi_rusak_berat', '>', 0);
            }
        }

        if ($request->filled('cari')) {
            $query->where(function ($q) use ($request) {
                $q->where('barang.nama_barang', 'like', '%' . $request->cari . '%')
                    ->orWhere('barang.kode_barang', 'like', '%' . $request->cari . '%')
                    ->orWhere('barang.merek_tipe', 'like', '%' . $request->cari . '%');
            });
        }

        $barang = $query->orderByDesc('summary_barang.total_group')
            ->orderBy('barang.nama_barang')
            ->orderByDesc('barang.jumlah')
            ->paginate(10)
            ->withQueryString();

        $gedungList = Gedung::orderBy('nama_gedung')->get();
        $jenisList = JenisBarang::orderBy('nama_jenis')->get();
        $ruanganList = Ruangan::with('gedung')->orderBy('nama_ruang')->get();

        $totalSemuaBarang = Barang::sum('jumlah');
        $kategoriTabs = JenisBarang::select(
            'jenis_barang.jenis_id',
            'jenis_barang.nama_jenis',
            DB::raw('COALESCE(SUM(barang.jumlah), 0) as total_unit')
        )
            ->leftJoin('barang', 'barang.jenis_id', '=', 'jenis_barang.jenis_id')
            ->groupBy('jenis_barang.jenis_id', 'jenis_barang.nama_jenis')
            ->orderByDesc('total_unit')
            ->orderBy('jenis_barang.nama_jenis')
            ->get();

        if ($request->ajax()) {
            return view('barang._table', compact('barang'));
        }

        return view('barang.index', compact('barang', 'gedungList', 'jenisList', 'ruanganList', 'kategoriTabs', 'totalSemuaBarang'));
    }

    public function show(Barang $barang)
    {
        return redirect()->route('rekomendasi.show', $barang);
    }
    
    public function store(Request $request, Ruangan $ruangan)
    {
        $this->authorizeEdit($ruangan);

        $validated = $request->validate([
            'jenis_id' => 'nullable|exists:jenis_barang,jenis_id',
            'kode_barang' => 'nullable|string|max:50',
            'nama_barang' => 'required|string|max:150',
            'nup' => 'nullable|string|max:50',
            'merek_tipe' => 'nullable|string|max:100',
            'tahun_perolehan' => 'nullable|integer|min:1990|max:'.date('Y'),
            'harga_satuan' => 'required|numeric|min:0',
            'kondisi_baik' => 'required|integer|min:0',
            'kondisi_rusak_ringan' => 'required|integer|min:0',
            'kondisi_rusak_berat' => 'required|integer|min:0',
            'satuan' => 'nullable|string|max:20',
            'sumber_dana' => 'nullable|string|max:100',
            'interval_pemeliharaan_bulan' => 'nullable|integer|min:1|max:60',
            'keterangan' => 'nullable|string',
        ]);

        $jumlah = $validated['kondisi_baik'] + $validated['kondisi_rusak_ringan'] + $validated['kondisi_rusak_berat'];
        if ($jumlah < 1) {
            return back()->withErrors(['kondisi_baik' => 'Total jumlah barang minimal 1 unit.'])->withInput();
        }
        $validated['jumlah'] = $jumlah;

        $ruangan->barang()->create($validated);

        return redirect()->route('ruangan.show', $ruangan)->with('success', 'Barang berhasil ditambahkan.');
    }

    public function storeGlobal(Request $request)
    {
        $validated = $request->validate([
            'ruangan_id' => 'required|exists:ruangan,ruangan_id',
            'jenis_id' => 'nullable|exists:jenis_barang,jenis_id',
            'kode_barang' => 'nullable|string|max:50',
            'nama_barang' => 'required|string|max:150',
            'nup' => 'nullable|string|max:50',
            'merek_tipe' => 'nullable|string|max:100',
            'tahun_perolehan' => 'nullable|integer|min:1990|max:' . date('Y'),
            'harga_satuan' => 'required|numeric|min:0',
            'kondisi_baik' => 'required|integer|min:0',
            'kondisi_rusak_ringan' => 'required|integer|min:0',
            'kondisi_rusak_berat' => 'required|integer|min:0',
            'satuan' => 'nullable|string|max:20',
            'sumber_dana' => 'nullable|string|max:100',
            'interval_pemeliharaan_bulan' => 'nullable|integer|min:1|max:60',
            'keterangan' => 'nullable|string',
        ]);

        $jumlah = $validated['kondisi_baik'] + $validated['kondisi_rusak_ringan'] + $validated['kondisi_rusak_berat'];
        if ($jumlah < 1) {
            return back()->withErrors(['kondisi_baik' => 'Total jumlah barang minimal 1 unit.'])->withInput();
        }
        $validated['jumlah'] = $jumlah;

        $ruangan = Ruangan::findOrFail($validated['ruangan_id']);

        // Dosen/Tendik cuma boleh tambah barang ke ruangan miliknya sendiri
        if (auth()->user()->role === 'dosen_tendik' && ! $ruangan->dikoordinasiOleh(auth()->id())) {
            abort(403, 'Anda hanya dapat menambahkan barang ke ruangan yang menjadi tanggung jawab Anda.');
        }

        Barang::create($validated);

        return redirect()->route('barang.index')->with('success', 'Barang berhasil ditambahkan.');
    }

    public function update(Request $request, Barang $barang)
    {
        $this->authorizeEdit($barang->ruangan);

        $validated = $request->validate([
            'jenis_id' => 'nullable|exists:jenis_barang,jenis_id',
            'kode_barang' => 'nullable|string|max:50',
            'nama_barang' => 'required|string|max:150',
            'nup' => 'nullable|string|max:50',
            'merek_tipe' => 'nullable|string|max:100',
            'tahun_perolehan' => 'nullable|integer|min:1990|max:'.date('Y'),
            'harga_satuan' => 'required|numeric|min:0',
            'kondisi_baik' => 'required|integer|min:0',
            'kondisi_rusak_ringan' => 'required|integer|min:0',
            'kondisi_rusak_berat' => 'required|integer|min:0',
            'satuan' => 'nullable|string|max:20',
            'sumber_dana' => 'nullable|string|max:100',
            'interval_pemeliharaan_bulan' => 'nullable|integer|min:1|max:60',
            'keterangan' => 'nullable|string',
        ]);

        $jumlah = $validated['kondisi_baik'] + $validated['kondisi_rusak_ringan'] + $validated['kondisi_rusak_berat'];
        if ($jumlah < 1) {
            return back()->withErrors(['kondisi_baik' => 'Total jumlah barang minimal 1 unit.'])->withInput();
        }
        $validated['jumlah'] = $jumlah;

        $barang->update($validated);

        return back()->with('success', 'Barang berhasil diperbarui.');
    }

    public function destroy(Barang $barang)
    {
        $ruangan = $barang->ruangan;
        $this->authorizeEdit($ruangan);

        $barang->delete();

        return redirect()->route('ruangan.show', $ruangan)->with('success', 'Barang berhasil dihapus.');
    }

    private function authorizeEdit(Ruangan $ruangan): void
    {
        $user = auth()->user();

        $bolehEdit = $user->role === 'admin'
            || ($user->role === 'dosen_tendik' && $ruangan->dikoordinasiOleh($user->user_id));
        if (! $bolehEdit) {
            abort(403, 'Anda tidak memiliki akses untuk mengelola barang di ruangan ini.');
        }
    }
}