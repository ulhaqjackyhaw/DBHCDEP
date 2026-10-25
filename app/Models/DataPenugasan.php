<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Traits\HasAuditLog;
use Carbon\Carbon;

class DataPenugasan extends Model
{
    use HasFactory, HasAuditLog;

    protected $table = 'data_penugasan';

    protected $fillable = [
        'nik',
        'nama',
        'kj',
        'jabatan_definitif',
        'kode_jabatan',
        'unit_definitif',
        'lokasi_definitif',
        'unit_penugasan',
        'lokasi_penugasan',
        'nomor_sprint',
        'tanggal_mulai',
        'tanggal_selesai',
        'pic',
        'keterangan',
    ];

    protected $appends = [
        'tanggal_mulai_formatted',
        'tanggal_selesai_formatted',
        'durasi_penugasan',
        'is_overdue',
        'is_warning',
        'days_remaining',
        'sisa_hari',
    ];

    /**
     * Scope untuk filter berdasarkan unit penugasan
     */
    public function scopeByUnitPenugasan($query, $unitPenugasan)
    {
        return $query->where('unit_penugasan', $unitPenugasan);
    }

    /**
     * Scope untuk filter berdasarkan lokasi penugasan
     */
    public function scopeByLokasiPenugasan($query, $lokasiPenugasan)
    {
        return $query->where('lokasi_penugasan', $lokasiPenugasan);
    }

    /**
     * Scope untuk filter berdasarkan jabatan definitif
     */
    public function scopeByJabatanDefinitif($query, $jabatanDefinitif)
    {
        return $query->where('jabatan_definitif', $jabatanDefinitif);
    }

    /**
     * Format tanggal mulai untuk display
     */
    public function getTanggalMulaiFormattedAttribute()
    {
        if (!$this->tanggal_mulai) {
            return '-';
        }

        try {
            $date = Carbon::createFromFormat('d/m/Y', $this->tanggal_mulai);
            return $date->format('d M Y');
        } catch (\Exception $e) {
            return $this->tanggal_mulai;
        }
    }

    /**
     * Format tanggal selesai untuk display
     */
    public function getTanggalSelesaiFormattedAttribute()
    {
        if (!$this->tanggal_selesai) {
            return '-';
        }

        try {
            $date = Carbon::createFromFormat('d/m/Y', $this->tanggal_selesai);
            return $date->format('d M Y');
        } catch (\Exception $e) {
            return $this->tanggal_selesai;
        }
    }

    /**
     * Hitung durasi penugasan
     */
    public function getDurasiPenugasanAttribute()
    {
        if (!$this->tanggal_mulai) {
            return '-';
        }

        try {
            $startDate = Carbon::createFromFormat('d/m/Y', $this->tanggal_mulai);

            if ($this->tanggal_selesai) {
                $endDate = Carbon::createFromFormat('d/m/Y', $this->tanggal_selesai);
                $days = $startDate->diffInDays($endDate);
                $months = floor($days / 30);
                $remainingDays = $days % 30;

                if ($months > 0) {
                    return $months . ' bulan ' . $remainingDays . ' hari';
                } else {
                    return $days . ' hari';
                }
            } else {
                $days = $startDate->diffInDays(Carbon::now());
                $months = floor($days / 30);
                $remainingDays = $days % 30;

                if ($months > 0) {
                    return $months . ' bulan ' . $remainingDays . ' hari (ongoing)';
                } else {
                    return $days . ' hari (ongoing)';
                }
            }
        } catch (\Exception $e) {
            return '-';
        }
    }

    /**
     * Check if penugasan sudah melewati tanggal selesai
     */
    public function getIsOverdueAttribute()
    {
        if (!$this->tanggal_selesai) {
            return false;
        }

        try {
            $endDate = Carbon::createFromFormat('d/m/Y', $this->tanggal_selesai);
            return Carbon::now()->gt($endDate);
        } catch (\Exception $e) {
            return false;
        }
    }

    /**
     * Check if penugasan akan selesai dalam 15 hari
     */
    public function getIsWarningAttribute()
    {
        if (!$this->tanggal_selesai) {
            return false;
        }

        try {
            $endDate = Carbon::createFromFormat('d/m/Y', $this->tanggal_selesai);
            $now = Carbon::now();
            $daysRemaining = $now->diffInDays($endDate, false);

            return $daysRemaining >= 0 && $daysRemaining <= 15;
        } catch (\Exception $e) {
            return false;
        }
    }

    /**
     * Get days remaining until end date (numeric)
     */
    public function getDaysRemainingAttribute()
    {
        if (!$this->tanggal_selesai) {
            return null;
        }

        try {
            $endDate = Carbon::createFromFormat('d/m/Y', $this->tanggal_selesai);
            return Carbon::now()->diffInDays($endDate, false);
        } catch (\Exception $e) {
            return null;
        }
    }

    /**
     * Get sisa hari dalam format text (sama seperti DataPgs)
     */
    public function getSisaHariAttribute()
    {
        if (!$this->tanggal_selesai) {
            return null;
        }

        try {
            $endDate = Carbon::createFromFormat('d/m/Y', $this->tanggal_selesai);
            $now = Carbon::now();
            $sisaHari = round($now->diffInDays($endDate, false));

            if ($sisaHari < 0) {
                return 'Lewat ' . abs($sisaHari) . ' hari';
            } elseif ($sisaHari == 0) {
                return 'Hari ini';
            } else {
                return $sisaHari . ' hari lagi';
            }
        } catch (\Exception $e) {
            return '-';
        }
    }
}
