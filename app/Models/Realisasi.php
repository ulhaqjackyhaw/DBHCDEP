<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Traits\HasAuditLog;

class Realisasi extends Model
{
    use HasFactory, HasAuditLog;

    protected $table = 'realisasi';

    protected $fillable = [
        'program_kerja',
        'tahun',
        'rkap',
        'realisasi_jan_mar',
        'realisasi_jan_jun',
        'realisasi_jul_sep',
        'realisasi_jul_des',
        'total',
    ];

    // Accessors for computed achievements
    public function getAchS1Attribute(): float
    {
        // Achievement Semester 1 = REALISASI JAN–JUN / RKAP
        if (empty($this->rkap) || (float) $this->rkap == 0.0)
            return 0.0;
        return (float) $this->realisasi_jan_jun / (float) $this->rkap;
    }

    public function getAchS2Attribute(): float
    {
        // Achievement Semester 2 = REALISASI JUL–DES / RKAP
        if (empty($this->rkap) || (float) $this->rkap == 0.0)
            return 0.0;
        return (float) $this->realisasi_jul_des / (float) $this->rkap;
    }

    public function getAchYearAttribute(): float
    {
        // Achievement Tahun = (Realisasi S1 + Realisasi S2) / RKAP
        // Catatan: Jika ingin mengikuti teks persis "(ACH S1 + ACH S2) / RKAP",
        // itu akan menghasilkan pembagian ganda oleh RKAP. Rumus yang umum/logis dipakai di sini.
        if (empty($this->rkap) || (float) $this->rkap == 0.0)
            return 0.0;
        $totalRealisasi = (float) $this->realisasi_jan_jun + (float) $this->realisasi_jul_des;
        return $totalRealisasi / (float) $this->rkap;
    }
}
