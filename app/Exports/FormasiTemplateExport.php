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

class FormasiTemplateExport implements FromArray, WithHeadings, WithStyles, WithColumnWidths
{
    /**
     * @return array
     */
    public function array(): array
    {
        // Return 2 example rows for template
        return [
            [
                'HLPDCONTOHDIV00AM004J1101',
                'Corporate Unit',
                'Corporate Secretary',
                'Communications Division',
                'External Communications Department',
                'KC Bandara Halim Perdanakusuma',
                'Airport Rescue & Fire Fighting',
                'Airport Rescue & Fire Fighting Chief',
                'BOD-4',
                '11',
                '1',
            ],
            [
                'CONTOHREGD008AD00DIV58DEP000J1372',
                'Regional Unit',
                'Regional Support',
                'Finance & Accounting Division',
                'Regional Accounting Department',
                'Regional 1',
                'Accounting',
                'Regional Accounting Division Head',
                'BOD-2',
                '16',
                '1',
            ],
        ];
    }

    /**
     * @return array
     */
    public function headings(): array
    {
        return [
            'KODE JABATAN',
            'UNIT DEPUTY EGM',
            'UNIT ASSISTANT DEPUTY',
            'UNIT DIVISION HEAD',
            'UNIT DEPARTMENT HEAD',
            'LOKASI KERJA',
            'UNIT KERJA',
            'JABATAN',
            'KELOMPOK KELAS JABATAN',
            'GRADE',
            'KUOTA',
        ];
    }

    /**
     * @param Worksheet $sheet
     * @return array
     */
    public function styles(Worksheet $sheet)
    {
        // Style header row
        $sheet->getStyle('A1:K1')->applyFromArray([
            'font' => [
                'bold' => true,
                'size' => 12,
                'color' => ['rgb' => 'FFFFFF'],
            ],
            'fill' => [
                'fillType' => Fill::FILL_SOLID,
                'startColor' => ['rgb' => '3B82F6'],
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
        $sheet->getStyle('A2:K3')->applyFromArray([
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
        $sheet->getStyle('I2:I3')->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);
        $sheet->getStyle('J2:J3')->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);
        $sheet->getStyle('K2:K3')->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);

        return [];
    }

    /**
     * @return array
     */
    public function columnWidths(): array
    {
        return [
            'A' => 40,  // KODE JABATAN
            'B' => 30,  // UNIT DEPUTY EGM
            'C' => 30,  // UNIT ASSISTANT DEPUTY
            'D' => 30,  // UNIT DIVISION HEAD
            'E' => 30,  // UNIT DEPARTMENT HEAD
            'F' => 40,  // LOKASI KERJA
            'G' => 35,  // UNIT KERJA
            'H' => 40,  // JABATAN
            'I' => 30,  // KELOMPOK KELAS JABATAN
            'J' => 12,  // GRADE
            'K' => 12,  // KUOTA
        ];
    }
}