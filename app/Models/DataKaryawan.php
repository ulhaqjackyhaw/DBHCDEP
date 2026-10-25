<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use App\Traits\HasAuditLog;

class DataKaryawan extends Model
{
    use HasFactory, HasAuditLog;

    protected $table = 'data_karyawan';

    protected $fillable = [
        // Basic Info
        'nik',
        'nama',
        'kode_jabatan',

        // Unit Structure
        'unit_deputy_egm',
        'unit_assistant_deputy',
        'unit_division_head',
        'unit_department_head',
        'unit_kerja',

        // Job Information
        'jabatan',
        'person_grade',
        'lokasi_kerja',
        'awal_lokasi_kerja',
        'status_jabatan',
        'sub_status',
        'asal_instansi',
        'instansi',

        // Personal Information
        'jenis_kelamin',
        'tanggal_lahir',
        'usia',
        'rencana_mpp',
        'rencana_pensiun',
        'pendidikan_diakui',
        'pendidikan_dimiliki',
        'jurusan',
        'tmt_karyawan',
        'masa_kerja',
        'tmt_kj_tertinggi',
        'masa_kj_tertinggi_tahun',

        // Job Classification
        'sub_keluarga_jabatan',
        'keluarga_jabatan',
        'fungsi_jabatan',
        'jalur_karir',
        'jenjang_karir',
        'kelompok_kelas_jabatan',
        'fungsi_pekerjaan',

        // License Information
        'lisence_dimiliki',
        'rating',
        'no_stkp',
        'masa_berlaku',
        'lisence_dibayarkan_januari',

        // Additional Personal Data
        'agama',
        'nilai_npi_2022',
        'kategori',
        'no_ktp',
        'alamat_ktp',
        'no_kontrak',
        'email',
        'no_hp',
        'status_pernikahan',
        'generasi',

        // Job History & Performance
        'no_sk_jabatan_terakhir',
        'tgl_sk_jabatan_terakhir',
        'cek_lisence_serkom',
        'kpi_2023',
        'kriteria',
        'fungsi_kontrak_os',
        'penugasan',
    ];

    /**
     * Relationship dengan tabel formasi berdasarkan kode_jabatan
     */
    public function formasi()
    {
        return $this->belongsTo(Formasi::class, 'kode_jabatan', 'kode_jabatan');
    }

    /**
     * Scope untuk filter berdasarkan lokasi
     */
    public function scopeByLokasi($query, $lokasi)
    {
        return $query->where('lokasi', $lokasi);
    }

    /**
     * Scope untuk filter berdasarkan unit
     */
    public function scopeByUnit($query, $unit)
    {
        return $query->where('unit', $unit);
    }

    /**
     * Scope untuk filter berdasarkan status kepegawaian
     */
    public function scopeByStatus($query, $status)
    {
        return $query->where('status_kepegawaian', $status);
    }

    /**
     * Scope untuk filter berdasarkan gender
     */
    public function scopeByGender($query, $gender)
    {
        return $query->where('gender', $gender);
    }
}
