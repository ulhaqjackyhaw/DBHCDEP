<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use App\Traits\HasAuditLog;

class Formasi extends Model
{
    use HasFactory, HasAuditLog;

    protected $table = 'formasi';

    protected $fillable = [
        'kode_jabatan',
        'unit_deputy_egm',
        'unit_assistant_deputy',
        'unit_division_head',
        'unit_department_head',
        'lokasi_kerja',
        'unit_kerja',
        'jabatan',
        'kelompok_kelas_jabatan',
        'grade',
        'kuota',
    ];

    protected $casts = [
        //
    ];

    /**
     * Get the employees for this formasi position
     */
    public function dataKaryawan()
    {
        return $this->hasMany(DataKaryawan::class, 'kode_jabatan', 'kode_jabatan');
    }

    /**
     * Scope a query to only include positions by location
     */
    public function scopeByLokasi($query, $lokasi)
    {
        return $query->where('lokasi_kerja', $lokasi);
    }

    /**
     * Scope a query to only include positions by unit
     */
    public function scopeByUnit($query, $unit)
    {
        return $query->where('unit_kerja', $unit);
    }

    /**
     * Scope a query to only include positions by grade
     */
    public function scopeByGrade($query, $grade)
    {
        return $query->where('grade', $grade);
    }

    /**
     * Scope a query to only include positions by kelompok kelas jabatan
     */
    public function scopeByKelompokKelas($query, $kelompokKelas)
    {
        return $query->where('kelompok_kelas_jabatan', $kelompokKelas);
    }
}
