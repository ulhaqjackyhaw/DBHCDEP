# Fix: Session Tidak Ditemukan pada Import Data

## Masalah
Import data karyawan selalu menampilkan error "Session tidak ditemukan" padahal sebelumnya berjalan cepat.

## Penyebab
1. **Queue tidak berjalan** - Job menggunakan `ShouldQueue` tetapi queue worker tidak aktif
2. **Cache driver tidak konsisten** - Menggunakan database cache yang memerlukan setup tambahan
3. **Race condition** - Progress check terjadi sebelum job sempat menulis ke cache
4. **Tidak ada inisialisasi progress** - Cache hanya dibuat dari dalam job, bukan saat dispatch

## Solusi yang Diterapkan

### 1. Inisialisasi Progress Sebelum Dispatch Job
**File**: `app/Http/Controllers/DataKaryawanController.php`

```php
// Initialize progress immediately in cache
\Cache::put("import_progress_{$sessionId}", [
    'progress' => 0,
    'status' => 'initializing',
    'message' => 'Menginisialisasi proses import...'
], 3600);
```

**Alasan**: Memastikan session sudah ada di cache sebelum frontend mulai polling.

### 2. Gunakan Sync Queue untuk Development
**File**: `app/Http/Controllers/DataKaryawanController.php`

```php
// Dispatch job on sync queue for immediate processing
\App\Jobs\ImportDataKaryawanJob::dispatch($filePath, $sessionId, 'add')
    ->onQueue('sync');
```

**Alasan**: 
- Tidak perlu menjalankan queue worker terpisah
- Job langsung dieksekusi secara synchronous
- Lebih mudah untuk debugging
- Cocok untuk development environment

### 3. Ubah Cache Driver ke File
**File**: `.env`

```env
QUEUE_CONNECTION=sync
CACHE_STORE=file
```

**Alasan**:
- File cache lebih reliable untuk development
- Tidak perlu setup database cache table
- Lebih mudah di-debug (bisa lihat di `storage/framework/cache`)

### 4. Improve Progress Check dengan Fallback
**File**: `app/Http/Controllers/DataKaryawanController.php`

```php
public function checkImportProgress($sessionId)
{
    try {
        $progress = \Cache::get("import_progress_{$sessionId}");
        
        if (!$progress) {
            // Check if session just started (less than 5 seconds ago)
            if (strtotime('now') - hexdec(substr($sessionId, 7, 8)) < 5) {
                return response()->json([
                    'progress' => 0,
                    'status' => 'initializing',
                    'message' => 'Menginisialisasi...'
                ]);
            }
            
            return response()->json([
                'progress' => 0,
                'status' => 'not_found',
                'message' => 'Session tidak ditemukan atau sudah expired'
            ]);
        }

        return response()->json($progress);
    } catch (\Exception $e) {
        \Log::error('Check Progress Error: ' . $e->getMessage());
        return response()->json([
            'progress' => 0,
            'status' => 'error',
            'message' => 'Error: ' . $e->getMessage()
        ]);
    }
}
```

**Alasan**: 
- Menangani case session baru dibuat (< 5 detik)
- Error handling yang lebih baik
- Logging untuk debugging

### 5. Frontend Error Handling
**File**: `resources/views/karyawan/import-progress.blade.php`

Ditambahkan handling untuk:
- `status === 'not_found'` - Session tidak ditemukan
- `catch(error)` - Network error atau server down

## Cara Menggunakan

### Development (Sekarang)
```bash
# Cukup jalankan server
php artisan serve

# Import akan langsung diproses (sync)
```

### Production (Nanti)
```bash
# Ubah di .env
QUEUE_CONNECTION=database
CACHE_STORE=database

# Jalankan queue worker
php artisan queue:work --tries=3
```

## Testing

1. Upload file Excel melalui menu Import
2. Halaman progress akan muncul dengan spinner
3. Progress bar akan update setiap 500ms
4. Seharusnya tidak muncul "Session tidak ditemukan"
5. Setelah selesai, auto redirect ke halaman data karyawan

## Monitoring

### Cek Cache
```bash
# Lihat semua cache
php artisan tinker
>>> Cache::get('import_progress_[SESSION_ID]');
```

### Cek Log
```bash
# Lihat error log
cat storage/logs/laravel.log
```

### Cek File Upload
```bash
# Lihat file temporary
ls storage/app/temp/
```

## Troubleshooting

### Masih Muncul "Session Tidak Ditemukan"
1. Clear cache: `php artisan cache:clear`
2. Periksa permission folder: `storage/framework/cache` harus writable
3. Pastikan `.env` sudah di-reload: restart server

### Progress Stuck di 0%
1. Cek log: `storage/logs/laravel.log`
2. Periksa file Excel: pastikan format sesuai template
3. Cek memory PHP: tambah `memory_limit` di `php.ini`

### Import Lambat
1. Untuk data besar (>1000 rows), pertimbangkan:
   - Gunakan queue database
   - Jalankan queue worker
   - Tingkatkan batch size di Import class

## Perubahan File
- ✅ `app/Http/Controllers/DataKaryawanController.php`
- ✅ `resources/views/karyawan/import-progress.blade.php`
- ✅ `.env`

## Referensi
- [Laravel Queue Documentation](https://laravel.com/docs/12.x/queues)
- [Laravel Cache Documentation](https://laravel.com/docs/12.x/cache)
- [Maatwebsite Excel](https://docs.laravel-excel.com/)
