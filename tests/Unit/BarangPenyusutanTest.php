<?php

namespace Tests\Unit;

use App\Models\Barang;
use App\Models\JenisBarang;
use Tests\TestCase;

class BarangPenyusutanTest extends TestCase
{
    public function test_prioritas_1_masa_manfaat_kustom_pada_barang_mengalahkan_jenis(): void
    {
        $jenis = new JenisBarang(['nama_jenis' => 'Laptop', 'masa_pakai_tahun' => 4]);

        $barang = new Barang([
            'nama_barang' => 'Laptop Lab Khusus',
            'tahun_perolehan' => (int) date('Y') - 2,
            'masa_manfaat' => 10,
            'harga_satuan' => 10000000,
            'jumlah' => 1,
        ]);
        $barang->setRelation('jenis', $jenis);

        $this->assertSame(10, $barang->masa_pakai);
        $this->assertSame('Kustom barang ini', $barang->sumber_masa_pakai);
        $this->assertSame(1000000.0, $barang->nilai_penyusutan_tahun);
        $this->assertSame(2000000.0, $barang->akumulasi_penyusutan);
        $this->assertSame(8000000.0, $barang->nilai_buku);
        $this->assertSame(20.0, $barang->persen_penyusutan);
    }

    public function test_prioritas_2_mengikuti_jenis_barang_saat_masa_manfaat_kustom_kosong(): void
    {
        $jenis = new JenisBarang(['nama_jenis' => 'Laptop', 'masa_pakai_tahun' => 4]);

        $barang = new Barang([
            'nama_barang' => 'Laptop Kantor',
            'tahun_perolehan' => (int) date('Y') - 1,
            'masa_manfaat' => null,
            'harga_satuan' => 8000000,
            'jumlah' => 2,
        ]);
        $barang->setRelation('jenis', $jenis);

        $this->assertSame(4, $barang->masa_pakai);
        $this->assertSame('Kategori Laptop', $barang->sumber_masa_pakai);
        $this->assertSame(4000000.0, $barang->nilai_penyusutan_tahun);
        $this->assertSame(4000000.0, $barang->akumulasi_penyusutan);
        $this->assertSame(12000000.0, $barang->nilai_buku);
    }

    public function test_prioritas_3_default_5_tahun_untuk_barang_tanpa_kategori(): void
    {
        $barang = new Barang([
            'nama_barang' => 'Alat Unik Departemen',
            'tahun_perolehan' => (int) date('Y') - 3,
            'masa_manfaat' => null,
            'harga_satuan' => 5000000,
            'jumlah' => 1,
        ]);
        $barang->setRelation('jenis', null);

        $this->assertSame(5, $barang->masa_pakai);
        $this->assertSame('Default sistem (5 thn)', $barang->sumber_masa_pakai);
        $this->assertSame(1000000.0, $barang->nilai_penyusutan_tahun);
        $this->assertSame(3000000.0, $barang->akumulasi_penyusutan);
        $this->assertSame(2000000.0, $barang->nilai_buku);
        $this->assertSame(60.0, $barang->persen_penyusutan);
    }

    public function test_aset_lewat_masa_manfaat_tersusutkan_penuh_menjadi_nol(): void
    {
        $barang = new Barang([
            'nama_barang' => 'Proyektor Lama',
            'tahun_perolehan' => (int) date('Y') - 8,
            'masa_manfaat' => 5,
            'harga_satuan' => 7500000,
            'jumlah' => 1,
        ]);
        $barang->setRelation('jenis', null);

        $this->assertSame(7500000.0, $barang->akumulasi_penyusutan);
        $this->assertSame(0.0, $barang->nilai_buku);
        $this->assertSame(100.0, $barang->persen_penyusutan);
    }

    public function test_barang_tanpa_tahun_perolehan_tidak_menyusut_negatif_atau_error(): void
    {
        $barang = new Barang([
            'nama_barang' => 'Meja Tanpa Tahun',
            'tahun_perolehan' => null,
            'masa_manfaat' => null,
            'harga_satuan' => 1500000,
            'jumlah' => 2,
        ]);
        $barang->setRelation('jenis', null);

        $this->assertSame(5, $barang->masa_pakai);
        $this->assertSame(0.0, $barang->akumulasi_penyusutan);
        $this->assertSame(3000000.0, $barang->nilai_buku);
        $this->assertSame(0.0, $barang->persen_penyusutan);
    }
}
