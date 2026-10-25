<?php

namespace App\Imports;

use App\Models\DataPenugasan;
use Maatwebsite\Excel\Concerns\ToModel;
use Maatwebsite\Excel\Concerns\WithHeadingRow;
use Maatwebsite\Excel\Concerns\WithBatchInserts;
use Maatwebsite\Excel\Concerns\WithChunkReading;
use PhpOffice\PhpSpreadsheet\Shared\Date;

class DataPenugasanImport implements ToModel, WithHeadingRow, WithBatchInserts, WithChunkReading
{
    /**
     * @param array $row
     *
     * @return \Illuminate\Database\Eloquent\Model|null
     */
    public function model(array $row)
    {
        // Function to convert date
        $convertDate = function ($value) {
            if (empty($value)) {
                return null;
            }

            // If it's a serial date number from Excel
            if (is_numeric($value)) {
                try {
                    $date = Date::excelToDateTimeObject($value);
                    return $date->format('d/m/Y');
                } catch (\Exception $e) {
                    return null;
                }
            }

            // If it's already a string date
            if (is_string($value)) {
                // Try various date formats
                $formats = ['d/m/Y', 'Y-m-d', 'd-m-Y', 'd M Y', 'd F Y'];
                foreach ($formats as $format) {
                    try {
                        $date = \DateTime::createFromFormat($format, $value);
                        if ($date) {
                            return $date->format('d/m/Y');
                        }
                    } catch (\Exception $e) {
                        continue;
                    }
                }
                // If no format matches, return as is
                return $value;
            }

            return null;
        };

        return new DataPenugasan([
            'nik' => $row['nik'] ?? null,
            'nama' => $row['nama'] ?? null,
            'kj' => $row['kj'] ?? null,
            'jabatan_definitif' => $row['jabatan_definitif'] ?? null,
            'unit_definitif' => $row['unit_definitif'] ?? null,
            'lokasi_definitif' => $row['lokasi_definitif'] ?? null,
            'unit_penugasan' => $row['unit_penugasan'] ?? null,
            'lokasi_penugasan' => $row['lokasi_penugasan'] ?? null,
            'nomor_sprint' => $row['nomor_sprint'] ?? null,
            'tanggal_mulai' => $convertDate($row['tanggal_mulai'] ?? null),
            'tanggal_selesai' => $convertDate($row['tanggal_selesai'] ?? null),
            'pic' => $row['pic'] ?? null,
            'keterangan' => $row['keterangan'] ?? null,
        ]);
    }

    public function batchSize(): int
    {
        return 1000;
    }

    public function chunkSize(): int
    {
        return 1000;
    }
}
