<?php

namespace App\Http\Controllers;

use Illuminate\Support\Facades\DB;
use Illuminate\Http\Request;



class EmployeeController extends Controller
{
    public function analitikOrganic()
    {
        // 1. SETUP DASAR
        // ===========================================
        $baseQuery = DB::table('data_karyawan')
            ->whereNotNull('sub_status')
            ->where('sub_status', '=', 'KP');

        // --- PIVOT DATA UNIT-LOKASI UNTUK TABEL KOMPARASI ---
        // Ambil semua kombinasi unit-lokasi beserta total karyawan, rata usia, rata masa kerja
        $unitLocationRaw = (clone $baseQuery)
            ->select(
                'unit_kerja',
                'lokasi_kerja',
                DB::raw('COUNT(*) as total_karyawan'),
                DB::raw('ROUND(AVG(TIMESTAMPDIFF(YEAR, STR_TO_DATE(tanggal_lahir, "%d/%m/%Y"), CURDATE())), 0) as rata_usia'),
                DB::raw('ROUND(AVG(TIMESTAMPDIFF(YEAR, STR_TO_DATE(tmt_karyawan, "%d/%m/%Y"), CURDATE())), 1) as rata_masa_kerja')
            )
            ->whereNotNull('unit_kerja')
            ->whereNotNull('lokasi_kerja')
            ->groupBy('unit_kerja', 'lokasi_kerja')
            ->get();

        // Dapatkan semua unit dan lokasi unik
        $allUnits = $unitLocationRaw->pluck('unit_kerja')->unique()->sort()->values();
        $allLocations = $unitLocationRaw->pluck('lokasi_kerja')->unique()->sort()->values();

        // Bentuk pivot: [unit][lokasi] = total_karyawan
        $unitLocationPivot = [];
        foreach ($allUnits as $unit) {
            foreach ($allLocations as $lokasi) {
                $found = $unitLocationRaw->first(fn($row) => $row->unit_kerja === $unit && $row->lokasi_kerja === $lokasi);
                $unitLocationPivot[$unit][$lokasi] = $found ? $found->total_karyawan : 0;
            }
            // Total per unit
            $unitLocationPivot[$unit]['total'] = $unitLocationRaw->where('unit_kerja', $unit)->sum('total_karyawan');
        }

        // (opsional) Data rata-rata usia & masa kerja per unit per lokasi
        $unitLocationMeta = [];
        foreach ($allUnits as $unit) {
            foreach ($allLocations as $lokasi) {
                $found = $unitLocationRaw->first(fn($row) => $row->unit_kerja === $unit && $row->lokasi_kerja === $lokasi);
                $unitLocationMeta[$unit][$lokasi] = [
                    'rata_usia' => $found ? $found->rata_usia : null,
                    'rata_masa_kerja' => $found ? $found->rata_masa_kerja : null,
                ];
            }
        }
        // 1. SETUP DASAR
        // ===========================================
        $baseQuery = DB::table('data_karyawan')
            ->whereNotNull('sub_status')
            ->where('sub_status', '=', 'KP');

        $employees = (clone $baseQuery)->get();
        $totalOrganic = $employees->count();

        // Hitung berdasarkan status jabatan
        $statusJabatanCounts = (clone $baseQuery)
            ->select('status_jabatan', DB::raw('COUNT(*) as total'))
            ->whereNotNull('status_jabatan')
            ->groupBy('status_jabatan')
            ->get();

        // Helper function untuk pivot data menjadi matriks
        $pivot = function ($data, $rowKey, $colKey, $valKey) {
            $matrix = [];
            $colHeaders = $data->pluck($colKey)->unique()->sort();
            if ($colHeaders->isEmpty())
                return ['matrix' => [], 'colHeaders' => collect()];
            foreach ($data as $item) {
                $row = $item->{$rowKey};
                $col = $item->{$colKey};
                $val = $item->{$valKey};
                if (!isset($matrix[$row])) {
                    $matrix[$row] = ['Total' => 0];
                    foreach ($colHeaders as $header)
                        $matrix[$row][$header] = 0;
                }
                $matrix[$row][$col] = $val;
                $matrix[$row]['Total'] += $val;
            }
            return ['matrix' => $matrix, 'colHeaders' => $colHeaders];
        };

        // 2. KALKULASI SEMUA ANALISIS
        // ===========================================

        // A. Analisis untuk Dashboard Utama
        $genderCounts = (clone $baseQuery)->select('jenis_kelamin', DB::raw('COUNT(*) as total'))->whereNotNull('jenis_kelamin')->groupBy('jenis_kelamin')->get();
        $ageGroupsData = (clone $baseQuery)->select(
            DB::raw("CASE 
                    WHEN TIMESTAMPDIFF(YEAR, STR_TO_DATE(tanggal_lahir, '%d/%m/%Y'), CURDATE()) BETWEEN 13 AND 28 THEN 'Gen Z'
                    WHEN TIMESTAMPDIFF(YEAR, STR_TO_DATE(tanggal_lahir, '%d/%m/%Y'), CURDATE()) BETWEEN 29 AND 44 THEN 'Gen Y (Milenial)'
                    WHEN TIMESTAMPDIFF(YEAR, STR_TO_DATE(tanggal_lahir, '%d/%m/%Y'), CURDATE()) BETWEEN 45 AND 60 THEN 'Gen X'
                    WHEN TIMESTAMPDIFF(YEAR, STR_TO_DATE(tanggal_lahir, '%d/%m/%Y'), CURDATE()) BETWEEN 61 AND 79 THEN 'Baby Boomers'
                    WHEN TIMESTAMPDIFF(YEAR, STR_TO_DATE(tanggal_lahir, '%d/%m/%Y'), CURDATE()) >= 79 THEN 'Pre-Boomer'
                    ELSE 'Lainnya'
                END as age_group, COUNT(*) as total")
        )
            ->whereNotNull('tanggal_lahir')
            ->groupBy('age_group')
            ->orderBy(DB::raw("CASE 
                WHEN age_group = 'Pre-Boomer' THEN 1
                WHEN age_group = 'Baby Boomers' THEN 2
                WHEN age_group = 'Gen X' THEN 3
                WHEN age_group = 'Gen Y (Milenial)' THEN 4
                WHEN age_group = 'Gen Z' THEN 5
                ELSE 6
            END"))
            ->get();

        $tenureGroups = (clone $baseQuery)->select(DB::raw("CASE WHEN TIMESTAMPDIFF(YEAR, STR_TO_DATE(tmt_karyawan, '%d/%m/%Y'), CURDATE()) < 1 THEN '< 1' WHEN TIMESTAMPDIFF(YEAR, STR_TO_DATE(tmt_karyawan, '%d/%m/%Y'), CURDATE()) BETWEEN 1 AND 5 THEN '1-5' WHEN TIMESTAMPDIFF(YEAR, STR_TO_DATE(tmt_karyawan, '%d/%m/%Y'), CURDATE()) BETWEEN 6 AND 10 THEN '6-10' WHEN TIMESTAMPDIFF(YEAR, STR_TO_DATE(tmt_karyawan, '%d/%m/%Y'), CURDATE()) BETWEEN 11 AND 20 THEN '11-20' ELSE '> 20' END as tenure_group, COUNT(*) as total"))->whereNotNull('tmt_karyawan')->groupBy('tenure_group')->orderBy('tenure_group')->get();

        // Distribusi Masa Kerja per Lokasi
        $tenureLocationRaw = (clone $baseQuery)
            ->select('lokasi_kerja', DB::raw("CASE 
                WHEN TIMESTAMPDIFF(YEAR, STR_TO_DATE(tmt_karyawan, '%d/%m/%Y'), CURDATE()) < 1 THEN '0-1 thn' 
                WHEN TIMESTAMPDIFF(YEAR, STR_TO_DATE(tmt_karyawan, '%d/%m/%Y'), CURDATE()) BETWEEN 1 AND 3 THEN '2-3 thn' 
                WHEN TIMESTAMPDIFF(YEAR, STR_TO_DATE(tmt_karyawan, '%d/%m/%Y'), CURDATE()) BETWEEN 4 AND 6 THEN '4-6 thn' 
                WHEN TIMESTAMPDIFF(YEAR, STR_TO_DATE(tmt_karyawan, '%d/%m/%Y'), CURDATE()) BETWEEN 7 AND 10 THEN '7-10 thn' 
                ELSE '>10 thn' 
            END as tenure_group"), DB::raw('COUNT(*) as total'))
            ->whereNotNull('tmt_karyawan')
            ->whereNotNull('lokasi_kerja')
            ->groupBy('lokasi_kerja', 'tenure_group')
            ->orderBy('lokasi_kerja')
            ->get();

        $tenureLocationLabels = $tenureLocationRaw->pluck('lokasi_kerja')->unique()->values();
        $tenureGroupsList = $tenureLocationRaw->pluck('tenure_group')->unique()->values();
        $tenureColorPalette = ['#16a34a', '#f97316', '#4f46e5', '#db2777', '#8b5cf6'];
        $tenureLocationDatasets = [];
        foreach ($tenureGroupsList as $i => $tenureGroup) {
            $row = [];
            foreach ($tenureLocationLabels as $lokasi) {
                $found = $tenureLocationRaw->first(fn($item) => $item->lokasi_kerja == $lokasi && $item->tenure_group == $tenureGroup);
                $row[] = $found ? $found->total : 0;
            }
            $tenureLocationDatasets[] = [
                'label' => $tenureGroup,  // Sudah match dengan Blade karena query sudah diubah
                'data' => $row,
                'backgroundColor' => $tenureColorPalette[$i % count($tenureColorPalette)]
            ];
        }

        $kkjCounts = (clone $baseQuery)->select('kelompok_kelas_jabatan', DB::raw('COUNT(*) as total'))->whereNotNull('kelompok_kelas_jabatan')->groupBy('kelompok_kelas_jabatan')->orderBy('total', 'desc')->get();

        // Piramida Jabatan per Lokasi
        $kkjLocationRaw = (clone $baseQuery)
            ->select('lokasi_kerja', 'kelompok_kelas_jabatan', DB::raw('COUNT(*) as total'))
            ->whereNotNull('lokasi_kerja')
            ->whereNotNull('kelompok_kelas_jabatan')
            ->groupBy('lokasi_kerja', 'kelompok_kelas_jabatan')
            ->orderBy('lokasi_kerja')
            ->get();

        $kkjLocationLabels = $kkjLocationRaw->pluck('lokasi_kerja')->unique()->values();
        $kkjGroups = $kkjLocationRaw->pluck('kelompok_kelas_jabatan')->unique()->values();
        $kkjColorPalette = ['#f97316', '#4f46e5', '#16a34a', '#db2777', '#8b5cf6', '#64748b'];
        $bodOrder = ['BOD-1', 'BOD-2', 'BOD-3', 'BOD-4'];
        $kkjGroupsSorted = $kkjGroups->sort(function ($a, $b) use ($bodOrder) {
            $ai = array_search($a, $bodOrder);
            $bi = array_search($b, $bodOrder);
            if ($ai === false)
                $ai = 99;
            if ($bi === false)
                $bi = 99;
            return $ai <=> $bi ?: strcmp($a, $b);
        })->values();
        $kkjLocationDatasets = [];
        foreach ($kkjGroupsSorted as $i => $group) {
            $row = [];
            foreach ($kkjLocationLabels as $lokasi) {
                $found = $kkjLocationRaw->first(fn($item) => $item->lokasi_kerja == $lokasi && $item->kelompok_kelas_jabatan == $group);
                $row[] = $found ? $found->total : 0;
            }
            $kkjLocationDatasets[] = [
                'label' => $group,
                'data' => $row,
                'backgroundColor' => $kkjColorPalette[$i % count($kkjColorPalette)]
            ];
        }

        // Distribusi Gender per Lokasi
        $genderLocationRaw = (clone $baseQuery)
            ->select('lokasi_kerja', 'jenis_kelamin', DB::raw('COUNT(*) as total'))
            ->whereNotNull('jenis_kelamin')
            ->whereNotNull('lokasi_kerja')
            ->groupBy('lokasi_kerja', 'jenis_kelamin')
            ->orderBy('lokasi_kerja')
            ->get();

        $genderLocationLabels = $genderLocationRaw->pluck('lokasi_kerja')->unique()->values();
        $genderGroups = $genderLocationRaw->pluck('jenis_kelamin')->unique()->values();
        $genderColorPalette = ['#4f46e5', '#db2777', '#f97316', '#16a34a', '#8b5cf6'];
        $genderLocationDatasets = [];
        foreach ($genderGroups as $i => $gender) {
            $row = [];
            foreach ($genderLocationLabels as $lokasi) {
                $found = $genderLocationRaw->first(fn($item) => $item->lokasi_kerja == $lokasi && $item->jenis_kelamin == $gender);
                $row[] = $found ? $found->total : 0;
            }
            // Normalize gender label to title case untuk consistency
            $normalizedGender = strtoupper($gender) === 'LAKI-LAKI' ? 'Laki-laki' :
                (strtoupper($gender) === 'PEREMPUAN' ? 'Perempuan' : $gender);
            $genderLocationDatasets[] = [
                'label' => $normalizedGender,
                'data' => $row,
                'backgroundColor' => $genderColorPalette[$i % count($genderColorPalette)]
            ];
        }

        // Distribusi Generasi per Lokasi
        $ageLocationRaw = (clone $baseQuery)
            ->select('lokasi_kerja', DB::raw("CASE 
                WHEN TIMESTAMPDIFF(YEAR, STR_TO_DATE(tanggal_lahir, '%d/%m/%Y'), CURDATE()) BETWEEN 13 AND 28 THEN 'Gen Z'
                WHEN TIMESTAMPDIFF(YEAR, STR_TO_DATE(tanggal_lahir, '%d/%m/%Y'), CURDATE()) BETWEEN 29 AND 44 THEN 'Gen Y (Milenial)'
                WHEN TIMESTAMPDIFF(YEAR, STR_TO_DATE(tanggal_lahir, '%d/%m/%Y'), CURDATE()) BETWEEN 45 AND 60 THEN 'Gen X'
                WHEN TIMESTAMPDIFF(YEAR, STR_TO_DATE(tanggal_lahir, '%d/%m/%Y'), CURDATE()) BETWEEN 61 AND 79 THEN 'Baby Boomers'
                WHEN TIMESTAMPDIFF(YEAR, STR_TO_DATE(tanggal_lahir, '%d/%m/%Y'), CURDATE()) >= 79 THEN 'Pre-Boomer'
                ELSE 'Lainnya'
            END as age_group"), DB::raw('COUNT(*) as total'))
            ->whereNotNull('tanggal_lahir')
            ->whereNotNull('lokasi_kerja')
            ->groupBy('lokasi_kerja', 'age_group')
            ->orderBy('lokasi_kerja')
            ->get();

        $ageLocationLabels = $ageLocationRaw->pluck('lokasi_kerja')->unique()->values();
        $ageGroups = $ageLocationRaw->pluck('age_group')->unique()->values();
        $ageLocationDatasets = [];
        $ageColorPalette = ['#f97316', '#4f46e5', '#db2777', '#16a34a', '#8b5cf6', '#64748b'];
        foreach ($ageGroups as $i => $ageGroup) {
            $row = [];
            foreach ($ageLocationLabels as $lokasi) {
                $found = $ageLocationRaw->first(fn($item) => $item->lokasi_kerja == $lokasi && $item->age_group == $ageGroup);
                $row[] = $found ? $found->total : 0;
            }
            // Normalize age group label untuk consistency dengan Blade
            $normalizedAge = $ageGroup === 'Gen Y (Milenial)' ? 'Milenial' : $ageGroup;
            $ageLocationDatasets[] = [
                'label' => $normalizedAge,
                'data' => $row,
                'backgroundColor' => $ageColorPalette[$i % count($ageColorPalette)]
            ];
        }

        $bodRows = (clone $baseQuery)->whereIn('kelompok_kelas_jabatan', ['BOD-1', 'BOD-2', 'BOD-3', 'BOD-4'])->select('nama', 'jabatan', 'kelompok_kelas_jabatan')->get();
        $bodGroups = collect($bodRows)->groupBy('kelompok_kelas_jabatan')->map(function ($group) {
            return $group->map(fn($item) => ['nama' => $item->nama, 'jabatan' => $item->jabatan])->all();
        })->all();


        // B. Analisis untuk Tab Strategis  <-- PERUBAHAN LOGIKA DI AREA INI
        // Mengganti `rata_masa_kerja` dengan `persen_karyawan_baru`
        $unitHealth = (clone $baseQuery)->select(
            'lokasi_kerja', // tambahkan lokasi
            'unit_kerja',
            DB::raw('COUNT(*) as total_karyawan'),
            DB::raw('ROUND(AVG(TIMESTAMPDIFF(YEAR, STR_TO_DATE(tanggal_lahir, \'%d/%m/%Y\'), CURDATE())), 0) as rata_usia'),
            // Diubah: Menghitung persentase karyawan dengan masa kerja < 1 tahun sebagai proksi stabilitas
            DB::raw('ROUND(SUM(CASE WHEN TIMESTAMPDIFF(YEAR, STR_TO_DATE(tmt_karyawan, \'%d/%m/%Y\'), CURDATE()) < 1 THEN 1 ELSE 0 END) * 100 / COUNT(*), 1) as persen_karyawan_baru'),
            DB::raw('ROUND(AVG(TIMESTAMPDIFF(YEAR, STR_TO_DATE(tmt_karyawan, \'%d/%m/%Y\'), CURDATE())), 1) as rata_masa_kerja'), // <-- Tetap dihitung untuk tabel, tapi tidak untuk kuadran
            DB::raw('ROUND(SUM(CASE WHEN jenis_kelamin = "Perempuan" OR jenis_kelamin = "Wanita" THEN 1 ELSE 0 END) * 100 / COUNT(*), 0) as persen_wanita')
        )
            ->whereNotNull('unit_kerja')
            ->groupBy('lokasi_kerja', 'unit_kerja') // group by lokasi dan unit
            ->orderByDesc('total_karyawan')
            ->get();

        // Diubah: Sumbu X sekarang menggunakan `persen_karyawan_baru`
        $quadrantData = $unitHealth->map(fn($unit) => ['x' => $unit->persen_karyawan_baru, 'y' => $unit->total_karyawan, 'r' => sqrt($unit->total_karyawan / M_PI) * 2, 'label' => $unit->unit_kerja]);

        // Diubah: Median dihitung berdasarkan metrik baru untuk garis kuadran
        $medianTenure = $unitHealth->median('persen_karyawan_baru') ?? 0;
        $medianHeadcount = $unitHealth->median('total_karyawan') ?? 0;

        // C. Analisis untuk Tab Matriks
        $ageGradeCounts = (clone $baseQuery)->select('person_grade', DB::raw("CASE WHEN TIMESTAMPDIFF(YEAR, STR_TO_DATE(tanggal_lahir, '%d/%m/%Y'), CURDATE()) <= 25 THEN '≤ 25' WHEN TIMESTAMPDIFF(YEAR, STR_TO_DATE(tanggal_lahir, '%d/%m/%Y'), CURDATE()) BETWEEN 26 AND 35 THEN '26-35' WHEN TIMESTAMPDIFF(YEAR, STR_TO_DATE(tanggal_lahir, '%d/%m/%Y'), CURDATE()) BETWEEN 36 AND 45 THEN '36-45' WHEN TIMESTAMPDIFF(YEAR, STR_TO_DATE(tanggal_lahir, '%d/%m/%Y'), CURDATE()) BETWEEN 46 AND 55 THEN '46-55' ELSE '> 55' END as age_group, COUNT(*) as total"))->whereNotNull('person_grade')->whereNotNull('tanggal_lahir')->groupBy('person_grade', 'age_group')->orderBy('person_grade', 'asc')->get()->map(fn($i) => (object) ['GRADE' => 'Grade ' . $i->person_grade, 'age_group' => $i->age_group, 'total' => $i->total]);
        $ageGradeResult = $pivot($ageGradeCounts, 'GRADE', 'age_group', 'total');

        $gradeGenderCounts = (clone $baseQuery)->select('person_grade', 'jenis_kelamin', DB::raw('COUNT(*) as total'))->whereNotNull('person_grade')->whereNotNull('jenis_kelamin')->groupBy('person_grade', 'jenis_kelamin')->orderBy('person_grade', 'asc')->get()->map(fn($i) => (object) ['GRADE' => 'Grade ' . $i->person_grade, 'GENDER' => $i->jenis_kelamin, 'total' => $i->total]);
        $gradeGenderResult = $pivot($gradeGenderCounts, 'GRADE', 'GENDER', 'total');
        $gradeGenderChartData = ['labels' => array_keys($gradeGenderResult['matrix']), 'datasets' => []];
        foreach ($gradeGenderResult['colHeaders'] as $gender) {
            $dataset = ['label' => $gender, 'data' => collect($gradeGenderResult['matrix'])->pluck($gender)->all()];
            $gradeGenderChartData['datasets'][] = $dataset;
        }

        // D. Analisis untuk Tab Lanjutan
        $educationCounts = (clone $baseQuery)->select('pendidikan_diakui', DB::raw('COUNT(*) as total'))->whereNotNull('pendidikan_diakui')->groupBy('pendidikan_diakui')->orderBy('total', 'desc')->get();
        $tenureGradeCounts = (clone $baseQuery)->select('person_grade', DB::raw("CASE WHEN TIMESTAMPDIFF(YEAR, STR_TO_DATE(tmt_karyawan, '%d/%m/%Y'), CURDATE()) < 1 THEN '< 1' WHEN TIMESTAMPDIFF(YEAR, STR_TO_DATE(tmt_karyawan, '%d/%m/%Y'), CURDATE()) BETWEEN 1 AND 5 THEN '1-5' WHEN TIMESTAMPDIFF(YEAR, STR_TO_DATE(tmt_karyawan, '%d/%m/%Y'), CURDATE()) BETWEEN 6 AND 10 THEN '6-10' WHEN TIMESTAMPDIFF(YEAR, STR_TO_DATE(tmt_karyawan, '%d/%m/%Y'), CURDATE()) BETWEEN 11 AND 20 THEN '11-20' ELSE '> 20' END as tenure_group, COUNT(*) as total"))->whereNotNull('person_grade')->whereNotNull('tmt_karyawan')->groupBy('person_grade', 'tenure_group')->orderBy('person_grade', 'asc')->get()->map(fn($i) => (object) ['GRADE' => 'Grade ' . $i->person_grade, 'tenure_group' => $i->tenure_group, 'total' => $i->total]);
        $tenureGradeResult = $pivot($tenureGradeCounts, 'GRADE', 'tenure_group', 'total');
        $turnoverProxy = (clone $baseQuery)->select('unit_kerja', DB::raw('SUM(CASE WHEN TIMESTAMPDIFF(YEAR, STR_TO_DATE(tmt_karyawan, \'%d/%m/%Y\'), CURDATE()) < 1 THEN 1 ELSE 0 END) as baru, COUNT(*) as total'))->groupBy('unit_kerja')->get()->map(function ($i) {
            $i->persen_baru = round(($i->baru / max(1, $i->total)) * 100, 1);
            return $i;
        });

        // Analisis Rencana Pensiun per Tahun
        $retirementByYear = (clone $baseQuery)
            ->select(DB::raw("SUBSTRING_INDEX(rencana_pensiun, '/', -1) as tahun_pensiun"), DB::raw('COUNT(*) as total'))
            ->whereNotNull('rencana_pensiun')
            ->where('rencana_pensiun', '!=', '')
            ->groupBy('tahun_pensiun')
            ->orderBy('tahun_pensiun', 'asc')
            ->get();

        // E. Analisis Top 10 Unit dengan tingkat pendidikan tertinggi
        $topEducatedUnits = (clone $baseQuery)
            ->select(
                'unit_kerja',
                DB::raw('COUNT(*) as total_karyawan'),
                DB::raw('AVG(CASE 
                    WHEN pendidikan_diakui = "S3" THEN 9
                    WHEN pendidikan_diakui = "S2" THEN 8
                    WHEN pendidikan_diakui = "S1" THEN 7
                    WHEN pendidikan_diakui = "D4" THEN 6
                    WHEN pendidikan_diakui = "D3" THEN 5
                    WHEN pendidikan_diakui = "D2" THEN 4
                    WHEN pendidikan_diakui = "D1" THEN 3
                    WHEN pendidikan_diakui = "SMA/SMK" THEN 2
                    WHEN pendidikan_diakui = "SMP" THEN 1
                    ELSE 0
                END) as education_score'),
                DB::raw('ROUND(SUM(CASE 
                    WHEN pendidikan_diakui IN ("S1", "S2", "S3") THEN 1 
                    ELSE 0 
                END) * 100.0 / COUNT(*), 1) as persen_sarjana'),
                DB::raw('ROUND(SUM(CASE 
                    WHEN pendidikan_diakui IN ("S1", "S2", "S3") THEN 1 
                    ELSE 0 
                END) * 100.0 / COUNT(*), 1) as high_education_percentage'),
                DB::raw('SUM(CASE WHEN pendidikan_diakui = "S3" THEN 1 ELSE 0 END) as s3_count'),
                DB::raw('SUM(CASE WHEN pendidikan_diakui = "S2" THEN 1 ELSE 0 END) as s2_count'),
                DB::raw('SUM(CASE WHEN pendidikan_diakui = "S1" THEN 1 ELSE 0 END) as s1_count'),
                DB::raw('SUM(CASE WHEN pendidikan_diakui IN ("D1", "D2", "D3", "D4") THEN 1 ELSE 0 END) as diploma_count')
            )
            ->whereNotNull('unit_kerja')
            ->where('unit_kerja', '!=', '')
            ->groupBy('unit_kerja')
            ->having('total_karyawan', '>=', 3)
            ->orderByDesc('education_score')
            ->limit(10)
            ->get();

        // F. Analisis sebaran pendidikan berdasarkan lokasi (menggunakan kolom 'lokasi')
        $locationEducation = (clone $baseQuery)
            ->select('lokasi_kerja', 'pendidikan_diakui', DB::raw('COUNT(*) as jumlah'))
            ->whereNotNull('lokasi_kerja')
            ->where('lokasi_kerja', '!=', '')
            ->whereNotNull('pendidikan_diakui')
            ->where('pendidikan_diakui', '!=', '')
            ->groupBy('lokasi_kerja', 'pendidikan_diakui')
            ->orderBy('lokasi_kerja')
            ->orderBy('pendidikan_diakui')
            ->get();

        // Mapping untuk normalisasi pendidikan dari database ke format standar
        $pendidikanMapping = [
            'S3 - STRATA 3' => 'S3',
            'S2 - STRATA 2' => 'S2',
            'S1 - STRATA 1' => 'S1',
            'DIV - DIPLOMA IV' => 'D4',
            'DIII - DIPLOMA III' => 'D3',
            'DII  - DIPLOMA II' => 'D2',
            'DII - DIPLOMA II' => 'D2',
            'DI - DIPLOMA I' => 'D1',
            'SLTA' => 'SMA/SMK',
            'SMA' => 'SMA/SMK',
            'SMK' => 'SMA/SMK',
            'SMP' => 'Lainnya',
            'SD' => 'Lainnya',
        ];

        // Transform data untuk matrix lokasi-pendidikan
        $locationMatrix = [];
        $educationLevels = ['S3', 'S2', 'S1', 'D4', 'D3', 'D2', 'D1', 'SMA/SMK', 'Lainnya'];

        foreach ($locationEducation as $item) {
            if (!isset($locationMatrix[$item->lokasi_kerja])) {
                $locationMatrix[$item->lokasi_kerja] = array_fill_keys($educationLevels, 0);
                $locationMatrix[$item->lokasi_kerja]['total'] = 0;
            }

            // Normalisasi nilai pendidikan
            $pendidikan = $item->pendidikan_diakui;
            $normalizedPendidikan = $pendidikanMapping[$pendidikan] ?? 'Lainnya';

            $locationMatrix[$item->lokasi_kerja][$normalizedPendidikan] += $item->jumlah;
            $locationMatrix[$item->lokasi_kerja]['total'] += $item->jumlah;
        }

        // Hitung education_score dan highest_education untuk setiap lokasi
        $scores = [
            'S3' => 9,
            'S2' => 8,
            'S1' => 7,
            'D4' => 6,
            'D3' => 5,
            'D2' => 4,
            'D1' => 3,
            'SMA/SMK' => 2,
            'Lainnya' => 1
        ];

        foreach ($locationMatrix as $location => $data) {
            $total = $data['total'];
            $educationScore = 0;

            // Hitung skor pendidikan untuk lokasi
            foreach ($scores as $education => $score) {
                if (isset($data[$education]) && $data[$education] > 0) {
                    $educationScore += ($data[$education] * $score);
                }
            }

            $locationMatrix[$location]['education_score'] = $total > 0 ? round($educationScore / $total, 2) : 0;
            $locationMatrix[$location]['highest_education'] = $this->getHighestEducation($data);
        }

        // Summary lokasi dengan pendidikan tertinggi
        $locationSummary = collect($locationMatrix)->map(function ($data, $location) {
            $total = $data['total'];
            $educationScore = 0;

            // Hitung skor pendidikan untuk lokasi
            $scores = [
                'S3' => 9,
                'S2' => 8,
                'S1' => 7,
                'D4' => 6,
                'D3' => 5,
                'D2' => 4,
                'D1' => 3,
                'SMA/SMK' => 2,
                'SMP' => 1
            ];

            foreach ($scores as $education => $score) {
                if (isset($data[$education]) && $data[$education] > 0) {
                    $educationScore += ($data[$education] * $score);
                }
            }

            return [
                'location' => $location,
                'total_employees' => $total,
                'average_education_score' => $total > 0 ? round($educationScore / $total, 2) : 0,
                'highest_education' => $this->getHighestEducation($data),
                'education_distribution' => $data
            ];
        })->sortByDesc('average_education_score');

        // 3. KIRIM DATA KE VIEW
        // ===========================================

        // (Unit location pivot data is already computed above, no need to duplicate)

        return view('analitik.organic', [
            'unitLocationPivot' => $unitLocationPivot,
            'allUnits' => $allUnits,
            'allLocations' => $allLocations,
            'unitLocationMeta' => $unitLocationMeta,
            'employees' => $employees,
            'totalOrganic' => $totalOrganic,
            'genderData' => $genderCounts,
            'ageGroupsData' => $ageGroupsData,
            'genderLocationLabels' => $genderLocationLabels,
            'genderLocationDatasets' => $genderLocationDatasets,
            'ageLocationLabels' => $ageLocationLabels,
            'ageLocationDatasets' => $ageLocationDatasets,
            'tenureGroups' => $tenureGroups,
            'tenureLocationLabels' => $tenureLocationLabels,
            'tenureLocationDatasets' => $tenureLocationDatasets,
            'kkjData' => $kkjCounts,
            'kkjLocationLabels' => $kkjLocationLabels,
            'kkjLocationDatasets' => $kkjLocationDatasets,
            'bodGroups' => $bodGroups,
            'unitHealth' => $unitHealth,
            'quadrantData' => $quadrantData,
            'medianTenure' => $medianTenure,
            'medianHeadcount' => $medianHeadcount,
            'ageGradeMatrix' => $ageGradeResult['matrix'],
            'ageGradeHeaders' => $ageGradeResult['colHeaders'],
            'gradeGenderMatrix' => $gradeGenderResult['matrix'],
            'gradeGenderHeaders' => $gradeGenderResult['colHeaders'],
            'gradeGenderChartData' => $gradeGenderChartData,
            'educationCounts' => $educationCounts,
            'tenureGradeMatrix' => $tenureGradeResult['matrix'],
            'tenureGradeHeaders' => $tenureGradeResult['colHeaders'],
            'turnoverProxy' => $turnoverProxy,
            'topEducatedUnits' => $topEducatedUnits,
            'locationMatrix' => $locationMatrix,
            'locationSummary' => $locationSummary,
            'educationLevels' => $educationLevels,
            'retirementByYear' => $retirementByYear,
            'statusJabatanCounts' => $statusJabatanCounts,
        ]);
    }


    public function analitikOutsourcing()
    {
        $baseQuery = DB::table('data_karyawan')
            ->whereNotNull('sub_status')
            ->where('sub_status', '=', 'ALIH DAYA');

        $employees = (clone $baseQuery)->get();
        $totalOutsourcing = $employees->count();

        // Analisis Gender by Location (for grouped chart)
        $genderLocationRaw = (clone $baseQuery)
            ->select('lokasi_kerja', 'jenis_kelamin', DB::raw('COUNT(*) as total'))
            ->whereNotNull('jenis_kelamin')
            ->whereNotNull('lokasi_kerja')
            ->groupBy('lokasi_kerja', 'jenis_kelamin')
            ->orderBy('lokasi_kerja')
            ->get();

        // Prepare data for grouped/stacked bar chart
        $locations = $genderLocationRaw->pluck('lokasi_kerja')->unique()->values();
        $genders = $genderLocationRaw->pluck('jenis_kelamin')->unique()->values();
        $colorPalette = ['#4f46e5', '#db2777', '#f97316', '#16a34a', '#8b5cf6'];
        $genderLocationMatrix = [];
        foreach ($genders as $i => $gender) {
            $row = [];
            foreach ($locations as $lokasi) {
                $found = $genderLocationRaw->first(fn($item) => $item->lokasi_kerja == $lokasi && $item->jenis_kelamin == $gender);
                $row[] = $found ? $found->total : 0;
            }
            $genderLocationMatrix[] = [
                'label' => $gender,
                'data' => $row,
                'backgroundColor' => $colorPalette[$i % count($colorPalette)]
            ];
        }

        // Analisis Instansi per Lokasi (for grouped/stacked bar chart)
        $instansiLocationRaw = (clone $baseQuery)
            ->select('lokasi_kerja', 'instansi', DB::raw('COUNT(*) as total'))
            ->whereNotNull('instansi')
            ->where('instansi', '!=', '')
            ->whereNotNull('lokasi_kerja')
            ->where('lokasi_kerja', '!=', '')
            ->groupBy('lokasi_kerja', 'instansi')
            ->orderBy('lokasi_kerja')
            ->get();

        $instansiLocationLabels = $instansiLocationRaw->pluck('lokasi_kerja')->unique()->values();
        $instansiList = $instansiLocationRaw->pluck('instansi')->unique()->values();
        $instansiColorPalette = ['#4f46e5', '#db2777', '#f97316', '#16a34a', '#8b5cf6', '#64748b', '#eab308', '#0ea5e9', '#f43f5e', '#22d3ee'];
        $instansiLocationDatasets = [];
        foreach ($instansiList as $i => $instansi) {
            $row = [];
            foreach ($instansiLocationLabels as $lokasi) {
                $found = $instansiLocationRaw->first(fn($item) => $item->lokasi_kerja == $lokasi && $item->instansi == $instansi);
                $row[] = $found ? $found->total : 0;
            }
            $instansiLocationDatasets[] = [
                'label' => $instansi,
                'data' => $row,
                'backgroundColor' => $instansiColorPalette[$i % count($instansiColorPalette)]
            ];
        }

        // Analisis Generasi (Usia) - untuk chart biasa
        $ageGroupsData = (clone $baseQuery)->select(
            DB::raw("CASE 
                WHEN TIMESTAMPDIFF(YEAR, STR_TO_DATE(tanggal_lahir, '%d/%m/%Y'), CURDATE()) BETWEEN 13 AND 28 THEN 'Gen Z'
                WHEN TIMESTAMPDIFF(YEAR, STR_TO_DATE(tanggal_lahir, '%d/%m/%Y'), CURDATE()) BETWEEN 29 AND 44 THEN 'Gen Y (Milenial)'
                WHEN TIMESTAMPDIFF(YEAR, STR_TO_DATE(tanggal_lahir, '%d/%m/%Y'), CURDATE()) BETWEEN 45 AND 60 THEN 'Gen X'
                WHEN TIMESTAMPDIFF(YEAR, STR_TO_DATE(tanggal_lahir, '%d/%m/%Y'), CURDATE()) BETWEEN 61 AND 79 THEN 'Baby Boomers'
                WHEN TIMESTAMPDIFF(YEAR, STR_TO_DATE(tanggal_lahir, '%d/%m/%Y'), CURDATE()) >= 79 THEN 'Pre-Boomer'
                ELSE 'Lainnya'
            END as age_group"),
            DB::raw('COUNT(*) as total')
        )
            ->whereNotNull('tanggal_lahir')
            ->groupBy('age_group')
            ->orderBy(DB::raw("CASE 
                WHEN age_group = 'Pre-Boomer' THEN 1
                WHEN age_group = 'Baby Boomers' THEN 2
                WHEN age_group = 'Gen X' THEN 3
                WHEN age_group = 'Gen Y (Milenial)' THEN 4
                WHEN age_group = 'Gen Z' THEN 5
                ELSE 6
            END"))
            ->get();

        // Distribusi Generasi per Lokasi (untuk chart grouped/stacked)
        $ageLocationRaw = (clone $baseQuery)
            ->select('lokasi_kerja', DB::raw("CASE 
                WHEN TIMESTAMPDIFF(YEAR, STR_TO_DATE(tanggal_lahir, '%d/%m/%Y'), CURDATE()) BETWEEN 13 AND 28 THEN 'Gen Z'
                WHEN TIMESTAMPDIFF(YEAR, STR_TO_DATE(tanggal_lahir, '%d/%m/%Y'), CURDATE()) BETWEEN 29 AND 44 THEN 'Gen Y (Milenial)'
                WHEN TIMESTAMPDIFF(YEAR, STR_TO_DATE(tanggal_lahir, '%d/%m/%Y'), CURDATE()) BETWEEN 45 AND 60 THEN 'Gen X'
                WHEN TIMESTAMPDIFF(YEAR, STR_TO_DATE(tanggal_lahir, '%d/%m/%Y'), CURDATE()) BETWEEN 61 AND 79 THEN 'Baby Boomers'
                WHEN TIMESTAMPDIFF(YEAR, STR_TO_DATE(tanggal_lahir, '%d/%m/%Y'), CURDATE()) >= 79 THEN 'Pre-Boomer'
                ELSE 'Lainnya'
            END as age_group"), DB::raw('COUNT(*) as total'))
            ->whereNotNull('tanggal_lahir')
            ->whereNotNull('lokasi_kerja')
            ->groupBy('lokasi_kerja', 'age_group')
            ->orderBy('lokasi_kerja')
            ->get();

        $ageLocations = $ageLocationRaw->pluck('lokasi_kerja')->unique()->values();
        $ageGroups = $ageLocationRaw->pluck('age_group')->unique()->values();
        $ageLocationDatasets = [];
        $ageColorPalette = ['#f97316', '#4f46e5', '#db2777', '#16a34a', '#8b5cf6', '#64748b'];
        foreach ($ageGroups as $i => $ageGroup) {
            $row = [];
            foreach ($ageLocations as $lokasi) {
                $found = $ageLocationRaw->first(fn($item) => $item->lokasi_kerja == $lokasi && $item->age_group == $ageGroup);
                $row[] = $found ? $found->total : 0;
            }
            $ageLocationDatasets[] = [
                'label' => $ageGroup,
                'data' => $row,
                'backgroundColor' => $ageColorPalette[$i % count($ageColorPalette)]
            ];
        } {
            $baseQuery = DB::table('data_karyawan')
                ->whereNotNull('sub_status')
                ->where('sub_status', '=', 'ALIH DAYA');

            $employees = (clone $baseQuery)->get();
            $totalOutsourcing = $employees->count();


            // Analisis Gender by Location (for grouped chart)
            $genderLocationRaw = (clone $baseQuery)
                ->select('lokasi_kerja', 'jenis_kelamin', DB::raw('COUNT(*) as total'))
                ->whereNotNull('jenis_kelamin')
                ->whereNotNull('lokasi_kerja')
                ->groupBy('lokasi_kerja', 'jenis_kelamin')
                ->orderBy('lokasi_kerja')
                ->get();

            // Prepare data for grouped/stacked bar chart
            $locations = $genderLocationRaw->pluck('lokasi_kerja')->unique()->values();
            $genders = $genderLocationRaw->pluck('jenis_kelamin')->unique()->values();
            $colorPalette = ['#4f46e5', '#db2777', '#f97316', '#16a34a', '#8b5cf6'];
            $genderLocationMatrix = [];
            foreach ($genders as $i => $gender) {
                $row = [];
                foreach ($locations as $lokasi) {
                    $found = $genderLocationRaw->first(fn($item) => $item->lokasi_kerja == $lokasi && $item->jenis_kelamin == $gender);
                    $row[] = $found ? $found->total : 0;
                }
                $genderLocationMatrix[] = [
                    'label' => $gender,
                    'data' => $row,
                    'backgroundColor' => $colorPalette[$i % count($colorPalette)]
                ];
            }

            // Analisis Generasi (Usia)
            $ageGroupsData = (clone $baseQuery)->select(
                DB::raw("CASE 
                WHEN TIMESTAMPDIFF(YEAR, STR_TO_DATE(tanggal_lahir, '%d/%m/%Y'), CURDATE()) BETWEEN 13 AND 28 THEN 'Gen Z'
                WHEN TIMESTAMPDIFF(YEAR, STR_TO_DATE(tanggal_lahir, '%d/%m/%Y'), CURDATE()) BETWEEN 29 AND 44 THEN 'Gen Y (Milenial)'
                WHEN TIMESTAMPDIFF(YEAR, STR_TO_DATE(tanggal_lahir, '%d/%m/%Y'), CURDATE()) BETWEEN 45 AND 60 THEN 'Gen X'
                WHEN TIMESTAMPDIFF(YEAR, STR_TO_DATE(tanggal_lahir, '%d/%m/%Y'), CURDATE()) BETWEEN 61 AND 79 THEN 'Baby Boomers'
                WHEN TIMESTAMPDIFF(YEAR, STR_TO_DATE(tanggal_lahir, '%d/%m/%Y'), CURDATE()) >= 79 THEN 'Pre-Boomer'
                ELSE 'Lainnya'
            END as age_group, COUNT(*) as total")
            )
                ->whereNotNull('tanggal_lahir')
                ->groupBy('age_group')
                ->orderBy(DB::raw("CASE 
                WHEN age_group = 'Pre-Boomer' THEN 1
                WHEN age_group = 'Baby Boomers' THEN 2
                WHEN age_group = 'Gen X' THEN 3
                WHEN age_group = 'Gen Y (Milenial)' THEN 4
                WHEN age_group = 'Gen Z' THEN 5
                ELSE 6
            END"))
                ->get();

            // Analisis Unit
            $unitCounts = (clone $baseQuery)
                ->select('unit_kerja', DB::raw('COUNT(*) as total'))
                ->whereNotNull('unit_kerja')
                ->groupBy('unit_kerja')
                ->orderBy('total', 'desc')
                ->get();

            return view('analitik.outsourcing', [
                'employees' => $employees,
                'totalOutsourcing' => $totalOutsourcing,
                // For grouped gender chart
                'genderLocationLabels' => $locations,
                'genderLocationDatasets' => $genderLocationMatrix,
                // For grouped age chart
                'ageLocationLabels' => $ageLocations,
                'ageLocationDatasets' => $ageLocationDatasets,
                // For fallback/simple chart (if needed)
                // 'genderCounts' => $genderCounts,
                'ageGroupsData' => $ageGroupsData,
                'unitCounts' => $unitCounts,
                'instansiLocationLabels' => $instansiLocationLabels,
                'instansiLocationDatasets' => $instansiLocationDatasets,
            ]);
        }
    }

    private function getHighestEducation($data)
    {
        $educationLevels = ['S3', 'S2', 'S1', 'D4', 'D3', 'D2', 'D1', 'SMA/SMK', 'SMP', 'Lainnya'];

        $maxCount = 0;
        $dominantEducation = 'Tidak ada data';

        // Cari tingkat pendidikan dengan jumlah terbanyak (dominan)
        foreach ($educationLevels as $education) {
            if (isset($data[$education]) && $data[$education] > $maxCount) {
                $maxCount = $data[$education];
                $dominantEducation = $education;
            }
        }

        return $dominantEducation;
    }
}