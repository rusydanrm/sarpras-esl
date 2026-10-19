<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Barang extends Model
{
    protected $table = 'barang';
    protected $primaryKey = 'barang_id';

    protected $fillable = [
        'ruangan_id', 'jenis_id', 'kode_barang', 'nama_barang', 'nup',
        'merek_tipe', 'tahun_perolehan', 'harga_satuan', 'jumlah', 'satuan',
        'sumber_dana', 'kondisi_baik', 'kondisi_rusak_ringan', 'kondisi_rusak_berat', 'foto_url', 'qr_code',
        'interval_pemeliharaan_bulan', 'tanggal_pemeliharaan_terakhir', 'keterangan',
    ];

    protected $appends = [
        'status_rekomendasi',
        'status_servis',
        'jatuh_tempo_berikutnya',
        'usia_tahun',
        'usia_teks',
        'masa_pakai',
        'masa_manfaat',
        'estimasi_biaya_ganti',
        'ringkasan_kondisi',
        'teks_kondisi',
        'nilai_penyusutan_bulan',
        'akumulasi_penyusutan',
        'nilai_buku',
    ];

    protected function casts(): array
    {
        return [
            'tanggal_pemeliharaan_terakhir' => 'date',
            'kondisi_baik' => 'integer',
            'kondisi_rusak_ringan' => 'integer',
            'kondisi_rusak_berat' => 'integer',
            'jumlah' => 'integer',
            'interval_pemeliharaan_bulan' => 'integer',
        ];
    }

    protected static function booted(): void
    {
        static::saving(function (Barang $barang) {
            if (empty($barang->jenis_id) && !empty($barang->nama_barang)) {
                $barang->jenis_id = static::deteksiJenisId($barang->nama_barang);
            }
        });

        static::creating(function (Barang $barang) {
            if (empty($barang->interval_pemeliharaan_bulan) && !empty($barang->nama_barang)) {
                $barang->interval_pemeliharaan_bulan = static::deteksiIntervalRekomendasi($barang->nama_barang, $barang->jenis_id);
            }
        });
    }

    public static function deteksiIntervalRekomendasi(?string $namaBarang, ?int $jenisId = null): ?int
    {
        if (empty($namaBarang)) {
            return null;
        }

        // Interval rutin rekomendasi (dalam bulan)
        if (preg_match('/\b(ac|air\s*conditioner|split)\b/i', $namaBarang)) {
            return 3; // AC cuci/servis berkala tiap 3 bulan
        }
        if (preg_match('/\b(proyektor|projector|infocus|lcd)\b/i', $namaBarang)) {
            return 6; // Proyektor pembersihan optik/filter tiap 6 bulan
        }
        if (preg_match('/\b(komputer|pc|all\s*in\s*one|aio|cpu|server)\b/i', $namaBarang)) {
            return 6; // PC/Server perawatan hardware/debu tiap 6 bulan
        }
        if (preg_match('/\b(printer|scanner|scaner|fotocopy)\b/i', $namaBarang)) {
            return 6; // Printer/Scanner perawatan roller/tinta tiap 6 bulan
        }
        if (preg_match('/\b(dispenser)\b/i', $namaBarang)) {
            return 3; // Dispenser sanitasi tiap 3 bulan
        }

        return null;
    }

    public static function deteksiJenisId(?string $namaBarang): ?int
    {
        if (empty($namaBarang)) {
            return null;
        }

        $kategoriRegex = [
            'AC' => '/\b(ac|air\s*conditioner|split)\b/i',
            'Laptop' => '/\b(laptop|notebook|macbook|thinkpad)\b/i',
            'Printer' => '/\b(printer|cetak)\b/i',
            'PC Komputer' => '/\b(komputer|pc|all\s*in\s*one|aio|cpu|monitor)\b/i',
            'Scanner' => '/\b(scanner|scaner)\b/i',
            'Handphone' => '/\b(handphone|hp|smartphone)\b/i',
            'Harddisk Eksternal' => '/\b(hardisk|harddisk|hdd|eksternal|xteral)\b/i',
            'LCD Proyektor' => '/\b(proyektor|projector|infocus|lcd)\b/i',
            'Bangku/Kursi' => '/\b(kursi|bangku|chair|stool)\b/i',
            'Dispenser' => '/\b(dispenser)\b/i',
            'Lemari' => '/\b(lemari|filing|cabinet|filin|laci|rak)\b/i',
            'Meja' => '/\b(meja|desk|table)\b/i',
            'Kamera' => '/\b(kamera|camera)\b/i',
        ];

        foreach ($kategoriRegex as $namaJenis => $pattern) {
            if (preg_match($pattern, $namaBarang)) {
                $jenis = JenisBarang::where('nama_jenis', $namaJenis)->first();
                if ($jenis) {
                    return $jenis->jenis_id;
                }
            }
        }

        $campuran = JenisBarang::where('nama_jenis', 'Campuran')->first();
        return $campuran?->jenis_id;
    }

    public function ruangan(): BelongsTo
    {
        return $this->belongsTo(Ruangan::class, 'ruangan_id', 'ruangan_id');
    }

    public function jenis(): BelongsTo
    {
        return $this->belongsTo(JenisBarang::class, 'jenis_id', 'jenis_id');
    }

    public function pemeliharaan(): HasMany
    {
        return $this->hasMany(Pemeliharaan::class, 'barang_id', 'barang_id')->orderByDesc('tanggal_tindakan');
    }

    public function pengajuan(): HasMany
    {
        return $this->hasMany(Pengajuan::class, 'barang_id', 'barang_id');
    }

    public function notifikasi(): HasMany
    {
        return $this->hasMany(Notifikasi::class, 'barang_id', 'barang_id');
    }

    // Dipakai scheduled task untuk cek jatuh tempo pemeliharaan rutin
    public function getJatuhTempoBerikutnyaAttribute(): ?\Carbon\Carbon
    {
        if (! $this->interval_pemeliharaan_bulan) {
            return null;
        }

        if ($this->tanggal_pemeliharaan_terakhir) {
            return $this->tanggal_pemeliharaan_terakhir->copy()->addMonths($this->interval_pemeliharaan_bulan);
        }

        // Jika belum pernah diservis, hitung dari tahun perolehan atau created_at
        $basis = $this->tahun_perolehan
            ? \Carbon\Carbon::create($this->tahun_perolehan, 1, 1)
            : ($this->created_at ? $this->created_at->copy() : now());

        return $basis->copy()->addMonths($this->interval_pemeliharaan_bulan);
    }

    public function getStatusServisAttribute(): array
    {
        $jatuhTempo = $this->jatuh_tempo_berikutnya;

        if (!$this->interval_pemeliharaan_bulan || !$jatuhTempo) {
            return [
                'status' => 'Tidak Ada Jadwal',
                'badge' => 'Tidak Perlu Rutin',
                'variant' => 'default',
                'keterangan' => 'Pemeliharaan dilakukan insidental (saat ada kerusakan)',
                'prioritas' => 4,
            ];
        }

        $now = now()->startOfDay();
        $target = $jatuhTempo->copy()->startOfDay();

        if ($now->gt($target)) {
            $hariTelat = (int) $now->diffInDays($target);
            return [
                'status' => 'Jatuh Tempo',
                'badge' => 'Jatuh Tempo Servis',
                'variant' => 'danger',
                'keterangan' => $hariTelat > 0 ? "Terlambat {$hariTelat} hari (" . $target->format('d/m/Y') . ")" : "Jatuh tempo hari ini",
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
            'badge' => 'Jadwal Teratur',
            'variant' => 'success',
            'keterangan' => "Jatuh tempo " . $target->format('d/m/Y'),
            'prioritas' => 3,
        ];
    }

    public function getRingkasanKondisiAttribute(): array
    {
        $ringkasan = [];
        if ($this->kondisi_baik > 0) {
            $ringkasan[] = ['label' => 'Baik', 'singkat' => 'Baik', 'jumlah' => $this->kondisi_baik, 'variant' => 'success'];
        }
        if ($this->kondisi_rusak_ringan > 0) {
            $ringkasan[] = ['label' => 'Rusak Ringan', 'singkat' => 'RR', 'jumlah' => $this->kondisi_rusak_ringan, 'variant' => 'warning'];
        }
        if ($this->kondisi_rusak_berat > 0) {
            $ringkasan[] = ['label' => 'Rusak Berat', 'singkat' => 'RB', 'jumlah' => $this->kondisi_rusak_berat, 'variant' => 'danger'];
        }
        return $ringkasan;
    }

    public function getTeksKondisiAttribute(): string
    {
        $parts = [];
        if ($this->kondisi_baik > 0) {
            $parts[] = "{$this->kondisi_baik} Baik";
        }
        if ($this->kondisi_rusak_ringan > 0) {
            $parts[] = "{$this->kondisi_rusak_ringan} Rusak Ringan";
        }
        if ($this->kondisi_rusak_berat > 0) {
            $parts[] = "{$this->kondisi_rusak_berat} Rusak Berat";
        }
        return implode(', ', $parts) ?: '0 unit';
    }

    public function getUsiaTahunAttribute(): ?int
    {
        if (empty($this->tahun_perolehan)) {
            return null;
        }
        return max(0, (int) date('Y') - (int) $this->tahun_perolehan);
    }

    public function getMasaPakaiAttribute(): int
    {
        // Prioritas 1: Ambil dari konfigurasi dinamis JenisBarang di database
        if (!empty($this->jenis?->masa_pakai_tahun)) {
            return (int) $this->jenis->masa_pakai_tahun;
        }

        // Prioritas 2: Fallback bawaan
        $namaJenis = $this->jenis?->nama_jenis ?? '';

        return match ($namaJenis) {
            'Laptop', 'PC Komputer', 'Printer', 'Scanner', 'Handphone', 'Harddisk Eksternal' => 4,
            'AC', 'LCD Proyektor', 'Kamera', 'Dispenser' => 5,
            'Bangku/Kursi', 'Meja', 'Lemari' => 8,
            default => 5,
        };
    }

    public function getMasaManfaatAttribute(): int
    {
        return $this->masa_pakai;
    }

    public function getStatusRekomendasiAttribute(): array
    {
        $usia = $this->usia_tahun;
        $masaPakai = $this->masa_pakai;
        $adaRusakBerat = $this->kondisi_rusak_berat > 0;
        $adaRusakRingan = $this->kondisi_rusak_ringan > 0;
        $lewatMasaPakai = $usia !== null && $usia >= $masaPakai;

        if ($adaRusakBerat) {
            return [
                'status' => 'Segera Diganti',
                'prioritas' => 1,
                'variant' => 'danger',
                'badge' => 'Segera Diganti',
                'alasan' => $lewatMasaPakai
                    ? "Unit rusak berat & usia ({$usia} thn) melampaui masa pakai standar ({$masaPakai} thn)"
                    : "Terdapat {$this->kondisi_rusak_berat} unit rusak berat",
                'unit_rekomendasi' => $this->kondisi_rusak_berat,
            ];
        }

        if ($lewatMasaPakai) {
            return [
                'status' => 'Rekomendasi Peremajaan',
                'prioritas' => 2,
                'variant' => 'warning',
                'badge' => 'Peremajaan',
                'alasan' => "Usia ({$usia} thn) telah melampaui masa pakai standar ({$masaPakai} thn)",
                'unit_rekomendasi' => $this->jumlah,
            ];
        }

        if ($adaRusakRingan) {
            return [
                'status' => 'Perlu Pemeliharaan',
                'prioritas' => 3,
                'variant' => 'blue',
                'badge' => 'Perbaikan/Servis',
                'alasan' => "Terdapat {$this->kondisi_rusak_ringan} unit rusak ringan yang perlu perbaikan",
                'unit_rekomendasi' => $this->kondisi_rusak_ringan,
            ];
        }

        return [
            'status' => 'Kondisi Prima',
            'prioritas' => 4,
            'variant' => 'success',
            'badge' => 'Prima',
            'alasan' => 'Kondisi baik dan usia pemakaian masih optimal',
            'unit_rekomendasi' => 0,
        ];
    }

    public function getEstimasiBiayaGantiAttribute(): float
    {
        $rekomendasi = $this->status_rekomendasi;
        $hargaSatuan = (float) $this->harga_satuan;

        if ($rekomendasi['prioritas'] === 1) {
            // Prioritas 1: hitung unit rusak berat
            return (float) ($this->kondisi_rusak_berat * $hargaSatuan);
        }

        if ($rekomendasi['prioritas'] === 2) {
            // Prioritas 2: seluruh unit yang sudah lewat masa pakai
            return (float) ($this->jumlah * $hargaSatuan);
        }

        return 0;
    }

    public function getNilaiPenyusutanBulanAttribute(): float
    {
        $harga = (float) ($this->total_harga ?? ($this->harga_satuan * $this->jumlah));
        $masaManfaat = (int) ($this->masa_manfaat ?? 8);
        if ($harga <= 0 || $masaManfaat <= 0) {
            return 0.0;
        }
        return round($harga / ($masaManfaat * 12), 2);
    }

    public function getAkumulasiPenyusutanAttribute(): float
    {
        $harga = (float) ($this->total_harga ?? ($this->harga_satuan * $this->jumlah));
        $usiaTahun = $this->usia_tahun ?? 0;
        $usiaBulan = $usiaTahun * 12;
        $penyusutanBulan = $this->nilai_penyusutan_bulan;

        return min($harga, round($usiaBulan * $penyusutanBulan, 2));
    }

    public function getNilaiBukuAttribute(): float
    {
        $harga = (float) ($this->total_harga ?? ($this->harga_satuan * $this->jumlah));
        $akumulasi = $this->akumulasi_penyusutan;
        return max(0.0, round($harga - $akumulasi, 2));
    }

    public function getUsiaTeksAttribute(): string
    {
        $usia = $this->usia_tahun;
        if ($usia === null) {
            return '';
        }
        return $usia . ' tahun';
    }
}