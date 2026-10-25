# Update Struktur Formasi - Dokumentasi

## Perubahan Struktur Database

Tabel `formasi` telah diperbarui dengan menambahkan kolom-kolom hierarchy unit dan mengubah nama beberapa kolom:

### Kolom Baru:
1. `unit_deputy_egm` - Unit Deputy EGM (nullable)
2. `unit_assistant_deputy` - Unit Assistant Deputy (nullable)
3. `unit_division_head` - Unit Division Head (nullable)
4. `unit_department_head` - Unit Department Head (nullable)

### Kolom yang Diubah Namanya:
- `lokasi` → `lokasi_kerja`
- `unit` → `unit_kerja`

### Struktur Lengkap Kolom:
1. KODE JABATAN (`kode_jabatan`)
2. UNIT DEPUTY EGM (`unit_deputy_egm`)
3. UNIT ASSISTANT DEPUTY (`unit_assistant_deputy`)
4. UNIT DIVISION HEAD (`unit_division_head`)
5. UNIT DEPARTMENT HEAD (`unit_department_head`)
6. LOKASI KERJA (`lokasi_kerja`)
7. UNIT KERJA (`unit_kerja`)
8. JABATAN (`jabatan`)
9. KELOMPOK KELAS JABATAN (`kelompok_kelas_jabatan`)
10. GRADE (`grade`)
11. KUOTA (`kuota`)

## File yang Telah Diupdate:

### 1. Database Migration
- **File**: `database/migrations/2025_10_28_110050_add_unit_hierarchy_columns_to_formasi_table.php`
- **Status**: ✅ Sudah dijalankan (Batch 2)

### 2. Model
- **File**: `app/Models/Formasi.php`
- **Perubahan**: 
  - Update `$fillable` array dengan kolom baru
  - Update scope methods (`scopeByLokasi`, `scopeByUnit`)

### 3. Import
- **File**: `app/Imports/FormasiImport.php`
- **Perubahan**: 
  - Support mapping untuk semua kolom baru
  - Support berbagai format header (uppercase, lowercase, dengan/tanpa underscore)

### 4. Export
- **File**: `app/Exports/FormasiExport.php`
- **Perubahan**: 
  - Update headings dengan 11 kolom
  - Update mapping data
  - Update column widths
  - Update sorting (orderBy)

- **File**: `app/Exports/FormasiTemplateExport.php`
- **Perubahan**: 
  - Update headings dengan 11 kolom
  - Update contoh data template
  - Update column widths
  - Update styling ranges

### 5. Controller
- **File**: `app/Http/Controllers/FormasiController.php`
- **Perubahan**: 
  - Update validation rules di method `store()` dan `update()`
  - Update unique check menggunakan `lokasi_kerja` dan `unit_kerja`

### 6. Views
- **File**: `resources/views/formasi/index.blade.php`
- **Perubahan**: 
  - Update table headers (11 kolom)
  - Update data binding di Alpine.js
  - Update colspan untuk empty state

- **File**: `resources/views/formasi/create.blade.php`
- **Perubahan**: 
  - Tambah input fields untuk 4 kolom hierarchy unit
  - Update labels dari "Lokasi" menjadi "Lokasi Kerja"
  - Update labels dari "Unit" menjadi "Unit Kerja"

- **File**: `resources/views/formasi/edit.blade.php`
- **Perubahan**: 
  - Tambah input fields untuk 4 kolom hierarchy unit
  - Update labels dan field bindings

- **File**: `resources/views/formasi/show.blade.php`
- **Perubahan**: 
  - Tambah display untuk 4 kolom hierarchy unit
  - Update labels sesuai kolom baru

## Cara Penggunaan:

### Import Data
Template Excel yang baru sekarang harus mengikuti format 11 kolom:
1. KODE JABATAN (wajib)
2. UNIT DEPUTY EGM (opsional)
3. UNIT ASSISTANT DEPUTY (opsional)
4. UNIT DIVISION HEAD (opsional)
5. UNIT DEPARTMENT HEAD (opsional)
6. LOKASI KERJA (wajib)
7. UNIT KERJA (wajib)
8. JABATAN (wajib)
9. KELOMPOK KELAS JABATAN (wajib)
10. GRADE (wajib)
11. KUOTA (wajib)

### Download Template
Gunakan menu "Download Template" untuk mendapatkan template Excel yang sudah disesuaikan dengan struktur baru.

### Backward Compatibility
Import masih support header lama (`lokasi`, `unit`) untuk backward compatibility, namun akan disimpan ke kolom baru (`lokasi_kerja`, `unit_kerja`).

## Catatan Penting:

1. **Kolom Hierarchy Unit** (`unit_deputy_egm`, `unit_assistant_deputy`, `unit_division_head`, `unit_department_head`) bersifat **nullable/opsional**
2. **Unique Constraint** tetap menggunakan kombinasi: `kode_jabatan` + `lokasi_kerja` + `unit_kerja`
3. Data lama akan tetap berfungsi setelah migration (lokasi → lokasi_kerja, unit → unit_kerja)
4. Export akan menghasilkan file dengan 11 kolom sesuai struktur baru

## Testing:

Untuk testing, silakan:
1. Download template baru dari menu "Download Template"
2. Isi data sesuai format
3. Upload menggunakan fitur Import Add atau Import Replace
4. Verifikasi data muncul dengan benar di tabel

## Rollback (jika diperlukan):

Jika terjadi masalah dan ingin rollback:
```bash
php artisan migrate:rollback --step=1
```

Ini akan menghapus kolom baru dan mengembalikan nama kolom lama.
