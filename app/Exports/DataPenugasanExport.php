<?php

namespace App\Exports;

use App\Models\DataPenugasan;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;
use Maatwebsite\Excel\Concerns\WithStyles;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;

class DataPenugasanExport implements FromCollection, WithHeadings, WithMapping, WithStyles
{
    /**
     * @return \Illuminate\Support\Collection
     */
    public function collection()
    {
        return DataPenugasan::orderBy('created_at', 'desc')->get();
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
     * @param DataPenugasan $penugasan
     */
    public function map($penugasan): array
    {
        return [
            $penugasan->nik,
            $penugasan->nama,
            $penugasan->kj,
            $penugasan->jabatan_definitif,
            $penugasan->unit_definitif,
            $penugasan->lokasi_definitif,
            $penugasan->unit_penugasan,
            $penugasan->lokasi_penugasan,
            $penugasan->nomor_sprint,
            $penugasan->tanggal_mulai,
            $penugasan->tanggal_selesai,
            $penugasan->pic,
            $penugasan->keterangan,
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
