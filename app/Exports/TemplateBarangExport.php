<?php

namespace App\Exports;

use Maatwebsite\Excel\Concerns\FromArray;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithStyles;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;

class TemplateBarangExport implements FromArray, WithHeadings, WithStyles
{
    public function array(): array
    {
        // Baris contoh, membantu user paham format yang benar
        return [
            ['3LV4', 'ESL-AC-001', 'AC Split 1 PK', '', 'Daikin', 2022, 4500000, 1, 0, 0, 'unit', 'APBN', ''],
        ];
    }

    public function headings(): array
    {
        return [
            'Kode Ruang',
            'Kode Barang',
            'Nama Barang',
            'NUP',
            'Merek/Tipe',
            'Tahun Perolehan',
            'Harga Satuan',
            'Baik',
            'Rusak Ringan',
            'Rusak Berat',
            'Satuan',
            'Sumber Dana',
            'Keterangan',
        ];
    }

    public function styles(Worksheet $sheet)
    {
        return [1 => ['font' => ['bold' => true]]];
    }
}