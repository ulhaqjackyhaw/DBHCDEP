# Real-Time Import Progress Feature

## Overview
Fitur ini menambahkan progress bar real-time untuk proses import data karyawan yang menampilkan persentase loading secara real-time.

## Fitur Utama
- ✅ Progress bar real-time dengan persentase yang update otomatis
- ✅ Background processing menggunakan Queue Jobs
- ✅ Halaman progress terpisah dengan animasi
- ✅ Auto-redirect setelah import selesai
- ✅ Error handling yang lebih baik

## Cara Menggunakan

### 1. Jalankan Queue Worker
Sebelum melakukan import, pastikan queue worker sudah berjalan:

**Windows:**
```bash
run_queue.bat
```

Atau manual:
```bash
php artisan queue:work --tries=3 --timeout=600
```

**Catatan:** Biarkan window terminal ini tetap terbuka selama aplikasi digunakan.

### 2. Lakukan Import Data
1. Login ke aplikasi sebagai admin
2. Pergi ke menu "Data Karyawan"
3. Pilih file Excel
4. Klik tombol "Tambah" atau "Ganti Semua"
5. Anda akan diarahkan ke halaman progress
6. Progress bar akan update secara real-time menampilkan:
   - Persentase (0-100%)
   - Status saat ini (Menghapus data lama, Membaca file, Memproses data)
   - Jumlah baris yang telah diproses
7. Setelah selesai, halaman akan auto-redirect ke daftar data karyawan dalam 3 detik

## Teknologi yang Digunakan

### Backend
- **Laravel Queue Jobs**: Untuk background processing
- **Cache**: Untuk menyimpan progress state
- **Job Dispatching**: Async processing

### Frontend
- **AJAX Polling**: Cek progress setiap 500ms
- **Bootstrap Progress Bar**: UI komponen
- **Real-time Updates**: JavaScript interval

## Struktur File

### Baru Ditambahkan
```
app/
├── Jobs/
│   └── ImportDataKaryawanJob.php         # Job untuk proses import
resources/
└── views/
    └── karyawan/
        └── import-progress.blade.php     # Halaman progress
run_queue.bat                             # Script untuk jalankan queue worker
```

### File yang Dimodifikasi
```
app/
├── Http/Controllers/
│   └── DataKaryawanController.php        # Tambah method checkImportProgress
├── Imports/
│   └── DataKaryawanImport.php            # Tambah progress tracking
routes/
└── web.php                                # Tambah route untuk check progress
```

## Flow Process

```
User Upload File
       ↓
Generate Session ID
       ↓
Save File to temp/
       ↓
Dispatch Job to Queue
       ↓
Redirect to Progress Page
       ↓
[Progress Page]
       ↓
Check Progress via AJAX (500ms interval)
       ↓
Update Progress Bar & Message
       ↓
[Job Processing in Background]
- Initialize (0-10%)
- Delete old data if replace mode (10-20%)
- Read Excel file (20-25%)
- Process rows with batch (25-95%)
  - Update cache every 50 rows
- Complete (95-100%)
       ↓
Auto Redirect to Data List
```

## Progress Stages

| Stage | Progress | Description |
|-------|----------|-------------|
| Initialize | 0% | Memulai import |
| Delete (if replace) | 10% | Menghapus data lama |
| Read File | 20% | Membaca file Excel |
| Processing | 25-95% | Memproses data baris per baris |
| Complete | 100% | Import selesai |

## Configuration

### Queue Configuration
File: `config/queue.php`
```php
'default' => env('QUEUE_CONNECTION', 'database'),
```

### Cache Duration
Progress disimpan di cache selama 1 jam (3600 detik).

## Troubleshooting

### Progress Tidak Update
**Masalah:** Progress bar stuck di 0%
**Solusi:** 
- Pastikan queue worker berjalan: `php artisan queue:work`
- Cek log: `storage/logs/laravel.log`
- Restart queue worker: Ctrl+C lalu jalankan ulang

### Import Tidak Jalan
**Masalah:** File terupload tapi tidak diproses
**Solusi:**
- Jalankan queue worker manual: `php artisan queue:work`
- Cek jobs table: Pastikan job masuk database
- Cek storage/app/temp/ : Pastikan file tersimpan

### Error "Session Not Found"
**Masalah:** Progress menampilkan "Session tidak ditemukan"
**Solusi:**
- Clear cache: `php artisan cache:clear`
- Restart queue worker

### Browser Timeout
**Masalah:** Halaman progress timeout setelah beberapa menit
**Solusi:**
- Sudah diatasi dengan background processing
- Jika masih terjadi, refresh halaman

## Performance

### Optimizations Applied
- **Batch Insert**: 500 rows per batch
- **Chunk Reading**: 1000 rows per chunk
- **Progress Update**: Setiap 50 rows (mengurangi write ke cache)
- **Background Processing**: Tidak memblokir browser
- **Cache**: Menggunakan cache untuk state management

### Expected Performance
- **Small files** (< 1000 rows): ~5-10 detik
- **Medium files** (1000-5000 rows): ~15-30 detik
- **Large files** (5000-10000 rows): ~30-60 detik

## Development Notes

### For Developers

#### Menambah Stage Progress Baru
Edit `app/Jobs/ImportDataKaryawanJob.php`:
```php
Cache::put("import_progress_{$this->sessionId}", [
    'progress' => 50,  // Persentase
    'status' => 'processing',
    'message' => 'Your custom message...'
], 3600);
```

#### Mengubah Interval Check
Edit `resources/views/karyawan/import-progress.blade.php`:
```javascript
checkInterval = setInterval(updateProgress, 500); // Ubah 500 ke nilai lain (ms)
```

#### Mengubah Auto Redirect Delay
Edit `resources/views/karyawan/import-progress.blade.php`:
```javascript
setTimeout(() => {
    window.location.href = "...";
}, 3000); // Ubah 3000 ke nilai lain (ms)
```

## Production Deployment

### Menggunakan Supervisor (Recommended)
Untuk production, gunakan Supervisor untuk memastikan queue worker selalu berjalan:

**Install Supervisor:**
```bash
sudo apt-get install supervisor
```

**Config File:** `/etc/supervisor/conf.d/laravel-worker.conf`
```ini
[program:laravel-worker]
process_name=%(program_name)s_%(process_num)02d
command=php /path/to/artisan queue:work --sleep=3 --tries=3 --timeout=600
autostart=true
autorestart=true
user=www-data
numprocs=2
redirect_stderr=true
stdout_logfile=/path/to/storage/logs/worker.log
```

**Start Supervisor:**
```bash
sudo supervisorctl reread
sudo supervisorctl update
sudo supervisorctl start laravel-worker:*
```

### Alternative: Cron Job
Jika tidak bisa menggunakan Supervisor, tambahkan ke crontab:
```bash
* * * * * cd /path/to/project && php artisan schedule:run >> /dev/null 2>&1
```

Dan di `app/Console/Kernel.php`:
```php
protected function schedule(Schedule $schedule)
{
    $schedule->command('queue:work --stop-when-empty')
             ->everyMinute()
             ->withoutOverlapping();
}
```

## API Endpoints

### Check Import Progress
```
GET /data-karyawan/import-progress/{sessionId}

Response:
{
    "progress": 75,
    "status": "processing",
    "message": "Memproses data: 3000 baris..."
}
```

**Status Values:**
- `processing`: Import sedang berjalan
- `completed`: Import berhasil
- `error`: Terjadi error
- `not_found`: Session tidak ditemukan

## Known Limitations

1. **Single Worker**: Hanya support 1 import job per waktu untuk menghindari race condition
2. **Cache Dependency**: Progress state bergantung pada cache
3. **Session Expiry**: Session progress expire setelah 1 jam
4. **Browser Support**: Memerlukan JavaScript enabled

## Future Enhancements

- [ ] Support multiple concurrent imports
- [ ] WebSocket untuk real-time update (menggantikan polling)
- [ ] Progress history/logging
- [ ] Pause/Resume import
- [ ] Download error report
- [ ] Email notification setelah import selesai

## Support

Jika mengalami masalah:
1. Cek log: `storage/logs/laravel.log`
2. Cek queue jobs: `SELECT * FROM jobs;`
3. Cek failed jobs: `SELECT * FROM failed_jobs;`
4. Clear cache: `php artisan cache:clear`
5. Restart queue: Stop dan start ulang `run_queue.bat`
