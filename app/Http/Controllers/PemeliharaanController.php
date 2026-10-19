<?php

namespace App\Http\Controllers;

use App\Models\Pemeliharaan;
use App\Models\Barang;
use App\Models\Vendor;
use App\Models\User;
use App\Models\Pengajuan;
use Illuminate\Http\Request;

class PemeliharaanController extends Controller
{
    public function index(Request $request)
    {
        return redirect()->route('rekomendasi.index');
    }

    public function store(Request $request)
    {
        $this->authorizeRole();

        $validated = $request->validate([
            'barang_id' => 'required|exists:barang,barang_id',
            'pengajuan_id' => 'nullable|exists:pengajuan,pengajuan_id',
            'teknisi_id' => 'nullable|exists:users,user_id',
            'vendor_id' => 'nullable|exists:vendor,vendor_id',
            'jenis_tindakan' => 'required|in:Perbaikan,Pemeliharaan Rutin,Penggantian',
            'kerusakan' => 'nullable|string|max:500',
            'biaya' => 'nullable|numeric|min:0',
            'tanggal_tindakan' => 'required|date',
            'keterangan' => 'nullable|string',
        ]);

        Pemeliharaan::create($validated);

        // Update tanggal servis terakhir di barang, dasar hitung jadwal berikutnya
        $barang = Barang::find($validated['barang_id']);
        if ($validated['tanggal_tindakan'] >= ($barang->tanggal_pemeliharaan_terakhir ?? '0000-00-00')) {
            $barang->update(['tanggal_pemeliharaan_terakhir' => $validated['tanggal_tindakan']]);
        }

        // Barang yang baru diservis otomatis kondisinya jadi Baik lagi
        if (in_array($validated['jenis_tindakan'], ['Perbaikan', 'Penggantian'])) {
            $barang->update([
                'kondisi_baik' => $barang->jumlah,
                'kondisi_rusak_ringan' => 0,
                'kondisi_rusak_berat' => 0,
            ]);
        }

        // Tandai notifikasi jadwal untuk barang ini sudah selesai ditindaklanjuti
        $barang->notifikasi()->where('sudah_dibaca', false)->update(['sudah_dibaca' => true]);

        return redirect()->route('rekomendasi.index')->with('success', 'Riwayat pemeliharaan / servis berhasil dicatat.');
    }

    private function authorizeRole(): void
    {
        if (!in_array(auth()->user()->role, ['admin', 'teknisi'])) {
            abort(403, 'Hanya Admin atau Teknisi yang dapat mencatat pemeliharaan.');
        }
    }
}