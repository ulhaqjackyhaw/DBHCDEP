<?php

namespace App\Exports;

use Maatwebsite\Excel\Concerns\FromArray;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithStyles;
use Maatwebsite\Excel\Concerns\WithColumnWidths;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;
use PhpOffice\PhpSpreadsheet\Style\Fill;
use PhpOffice\PhpSpreadsheet\Style\Alignment;
use PhpOffice\PhpSpreadsheet\Style\Border;

class DataKaryawanTemplateExport implements FromArray, WithHeadings, WithStyles, WithColumnWidths
{
    /**
     * @return array
     */
    public function array(): array
    {
        // Example rows for template
        return [
            [
                '21234567', // NIK
                'Abyasa Hutagalung', // NAMA
                'CGKD01AD02DIV03DEP008J0085', // KODE JABATAN
                'Airport Operation & Security Services', // UNIT DEPUTY EGM
                'Airport Rescue & Fire Fighting', // UNIT ASSISTANT DEPUTY
                'Airport Rescue & Fire Fighting Operation Performance', // UNIT DIVISION HEAD
                'Building Rescue & Fire Fighting', // UNIT DEPARTMENT HEAD
                'Building Rescue & Fire Fighting', // UNIT KERJA
                'Building Rescue & Firefighting Basic Firefighter', // JABATAN
                '-', // PERSON GRADE
                'CGK', // LOKASI KERJA
                'PT Angkasa Pura Indonesia', // AWAL LOKASI KERJA
                'KARYAWAN', // STATUS JABATAN
                'ALIH DAYA', // SUB STATUS
                '-', // ASAL INSTANSI
                'PT IAS SUPPORT INDONESIA', // INSTANSI
                'PEREMPUAN', // JENIS KELAMIN
                '16/02/1994', // TANGGAL LAHIR
                '31', // USIA
                '01 March 2051', // RENCANA MPP
                '01 August 2052', // RENCANA PENSIUN
                'D2', // PENDIDIKAN DIAKUI
                'SMP', // PENDIDIKAN DIMILIKI
                'Teknik Informatika', // JURUSAN
                '01/10/2023', // TMT KARYAWAN
                '2', // MASA KERJA
                '01/10/2023', // TMT KJ TERTINGGI
                '2', // MASA KJ TERTINGGI (TAHUN)
                'ARFF', // SUB KELUARGA JABATAN
                'AIRPORT OPERATION', // KELUARGA JABATAN
                'Operation & Services', // FUNGSI JABATAN
                'Non Manajerial', // JALUR KARIR
                '-', // JENJANG KARIR
                '-', // KELOMPOK KELAS JABATAN
                'Operasional', // FUNGSI PEKERJAAN
                '-', // LISENCE DIMILIKI
                '87', // RATING
                '-', // NO STKP
                '-', // MASA BERLAKU
                '-', // LISENCE DIBAYARKAN JANUARI
                'BUDDHA', // AGAMA
                '87', // NILAI NPI 2022
                '-', // KATEGORI
                '3507900000000001', // NO KTP
                'Jakarta', // ALAMAT KTP
                '-', // NO KONTRAK
                'agus.maharani@ap1.co.id', // EMAIL
                '085580173457', // NO HP
                'MENIKAH', // STATUS PERNIKAHAN
                'GEN X', // GENERASI
                'SK-901/AP1/2025', // NO SK JABATAN TERAKHIR
                '07-Sep-2024', // TGL SK JABATAN TERAKHIR
                '-', // CEK LISENCE/SERKOM
                '87', // KPI 2023
                '', // KRITERIA
                'ARFF', // FUNGSI KONTRAK OS
                '-', // PENUGASAN
            ],
            [
                '27654321', // NIK
                'drg. Adikara Laksmiwati, S.Pt', // NAMA
                'HLPD00AD00DIV00AM006J1113', // KODE JABATAN
                '-', // UNIT DEPUTY EGM
                '-', // UNIT ASSISTANT DEPUTY
                '-', // UNIT DIVISION HEAD
                'Infrastructure & Maintenance', // UNIT DEPARTMENT HEAD
                'Infrastructure & Maintenance', // UNIT KERJA
                'Airside Infrastructure & Accessibility Supervisor', // JABATAN
                '12', // PERSON GRADE
                'HLP', // LOKASI KERJA
                'KC Bandara Halim Perdanakusuma', // AWAL LOKASI KERJA
                'KARYAWAN', // STATUS JABATAN
                'KP', // SUB STATUS
                'PT Angkasa Pura II', // ASAL INSTANSI
                'PT ANGKASA PURA INDONESIA', // INSTANSI
                'LAKI-LAKI', // JENIS KELAMIN
                '07/11/1998', // TANGGAL LAHIR
                '103', // USIA
                '26 March 2030', // RENCANA MPP
                '26 January 2031', // RENCANA PENSIUN
                'S3', // PENDIDIKAN DIAKUI
                'S2', // PENDIDIKAN DIMILIKI
                'Teknik Informatika', // JURUSAN
                '01/10/2024', // TMT KARYAWAN
                '2', // MASA KERJA
                '01/10/2024', // TMT KJ TERTINGGI
                '2', // MASA KJ TERTINGGI (TAHUN)
                'Airport Security', // SUB KELUARGA JABATAN
                'AIRPORT OPERATION', // KELUARGA JABATAN
                'Administration', // FUNGSI JABATAN
                'Manajerial', // JALUR KARIR
                '-', // JENJANG KARIR
                'BOD-3', // KELOMPOK KELAS JABATAN
                'Administrasi', // FUNGSI PEKERJAAN
                '-', // LISENCE DIMILIKI
                '87', // RATING
                '-', // NO STKP
                '-', // MASA BERLAKU
                '-', // LISENCE DIBAYARKAN JANUARI
                'HINDU', // AGAMA
                '87', // NILAI NPI 2022
                '-', // KATEGORI
                '3507900000000002', // NO KTP
                'Jakarta', // ALAMAT KTP
                '-', // NO KONTRAK
                'agus.maharani@ap1.co.id', // EMAIL
                '85580173452', // NO HP
                'MENIKAH', // STATUS PERNIKAHAN
                'GEN X', // GENERASI
                'SK-901/AP1/2026', // NO SK JABATAN TERAKHIR
                '07-Sep-2025', // TGL SK JABATAN TERAKHIR
                '-', // CEK LISENCE/SERKOM
                '87', // KPI 2023
                '', // KRITERIA
                '-', // FUNGSI KONTRAK OS
                '-', // PENUGASAN
            ],
        ];
    }

    /**
     * @return array
     */
    public function headings(): array
    {
        return [
            'NIK',
            'NAMA',
            'KODE JABATAN',
            'UNIT DEPUTY EGM',
            'UNIT ASSISTANT DEPUTY',
            'UNIT DIVISION HEAD',
            'UNIT DEPARTMENT HEAD',
            'UNIT KERJA',
            'JABATAN',
            'PERSON GRADE',
            'LOKASI KERJA',
            'AWAL LOKASI KERJA',
            'STATUS JABATAN',
            'SUB STATUS',
            'ASAL INSTANSI',
            'INSTANSI',
            'JENIS KELAMIN',
            'TANGGAL LAHIR',
            'USIA',
            'RENCANA MPP',
            'RENCANA PENSIUN',
            'PENDIDIKAN DIAKUI',
            'PENDIDIKAN DIMILIKI',
            'JURUSAN',
            'TMT KARYAWAN',
            'MASA KERJA',
            'TMT KJ TERTINGGI',
            'MASA KJ TERTINGGI (TAHUN)',
            'SUB KELUARGA JABATAN',
            'KELUARGA JABATAN',
            'FUNGSI JABATAN',
            'JALUR KARIR',
            'JENJANG KARIR',
            'KELOMPOK KELAS JABATAN',
            'FUNGSI PEKERJAAN',
            'LISENCE DIMILIKI',
            'RATING',
            'NO STKP',
            'MASA BERLAKU',
            'LISENCE DIBAYARKAN JANUARI',
            'AGAMA',
            'NILAI NPI 2022',
            'KATEGORI',
            'NO KTP',
            'ALAMAT KTP',
            'NO KONTRAK',
            'EMAIL',
            'NO HP',
            'STATUS PERNIKAHAN',
            'GENERASI',
            'NO SK JABATAN TERAKHIR',
            'TGL SK JABATAN TERAKHIR',
            'CEK LISENCE/SERKOM',
            'KPI 2023',
            'KRITERIA',
            'FUNGSI KONTRAK OS',
            'PENUGASAN',
        ];
    }

    public function styles(Worksheet $sheet)
    {
        // Style header row - update range to include all columns (A to BE = 57 columns)
        $sheet->getStyle('A1:BE1')->applyFromArray([
            'font' => [
                'bold' => true,
                'size' => 12,
                'color' => ['rgb' => 'FFFFFF'],
            ],
            'fill' => [
                'fillType' => Fill::FILL_SOLID,
                'startColor' => ['rgb' => '059669'],
            ],
            'alignment' => [
                'horizontal' => Alignment::HORIZONTAL_CENTER,
                'vertical' => Alignment::VERTICAL_CENTER,
            ],
            'borders' => [
                'allBorders' => [
                    'borderStyle' => Border::BORDER_THIN,
                    'color' => ['rgb' => '000000'],
                ],
            ],
        ]);

        // Style example rows
        $sheet->getStyle('A2:BE3')->applyFromArray([
            'fill' => [
                'fillType' => Fill::FILL_SOLID,
                'startColor' => ['rgb' => 'FEF3C7'],
            ],
            'borders' => [
                'allBorders' => [
                    'borderStyle' => Border::BORDER_THIN,
                    'color' => ['rgb' => 'E5E7EB'],
                ],
            ],
            'alignment' => [
                'vertical' => Alignment::VERTICAL_CENTER,
            ],
            'font' => [
                'italic' => true,
                'color' => ['rgb' => '92400E'],
            ],
        ]);

        // Center align specific columns
        $sheet->getStyle('A2:A3')->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);
        $sheet->getStyle('C2:C3')->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);
        $sheet->getStyle('I2:I3')->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);

        return [];
    }

    /**
     * @return array
     */
    public function columnWidths(): array
    {
        return [
            'A' => 15,  // NIK
            'B' => 30,  // NAMA
            'C' => 25,  // KODE JABATAN
            'D' => 25,  // UNIT DEPUTY EGM
            'E' => 25,  // UNIT ASSISTANT DEPUTY
            'F' => 25,  // UNIT DIVISION HEAD
            'G' => 25,  // UNIT DEPARTMENT HEAD
            'H' => 35,  // UNIT KERJA
            'I' => 40,  // JABATAN
            'J' => 12,  // PERSON GRADE
            'K' => 35,  // LOKASI KERJA
            'L' => 35,  // AWAL LOKASI KERJA
            'M' => 20,  // STATUS JABATAN
            'N' => 18,  // SUB STATUS
            'O' => 30,  // ASAL INSTANSI
            'P' => 30,  // INSTANSI
            'Q' => 15,  // JENIS KELAMIN
            'R' => 15,  // TANGGAL LAHIR
            'S' => 10,  // USIA
            'T' => 15,  // RENCANA MPP
            'U' => 15,  // RENCANA PENSIUN
            'V' => 20,  // PENDIDIKAN DIAKUI
            'W' => 20,  // PENDIDIKAN DIMILIKI
            'X' => 20,  // JURUSAN
            'Y' => 15,  // TMT KARYAWAN
            'Z' => 15,  // MASA KERJA
            'AA' => 15, // TMT KJ TERTINGGI
            'AB' => 18, // MASA KJ TERTINGGI (TAHUN)
            'AC' => 25, // SUB KELUARGA JABATAN
            'AD' => 25, // KELUARGA JABATAN
            'AE' => 25, // FUNGSI JABATAN
            'AF' => 20, // JALUR KARIR
            'AG' => 20, // JENJANG KARIR
            'AH' => 28, // KELOMPOK KELAS JABATAN
            'AI' => 25, // FUNGSI PEKERJAAN
            'AJ' => 30, // LISENCE DIMILIKI
            'AK' => 12, // RATING
            'AL' => 18, // NO STKP
            'AM' => 15, // MASA BERLAKU
            'AN' => 25, // LISENCE DIBAYARKAN JANUARI
            'AO' => 12, // AGAMA
            'AP' => 15, // NILAI NPI 2022
            'AQ' => 15, // KATEGORI
            'AR' => 18, // NO KTP
            'AS' => 40, // ALAMAT KTP
            'AT' => 18, // NO KONTRAK
            'AU' => 30, // EMAIL
            'AV' => 15, // NO HP
            'AW' => 18, // STATUS PERNIKAHAN
            'AX' => 15, // GENERASI
            'AY' => 25, // NO SK JABATAN TERAKHIR
            'AZ' => 18, // TGL SK JABATAN TERAKHIR
            'BA' => 20, // CEK LISENCE/SERKOM
            'BB' => 12, // KPI 2023
            'BC' => 15, // KRITERIA
            'BD' => 20, // FUNGSI KONTRAK OS
            'BE' => 20, // PENUGASAN
        ];
    }
}