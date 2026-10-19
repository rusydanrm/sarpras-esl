<?php

namespace App\Http\Controllers;

use App\Models\Peminjaman;
use App\Models\Ruangan;
use App\Models\Barang;
use Illuminate\Http\Request;

class PeminjamanController extends Controller
{
    public function index(Request $request)
    {
        $query = Peminjaman::with(['ruangan.gedung', 'barang.ruangan', 'peminjam']);

        if (in_array(auth()->user()->role, ['dosen_tendik', 'teknisi'])) {
            $query->where('user_id', auth()->id());
        }

        $baseQuery = Peminjaman::query();
        if (in_array(auth()->user()->role, ['dosen_tendik', 'teknisi'])) {
            $baseQuery->where('user_id', auth()->id());
        }

        $statusCounts = (clone $baseQuery)
            ->select('status', \Illuminate\Support\Facades\DB::raw('count(*) as total'))
            ->groupBy('status')
            ->pluck('total', 'status')
            ->toArray();
        $totalSemua = array_sum($statusCounts);

        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        if ($request->filled('jenis')) {
            $query->where('jenis_peminjaman', $request->jenis);
        }

        if ($request->filled('cari')) {
            $query->where(function ($q) use ($request) {
                $q->whereHas('ruangan', fn($r) => $r->where('nama_ruang', 'like', '%' . $request->cari . '%'))
                  ->orWhereHas('barang', fn($b) => $b->where('nama_barang', 'like', '%' . $request->cari . '%'))
                  ->orWhereHas('peminjam', fn($p) => $p->where('nama', 'like', '%' . $request->cari . '%'))
                  ->orWhere('keterangan', 'like', '%' . $request->cari . '%');
            });
        }

        $peminjaman = $query->orderByDesc('tanggal_mulai')->paginate(10)->withQueryString();
        $ruanganList = Ruangan::with('gedung')->orderBy('nama_ruang')->get();
        $barangList = Barang::with('ruangan')->orderBy('nama_barang')->get();

        if ($request->ajax()) {
            return view('peminjaman._table', compact('peminjaman'));
        }

        return view('peminjaman.index', compact('peminjaman', 'ruanganList', 'barangList', 'statusCounts', 'totalSemua'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'jenis_peminjaman' => 'required|in:Ruangan,Barang',
            'ruangan_id' => 'nullable|exists:ruangan,ruangan_id',
            'barang_id' => 'nullable|exists:barang,barang_id',
            'tanggal_mulai' => 'required|date|after_or_equal:now',
            'tanggal_selesai' => 'required|date|after:tanggal_mulai',
            'keterangan' => 'required|string|max:500',
        ]);

        if ($validated['jenis_peminjaman'] === 'Ruangan') {
            $request->validate(['ruangan_id' => 'required|exists:ruangan,ruangan_id']);
            $validated['barang_id'] = null;
        } else {
            $request->validate(['barang_id' => 'required|exists:barang,barang_id']);
            $validated['ruangan_id'] = null;
        }

        $kolomCek = $validated['jenis_peminjaman'] === 'Ruangan' ? 'ruangan_id' : 'barang_id';
        $idCek = $validated['jenis_peminjaman'] === 'Ruangan' ? $validated['ruangan_id'] : $validated['barang_id'];

        $bentrok = Peminjaman::where($kolomCek, $idCek)
            ->where('status', 'Disetujui')
            ->where(function ($q) use ($validated) {
                $q->whereBetween('tanggal_mulai', [$validated['tanggal_mulai'], $validated['tanggal_selesai']])
                    ->orWhereBetween('tanggal_selesai', [$validated['tanggal_mulai'], $validated['tanggal_selesai']])
                    ->orWhere(function ($q2) use ($validated) {
                        $q2->where('tanggal_mulai', '<=', $validated['tanggal_mulai'])
                            ->where('tanggal_selesai', '>=', $validated['tanggal_selesai']);
                    });
            })
            ->exists();

        if ($bentrok) {
            $label = $validated['jenis_peminjaman'] === 'Ruangan' ? 'Ruangan' : 'Barang';
            return back()->withErrors(['tanggal_mulai' => "$label sudah dipinjam pada rentang waktu tersebut."])->withInput();
        }

        Peminjaman::create([...$validated, 'user_id' => auth()->id(), 'status' => 'Diajukan']);

        return redirect()->route('peminjaman.index')->with('success', 'Pengajuan peminjaman berhasil dikirim.');
    }

    public function setujui(Peminjaman $peminjaman)
    {
        $this->authorizeRole(['pimpinan', 'admin'], 'Hanya Pimpinan/Admin yang dapat menyetujui peminjaman.');
        $this->authorizeStatus($peminjaman, 'Diajukan');

        $peminjaman->update(['status' => 'Disetujui']);

        return back()->with('success', 'Peminjaman telah disetujui.');
    }

    public function tolak(Request $request, Peminjaman $peminjaman)
    {
        $this->authorizeRole(['pimpinan', 'admin'], 'Hanya Pimpinan/Admin yang dapat menolak peminjaman.');
        $this->authorizeStatus($peminjaman, 'Diajukan');

        $validated = $request->validate(['keterangan' => 'required|string|max:500']);

        $peminjaman->update([
            'status' => 'Ditolak',
            'keterangan' => $peminjaman->keterangan . "\n\nAlasan ditolak: " . $validated['keterangan'],
        ]);

        return back()->with('success', 'Peminjaman telah ditolak.');
    }

    public function kembalikan(Peminjaman $peminjaman)
    {
        $this->authorizeRole(['admin'], 'Hanya Admin yang dapat menandai pengembalian.');
        $this->authorizeStatus($peminjaman, 'Disetujui');

        $peminjaman->update(['status' => 'Dikembalikan']);

        return back()->with('success', 'Barang/ruangan telah ditandai dikembalikan.');
    }

    private function authorizeRole(array $roles, string $pesan): void
    {
        if (!in_array(auth()->user()->role, $roles)) {
            abort(403, $pesan);
        }
    }

    private function authorizeStatus(Peminjaman $peminjaman, string $statusDiharapkan): void
    {
        if ($peminjaman->status !== $statusDiharapkan) {
            abort(422, 'Status peminjaman sudah berubah, silakan muat ulang halaman.');
        }
    }
}