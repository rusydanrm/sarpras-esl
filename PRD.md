# Product Requirement Document (PRD)
## Sistem Informasi Manajemen Sarana dan Prasarana (Sarpras ESL)
**Departemen Ekonomi Sumberdaya dan Lingkungan — IPB University**

---

## 1. Executive Summary & Problem Statement

### 1.1 Latar Belakang
Pengelolaan inventaris sarana dan prasarana di Departemen Ekonomi Sumberdaya dan Lingkungan (ESL), Fakultas Ekonomi dan Manajemen (FEM), IPB University selama ini masih bertumpu pada pencatatan manual berbasis spreadsheet Excel (*Daftar Inventaris Ruangan / DIR* dan *Daftar Barang Ruangan / DBR*). Proses manual ini menimbulkan berbagai kendala operasional:
- **Fragmentasi Data:** Berkas Excel tersebar di masing-masing ruangan tanpa database terpusat, menyebabkan kesulitan rekonsiliasi total nilai aset departemen.
- **Pengajuan Tidak Terekam:** Permohonan perbaikan atau pengadaan barang dilakukan melalui komunikasi informal (chat WhatsApp atau nota kertas) sehingga riwayat disposisi dan realisasi biaya sering tercecer.
- **Bentrok Peminjaman:** Peminjaman ruang sidang, ruang kelas, maupun alat laboratorium rawan tumpang tindih (*overlap*) jadwal karena ketiadaan jadwal kalender terintegrasi secara real-time.
- **Pemeliharaan Tidak Terjadwal:** Servis preventif berkala (AC, proyektor, komputer lab, kendaraan operasional) sering terlewat karena tidak adanya pengingat otomatis sebelum masa pemeliharaan jatuh tempo.
- **Transparansi Fasilitas Rendah:** Mahasiswa dan pihak luar kesulitan mengetahui fasilitas yang tersedia di setiap ruangan tanpa bertanya langsung kepada staf departemen.

### 1.2 Tujuan Produk
1. **Sentralisasi Inventaris:** Memusatkan seluruh data ruangan, barang, dan kendaraan dinas ke dalam satu platform web terintegrasi.
2. **Otomasi Alur Pengajuan:** Menyediakan workflow persetujuan berjenjang (*multi-stage approval*) yang transparan dan akuntabel.
3. **Pencegahan Bentrok:** Mengotomatiskan validasi jadwal peminjaman ruangan dan peralatan anti-tumpang tindih.
4. **Pemeliharaan Preventif:** Mengimplementasikan *scheduled reminder* harian untuk pemeliharaan rutin aset dan kendaraan.
5. **Analisis Kesehatan Aset:** Memberikan rekomendasi perbaikan/penggantian aset otomatis berdasarkan usia pakai dan kondisi fisik.
6. **Migrasi Cepat (Smart Excel Import):** Memfasilitasi konversi data format DIR/DBR lama melalui upload file maupun *paste clipboard* langsung.
7. **Portal Terbuka:** Menyediakan katalog fasilitas publik (*read-only*) tanpa login bagi mahasiswa dan dosen.

---

## 2. Tech Stack & Arsitektur Sistem

- **Platform:** Desktop-First Web Application (Responsive Tablet/Mobile).
- **Backend Framework:** Laravel 13, PHP 8.4 (MySQL Database).
- **Frontend Layer:** Blade Templating + Tailwind CSS + Alpine.js (Lightweight reactive client-side interactivity, tanpa overhead SPA).
- **Desain & UI:** Mengadopsi pedoman **[DESIGN.md](file:///d:/Kuliah/Sarpras_ESL/sarpras-esl/DESIGN.md)** (FlowTask Modern Collaborative Workspace + Apple Design tactile fluid interactions).
- **Konvensi Database:**
  - Primary Key seragam: `{nama_tabel}_id` (contoh: `barang_id`, `ruangan_id`, `pengajuan_id`).
  - Stored Generated Columns: `luas_m2` (`panjang * lebar`) pada tabel `ruangan`, dan `total_harga` (`jumlah * harga_satuan`) pada tabel `barang`.
- **Interaksi Data:** 
  - Dynamic Table Filtering via AJAX Partial View swap (`#tabelWrapper` / `_table.blade.php`) dengan *debounced input*.
  - Modal mengambang (*floating modal / slide-over drawer*) untuk aksi tambah/edit/detail agar pengguna tidak kehilangan konteks halaman.
- **Pustaka Ekspor/Impor:** `maatwebsite/excel` (Excel .xlsx) dan `barryvdh/laravel-dompdf` (PDF resmi bertanda tangan).

---

## 3. User Personas & Hak Akses (RBAC)

Sistem membedakan hak akses ke dalam **4 Role Terotentikasi + 1 Penugasan Khusus + 1 Akses Publik**:

| Role / Entitas | Deskripsi & Hak Akses Utama |
| :--- | :--- |
| **Admin** | Staf pengelola sarpras utama. Akses penuh ke seluruh modul, aktivasi akun pendaftar baru (*pending* ➔ *aktif*), assignment role pengguna, kelola master data, verifikasi tahap-1 pengajuan, dan persetujuan peminjaman. |
| **Dosen / Tendik** | Tenaga pengajar dan kependidikan. Dapat mengajukan permohonan pengajuan sarpras dan peminjaman fasilitas. Jika ditugaskan sebagai **Koordinator Ruangan**, memiliki wewenang CRUD barang pada ruangan yang dikoordinasikannya. |
| **Koordinator Ruangan** | *Bukan role terpisah*, melainkan penugasan *Many-to-Many* (tabel pivot `ruangan_koordinator`). Satu dosen/tendik dapat mengoordinasikan beberapa ruangan, dan satu ruangan dapat memiliki lebih dari satu koordinator. |
| **Teknisi** | Petugas pemeliharaan fisik. Bertugas mencatat log tindakan pemeliharaan, memproses tiket pengajuan yang telah disetujui pimpinan, mencatat realisasi biaya servis, dan menyelesaikan pengajuan. |
| **Pimpinan** | Ketua Departemen / Sekretaris Departemen ESL. Akses monitoring & laporan eksekutif, persetujuan tahap-2 (final approval/rejection) pengajuan sarpras, serta persetujuan peminjaman ruangan. |
| **Publik (Tanpa Login)** | Mahasiswa atau sivitas akademika umum. Mengakses portal `/portal` untuk melihat kartu ruangan, kapasitas, daftar fasilitas barang yang tersedia secara *read-only*. |

### Alur Pendaftaran Akun Baru (Self-Register Flow)
```
User Registrasi Mandiri ➔ Akun Dibuat (status: 'pending', role: null)
➔ Dialihkan ke halaman /menunggu-persetujuan
➔ Admin membuka modul 'Kelola Pengguna' ➔ Pilih Role & Klik 'Aktifkan'
➔ Status menjadi 'aktif' ➔ User dapat login & mengakses modul sesuai role.
```

---

## 4. Spesifikasi Fungsional Modul (12 Modul Inti)

### Modul 1: Autentikasi & Kelola Pengguna
- **Fungsi:** Login, registrasi akun baru, reset password, update profil mandiri.
- **Fitur Admin:**
  - Tabel pengguna dengan badge status (`Pending`, `Aktif`, `Nonaktif`).
  - Modal penetapan role (`admin`, `dosen_tendik`, `teknisi`, `pimpinan`) dan penempatan unit/bagian kerja (`unit_bagian_id`).
  - Toggle aktivasi akun dalam 1-klik.

### Modul 2: Dashboard & Workspace Overview
- **Header:** Ucapan dinamis personal (*"Selamat Pagi, [Nama]! 👋"*), filter periode semester/bulan, tombol aksi cepat *+ Ajukan Sarpras*.
- **Row 1 (4 Kartu KPI):**
  1. Total Ruangan (+ persentase status aktif).
  2. Total Nilai Aset (+ estimasi rupiah perolehan & tren).
  3. Total Unit Barang (+ rasio kondisi).
  4. Perlu Tindakan / Rusak Berat (+ indikator urgensi servis).
- **Row 2 (3-Column Grid):**
  - Kolom Kiri: Progress utilisasi & kapasitas ruangan utama (progress bar visual).
  - Kolom Tengah: Ring Donut Chart kondisi fisik aset (Baik, RR, RB) dengan metrik total di tengah dan legenda di bawah.
  - Kolom Kanan: Mini kalender mingguan + daftar timeline agenda peminjaman & jadwal servis terdekat.
- **Row 3 (2-Column Grid):**
  - Daftar Koordinator Ruangan & Teknisi yang aktif bertugas.
  - Grafik batang bertingkat (*Stacked Bar Chart*) tren pemeliharaan & pengajuan 7 hari terakhir.
- **Row 4 (Kanban Workflow):** Ringkasan alur tiket pengajuan (Diajukan ➔ Diverifikasi ➔ Disetujui ➔ Selesai).

### Modul 3: Master Data Terpadu
- Menggunakan arsitektur *Generic Tabbed Controller* untuk mengelola 4 entitas master:
  1. **Gedung:** Kode gedung, nama gedung, lokasi/deskripsi.
  2. **Unit / Bagian Kerja:** Nama unit (e.g., Tata Usaha, Lab Komputasi, Divisi Keuangan).
  3. **Vendor / Rekanan:** Nama rekanan penyedia/servis, kontak telepon, alamat, PIC.
  4. **Jenis Barang:** Nama kategori + kolom kritis **`masa_pakai_tahun`** (standar masa pakai dalam tahun untuk dasar kalkulasi penyusutan dan rekomendasi penggantian).

### Modul 4: Inventarisasi Ruangan & Data Barang
- **Ruangan:**
  - Kode ruangan, nama, lantai, kapasitas orang, dimensi (`panjang` x `lebar` ➔ `luas_m2` otomatis via MySQL generated column), status kelayakan.
  - Penugasan Multi-Koordinator via Alpine multiselect pivot.
- **Data Barang (Tingkat Ruangan & Lintas Ruangan / Global):**
  - Atribut: Kode barang, NUP / Serial Number, nama barang, merk/tipe, tahun perolehan, harga perolehan satuan, jumlah unit, total harga (otomatis `jumlah * harga_satuan`), interval pemeliharaan berkala (dalam bulan), tanggal pemeliharaan terakhir, vendor penyedia.
  - Kondisi terbagi 3 hitungan unit: `kondisi_baik`, `kondisi_rusak_ringan`, `kondisi_rusak_berat`.
  - Modal detail komprehensif menampilkan spesifikasi lengkap dan riwayat servis barang tersebut.

### Modul 5: Smart Impor Excel (Format DIR / DBR)
- Mengatasi hambatan migrasi dari format spreadsheet Excel lama:
  - **Mode 1 (Upload File):** Mengunggah file spreadsheet `.xlsx` atau `.csv`.
  - **Mode 2 (Tempel Clipboard):** Pengguna cukup menyalin (*Ctrl+C*) tabel dari Microsoft Excel lalu menempelkannya (*Ctrl+V*) ke textarea sistem.
- **Parser Cerdas:** Otomatis mendeteksi kolom DIR/DBR asli kampus, termasuk mengonversi kolom centang kondisi (`B`, `RR`, `RB`) menjadi angka kuantitas database.
- **Pratinjau & Validasi:** Tampilan pratinjau data dengan validasi baris merah jika ruangan atau kategori tidak ditemukan sebelum data disimpan permanen ke database, disertai riwayat log impor (`import_logs`).

### Modul 6: Modul Pengajuan Sarpras (Multi-Stage Workflow)
- **Jenis Pengajuan:** Pengadaan Baru, Perbaikan / Servis, Pemeliharaan Rutin, Penggantian Aset Rusak.
- **Alur Status Berjenjang:**
  ```
  [1. Diajukan] (oleh Dosen/Tendik/Teknisi)
       ↓
  [2. Diverifikasi] (oleh Admin - Tahap 1: Verifikasi kelengkapan & estimasi biaya)
       ↓
  [3. Disetujui / Ditolak] (oleh Pimpinan - Tahap 2: Persetujuan akhir wewenang anggaran)
       ↓
  [4. Diproses] (oleh Teknisi/Admin - Tahap 3: Pelaksanaan servis/pembelian)
       ↓
  [5. Selesai] (Pencatatan realisasi biaya & pembaruan kondisi aset)
  ```
- **Tampilan Antarmuka:** Tersedia dalam mode **Tabel Interaktif** dan mode **Kanban Board** dengan kartu tiket berprioritas (*High, Medium, Low*), lampiran bukti foto/dokumen, serta catatan penolakan jika tidak disetujui.

### Modul 7: Pemeliharaan (Maintenance) & Scheduler Otomatis
- **Pencatatan Tindakan Servis:** Tanggal pengerjaan, jenis tindakan (rutin/perbaikan), biaya servis riil, pihak pelaksana (teknisi internal atau vendor rekanan), perubahan kondisi barang (sebelum vs sesudah), nota/faktur.
- **Otomasi Pemeliharaan Rutin:**
  - Tiap barang memiliki konfigurasi `interval_pemeliharaan_bulan` dan `tanggal_pemeliharaan_terakhir`.
  - Perintah Laravel Console `cek:jadwal-pemeliharaan` dijadwalkan berjalan otomatis setiap hari pukul 07:00 pagi.
  - Menghitung tanggal jatuh tempo servis. Jika telah jatuh tempo, otomatis menerbitkan data ke tabel `notifikasi` bagi Admin dan Teknisi.

### Modul 8: Peminjaman Ruangan & Fasilitas (Anti-Bentrok)
- **Objek Pinjam:** Ruangan atau Peralatan/Barang tertentu (`jenis_peminjaman`).
- **Mesin Validasi Anti-Overlap (Conflict Prevention):**
  - Sistem otomatis mengecek tumpang tindih waktu: `tanggal_mulai` s/d `tanggal_selesai` terhadap reservasi lain yang sudah disetujui.
  - Memberikan peringatan instan jika waktu yang dipilih telah terisi agenda lain.
- **Alur Peminjaman:** Diajukan ➔ Disetujui Admin/Pimpinan ➔ Sedang Dipinjam ➔ Selesai / Dikembalikan (dengan checklist verifikasi kondisi aset saat kembali).

### Modul 9: Monitoring & Rekomendasi Aset (Maintenance & Repair / MR)
- **Kalkulasi Kesehatan Aset Otomatis:**
  - Menghitung usia berjalan aset (`Tahun Sekarang - Tahun Perolehan`) dibandingkan dengan standar `masa_pakai_tahun` pada master jenis barang.
  - Status Kelayakan:
    - *Normal:* Usia belum melampaui batas dan kondisi baik.
    - *Perhatian:* Mendekati batas usia pakai atau berstatus Rusak Ringan.
    - *Kritis / Usulan Penggantian:* Telah melampaui masa pakai atau kondisi Rusak Berat dengan frekuensi servis tinggi.
- **Rekomendasi Tindakan:** Sistem menyarankan opsi: `Pemeliharaan Rutin`, `Servis Besar`, atau `Penghapusan Aset / Pengadaan Baru`.
- Ekspor rekapitulasi analisis ke format Excel dan PDF.

### Modul 10: Transportasi & Servis Kendaraan Operasional
- **Inventaris Kendaraan:** No Polisi, merk/tipe, kategori (Roda 2 / Roda 4), tahun pembuatan, nomor rangka, nomor mesin, penanggung jawab/driver operasional, odometer (KM terakhir).
- **Pengawasan Legalitas:** Pencatatan jatuh tempo Pajak Tahunan (1 tahunan) dan jatuh tempo STNK (5 tahunan) dengan badge peringatan jika mendekati jatuh tempo.
- **Riwayat Servis Kendaraan:** Log servis berkala (ganti oli, tune up, ganti ban), kilometer servis, bengkel rekanan, rincian biaya, dan lampiran nota.

### Modul 11: Laporan & Ekspor Dokumen Resmi
- **Penyaringan Laporan Dinamis:** Berdasarkan gedung, ruangan, kategori barang, status kondisi (B/RR/RB), tahun perolehan, dan rentang tanggal perolehan.
- **Format Ekspor:**
  1. **Excel (.xlsx):** Format tabel siap olah dengan baris total dan formula kalkulasi.
  2. **PDF Resmi (DIR):** Format cetak standar universitas lengkap dengan kop Departemen ESL IPB, identitas ruangan & koordinator, tabel barang rapi, dan kolom tanda tangan pengesahan pimpinan.

### Modul 12: Portal Publik (`/portal`)
- Dapat diakses langsung oleh mahasiswa, dosen, dan tamu tanpa autentikasi login.
- Layout minimalis tanpa sidebar admin:
  - Hero banner selamat datang dan kolom pencarian fasilitas.
  - Grid kartu ruangan menampilkan foto, kapasitas orang, luas m², nama koordinator ruangan, dan badge fasilitas utama.
  - Halaman detail ruangan yang menampilkan tabel barang dan peralatan yang tersedia di ruangan tersebut secara *read-only*.

---

## 5. Skema Relasi Database (Entity Relationship)

```
[unit_bagian] 1──n [users]
[gedung] 1──n [ruangan] 1──n [barang]
[users] n──n [ruangan] (via ruangan_koordinator pivot)
[jenis_barang] 1──n [barang]
[vendor] 1──n [barang]
[users] 1──n [pengajuan]
[barang] 1──n [pengajuan]
[pengajuan] 1──1 [pemeliharaan]
[barang] 1──n [pemeliharaan]
[users] 1──n [peminjaman]
[ruangan] 1──n [peminjaman]
[barang] 1──n [peminjaman]
[kendaraan] 1──n [servis_kendaraan]
[users] 1──n [notifikasi]
[users] 1──n [import_logs]
```

### Tabel Kunci:
1. `users` (`user_id`, `nama`, `email`, `password`, `role`, `status`, `unit_id`)
2. `gedung` (`gedung_id`, `kode_gedung`, `nama_gedung`, `lokasi`)
3. `ruangan` (`ruangan_id`, `gedung_id`, `kode_ruang`, `nama_ruang`, `lantai`, `kapasitas`, `panjang`, `lebar`, `luas_m2` [stored], `status`)
4. `ruangan_koordinator` (`id`, `ruangan_id`, `user_id`)
5. `jenis_barang` (`jenis_id`, `kode_jenis`, `nama_jenis`, `masa_pakai_tahun`)
6. `vendor` (`vendor_id`, `nama_vendor`, `kontak`, `alamat`)
7. `barang` (`barang_id`, `ruangan_id`, `jenis_id`, `vendor_id`, `kode_barang`, `nup`, `nama_barang`, `merk`, `tahun_perolehan`, `harga_satuan`, `jumlah`, `satuan`, `total_harga` [stored], `kondisi_baik`, `kondisi_rusak_ringan`, `kondisi_rusak_berat`, `interval_pemeliharaan_bulan`, `tanggal_pemeliharaan_terakhir`)
8. `pengajuan` (`pengajuan_id`, `user_id`, `barang_id`, `ruangan_id`, `jenis_pengajuan`, `deskripsi`, `estimasi_biaya`, `realisasi_biaya`, `status`, `verifikator_id`, `catatan_verifikasi`, `lampiran`)
9. `pemeliharaan` (`pemeliharaan_id`, `barang_id`, `pengajuan_id`, `tanggal_tindakan`, `jenis_pemeliharaan`, `biaya`, `pelaksana`, `kondisi_sebelum`, `kondisi_setelah`, `deskripsi_tindakan`)
10. `peminjaman` (`peminjaman_id`, `user_id`, `jenis_peminjaman`, `ruangan_id`, `barang_id`, `tanggal_mulai`, `tanggal_selesai`, `tujuan`, `status`, `kondisi_kembali`)
11. `kendaraan` (`kendaraan_id`, `nomor_polisi`, `merk_tipe`, `jenis`, `tahun`, `nomor_rangka`, `nomor_mesin`, `penanggung_jawab`, `jatuh_tempo_pajak`, `jatuh_tempo_stnk`, `odometer_km`)
12. `servis_kendaraan` (`servis_id`, `kendaraan_id`, `tanggal_servis`, `odometer_km`, `jenis_servis`, `biaya`, `bengkel`, `nota_lampiran`)
13. `notifikasi` (`notifikasi_id`, `user_id`, `judul`, `pesan`, `link`, `is_dibaca`)
14. `import_logs` (`import_id`, `user_id`, `nama_file`, `mode`, `jumlah_berhasil`, `jumlah_gagal`)

---

## 6. Persyaratan Non-Fungsional (NFR)
1. **Keamanan & Otorisasi:** Proteksi CSRF pada semua form, enkripsi password via bcrypt/Argon2, otorisasi ketat berbasis Role & Policy Middleware.
2. **Kinerja (Zero N+1 Query):** Optimasi pemuatan relasi menggunakan Eager Loading (`with('ruangan.gedung', 'jenis')`) pada seluruh controller dan kueri dashboard.
3. **Integritas Transaksi:** Operasi multi-tabel (proses penyimpanan impor massal, update status pengajuan yang memicu log pemeliharaan) wajib dibungkus dalam `DB::transaction()`.
4. **Aksesibilitas & Responsivitas:** UI dirancang dengan standar kontras warna tajam (rasio minimal 4.5:1), tipografi responsif rem, dan navigasi ramah sentuhan ponsel.

---

## 7. Roadmap Implementasi Prompt Desain (Stitch Screen Mapping)
Gunakan dokumen PRD ini bersamaan dengan **[DESIGN.md](file:///d:/Kuliah/Sarpras_ESL/sarpras-esl/DESIGN.md)** saat membuat prompt desain per-halaman di Stitch:

- **Prompt Layar 1:** Dashboard Utama & Workspace Overview *(Bento KPI, Progres Ruangan, Donut Kondisi, Mini Kalender, Kanban Tiket)*
- **Prompt Layar 2:** Inventaris Ruangan & Detail Barang Ruangan *(Katalog DIR / Kartu Ruangan + Drawer Barang)*
- **Prompt Layar 3:** Manajemen Pengajuan Sarpras *(Papan Kanban Berjenjang + Form Modal Pengajuan Multi-Step)*
- **Prompt Layar 4:** Modul Peminjaman Fasilitas *(Kalender Interaktif Slot Waktu + Validasi Anti-Bentrok)*
- **Prompt Layar 5:** Maintenance & Repair (MR) *(Kesehatan Aset, Usia Pakai vs Standar, Log Servis)*
- **Prompt Layar 6:** Transportasi & Kendaraan Operasional *(Kartu Kendaraan, Notifikasi Pajak, Log Bengkel)*
- **Prompt Layar 7:** Portal Publik Mahasiswa `/portal` *(Katalog Fasilitas Read-Only Tanpa Login)*
