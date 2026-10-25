<?php

namespace App\Imports;

use App\Models\DataPgs;
use Maatwebsite\Excel\Concerns\ToModel;
use Maatwebsite\Excel\Concerns\WithHeadingRow;
use Maatwebsite\Excel\Concerns\WithValidation;
use Maatwebsite\Excel\Concerns\Importable;
use Maatwebsite\Excel\Concerns\WithBatchInserts;
use Maatwebsite\Excel\Concerns\WithChunkReading;
use Maatwebsite\Excel\Concerns\SkipsEmptyRows;

class DataPgsImport implements ToModel, WithHeadingRow, WithBatchInserts, WithChunkReading, SkipsEmptyRows
{
    use Importable;

    /**
     * @param array $row
     *
     * @return \Illuminate\Database\Eloquent\Model|null
     */
    public function model(array $row)
    {
        // Debug: log first row to see actual header format
        static $logged = false;
        if (!$logged) {
            \Log::info('DataPgsImport row keys: ' . json_encode(array_keys($row)));
            \Log::info('DataPgsImport sample row: ' . json_encode($row));
            $logged = true;
        }

        // Helper function to get value with case-insensitive key matching
        $getValue = function ($keys) use ($row) {
            foreach ($keys as $key) {
                if (isset($row[$key])) {
                    return $row[$key];
                }
            }
            return null;
        };

        // Handle date conversion for Excel serial dates
        $tanggalPgs = $getValue([
            'tanggal_pgs',
            'tanggal pgs',
            'TANGGAL PGS',
            'tanggal_mulai_pgs',
            'tanggal mulai pgs',
            'TANGGAL MULAI PGS'
        ]);

        if ($tanggalPgs && is_numeric($tanggalPgs)) {
            // Convert Excel serial date to date string
            $tanggalPgs = \PhpOffice\PhpSpreadsheet\Shared\Date::excelToDateTimeObject($tanggalPgs)->format('d/m/Y');
        } elseif ($tanggalPgs && is_string($tanggalPgs)) {
            // Try to parse various date formats
            try {
                if (strpos($tanggalPgs, '/') !== false) {
                    // Already in d/m/Y format
                    $parsedDate = \Carbon\Carbon::createFromFormat('d/m/Y', $tanggalPgs);
                } elseif (strpos($tanggalPgs, '-') !== false) {
                    // Assume Y-m-d format
                    $parsedDate = \Carbon\Carbon::createFromFormat('Y-m-d', $tanggalPgs);
                } else {
                    throw new \Exception('Unknown date format');
                }
                $tanggalPgs = $parsedDate->format('d/m/Y');
            } catch (\Exception $e) {
                // Keep original format if parsing fails
            }
        }

        // Handle tanggal selesai PGS
        $tanggalSelesaiPgs = $getValue([
            'tanggal_selesai_pgs',
            'tanggal selesai pgs',
            'TANGGAL SELESAI PGS'
        ]);

        if ($tanggalSelesaiPgs && is_numeric($tanggalSelesaiPgs)) {
            $tanggalSelesaiPgs = \PhpOffice\PhpSpreadsheet\Shared\Date::excelToDateTimeObject($tanggalSelesaiPgs)->format('d/m/Y');
        } elseif ($tanggalSelesaiPgs && is_string($tanggalSelesaiPgs)) {
            try {
                if (strpos($tanggalSelesaiPgs, '/') !== false) {
                    $parsedDate = \Carbon\Carbon::createFromFormat('d/m/Y', $tanggalSelesaiPgs);
                } elseif (strpos($tanggalSelesaiPgs, '-') !== false) {
                    $parsedDate = \Carbon\Carbon::createFromFormat('Y-m-d', $tanggalSelesaiPgs);
                } else {
                    throw new \Exception('Unknown date format');
                }
                $tanggalSelesaiPgs = $parsedDate->format('d/m/Y');
            } catch (\Exception $e) {
                $tanggalSelesaiPgs = null;
            }
        }

        $lokasiUnitKerja = $getValue([
            'lokasi_unit_kerja',
            'lokasiunit_kerja',  // Excel slug converts "/" to nothing
            'lokasi/unit_kerja',
            'lokasi/unit kerja',
            'lokasi unit kerja',
            'LOKASI/UNIT KERJA',
            'lokasiunit kerja',  // Possible conversion
            'LOKASIUNIT KERJA',
            'LOKASI UNIT KERJA',
            'unit_kerja',
            'unit kerja',
            'UNIT KERJA',
            'lokasi',
            'LOKASI'
        ]);

        // Set default value if lokasi_unit_kerja is still null
        if (empty($lokasiUnitKerja)) {
            $lokasiUnitKerja = '-';
        }

        return new DataPgs([
            'nik' => (string) $getValue(['nik', 'NIK']),
            'nama' => $getValue(['nama', 'NAMA']),
            'jabatan_definitif' => $getValue(['jabatan_definitif', 'jabatan definitif', 'JABATAN DEFINITIF']),
            'jabatan_pgs' => $getValue(['jabatan_pgs', 'jabatan pgs', 'JABATAN PGS']),
            'lokasi_unit_kerja' => $lokasiUnitKerja,
            'tanggal_pgs' => $tanggalPgs,
            'tanggal_selesai_pgs' => $tanggalSelesaiPgs,
        ]);
    }

    /**
     * @return int
     */
    public function batchSize(): int
    {
        return 200;
    }

    /**
     * @return int
     */
    public function chunkSize(): int
    {
        return 200;
    }
}