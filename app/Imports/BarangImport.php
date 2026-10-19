<?php

namespace App\Imports;

use App\Models\Ruangan;
use Maatwebsite\Excel\Concerns\ToCollection;
use Maatwebsite\Excel\Concerns\WithHeadingRow;

class BarangImport implements ToCollection, WithHeadingRow
{
    public array $valid = [];
    public array $gagal = [];

    public function __construct(protected ?int $koordinatorId = null)
    {
    }

    public function collection($rows)
    {
        foreach ($rows as $index => $row) {
            $baris = $index + 2;
            $error = [];

            $kodeRuang = trim($row['kode_ruang'] ?? '');
            $namaBarang = trim($row['nama_barang'] ?? '');
            $hargaSatuan = $row['harga_satuan'] ?? null;

            // Cek kolom kondisi B / RR / RB
            $bVal = $row['baik'] ?? $row['b'] ?? null;
            $rrVal = $row['rusak_ringan'] ?? $row['rr'] ?? null;
            $rbVal = $row['rusak_berat'] ?? $row['rb'] ?? null;

            if ($bVal !== null || $rrVal !== null || $rbVal !== null) {
                $kondisiBaik = is_numeric($bVal) ? max(0, (int) $bVal) : 0;
                $kondisiRR = is_numeric($rrVal) ? max(0, (int) $rrVal) : 0;
                $kondisiRB = is_numeric($rbVal) ? max(0, (int) $rbVal) : 0;
                $jumlah = $kondisiBaik + $kondisiRR + $kondisiRB;
                if ($jumlah === 0 && is_numeric($row['jumlah'] ?? null)) {
                    $jumlah = (int) $row['jumlah'];
                    $kondisiBaik = $jumlah;
                }
            } else {
                // Format lama / kolom kondisi tunggal
                $jumlah = is_numeric($row['jumlah'] ?? null) ? (int) $row['jumlah'] : 0;
                $kondisiTunggal = trim($row['kondisi'] ?? 'Baik');
                if ($kondisiTunggal === 'Rusak Ringan') {
                    $kondisiBaik = 0;
                    $kondisiRR = $jumlah;
                    $kondisiRB = 0;
                } elseif ($kondisiTunggal === 'Rusak Berat') {
                    $kondisiBaik = 0;
                    $kondisiRR = 0;
                    $kondisiRB = $jumlah;
                } else {
                    $kondisiBaik = $jumlah;
                    $kondisiRR = 0;
                    $kondisiRB = 0;
                }
            }

            $ruangan = Ruangan::where('kode_ruang', $kodeRuang)->first();

            if (!$namaBarang) {
                $error[] = 'Nama barang wajib diisi';
            }
            if (!$ruangan) {
                $error[] = "Kode ruang '{$kodeRuang}' tidak ditemukan";
            } elseif ($this->koordinatorId !== null && !$ruangan->dikoordinasiOleh($this->koordinatorId)) {
                $error[] = 'Anda tidak berwenang menambahkan barang ke ruangan ini';
            }
            if ($jumlah < 1) {
                $error[] = 'Total jumlah barang minimal 1 unit';
            }
            if (!is_numeric($hargaSatuan) || $hargaSatuan < 0) {
                $error[] = 'Harga satuan harus angka';
            }

            $tahun = $row['tahun_perolehan'] ?? null;
            if ($tahun !== null && $tahun !== '' && (!is_numeric($tahun) || $tahun < 1990 || $tahun > date('Y'))) {
                $error[] = 'Tahun perolehan tidak valid';
            }

            $data = [
                'baris' => $baris,
                'kode_ruang' => $kodeRuang,
                'nama_barang' => $namaBarang,
                'kode_barang' => trim($row['kode_barang'] ?? ''),
                'nup' => trim($row['nup'] ?? ''),
                'merek_tipe' => trim($row['merek_tipe'] ?? ''),
                'tahun_perolehan' => $tahun ?: null,
                'jumlah' => $jumlah,
                'satuan' => trim($row['satuan'] ?? ''),
                'harga_satuan' => $hargaSatuan,
                'sumber_dana' => trim($row['sumber_dana'] ?? ''),
                'kondisi_baik' => $kondisiBaik,
                'kondisi_rusak_ringan' => $kondisiRR,
                'kondisi_rusak_berat' => $kondisiRB,
                'keterangan' => trim($row['keterangan'] ?? ''),
                'ruangan_id' => $ruangan?->ruangan_id,
            ];

            if (empty($error)) {
                $this->valid[] = $data;
            } else {
                $this->gagal[] = [...$data, 'alasan' => implode(', ', $error)];
            }
        }
    }
}