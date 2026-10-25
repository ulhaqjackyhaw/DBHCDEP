<?php

namespace App\Exports;

use App\Models\Version;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;
use Carbon\Carbon;

class VersionDataExport implements FromCollection, WithHeadings, WithMapping
{
    protected $versionId;

    public function __construct($versionId)
    {
        $this->versionId = $versionId;
    }

    public function collection()
    {
        return Version::findOrFail($this->versionId)->history;
    }

    public function headings(): array
    {
        return [
            // Basic Info
            'NIK',
            'Nama',
            'Kode Jabatan',
            // Unit Structure
            'Unit Deputy EGM',
            'Unit Assistant Deputy',
            'Unit Division Head',
            'Unit Department Head',
            'Unit Kerja',
            // Job Information
            'Jabatan',
            'Person Grade',
            'Lokasi Kerja',
            'Awal Lokasi Kerja',
            'Status Jabatan',
            'Sub Status',
            'Asal Instansi',
            'Instansi',
            // Personal Information
            'Jenis Kelamin',
            'Tanggal Lahir',
            'Usia',
            'Rencana MPP',
            'Rencana Pensiun',
            'Pendidikan Diakui',
            'Pendidikan Dimiliki',
            'Jurusan',
            'TMT Karyawan',
            'Masa Kerja',
            'TMT KJ Tertinggi',
            'Masa KJ Tertinggi (Tahun)',
            // Job Classification
            'Sub Keluarga Jabatan',
            'Keluarga Jabatan',
            'Fungsi Jabatan',
            'Jalur Karir',
            'Jenjang Karir',
            'Kelompok Kelas Jabatan',
            'Fungsi Pekerjaan',
            // License Information
            'License Dimiliki',
            'Rating',
            'No STKP',
            'Masa Berlaku',
            'License Dibayarkan Januari',
            // Additional Personal Data
            'Agama',
            'Nilai NPI 2022',
            'Kategori',
            'No KTP',
            'Alamat KTP',
            'No Kontrak',
            'Email',
            'No HP',
            'Status Pernikahan',
            'Generasi',
            // Job History & Performance
            'No SK Jabatan Terakhir',
            'Tgl SK Jabatan Terakhir',
            'Cek License Serkom',
            'KPI 2023',
            'Kriteria',
            'Fungsi Kontrak OS',
            'Penugasan'
        ];
    }

    public function map($employeeHistory): array
    {
        return [
            // Basic Info
            $employeeHistory->nik,
            $employeeHistory->nama,
            $employeeHistory->kode_jabatan,
            // Unit Structure
            $employeeHistory->unit_deputy_egm,
            $employeeHistory->unit_assistant_deputy,
            $employeeHistory->unit_division_head,
            $employeeHistory->unit_department_head,
            $employeeHistory->unit_kerja,
            // Job Information
            $employeeHistory->jabatan,
            $employeeHistory->person_grade,
            $employeeHistory->lokasi_kerja,
            $employeeHistory->awal_lokasi_kerja,
            $employeeHistory->status_jabatan,
            $employeeHistory->sub_status,
            $employeeHistory->asal_instansi,
            $employeeHistory->instansi,
            // Personal Information
            $employeeHistory->jenis_kelamin,
            $employeeHistory->tanggal_lahir,
            $employeeHistory->usia,
            $employeeHistory->rencana_mpp,
            $employeeHistory->rencana_pensiun,
            $employeeHistory->pendidikan_diakui,
            $employeeHistory->pendidikan_dimiliki,
            $employeeHistory->jurusan,
            $employeeHistory->tmt_karyawan,
            $employeeHistory->masa_kerja,
            $employeeHistory->tmt_kj_tertinggi,
            $employeeHistory->masa_kj_tertinggi_tahun,
            // Job Classification
            $employeeHistory->sub_keluarga_jabatan,
            $employeeHistory->keluarga_jabatan,
            $employeeHistory->fungsi_jabatan,
            $employeeHistory->jalur_karir,
            $employeeHistory->jenjang_karir,
            $employeeHistory->kelompok_kelas_jabatan,
            $employeeHistory->fungsi_pekerjaan,
            // License Information
            $employeeHistory->lisence_dimiliki,
            $employeeHistory->rating,
            $employeeHistory->no_stkp,
            $employeeHistory->masa_berlaku,
            $employeeHistory->lisence_dibayarkan_januari,
            // Additional Personal Data
            $employeeHistory->agama,
            $employeeHistory->nilai_npi_2022,
            $employeeHistory->kategori,
            $employeeHistory->no_ktp,
            $employeeHistory->alamat_ktp,
            $employeeHistory->no_kontrak,
            $employeeHistory->email,
            $employeeHistory->no_hp,
            $employeeHistory->status_pernikahan,
            $employeeHistory->generasi,
            // Job History & Performance
            $employeeHistory->no_sk_jabatan_terakhir,
            $employeeHistory->tgl_sk_jabatan_terakhir,
            $employeeHistory->cek_lisence_serkom,
            $employeeHistory->kpi_2023,
            $employeeHistory->kriteria,
            $employeeHistory->fungsi_kontrak_os,
            $employeeHistory->penugasan
        ];
    }
}
