# Fix: Update Referensi Kolom Database di Dashboard

## Masalah yang Diperbaiki
Error SQL karena DashboardController dan JabatanLowongExport masih menggunakan nama kolom lama dari tabel `formasi`:
- `lokasi` (seharusnya `lokasi_kerja`)
- `unit` (seharusnya `unit_kerja`)

**Error Message:**
```
SQLSTATE[42S22]: Column not found: 1054 Unknown column 'lokasi' in 'field list'
```

## File yang Diperbaiki

### 1. DashboardController.php
**Lokasi:** `app/Http/Controllers/DashboardController.php`

#### Perubahan pada method `index()`:
- Baris query `Formasi::select()` diupdate:
  - `'lokasi'` → `'lokasi_kerja'`
  - `'unit'` → `'unit_kerja'`
- Key untuk `keyBy()` diupdate untuk menggunakan kolom baru
- Referensi ke `$formasi->lokasi` dan `$formasi->unit` diubah menjadi `$formasi->lokasi_kerja` dan `$formasi->unit_kerja`

#### Perubahan pada method `getJabatanLowongDetail()`:
- Query `Formasi::select()` diupdate:
  - `'lokasi'` → `'lokasi_kerja'`
  - `'unit'` → `'unit_kerja'`
- Where clause diupdate: `->where('lokasi_kerja', $lokasi)`
- Key untuk `keyBy()` dan referensi objek diupdate untuk menggunakan kolom baru
- Array result diupdate untuk menggunakan `$formasi->lokasi_kerja` dan `$formasi->unit_kerja`

### 2. JabatanLowongExport.php
**Lokasi:** `app/Exports/JabatanLowongExport.php`

#### Perubahan pada method `collection()`:
- Query DataKaryawan diupdate:
  - `->where('lokasi', $item->lokasi)` → `->where('lokasi_kerja', $item->lokasi_kerja)`
  - `->where('unit', $item->unit)` → `->where('unit_kerja', $item->unit_kerja)`
- Array push diupdate:
  - `'lokasi' => $item->lokasi` → `'lokasi' => $item->lokasi_kerja`
  - `'unit' => $item->unit` → `'unit' => $item->unit_kerja`

## Status Perbaikan

✅ **DashboardController.php** - Updated
✅ **JabatanLowongExport.php** - Updated
✅ **Syntax Check** - Passed (no errors)

## Testing

Untuk memverifikasi perbaikan:
1. Akses halaman dashboard: `/dashboard`
2. Periksa bagian "Jabatan Lowong" tampil dengan benar
3. Klik detail pada salah satu item lowong
4. Export data jabatan lowong ke Excel
5. Semua fitur harus berfungsi tanpa error SQL

## Catatan

Perubahan ini merupakan bagian dari update struktur database formasi yang menambahkan kolom hierarchy unit dan mengubah nama kolom:
- `lokasi` → `lokasi_kerja`
- `unit` → `unit_kerja`

Semua referensi ke kolom lama sudah diupdate untuk menggunakan nama kolom baru.
