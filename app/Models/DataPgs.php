<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use App\Traits\HasAuditLog;

class DataPgs extends Model
{
    use HasFactory, HasAuditLog;

    protected $table = 'data_pgs';

    protected $fillable = [
        'nik',
        'nama',
        'jabatan_definitif',
        'jabatan_pgs',
        'kode_jabatan',
        'lokasi_unit_kerja',
        'tanggal_pgs',
        'tanggal_selesai_pgs',
    ];

    /**
     * Append accessors to JSON output
     */
    protected $appends = [
        'durasi_pgs',
        'tanggal_pgs_formatted',
        'tanggal_selesai_pgs_formatted',
        'is_overdue',
        'is_warning',
        'sisa_hari',
        'total_days',
    ];

    /**
     * Scope untuk filter berdasarkan lokasi unit kerja
     */
    public function scopeByLokasiUnit($query, $lokasiUnit)
    {
        return $query->where('lokasi_unit_kerja', $lokasiUnit);
    }

    /**
     * Scope untuk filter berdasarkan jabatan definitif
     */
    public function scopeByJabatanDefinitif($query, $jabatanDefinitif)
    {
        return $query->where('jabatan_definitif', $jabatanDefinitif);
    }

    /**
     * Scope untuk filter berdasarkan jabatan PGS
     */
    public function scopeByJabatanPgs($query, $jabatanPgs)
    {
        return $query->where('jabatan_pgs', $jabatanPgs);
    }

    /**
     * Menghitung TOTAL durasi PGS dari tanggal mulai sampai tanggal selesai
     */
    public function getDurasiPgsAttribute()
    {
        if (!$this->tanggal_pgs) {
            return '-';
        }

        // Jika tidak ada tanggal selesai, hitung dari tanggal mulai sampai sekarang
        if (!$this->tanggal_selesai_pgs) {
            try {
                $tanggalPgs = \Carbon\Carbon::createFromFormat('d/m/Y', $this->tanggal_pgs)->startOfDay();
                $sekarang = \Carbon\Carbon::now()->startOfDay();

                if ($tanggalPgs->isFuture()) {
                    return 'Belum dimulai';
                }

                $totalHari = (int) $tanggalPgs->diffInDays($sekarang);

                if ($totalHari == 0) {
                    return 'Hari ini';
                }

                $bulan = floor($totalHari / 30);
                $hari = $totalHari % 30;

                if ($bulan > 0 && $hari > 0) {
                    return $bulan . ' bulan ' . $hari . ' hari';
                } elseif ($bulan > 0) {
                    return $bulan . ' bulan';
                } else {
                    return $hari . ' hari';
                }
            } catch (\Exception $e) {
                return 'Invalid Date';
            }
        }

        // Jika ada tanggal selesai, hitung TOTAL durasi PGS (tanggal mulai sampai tanggal selesai)
        try {
            $tanggalPgs = \Carbon\Carbon::createFromFormat('d/m/Y', $this->tanggal_pgs)->startOfDay();
            $tanggalSelesai = \Carbon\Carbon::createFromFormat('d/m/Y', $this->tanggal_selesai_pgs)->startOfDay();

            // Hitung total hari PGS
            $totalHari = (int) $tanggalPgs->diffInDays($tanggalSelesai);

            if ($totalHari == 0) {
                return '1 hari';
            }

            // Konversi ke bulan dan hari
            $bulan = floor($totalHari / 30);
            $hari = $totalHari % 30;

            // Format output
            if ($bulan > 0 && $hari > 0) {
                return $bulan . ' bulan ' . $hari . ' hari';
            } elseif ($bulan > 0) {
                return $bulan . ' bulan';
            } else {
                return $totalHari . ' hari';
            }
        } catch (\Exception $e) {
            return 'Invalid Date';
        }
    }

    /**
     * Format tanggal PGS untuk tampilan
     */
    public function getTanggalPgsFormattedAttribute()
    {
        if (!$this->tanggal_pgs) {
            return '-';
        }

        try {
            $tanggal = \Carbon\Carbon::createFromFormat('d/m/Y', $this->tanggal_pgs);
            return $tanggal->format('d M Y');
        } catch (\Exception $e) {
            return $this->tanggal_pgs;
        }
    }

    /**
     * Format tanggal selesai PGS untuk tampilan
     */
    public function getTanggalSelesaiPgsFormattedAttribute()
    {
        if (!$this->tanggal_selesai_pgs) {
            return '-';
        }

        try {
            $tanggal = \Carbon\Carbon::createFromFormat('d/m/Y', $this->tanggal_selesai_pgs);
            return $tanggal->format('d M Y');
        } catch (\Exception $e) {
            return $this->tanggal_selesai_pgs;
        }
    }

    /**
     * Cek apakah durasi PGS sudah melebihi batas (sudah melewati tanggal selesai)
     */
    public function getIsOverdueAttribute()
    {
        if (!$this->tanggal_selesai_pgs) {
            return false;
        }

        try {
            $tanggalSelesai = \Carbon\Carbon::createFromFormat('d/m/Y', $this->tanggal_selesai_pgs)->startOfDay();
            $sekarang = \Carbon\Carbon::now()->startOfDay();

            // Overdue jika sudah melewati tanggal selesai
            return $sekarang->isAfter($tanggalSelesai);
        } catch (\Exception $e) {
            return false;
        }
    }

    /**
     * Cek apakah dalam masa peringatan (15 hari sebelum tanggal selesai)
     */
    public function getIsWarningAttribute()
    {
        if (!$this->tanggal_selesai_pgs) {
            return false;
        }

        try {
            $tanggalSelesai = \Carbon\Carbon::createFromFormat('d/m/Y', $this->tanggal_selesai_pgs)->startOfDay();
            $sekarang = \Carbon\Carbon::now()->startOfDay();

            // Warning jika 15 hari atau kurang sebelum tanggal selesai dan belum overdue
            $sisaHari = (int) floor($sekarang->diffInDays($tanggalSelesai, false));
            return $sisaHari > 0 && $sisaHari <= 15;
        } catch (\Exception $e) {
            return false;
        }
    }

    /**
     * Dapatkan sisa hari sebelum tanggal selesai
     */
    public function getSisaHariAttribute()
    {
        if (!$this->tanggal_selesai_pgs) {
            return null;
        }

        try {
            $tanggalSelesai = \Carbon\Carbon::createFromFormat('d/m/Y', $this->tanggal_selesai_pgs)->startOfDay();
            $sekarang = \Carbon\Carbon::now()->startOfDay();

            // Hitung sisa hari dengan benar dan bulatkan
            $sisaHari = (int) floor($sekarang->diffInDays($tanggalSelesai, false));

            if ($sisaHari < 0) {
                // Sudah lewat
                return 'Lewat ' . abs($sisaHari) . ' hari';
            } elseif ($sisaHari == 0) {
                return 'Hari ini berakhir';
            } else {
                return $sisaHari . ' hari lagi';
            }
        } catch (\Exception $e) {
            return '-';
        }
    }

    /**
     * Dapatkan total hari PGS
     */
    public function getTotalDaysAttribute()
    {
        if (!$this->tanggal_pgs) {
            return 0;
        }

        try {
            $tanggalPgs = \Carbon\Carbon::createFromFormat('d/m/Y', $this->tanggal_pgs)->startOfDay();
            $sekarang = \Carbon\Carbon::now()->startOfDay();
            return $tanggalPgs->diffInDays($sekarang);
        } catch (\Exception $e) {
            return 0;
        }
    }
}
