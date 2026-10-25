<?php

namespace App\Exports;

use App\Models\DataKaryawan;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;
use Maatwebsite\Excel\Concerns\WithStyles;
use Maatwebsite\Excel\Concerns\WithColumnWidths;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;
use PhpOffice\PhpSpreadsheet\Style\Fill;
use PhpOffice\PhpSpreadsheet\Style\Alignment;
use PhpOffice\PhpSpreadsheet\Style\Border;

class DataKaryawanExport implements FromCollection, WithHeadings, WithMapping, WithStyles, WithColumnWidths
{
    /**
     * @return \Illuminate\Support\Collection
     */
    public function collection()
    {
        return DataKaryawan::orderBy('nama')
            ->orderBy('lokasi_kerja')
            ->orderBy('unit_kerja')
            ->get();
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

    /**
     * @param mixed $dataKaryawan
     * @return array
     */
    public function map($dataKaryawan): array
    {
        return [
            $dataKaryawan->nik,
            $dataKaryawan->nama,
            $dataKaryawan->kode_jabatan,
            $dataKaryawan->unit_deputy_egm,
            $dataKaryawan->unit_assistant_deputy,
            $dataKaryawan->unit_division_head,
            $dataKaryawan->unit_department_head,
            $dataKaryawan->unit_kerja,
            $dataKaryawan->jabatan,
            $dataKaryawan->person_grade,
            $dataKaryawan->lokasi_kerja,
            $dataKaryawan->awal_lokasi_kerja,
            $dataKaryawan->status_jabatan,
            $dataKaryawan->sub_status,
            $dataKaryawan->asal_instansi,
            $dataKaryawan->instansi,
            $dataKaryawan->jenis_kelamin,
            $dataKaryawan->tanggal_lahir,
            $dataKaryawan->usia,
            $dataKaryawan->rencana_mpp,
            $dataKaryawan->rencana_pensiun,
            $dataKaryawan->pendidikan_diakui,
            $dataKaryawan->pendidikan_dimiliki,
            $dataKaryawan->jurusan,
            $dataKaryawan->tmt_karyawan,
            $dataKaryawan->masa_kerja,
            $dataKaryawan->tmt_kj_tertinggi,
            $dataKaryawan->masa_kj_tertinggi_tahun,
            $dataKaryawan->sub_keluarga_jabatan,
            $dataKaryawan->keluarga_jabatan,
            $dataKaryawan->fungsi_jabatan,
            $dataKaryawan->jalur_karir,
            $dataKaryawan->jenjang_karir,
            $dataKaryawan->kelompok_kelas_jabatan,
            $dataKaryawan->fungsi_pekerjaan,
            $dataKaryawan->lisence_dimiliki,
            $dataKaryawan->rating,
            $dataKaryawan->no_stkp,
            $dataKaryawan->masa_berlaku,
            $dataKaryawan->lisence_dibayarkan_januari,
            $dataKaryawan->agama,
            $dataKaryawan->nilai_npi_2022,
            $dataKaryawan->kategori,
            $dataKaryawan->no_ktp,
            $dataKaryawan->alamat_ktp,
            $dataKaryawan->no_kontrak,
            $dataKaryawan->email,
            $dataKaryawan->no_hp,
            $dataKaryawan->status_pernikahan,
            $dataKaryawan->generasi,
            $dataKaryawan->no_sk_jabatan_terakhir,
            $dataKaryawan->tgl_sk_jabatan_terakhir,
            $dataKaryawan->cek_lisence_serkom,
            $dataKaryawan->kpi_2023,
            $dataKaryawan->kriteria,
            $dataKaryawan->fungsi_kontrak_os,
            $dataKaryawan->penugasan,
        ];
    }

    /**
     * @param Worksheet $sheet
     * @return array
     */
    public function styles(Worksheet $sheet)
    {
        $highestRow = $sheet->getHighestRow();
        $highestColumn = $sheet->getHighestColumn();

        // Style header row
        $sheet->getStyle('A1:' . $highestColumn . '1')->applyFromArray([
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

        // Style data rows with alternating colors
        for ($row = 2; $row <= $highestRow; $row++) {
            $fillColor = ($row % 2 == 0) ? 'F0FDF4' : 'FFFFFF';
            $sheet->getStyle('A' . $row . ':' . $highestColumn . $row)->applyFromArray([
                'fill' => [
                    'fillType' => Fill::FILL_SOLID,
                    'startColor' => ['rgb' => $fillColor],
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
            ]);
        }

        // Center align specific columns (NIK, Jenis Kelamin, Grade, Usia, Masa Kerja)
        $sheet->getStyle('A2:A' . $highestRow)->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);
        $sheet->getStyle('T2:T' . $highestRow)->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);
        $sheet->getStyle('V2:V' . $highestRow)->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);
        $sheet->getStyle('AM2:AM' . $highestRow)->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);
        $sheet->getStyle('K2:K' . $highestRow)->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);
        $sheet->getStyle('L2:L' . $highestRow)->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);

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
            'J' => 15,  // TMT JABATAN
            'K' => 12,  // JOB GRADE
            'L' => 12,  // PERSON GRADE
            'M' => 35,  // LOKASI KERJA
            'N' => 35,  // AWAL LOKASI KERJA
            'O' => 20,  // STATUS
            'P' => 20,  // STATUS JABATAN
            'Q' => 18,  // SUB STATUS
            'R' => 30,  // ASAL INSTANSI
            'S' => 30,  // INSTANSI
            'T' => 15,  // JENIS KELAMIN
            'U' => 15,  // TANGGAL LAHIR
            'V' => 10,  // USIA
            'W' => 15,  // RENCANA MPP
            'X' => 15,  // RENCANA PENSIUN
            'Y' => 20,  // PENDIDIKAN DIAKUI
            'Z' => 20,  // PENDIDIKAN DIMILIKI
            'AA' => 15, // TMT KARYAWAN
            'AB' => 15, // MASA KERJA
            'AC' => 15, // TMT
            'AD' => 15, // TMT KJ TERTINGGI
            'AE' => 18, // MASA KJ TERTINGGI (TAHUN)
            'AF' => 25, // SUB KELUARGA JABATAN
            'AG' => 25, // KELUARGA JABATAN
            'AH' => 25, // FUNGSI JABATAN
            'AI' => 20, // JALUR KARIR
            'AJ' => 20, // JENJANG KARIR
            'AK' => 28, // KELOMPOK KELAS JABATAN
            'AL' => 25, // FUNGSI PEKERJAAN
            'AM' => 10, // GRADE
            'AN' => 30, // LISENCE DIMILIKI
            'AO' => 12, // RATING
            'AP' => 18, // NO STKP
            'AQ' => 15, // MASA BERLAKU
            'AR' => 25, // LISENCE DIBAYARKAN JANUARI
            'AS' => 20, // JURUSAN
            'AT' => 12, // AGAMA
            'AU' => 15, // NILAI NPI 2022
            'AV' => 15, // KATEGORI
            'AW' => 18, // NO KTP
            'AX' => 40, // ALAMAT KTP
            'AY' => 18, // NO KONTRAK
            'AZ' => 30, // EMAIL
            'BA' => 15, // NO HP
            'BB' => 18, // STATUS PERNIKAHAN
            'BC' => 15, // GENERASI
            'BD' => 25, // NO SK JABATAN TERAKHIR
            'BE' => 18, // TGL SK JABATAN TERAKHIR
            'BF' => 20, // CEK LISENCE/SERKOM
            'BG' => 12, // KPI 2023
            'BH' => 15, // KRITERIA
            'BI' => 20, // FUNGSI KONTRAK OS
        ];
    }
}