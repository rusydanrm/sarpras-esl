<?php

namespace App\Console\Commands;

use App\Models\Barang;
use App\Models\Kendaraan;
use App\Models\Notifikasi;
use App\Models\User;
use Illuminate\Console\Command;
use Carbon\Carbon;

class CekJadwalPemeliharaan extends Command
{
    protected $signature = 'cek:jadwal-pemeliharaan';
    protected $description = 'Cek barang dan kendaraan yang jadwal servis/pajak sudah jatuh tempo atau mendekati, lalu buat notifikasi';

    public function handle(): void
    {
        $hariPeringatan = 14; // beri peringatan 14 hari sebelum jatuh tempo
        $recipients = User::whereIn('role', ['admin', 'teknisi'])->where('status', 'aktif')->get();
        $jumlahDibuat = 0;

        // 1. Cek Jadwal Pemeliharaan Barang
        $barangList = Barang::whereNotNull('interval_pemeliharaan_bulan')
            ->with('ruangan')
            ->get();

        foreach ($barangList as $barang) {
            $jatuhTempo = $barang->jatuh_tempo_berikutnya;
            if (!$jatuhTempo) {
                continue;
            }

            $sudahMendekati = now()->addDays($hariPeringatan)->gte($jatuhTempo);
            if (!$sudahMendekati) {
                continue;
            }

            $namaRuang = $barang->ruangan?->nama_ruang ?? 'Tanpa Ruangan';
            $statusPesan = now()->gte($jatuhTempo)
                ? 'sudah jatuh tempo'
                : 'akan jatuh tempo ' . $jatuhTempo->diffForHumans();

            foreach ($recipients as $user) {
                $sudahAda = Notifikasi::where('barang_id', $barang->barang_id)
                    ->where('user_id', $user->user_id)
                    ->where('tanggal_jatuh_tempo', $jatuhTempo->format('Y-m-d'))
                    ->where('sudah_dibaca', false)
                    ->exists();

                if ($sudahAda) {
                    continue;
                }

                Notifikasi::create([
                    'barang_id' => $barang->barang_id,
                    'kendaraan_id' => null,
                    'user_id' => $user->user_id,
                    'tipe' => 'pemeliharaan_barang',
                    'pesan' => "Jadwal servis {$barang->nama_barang} ({$namaRuang}) {$statusPesan}.",
                    'link_url' => route('rekomendasi.show', $barang, false),
                    'tanggal_jatuh_tempo' => $jatuhTempo->format('Y-m-d'),
                    'sudah_dibaca' => false,
                ]);

                $jumlahDibuat++;
            }
        }

        // 2. Cek Jadwal Servis Kendaraan
        $kendaraanList = Kendaraan::all();

        foreach ($kendaraanList as $kendaraan) {
            // A. Servis Berkala
            $jatuhTempoServis = $kendaraan->jatuh_tempo_servis;
            if ($jatuhTempoServis && now()->addDays($hariPeringatan)->gte($jatuhTempoServis)) {
                $statusPesan = now()->gte($jatuhTempoServis)
                    ? 'sudah jatuh tempo'
                    : 'akan jatuh tempo ' . $jatuhTempoServis->diffForHumans();

                foreach ($recipients as $user) {
                    $sudahAda = Notifikasi::where('kendaraan_id', $kendaraan->kendaraan_id)
                        ->where('user_id', $user->user_id)
                        ->where('tipe', 'servis_kendaraan')
                        ->where('tanggal_jatuh_tempo', $jatuhTempoServis->format('Y-m-d'))
                        ->where('sudah_dibaca', false)
                        ->exists();

                    if ($sudahAda) {
                        continue;
                    }

                    Notifikasi::create([
                        'barang_id' => null,
                        'kendaraan_id' => $kendaraan->kendaraan_id,
                        'user_id' => $user->user_id,
                        'tipe' => 'servis_kendaraan',
                        'pesan' => "Servis berkala kendaraan {$kendaraan->nama_kendaraan} ({$kendaraan->plat_nomor}) {$statusPesan}.",
                        'link_url' => route('transportasi.show', $kendaraan, false),
                        'tanggal_jatuh_tempo' => $jatuhTempoServis->format('Y-m-d'),
                        'sudah_dibaca' => false,
                    ]);

                    $jumlahDibuat++;
                }
            }

            // B. Pajak STNK Tahunan (Peringatan 30 hari sebelum jatuh tempo)
            if ($kendaraan->tanggal_pajak_stnk && now()->addDays(30)->gte($kendaraan->tanggal_pajak_stnk)) {
                $jatuhTempoPajak = Carbon::parse($kendaraan->tanggal_pajak_stnk);
                $statusPesan = now()->gte($jatuhTempoPajak)
                    ? 'sudah jatuh tempo'
                    : 'akan jatuh tempo ' . $jatuhTempoPajak->diffForHumans();

                foreach ($recipients as $user) {
                    $sudahAda = Notifikasi::where('kendaraan_id', $kendaraan->kendaraan_id)
                        ->where('user_id', $user->user_id)
                        ->where('tipe', 'pajak_stnk')
                        ->where('tanggal_jatuh_tempo', $jatuhTempoPajak->format('Y-m-d'))
                        ->where('sudah_dibaca', false)
                        ->exists();

                    if ($sudahAda) {
                        continue;
                    }

                    Notifikasi::create([
                        'barang_id' => null,
                        'kendaraan_id' => $kendaraan->kendaraan_id,
                        'user_id' => $user->user_id,
                        'tipe' => 'pajak_stnk',
                        'pesan' => "Pajak STNK kendaraan {$kendaraan->nama_kendaraan} ({$kendaraan->plat_nomor}) {$statusPesan}.",
                        'link_url' => route('transportasi.show', $kendaraan, false),
                        'tanggal_jatuh_tempo' => $jatuhTempoPajak->format('Y-m-d'),
                        'sudah_dibaca' => false,
                    ]);

                    $jumlahDibuat++;
                }
            }
        }

        $this->info("Selesai. {$jumlahDibuat} notifikasi baru dibuat untuk " . $recipients->count() . " pengguna.");
    }
}