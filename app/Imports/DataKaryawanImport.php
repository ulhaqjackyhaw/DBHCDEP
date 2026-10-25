<?php

namespace App\Imports;

use App\Models\DataKaryawan;
use Maatwebsite\Excel\Concerns\ToModel;
use Maatwebsite\Excel\Concerns\WithHeadingRow;
use Maatwebsite\Excel\Concerns\WithValidation;
use Maatwebsite\Excel\Concerns\Importable;
use Maatwebsite\Excel\Concerns\WithBatchInserts;
use Maatwebsite\Excel\Concerns\WithChunkReading;
use Maatwebsite\Excel\Concerns\SkipsEmptyRows;
use Maatwebsite\Excel\Concerns\SkipsOnError;
use Maatwebsite\Excel\Concerns\SkipsOnFailure;
use Maatwebsite\Excel\Concerns\WithUpserts;
use Maatwebsite\Excel\Validators\Failure;

class DataKaryawanImport implements ToModel, WithHeadingRow, WithBatchInserts, WithChunkReading, SkipsEmptyRows, SkipsOnError, SkipsOnFailure, WithUpserts
{
    use Importable;

    private $errors = [];
    private $skipped = 0;
    private $skippedNiks = []; // NIK yang di-skip (kosong)
    private $sessionId;
    private $processedRows = 0;
    private $totalRows = 0;

    public function __construct($sessionId = null)
    {
        $this->sessionId = $sessionId;
    }

    /**
     * @param array $row
     *
     * @return \Illuminate\Database\Eloquent\Model|null
     */
    /**
     * Helper to convert Excel dates safely and efficiently
     */
    private function convertDate($dateValue)
    {
        if (empty($dateValue)) {
            return null;
        }

        if (is_numeric($dateValue)) {
            try {
                return \PhpOffice\PhpSpreadsheet\Shared\Date::excelToDateTimeObject($dateValue)->format('d/m/Y');
            } catch (\Throwable $e) {
                return (string) $dateValue;
            }
        }

        return (string) $dateValue;
    }

    /**
     * Helper to get value with multiple possible keys
     */
    private function getValue(array $row, ...$keys)
    {
        foreach ($keys as $key) {
            if (isset($row[$key]) && $row[$key] !== '' && $row[$key] !== null) {
                return $row[$key];
            }
        }
        return null;
    }

    /**
     * @param array $row
     *
     * @return \Illuminate\Database\Eloquent\Model|null
     */
    public function model(array $row)
    {
        $this->processedRows++;

        // Update progress setiap 100 baris atau saat baris terakhir
        if ($this->sessionId && ($this->processedRows % 100 === 0 || ($this->totalRows > 0 && $this->processedRows === $this->totalRows))) {
            $progress = $this->totalRows > 0
                ? min(98, round(5 + (($this->processedRows / $this->totalRows) * 93)))
                : min(95, round(10 + ($this->processedRows * 0.05)));

            \Cache::put("import_progress_{$this->sessionId}", [
                'progress' => $progress,
                'status' => 'processing',
                'message' => "Memproses data: {$this->processedRows}" . ($this->totalRows > 0 ? " dari {$this->totalRows} baris ({$progress}%)" : " baris...")
            ], 3600);
        }

        // Check if NIK exists
        $nik = (string) $this->getValue($row, 'nik');
        if (empty($nik)) {
            $this->skipped++;
            $this->skippedNiks[] = [
                'row' => $this->processedRows,
                'reason' => 'NIK kosong atau tidak valid',
                'nama' => $this->getValue($row, 'nama') ?? 'N/A'
            ];
            return null; // Skip rows without NIK
        }

        // WithUpserts akan handle update/insert otomatis berdasarkan NIK
        return new DataKaryawan([
            // Mapping langsung 1:1 dengan header Excel
            'nik' => (string) $this->getValue($row, 'nik'),
            'nama' => $this->getValue($row, 'nama'),
            'kode_jabatan' => $this->getValue($row, 'kode_jabatan'),

            // Unit Structure
            'unit_deputy_egm' => $this->getValue($row, 'unit_deputy_egm'),
            'unit_assistant_deputy' => $this->getValue($row, 'unit_assistant_deputy'),
            'unit_division_head' => $this->getValue($row, 'unit_division_head'),
            'unit_department_head' => $this->getValue($row, 'unit_department_head'),
            'unit_kerja' => $this->getValue($row, 'unit_kerja'),

            // Job Information
            'jabatan' => $this->getValue($row, 'jabatan'),
            'person_grade' => $this->getValue($row, 'person_grade'),
            'lokasi_kerja' => $this->getValue($row, 'lokasi_kerja'),
            'awal_lokasi_kerja' => $this->getValue($row, 'awal_lokasi_kerja'),
            'status_jabatan' => $this->getValue($row, 'status_jabatan'),
            'sub_status' => $this->getValue($row, 'sub_status'),
            'asal_instansi' => $this->getValue($row, 'asal_instansi'),
            'instansi' => $this->getValue($row, 'instansi'),

            // Personal Information
            'jenis_kelamin' => $this->getValue($row, 'jenis_kelamin'),
            'tanggal_lahir' => $this->convertDate($this->getValue($row, 'tanggal_lahir')),
            'usia' => $this->getValue($row, 'usia'),
            'rencana_mpp' => $this->convertDate($this->getValue($row, 'rencana_mpp')),
            'rencana_pensiun' => $this->convertDate($this->getValue($row, 'rencana_pensiun')),
            'pendidikan_diakui' => $this->getValue($row, 'pendidikan_diakui'),
            'pendidikan_dimiliki' => $this->getValue($row, 'pendidikan_dimiliki'),
            'jurusan' => $this->getValue($row, 'jurusan'),
            'tmt_karyawan' => $this->convertDate($this->getValue($row, 'tmt_karyawan')),
            'masa_kerja' => $this->getValue($row, 'masa_kerja'),
            'tmt_kj_tertinggi' => $this->convertDate($this->getValue($row, 'tmt_kj_tertinggi')),
            'masa_kj_tertinggi_tahun' => $this->getValue($row, 'masa_kj_tertinggi_tahun'),

            // Job Classification
            'sub_keluarga_jabatan' => $this->getValue($row, 'sub_keluarga_jabatan'),
            'keluarga_jabatan' => $this->getValue($row, 'keluarga_jabatan'),
            'fungsi_jabatan' => $this->getValue($row, 'fungsi_jabatan'),
            'jalur_karir' => $this->getValue($row, 'jalur_karir'),
            'jenjang_karir' => $this->getValue($row, 'jenjang_karir'),
            'kelompok_kelas_jabatan' => $this->getValue($row, 'kelompok_kelas_jabatan'),
            'fungsi_pekerjaan' => $this->getValue($row, 'fungsi_pekerjaan'),

            // License Information
            'lisence_dimiliki' => $this->getValue($row, 'lisence_dimiliki'),
            'rating' => $this->getValue($row, 'rating'),
            'no_stkp' => $this->getValue($row, 'no_stkp'),
            'masa_berlaku' => $this->convertDate($this->getValue($row, 'masa_berlaku')),
            'lisence_dibayarkan_januari' => $this->getValue($row, 'lisence_dibayarkan_januari'),

            // Additional Personal Data
            'agama' => $this->getValue($row, 'agama'),
            'nilai_npi_2022' => $this->getValue($row, 'nilai_npi_2022'),
            'kategori' => $this->getValue($row, 'kategori'),
            'no_ktp' => $this->getValue($row, 'no_ktp'),
            'alamat_ktp' => $this->getValue($row, 'alamat_ktp'),
            'no_kontrak' => $this->getValue($row, 'no_kontrak'),
            'email' => $this->getValue($row, 'email'),
            'no_hp' => $this->getValue($row, 'no_hp'),
            'status_pernikahan' => $this->getValue($row, 'status_pernikahan'),
            'generasi' => $this->getValue($row, 'generasi'),

            // Job History & Performance
            'no_sk_jabatan_terakhir' => $this->getValue($row, 'no_sk_jabatan_terakhir'),
            'tgl_sk_jabatan_terakhir' => $this->convertDate($this->getValue($row, 'tgl_sk_jabatan_terakhir')),
            'cek_lisence_serkom' => $this->getValue($row, 'cek_lisence_serkom'),
            'kpi_2023' => $this->getValue($row, 'kpi_2023'),
            'kriteria' => $this->getValue($row, 'kriteria'),
            'fungsi_kontrak_os' => $this->getValue($row, 'fungsi_kontrak_os'),
            'penugasan' => $this->getValue($row, 'penugasan'),
        ]);
    }

    /**
     * Handle errors during import
     */
    public function onError(\Throwable $e)
    {
        $this->errors[] = $e->getMessage();
        \Log::error('Import Error: ' . $e->getMessage());
    }

    /**
     * Handle validation failures
     */
    public function onFailure(Failure ...$failures)
    {
        foreach ($failures as $failure) {
            $this->errors[] = "Row {$failure->row()}: " . implode(', ', $failure->errors());
            \Log::warning("Import Failure - Row {$failure->row()}: " . implode(', ', $failure->errors()));
        }
    }

    /**
     * Get import statistics
     */
    public function getSkippedCount(): int
    {
        return $this->skipped;
    }

    /**
     * Get import errors
     */
    public function getErrors(): array
    {
        return $this->errors;
    }

    /**
     * Get skipped NIKs detail
     */
    public function getSkippedNiks(): array
    {
        return $this->skippedNiks;
    }

    /**
     * Get duplicate NIKs detail (deprecated - now using upsert)
     */
    public function getDuplicateNiks(): array
    {
        return []; // No longer tracking duplicates, using upsert instead
    }

    /**
     * @return int
     */
    public function batchSize(): int
    {
        return 250; // Optimized for 60+ columns (250 x 60 = 15,000 placeholders, well within MySQL limit)
    }

    /**
     * @return int
     */
    public function chunkSize(): int
    {
        return 500; // Optimized chunk reading for memory efficiency
    }

    /**
     * Set total rows for progress calculation
     */
    public function setTotalRows(int $total)
    {
        $this->totalRows = $total;
    }

    /**
     * Unique field untuk upsert (update jika ada, insert jika tidak)
     */
    public function uniqueBy()
    {
        return 'nik';
    }
}