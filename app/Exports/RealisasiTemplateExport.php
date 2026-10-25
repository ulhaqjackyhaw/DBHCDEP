<?php

namespace App\Exports;

use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithStyles;
use Maatwebsite\Excel\Concerns\WithColumnWidths;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;
use PhpOffice\PhpSpreadsheet\Style\NumberFormat;
use Maatwebsite\Excel\Concerns\WithColumnFormatting;
use Illuminate\Support\Collection;
use PhpOffice\PhpSpreadsheet\Style\Fill;
use PhpOffice\PhpSpreadsheet\Style\Alignment;
use PhpOffice\PhpSpreadsheet\Style\Border;

class RealisasiTemplateExport implements FromCollection, WithHeadings, WithStyles, WithColumnWidths, WithColumnFormatting
{
    protected $tahun;

    public function __construct($tahun = null)
    {
        $this->tahun = $tahun ?? date('Y');
    }

    /**
     * @return \Illuminate\Support\Collection
     */
    public function collection()
    {
        // Return empty collection with sample data for template
        return new Collection([
            [
                'CONTOH Pengembangan SDM',
                0.00,
                0.00,
                0.00,
                0.00,
                0.00,
                0.00,
            ],
            [
                'CONTOH Peningkatan Infrastruktur',
                0.00,
                0.00,
                0.00,
                0.00,
                0.00,
                0.00,
            ],
            [
                'CONTOH Sistem Informasi',
                0.00,
                0.00,
                0.00,
                0.00,
                0.00,
                0.00,
            ],
        ]);
    }

    /**
     * @return array
     */
    public function headings(): array
    {
        return [
            'PROGRAM KERJA',
            'RKAP',
            'REALISASI JAN-MAR',
            'REALISASI JAN-JUN',
            'REALISASI JUL-SEP',
            'REALISASI JUL-DES',
            'TOTAL',
        ];
    }

    /**
     * @param Worksheet $sheet
     * @return array
     */
    public function styles(Worksheet $sheet)
    {
        // Style header row
        $sheet->getStyle('A1:G1')->applyFromArray([
            'font' => [
                'bold' => true,
                'size' => 12,
                'color' => ['rgb' => 'FFFFFF'],
            ],
            'fill' => [
                'fillType' => Fill::FILL_SOLID,
                'startColor' => ['rgb' => 'DC2626'],
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
        $sheet->getStyle('A2:G4')->applyFromArray([
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

        // Right align numeric columns
        $sheet->getStyle('B2:G4')->getAlignment()->setHorizontal(Alignment::HORIZONTAL_RIGHT);

        return [];
    }

    /**
     * @return array
     */
    public function columnWidths(): array
    {
        return [
            'A' => 50,  // Program Kerja
            'B' => 18,  // RKAP
            'C' => 20,  // Realisasi Jan-Mar
            'D' => 20,  // Realisasi Jan-Jun
            'E' => 20,  // Realisasi Jul-Sep
            'F' => 20,  // Realisasi Jul-Des
            'G' => 20,  // Total
        ];
    }

    /**
     * @return array
     */
    public function columnFormats(): array
    {
        return [
            'B' => NumberFormat::FORMAT_NUMBER_COMMA_SEPARATED1, // RKAP
            'C' => NumberFormat::FORMAT_NUMBER_COMMA_SEPARATED1, // Realisasi Jan-Mar
            'D' => NumberFormat::FORMAT_NUMBER_COMMA_SEPARATED1, // Realisasi Jan-Jun
            'E' => NumberFormat::FORMAT_NUMBER_COMMA_SEPARATED1, // Realisasi Jul-Sep
            'F' => NumberFormat::FORMAT_NUMBER_COMMA_SEPARATED1, // Realisasi Jul-Des
            'G' => NumberFormat::FORMAT_NUMBER_COMMA_SEPARATED1, // Total
        ];
    }
}
