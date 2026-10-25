<?php

namespace App\Exports;

use App\Models\Formasi;
use App\Models\DataKaryawan;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;
use Maatwebsite\Excel\Concerns\WithStyles;
use Maatwebsite\Excel\Concerns\WithColumnWidths;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;
use Illuminate\Support\Collection;

class SemuaJabatanLengkapExport implements FromCollection, WithHeadings, WithMapping, WithStyles, WithColumnWidths
{
    /**
     * @return \Illuminate\Support\Collection
     */
    public function collection()
    {
        // Ambil semua formasi
        $formasi = Formasi::all();

        // Hitung jumlah karyawan per kode_jabatan (hanya berdasarkan kode jabatan)
        $karyawanCounts = DataKaryawan::select('kode_jabatan', \DB::raw('COUNT(*) as count'))
            ->whereNotNull('kode_jabatan')
            ->where('kode_jabatan', '!=', '')
            ->groupBy('kode_jabatan')
            ->pluck('count', 'kode_jabatan');

        $result = new Collection();

        foreach ($formasi as $item) {
            // Hitung jumlah karyawan yang mengisi posisi ini (hanya berdasarkan kode_jabatan)
            $terisi = $karyawanCounts->get($item->kode_jabatan, 0);

            // Hitung sisa formasi (vacancy)
            $sisaFormasi = $item->kuota - $terisi;

            // Tampilkan SEMUA jabatan (baik yang lowong maupun yang sudah terisi penuh)
            $result->push([
                'lokasi' => $item->lokasi_kerja,
                'unit' => $item->unit_kerja,
                'kode_jabatan' => $item->kode_jabatan,
                'jabatan' => $item->jabatan,
                'kelompok_kelas_jabatan' => $item->kelompok_kelas_jabatan,
                'grade' => $item->grade,
                'kuota' => $item->kuota,
                'terisi' => $terisi,
                'sisa_formasi' => $sisaFormasi,
            ]);
        }

        // Sort by lokasi, unit, then jabatan
        return $result->sortBy([
            ['lokasi', 'asc'],
            ['unit', 'asc'],
            ['jabatan', 'asc'],
        ]);
    }

    /**
     * @return array
     */
    public function headings(): array
    {
        return [
            'NO',
            'LOKASI',
            'UNIT',
            'KODE JABATAN',
            'JABATAN',
            'KELOMPOK KELAS JABATAN',
            'GRADE',
            'FORMASI',
            'TERISI',
            'LOWONG',
        ];
    }

    /**
     * @param mixed $row
     * @return array
     */
    public function map($row): array
    {
        static $no = 0;
        $no++;

        return [
            $no,
            $row['lokasi'],
            $row['unit'],
            $row['kode_jabatan'],
            $row['jabatan'],
            $row['kelompok_kelas_jabatan'],
            $row['grade'],
            $row['kuota'],
            $row['terisi'],
            $row['sisa_formasi'],
        ];
    }

    /**
     * @param Worksheet $sheet
     * @return array
     */
    public function styles(Worksheet $sheet)
    {
        return [
            // Style the first row (header)
            1 => [
                'font' => ['bold' => true, 'size' => 12],
                'fill' => [
                    'fillType' => \PhpOffice\PhpSpreadsheet\Style\Fill::FILL_SOLID,
                    'startColor' => ['rgb' => '4F46E5'] // Indigo
                ],
                'font' => ['bold' => true, 'color' => ['rgb' => 'FFFFFF']],
                'alignment' => [
                    'horizontal' => \PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_CENTER,
                    'vertical' => \PhpOffice\PhpSpreadsheet\Style\Alignment::VERTICAL_CENTER,
                ],
            ],
        ];
    }

    /**
     * @return array
     */
    public function columnWidths(): array
    {
        return [
            'A' => 6,  // NO
            'B' => 20, // LOKASI
            'C' => 35, // UNIT
            'D' => 18, // KODE JABATAN
            'E' => 40, // JABATAN
            'F' => 25, // KELOMPOK KELAS JABATAN
            'G' => 10, // GRADE
            'H' => 12, // FORMASI
            'I' => 12, // TERISI
            'J' => 12, // LOWONG
        ];
    }
}
