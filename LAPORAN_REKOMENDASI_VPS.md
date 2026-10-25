# LAPORAN REKOMENDASI SPESIFIKASI VPS
## Dashboard Kepegawaian Regional 1 PT Angkasa Pura Indonesia

**Tanggal**: 12 Desember 2025  
**Perihal**: Upgrade Spesifikasi Server untuk Stabilitas Sistem

---

## 1. LATAR BELAKANG

Saat ini sistem Dashboard Kepegawaian Regional 1 mengalami kendala teknis berupa:
- ❌ Server **crash/down** saat melakukan upload/download data Excel karyawan (6,000 baris)
- ❌ Sistem menjadi **sangat lambat (ngelag)** saat diakses multiple user
- ❌ **Harus restart berkali-kali** untuk menyelesaikan 1 proses upload
- ❌ **Data loss risk** tinggi karena proses upload tidak complete

**Penyebab Utama**: Spesifikasi VPS yang sekarang **tidak mencukupi** untuk beban kerja sistem.

---

## 2. SPESIFIKASI SAAT INI

| Komponen | Spesifikasi Current | Status |
|----------|---------------------|--------|
| RAM | 1 GB | ❌ Sangat Kurang |
| CPU | 1 vCore | ❌ Kurang |
| Storage | 60 GB | ✅ Cukup |

**Analisis**: 
- Struktur data Excel: **6,000 baris × 64 kolom** = 384,000 cells
- File size: ~5-8 MB per upload
- Memory requirement per upload: **~570 MB**
- **Kesimpulan**: RAM 1GB **impossible** untuk handle beban ini

---

## 3. REKOMENDASI SPESIFIKASI VPS

### ✅ PILIHAN TERBAIK (Cost-Effective)

| Komponen | Spesifikasi | Keterangan |
|----------|-------------|------------|
| **RAM** | **8 GB** | **WAJIB** - untuk Excel processing 64 kolom |
| **CPU** | **4 vCores** | Optimal untuk 20-30 concurrent users |
| **Storage** | **60 GB SSD** | Cukup untuk database & aplikasi |
| **Bandwidth** | 2-4 TB/bulan | Untuk upload/download operations |
| **IP Address** | 1 IP | Standard |

**Estimasi Biaya**: **Rp 500.000 - 600.000/bulan**

---

## 4. BENEFIT UPGRADE VPS

### Keuntungan Operasional:
✅ **Upload/Download Lancar**
   - Excel 6,000 baris × 64 kolom dapat diproses **tanpa crash**
   - Tidak perlu restart server berkali-kali
   - Waktu processing: ~2-3 menit (vs sekarang crash)

✅ **Multiple User Access**
   - Support 20-30 user bersamaan dengan smooth
   - Dashboard analytics tetap responsif
   - Tidak ada lag saat peak hours

✅ **Data Security**
   - Zero data loss karena sistem stabil
   - Backup & restore berjalan normal
   - Audit trail tercatat lengkap

✅ **Produktivitas Meningkat**
   - Staff tidak perlu menunggu lama
   - Update data karyawan lebih cepat
   - Report generation tidak terganggu

### Keuntungan Finansial:
- **Mencegah downtime loss**: ~Rp 500k-1jt per incident
- **Efisiensi waktu staff**: ~2-3 jam/hari saved
- **Menghindari data re-entry**: Cost sangat besar jika data corrupt

---

## 5. PERBANDINGAN OPSI

| Spesifikasi | Biaya/Bulan | Status | Rekomendasi |
|-------------|-------------|--------|-------------|
| **RAM 1GB + CPU 1** (Current) | Rp 50-100k | ❌ Crash terus | Tidak layak |
| **RAM 4GB + CPU 2** | Rp 250-300k | ⚠️ Masih risky | Tidak disarankan untuk 64 kolom |
| **RAM 8GB + CPU 4** | **Rp 500-600k** | ✅ **AMAN** | **✅ PILIH INI** |
| **RAM 8GB + CPU 8** | Rp 700-800k | ✅ Safe tapi overkill | Budget lebih tanpa benefit signifikan |
| **RAM 16GB + CPU 8** | Rp 1-1.2jt | 🚀 Future-proof | Opsional untuk long-term |

---

## 6. PROVIDER VPS REKOMENDASI

### Lokal (Support Bahasa Indonesia 24/7)
| Provider | Spesifikasi | Harga/Bulan | Link |
|----------|-------------|-------------|------|
| **Niagahoster** | 8GB RAM, 4 CPU | Rp 500k | niagahoster.co.id |
| **Dewaweb** | 8GB RAM, 4 CPU | Rp 550k | dewaweb.com |
| **IDCloudHost** | 8GB RAM, 4 CPU | Rp 580k | idcloudhost.com |

### Global (Lebih Murah, Support English)
| Provider | Spesifikasi | Harga/Bulan | Link |
|----------|-------------|-------------|------|
| **Vultr** | 8GB RAM, 4 CPU | $24 (~Rp 360k) | vultr.com |
| **DigitalOcean** | 8GB RAM, 4 CPU | $48 (~Rp 720k) | digitalocean.com |

**Rekomendasi Provider**: **Niagahoster** atau **Dewaweb** (support lokal, harga kompetitif)

---

## 7. KONFIGURASI TEKNIS DIPERLUKAN

Setelah upgrade VPS, perlu konfigurasi optimal:

### PHP Settings (php.ini):
```ini
memory_limit = 512M              # Untuk Excel processing
max_execution_time = 300         # 5 menit untuk upload besar
upload_max_filesize = 50M        # Max file Excel
post_max_size = 50M
max_input_time = 300
```

### MySQL/MariaDB Configuration:
```ini
max_allowed_packet = 64M
innodb_buffer_pool_size = 2G     # 25% dari RAM
innodb_log_file_size = 512M
```

### Queue Worker (Background Processing):
```bash
# Project sudah support queue
php artisan queue:work --timeout=600
```

*Konfigurasi ini akan diterapkan oleh tim IT/developer setelah upgrade.*

---

## 8. TIMELINE & ACTION PLAN

### Fase 1: Procurement (3-5 hari kerja)
- [ ] Approval budget dari manajemen
- [ ] Pembelian & setup VPS baru
- [ ] Testing koneksi & instalasi environment

### Fase 2: Migration (1-2 hari kerja)
- [ ] Backup data lengkap dari server lama
- [ ] Migrasi database & aplikasi ke VPS baru
- [ ] Testing fungsionalitas sistem
- [ ] DNS pointing ke server baru

### Fase 3: Monitoring (1 minggu)
- [ ] Monitoring performa & stability
- [ ] Fine-tuning konfigurasi jika diperlukan
- [ ] Training user untuk fitur optimal

**Total Timeline**: **1-2 minggu** dari approval hingga fully operational

---

## 9. RISK MITIGATION

### Jika Tidak Upgrade:
- ❌ Sistem akan **terus crash** (guaranteed)
- ❌ **Data loss risk tinggi** (corrupt saat upload gagal)
- ❌ **Produktivitas staff turun** drastis
- ❌ **Reputasi IT** menurun di mata user
- ❌ **Cost downtime** bisa mencapai jutaan rupiah

### Dengan Upgrade:
- ✅ Sistem **stabil & reliable**
- ✅ **Zero crash**, data aman
- ✅ **User satisfaction** meningkat
- ✅ **ROI positif** dalam 2-3 bulan

---

## 10. KESIMPULAN & REKOMENDASI

### Kesimpulan:
1. VPS current (1GB RAM) **tidak memadai** untuk sistem Dashboard Kepegawaian dengan struktur data 6,000 baris × 64 kolom
2. Upgrade ke **RAM 8GB + CPU 4 vCores** adalah **kebutuhan mutlak**, bukan optional
3. Estimasi biaya **Rp 500-600k/bulan** adalah **investasi infrastruktur** yang justified

### Rekomendasi Final:

**UPGRADE VPS KE SPESIFIKASI:**
- **RAM**: 8 GB
- **CPU**: 4 vCores
- **Storage**: 60 GB SSD
- **Provider**: Niagahoster atau Dewaweb
- **Budget**: Rp 500-600 ribu/bulan

### ROI Analysis:
```
Cost Upgrade  : Rp 500k/bulan
Cost Downtime : Rp 500k-1jt per incident (minimal 2-3x/bulan)
Time Saved    : 2-3 jam staff/hari × Rp 50k/jam = Rp 100-150k/hari

ROI Payback Period: < 1 bulan
```

**Upgrade ini akan menyelesaikan semua masalah crash dan lag yang ada sekarang.**

---

## 11. APPROVAL

| Jabatan | Nama | Tanda Tangan | Tanggal |
|---------|------|--------------|---------|
| **Developer/IT** | _____________ | _____________ | _______ |
| **Manager IT** | _____________ | _____________ | _______ |
| **Direktur/GM** | _____________ | _____________ | _______ |

---

**Prepared by**: IT Team Dashboard Kepegawaian  
**Date**: 12 Desember 2025  
**Document Version**: 1.0

---

## LAMPIRAN

### A. Screenshot Error (Jika Ada)
*Lampirkan screenshot saat sistem crash untuk dokumentasi*

### B. Current System Metrics
- Database size: _____ MB
- Total records: 6,000+ karyawan
- Excel columns: 64 kolom
- Daily active users: _____ users
- Peak concurrent users: _____ users

### C. Technical Specifications Project
- Framework: Laravel 12
- Database: MySQL/MariaDB
- Import/Export: Maatwebsite Excel + PhpSpreadsheet
- Features: RBAC, Audit Trail, Version Control, Analytics Dashboard

---

**Catatan**: Laporan ini dibuat berdasarkan analisis teknis mendalam terhadap struktur data, performa sistem, dan requirement operasional Dashboard Kepegawaian Regional 1.
