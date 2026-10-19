<?php

namespace App\Exports;

use App\Models\Barang;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;

class RekomendasiBarangExport implements FromCollection, WithHeadings, WithMapping
{
    public function __construct(protected array $filter = [])
    {
    }

    public function collection()
    {
        $query = Barang::with(['ruangan.gedung', 'jenis', 'pemeliharaan']);

        if (!empty($this->filter['gedung_id'])) {
            $query->whereHas('ruangan', fn($q) => $q->where('gedung_id', $this->filter['gedung_id']));
        }

        if (!empty($this->filter['jenis_id'])) {
            $query->where('jenis_id', $this->filter['jenis_id']);
        }

        if (!empty($this->filter['q'])) {
            $keyword = $this->filter['q'];
            $query->where(function ($q) use ($keyword) {
                $q->where('nama_barang', 'like', "%{$keyword}%")
                  ->orWhere('kode_barang', 'like', "%{$keyword}%")
                  ->orWhere('nup', 'like', "%{$keyword}%")
                  ->orWhere('merek_tipe', 'like', "%{$keyword}%");
            });
        }

        $items = $query->orderBy('nama_barang')->get();

        // Filter kondisi jika dipilih
        if (!empty($this->filter['kondisi'])) {
            $k = $this->filter['kondisi'];
            $items = $items->filter(function ($b) use ($k) {
                if ($k === 'Baik') return $b->kondisi_baik > 0;
                if ($k === 'Rusak Ringan') return $b->kondisi_rusak_ringan > 0;
                if ($k === 'Rusak Berat') return $b->kondisi_rusak_berat > 0;
                if ($k === 'Perlu Perbaikan') return ($b->kondisi_rusak_ringan + $b->kondisi_rusak_berat) > 0;
                return true;
            });
        }

        // Filter usia minimal jika dipilih
        if (!empty($this->filter['usia_min'])) {
            $usiaMin = (int) $this->filter['usia_min'];
            $items = $items->filter(function ($barang) use ($usiaMin) {
                return ($barang->usia_tahun ?? 0) >= $usiaMin;
            });
        }

        return $items->values();
    }

    public function headings(): array
    {
        return [
            'Nama Barang',
            'Kode Barang',
            'NUP',
            'Ruangan',
            'Gedung',
            'Kategori / Jenis',
            'Tahun Perolehan',
            'Usia (Tahun)',
            'Masa Pakai Standar',
            'Baik',
            'Rusak Ringan',
            'Rusak Berat',
            'Total Jumlah',
            'Satuan',
            'Harga Satuan (Rp)',
            'Total Nilai Perolehan (Rp)',
            'Frekuensi Servis',
            'Total Biaya Servis (Rp)',
            'Tanggal Servis Terakhir',
        ];
    }

    public function map($barang): array
    {
        $jumlahServis = $barang->pemeliharaan->count();
        $totalBiaya = $barang->pemeliharaan->sum('biaya');
        $servisTerakhir = $barang->pemeliharaan->first();

        return [
            $barang->nama_barang,
            $barang->kode_barang ?? '-',
            $barang->nup ?? '-',
            $barang->ruangan?->nama_ruang ?? '-',
            $barang->ruangan?->gedung?->nama_gedung ?? '-',
            $barang->jenis?->nama_jenis ?? '-',
            $barang->tahun_perolehan ?? '-',
            $barang->usia_tahun !== null ? $barang->usia_tahun . ' thn' : '-',
            $barang->masa_pakai . ' thn',
            $barang->kondisi_baik,
            $barang->kondisi_rusak_ringan,
            $barang->kondisi_rusak_berat,
            $barang->jumlah,
            $barang->satuan ?? 'unit',
            $barang->harga_satuan,
            $barang->total_harga,
            $jumlahServis . 'x',
            $totalBiaya,
            $servisTerakhir?->tanggal_tindakan ? $servisTerakhir->tanggal_tindakan->format('d/m/Y') : '-',
        ];
    }
}

