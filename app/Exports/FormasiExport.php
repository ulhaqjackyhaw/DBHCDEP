<?php

namespace App\Exports;

use App\Models\Formasi;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;
use Maatwebsite\Excel\Concerns\WithStyles;
use Maatwebsite\Excel\Concerns\WithColumnWidths;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;
use PhpOffice\PhpSpreadsheet\Style\Fill;
use PhpOffice\PhpSpreadsheet\Style\Alignment;
use PhpOffice\PhpSpreadsheet\Style\Border;

class FormasiExport implements FromCollection, WithHeadings, WithMapping, WithStyles, WithColumnWidths
{
    /**
     * @return \Illuminate\Support\Collection
     */
    public function collection()
    {
        return Formasi::orderBy('lokasi_kerja')
            ->orderBy('unit_kerja')
            ->orderBy('jabatan')
            ->get();
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
     * @param mixed $formasi
     * @return array
     */
    public function map($formasi): array
    {
        return [
            $formasi->kode_jabatan,
            $formasi->unit_deputy_egm,
            $formasi->unit_assistant_deputy,
            $formasi->unit_division_head,
            $formasi->unit_department_head,
            $formasi->lokasi_kerja,
            $formasi->unit_kerja,
            $formasi->jabatan,
            $formasi->kelompok_kelas_jabatan,
            $formasi->grade,
            $formasi->kuota,
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

        // Style data rows with alternating colors
        for ($row = 2; $row <= $highestRow; $row++) {
            $fillColor = ($row % 2 == 0) ? 'F3F4F6' : 'FFFFFF';
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

        // Center align specific columns
        $sheet->getStyle('A2:A' . $highestRow)->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);
        $sheet->getStyle('E2:E' . $highestRow)->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);
        $sheet->getStyle('F2:F' . $highestRow)->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);
        $sheet->getStyle('G2:G' . $highestRow)->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);

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