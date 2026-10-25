<?php

namespace App\Exports;

use App\Models\Realisasi;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;
use Maatwebsite\Excel\Concerns\WithStyles;
use Maatwebsite\Excel\Concerns\WithColumnWidths;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;
use PhpOffice\PhpSpreadsheet\Style\NumberFormat;
use Maatwebsite\Excel\Concerns\WithColumnFormatting;
use PhpOffice\PhpSpreadsheet\Style\Fill;
use PhpOffice\PhpSpreadsheet\Style\Alignment;
use PhpOffice\PhpSpreadsheet\Style\Border;

class RealisasiExport implements FromCollection, WithHeadings, WithMapping, WithStyles, WithColumnWidths, WithColumnFormatting
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
        return Realisasi::where('tahun', $this->tahun)
            ->orderBy('program_kerja')
            ->get();
    }

    /**
     * @return array
     */
    public function headings(): array
    {
        return [
            'No',
            'PROGRAM KERJA',
            'TAHUN',
            'RKAP ' . $this->tahun,
            'REALISASI JAN-MAR',
            'REALISASI JAN-JUN',
            'REALISASI JUL-SEP',
            'REALISASI JUL-DES',
            'TOTAL',
            'ACHIEVEMENT S1 (%)',
            'ACHIEVEMENT S2 (%)',
            'ACHIEVEMENT ' . $this->tahun . ' (%)',
        ];
    }

    /**
     * @param mixed $realisasi
     * @return array
     */
    public function map($realisasi): array
    {
        static $no = 0;
        $no++;

        // Hitung achievement
        $achS1 = 0;
        $achS2 = 0;
        $achYear = 0;

        if ($realisasi->rkap > 0) {
            // Return fractional values (e.g. 0.1667) so Excel can format as percent
            $achS1 = ($realisasi->realisasi_jan_jun / $realisasi->rkap);
            $achS2 = ($realisasi->realisasi_jul_des / $realisasi->rkap);
            $achYear = (($realisasi->realisasi_jan_jun + $realisasi->realisasi_jul_des) / $realisasi->rkap);
        }

        return [
            $no,
            $realisasi->program_kerja,
            $realisasi->tahun,
            (float) $realisasi->rkap,
            (float) $realisasi->realisasi_jan_mar,
            (float) $realisasi->realisasi_jan_jun,
            (float) $realisasi->realisasi_jul_sep,
            (float) $realisasi->realisasi_jul_des,
            (float) $realisasi->total,
            // Keep as floats in fractional form; formatting (percent) handled by columnFormats
            round($achS1, 4),
            round($achS2, 4),
            round($achYear, 4),
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

        // Style data rows with alternating colors
        for ($row = 2; $row <= $highestRow; $row++) {
            $fillColor = ($row % 2 == 0) ? 'FEF2F2' : 'FFFFFF';
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

        // Center align numeric columns
        $sheet->getStyle('A2:A' . $highestRow)->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);
        $sheet->getStyle('C2:C' . $highestRow)->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);
        $sheet->getStyle('D2:L' . $highestRow)->getAlignment()->setHorizontal(Alignment::HORIZONTAL_RIGHT);

        return [];
    }

    /**
     * @return array
     */
    public function columnWidths(): array
    {
        return [
            'A' => 6,   // No
            'B' => 50,  // Program Kerja
            'C' => 10,  // Tahun
            'D' => 18,  // RKAP
            'E' => 20,  // Realisasi Jan-Mar
            'F' => 20,  // Realisasi Jan-Jun
            'G' => 20,  // Realisasi Jul-Sep
            'H' => 20,  // Realisasi Jul-Des
            'I' => 20,  // Total
            'J' => 18,  // Achievement S1
            'K' => 18,  // Achievement S2
            'L' => 18,  // Achievement Year
        ];
    }

    /**
     * @return array
     */
    public function columnFormats(): array
    {
        return [
            'D' => NumberFormat::FORMAT_NUMBER_COMMA_SEPARATED1, // RKAP
            'E' => NumberFormat::FORMAT_NUMBER_COMMA_SEPARATED1, // Realisasi Jan-Mar
            'F' => NumberFormat::FORMAT_NUMBER_COMMA_SEPARATED1, // Realisasi Jan-Jun
            'G' => NumberFormat::FORMAT_NUMBER_COMMA_SEPARATED1, // Realisasi Jul-Sep
            'H' => NumberFormat::FORMAT_NUMBER_COMMA_SEPARATED1, // Realisasi Jul-Des
            'I' => NumberFormat::FORMAT_NUMBER_COMMA_SEPARATED1, // Total
            // Use Excel percentage format (two decimal places)
            'J' => NumberFormat::FORMAT_PERCENTAGE_00,           // Achievement S1
            'K' => NumberFormat::FORMAT_PERCENTAGE_00,           // Achievement S2
            'L' => NumberFormat::FORMAT_PERCENTAGE_00,           // Achievement Year
        ];
    }
}
