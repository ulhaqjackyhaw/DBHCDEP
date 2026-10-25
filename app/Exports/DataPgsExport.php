<?php

namespace App\Exports;

use App\Models\DataPgs;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;
use Maatwebsite\Excel\Concerns\WithStyles;
use Maatwebsite\Excel\Concerns\WithColumnWidths;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;
use PhpOffice\PhpSpreadsheet\Style\Fill;
use PhpOffice\PhpSpreadsheet\Style\Alignment;
use PhpOffice\PhpSpreadsheet\Style\Border;

class DataPgsExport implements FromCollection, WithHeadings, WithMapping, WithStyles, WithColumnWidths
{
    /**
     * @return \Illuminate\Support\Collection
     */
    public function collection()
    {
        return DataPgs::orderBy('nama')
            ->orderBy('tanggal_pgs')
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
            'JABATAN DEFINITIF',
            'JABATAN PGS',
            'LOKASI/UNIT KERJA',
            'TANGGAL MULAI PGS',
            'TANGGAL SELESAI PGS',
            'DURASI PGS',
            'STATUS',
        ];
    }

    /**
     * @param DataPgs $dataPgs
     * @return array
     */
    public function map($dataPgs): array
    {
        // Determine status text
        $status = 'Normal';
        if ($dataPgs->is_overdue) {
            $status = 'OVERDUE - ' . $dataPgs->sisa_hari;
        } elseif ($dataPgs->is_warning) {
            $status = 'PERINGATAN - ' . $dataPgs->sisa_hari;
        } elseif ($dataPgs->tanggal_selesai_pgs) {
            $status = 'Aktif - ' . $dataPgs->sisa_hari;
        } else {
            $status = 'Belum diatur';
        }

        return [
            $dataPgs->nik,
            $dataPgs->nama,
            $dataPgs->jabatan_definitif,
            $dataPgs->jabatan_pgs,
            $dataPgs->lokasi_unit_kerja,
            $dataPgs->tanggal_pgs,
            $dataPgs->tanggal_selesai_pgs ?? '-',
            $dataPgs->durasi_pgs,
            $status,
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
                'startColor' => ['rgb' => '7C3AED'],
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

        // Style data rows with alternating colors and status-based coloring
        for ($row = 2; $row <= $highestRow; $row++) {
            $fillColor = ($row % 2 == 0) ? 'F5F3FF' : 'FFFFFF';

            // Get status value to determine row color
            $statusValue = $sheet->getCell('I' . $row)->getValue();

            if (strpos($statusValue, 'OVERDUE') !== false) {
                $fillColor = 'FEE2E2'; // Light red for overdue
            } elseif (strpos($statusValue, 'PERINGATAN') !== false) {
                $fillColor = 'FEF3C7'; // Light yellow for warning
            }

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

        // Center align NIK, Tanggal PGS, Tanggal Selesai, and Status
        $sheet->getStyle('A2:A' . $highestRow)->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);
        $sheet->getStyle('F2:F' . $highestRow)->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);
        $sheet->getStyle('G2:G' . $highestRow)->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);
        $sheet->getStyle('H2:H' . $highestRow)->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);
        $sheet->getStyle('I2:I' . $highestRow)->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);

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
            'C' => 35,  // JABATAN DEFINITIF
            'D' => 35,  // JABATAN PGS
            'E' => 40,  // LOKASI/UNIT KERJA
            'F' => 18,  // TANGGAL MULAI PGS
            'G' => 18,  // TANGGAL SELESAI PGS
            'H' => 20,  // DURASI PGS
            'I' => 25,  // STATUS
        ];
    }
}