<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\RuanganController;
use App\Http\Controllers\BarangController;
use App\Http\Controllers\PengajuanController;
use App\Http\Controllers\PemeliharaanController;
use App\Http\Controllers\NotifikasiController;
use App\Http\Controllers\PeminjamanController;
use App\Http\Controllers\UserController;
use App\Http\Controllers\MasterDataController;
use App\Http\Controllers\ImportExcelController;
use App\Http\Controllers\LaporanController;
use App\Http\Controllers\RekomendasiController;
use App\Http\Controllers\TransportasiController;
use App\Http\Controllers\PortalController;

Route::get('/', function () {
    return redirect()->route('dashboard');
});

Route::get('/portal', [PortalController::class, 'index'])->name('portal.index');
Route::get('/portal/ruangan/{ruangan}', [PortalController::class, 'show'])->name('portal.show');

Route::middleware(['auth', 'active'])->group(function () {
    
    Route::middleware('auth')->group(function () {

        Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard');

        Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
        Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
        Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');

        Route::get('/barang', [BarangController::class, 'index'])->name('barang.index');

        // Admin & Dosen/Tendik bisa kelola ruangan (dosen_tendik dibatasi ruangan miliknya di controller)
        Route::middleware('role:admin,dosen_tendik')->group(function () {
            Route::resource('ruangan', RuanganController::class);
            Route::resource('ruangan.barang', BarangController::class)->shallow();

            Route::post('/barang', [BarangController::class, 'storeGlobal'])->name('barang.store-global');

            // Modul Import Excel
            Route::get('/impor-excel', [ImportExcelController::class, 'index'])->name('impor.index');
            Route::post('/impor-excel/preview', [ImportExcelController::class, 'preview'])->name('impor.preview');
            Route::post('/impor-excel/simpan', [ImportExcelController::class, 'simpan'])->name('impor.simpan');
            Route::get('/impor-excel/template', [ImportExcelController::class, 'downloadTemplate'])->name('impor.template');
            Route::post('/impor-excel/tempel', [ImportExcelController::class, 'previewTempel'])->name('impor.tempel');
        });

        Route::middleware('role:admin')->group(function () {
            // Kelola pengguna
            Route::resource('pengguna', UserController::class);

            // Modul Master Data
            Route::get('/master-data', [MasterDataController::class, 'index'])->name('master-data.index');
            Route::post('/master-data/{tipe}', [MasterDataController::class, 'store'])->name('master-data.store');
            Route::put('/master-data/{tipe}/{id}', [MasterDataController::class, 'update'])->name('master-data.update');
            Route::delete('/master-data/{tipe}/{id}', [MasterDataController::class, 'destroy'])->name('master-data.destroy');
        });

        // Modul Pengajuan
        Route::get('/pengajuan', [PengajuanController::class, 'index'])->name('pengajuan.index');
        Route::post('/pengajuan', [PengajuanController::class, 'store'])->name('pengajuan.store');
        Route::patch('/pengajuan/{pengajuan}/verifikasi', [PengajuanController::class, 'verifikasi'])->name('pengajuan.verifikasi');
        Route::patch('/pengajuan/{pengajuan}/setujui', [PengajuanController::class, 'setujui'])->name('pengajuan.setujui');
        Route::patch('/pengajuan/{pengajuan}/tolak', [PengajuanController::class, 'tolak'])->name('pengajuan.tolak');
        Route::patch('/pengajuan/{pengajuan}/proses', [PengajuanController::class, 'proses'])->name('pengajuan.proses');
        Route::patch('/pengajuan/{pengajuan}/selesai', [PengajuanController::class, 'selesai'])->name('pengajuan.selesai');

        // Modul Pemeliharaan
        Route::get('/pemeliharaan', [PemeliharaanController::class, 'index'])->name('pemeliharaan.index');
        Route::post('/pemeliharaan', [PemeliharaanController::class, 'store'])->name('pemeliharaan.store');

        // Notifikasi
        Route::post('/notifikasi/baca-semua', [NotifikasiController::class, 'bacaSemua'])->name('notifikasi.baca-semua');

        // Modul Peminjaman
        Route::get('/peminjaman', [PeminjamanController::class, 'index'])->name('peminjaman.index');
        Route::post('/peminjaman', [PeminjamanController::class, 'store'])->name('peminjaman.store');
        Route::patch('/peminjaman/{peminjaman}/setujui', [PeminjamanController::class, 'setujui'])->name('peminjaman.setujui');
        Route::patch('/peminjaman/{peminjaman}/tolak', [PeminjamanController::class, 'tolak'])->name('peminjaman.tolak');
        Route::patch('/peminjaman/{peminjaman}/kembalikan', [PeminjamanController::class, 'kembalikan'])->name('peminjaman.kembalikan');

        // Modul Laporan
        Route::get('/laporan', [LaporanController::class, 'index'])->name('laporan.index');
        Route::get('/laporan/ekspor-excel', [LaporanController::class, 'eksporExcel'])->name('laporan.excel');
        Route::get('/laporan/ekspor-pdf', [LaporanController::class, 'eksporPdf'])->name('laporan.pdf');

        // Modul Monitoring & Rekomendasi (MR)
        Route::get('/rekomendasi', [RekomendasiController::class, 'index'])->name('rekomendasi.index');
        Route::get('/rekomendasi/ekspor-excel', [RekomendasiController::class, 'eksporExcel'])->name('rekomendasi.excel');
        Route::get('/rekomendasi/ekspor-pdf', [RekomendasiController::class, 'eksporPdf'])->name('rekomendasi.pdf');
        Route::get('/rekomendasi/{barang}', [RekomendasiController::class, 'show'])->name('rekomendasi.show');

        // Modul Transportasi & Monitoring Servis Kendaraan
        Route::resource('transportasi', TransportasiController::class)
            ->parameters(['transportasi' => 'kendaraan'])
            ->except(['create', 'edit']);
        Route::post('/transportasi/{kendaraan}/servis', [TransportasiController::class, 'catatServis'])->name('transportasi.servis');
        Route::get('/transportasi/{kendaraan}/riwayat', [TransportasiController::class, 'riwayatServis'])->name('transportasi.riwayat');
    });
});

Route::middleware('auth')->get('/menunggu-persetujuan', function () {
    return view('menunggu-persetujuan');
})->name('menunggu-persetujuan');

require __DIR__.'/auth.php';