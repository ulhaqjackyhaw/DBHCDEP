# Panduan Import Data Karyawan dengan Kolom Extended

## Overview
Sistem sekarang mendukung import data karyawan dengan **59 kolom** yang mencakup informasi lengkap kepegawaian, termasuk unit struktur, job classification, license, dan data personal.

## Kolom yang Didukung

### Basic Information (14 kolom)
1. NIK
2. NAMA
3. KODE JABATAN
4. UNIT KERJA
5. JABATAN
6. KELOMPOK KELAS JABATAN
7. GRADE (Job Grade atau Person Grade)
8. STATUS (Status Kepegawaian)
9. ASAL INSTANSI
10. JENIS KELAMIN
11. TANGGAL LAHIR
12. PENDIDIKAN DIAKUI/PENDIDIKAN TERAKHIR
13. TMT
14. LOKASI KERJA

### Extended Unit Structure (4 kolom)
15. UNIT DEPUTY EGM
16. UNIT ASSISTANT DEPUTY
17. UNIT DIVISION HEAD
18. UNIT DEPARTMENT HEAD

### Extended Job Information (7 kolom)
19. TMT JABATAN
20. JOB GRADE
21. PERSON GRADE
22. AWAL LOKASI KERJA
23. STATUS JABATAN
24. SUB STATUS
25. INSTANSI

### Extended Personal Information (10 kolom)
26. USIA
27. RENCANA MPP
28. RENCANA PENSIUN
29. PENDIDIKAN DIAKUI
30. PENDIDIKAN DIMILIKI
31. TMT KARYAWAN
32. MASA KERJA
33. TMT KJ TERTINGGI
34. MASA KJ TERTINGGI (TAHUN)

### Job Classification Extended (6 kolom)
35. SUB KELUARGA JABATAN
36. KELUARGA JABATAN
37. FUNGSI JABATAN
38. JALUR KARIR
39. JENJANG KARIR
40. FUNGSI PEKERJAAN

### License Information (5 kolom)
41. LISENCE DIMILIKI
42. RATING
43. NO STKP
44. MASA BERLAKU
45. LISENCE DIBAYARKAN JANUARI

### Additional Personal Data (8 kolom)
46. JURUSAN
47. AGAMA
48. NILAI NPI 2022
49. KATEGORI
50. NO KTP
51. ALAMAT KTP
52. NO KONTRAK
53. EMAIL

### Contact & Demographic (3 kolom)
54. NO HP
55. STATUS PERNIKAHAN
56. GENERASI

### Job History (4 kolom)
57. NO SK JABATAN TERAKHIR
58. TGL SK JABATAN TERAKHIR
59. CEK LISENCE/SERKOM

### Performance Indicators (3 kolom)
60. KPI 2023
61. KRITERIA
62. FUNGSI KONTRAK OS

## Langkah-langkah Implementasi

### 1. Jalankan Migration
```bash
php artisan migrate
```

Migration akan menambahkan 45+ kolom baru ke tabel `data_karyawan`.

### 2. Download Template Excel Baru
- Akses menu **Data Karyawan**
- Klik tombol **Download Template**
- Template akan memiliki 59 kolom sesuai dengan struktur di atas

### 3. Format File Excel

#### Header Row
Header harus menggunakan format UPPERCASE (huruf besar semua) seperti di template:
- `NIK`, `NAMA`, `KODE JABATAN`, dst.

#### Format Data
- **NIK**: Text/String (contoh: `18617368`)
- **Nama**: Text dengan format proper case
- **Tanggal**: Gunakan format `dd/mm/yyyy` atau biarkan Excel format tanggal (akan auto-convert)
  - Contoh: `23/07/1995`, `12/04/2022`
- **Jenis Kelamin**: `Laki-laki` atau `Perempuan`
- **Angka**: Numeric value (contoh: usia `28`, grade `11`)

### 4. Upload File

#### Via Web Interface
1. Login sebagai Admin
2. Pergi ke **Data Karyawan** > **Import Data**
3. Pilih file Excel yang sudah diisi
4. Klik **Upload**

#### Via API (jika tersedia)
```php
POST /data-karyawan/import
Content-Type: multipart/form-data
file: [your-excel-file.xlsx]
```

### 5. Batch Processing

Import menggunakan **batch processing** untuk handle file besar:
- **Batch Size**: 200 rows per batch
- **Chunk Size**: 200 rows per read
- Mendukung file dengan ribuan baris

## Fitur Import

### 1. Flexible Column Names
Import mendukung berbagai variasi nama kolom:
```php
// Contoh: Jenis Kelamin bisa dari kolom:
'gender', 'jenis_kelamin', 'JENIS KELAMIN'

// Unit Kerja bisa dari:
'unit', 'unit_kerja', 'UNIT KERJA'
```

### 2. Auto Date Conversion
- Excel serial dates otomatis dikonversi ke format `dd/mm/yyyy`
- Mendukung berbagai format tanggal Excel

### 3. Smart Null Handling
- Kolom yang kosong/null akan diabaikan
- Tidak akan error jika beberapa kolom tidak diisi

### 4. Skip Empty Rows
- Baris kosong otomatis di-skip
- Tidak akan membuat record kosong

## Validasi & Error Handling

### Required Fields (Wajib Diisi)
- NIK (harus unique)
- NAMA
- KODE JABATAN

### Optional Fields
- Semua kolom lainnya bersifat optional (boleh kosong)

### Error Messages
Jika terjadi error, sistem akan menampilkan:
- Row number yang bermasalah
- Jenis error (duplicate NIK, format tidak valid, dll)
- Suggested fix

## Performance Tips

### Untuk File Besar (>5000 rows)
1. Split file menjadi beberapa bagian (max 5000 rows per file)
2. Upload satu per satu
3. Monitor progress via log atau notification

### Optimasi Import Speed
- Pastikan Excel tidak memiliki formula kompleks
- Hapus formatting yang tidak perlu
- Export sebagai `.xlsx` bukan `.xls`

## Example Data

```csv
NIK,NAMA,KODE JABATAN,UNIT KERJA,JABATAN,JENIS KELAMIN,TANGGAL LAHIR,...
18617368,"Andi Firmansyah","CGKD01AD03DIV06DEP022J0175","T3 Ventilation","Engineer","Laki-laki","23/07/1995",...
455425237,"Cantika Ayu","A2910","Terminal 1 Building","OS","Perempuan","27/09/1993",...
```

## Troubleshooting

### Issue: "Column not found"
**Solution**: Pastikan header row menggunakan nama kolom yang sesuai (UPPERCASE)

### Issue: "Duplicate NIK"
**Solution**: NIK harus unique. Cek data untuk NIK yang duplikat

### Issue: "Invalid date format"
**Solution**: 
- Gunakan format `dd/mm/yyyy`
- Atau biarkan Excel handle format tanggal

### Issue: "Import stuck/timeout"
**Solution**:
- Split file menjadi lebih kecil
- Increase PHP max_execution_time
- Check server memory

## Database Schema

Setelah migration, tabel `data_karyawan` memiliki struktur:

```sql
CREATE TABLE data_karyawan (
  id BIGINT PRIMARY KEY AUTO_INCREMENT,
  -- Basic fields (existing)
  nik VARCHAR(255) UNIQUE,
  nama VARCHAR(255),
  gender VARCHAR(255),
  kode_jabatan VARCHAR(255),
  -- ... (14 kolom basic)
  
  -- Extended fields (new)
  unit_deputy_egm VARCHAR(255) NULL,
  unit_assistant_deputy VARCHAR(255) NULL,
  -- ... (45 kolom tambahan)
  
  created_at TIMESTAMP,
  updated_at TIMESTAMP
);
```

## API Response

Setelah import berhasil:

```json
{
  "success": true,
  "message": "Data berhasil diimport",
  "data": {
    "total_rows": 1500,
    "imported": 1495,
    "failed": 5,
    "errors": [
      {
        "row": 125,
        "error": "Duplicate NIK: 12345678"
      }
    ]
  }
}
```

## Best Practices

1. **Selalu gunakan template yang disediakan**
2. **Backup database sebelum import besar**
3. **Test dengan sample data kecil dulu** (10-20 rows)
4. **Validasi data di Excel sebelum upload**
5. **Monitor error log** di `storage/logs/laravel.log`

## Support & Contact

Untuk pertanyaan atau issue:
- Check log: `storage/logs/laravel.log`
- Contact admin sistem
- Submit issue ke repository

---

**Last Updated**: October 27, 2025
**Version**: 2.0.0 (Extended Import Feature)
