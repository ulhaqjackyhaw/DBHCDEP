# DBHC2 — Sistem Informasi Database Human Capital

Aplikasi web berbasis **Laravel** untuk pengelolaan data kepegawaian (human capital) secara terpusat. Sistem ini mencakup manajemen data karyawan, formasi jabatan, realisasi program kerja, penugasan, data Pejabat Ganti Sementara (PGS), kontrol versi data, serta analitik visual berbasis grafik.

---

## Daftar Isi

- [Gambaran Umum](#gambaran-umum)
- [Teknologi yang Digunakan](#teknologi-yang-digunakan)
- [Struktur Modul](#struktur-modul)
- [Fitur-Fitur Sistem](#fitur-fitur-sistem)
  - [1. Autentikasi & Manajemen Pengguna](#1-autentikasi--manajemen-pengguna)
  - [2. Dashboard & Analitik](#2-dashboard--analitik)
  - [3. Data Karyawan](#3-data-karyawan)
  - [4. Formasi Jabatan](#4-formasi-jabatan)
  - [5. Jabatan Lowong](#5-jabatan-lowong)
  - [6. Realisasi Program Kerja](#6-realisasi-program-kerja)
  - [7. Data PGS (Pejabat Ganti Sementara)](#7-data-pgs-pejabat-ganti-sementara)
  - [8. Data Penugasan](#8-data-penugasan)
  - [9. Version Control (Snapshot Data)](#9-version-control-snapshot-data)
  - [10. Audit Log](#10-audit-log)
  - [11. Profil Pengguna](#11-profil-pengguna)
- [Alur Bisnis](#alur-bisnis)
- [Sistem Kontrol Akses (Peran)](#sistem-kontrol-akses-peran)
- [Struktur Data Utama](#struktur-data-utama)
- [Ekspor & Impor Excel](#ekspor--impor-excel)

---

## Gambaran Umum

DBHC2 dirancang sebagai **pusat kendali data SDM** di sebuah organisasi/perusahaan. Sistem ini memungkinkan tim Human Capital untuk:

- Memantau seluruh data karyawan (organik maupun outsourcing) dalam satu platform.
- Mencocokkan ketersediaan karyawan dengan formasi jabatan yang ada, sehingga jabatan lowong dapat teridentifikasi secara otomatis.
- Melacak penugasan lintas unit, status PGS, dan realisasi program kerja SDM.
- Menjaga keamanan data melalui sistem **snapshot/version control** — data dapat di-rollback ke kondisi sebelumnya kapan saja.
- Merekam setiap perubahan data melalui **audit log** yang lengkap.

---

## Teknologi yang Digunakan

| Komponen | Teknologi |
|---|---|
| Framework Backend | Laravel (PHP) |
| Database | MySQL / MariaDB |
| Autentikasi | Laravel built-in Auth (session-based) |
| Export/Import Excel | Maatwebsite Laravel-Excel |
| Antrian (Queue) | Laravel Queue (sync) |
| Cache (Progress Import) | Laravel Cache |
| Frontend | Blade Templates + Chart.js |

---

## Struktur Modul

```
DBHC2/
├── app/
│   ├── Http/Controllers/
│   │   ├── Auth/LoginController.php       # Login & logout
│   │   ├── DashboardController.php        # Dashboard & KPI utama
│   │   ├── DataKaryawanController.php     # CRUD & import data karyawan
│   │   ├── FormasiController.php          # CRUD & import formasi jabatan
│   │   ├── RealisasiController.php        # CRUD realisasi program kerja
│   │   ├── DataPgsController.php          # CRUD data PGS
│   │   ├── DataPenugasanController.php    # CRUD data penugasan
│   │   ├── VersionController.php          # Snapshot & restore versi data
│   │   ├── AuditLogController.php         # Riwayat aktivitas (audit log)
│   │   ├── UserController.php             # Manajemen pengguna (admin)
│   │   ├── ProfileController.php          # Profil pengguna
│   │   └── EmployeeController.php         # Analitik organik & outsourcing
│   ├── Models/
│   │   ├── DataKaryawan.php               # Model data karyawan
│   │   ├── Formasi.php                    # Model formasi jabatan
│   │   ├── Realisasi.php                  # Model realisasi program kerja
│   │   ├── DataPgs.php                    # Model data PGS
│   │   ├── DataPenugasan.php              # Model data penugasan
│   │   ├── Version.php                    # Model versi/snapshot
│   │   ├── EmployeeHistory.php            # Model histori karyawan per versi
│   │   ├── AuditLog.php                   # Model audit log
│   │   └── User.php                       # Model pengguna
│   └── Exports/
│       ├── DataKaryawanExport.php
│       ├── FormasiExport.php
│       ├── RealisasiExport.php
│       ├── DataPgsExport.php
│       ├── DataPenugasanExport.php
│       ├── JabatanLowongExport.php
│       ├── SemuaJabatanLengkapExport.php
│       ├── VersionDataExport.php
│       └── *TemplateExport.php            # Template impor Excel
└── routes/web.php                         # Definisi semua route
```

---

## Fitur-Fitur Sistem

### 1. Autentikasi & Manajemen Pengguna

**Autentikasi:**
- Login menggunakan email dan password melalui `LoginController`.
- Sistem menggunakan middleware `auth` (wajib login) dan `can:admin` (hanya admin).
- Tidak ada registrasi publik — akun dibuat langsung oleh admin.

**Manajemen Pengguna (Admin only):**
- Admin dapat membuat, melihat, mengedit, dan menghapus akun pengguna.
- Terdapat dua **peran (role)**:
  - `admin` — akses penuh ke semua fitur termasuk CRUD dan impor.
  - `user` — akses baca (view-only) pada semua data; tidak dapat menambah, mengubah, atau menghapus data.
- Admin tidak dapat menghapus akun miliknya sendiri (proteksi diri sendiri).
- Data sensitif seperti password disimpan dalam bentuk hash.

**Profil Pengguna:**
- Setiap pengguna dapat mengubah nama, email, password, serta mengunggah foto profil.
- Tersedia pilihan avatar preset jika tidak ingin menggunakan foto sendiri.

---

### 2. Dashboard & Analitik

Dashboard adalah halaman utama setelah login, menampilkan ringkasan dan visualisasi data kepegawaian secara real-time.

**KPI Ringkas (Kartu Statistik):**

| Metrik | Keterangan |
|---|---|
| Total Karyawan | Jumlah seluruh karyawan aktif |
| Total Unit Kerja | Jumlah unit kerja yang memiliki karyawan |
| Rata-rata Usia | Dihitung dari tanggal_lahir (format dd/mm/yyyy) |
| Rata-rata Masa Kerja | Dihitung dari tmt_karyawan |
| Persentase Perempuan | Perbandingan gender |

**Grafik dan Visualisasi:**
- **Pie Chart Status:** Distribusi karyawan berdasarkan sub-status (KP, Alih Daya, dll.)
- **Bar Chart Gender:** Perbandingan organik vs outsourcing per gender
- **Bar Chart Pendidikan:** Distribusi pendidikan diakui, dipilah organik vs outsourcing, diurutkan SMP sampai S3
- **Bar Chart Sebaran Usia:** Pengelompokan usia ke dalam bin
- **Bar Chart Masa Kerja:** Pengelompokan masa kerja dalam tahun
- **Bar Chart Top 10 Unit:** Unit kerja dengan jumlah karyawan terbanyak
- **Bar Chart Fungsi Jabatan:** Top 10 fungsi jabatan
- **Tabel BOD Level:** Daftar karyawan pada level BOD-4, BOD-3, BOD-2, BOD-1
- **Tabel Jabatan Lowong:** Rekap posisi jabatan yang belum terisi per lokasi dan kelompok kelas jabatan
- **Grafik Pertumbuhan Karyawan:** Tren jumlah karyawan dari waktu ke waktu berdasarkan riwayat snapshot (organik, outsourcing, total)

---

### 3. Data Karyawan

Modul inti yang mengelola seluruh data individu karyawan.

**Data yang dicatat per karyawan:**

| Kategori | Field |
|---|---|
| Identitas | NIK, Nama, Jenis Kelamin, Tanggal Lahir, Usia |
| Jabatan | Kode Jabatan, Jabatan, Lokasi Kerja, Unit Kerja, Person Grade, Kelompok Kelas Jabatan |
| Struktur Unit | Unit Deputy/EGM, Unit Assistant Deputy, Unit Division Head, Unit Department Head |
| Status | Status Jabatan, Sub Status, Asal Instansi, Instansi |
| Karir | Jalur Karir, Jenjang Karir, Keluarga Jabatan, Sub Keluarga Jabatan, Fungsi Jabatan |
| Kepegawaian | TMT Karyawan, Masa Kerja, TMT KJ Tertinggi, No. SK, Generasi |
| Personal | Agama, Status Pernikahan, Pendidikan, Jurusan, No. KTP, Alamat, No. HP, Email |
| Lisensi | Lisensi Dimiliki, Rating, No. STKP, Masa Berlaku, Lisensi Serkom |
| Kinerja | KPI 2023, Nilai NPI 2022, Kriteria |
| Pensiun | Rencana MPP, Rencana Pensiun |

**Operasi (Admin only untuk tulis):**
- Tambah karyawan satu per satu melalui form.
- Edit dan hapus data karyawan.
- **Import Excel** dengan dua mode: Tambah (Add) dan Ganti (Replace).
- Import besar menggunakan **progress tracking real-time** via antrian (queue).
- Download template Excel untuk keperluan impor.
- **Export** seluruh data ke file Excel.

---

### 4. Formasi Jabatan

Formasi adalah **daftar posisi jabatan resmi** yang ditetapkan organisasi, beserta kuota yang diizinkan untuk diisi.

**Data Formasi:**

| Field | Keterangan |
|---|---|
| Kode Jabatan | Kode unik identifikasi jabatan |
| Lokasi Kerja | Lokasi di mana jabatan ini berada |
| Unit Kerja | Unit yang membawahi jabatan |
| Jabatan | Nama jabatan |
| Kelompok Kelas Jabatan | Tingkat/kelas jabatan (BOD-1 s/d BOD-4, dll.) |
| Grade | Grade jabatan |
| Kuota | Jumlah posisi yang resmi tersedia |

**Relasi dengan Data Karyawan:**
- Formasi dan Data Karyawan terhubung melalui `kode_jabatan`.
- Satu formasi dapat memiliki banyak karyawan (hasMany).
- Seorang karyawan terhubung ke satu formasi (belongsTo).

**Operasi (Admin only untuk tulis):**
- CRUD lengkap formasi.
- Import Tambah / Import Ganti dari file Excel.
- Export data formasi dan Export Semua Jabatan Lengkap.
- Download template Excel.

---

### 5. Jabatan Lowong

Fitur ini **menghitung secara otomatis** posisi jabatan yang belum terisi penuh.

**Logika Perhitungan:**

```
Lowong = Kuota Formasi - Jumlah Karyawan yang Menduduki Kode Jabatan Tersebut
```

- Tampilan berupa tabel rekap per **lokasi kerja** dan **kelompok kelas jabatan**.
- Klik pada cell tabel akan membuka **modal detail** yang menampilkan daftar jabatan lowong yang spesifik (kode jabatan, nama jabatan, unit, formasi, terisi, sisa lowong).
- **Export** daftar jabatan lowong ke Excel tersedia untuk semua pengguna login.
- Admin dapat export detail jabatan lowong per lokasi+level dalam format CSV.

---

### 6. Realisasi Program Kerja

Modul pencatatan capaian/realisasi program kerja SDM per tahun.

**Data Realisasi:**

| Field | Keterangan |
|---|---|
| Program Kerja | Nama program |
| Tahun | Tahun anggaran |
| RKAP | Target/anggaran yang ditetapkan |
| Realisasi Jan-Mar | Capaian triwulan 1 |
| Realisasi Jan-Jun | Capaian semester 1 |
| Realisasi Jul-Sep | Capaian triwulan 3 |
| Realisasi Jul-Des | Capaian semester 2 |

**Perhitungan Otomatis (Accessor):**
- **ACH Semester 1** = Realisasi Jan-Jun / RKAP
- **ACH Semester 2** = Realisasi Jul-Des / RKAP
- **ACH Tahun** = (Realisasi S1 + Realisasi S2) / RKAP

**Operasi:**
- CRUD lengkap (Admin only untuk tulis).
- Import dari Excel dan Export ke Excel.

---

### 7. Data PGS (Pejabat Ganti Sementara)

Mencatat karyawan yang sedang menjabat sementara pada posisi yang bukan jabatan definitifnya.

**Data PGS:**

| Field | Keterangan |
|---|---|
| NIK & Nama | Identitas karyawan |
| Jabatan Definitif | Jabatan asli karyawan |
| Jabatan PGS | Jabatan yang sedang diemban sementara |
| Lokasi Unit Kerja | Unit tempat PGS berlangsung |
| Tanggal PGS | Tanggal mulai PGS |
| Tanggal Selesai PGS | Tanggal berakhir PGS (boleh kosong jika masih berlangsung) |

**Fitur Cerdas (Accessor Otomatis):**
- **Durasi PGS:** Dihitung otomatis dalam bulan dan hari.
- **Sisa Hari:** "X hari lagi", "Hari ini berakhir", atau "Lewat X hari".
- **Is Warning:** Peringatan jika PGS akan berakhir dalam <= 15 hari.
- **Is Overdue:** Ditandai jika PGS sudah melewati tanggal selesai.

**Operasi:** CRUD lengkap, Import Tambah/Ganti, Export ke Excel (Admin only untuk tulis).

---

### 8. Data Penugasan

Mencatat karyawan yang sedang ditugaskan di luar unit/lokasi definitifnya.

**Data Penugasan:**

| Field | Keterangan |
|---|---|
| NIK, Nama, KJ | Identitas dan kelas jabatan karyawan |
| Jabatan Definitif | Jabatan asli |
| Unit & Lokasi Definitif | Unit/lokasi asal karyawan |
| Unit & Lokasi Penugasan | Unit/lokasi tujuan penugasan |
| Nomor Sprint | Nomor sprint/proyek |
| Tanggal Mulai & Selesai | Durasi penugasan |
| PIC | Penanggung jawab penugasan |
| Keterangan | Catatan tambahan |

**Fitur Cerdas (Accessor Otomatis):**
- **Durasi Penugasan:** Dihitung otomatis, termasuk status "ongoing".
- **Sisa Hari, Is Warning, Is Overdue:** Sama seperti modul Data PGS.

**Operasi:** CRUD lengkap, Import Tambah/Ganti, Export ke Excel (Admin only untuk tulis).

---

### 9. Version Control (Snapshot Data)

Sistem **versi/snapshot** yang memungkinkan pembuatan checkpoint data karyawan dan pemulihan ke kondisi sebelumnya.

**Cara Kerja:**
1. **Buat Snapshot:** Menyalin seluruh data karyawan ke tabel `employee_history` dengan `version_id`. Menggunakan `chunkById(500)` untuk efisiensi memori pada data besar.
2. **Lihat Riwayat:** Daftar semua snapshot dengan filter per tahun/bulan/tanggal dan jumlah data per snapshot.
3. **Restore:** Data `data_karyawan` dihapus total lalu diisi ulang dari snapshot yang dipilih dalam satu transaksi database yang aman.
4. **Download:** Setiap snapshot dapat diunduh sebagai file Excel.
5. **Hapus:** Snapshot lama dapat dihapus untuk menghemat ruang.

**Dashboard Integration:** Grafik pertumbuhan karyawan menampilkan tren dari semua snapshot (Total, Organik, Outsourcing).

---

### 10. Audit Log

Sistem pencatatan otomatis atas setiap perubahan data di sistem.

**Jenis Aksi yang Dicatat:**

| Aksi | Deskripsi |
|---|---|
| `created` | Data baru ditambahkan |
| `updated` | Data diubah |
| `deleted` | Data dihapus |
| `restored` | Data dipulihkan |
| `bulk_created` | Import data (mode tambah) |
| `bulk_replaced` | Import data (mode ganti) |
| `version_restored` | Restore dari snapshot versi |

**Informasi yang Tersimpan:**
- Siapa yang melakukan (user_id, nama, email)
- Objek data mana yang diubah (model_type, model_id, model_identifier)
- Nilai sebelum dan sesudah perubahan (old_values, new_values, changes)
- IP address dan user agent browser

**Fitur Filter (Admin only):**
- Filter berdasarkan modul, jenis aksi, pengguna, dan rentang tanggal.
- Pencarian berdasarkan nama/email/identifier data.
- Tampilan perubahan field dalam bahasa Indonesia dengan masking untuk data sensitif.

---

### 11. Profil Pengguna

Setiap pengguna dapat mengelola profil pribadinya:
- Mengubah nama dan email.
- Mengubah password (dengan konfirmasi).
- Mengunggah foto profil dari komputer.
- Memilih avatar preset yang tersedia.
- Menghapus foto profil.

---

## Alur Bisnis

```
[Login]
   |
   v
[Dashboard] ---- Melihat KPI & Grafik Kepegawaian
   |
   |--- [Data Karyawan]
   |         |-- Lihat, tambah, edit, hapus (Admin)
   |         |-- Import Excel (Admin) --- Progress Real-time
   |         +-- Export Excel
   |
   |--- [Formasi Jabatan]
   |         |-- Definisi posisi & kuota jabatan (Admin)
   |         +-- Jabatan Lowong = Kuota - Karyawan Aktif
   |
   |--- [Jabatan Lowong]
   |         +-- Rekap otomatis per Lokasi x Kelas Jabatan
   |
   |--- [Realisasi Program Kerja]
   |         +-- Pencatatan & perhitungan ACH per semester
   |
   |--- [Data PGS]
   |         +-- Monitoring PGS dengan status & sisa hari
   |
   |--- [Data Penugasan]
   |         +-- Monitoring penugasan dengan status & sisa hari
   |
   |--- [Version Control]
   |         |-- Buat Snapshot -----> Simpan ke employee_history
   |         |-- Restore ----------> Kembalikan data_karyawan dari snapshot
   |         +-- Download Snapshot sebagai Excel
   |
   |--- [Audit Log] (Admin)
   |         +-- Riwayat semua perubahan data dengan detail lengkap
   |
   +--- [Manajemen User] (Admin)
             +-- CRUD akun pengguna & pengaturan role
```

---

## Sistem Kontrol Akses (Peran)

| Fitur | user (viewer) | admin |
|---|:---:|:---:|
| Login & Profil | Ya | Ya |
| Lihat Dashboard | Ya | Ya |
| Lihat Data Karyawan | Ya | Ya |
| Export Data Karyawan | Ya | Ya |
| Tambah / Edit / Hapus Karyawan | Tidak | Ya |
| Import Data Karyawan | Tidak | Ya |
| Lihat Formasi | Ya | Ya |
| Export Formasi | Ya | Ya |
| CRUD & Import Formasi | Tidak | Ya |
| Lihat Jabatan Lowong | Ya | Ya |
| Export Jabatan Lowong | Ya | Ya |
| Lihat Realisasi | Ya | Ya |
| Export Realisasi | Ya | Ya |
| CRUD & Import Realisasi | Tidak | Ya |
| Lihat Data PGS | Ya | Ya |
| Export Data PGS | Ya | Ya |
| CRUD & Import PGS | Tidak | Ya |
| Lihat Data Penugasan | Ya | Ya |
| Export Data Penugasan | Ya | Ya |
| CRUD & Import Penugasan | Tidak | Ya |
| Version Control (Snapshot) | Ya | Ya |
| Audit Log | Tidak | Ya |
| Manajemen User | Tidak | Ya |

---

## Struktur Data Utama

### Relasi Antar Tabel

```
users
  +-- audit_logs (user_id)

formasi (kode_jabatan)
  +-- data_karyawan (kode_jabatan) -- many karyawan per formasi

versions (id)
  +-- employee_history (version_id) -- snapshot per versi

data_pgs           -- berdiri sendiri (NIK sebagai referensi)
data_penugasan     -- berdiri sendiri (NIK sebagai referensi)
realisasi          -- berdiri sendiri
```

### Model dan Trait

Semua model data utama (`DataKaryawan`, `Formasi`, `Realisasi`, `DataPgs`, `DataPenugasan`, `User`, `Version`) menggunakan **trait `HasAuditLog`** yang secara otomatis mencatat setiap operasi create, update, dan delete ke tabel `audit_logs`.

---

## Ekspor & Impor Excel

### File Export yang Tersedia

| Export | Controller | Keterangan |
|---|---|---|
| data_karyawan_*.xlsx | DataKaryawanController | Seluruh data karyawan |
| template_data_karyawan.xlsx | DataKaryawanController | Template impor karyawan |
| formasi_*.xlsx | FormasiController | Seluruh data formasi |
| template_formasi.xlsx | FormasiController | Template impor formasi |
| semua_jabatan_lengkap_*.xlsx | FormasiController | Formasi + info pengisian |
| Jabatan_Lowong_*.xlsx | DashboardController | Daftar posisi lowong |
| realisasi_*.xlsx | RealisasiController | Data realisasi |
| template_realisasi.xlsx | RealisasiController | Template impor realisasi |
| data_pgs_*.xlsx | DataPgsController | Data PGS |
| template_data_pgs.xlsx | DataPgsController | Template impor PGS |
| data_penugasan_*.xlsx | DataPenugasanController | Data penugasan |
| template_penugasan.xlsx | DataPenugasanController | Template impor penugasan |
| Data_Karyawan_[versi]_*.xlsx | VersionController | Snapshot versi tertentu |

### Mode Import

Semua modul yang mendukung import menyediakan dua mode:
- **Import Tambah (Add):** Data baru ditambahkan di atas data yang sudah ada. Data lama tidak dihapus.
- **Import Ganti (Replace):** Data lama dihapus seluruhnya, diganti dengan data dari file Excel.

Import data karyawan mendukung **progress tracking real-time** menggunakan session ID dan Laravel Cache. Pengguna dapat memantau persentase progres import tanpa harus menunggu halaman reload.
#   D B H C D E P  
 