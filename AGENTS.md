# AGENTS.md — Sistem Sarpras ESL

## Ringkasan Proyek
Aplikasi web manajemen sarana dan prasarana untuk Departemen Ekonomi
Sumberdaya dan Lingkungan (ESL), IPB University. Menggantikan pencatatan
manual berbasis Excel (DIR/DBR). Proyek magang mahasiswa TRPL, dikerjakan
bertahap fitur demi fitur — SEMUA keputusan arsitektur di bawah ini
SUDAH DIPUTUSKAN secara sadar, jangan disarankan untuk diubah tanpa diminta
eksplisit oleh pengguna.

## Tech Stack (WAJIB, jangan disarankan ganti)
- Backend: Laravel 13, PHP 8.4
- Frontend: Blade templating + Tailwind CSS + Alpine.js
- **BUKAN** React/Vue/Livewire/Inertia — ini keputusan sadar demi kemudahan
  handover ke staf non-developer ESL setelah magang selesai.
- Database: MySQL
- Semua form tambah/edit tampil sebagai **modal mengambang** (Alpine
  `x-data`/`x-show`), bukan halaman terpisah.
- Ikon: `blade-ui-kit/blade-heroicons` (`<x-heroicon-o-nama />`)
- Excel: `maatwebsite/excel` (perlu ekstensi PHP `zip` aktif)
- PDF: `barryvdh/laravel-dompdf`

## Design System
Warna (Tailwind arbitrary value, contoh `bg-[#120F8B]`):
- Primary: `#120F8B` (navy, sidebar & tombol utama)
- Success: `#10B981` — kondisi "Baik"
- Warning: `#F59E0B` — kondisi "Rusak Ringan"
- Danger: `#EF4444` — kondisi "Rusak Berat"
- Neutral: `#181B21` (judul), `#495465` (body), `#9EA9B9` (placeholder)
- Border: `#DEE2E7`, Background: `#F7F8F9`
- Excel: `#1D6F42`, PDF: `#C8102E` (khusus tombol ekspor)

Komponen reusable di `resources/views/components/ui/`:
`<x-ui.button variant="primary|outline|danger|ghost|excel|pdf" size="sm|md|lg">`,
`<x-ui.input label="...">`, `<x-ui.select label="...">`, `<x-ui.badge
variant="success|warning|danger">`, `<x-ui.card>`, `<x-ui.modal open="varName"
title="...">`, `<x-ui.confirm-delete-form action="..." title="..."
message="...">`.

**PENTING — bug yang sudah pernah terjadi, jangan diulang:**
`@json($var)` GAGAL diproses kalau ditulis di dalam atribut tag komponen
custom (`<x-ui.button @click='x = @json($var)'>`). WAJIB pakai
`{{ Illuminate\Support\Js::from($var) }}` untuk kasus itu. Di tag `<button>`
HTML biasa (bukan komponen), `@json()` tetap aman dipakai.

Layout: `resources/views/components/layouts/app.blade.php` (admin, sidebar
navy + topbar) dan `components/layouts/public.blade.php` (portal publik,
tanpa sidebar). Sidebar responsive: drawer di mobile via `x-data="{
sidebarOpen: false }"`.

## Role & Hak Akses (4 role, BUKAN 3 — sempat direvisi dari Admin/Koordinator/
Viewer jadi Admin/Dosen-Tendik/Teknisi/Pimpinan)
- **admin**: akses penuh semua modul, verifikasi tahap-1 Pengajuan
- **dosen_tendik**: bisa jadi koordinator ruangan (banyak-ke-banyak, lihat
  bawah), CRUD barang di ruangan yang dikoordinasi, ajukan Pengajuan/
  Peminjaman
- **teknisi**: catat Pemeliharaan, proses/selesaikan Pengajuan
- **pimpinan**: read-only + persetujuan tahap-2 Pengajuan + persetujuan
  Peminjaman

"Koordinator Ruangan" BUKAN role terpisah — itu penugasan lewat tabel pivot
`ruangan_koordinator` (many-to-many, 1 ruangan bisa punya banyak koordinator,
1 user bisa koordinasi banyak ruangan). Model: `Ruangan::koordinator()`
`belongsToMany`, helper `$ruangan->dikoordinasiOleh($userId)`.

Akun baru: self-register → status `pending`, `role = null` → Admin approve
lewat Kelola Pengguna (pilih role + ubah status `aktif`).

## Alur Status (workflow)
**Pengajuan** (Pengadaan/Perbaikan/Pemeliharaan/Penggantian):
`Diajukan → [Admin verifikasi] → Diverifikasi → [Pimpinan setuju/tolak] →
Disetujui/Ditolak → [Admin/Teknisi proses] → Diproses → Selesai`

**Peminjaman** (Ruangan ATAU Barang, kolom `jenis_peminjaman`):
`Diajukan → [Admin/Pimpinan setuju/tolak] → Disetujui/Ditolak → [Admin
tandai] → Dikembalikan`. Ada pengecekan bentrok jadwal (overlap tanggal)
sebelum booking baru disetujui.

## Modul yang Sudah Selesai
Login (tanpa self-register aktif belum, lihat catatan di atas), Dashboard
+ chart (Chart.js, query dioptimasi hindari N+1), Inventaris Ruangan, Data
Barang (lintas ruangan + modal detail read-only, BUKAN tabel semua kolom),
Pengajuan, Pemeliharaan (+ jadwal rutin otomatis via
`interval_pemeliharaan_bulan` & `tanggal_pemeliharaan_terakhir` di tabel
`barang`, command `cek:jadwal-pemeliharaan` scheduled daily 07:00, generate
ke tabel `notifikasi`), Peminjaman, Impor Excel (2 mode: upload file DAN
tempel langsung dari clipboard Excel — parser di
`ImportExcelController::previewTempel()` menangani format kolom asli DIR/DBR
termasuk kolom B/RR/RB checkbox untuk kondisi), Laporan (filter + ekspor
Excel/PDF), Master Data (Gedung/Jenis Barang/Vendor/Unit — 1 controller
generik dengan tab), Kelola Pengguna, Portal publik read-only di `/portal`
(tanpa login, layout terpisah, card grid ruangan → tabel detail barang).

## Konvensi Kode yang Sudah Dipakai Konsisten
- Filter tabel: AJAX partial (`fetch()` + `innerHTML` swap ke `#tabelWrapper`,
  bukan full page reload), pola: `_table.blade.php` terpisah dari
  `index.blade.php`, controller cek `$request->ajax()` → return partial view.
  Text input pakai `x-on:input.debounce.400-500ms`, select pakai
  `x-on:change`.
- Pagination: `paginate(10)` (bukan 15), view custom di
  `resources/views/vendor/pagination/tailwind.blade.php` (sudah di-restyle
  navy, jangan generate ulang dari default Laravel).
- Konfirmasi hapus: SELALU pakai `<x-ui.confirm-delete-form>`, JANGAN pakai
  `confirm()` JavaScript bawaan browser.
- Primary key semua tabel: `{nama_tabel}_id` (bukan generik `id`).
- `luas_m2` dan `total_harga` adalah **generated column** MySQL
  (`storedAs()` di migration) — jangan pernah di-set manual di controller.

## Yang BELUM Dikerjakan / Perlu Ditanyakan Dulu ke User Sebelum Mulai
- Halaman Register belum diaktifkan ulang (sempat aktif lalu dibahas ulang,
  cek `routes/auth.php` untuk status terkini).
- Dokumentasi testing & laporan magang belum disusun.
- Jangan asumsikan scope baru (misal QR code, stok per-unit barang individual,
  vendor self-service) — semua itu sudah pernah dibahas dan sengaja
  ditunda/ditolak. Tanya dulu ke user kalau relevan.

## Cara Kerja yang Diharapkan
- Selalu tanya dulu sebelum melakukan perubahan struktur database/skema
  besar — user sangat menghargai konfirmasi sebelum migrasi besar.
- Kalau menambah field baru ke form, SELALU cek juga: (1) migration & model
  fillable, (2) validasi di controller (biasanya ada 2-3 method: store,
  update, dan kadang storeGlobal), (3) modal Tambah DAN modal Edit — jangan
  cuma salah satu.
- User lebih suka penjelasan singkat kenapa sesuatu terjadi/error sebelum
  kode perbaikan, bukan langsung dump kode tanpa konteks.
