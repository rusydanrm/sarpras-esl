<!DOCTYPE html>
<html>

<head>
    <meta charset="utf-8">
    <title>Laporan Monitoring & Rekomendasi (MR) Aset</title>
    <style>
        @page {
            margin: 1.5cm 1.5cm 1.5cm 1.5cm;
        }

        body {
            font-family: 'Helvetica Neue', Helvetica, Arial, sans-serif;
            font-size: 10px;
            color: #181B21;
            line-height: 1.3;
        }

        .header {
            border-bottom: 2px solid #120F8B;
            padding-bottom: 8px;
            margin-bottom: 12px;
        }

        .instansi {
            font-size: 11px;
            font-weight: bold;
            color: #495465;
            text-transform: uppercase;
            letter-spacing: 0.5px;
        }

        h1 {
            color: #120F8B;
            font-size: 16px;
            margin: 2px 0 2px 0;
            font-weight: bold;
        }

        .sub {
            color: #495465;
            font-size: 9px;
        }

        .summary-box {
            width: 100%;
            margin-bottom: 14px;
            border-collapse: collapse;
        }

        .summary-card {
            background: #F7F8F9;
            border: 1px solid #DEE2E7;
            padding: 8px 10px;
            border-radius: 4px;
        }

        .summary-label {
            font-size: 8.5px;
            font-weight: bold;
            color: #495465;
            text-transform: uppercase;
        }

        .summary-val {
            font-size: 13px;
            font-weight: bold;
            color: #120F8B;
            margin-top: 2px;
        }

        table.data-table {
            width: 100%;
            border-collapse: collapse;
            font-size: 9px;
        }

        table.data-table th {
            background: #120F8B;
            color: #ffffff;
            padding: 6px 6px;
            text-align: left;
            font-size: 8.5px;
            text-transform: uppercase;
            letter-spacing: 0.3px;
            border: 1px solid #120F8B;
        }

        table.data-table td {
            padding: 5px 6px;
            border: 1px solid #DEE2E7;
            vertical-align: top;
        }

        table.data-table tr:nth-child(even) {
            background: #FBFBFC;
        }

        .badge-danger {
            background: #FEF7F7;
            color: #EF4444;
            padding: 2px 4px;
            border-radius: 3px;
            font-weight: bold;
            font-size: 8px;
            display: inline-block;
        }

        .badge-warning {
            background: #FFFBF4;
            color: #D97706;
            padding: 2px 4px;
            border-radius: 3px;
            font-weight: bold;
            font-size: 8px;
            display: inline-block;
        }

        .badge-primary {
            background: #EEF2FF;
            color: #120F8B;
            padding: 2px 4px;
            border-radius: 3px;
            font-weight: bold;
            font-size: 8px;
            display: inline-block;
        }

        .badge-success {
            background: #F0FEF9;
            color: #059669;
            padding: 2px 4px;
            border-radius: 3px;
            font-weight: bold;
            font-size: 8px;
            display: inline-block;
        }

        .text-center { text-align: center; }
        .text-right { text-align: right; }
        .text-left { text-align: left; }

        .footer {
            margin-top: 20px;
            width: 100%;
        }

        .ttd-box {
            float: right;
            width: 200px;
            text-align: center;
        }
    </style>
</head>

<body>
    <div class="header">
        <div class="instansi">Departemen Ekonomi Sumberdaya dan Lingkungan &bull; IPB University</div>
        <h1>Laporan Monitoring & Riwayat Pemeliharaan Aset (MR)</h1>
        <div class="sub">
            Dokumen Inventaris &bull; Dicetak pada: {{ $tanggal }}
            @if (!empty($filter['kondisi'])) &bull; Filter Kondisi: {{ $filter['kondisi'] }} @endif
            @if (!empty($filter['gedung_id'])) &bull; Filter Gedung: Aktif @endif
            @if (!empty($filter['jenis_id'])) &bull; Filter Kategori: Aktif @endif
        </div>
    </div>

    {{-- Ringkasan Monitoring & Pemeliharaan --}}
    <table class="summary-box">
        <tr>
            <td style="width: 33%; padding-right: 6px;">
                <div class="summary-card" style="border-left: 3px solid #120F8B;">
                    <div class="summary-label">Total Aset & Unit</div>
                    <div class="summary-val">{{ $data->count() }} Aset ({{ $data->sum('jumlah') }} Unit)</div>
                </div>
            </td>
            <td style="width: 33%; padding-right: 6px;">
                <div class="summary-card" style="border-left: 3px solid #F59E0B;">
                    <div class="summary-label">Perlu Perbaikan / Servis</div>
                    <div class="summary-val" style="color: #D97706;">{{ $totalPerluPerbaikan }} Unit ({{ $totalRusakRingan }} RR / {{ $totalRusakBerat }} RB)</div>
                </div>
            </td>
            <td style="width: 34%;">
                <div class="summary-card" style="border-left: 3px solid #059669;">
                    <div class="summary-label">Total Biaya Servis Tercatat</div>
                    <div class="summary-val" style="color: #059669;">Rp {{ number_format($totalBiayaServis, 0, ',', '.') }}</div>
                </div>
            </td>
        </tr>
    </table>

    <table class="data-table">
        <thead>
            <tr>
                <th class="text-center" style="width: 20px;">No</th>
                <th style="width: 170px;">Nama Barang & Kode</th>
                <th style="width: 120px;">Lokasi Ruangan</th>
                <th class="text-center" style="width: 65px;">Thn / Usia</th>
                <th class="text-center" style="width: 75px;">Kondisi (B/RR/RB)</th>
                <th class="text-center" style="width: 80px;">Riwayat Servis</th>
                <th class="text-right" style="width: 100px;">Nilai Perolehan (Rp)</th>
                <th class="text-right" style="width: 100px;">Biaya Servis (Rp)</th>
            </tr>
        </thead>
        <tbody>
            @forelse ($data as $b)
                @php
                    $usia = $b->usia_tahun;
                    $masaPakai = $b->masa_pakai;
                    $jumlahServis = $b->pemeliharaan->count();
                    $totalBiaya = $b->pemeliharaan->sum('biaya');
                    $servisTerakhir = $b->pemeliharaan->first();
                @endphp
                <tr>
                    <td class="text-center">{{ $loop->iteration }}</td>
                    <td>
                        <strong>{{ $b->nama_barang }}</strong>
                        @if ($b->kode_barang)
                            <br><span style="color: #495465;">Kode: {{ $b->kode_barang }}</span>
                        @endif
                        @if ($b->nup)
                            <br><span style="color: #9EA9B9;">NUP: {{ $b->nup }}</span>
                        @endif
                        @if ($b->jenis)
                            <br><span style="color: #120F8B;">Kategori: {{ $b->jenis->nama_jenis }}</span>
                        @endif
                    </td>
                    <td>
                        {{ $b->ruangan?->nama_ruang ?? '-' }}
                        <br><span style="color: #495465;">{{ $b->ruangan?->gedung?->nama_gedung ?? '-' }}</span>
                    </td>
                    <td class="text-center">
                        {{ $b->tahun_perolehan ?? '-' }}
                        <br><strong>{{ $usia !== null ? $usia . ' Thn' : '-' }}</strong>
                        <br><span style="color: #495465; font-size: 7.5px;">(Std {{ $masaPakai }} Thn)</span>
                    </td>
                    <td class="text-center">
                        <span style="color: #059669; font-weight: bold;">{{ $b->kondisi_baik }} B</span> / 
                        <span style="color: #D97706; font-weight: bold;">{{ $b->kondisi_rusak_ringan }} RR</span> / 
                        <span style="color: #EF4444; font-weight: bold;">{{ $b->kondisi_rusak_berat }} RB</span>
                    </td>
                    <td class="text-center">
                        @if ($jumlahServis > 0)
                            <strong>{{ $jumlahServis }}x Servis</strong>
                            @if ($servisTerakhir?->tanggal_tindakan)
                                <br><span style="color: #495465; font-size: 7.5px;">Tgl: {{ $servisTerakhir->tanggal_tindakan->format('d/m/Y') }}</span>
                            @endif
                        @else
                            <span style="color: #9EA9B9;">-</span>
                        @endif
                    </td>
                    <td class="text-right">
                        Rp {{ number_format($b->total_harga, 0, ',', '.') }}
                    </td>
                    <td class="text-right">
                        @if ($totalBiaya > 0)
                            <strong>Rp {{ number_format($totalBiaya, 0, ',', '.') }}</strong>
                        @else
                            <span style="color: #9EA9B9;">-</span>
                        @endif
                    </td>
                </tr>
            @empty
                <tr>
                    <td colspan="8" class="text-center" style="padding: 15px; color: #9EA9B9;">
                        Tidak ada data barang yang memenuhi kriteria filter.
                    </td>
                </tr>
            @endforelse
        </tbody>
    </table>

    <div class="footer">
        <table style="width: 100%;">
            <tr>
                <td style="border: none; width: 60%;">
                    <div style="font-size: 8px; color: #495465;">
                        * Laporan Monitoring & Pemeliharaan digenerate otomatis oleh Sistem Informasi Sarana & Prasarana ESL IPB.<br>
                        * Dicatat untuk keperluan monitoring fisik dan rekonsiliasi riwayat perbaikan berkala.
                    </div>
                </td>
                <td style="border: none; width: 40%;">
                    <div class="ttd-box">
                        <div style="font-size: 9px; margin-bottom: 45px;">
                            Bogor, {{ now()->translatedFormat('d F Y') }}<br>
                            Mengetahui,<br>
                            <strong>Pimpinan / Pengelola Sarpras ESL</strong>
                        </div>
                        <div style="border-bottom: 1px solid #181B21; width: 140px; margin: 0 auto;"></div>
                        <div style="font-size: 8.5px; margin-top: 3px;">NIP. ........................................</div>
                    </div>
                </td>
            </tr>
        </table>
    </div>
</body>

</html>
