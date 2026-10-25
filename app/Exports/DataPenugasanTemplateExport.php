<?php

namespace App\Exports;

use Maatwebsite\Excel\Concerns\FromArray;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithStyles;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;

class DataPenugasanTemplateExport implements FromArray, WithHeadings, WithStyles
{
    /**
     * @return array
     */
    public function array(): array
    {
        // Return empty array with one example row
        return [
            [
                '20245985',
                'CONTOH Alfi',
                '10',
                'CONTOH Security Quality Control Staff',
                'CONTOH Security Quality Control',
                'CONTOH CGK',
                'CONTOH Airport Operation & Services',
                'CONTOH REG I',
                'CONTOH SPR.CGR.CEO.196/KP.04.01/2025',
                '24/10/2025',
                '23/01/2026',
                'CONTOH Pipit',
                'CONTOH II',
            ]
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
            'KJ',
            'JABATAN DEFINITIF',
            'UNIT DEFINITIF',
            'LOKASI DEFINITIF',
            'UNIT PENUGASAN',
            'LOKASI PENUGASAN',
            'NOMOR SPRINT',
            'TANGGAL MULAI',
            'TANGGAL SELESAI',
            'PIC',
            'KETERANGAN',
        ];
    }

    /**
     * @param Worksheet $sheet
     */
    public function styles(Worksheet $sheet)
    {
        return [
            1 => [
                'font' => ['bold' => true],
                'fill' => [
                    'fillType' => \PhpOffice\PhpSpreadsheet\Style\Fill::FILL_SOLID,
                    'startColor' => ['rgb' => '4472C4']
                ],
                'font' => ['color' => ['rgb' => 'FFFFFF'], 'bold' => true]
            ],
        ];
    }
}
