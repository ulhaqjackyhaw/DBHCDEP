<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use App\Models\DataKaryawan;
use App\Models\Formasi;
use App\Models\DataPgs;
use App\Models\DataPenugasan;
use App\Models\Version;
use App\Models\EmployeeHistory;
use App\Exports\JabatanLowongExport;
use Maatwebsite\Excel\Facades\Excel;
use Carbon\Carbon;

class DashboardController extends Controller
{
    public function index()
    {
        // === KPI RINGKAS ===
        $total = DataKaryawan::count();

        $totalUnits = DataKaryawan::whereNotNull('unit_kerja')
            ->where('unit_kerja', '!=', '')
            ->distinct('unit_kerja')->count('unit_kerja');

        // Untuk usia, kita hitung dari tanggal_lahir (format dd/mm/yyyy)
        $karyawanData = DataKaryawan::whereNotNull('tanggal_lahir')
            ->where('tanggal_lahir', '!=', '')
            ->whereNotNull('tmt_karyawan')
            ->where('tmt_karyawan', '!=', '')
            ->get(['tanggal_lahir', 'tmt_karyawan']);

        $totalAge = 0;
        $validAgeCount = 0;
        $totalMK = 0;
        $validMKCount = 0;

        foreach ($karyawanData as $karyawan) {
            // Hitung usia
            if ($karyawan->tanggal_lahir) {
                $age = $this->calculateAge($karyawan->tanggal_lahir);
                if ($age > 0) {
                    $totalAge += $age;
                    $validAgeCount++;
                }
            }

            // Hitung masa kerja
            if ($karyawan->tmt_karyawan) {
                $mk = $this->calculateWorkPeriod($karyawan->tmt_karyawan);
                if ($mk > 0) {
                    $totalMK += $mk;
                    $validMKCount++;
                }
            }
        }

        $avgAge = $validAgeCount > 0 ? round($totalAge / $validAgeCount, 1) : 0;
        $avgMK = $validMKCount > 0 ? round($totalMK / $validMKCount, 1) : 0;

        $female = DataKaryawan::where('jenis_kelamin', 'Perempuan')->count();
        $femalePct = $total ? round($female / $total * 100, 1) : 0;

        // === STATUS (PIE) ===
        $employeeStatus = DataKaryawan::select(DB::raw("COALESCE(sub_status,'(Kosong)') AS status_label"), DB::raw('COUNT(*) AS total'))
            ->groupBy('status_label')
            ->orderByDesc('total')
            ->get();

        $labels = $employeeStatus->pluck('status_label')->toArray();
        $data = $employeeStatus->pluck('total')->toArray();

        // === GENDER (fallback pie) ===
        $gender = DataKaryawan::select(DB::raw("COALESCE(jenis_kelamin,'(Kosong)') AS gender_label"), DB::raw('COUNT(*) AS total'))
            ->groupBy('gender_label')
            ->orderByDesc('total')
            ->get();
        $genderLabels = $gender->pluck('gender_label')->toArray();
        $genderData = $gender->pluck('total')->toArray();

        // === PENDIDIKAN (fallback) ===
        $pend = DataKaryawan::select(DB::raw("COALESCE(pendidikan_diakui,'(Kosong)') AS pend_label"), DB::raw('COUNT(*) AS total'))
            ->groupBy('pend_label')
            ->orderByDesc('total')
            ->get();
        $pendLabels = $pend->pluck('pend_label')->toArray();
        $pendData = $pend->pluck('total')->toArray();

        // app/Http/Controllers/DashboardController.php

        // ====== PENDIDIKAN Grouped (Organik vs OS) ======
        $pendidikanStatus = DataKaryawan::select(
            DB::raw("COALESCE(pendidikan_diakui,'(Kosong)') AS pend_label"),
            DB::raw("SUM(CASE WHEN UPPER(TRIM(sub_status)) IN ('ALIH DAYA','OUTSOURCING','OS','OUTSOURCE') OR LOWER(TRIM(sub_status)) LIKE '%alih%' OR LOWER(TRIM(sub_status)) LIKE '%outsour%' THEN 1 ELSE 0 END) AS os"),
            DB::raw("SUM(CASE WHEN UPPER(TRIM(sub_status)) IN ('ALIH DAYA','OUTSOURCING','OS','OUTSOURCE') OR LOWER(TRIM(sub_status)) LIKE '%alih%' OR LOWER(TRIM(sub_status)) LIKE '%outsour%' THEN 0 ELSE 1 END) AS organic")
        )
            ->groupBy('pend_label')
            // ---- SORTING BERDASARKAN FORMAT DATA ASLI ----
            ->orderByRaw("
                CASE pend_label
                    WHEN 'SMP' THEN 1
                    WHEN 'SLTP' THEN 2
                    WHEN 'SLTA' THEN 3
                    WHEN 'SMA/SMK' THEN 4
                    WHEN 'DII  - DIPLOMA II' THEN 5
                    WHEN 'D2' THEN 6
                    WHEN 'DIII - DIPLOMA III' THEN 7
                    WHEN 'D3' THEN 8
                    WHEN 'DIV - DIPLOMA IV' THEN 9
                    WHEN 'S1 - STRATA 1' THEN 10
                    WHEN 'S1' THEN 11
                    WHEN 'S2 - STRATA 2' THEN 12
                    WHEN 'S2' THEN 13
                    WHEN 'S3 - STRATA 3' THEN 14
                    WHEN 'S3' THEN 15
                    ELSE 99
                END ASC
            ")
            ->get();

        $pendGroupLabels = $pendidikanStatus->pluck('pend_label')->toArray();
        $pendGroupOS = $pendidikanStatus->pluck('os')->map(fn($v) => (int) $v)->toArray();
        $pendGroupOrganic = $pendidikanStatus->pluck('organic')->map(fn($v) => (int) $v)->toArray();
        // ====== AKHIR DARI KODE YANG DITAMBAHKAN KEMBALI ======

        // === SEBARAN USIA (BIN) ===
        $usiaBins = [
            '<=24' => [0, 24],
            '25-29' => [25, 29],
            '30-34' => [30, 34],
            '35-39' => [35, 39],
            '>=40' => [40, 200],
        ];
        $usiaLabels = array_keys($usiaBins);
        $usiaData = [];

        // Hitung sebaran usia dengan parsing manual
        $allKaryawan = DataKaryawan::whereNotNull('tanggal_lahir')
            ->where('tanggal_lahir', '!=', '')
            ->get(['tanggal_lahir']);

        foreach ($usiaBins as $label => [$min, $max]) {
            $count = 0;
            foreach ($allKaryawan as $k) {
                $age = $this->calculateAge($k->tanggal_lahir);
                if ($label === '>=40') {
                    if ($age >= 40)
                        $count++;
                } else {
                    if ($age >= $min && $age <= $max)
                        $count++;
                }
            }
            $usiaData[] = $count;
        }

        // === SEBARAN MASA KERJA (BIN) ===
        $mkBins = [
            '0-1' => [0, 1],
            '2-3' => [2, 3],
            '4-6' => [4, 6],
            '7-10' => [7, 10],
            '>10' => [11, 200],
        ];
        $mkLabels = array_keys($mkBins);
        $mkData = [];

        // Hitung sebaran masa kerja dengan parsing manual
        $allKaryawanMK = DataKaryawan::whereNotNull('tmt_karyawan')
            ->where('tmt_karyawan', '!=', '')
            ->get(['tmt_karyawan']);

        foreach ($mkBins as $label => [$min, $max]) {
            $count = 0;
            foreach ($allKaryawanMK as $k) {
                $mk = $this->calculateWorkPeriod($k->tmt_karyawan);
                if ($label === '>10') {
                    if ($mk > 10)
                        $count++;
                } else {
                    if ($mk >= $min && $mk <= $max)
                        $count++;
                }
            }
            $mkData[] = $count;
        }

        // === TOP 10 UNIT ===
        $unitTop = DataKaryawan::select('unit_kerja', DB::raw('COUNT(*) AS total'))
            ->whereNotNull('unit_kerja')->where('unit_kerja', '!=', '')
            ->groupBy('unit_kerja')
            ->orderByDesc('total')
            ->limit(10)
            ->get();
        $unitTopLabels = $unitTop->pluck('unit_kerja')->toArray();
        $unitTopData = $unitTop->pluck('total')->toArray();

        // --- LOGIKA BARU UNTUK JABATAN LOWONG (PAKAI FIELD KUOTA) ---
        // Ambil semua formasi unik (kode_jabatan, lokasi_kerja, unit_kerja, jabatan, kelompok_kelas_jabatan, kuota)
        $formasiData = Formasi::select('kode_jabatan', 'lokasi_kerja', 'unit_kerja', 'jabatan', 'kelompok_kelas_jabatan', 'kuota')->get();

        // Hitung jumlah karyawan per kode_jabatan (hanya berdasarkan kode jabatan saja)
        $karyawanData = DataKaryawan::select('kode_jabatan', DB::raw('COUNT(*) as karyawan_count'))
            ->whereNotNull('kode_jabatan')
            ->where('kode_jabatan', '!=', '')
            ->groupBy('kode_jabatan')
            ->get()
            ->keyBy('kode_jabatan');

        // Hitung penugasan PGS yang mengisi jabatan lowong per kode_jabatan
        $pgsData = DataPgs::select('kode_jabatan', DB::raw('COUNT(*) as pgs_count'))
            ->whereNotNull('kode_jabatan')
            ->where('kode_jabatan', '!=', '')
            ->groupBy('kode_jabatan')
            ->get()
            ->keyBy('kode_jabatan');

        // Hitung jabatan lowong per lokasi dan kelompok_kelas_jabatan
        $jabatanLowongData = collect();
        foreach ($formasiData as $formasi) {
            // Ambil jumlah karyawan dan PGS berdasarkan kode_jabatan
            $karyawanCount = $karyawanData->get($formasi->kode_jabatan)->karyawan_count ?? 0;
            $pgsCount = $pgsData->get($formasi->kode_jabatan)->pgs_count ?? 0;
            $terisiCount = $karyawanCount + $pgsCount;
            $lowongCount = $formasi->kuota - $terisiCount;

            if ($lowongCount > 0) {
                $jabatanLowongData->push((object) [
                    'lokasi' => $formasi->lokasi_kerja,
                    'level' => $formasi->kelompok_kelas_jabatan, // menggunakan kelompok_kelas_jabatan sebagai level
                    'total' => $lowongCount
                ]);
            }
        }

        // Menghitung total keseluruhan
        $totalJabatanLowong = $jabatanLowongData->sum('total');

        // Group by lokasi dan aggregate level yang sama
        $jabatanLowongGrouped = $jabatanLowongData->groupBy('lokasi')->map(function ($items, $lokasi) {
            return $items->groupBy('level')->map(function ($levelItems) {
                return (object) [
                    'level' => $levelItems->first()->level,
                    'total' => $levelItems->sum('total')
                ];
            })->values();
        });

        // === TABEL UNIT (SEMUA + JUMLAH) ===
        $unitTable = DataKaryawan::select('unit_kerja', DB::raw('COUNT(*) AS total'))
            ->whereNotNull('unit_kerja')->where('unit_kerja', '!=', '')
            ->groupBy('unit_kerja')
            ->orderBy('unit_kerja')
            ->get();

        // === FUNGSI JABATAN ===
        $fungsiJabatan = DataKaryawan::select(DB::raw("COALESCE(fungsi_jabatan,'(Kosong)') AS fungsi_label"), DB::raw('COUNT(*) AS total'))
            ->groupBy('fungsi_label')
            ->orderByDesc('total')
            ->limit(10)
            ->get();
        $fungsiJabatanLabels = $fungsiJabatan->pluck('fungsi_label')->toArray();
        $fungsiJabatanData = $fungsiJabatan->pluck('total')->toArray();

        // === INSTANSI ===
        $instansi = DataKaryawan::select(DB::raw("COALESCE(instansi,'(Kosong)') AS instansi_label"), DB::raw('COUNT(*) AS total'))
            ->groupBy('instansi_label')
            ->orderByDesc('total')
            ->get();
        $instansiLabels = $instansi->pluck('instansi_label')->toArray();
        $instansiData = $instansi->pluck('total')->toArray();

        // ====== GENDER Grouped (Organik vs OS) ======
        $genderStatus = DataKaryawan::select(
            DB::raw("
                    CASE
                        WHEN LOWER(TRIM(jenis_kelamin)) IN ('l','lk','laki','laki-laki','pria','male','m') THEN 'Laki-laki'
                        WHEN LOWER(TRIM(jenis_kelamin)) IN ('p','perempuan','wanita','female','f') THEN 'Perempuan'
                        ELSE '(Kosong)'
                    END AS gender_label
                "),
            DB::raw("SUM(CASE WHEN UPPER(TRIM(sub_status)) IN ('ALIH DAYA','OUTSOURCING','OS','OUTSOURCE') OR LOWER(TRIM(sub_status)) LIKE '%alih%' OR LOWER(TRIM(sub_status)) LIKE '%outsour%' THEN 1 ELSE 0 END) AS os"),
            DB::raw("SUM(CASE WHEN UPPER(TRIM(sub_status)) IN ('ALIH DAYA','OUTSOURCING','OS','OUTSOURCE') OR LOWER(TRIM(sub_status)) LIKE '%alih%' OR LOWER(TRIM(sub_status)) LIKE '%outsour%' THEN 0 ELSE 1 END) AS organic")
        )
            ->groupBy('gender_label')
            ->get();

        $genderGroupLabels = ['Laki-laki', 'Perempuan'];
        $map = [];
        foreach ($genderStatus as $row) {
            $map[$row->gender_label] = ['os' => (int) $row->os, 'organic' => (int) $row->organic];
        }
        $genderGroupOS = [];
        $genderGroupOrganic = [];
        foreach ($genderGroupLabels as $g) {
            $genderGroupOS[] = $map[$g]['os'] ?? 0;
            $genderGroupOrganic[] = $map[$g]['organic'] ?? 0;
        }

        // ====== PENGELOMPOKAN KELOMPOK KELAS JABATAN (BOD-4 .. BOD-1) ======
        $normExpr = "LOWER(REPLACE(REPLACE(TRIM(kelompok_kelas_jabatan),'-',''),' ',''))";

        $bodRows = DataKaryawan::select([
            DB::raw("$normExpr as kelas_norm"),
            'nama',
            'jabatan',
        ])
            ->whereNotNull('kelompok_kelas_jabatan')
            ->where('kelompok_kelas_jabatan', '!=', '')
            ->whereIn(DB::raw($normExpr), ['bod4', 'bod3', 'bod2', 'bod1'])
            ->orderBy('nama')
            ->get();

        $bodGroups = ['BOD-4' => [], 'BOD-3' => [], 'BOD-2' => [], 'BOD-1' => []];
        foreach ($bodRows as $r) {
            $key = match ($r->kelas_norm) {
                'bod4' => 'BOD-4',
                'bod3' => 'BOD-3',
                'bod2' => 'BOD-2',
                'bod1' => 'BOD-1',
                default => null,
            };
            if ($key) {
                $bodGroups[$key][] = [
                    'nama' => $r->nama ?? '-',
                    'jabatan' => $r->jabatan ?? '-',
                ];
            }
        }

        // CATATAN: Data pendidikan grouped ($pendGroupLabels, $pendGroupOS, $pendGroupOrganic) 
        // sudah didefinisikan di baris ~113-115 dari query database
        // Tidak perlu override karena data asli sudah benar

        // ====== GRAFIK PERTUMBUHAN KARYAWAN DARI VERSION HISTORY ======
        $versionGrowth = $this->getVersionGrowthData();

        // ====== BAGIAN COMPACT() YANG DIPERBAIKI ======
        return view('dashboard', compact(
            // chart lama
            'labels',
            'data',
            // KPI
            'total',
            'totalUnits',
            'avgAge',
            'avgMK',
            'femalePct',
            // chart tambahan
            'genderLabels',
            'genderData',
            'pendLabels',
            'pendData',
            'usiaLabels',
            'usiaData',
            'mkLabels',
            'mkData',
            'unitTopLabels',
            'unitTopData',
            'fungsiJabatanLabels',
            'fungsiJabatanData',
            'instansiLabels',
            'instansiData',
            // tabel unit
            'unitTable',
            // gender grouped bar
            'genderGroupLabels',
            'genderGroupOS',
            'genderGroupOrganic',
            // pendidikan grouped bar (INI YANG HILANG)
            'pendGroupLabels',      // <--- Pastikan ini ada
            'pendGroupOS',          // <--- Pastikan ini ada
            'pendGroupOrganic',     // <--- Pastikan ini ada
            // tabel BOD
            'bodGroups',
            'jabatanLowongGrouped', // Data terkelompok
            'totalJabatanLowong',   // Variabel TOTAL KESELURUHAN yang baru
            // Version Growth Data
            'versionGrowth'
        ));
    }

    /**
     * Get version history growth data
     */
    private function getVersionGrowthData()
    {
        // Ambil semua versi yang tersimpan, urutkan dari yang terlama
        $versions = Version::orderBy('created_at', 'asc')->get();

        $growthLabels = [];
        $growthTotal = [];
        $growthOrganic = [];
        $growthOutsourcing = [];

        foreach ($versions as $version) {
            // Format tanggal untuk label
            $growthLabels[] = $version->created_at->format('d M Y H:i');

            // Hitung total karyawan di versi ini
            $totalCount = EmployeeHistory::where('version_id', $version->id)->count();
            $growthTotal[] = $totalCount;

            // Hitung organik (sub_status != ALIH DAYA/OUTSOURCING)
            $organicCount = EmployeeHistory::where('version_id', $version->id)
                ->where(function ($q) {
                    $q->where('sub_status', 'NOT LIKE', '%ALIH DAYA%')
                        ->where('sub_status', 'NOT LIKE', '%OUTSOURCING%')
                        ->where('sub_status', 'NOT LIKE', '%OUTSOURCE%')
                        ->where('sub_status', 'NOT LIKE', '%OS%')
                        ->orWhereNull('sub_status');
                })
                ->count();
            $growthOrganic[] = $organicCount;

            // Hitung outsourcing
            $outsourcingCount = EmployeeHistory::where('version_id', $version->id)
                ->where(function ($q) {
                    $q->where('sub_status', 'LIKE', '%ALIH DAYA%')
                        ->orWhere('sub_status', 'LIKE', '%OUTSOURCING%')
                        ->orWhere('sub_status', 'LIKE', '%OUTSOURCE%')
                        ->orWhere('sub_status', 'LIKE', '%OS%');
                })
                ->count();
            $growthOutsourcing[] = $outsourcingCount;
        }

        // Tambahkan data current (data saat ini yang belum di-snapshot)
        $currentTotal = DataKaryawan::count();
        $currentOrganic = DataKaryawan::where(function ($q) {
            $q->where('sub_status', 'NOT LIKE', '%ALIH DAYA%')
                ->where('sub_status', 'NOT LIKE', '%OUTSOURCING%')
                ->where('sub_status', 'NOT LIKE', '%OUTSOURCE%')
                ->where('sub_status', 'NOT LIKE', '%OS%')
                ->orWhereNull('sub_status');
        })->count();
        $currentOutsourcing = DataKaryawan::where(function ($q) {
            $q->where('sub_status', 'LIKE', '%ALIH DAYA%')
                ->orWhere('sub_status', 'LIKE', '%OUTSOURCING%')
                ->orWhere('sub_status', 'LIKE', '%OUTSOURCE%')
                ->orWhere('sub_status', 'LIKE', '%OS%');
        })->count();

        $growthLabels[] = 'Saat Ini';
        $growthTotal[] = $currentTotal;
        $growthOrganic[] = $currentOrganic;
        $growthOutsourcing[] = $currentOutsourcing;

        return [
            'labels' => $growthLabels,
            'total' => $growthTotal,
            'organic' => $growthOrganic,
            'outsourcing' => $growthOutsourcing,
            'hasData' => count($versions) > 0
        ];
    }

    /**
     * Calculate age from birth date
     */
    private function calculateAge($birthDate)
    {
        if (!$birthDate)
            return 0;

        // Parse berbagai format tanggal (dd/mm/yyyy, dd-mm-yyyy, yyyy-mm-dd)
        $parsedDate = null;
        if (strpos($birthDate, '/') !== false) {
            $parts = explode('/', $birthDate);
            if (count($parts) === 3) {
                $parsedDate = \DateTime::createFromFormat('d/m/Y', $birthDate);
            }
        } elseif (strpos($birthDate, '-') !== false) {
            $parts = explode('-', $birthDate);
            if (count($parts) === 3) {
                if (strlen($parts[0]) === 4) {
                    $parsedDate = \DateTime::createFromFormat('Y-m-d', $birthDate);
                } else {
                    $parsedDate = \DateTime::createFromFormat('d-m-Y', $birthDate);
                }
            }
        }

        if (!$parsedDate || $parsedDate === false)
            return 0;

        $today = new \DateTime();
        $age = $today->diff($parsedDate)->y;

        return $age;
    }

    /**
     * Calculate work period from TMT date
     */
    private function calculateWorkPeriod($tmtDate)
    {
        if (!$tmtDate)
            return 0;

        // Parse berbagai format tanggal (dd/mm/yyyy, dd-mm-yyyy, yyyy-mm-dd)
        $parsedDate = null;
        if (strpos($tmtDate, '/') !== false) {
            $parts = explode('/', $tmtDate);
            if (count($parts) === 3) {
                $parsedDate = \DateTime::createFromFormat('d/m/Y', $tmtDate);
            }
        } elseif (strpos($tmtDate, '-') !== false) {
            $parts = explode('-', $tmtDate);
            if (count($parts) === 3) {
                if (strlen($parts[0]) === 4) {
                    $parsedDate = \DateTime::createFromFormat('Y-m-d', $tmtDate);
                } else {
                    $parsedDate = \DateTime::createFromFormat('d-m-Y', $tmtDate);
                }
            }
        }

        if (!$parsedDate || $parsedDate === false)
            return 0;

        $today = new \DateTime();
        $workYears = $today->diff($parsedDate)->y;

        return $workYears;
    }

    /**
     * Get detail jabatan lowong untuk modal popup
     */
    public function getJabatanLowongDetail(Request $request)
    {
        $lokasi = $request->get('lokasi');
        $level = $request->get('level');

        if (!$lokasi || !$level) {
            return response()->json([
                'success' => false,
                'message' => 'Lokasi dan level harus diisi'
            ]);
        }

        try {
            // Ambil semua formasi berdasarkan lokasi dan level (pakai field kuota)
            $formasiDetail = Formasi::select([
                'kode_jabatan',
                'lokasi_kerja',
                'unit_kerja',
                'jabatan',
                'kelompok_kelas_jabatan',
                'kuota',
            ])
                ->where('lokasi_kerja', $lokasi)
                ->where('kelompok_kelas_jabatan', $level)
                ->get();

            // Hitung karyawan yang sudah terisi per kode_jabatan (hanya berdasarkan kode jabatan)
            $karyawanCounts = DataKaryawan::select('kode_jabatan', DB::raw('COUNT(*) as karyawan_count'))
                ->whereNotNull('kode_jabatan')
                ->where('kode_jabatan', '!=', '')
                ->groupBy('kode_jabatan')
                ->get()
                ->keyBy('kode_jabatan');

            // Hitung penugasan PGS yang mengisi jabatan lowong per kode_jabatan
            $pgsCounts = DataPgs::select('kode_jabatan', DB::raw('COUNT(*) as pgs_count'))
                ->whereNotNull('kode_jabatan')
                ->where('kode_jabatan', '!=', '')
                ->groupBy('kode_jabatan')
                ->get()
                ->keyBy('kode_jabatan');

            // Gabungkan data dan filter hanya yang lowong
            $detailLowong = collect();
            foreach ($formasiDetail as $formasi) {
                // Ambil jumlah karyawan dan PGS
                $karyawanCount = $karyawanCounts->get($formasi->kode_jabatan)->karyawan_count ?? 0;
                $pgsCount = $pgsCounts->get($formasi->kode_jabatan)->pgs_count ?? 0;
                $terisiCount = $karyawanCount + $pgsCount;
                $lowongCount = $formasi->kuota - $terisiCount;

                if ($lowongCount > 0) {
                    $detailLowong->push([
                        'kode_jabatan' => $formasi->kode_jabatan,
                        'lokasi' => $formasi->lokasi_kerja,
                        'unit' => $formasi->unit_kerja,
                        'jabatan' => $formasi->jabatan,
                        'level' => $formasi->kelompok_kelas_jabatan,
                        'formasi_count' => $formasi->kuota,
                        'karyawan_count' => $karyawanCount,
                        'pgs_count' => $pgsCount,
                        'terisi_count' => $terisiCount,
                        'lowong_count' => $lowongCount
                    ]);
                }
            }

            return response()->json([
                'success' => true,
                'data' => $detailLowong->values()->all(),
                'total' => $detailLowong->sum('lowong_count')
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Terjadi kesalahan: ' . $e->getMessage()
            ]);
        }
    }

    /**
     * Export detail jabatan lowong ke Excel
     */
    public function exportJabatanLowongDetail(Request $request)
    {
        $lokasi = $request->get('lokasi');
        $level = $request->get('level');

        if (!$lokasi || !$level) {
            return redirect()->back()->with('error', 'Lokasi dan level harus diisi');
        }

        // Panggil method detail untuk mendapatkan data
        $detailResponse = $this->getJabatanLowongDetail($request);
        $detailData = $detailResponse->getData(true);

        if (!$detailData['success']) {
            return redirect()->back()->with('error', $detailData['message']);
        }

        // Buat Excel manual menggunakan simple approach
        $filename = 'jabatan_lowong_' . str_replace([' ', '/'], '_', $lokasi) . '_' . str_replace(['-', ' '], '_', $level) . '_' . date('Y-m-d') . '.csv';

        $headers = [
            'Content-Type' => 'text/csv',
            'Content-Disposition' => 'attachment; filename="' . $filename . '"',
        ];

        $callback = function () use ($detailData, $lokasi, $level) {
            $file = fopen('php://output', 'w');

            // Header info
            fputcsv($file, ['Detail Jabatan Lowong']);
            fputcsv($file, ['Lokasi:', $lokasi]);
            fputcsv($file, ['Level:', $level]);
            fputcsv($file, ['Tanggal Export:', date('d/m/Y H:i:s')]);
            fputcsv($file, []); // Empty row

            // Header tabel
            fputcsv($file, ['Kode Jabatan', 'Nama Jabatan', 'Unit', 'Formasi', 'Terisi', 'Lowong']);

            // Data
            foreach ($detailData['data'] as $row) {
                fputcsv($file, [
                    $row['kode_jabatan'],
                    $row['jabatan'],
                    $row['unit'],
                    $row['formasi_count'],
                    $row['karyawan_count'],
                    $row['lowong_count']
                ]);
            }

            fclose($file);
        };

        return response()->stream($callback, 200, $headers);
    }

    /**
     * Export jabatan lowong (vacancy) per lokasi
     * Accessible by all authenticated users
     */
    public function exportJabatanLowong()
    {
        $fileName = 'Jabatan_Lowong_' . date('YmdHis') . '.xlsx';

        return Excel::download(new JabatanLowongExport(), $fileName);
    }

    /**
     * Search karyawan by NIK or Nama for modal assignment
     */
    public function searchKaryawan(Request $request)
    {
        $query = trim($request->get('q', ''));
        if (strlen($query) < 1) {
            return response()->json([
                'success' => true,
                'data' => []
            ]);
        }

        $karyawans = DataKaryawan::select([
            'id',
            'nik',
            'nama',
            'jabatan',
            'kode_jabatan',
            'unit_kerja',
            'lokasi_kerja',
            'kelompok_kelas_jabatan',
            'person_grade',
        ])
            ->where(function ($q) use ($query) {
                $q->where('nama', 'LIKE', "%{$query}%")
                    ->orWhere('nik', 'LIKE', "%{$query}%");
            })
            ->limit(20)
            ->get();

        return response()->json([
            'success' => true,
            'data' => $karyawans
        ]);
    }

    /**
     * Assign karyawan to a vacant position as Definitif
     */
    public function assignDefinitif(Request $request)
    {
        $request->validate([
            'kode_jabatan' => 'required|string',
            'karyawan_id' => 'required|exists:data_karyawan,id',
        ]);

        try {
            return DB::transaction(function () use ($request) {
                $formasi = Formasi::where('kode_jabatan', $request->kode_jabatan)->first();
                if (!$formasi) {
                    return response()->json([
                        'success' => false,
                        'message' => 'Posisi formasi tujuan tidak ditemukan'
                    ], 404);
                }

                $karyawan = DataKaryawan::findOrFail($request->karyawan_id);

                // Cek jika karyawan sudah memegang jabatan ini
                if ($karyawan->kode_jabatan === $formasi->kode_jabatan) {
                    return response()->json([
                        'success' => false,
                        'message' => 'Karyawan sudah memegang posisi jabatan ini'
                    ], 422);
                }

                $oldJabatan = $karyawan->jabatan ?? '-';

                // Update posisi karyawan ke jabatan formasi baru
                $karyawan->update([
                    'kode_jabatan' => $formasi->kode_jabatan,
                    'jabatan' => $formasi->jabatan,
                    'unit_kerja' => $formasi->unit_kerja,
                    'lokasi_kerja' => $formasi->lokasi_kerja,
                    'kelompok_kelas_jabatan' => $formasi->kelompok_kelas_jabatan,
                    'unit_deputy_egm' => $formasi->unit_deputy_egm,
                    'unit_assistant_deputy' => $formasi->unit_assistant_deputy,
                    'unit_division_head' => $formasi->unit_division_head,
                    'unit_department_head' => $formasi->unit_department_head,
                    'person_grade' => $formasi->grade ?: $karyawan->person_grade,
                ]);

                return response()->json([
                    'success' => true,
                    'message' => "Berhasil menempatkan {$karyawan->nama} ke jabatan definitif {$formasi->jabatan}. Posisi sebelumnya ({$oldJabatan}) kini lowong."
                ]);
            });
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Gagal melakukan penempatan definitif: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Assign karyawan to a vacant position as PGS
     */
    public function assignPgs(Request $request)
    {
        $request->validate([
            'kode_jabatan' => 'required|string',
            'karyawan_id' => 'required|exists:data_karyawan,id',
            'tanggal_pgs' => 'required|date',
            'tanggal_selesai_pgs' => 'nullable|date|after_or_equal:tanggal_pgs',
        ]);

        try {
            return DB::transaction(function () use ($request) {
                $formasi = Formasi::where('kode_jabatan', $request->kode_jabatan)->first();
                if (!$formasi) {
                    return response()->json([
                        'success' => false,
                        'message' => 'Posisi formasi tujuan tidak ditemukan'
                    ], 404);
                }

                $karyawan = DataKaryawan::findOrFail($request->karyawan_id);

                // Format tanggal ke d/m/Y untuk konsistensi database
                $tglPgs = Carbon::createFromFormat('Y-m-d', $request->tanggal_pgs)->format('d/m/Y');
                $tglSelesaiPgs = $request->tanggal_selesai_pgs 
                    ? Carbon::createFromFormat('Y-m-d', $request->tanggal_selesai_pgs)->format('d/m/Y') 
                    : null;

                $lokasiUnit = $formasi->lokasi_kerja . ($formasi->unit_kerja ? ' / ' . $formasi->unit_kerja : '');

                // Simpan ke DataPgs (jabatan definitif karyawan tidak berubah)
                DataPgs::create([
                    'nik' => $karyawan->nik,
                    'nama' => $karyawan->nama,
                    'jabatan_definitif' => $karyawan->jabatan ?? '-',
                    'jabatan_pgs' => $formasi->jabatan,
                    'kode_jabatan' => $formasi->kode_jabatan,
                    'lokasi_unit_kerja' => $lokasiUnit,
                    'tanggal_pgs' => $tglPgs,
                    'tanggal_selesai_pgs' => $tglSelesaiPgs,
                ]);

                return response()->json([
                    'success' => true,
                    'message' => "Berhasil menugaskan {$karyawan->nama} sebagai PGS {$formasi->jabatan}. Data telah tercatat di Master Data PGS."
                ]);
            });
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Gagal mencatat penugasan PGS: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Assign karyawan to a vacant position as Penugasan
     */
    public function assignPenugasan(Request $request)
    {
        $request->validate([
            'kode_jabatan' => 'required|string',
            'karyawan_id' => 'required|exists:data_karyawan,id',
            'tanggal_mulai' => 'required|date',
            'tanggal_selesai' => 'nullable|date|after_or_equal:tanggal_mulai',
            'nomor_sprint' => 'nullable|string|max:255',
            'pic' => 'nullable|string|max:255',
            'keterangan' => 'nullable|string',
        ]);

        try {
            return DB::transaction(function () use ($request) {
                $formasi = Formasi::where('kode_jabatan', $request->kode_jabatan)->first();
                if (!$formasi) {
                    return response()->json([
                        'success' => false,
                        'message' => 'Posisi formasi tujuan tidak ditemukan'
                    ], 404);
                }

                $karyawan = DataKaryawan::findOrFail($request->karyawan_id);

                $tglMulai = Carbon::createFromFormat('Y-m-d', $request->tanggal_mulai)->format('d/m/Y');
                $tglSelesai = $request->tanggal_selesai 
                    ? Carbon::createFromFormat('Y-m-d', $request->tanggal_selesai)->format('d/m/Y') 
                    : null;

                DataPenugasan::create([
                    'nik' => $karyawan->nik,
                    'nama' => $karyawan->nama,
                    'kj' => $karyawan->person_grade ?? $karyawan->kelompok_kelas_jabatan,
                    'jabatan_definitif' => $karyawan->jabatan ?? '-',
                    'kode_jabatan' => $formasi->kode_jabatan,
                    'unit_definitif' => $karyawan->unit_kerja ?? '-',
                    'lokasi_definitif' => $karyawan->lokasi_kerja ?? '-',
                    'unit_penugasan' => $formasi->unit_kerja ?: $formasi->lokasi_kerja,
                    'lokasi_penugasan' => $formasi->lokasi_kerja,
                    'nomor_sprint' => $request->nomor_sprint,
                    'tanggal_mulai' => $tglMulai,
                    'tanggal_selesai' => $tglSelesai,
                    'pic' => $request->pic,
                    'keterangan' => $request->keterangan,
                ]);

                return response()->json([
                    'success' => true,
                    'message' => "Berhasil mencatat penugasan {$karyawan->nama} ke unit {$formasi->unit_kerja}. Data tercatat di Data Penugasan."
                ]);
            });
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Gagal mencatat penugasan: ' . $e->getMessage()
            ], 500);
        }
    }
}