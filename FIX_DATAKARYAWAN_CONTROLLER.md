# Fix DataKaryawan Controller - Formasi Column References

## Masalah
Saat membuat atau mengedit karyawan dengan memilih formasi dari dropdown, data `lokasi_kerja` dan `unit_kerja` tidak tersimpan dengan benar karena controller masih menggunakan nama kolom lama.

## Error
```php
// ❌ SALAH - Menggunakan nama kolom lama
$data['lokasi_kerja'] = $formasi->lokasi;
$data['unit_kerja'] = $formasi->unit;
```

## Perbaikan
**File:** `app/Http/Controllers/DataKaryawanController.php`

### 1. Method `store()` (Baris ~119-120)
```php
// ✅ BENAR - Menggunakan nama kolom baru
$data['lokasi_kerja'] = $formasi->lokasi_kerja;
$data['unit_kerja'] = $formasi->unit_kerja;
```

### 2. Method `update()` (Baris ~313-314)
```php
// ✅ BENAR - Menggunakan nama kolom baru
$data['lokasi_kerja'] = $formasi->lokasi_kerja;
$data['unit_kerja'] = $formasi->unit_kerja;
```

## Dampak
- ✅ Data `lokasi_kerja` sekarang tersimpan dengan benar saat create/edit karyawan
- ✅ Data `unit_kerja` sekarang tersimpan dengan benar saat create/edit karyawan
- ✅ Konsisten dengan struktur tabel formasi yang baru (migration Batch 2)

## Testing
1. Buka halaman Tambah Karyawan (`/karyawan/create`)
2. Pilih formasi dari dropdown
3. Simpan data
4. Periksa data karyawan di database:
   ```sql
   SELECT nik, nama, lokasi_kerja, unit_kerja FROM data_karyawan ORDER BY id DESC LIMIT 1;
   ```
5. Pastikan `lokasi_kerja` dan `unit_kerja` terisi sesuai formasi yang dipilih

## Verifikasi
```bash
php -l app/Http/Controllers/DataKaryawanController.php
# Output: No syntax errors detected
```

## Catatan
Perubahan ini melengkapi fix sebelumnya di:
- ✅ DashboardController (3 lokasi)
- ✅ JabatanLowongExport 
- ✅ Karyawan create.blade.php (dropdown + JavaScript)
- ✅ Karyawan edit.blade.php (dropdown + JavaScript)
- ✅ DataKaryawanController store() & update()

**Total lokasi yang diperbaiki: 7 files**
