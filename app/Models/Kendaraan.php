<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Carbon\Carbon;

class Kendaraan extends Model
{
    protected $table = 'kendaraan';
    protected $primaryKey = 'kendaraan_id';

    protected $fillable = [
        'nama_kendaraan',
        'plat_nomor',
        'nup',
        'kode_barang',
        'jenis_kendaraan',
        'model_kendaraan',
        'merek_tipe',
        'no_rangka',
        'no_mesin',
        'tahun_perolehan',
        'masa_manfaat',
        'tgl_pembuatan',
        'tgl_perolehan',
        'harga_perolehan',
        'sumber_dana',
        'kode_akun',
        'kode_rkat',
        'dokumen',
        'referensi',
        'kondisi',
        'status',
        'penanggung_jawab',
        'interval_servis_bulan',
        'tanggal_servis_terakhir',
        'tanggal_pajak_stnk',
        'keterangan',
        'foto_url',
    ];

    protected $appends = [
        'status_servis',
        'status_pajak',
        'usia_tahun',
        'usia_teks',
        'jatuh_tempo_servis',
        'nilai_penyusutan_bulan',
        'akumulasi_penyusutan',
        'nilai_buku',
        'stnk_countdown',
    ];

    protected function casts(): array
    {
        return [
            'tanggal_servis_terakhir' => 'date',
            'tanggal_pajak_stnk' => 'date',
            'tgl_pembuatan' => 'date',
            'tgl_perolehan' => 'date',
            'interval_servis_bulan' => 'integer',
            'tahun_perolehan' => 'integer',
            'masa_manfaat' => 'integer',
            'harga_perolehan' => 'decimal:2',
        ];
    }

    public function getUsiaTahunAttribute(): ?int
    {
        if ($this->tgl_perolehan) {
            return (int) $this->tgl_perolehan->diffInYears(now());
        }
        if ($this->tahun_perolehan) {
            return max(0, (int) date('Y') - (int) $this->tahun_perolehan);
        }
        return null;
    }

    public function getUsiaTeksAttribute(): string
    {
        $tgl = $this->tgl_perolehan ?? ($this->tahun_perolehan ? Carbon::create($this->tahun_perolehan, 1, 1) : null);
        if (!$tgl) {
            return '-';
        }
        $diff = now()->diff($tgl);
        $parts = [];
        if ($diff->y > 0) $parts[] = "{$diff->y} tahun";
        if ($diff->m > 0) $parts[] = "{$diff->m} bulan";
        if (empty($parts)) $parts[] = "{$diff->d} hari";
        return implode(' ', $parts);
    }

    public function getNilaiPenyusutanBulanAttribute(): float
    {
        $harga = (float) ($this->harga_perolehan ?? 0);
        $masaBulan = max(1, ($this->masa_manfaat ?: 7) * 12);
        return $harga > 0 ? round($harga / $masaBulan, 2) : 0;
    }

    public function getAkumulasiPenyusutanAttribute(): float
    {
        $harga = (float) ($this->harga_perolehan ?? 0);
        if ($harga <= 0) return 0;

        $tgl = $this->tgl_perolehan ?? ($this->tahun_perolehan ? Carbon::create($this->tahun_perolehan, 1, 1) : null);
        if (!$tgl) return 0;

        $bulanBerjalan = max(0, $tgl->diffInMonths(now()));
        $penyusutanPerBulan = $this->nilai_penyusutan_bulan;
        return min($harga, round($bulanBerjalan * $penyusutanPerBulan, 2));
    }

    public function getNilaiBukuAttribute(): float
    {
        $harga = (float) ($this->harga_perolehan ?? 0);
        return max(0, round($harga - $this->akumulasi_penyusutan, 2));
    }

    public function getStnkCountdownAttribute(): ?string
    {
        if (!$this->tanggal_pajak_stnk) return null;
        $now = now()->startOfDay();
        $target = $this->tanggal_pajak_stnk->copy()->startOfDay();

        if ($now->gt($target)) {
            $hariTelat = (int) $now->diffInDays($target);
            return "Lewat {$hariTelat} hari";
        }

        $diff = $now->diff($target);
        $parts = [];
        if ($diff->y > 0) $parts[] = "{$diff->y} tahun";
        if ($diff->m > 0) $parts[] = "{$diff->m} bulan";
        if ($diff->d > 0) $parts[] = "{$diff->d} hari";
        return implode(' ', $parts) . ' (masih berlaku)';
    }

    public function servis(): HasMany
    {
        return $this->hasMany(ServisKendaraan::class, 'kendaraan_id', 'kendaraan_id')->orderByDesc('tanggal_servis');
    }

    public function getJatuhTempoServisAttribute(): ?Carbon
    {
        if (!$this->interval_servis_bulan) {
            return null;
        }

        if ($this->tanggal_servis_terakhir) {
            return $this->tanggal_servis_terakhir->copy()->addMonths($this->interval_servis_bulan);
        }

        $basis = $this->tgl_perolehan ?? ($this->tahun_perolehan ? Carbon::create($this->tahun_perolehan, 1, 1) : $this->created_at);
        if (!$basis) {
            return null;
        }

        return Carbon::parse($basis)->copy()->addMonths($this->interval_servis_bulan);
    }

    public function getStatusServisAttribute(): array
    {
        $jatuhTempo = $this->jatuh_tempo_servis;

        if (!$jatuhTempo) {
            return [
                'status' => 'Belum Ada Jadwal',
                'badge' => 'Belum Diatur',
                'variant' => 'default',
                'keterangan' => 'Belum ada data servis terakhir',
                'prioritas' => 4,
            ];
        }

        $now = now()->startOfDay();
        $target = $jatuhTempo->copy()->startOfDay();

        if ($now->gt($target)) {
            $hariTelat = (int) $now->diffInDays($target);
            return [
                'status' => 'Jatuh Tempo',
                'badge' => 'Jatuh Tempo',
                'variant' => 'danger',
                'keterangan' => $hariTelat > 0 ? "Terlambat {$hariTelat} hari" : "Jatuh tempo hari ini",
                'prioritas' => 1,
            ];
        }

        $sisaHari = (int) $now->diffInDays($target);
        if ($sisaHari <= 14) {
            return [
                'status' => 'Mendekati Servis',
                'badge' => 'Mendekati Servis',
                'variant' => 'warning',
                'keterangan' => "{$sisaHari} hari lagi (" . $target->format('d/m/Y') . ")",
                'prioritas' => 2,
            ];
        }

        return [
            'status' => 'Aman',
            'badge' => 'Aman',
            'variant' => 'success',
            'keterangan' => "Jatuh tempo " . $target->format('d/m/Y'),
            'prioritas' => 3,
        ];
    }

    public function getStatusPajakAttribute(): ?array
    {
        if (!$this->tanggal_pajak_stnk) {
            return null;
        }

        $now = now()->startOfDay();
        $target = $this->tanggal_pajak_stnk->copy()->startOfDay();

        if ($now->gt($target)) {
            $hariTelat = (int) $now->diffInDays($target);
            return [
                'status' => 'Pajak Lewat',
                'badge' => 'Pajak Lewat',
                'variant' => 'danger',
                'keterangan' => "Terlambat {$hariTelat} hari",
            ];
        }

        $sisaHari = (int) $now->diffInDays($target);
        if ($sisaHari <= 30) {
            return [
                'status' => 'Pajak Mendekati',
                'badge' => 'Pajak Segera',
                'variant' => 'warning',
                'keterangan' => "{$sisaHari} hari lagi",
            ];
        }

        return [
            'status' => 'Pajak Aktif',
            'badge' => 'Aktif',
            'variant' => 'success',
            'keterangan' => $target->format('d/m/Y'),
        ];
    }
}
