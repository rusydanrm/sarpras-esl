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
    <div class="sub">Departemen Ekonomi Sumberdaya dan Lingkungan &middot; Dicetak {{ $tanggal }}</div>

    <table>
        <thead>
            <tr>
                <th class="text-center" style="width: 24px;">No</th>
                <th class="text-left">Nama Barang</th>
                <th class="text-left">Ruangan</th>
                <th class="text-left">Gedung</th>
                <th class="text-right">Jumlah</th>
                <th class="text-left">Kondisi</th>
                <th class="text-right">Jumlah Harga Perolehan</th>
            </tr>
        </thead>
        <tbody>
            @foreach ($data as $b)
                <tr>
                    <td class="text-center">{{ $loop->iteration }}</td>
                    <td class="text-left">{{ $b->nama_barang }}</td>
                    <td class="text-left">{{ $b->ruangan?->nama_ruang ?? '-' }}</td>
                    <td class="text-left">{{ $b->ruangan?->gedung?->nama_gedung ?? '-' }}</td>
                    <td class="text-right">{{ $b->jumlah }} {{ $b->satuan }}</td>
                    <td class="text-left">{{ $b->teks_kondisi }}</td>
                    <td class="text-right">Rp {{ number_format($b->total_harga, 0, ',', '.') }}</td>
                </tr>
            @endforeach
        </tbody>
    </table>
</body>

</html>