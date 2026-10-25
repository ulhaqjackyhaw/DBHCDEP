# Audit Log System - Dokumentasi

## Overview
Sistem Audit Log telah berhasil diimplementasikan untuk tracking semua perubahan data (Create, Update, Delete) pada aplikasi DBHC. Sistem ini secara otomatis mencatat siapa yang melakukan perubahan, kapan, dan apa yang berubah.

## Fitur Utama

### 1. Automatic Tracking
- ✅ **Create (Tambah Data)**: Mencatat semua data baru yang ditambahkan
- ✅ **Update (Ubah Data)**: Mencatat perubahan field-by-field dengan nilai lama dan baru
- ✅ **Delete (Hapus Data)**: Mencatat data yang dihapus beserta seluruh nilainya
- ✅ **Restore**: Mencatat pemulihan data (jika menggunakan soft delete)

### 2. Informasi yang Dicatat
Setiap audit log mencatat:
- **User Information**: ID, nama, dan email user yang melakukan aksi
- **Action Type**: created, updated, deleted, atau restored
- **Model Information**: Tipe model dan ID record yang diubah
- **Model Identifier**: Identifier yang mudah dibaca (nama, NIPP, kode jabatan, dll)
- **Changes**: Detail perubahan (khusus untuk update)
  - Field yang berubah
  - Nilai lama
  - Nilai baru
- **Old Values**: Seluruh data sebelum perubahan (untuk delete & update)
- **New Values**: Seluruh data setelah perubahan (untuk create & update)
- **IP Address**: IP address user yang melakukan perubahan
- **User Agent**: Informasi browser/device yang digunakan
- **Timestamp**: Waktu perubahan dengan presisi detik

### 3. Filter & Pencarian
UI Audit Log dilengkapi dengan filter canggih:

#### A. Quick Menu Filter Buttons
**Tombol filter cepat** di bagian atas halaman untuk akses 1-klik:
- **Semua Menu** - Reset filter, tampilkan semua
- **Data Karyawan** - Filter perubahan di Data Karyawan (sky blue badge)
- **Formasi** - Filter perubahan di Formasi (indigo badge)
- **Realisasi** - Filter perubahan di Realisasi (green badge)  
- **User Management** - Filter perubahan di User (red badge)

#### B. Advanced Filters (Form)
- **Search**: Cari berdasarkan nama, email, atau identifier
- **Menu**: Filter berdasarkan menu (alternative ke quick buttons)
- **Tipe Data**: Filter berdasarkan model (Data Karyawan, Formasi, Realisasi, User)
  - *Otomatis disabled ketika Menu Filter aktif untuk menghindari konflik*
- **Aksi**: Filter berdasarkan jenis aksi (Tambah, Ubah, Hapus)
- **User**: Filter berdasarkan user yang melakukan perubahan
- **Tanggal**: Filter berdasarkan rentang tanggal (dari - sampai)

#### C. Active Filters Badge
Menampilkan **visual indicator** filter yang sedang aktif dengan badge berwarna:
- Menu filter dengan warna sesuai menu
- Search term dengan icon search
- Action dengan warna badge sesuai aksi
- Date range dengan icon calendar
- Filter count untuk melihat jumlah filter aktif

#### D. Auto-Sync Filters
JavaScript otomatis mencegah konflik filter:
- Ketika Menu Filter dipilih → Model Type dropdown ter-disable
- Ketika Model Type dipilih → Menu Filter ter-reset
- Memastikan filter konsisten dan tidak bertabrakan

### 4. Detail View
Setiap audit log dapat dilihat detailnya dengan informasi lengkap:
- Informasi utama dengan badge berwarna sesuai aksi
- Tabel perbandingan nilai lama vs baru (untuk update)
- JSON viewer untuk data lengkap (untuk create & delete)
- Informasi browser/device

## Models yang Sudah Dilengkapi Audit Log

1. ✅ **DataKaryawan** - Data karyawan organik & outsourcing
2. ✅ **Formasi** - Data formasi jabatan
3. ✅ **Realisasi** - Data realisasi program kerja
4. ✅ **User** - Data user aplikasi

## Cara Kerja

### Automatic Logging via Trait
Setiap model yang menggunakan trait `HasAuditLog` akan otomatis dicatat setiap perubahannya:

```php
use App\Traits\HasAuditLog;

class DataKaryawan extends Model
{
    use HasFactory, HasAuditLog;
    // ... model code
}
```

### Event Listeners
Trait `HasAuditLog` mendaftarkan event listeners untuk:
- `created` event → Log action 'created'
- `updated` event → Log action 'updated' dengan detail perubahan
- `deleted` event → Log action 'deleted'
- `restored` event → Log action 'restored' (untuk soft delete)

### Excluded Fields
Field berikut tidak dicatat dalam audit log:
- `created_at`
- `updated_at`
- `deleted_at`
- `password`
- `remember_token`

## Akses Audit Log

### Admin Only
Audit log hanya dapat diakses oleh user dengan role **admin** melalui:
- URL: `/audit-logs`
- Menu Sidebar: **Admin** > **Audit Log**

### Routes
```php
// Admin only routes
Route::middleware(['auth', 'can:admin'])->group(function () {
    Route::prefix('audit-logs')->name('audit-logs.')->group(function () {
        Route::get('/', [AuditLogController::class, 'index'])->name('index');
        Route::get('/{auditLog}', [AuditLogController::class, 'show'])->name('show');
    });
});
```

## Database Schema

### Tabel: `audit_logs`
```sql
- id (bigint, primary key)
- user_id (bigint, nullable, foreign key to users)
- user_name (string, nullable) - Backup jika user dihapus
- user_email (string, nullable)
- action (string) - created/updated/deleted/restored
- model_type (string) - Nama lengkap class model
- model_id (bigint, nullable) - ID record yang diubah
- model_identifier (text, nullable) - Identifier yang readable
- old_values (json, nullable) - Data sebelum perubahan
- new_values (json, nullable) - Data setelah perubahan
- changes (json, nullable) - Summary perubahan spesifik
- ip_address (string, nullable)
- user_agent (text, nullable)
- created_at (timestamp)
- updated_at (timestamp)

Indexes:
- user_id
- model_type
- model_id
- action
- created_at
```

## Contoh Penggunaan

### View Audit History untuk Specific Record
```php
// Di controller atau view
$karyawan = DataKaryawan::find(1);
$history = $karyawan->getAuditHistory();

// Atau
$history = $karyawan->auditLogs()->paginate(10);
```

### Custom Identifier
Jika ingin custom identifier untuk model tertentu, override method `getAuditIdentifier()`:

```php
class DataKaryawan extends Model
{
    use HasAuditLog;
    
    protected function getAuditIdentifier()
    {
        return $this->nipp . ' - ' . $this->nama;
    }
}
```

### Menambahkan Audit Log ke Model Baru
Cukup tambahkan trait `HasAuditLog`:

```php
use App\Traits\HasAuditLog;

class ModelBaru extends Model
{
    use HasFactory, HasAuditLog;
    // ... model code
}
```

## Performance Considerations

### Indexes
Tabel audit_logs sudah dilengkapi dengan indexes untuk:
- Query berdasarkan user
- Query berdasarkan model type
- Query berdasarkan model ID
- Query berdasarkan action
- Query berdasarkan tanggal

### Pagination
UI menggunakan pagination dengan 50 records per halaman untuk performa optimal.

### Background Processing (Optional)
Jika diperlukan, audit logging bisa dipindahkan ke queue untuk menghindari blocking pada request utama. Namun untuk saat ini menggunakan synchronous logging sudah cukup cepat.

## UI Features

### Index Page
- **Quick Filter Buttons** - 5 tombol untuk filter cepat per menu (Semua, Karyawan, Formasi, Realisasi, User)
- **Advanced Filter Panel** - 7 kriteria filter lengkap dengan dropdown dan date picker
- **Active Filters Display** - Badge indicator untuk filter yang sedang aktif
- **Statistics Summary** - Total aktivitas dan filter count
- **List View** - Card design dengan left border berwarna sesuai action
- **Color-coded Badges** - Action badges (green/yellow/red/blue)
- **Menu Badges** - Menu type badges (sky/indigo/green/red)
- **Change Preview** - Preview perubahan langsung di list untuk update action
- **Pagination** - 50 records per halaman
- **Responsive Design** - Mobile-friendly UI

### Detail Page
- Informasi utama dengan badge berwarna
- Tabel perbandingan untuk update changes
- JSON viewer untuk data lengkap
- User agent information
- Back button ke index

## Color Scheme

Actions menggunakan color scheme yang konsisten:
- **Created (Tambah)**: Green (#28a745)
- **Updated (Ubah)**: Yellow/Warning (#ffc107)
- **Deleted (Hapus)**: Red (#dc3545)
- **Restored (Pulihkan)**: Blue/Info (#17a2b8)

## Security

### Authorization
- Hanya admin yang dapat mengakses audit logs
- Middleware `can:admin` digunakan untuk proteksi routes
- User biasa tidak akan melihat menu Audit Log di sidebar

### Data Privacy
- Password tidak pernah dicatat dalam audit log
- Remember token tidak dicatat
- Field sensitif bisa ditambahkan ke excluded fields jika diperlukan

## Maintenance

### Cleanup Old Logs (Optional)
Jika diperlukan, bisa dibuat command untuk cleanup log lama:

```php
// Contoh: Hapus log lebih dari 1 tahun
AuditLog::where('created_at', '<', now()->subYear())->delete();
```

Bisa dijadwalkan di `app/Console/Kernel.php`:

```php
$schedule->command('audit:cleanup')->monthly();
```

## Future Enhancements

Fitur yang bisa ditambahkan di masa depan:
- [ ] Export audit logs ke Excel/PDF
- [ ] Email notification untuk perubahan critical
- [ ] Dashboard analytics untuk audit logs
- [ ] Restore functionality (revert changes)
- [ ] Comparison view untuk multiple versions
- [ ] Bulk export per model/date range

## Testing

Untuk testing audit log functionality:

1. Login sebagai admin
2. Lakukan CRUD operations pada Data Karyawan/Formasi/Realisasi
3. Buka menu Audit Log
4. Verifikasi semua perubahan tercatat dengan benar
5. Test filter dan search functionality
6. Test detail view untuk melihat perubahan detail

## Troubleshooting

### Audit log tidak tercatat
- Pastikan user sudah login (Auth::check() = true)
- Pastikan model menggunakan trait `HasAuditLog`
- Cek apakah migration sudah dijalankan
- Cek error log di `storage/logs/laravel.log`

### Performance issues
- Tambah indexes jika query lambat
- Pertimbangkan cleanup old logs secara berkala
- Pertimbangkan pindahkan ke queue processing

## Files Created

### Migration
- `database/migrations/2025_10_16_103558_create_audit_logs_table.php`

### Model
- `app/Models/AuditLog.php`

### Trait
- `app/Traits/HasAuditLog.php`

### Controller
- `app/Http/Controllers/AuditLogController.php`

### Views
- `resources/views/audit-logs/index.blade.php`
- `resources/views/audit-logs/show.blade.php`

### Routes
- Updated `routes/web.php` with audit-logs routes

### Sidebar
- Updated `resources/views/layouts/partials/sidebar.blade.php` with menu item

## Conclusion

Sistem Audit Log telah berhasil diimplementasikan dengan lengkap dan siap digunakan. Semua perubahan data akan tercatat secara otomatis, memberikan transparansi dan accountability dalam pengelolaan data aplikasi DBHC.

**Status**: ✅ **COMPLETED & READY TO USE**
