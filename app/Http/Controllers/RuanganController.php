<?php

namespace App\Http\Controllers;

use App\Models\Ruangan;
use App\Models\Gedung;
use App\Models\User;
use Illuminate\Http\Request;

class RuanganController extends Controller
{
    public function index(Request $request)
    {
        $query = Ruangan::with(['gedung', 'koordinator'])->withCount('barang');

        if (auth()->user()->role === 'dosen_tendik') {
            $query->whereHas('koordinator', fn($q) => $q->where('users.user_id', auth()->id()));
        }

        if ($request->filled('gedung_id')) {
            $query->where('gedung_id', $request->gedung_id);
        }

        if ($request->filled('cari')) {
            $query->where(function ($q) use ($request) {
                $q->where('nama_ruang', 'like', '%' . $request->cari . '%')
                    ->orWhere('kode_ruang', 'like', '%' . $request->cari . '%');
            });
        }

        $ruangan = $query->orderBy('nama_ruang')->paginate(10)->withQueryString();
        $gedungList = Gedung::orderBy('nama_gedung')->get();
        $koordinatorList = User::where('role', 'dosen_tendik')->orderBy('nama')->get();

        if ($request->ajax()) {
            return view('ruangan._table', compact('ruangan'));
        }

        return view('ruangan.index', compact('ruangan', 'gedungList', 'koordinatorList'));
    }

    public function store(Request $request)
    {
        $this->authorizeAdmin();

        $validated = $request->validate([
            'gedung_id' => 'required|exists:gedung,gedung_id',
            'koordinator_id' => 'nullable|array',
            'koordinator_id.*' => 'exists:users,user_id',
            'kode_ruang' => 'nullable|string|max:50',
            'nama_ruang' => 'required|string|max:150',
            'wing' => 'nullable|string|max:20',
            'lantai' => 'nullable|integer|min:0',
            'lokasi' => 'nullable|string|max:50',
            'panjang_m' => 'nullable|numeric|min:0',
            'lebar_m' => 'nullable|numeric|min:0',
            'keterangan' => 'nullable|string|max:100',
        ]);

        $validated['lantai'] = $validated['lantai'] ?? 1;
        $validated['panjang_m'] = $validated['panjang_m'] ?? 0;
        $validated['lebar_m'] = $validated['lebar_m'] ?? 0;

        $koordinatorIds = $validated['koordinator_id'] ?? [];
        unset($validated['koordinator_id']);

        $ruangan = Ruangan::create($validated);
        $ruangan->koordinator()->sync($koordinatorIds);

        return redirect()->route('ruangan.index')->with('success', 'Ruangan berhasil ditambahkan.');
    }

    public function update(Request $request, Ruangan $ruangan)
    {
        $this->authorizeEdit($ruangan);

        $validated = $request->validate([
            'gedung_id' => 'required|exists:gedung,gedung_id',
            'koordinator_id' => 'nullable|array',
            'koordinator_id.*' => 'exists:users,user_id',
            'kode_ruang' => 'nullable|string|max:50',
            'nama_ruang' => 'required|string|max:150',
            'wing' => 'nullable|string|max:20',
            'lantai' => 'nullable|integer|min:0',
            'lokasi' => 'nullable|string|max:50',
            'panjang_m' => 'nullable|numeric|min:0',
            'lebar_m' => 'nullable|numeric|min:0',
            'keterangan' => 'nullable|string|max:100',
        ]);

        $validated['lantai'] = $validated['lantai'] ?? 1;
        $validated['panjang_m'] = $validated['panjang_m'] ?? 0;
        $validated['lebar_m'] = $validated['lebar_m'] ?? 0;

        $koordinatorIds = $validated['koordinator_id'] ?? null;
        unset($validated['koordinator_id']);

        // Dosen/Tendik tidak boleh pindah gedung atau ubah daftar koordinator
        if (auth()->user()->role === 'dosen_tendik') {
            unset($validated['gedung_id']);
            $koordinatorIds = null; // jangan sentuh assignment koordinator
        }

        $ruangan->update($validated);

        if ($koordinatorIds !== null) {
            $ruangan->koordinator()->sync($koordinatorIds);
        }

        return redirect()->route('ruangan.index')->with('success', 'Ruangan berhasil diperbarui.');
    }

    public function destroy(Ruangan $ruangan)
    {
        $this->authorizeAdmin();
        $ruangan->delete();

        return redirect()->route('ruangan.index')->with('success', 'Ruangan berhasil dihapus.');
    }

    public function show(Ruangan $ruangan)
    {
        if (auth()->user()->role === 'dosen_tendik' && !$ruangan->dikoordinasiOleh(auth()->id())) {
            abort(403);
        }

        $ruangan->load([
            'gedung',
            'koordinator',
            'barang.jenis',
            'barang.pemeliharaan.teknisi',
            'barang.pemeliharaan.vendor'
        ]);

        $jenisList = \App\Models\JenisBarang::orderBy('nama_jenis')->get();

        return view('ruangan.show', compact('ruangan', 'jenisList'));
    }

    private function authorizeEdit(Ruangan $ruangan): void
    {
        $user = auth()->user();

        $bolehEdit = $user->role === 'admin'
            || ($user->role === 'dosen_tendik' && $ruangan->dikoordinasiOleh($user->user_id));

        if (!$bolehEdit) {
            abort(403, 'Anda tidak memiliki akses untuk mengubah ruangan ini.');
        }
    }

    private function authorizeAdmin(): void
    {
        if (auth()->user()->role !== 'admin') {
            abort(403, 'Hanya Admin yang dapat melakukan tindakan ini.');
        }
    }
}