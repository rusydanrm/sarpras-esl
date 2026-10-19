<?php

namespace App\Http\Controllers;

use App\Imports\BarangImport;
use App\Models\Barang;
use App\Models\ImportLog;
use Illuminate\Http\Request;
use Maatwebsite\Excel\Facades\Excel as ExcelFacade;
use Illuminate\Support\Facades\Session;

class ImportExcelController extends Controller
{
    public function index()
    {
        $this->authorizeRole();

        $ruanganList = \App\Models\Ruangan::with('gedung')->orderBy('nama_ruang')->get();

        return view('impor.index', compact('ruanganList'));
    }

    public function preview(Request $request)
    {
        $this->authorizeRole();

        $request->validate([
            'file' => 'required|file|mimes:xlsx,xls|max:5120',
        ]);

        $koordinatorId = auth()->user()->role === 'dosen_tendik' ? auth()->id() : null;

        $import = new BarangImport($koordinatorId);
        ExcelFacade::import($import, $request->file('file'));

        Session::put('impor_valid', $import->valid);
        Session::put('impor_nama_file', $request->file('file')->getClientOriginalName());

        return view('impor.preview', [
            'valid' => $import->valid,
            'gagal' => $import->gagal,
        ]);
    }

    public function previewTempel(Request $request)
    {
        $this->authorizeRole();

        $validated = $request->validate([
            'ruangan_id' => 'required|exists:ruangan,ruangan_id',
            'data_tempel' => 'required|string',
        ]);

        $ruangan = \App\Models\Ruangan::findOrFail($validated['ruangan_id']);

        if (auth()->user()->role === 'dosen_tendik' && !$ruangan->dikoordinasiOleh(auth()->id())) {
            return back()->withErrors(['ruangan_id' => 'Anda tidak berwenang mengisi barang ke ruangan ini.']);
        }

        $rawInput = preg_replace('/[\x{00a0}\x{200b}\x{feff}]+/u', ' ', $validated['data_tempel']);
        $baris = array_filter(explode("\n", trim($rawInput)), fn($b) => trim($b) !== '');
        $valid = [];
        $gagal = [];

        $satuanKeywords = ['buah', 'unit', 'set', 'pcs', 'lembar', 'meter', 'm', 'm2', 'm3', 'dus', 'btl', 'botol', 'paket', 'roll', 'pasang', 'batang', 'kg', 'liter', 'box', 'eksemplar', 'buku', 'rim', 'kodi', 'gross'];

        foreach (array_values($baris) as $index => $barisTeks) {
            $kolom = array_values(array_map('trim', explode("\t", rtrim($barisTeks, "\r"))));

            // Abaikan jika baris header tabel
            $barisGabung = strtolower(implode(' ', $kolom));
            if (str_contains($barisGabung, 'kode barang') || str_contains($barisGabung, 'nama barang') || str_contains($barisGabung, 'identitas barang')) {
                continue;
            }
            if (isset($kolom[0], $kolom[1]) && $kolom[0] === '1' && $kolom[1] === '2' && ($kolom[2] ?? '') === '3') {
                continue;
            }

            // 1. Coba deteksi pintar berbasis kata kunci Satuan (buah, unit, pcs, dll.)
            $satuanIdx = null;
            foreach ($kolom as $i => $val) {
                if (in_array(strtolower($val), $satuanKeywords)) {
                    $satuanIdx = $i;
                    break;
                }
            }

            if ($satuanIdx !== null && $satuanIdx >= 3) {
                $satuan = $kolom[$satuanIdx];
                $jumlah = is_numeric($kolom[$satuanIdx - 1] ?? '') ? (int)$kolom[$satuanIdx - 1] : 1;
                $hargaSatuan = is_numeric($kolom[$satuanIdx - 2] ?? '') ? $kolom[$satuanIdx - 2] : '0';
                
                $tahunCandidate = $kolom[$satuanIdx - 3] ?? '';
                $tahun = (is_numeric($tahunCandidate) && (int)$tahunCandidate >= 1980 && (int)$tahunCandidate <= 2035) ? (int)$tahunCandidate : null;
                
                $merekTipe = $kolom[$satuanIdx - 4] ?? '';

                // Nama & Kode Barang
                if (isset($kolom[0]) && is_numeric($kolom[0]) && strlen($kolom[0]) <= 4 && isset($kolom[2])) {
                    $kodeBarang = $kolom[1];
                    $namaBarang = $kolom[2];
                    $nup = $kolom[3] ?? '';
                } else {
                    $kodeBarang = $kolom[0];
                    $namaBarang = $kolom[1] ?? '';
                    $nup = $kolom[2] ?? '';
                }

                $sumberDana = $kolom[$satuanIdx + 2] ?? ($kolom[$satuanIdx + 1] ?? '');

                // Cari posisi kolom B, RR, RB setelah Sumber Dana
                // Jika kolom[$satuanIdx + 3] kosong tetapi kolom[$satuanIdx + 4] berisi data (terdapat kolom kosong pemisah di Excel)
                $colA = $kolom[$satuanIdx + 3] ?? '';
                $colB = $kolom[$satuanIdx + 4] ?? '';
                $colC = $kolom[$satuanIdx + 5] ?? '';
                $colD = $kolom[$satuanIdx + 6] ?? '';

                if ($colA === '' && ($colB !== '' || $colC !== '' || $colD !== '')) {
                    $kolomB  = $colB;
                    $kolomRR = $colC;
                    $kolomRB = $colD;
                } else {
                    $kolomB  = $colA;
                    $kolomRR = $colB;
                    $kolomRB = $colC;
                }
                $keterangan = $kolom[$satuanIdx + 7] ?? ($kolom[$satuanIdx + 6] ?? '');
            } else {
                // Fallback deteksi posisi
                $offset = (isset($kolom[0]) && is_numeric($kolom[0]) && strlen($kolom[0]) <= 4 && (count($kolom) >= 15 || isset($kolom[2]))) ? 1 : 0;
                $kodeBarang = $kolom[$offset + 0] ?? '';
                $namaBarang = $kolom[$offset + 1] ?? '';
                $nup = $kolom[$offset + 2] ?? '';
                $merekTipe = $kolom[$offset + 3] ?? '';
                $tahun = is_numeric($kolom[$offset + 4] ?? '') ? (int)$kolom[$offset + 4] : null;
                $hargaSatuan = $kolom[$offset + 5] ?? '0';
                $jumlah = is_numeric($kolom[$offset + 6] ?? '') ? (int)$kolom[$offset + 6] : 1;
                $satuan = $kolom[$offset + 7] ?? '';
                $sumberDana = $kolom[$offset + 9] ?? '';

                $colA = $kolom[$offset + 10] ?? '';
                $colB = $kolom[$offset + 11] ?? '';
                $colC = $kolom[$offset + 12] ?? '';
                $colD = $kolom[$offset + 13] ?? '';

                if ($colA === '' && ($colB !== '' || $colC !== '' || $colD !== '')) {
                    $kolomB  = $colB;
                    $kolomRR = $colC;
                    $kolomRB = $colD;
                } else {
                    $kolomB  = $colA;
                    $kolomRR = $colB;
                    $kolomRB = $colC;
                }
                $keterangan = $kolom[$offset + 14] ?? ($kolom[$offset + 13] ?? '');
            }

            $nomorBaris = $index + 1;
            $error = [];

            $bVal = trim($kolomB);
            $rrVal = trim($kolomRR);
            $rbVal = trim($kolomRB);

            $bNum = is_numeric($bVal) ? (int) $bVal : (in_array(strtolower($bVal), ['v', 'x', '✓', 'b', 'baik', 'true', 'ya']) ? 1 : 0);
            $rrNum = is_numeric($rrVal) ? (int) $rrVal : (in_array(strtolower($rrVal), ['v', 'x', '✓', 'rr', 'rusak ringan', 'true', 'ya']) ? 1 : 0);
            $rbNum = is_numeric($rbVal) ? (int) $rbVal : (in_array(strtolower($rbVal), ['v', 'x', '✓', 'rb', 'rusak berat', 'true', 'ya']) ? 1 : 0);

            $totalKondisi = $bNum + $rrNum + $rbNum;
            $jmlAngka = is_numeric($jumlah) && (int)$jumlah > 0 ? (int)$jumlah : 1;

            if ($totalKondisi > 0) {
                $kondisiBaik = $bNum;
                $kondisiRR = $rrNum;
                $kondisiRB = $rbNum;
                if ($totalKondisi === 1 && $jmlAngka > 1) {
                    if ($bNum === 1) $kondisiBaik = $jmlAngka;
                    elseif ($rrNum === 1) $kondisiRR = $jmlAngka;
                    elseif ($rbNum === 1) $kondisiRB = $jmlAngka;
                    $jumlah = $jmlAngka;
                } else {
                    $jumlah = $totalKondisi;
                }
            } else {
                // Jika kolom B/RR/RB kosong di Excel, defaultkan seluruh unit ke Kondisi Baik
                $kondisiBaik = $jmlAngka;
                $kondisiRR = 0;
                $kondisiRB = 0;
                $jumlah = $jmlAngka;
            }

            if (!$namaBarang) {
                $error[] = 'Nama barang wajib diisi';
            }
            if ($jumlah < 1) {
                $error[] = 'Jumlah harus angka lebih dari 0';
            }
            if (!is_numeric($hargaSatuan) || $hargaSatuan < 0) {
                $error[] = 'Harga satuan harus angka';
            }

            $data = [
                'baris' => $nomorBaris,
                'kode_ruang' => $ruangan->kode_ruang,
                'ruangan_id' => $ruangan->ruangan_id,
                'kode_barang' => $kodeBarang,
                'nama_barang' => $namaBarang,
                'nup' => $nup,
                'merek_tipe' => $merekTipe,
                'tahun_perolehan' => is_numeric($tahun) ? (int) $tahun : null,
                'harga_satuan' => $hargaSatuan,
                'jumlah' => $jumlah,
                'satuan' => $satuan,
                'sumber_dana' => $sumberDana,
                'kondisi_baik' => $kondisiBaik,
                'kondisi_rusak_ringan' => $kondisiRR,
                'kondisi_rusak_berat' => $kondisiRB,
                'keterangan' => $keterangan,
            ];

            if (empty($error)) {
                $valid[] = $data;
            } else {
                $gagal[] = [...$data, 'alasan' => implode(', ', $error)];
            }
        }

        Session::put('impor_valid', $valid);
        Session::put('impor_nama_file', 'Tempel Manual - ' . $ruangan->nama_ruang);

        return view('impor.preview', compact('valid', 'gagal'));
    }

    public function simpan(Request $request)
    {
        $this->authorizeRole();

        $dataValid = Session::get('impor_valid', []);
        $namaFile = Session::get('impor_nama_file', 'unknown.xlsx');

        if (empty($dataValid)) {
            return redirect()->route('impor.index')->withErrors(['umum' => 'Tidak ada data untuk disimpan, silakan unggah ulang.']);
        }

        $sukses = 0;
        foreach ($dataValid as $row) {
            Barang::create([
                'ruangan_id' => $row['ruangan_id'],
                'kode_barang' => $row['kode_barang'] ?: null,
                'nama_barang' => $row['nama_barang'],
                'nup' => $row['nup'] ?: null,
                'merek_tipe' => $row['merek_tipe'] ?: null,
                'tahun_perolehan' => $row['tahun_perolehan'],
                'jumlah' => $row['jumlah'],
                'satuan' => $row['satuan'] ?: null,
                'harga_satuan' => $row['harga_satuan'],
                'sumber_dana' => $row['sumber_dana'] ?: null,
                'kondisi_baik' => $row['kondisi_baik'] ?? $row['jumlah'],
                'kondisi_rusak_ringan' => $row['kondisi_rusak_ringan'] ?? 0,
                'kondisi_rusak_berat' => $row['kondisi_rusak_berat'] ?? 0,
                'keterangan' => $row['keterangan'] ?: null,
            ]);
            $sukses++;
        }

        ImportLog::create([
            'user_id' => auth()->id(),
            'nama_file' => $namaFile,
            'status' => $sukses > 0 ? 'sukses' : 'gagal',
            'jumlah_baris_sukses' => $sukses,
            'jumlah_baris_gagal' => 0,
            'tanggal_upload' => now(),
        ]);

        Session::forget(['impor_valid', 'impor_nama_file']);

        return view('impor.selesai', ['jumlah' => $sukses]);
    }

    public function downloadTemplate()
    {
        return ExcelFacade::download(new \App\Exports\TemplateBarangExport(), 'template_import_barang.xlsx');
    }

    private function authorizeRole(): void
    {
        if (!in_array(auth()->user()->role, ['admin', 'dosen_tendik'])) {
            abort(403, 'Hanya Admin atau Dosen/Tendik yang dapat melakukan impor data.');
        }
    }
}