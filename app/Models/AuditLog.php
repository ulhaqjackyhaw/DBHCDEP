<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class AuditLog extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'user_name',
        'user_email',
        'action',
        'model_type',
        'model_id',
        'model_identifier',
        'old_values',
        'new_values',
        'changes',
        'ip_address',
        'user_agent',
    ];

    protected $casts = [
        'old_values' => 'array',
        'new_values' => 'array',
        'changes' => 'array',
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
    ];

    /**
     * Relationship ke User
     */
    public function user()
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Get human-readable action name
     */
    public function getActionNameAttribute()
    {
        return match ($this->action) {
            'created' => 'Menambah Data',
            'updated' => 'Mengubah Data',
            'deleted' => 'Menghapus Data',
            'restored' => 'Memulihkan Data',
            'bulk_created' => 'Import Data (Tambah)',
            'bulk_replaced' => 'Import Data (Replace)',
            'version_restored' => 'Restore dari Snapshot',
            default => ucfirst($this->action),
        };
    }

    /**
     * Get model name in Indonesian
     */
    public function getModelNameAttribute()
    {
        return match ($this->model_type) {
            'App\\Models\\DataKaryawan' => 'Data Karyawan',
            'App\\Models\\Formasi' => 'Data Formasi',
            'App\\Models\\Realisasi' => 'Data Realisasi',
            'App\\Models\\DataPgs' => 'Data PGS',
            'App\\Models\\User' => 'User',
            'App\\Models\\Version' => 'Version Snapshot',
            default => class_basename($this->model_type),
        };
    }

    /**
     * Get badge color based on action
     */
    public function getActionBadgeColorAttribute()
    {
        return match ($this->action) {
            'created' => 'success',
            'updated' => 'warning',
            'deleted' => 'danger',
            'restored' => 'info',
            'bulk_created' => 'primary',
            'bulk_replaced' => 'dark',
            'version_restored' => 'purple',
            default => 'secondary',
        };
    }

    /**
     * Scope untuk filter berdasarkan model type
     */
    public function scopeForModel($query, $modelType)
    {
        return $query->where('model_type', $modelType);
    }

    /**
     * Scope untuk filter berdasarkan action
     */
    public function scopeForAction($query, $action)
    {
        return $query->where('action', $action);
    }

    /**
     * Scope untuk filter berdasarkan user
     */
    public function scopeByUser($query, $userId)
    {
        return $query->where('user_id', $userId);
    }

    /**
     * Scope untuk filter berdasarkan tanggal
     */
    public function scopeDateRange($query, $startDate, $endDate)
    {
        return $query->whereBetween('created_at', [$startDate, $endDate]);
    }

    /**
     * Get formatted changes summary
     */
    public function getChangesSummary()
    {
        $changes = $this->getAttribute('changes');

        if (empty($changes) || !is_array($changes)) {
            return [];
        }

        // Field yang tidak perlu ditampilkan sama sekali
        $excludedFields = ['updated_at', 'remember_token'];

        // Field sensitif yang perlu disembunyikan nilainya
        $sensitiveFields = ['password'];

        $summary = [];
        foreach ($changes as $field => $change) {
            // Skip field yang di-exclude
            if (in_array($field, $excludedFields)) {
                continue;
            }

            if (!is_array($change)) {
                continue;
            }

            // Untuk field sensitif, tampilkan hanya info bahwa field diubah
            if (in_array($field, $sensitiveFields)) {
                $summary[] = [
                    'field' => $this->getFieldLabel($field),
                    'old' => '••••••••',
                    'new' => '••••••••',
                    'is_sensitive' => true,
                ];
            } else {
                $summary[] = [
                    'field' => $this->getFieldLabel($field),
                    'old' => $change['old'] ?? '-',
                    'new' => $change['new'] ?? '-',
                    'is_sensitive' => false,
                ];
            }
        }

        return $summary;
    }    /**
         * Get field label in Indonesian
         */
    private function getFieldLabel($field)
    {
        $labels = [
            // Data Karyawan fields
            'nik' => 'NIK',
            'nama' => 'Nama',
            'jenis_kelamin' => 'Jenis Kelamin',
            'tanggal_lahir' => 'Tanggal Lahir',
            'pendidikan_diakui' => 'Pendidikan Diakui',
            'asal_instansi' => 'Asal Instansi',
            'unit_deputy_egm' => 'Unit Deputy/EGM',
            'unit_assistant_deputy' => 'Unit Assistant Deputy',
            'unit_division_head' => 'Unit Division Head',
            'unit_department_head' => 'Unit Department Head',
            'person_grade' => 'Person Grade',
            'kelompok_kelas_jabatan' => 'Kelompok Kelas Jabatan',
            'awal_lokasi_kerja' => 'Awal Lokasi Kerja',
            'status_jabatan' => 'Status Jabatan',
            'sub_status' => 'Sub Status',
            'instansi' => 'Instansi',
            'fungsi_kontrak_os' => 'Fungsi Kontrak OS',
            'penugasan' => 'Penugasan',
            'agama' => 'Agama',
            'status_pernikahan' => 'Status Pernikahan',
            'jurusan' => 'Jurusan',
            'pendidikan_dimiliki' => 'Pendidikan Dimiliki',
            'no_ktp' => 'No. KTP',
            'alamat_ktp' => 'Alamat KTP',
            'no_kontrak' => 'No. Kontrak',
            'generasi' => 'Generasi',
            'rencana_mpp' => 'Rencana MPP',
            'rencana_pensiun' => 'Rencana Pensiun',
            'tmt_karyawan' => 'TMT Karyawan',
            'masa_kerja' => 'Masa Kerja',
            'kategori' => 'Kategori',
            'nilai_npi_2022' => 'Nilai NPI 2022',
            'usia' => 'Usia',
            'keluarga_jabatan' => 'Keluarga Jabatan',
            'sub_keluarga_jabatan' => 'Sub Keluarga Jabatan',
            'fungsi_jabatan' => 'Fungsi Jabatan',
            'fungsi_pekerjaan' => 'Fungsi Pekerjaan',
            'jalur_karir' => 'Jalur Karir',
            'jenjang_karir' => 'Jenjang Karir',
            'tmt_kj_tertinggi' => 'TMT KJ Tertinggi',
            'masa_kj_tertinggi_tahun' => 'Masa KJ Tertinggi (Tahun)',
            'no_sk_jabatan_terakhir' => 'No. SK Jabatan Terakhir',
            'tgl_sk_jabatan_terakhir' => 'Tanggal SK Jabatan Terakhir',
            'kpi_2023' => 'KPI 2023',
            'kriteria' => 'Kriteria',
            'lisence_dimiliki' => 'Lisensi Dimiliki',
            'rating' => 'Rating',
            'no_stkp' => 'No. STKP',
            'masa_berlaku' => 'Masa Berlaku',
            'lisence_dibayarkan_januari' => 'Lisensi Dibayarkan Januari',
            'cek_lisence_serkom' => 'Cek Lisensi Serkom',
            'email' => 'Email',
            'no_hp' => 'No. HP',

            // Formasi fields
            'kode_jabatan' => 'Kode Jabatan',
            'lokasi_kerja' => 'Lokasi Kerja',
            'unit_kerja' => 'Unit Kerja',
            'jabatan' => 'Jabatan',
            'grade' => 'Grade',
            'kuota' => 'Kuota',

            // Realisasi fields
            'program_kerja' => 'Program Kerja',
            'tahun' => 'Tahun',
            'rkap' => 'RKAP',
            'realisasi_jan_mar' => 'Realisasi Jan-Mar',
            'realisasi_jan_jun' => 'Realisasi Jan-Jun',
            'realisasi_jul_sep' => 'Realisasi Jul-Sep',
            'realisasi_jul_des' => 'Realisasi Jul-Des',

            // DataPgs fields
            'jabatan_definitif' => 'Jabatan Definitif',
            'jabatan_pgs' => 'Jabatan PGS',
            'lokasi_unit_kerja' => 'Lokasi Unit Kerja',
            'tanggal_pgs' => 'Tanggal PGS',
            'tanggal_selesai_pgs' => 'Tanggal Selesai PGS',

            // User fields
            'name' => 'Nama',
            'password' => 'Password',
            'role' => 'Role',
        ];

        return $labels[$field] ?? ucwords(str_replace('_', ' ', $field));
    }
}
