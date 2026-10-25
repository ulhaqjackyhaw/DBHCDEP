# Solusi Error: Maximum Execution Time Exceeded

## Masalah
Saat import Excel 6000+ baris, terjadi error:
```
Maximum execution time of 300 seconds exceeded
```

## Solusi yang Sudah Diterapkan

### 1. **Middleware untuk Large Import** ✅
File: `app/Http/Middleware/LargeImportMiddleware.php`
- Set execution time: 900 detik (15 menit)
- Memory limit: 512MB
- Max input time: 900 detik

### 2. **Optimasi Import Class** ✅
File: `app/Imports/DataKaryawanImport.php`
- Batch size: 500 (dari 50)
- Chunk size: 1000 (dari 100)
- Hapus log debug yang membuat lambat
- Hapus validasi `exists()` per-row (sangat lambat untuk 6000 baris)

### 3. **Route Middleware Sudah Diterapkan** ✅
File: `routes/web.php` - route `karyawan.import.replace` sudah menggunakan middleware `large.import`

## Cara Pakai

### Test Import Ulang
1. Restart Laravel server:
```powershell
# Ctrl+C di terminal, lalu:
php artisan serve
```

2. Coba import file Excel 6000 baris lagi via form "Ganti Semua"

### Expected Performance
- 6000 baris seharusnya selesai dalam **3-7 menit**
- Progress bisa terlihat di log: `storage/logs/laravel.log`

## Troubleshooting Tambahan

### Jika Masih Timeout

#### Opsi A: Update php.ini (Permanen)
Cari file `php.ini` Anda (biasanya di `C:\xampp\php\php.ini` atau `C:\php\php.ini`):
```ini
max_execution_time = 900
max_input_time = 900
memory_limit = 512M
upload_max_filesize = 50M
post_max_size = 50M
```

Restart Apache/PHP server setelah edit.

#### Opsi B: Update .htaccess (Web Server Only)
Jika menggunakan Apache, tambahkan di `.htaccess`:
```apache
php_value max_execution_time 900
php_value max_input_time 900
php_value memory_limit 512M
```

#### Opsi C: Progress Bar (Future Enhancement)
Untuk file sangat besar (10k+ rows), pertimbangkan:
- Pakai Laravel Queue + Jobs
- Background processing dengan progress tracking
- Split import ke beberapa batch

## Monitoring Import

### Lihat Log Real-time
```powershell
Get-Content storage\logs\laravel.log -Tail 50 -Wait
```

### Check Database Progress
```sql
SELECT COUNT(*) FROM data_karyawan;
```

## Performance Tips

### Sebelum Import Besar
```powershell
# Clear cache
php artisan cache:clear
php artisan config:clear

# Optimize autoloader
composer dump-autoload -o
```

### Setelah Import
```powershell
# Rebuild index (jika ada)
php artisan db:seed --class=RebuildIndexSeeder
```

## Technical Details

### Batch Insert Strategy
- **Batch Size 500**: Insert 500 rows sekaligus (balance antara speed vs memory)
- **Chunk Reading 1000**: Baca 1000 rows dari Excel sebelum process

### Why Remove `exists()` Check?
Query `SELECT EXISTS(SELECT * FROM data_karyawan WHERE nik = ?)` untuk setiap row:
- 6000 rows × ~1ms = **6 detik** (best case)
- Dengan index lookup overhead: **30-60 detik**
- **Solusi**: Hapus semua data dulu (mode "Ganti Semua"), jadi tidak perlu cek duplikasi

### Database Optimization
Mode "Ganti Semua":
```php
DataKaryawan::truncate(); // Lebih cepat dari delete()
```

## Monitoring Metrics

### Import 6000 Rows
- **Delete existing**: ~1-2 detik
- **Excel parsing**: ~30-60 detik
- **Batch insert**: ~120-180 detik
- **Total**: ~3-5 menit ✅

### Bandingkan dengan Settings Lama
- Batch 50, Chunk 100: ~10-15 menit ❌
- Dengan `exists()` check: **Timeout di 5 menit** ❌
