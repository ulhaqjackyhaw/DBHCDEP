<?php

require __DIR__ . '/vendor/autoload.php';

$app = require_once __DIR__ . '/bootstrap/app.php';
$app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();

use App\Models\DataKaryawan;
use Illuminate\Support\Facades\DB;

echo "=== CEK DATA PENDIDIKAN ===\n\n";

// 1. Total karyawan
$total = DataKaryawan::count();
echo "Total Karyawan: $total\n\n";

// 1.5 Cek distinct values
echo "Distinct Values Pendidikan:\n";
$distinct = DataKaryawan::select('pendidikan_diakui')->distinct()->get();
foreach ($distinct as $d) {
    echo "  '{$d->pendidikan_diakui}'\n";
}
echo "\n";

// 2. Data pendidikan
$pend = DataKaryawan::select(
    DB::raw("COALESCE(pendidikan_diakui,'(Kosong)') AS pend_label"),
    DB::raw('COUNT(*) AS total')
)
    ->groupBy('pend_label')
    ->orderByDesc('total')
    ->get();

echo "Data Pendidikan:\n";
foreach ($pend as $item) {
    echo "  {$item->pend_label}: {$item->total}\n";
}
echo "\n";

// 3. Data pendidikan grouped (Organik vs Outsourcing)
$pendidikanStatus = DataKaryawan::select(
    DB::raw("COALESCE(pendidikan_diakui,'(Kosong)') AS pend_label"),
    DB::raw("SUM(CASE WHEN UPPER(TRIM(sub_status)) IN ('ALIH DAYA','OUTSOURCING','OS','OUTSOURCE') OR LOWER(TRIM(sub_status)) LIKE '%alih%' OR LOWER(TRIM(sub_status)) LIKE '%outsour%' THEN 1 ELSE 0 END) AS os"),
    DB::raw("SUM(CASE WHEN UPPER(TRIM(sub_status)) IN ('ALIH DAYA','OUTSOURCING','OS','OUTSOURCE') OR LOWER(TRIM(sub_status)) LIKE '%alih%' OR LOWER(TRIM(sub_status)) LIKE '%outsour%' THEN 0 ELSE 1 END) AS organic")
)
    ->groupBy('pend_label')
    ->orderByRaw("
        CASE pend_label
            WHEN 'SMP' THEN 1
            WHEN 'SMA/SMK' THEN 2
            WHEN 'D2' THEN 3
            WHEN 'D3' THEN 4
            WHEN 'S1' THEN 5
            WHEN 'S2' THEN 6
            WHEN 'S3' THEN 7
            ELSE 99
        END ASC
    ")
    ->get();

echo "Data Pendidikan Grouped (Organik vs Outsourcing):\n";
foreach ($pendidikanStatus as $item) {
    echo "  {$item->pend_label}: Organik={$item->organic}, OS={$item->os}\n";
}
echo "\n";

$pendGroupLabels = $pendidikanStatus->pluck('pend_label')->toArray();
$pendGroupOS = $pendidikanStatus->pluck('os')->map(fn($v) => (int) $v)->toArray();
$pendGroupOrganic = $pendidikanStatus->pluck('organic')->map(fn($v) => (int) $v)->toArray();

echo "Array untuk Frontend:\n";
echo "pendGroupLabels: " . json_encode($pendGroupLabels) . "\n";
echo "pendGroupOS: " . json_encode($pendGroupOS) . "\n";
echo "pendGroupOrganic: " . json_encode($pendGroupOrganic) . "\n";
echo "\n";

echo "hasPendGrouped: " . (count($pendGroupLabels) > 0 ? 'true' : 'false') . "\n";
