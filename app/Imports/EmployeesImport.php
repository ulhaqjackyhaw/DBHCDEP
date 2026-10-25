<?php

namespace App\Imports;

use App\Models\Employee;
use Maatwebsite\Excel\Concerns\ToModel;
use Maatwebsite\Excel\Concerns\WithHeadingRow;
use Maatwebsite\Excel\Concerns\WithUpserts;
use Maatwebsite\Excel\Concerns\WithValidation; // Tambahkan ini
use Maatwebsite\Excel\Concerns\SkipsOnFailure; // Tambahkan ini
use Maatwebsite\Excel\Validators\Failure; // Tambahkan ini

class EmployeesImport implements
    ToModel,
    WithHeadingRow,
    WithUpserts,
    WithValidation,
    SkipsOnFailure
{
    /**
     * Tentukan kolom unik untuk proses update/insert.
     * Laravel akan mencari karyawan berdasarkan 'nik' untuk di-update.
     */
    public function uniqueBy()
    {
        return 'nik';
    }

    public function model(array $row)
    {
        // Abaikan baris jika 'nik' kosong
        if (empty($row['nik'])) {
            return null;
        }

        return new Employee([
            'NIK' => $row['nik'],
            'Nama' => $row['nama'],
            'GENDER' => $row['gender'],
            'UNIT' => $row['unit'],
            'JABATAN' => $row['jabatan'],
            'KELOMPOK_KELAS_JABATAN' => $row['kelompok_kelas_jabatan'],
            'GRADE' => $row['grade'],
            'STATUS_KEPEGAWAIAN' => $row['status_kepegawaian'],
            'USIA' => $row['usia'],
            'PENDIDIKAN' => $row['pendidikan'],
            'MASA_KERJA' => $row['masa_kerja'],
        ]);
    }

    /**
     * Tentukan aturan validasi untuk setiap baris.
     */
    public function rules(): array
    {
        return [
            'nik' => 'required|numeric',
            'nama' => 'required|string',
            // Tambahkan aturan lain jika perlu
        ];
    }

    /**
     * Fungsi ini akan dipanggil jika validasi gagal,
     * dan akan melewati baris yang error tersebut.
     */
    public function onFailure(Failure ...$failures)
    {
        // Anda bisa menambahkan logging di sini jika ingin tahu baris mana yang gagal
        // Log::error('Baris gagal diimpor: ' . $failures[0]->row());
    }
}