<?php

namespace App\Imports;

use App\Models\Formasi;
use Maatwebsite\Excel\Concerns\ToModel;
use Maatwebsite\Excel\Concerns\WithHeadingRow;
use Maatwebsite\Excel\Concerns\WithValidation;
use Maatwebsite\Excel\Concerns\Importable;
use Maatwebsite\Excel\Concerns\WithBatchInserts;
use Maatwebsite\Excel\Concerns\WithChunkReading;
use Maatwebsite\Excel\Concerns\SkipsEmptyRows;

class FormasiImport implements ToModel, WithHeadingRow, WithBatchInserts, WithChunkReading, SkipsEmptyRows
{
    use Importable;

    /**
     * @param array $row
     *
     * @return \Illuminate\Database\Eloquent\Model|null
     */
    public function model(array $row)
    {
        return new Formasi([
            'kode_jabatan' => $row['kode_jabatan'] ?? $row['kode jabatan'] ?? $row['KODE JABATAN'] ?? $row['Kode Jabatan'],
            'unit_deputy_egm' => $row['unit_deputy_egm'] ?? $row['unit deputy egm'] ?? $row['UNIT DEPUTY EGM'] ?? $row['Unit Deputy EGM'] ?? null,
            'unit_assistant_deputy' => $row['unit_assistant_deputy'] ?? $row['unit assistant deputy'] ?? $row['UNIT ASSISTANT DEPUTY'] ?? $row['Unit Assistant Deputy'] ?? null,
            'unit_division_head' => $row['unit_division_head'] ?? $row['unit division head'] ?? $row['UNIT DIVISION HEAD'] ?? $row['Unit Division Head'] ?? null,
            'unit_department_head' => $row['unit_department_head'] ?? $row['unit department head'] ?? $row['UNIT DEPARTMENT HEAD'] ?? $row['Unit Department Head'] ?? null,
            'lokasi_kerja' => $row['lokasi_kerja'] ?? $row['lokasi kerja'] ?? $row['LOKASI KERJA'] ?? $row['Lokasi Kerja'] ?? $row['lokasi'] ?? $row['LOKASI'] ?? $row['Lokasi'],
            'unit_kerja' => $row['unit_kerja'] ?? $row['unit kerja'] ?? $row['UNIT KERJA'] ?? $row['Unit Kerja'] ?? $row['unit'] ?? $row['UNIT'] ?? $row['Unit'],
            'jabatan' => $row['jabatan'] ?? $row['JABATAN'] ?? $row['Jabatan'],
            'kelompok_kelas_jabatan' => $row['kelompok_kelas_jabatan'] ?? $row['kelompok kelas jabatan'] ?? $row['KELOMPOK KELAS JABATAN'] ?? $row['Kelompok Kelas Jabatan'],
            'grade' => (string) ($row['grade'] ?? $row['GRADE'] ?? $row['Grade']),
            'kuota' => isset($row['kuota']) ? (int) $row['kuota'] : (isset($row['KUOTA']) ? (int) $row['KUOTA'] : 1),
        ]);
    }



    /**
     * @return int
     */
    public function batchSize(): int
    {
        return 5000; // Tingkatkan untuk performa yang lebih baik dengan data besar
    }

    /**
     * @return int
     */
    public function chunkSize(): int
    {
        return 5000; // Tingkatkan untuk menangani puluhan ribu baris
    }
}