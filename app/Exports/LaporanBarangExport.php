<?php

namespace App\Exports;

use App\Models\Barang;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;

class LaporanBarangExport implements FromCollection, WithHeadings, WithMapping
{
    public function __construct(protected array $filter)
    {
    }

    public function collection()
    {
        $query = Barang::with(['ruangan.gedung']);

        if (!empty($this->filter['q'])) {
            $query->where(function ($q) {
                $q->where('nama_barang', 'like', '%' . $this->filter['q'] . '%')
                  ->orWhere('kode_barang', 'like', '%' . $this->filter['q'] . '%')
                  ->orWhere('merek_tipe', 'like', '%' . $this->filter['q'] . '%');
            });
        }
        if (!empty($this->filter['gedung_id'])) {
            $query->whereHas('ruangan', fn($q) => $q->where('gedung_id', $this->filter['gedung_id']));
        }
        if (!empty($this->filter['kondisi'])) {
            if ($this->filter['kondisi'] === 'Baik') {
                $query->where('kondisi_baik', '>', 0);
            } elseif ($this->filter['kondisi'] === 'Rusak Ringan') {
                $query->where('kondisi_rusak_ringan', '>', 0);
            } elseif ($this->filter['kondisi'] === 'Rusak Berat') {
                $query->where('kondisi_rusak_berat', '>', 0);
            }
        }
        if (!empty($this->filter['tahun'])) {
            $query->where('tahun_perolehan', $this->filter['tahun']);
        }
        if (!empty($this->filter['sumber_dana'])) {
            $query->where('sumber_dana', 'like', '%' . $this->filter['sumber_dana'] . '%');
        }

        return $query->orderBy('nama_barang')->get();
    }

    public function headings(): array
    {
        return ['Nama Barang', 'Kode Barang', 'Ruangan', 'Gedung', 'Baik', 'Rusak Ringan', 'Rusak Berat', 'Total Jumlah', 'Satuan', 'Harga Satuan', 'Jumlah Harga Perolehan', 'Sumber Dana', 'Tahun Perolehan'];
    }

    public function map($barang): array
    {
        return [
            $barang->nama_barang,
            $barang->kode_barang,
            $barang->ruangan->nama_ruang,
            $barang->ruangan->gedung->nama_gedung,
            $barang->kondisi_baik,
            $barang->kondisi_rusak_ringan,
            $barang->kondisi_rusak_berat,
            $barang->jumlah,
            $barang->satuan,
            $barang->harga_satuan,
            $barang->total_harga,
            $barang->sumber_dana,
            $barang->tahun_perolehan,
        ];
    }
}