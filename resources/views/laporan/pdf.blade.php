<!DOCTYPE html>
<html>

<head>
    <meta charset="utf-8">
    <style>
        body {
            font-family: sans-serif;
            font-size: 11px;
            color: #181B21;
        }

        h1 {
            color: #120F8B;
            font-size: 16px;
            margin-bottom: 2px;
        }

        .sub {
            color: #495465;
            font-size: 10px;
            margin-bottom: 16px;
        }

        table {
            width: 100%;
            border-collapse: collapse;
        }

        th {
            background: #F7F8F9;
            padding: 6px 8px;
            border-bottom: 1px solid #DEE2E7;
            font-size: 9px;
            text-transform: uppercase;
        }

        td {
            padding: 6px 8px;
            border-bottom: 1px solid #DEE2E7;
        }

        .text-left { text-align: left; }
        .text-center { text-align: center; }
        .text-right { text-align: right; }
    </style>
</head>

<body>
    <h1>Laporan Sarana dan Prasarana</h1>
    <div class="sub">Departemen Ekonomi Sumberdaya dan Lingkungan &middot; Dicetak {{ $tanggal }} &middot; Metode Penyusutan: Garis Lurus</div>

    <table>
        <thead>
            <tr>
                <th class="text-center" style="width: 24px;">No</th>
                <th class="text-left">Nama Barang</th>
                <th class="text-left">Ruangan &amp; Gedung</th>
                <th class="text-right">Jumlah</th>
                <th class="text-left">Kondisi</th>
                <th class="text-center">Thn / Manfaat</th>
                <th class="text-right">Harga Perolehan</th>
                <th class="text-right">Akum. Penyusutan</th>
                <th class="text-right">Nilai Buku</th>
            </tr>
        </thead>
        <tbody>
            @foreach ($data as $b)
                <tr>
                    <td class="text-center">{{ $loop->iteration }}</td>
                    <td class="text-left">
                        <strong>{{ $b->nama_barang }}</strong>
                        @if ($b->jenis)
                            <div style="font-size: 9px; color: #495465;">{{ $b->jenis->nama_jenis }}</div>
                        @endif
                    </td>
                    <td class="text-left">
                        {{ $b->ruangan?->nama_ruang ?? '-' }}
                        <div style="font-size: 9px; color: #495465;">{{ $b->ruangan?->gedung?->nama_gedung ?? '-' }}</div>
                    </td>
                    <td class="text-right">{{ $b->jumlah }} {{ $b->satuan }}</td>
                    <td class="text-left">{{ $b->teks_kondisi }}</td>
                    <td class="text-center">
                        {{ $b->tahun_perolehan ?: '-' }}
                        <div style="font-size: 9px; color: #495465;">({{ $b->masa_pakai }} thn)</div>
                    </td>
                    <td class="text-right">Rp {{ number_format($b->total_harga, 0, ',', '.') }}</td>
                    <td class="text-right">Rp {{ number_format($b->akumulasi_penyusutan, 0, ',', '.') }}</td>
                    <td class="text-right"><strong>Rp {{ number_format($b->nilai_buku, 0, ',', '.') }}</strong></td>
                </tr>
            @endforeach
        </tbody>
        <tfoot>
            <tr style="background: #F7F8F9; font-weight: bold;">
                <td colspan="6" class="text-right" style="padding: 8px; border-top: 2px solid #120F8B;">TOTAL KESELURUHAN</td>
                <td class="text-right" style="padding: 8px; border-top: 2px solid #120F8B;">Rp {{ number_format($data->sum('total_harga'), 0, ',', '.') }}</td>
                <td class="text-right" style="padding: 8px; border-top: 2px solid #120F8B;">Rp {{ number_format($data->sum('akumulasi_penyusutan'), 0, ',', '.') }}</td>
                <td class="text-right" style="padding: 8px; border-top: 2px solid #120F8B; color: #120F8B;">Rp {{ number_format($data->sum('nilai_buku'), 0, ',', '.') }}</td>
            </tr>
        </tfoot>
    </table>
</body>

</html>