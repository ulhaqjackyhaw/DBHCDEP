<?php

namespace App\Imports;

use App\Models\Realisasi;
use Maatwebsite\Excel\Concerns\ToModel;
use Maatwebsite\Excel\Concerns\WithHeadingRow;
use Maatwebsite\Excel\Concerns\Importable;
use Maatwebsite\Excel\Concerns\WithBatchInserts;
use Maatwebsite\Excel\Concerns\WithChunkReading;
use Maatwebsite\Excel\Concerns\SkipsEmptyRows;
use Maatwebsite\Excel\Concerns\WithValidation;

class RealisasiImport implements ToModel, WithHeadingRow, WithBatchInserts, WithChunkReading, SkipsEmptyRows, WithValidation
{
    use Importable;

    protected $tahun;
    protected $mode;

    public function __construct($tahun = null, $mode = 'add')
    {
        $this->tahun = $tahun ?? date('Y');
        $this->mode = $mode;
    }

    /**
     * @param array $row
     *
     * @return \Illuminate\Database\Eloquent\Model|null
     */
    public function model(array $row)
    {
        $programKerja = $row['program_kerja'] ?? $row['program kerja'] ?? null;

        if (empty($programKerja)) {
            return null; // Skip empty rows
        }

        // Normalisasi program_kerja: trim dan lowercase untuk pengecekan
        $programKerjaNormalized = trim(strtolower($programKerja));

        $data = [
            'program_kerja' => trim($programKerja), // Simpan yang asli tapi di-trim
            'tahun' => $this->tahun,
            'rkap' => $this->parseNumber($row['rkap'] ?? 0),
            'realisasi_jan_mar' => $this->parseNumber($row['realisasi_jan_mar'] ?? $row['realisasi jan mar'] ?? 0),
            'realisasi_jan_jun' => $this->parseNumber($row['realisasi_jan_jun'] ?? $row['realisasi jan jun'] ?? 0),
            'realisasi_jul_sep' => $this->parseNumber($row['realisasi_jul_sep'] ?? $row['realisasi jul sep'] ?? 0),
            'realisasi_jul_des' => $this->parseNumber($row['realisasi_jul_des'] ?? $row['realisasi jul des'] ?? 0),
            'total' => $this->parseNumber($row['total'] ?? 0),
        ];

        // Mode Ganti Semua: Langsung buat data baru (data lama sudah dihapus di controller)
        if ($this->mode === 'replace') {
            return new Realisasi($data);
        }

        // Mode Tambah: HANYA tambah data baru, TIDAK update data yang sudah ada
        // Cek apakah data dengan program kerja yang sama (case-insensitive) sudah ada
        $exists = Realisasi::where('tahun', $this->tahun)
            ->whereRaw('LOWER(TRIM(program_kerja)) = ?', [$programKerjaNormalized])
            ->exists();

        if ($exists) {
            // Skip data yang sudah ada (jangan update, jangan insert)
            return null;
        }

        // Buat data baru (hanya jika belum ada)
        return new Realisasi($data);
    }

    /**
     * Parse number from string (handle comma and dot separators)
     */
    private function parseNumber($value)
    {
        if (empty($value)) {
            return 0;
        }

        // Remove thousand separators (dots or commas)
        $value = str_replace(['.', ','], ['', '.'], $value);

        return (float) $value;
    }

    /**
     * @return int
     */
    public function batchSize(): int
    {
        return 1000;
    }

    /**
     * @return int
     */
    public function chunkSize(): int
    {
        return 1000;
    }

    /**
     * @return array
     */
    public function rules(): array
    {
        return [
            'program_kerja' => 'required|string',
            'rkap' => 'nullable|numeric',
            'realisasi_jan_mar' => 'nullable|numeric',
            'realisasi_jan_jun' => 'nullable|numeric',
            'realisasi_jul_sep' => 'nullable|numeric',
            'realisasi_jul_des' => 'nullable|numeric',
            'total' => 'nullable|numeric',
        ];
    }

    /**
     * @return array
     */
    public function customValidationMessages()
    {
        return [
            'program_kerja.required' => 'Program Kerja wajib diisi',
            'rkap.numeric' => 'RKAP harus berupa angka',
            'realisasi_jan_mar.numeric' => 'Realisasi Jan-Mar harus berupa angka',
            'realisasi_jan_jun.numeric' => 'Realisasi Jan-Jun harus berupa angka',
            'realisasi_jul_sep.numeric' => 'Realisasi Jul-Sep harus berupa angka',
            'realisasi_jul_des.numeric' => 'Realisasi Jul-Des harus berupa angka',
            'total.numeric' => 'Total harus berupa angka',
        ];
    }
}
