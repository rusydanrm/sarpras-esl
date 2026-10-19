<?php

namespace App\Http\Controllers;

use App\Models\Pengajuan;
use App\Models\Barang;
use App\Models\Ruangan;
use Illuminate\Http\Request;

class PengajuanController extends Controller
{
    public function index(Request $request)
    {
        $query = Pengajuan::with(['pemohon', 'verifikator', 'barang', 'ruangan']);

        // Dosen/Tendik dan Teknisi hanya lihat pengajuan miliknya sendiri
        if (in_array(auth()->user()->role, ['dosen_tendik', 'teknisi'])) {
            $query->where('user_id', auth()->id());
        }

        $baseQuery = Pengajuan::query();
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
            $query->where('jenis_pengajuan', $request->jenis);
        }

        $pengajuan = $query->orderByDesc('tanggal_pengajuan')->paginate(10)->withQueryString();
        $barangList = Barang::with('ruangan')->orderBy('nama_barang')->get();
        $ruanganList = Ruangan::orderBy('nama_ruang')->get();

        if ($request->ajax()) {
            return view('pengajuan._table', compact('pengajuan'));
        }

        return view('pengajuan.index', compact('pengajuan', 'barangList', 'ruanganList', 'statusCounts', 'totalSemua'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'jenis_pengajuan' => 'required|in:Pengadaan,Perbaikan,Pemeliharaan,Penggantian',
            'barang_id' => 'nullable|exists:barang,barang_id',
            'ruangan_id' => 'nullable|exists:ruangan,ruangan_id',
            'deskripsi' => 'required|string|max:1000',
        ]);

        // Validasi kondisional sesuai jenis pengajuan
        if (in_array($validated['jenis_pengajuan'], ['Perbaikan', 'Pemeliharaan', 'Penggantian'])) {
            $request->validate(['barang_id' => 'required|exists:barang,barang_id']);
        }

        if ($validated['jenis_pengajuan'] === 'Peminjaman') {
            $request->validate(['ruangan_id' => 'required|exists:ruangan,ruangan_id']);
        }

        Pengajuan::create([
            ...$validated,
            'user_id' => auth()->id(),
            'status' => 'Diajukan',
            'tanggal_pengajuan' => now(),
        ]);

        return redirect()->route('pengajuan.index')->with('success', 'Pengajuan berhasil dikirim.');
    }

    public function verifikasi(Request $request, Pengajuan $pengajuan)
    {
        $this->authorizeRole(['admin'], 'Hanya Admin yang dapat memverifikasi pengajuan.');
        $this->authorizeStatus($pengajuan, 'Diajukan');

        $pengajuan->update([
            'status' => 'Diverifikasi',
            'verifikator_id' => auth()->id(),
            'tanggal_verifikasi' => now(),
        ]);

        return back()->with('success', 'Pengajuan telah diverifikasi, menunggu persetujuan Pimpinan.');
    }

    public function setujui(Request $request, Pengajuan $pengajuan)
    {
        $this->authorizeRole(['pimpinan'], 'Hanya Pimpinan yang dapat menyetujui pengajuan.');
        $this->authorizeStatus($pengajuan, 'Diverifikasi');

        $validated = $request->validate(['catatan_verifikasi' => 'nullable|string|max:500']);

        $pengajuan->update([
            'status' => 'Disetujui',
            'catatan_verifikasi' => $validated['catatan_verifikasi'] ?? null,
        ]);

        return back()->with('success', 'Pengajuan telah disetujui.');
    }

    public function tolak(Request $request, Pengajuan $pengajuan)
    {
        $this->authorizeRole(['pimpinan'], 'Hanya Pimpinan yang dapat menolak pengajuan.');
        $this->authorizeStatus($pengajuan, 'Diverifikasi');

        $validated = $request->validate(['catatan_verifikasi' => 'required|string|max:500']);

        $pengajuan->update([
            'status' => 'Ditolak',
            'catatan_verifikasi' => $validated['catatan_verifikasi'],
        ]);

        return back()->with('success', 'Pengajuan telah ditolak.');
    }

    public function proses(Pengajuan $pengajuan)
    {
        $this->authorizeRole(['admin', 'teknisi'], 'Anda tidak memiliki akses untuk memproses pengajuan ini.');
        $this->authorizeStatus($pengajuan, 'Disetujui');

        $pengajuan->update(['status' => 'Diproses']);

        return back()->with('success', 'Pengajuan sedang diproses.');
    }

    public function selesai(Pengajuan $pengajuan)
    {
        $this->authorizeRole(['admin', 'teknisi'], 'Anda tidak memiliki akses untuk menyelesaikan pengajuan ini.');
        $this->authorizeStatus($pengajuan, 'Diproses');

        $pengajuan->update(['status' => 'Selesai']);

        return back()->with('success', 'Pengajuan telah diselesaikan.');
    }

    private function authorizeRole(array $roles, string $pesan): void
    {
        if (! in_array(auth()->user()->role, $roles)) {
            abort(403, $pesan);
        }
    }

    private function authorizeStatus(Pengajuan $pengajuan, string $statusDiharapkan): void
    {
        if ($pengajuan->status !== $statusDiharapkan) {
            abort(422, 'Status pengajuan sudah berubah, silakan muat ulang halaman.');
        }
    }
}