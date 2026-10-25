<?php
require __DIR__ . '/vendor/autoload.php';
$app = require_once __DIR__ . '/bootstrap/app.php';
$app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();

use Illuminate\Support\Facades\DB;

// Check Age Groups
echo "=== AGE GROUPS FOR KP ===\n";
$ageData = DB::table('data_karyawan')
    ->where('sub_status', 'KP')
    ->whereNotNull('tanggal_lahir')
    ->select(DB::raw("CASE 
        WHEN TIMESTAMPDIFF(YEAR, STR_TO_DATE(tanggal_lahir, '%d/%m/%Y'), CURDATE()) BETWEEN 13 AND 28 THEN 'Gen Z'
        WHEN TIMESTAMPDIFF(YEAR, STR_TO_DATE(tanggal_lahir, '%d/%m/%Y'), CURDATE()) BETWEEN 29 AND 44 THEN 'Gen Y (Milenial)'
        WHEN TIMESTAMPDIFF(YEAR, STR_TO_DATE(tanggal_lahir, '%d/%m/%Y'), CURDATE()) BETWEEN 45 AND 60 THEN 'Gen X'
        ELSE 'Lainnya'
    END as age_group"), DB::raw('COUNT(*) as total'))
    ->groupBy('age_group')
    ->get();

echo "Unique age groups:\n";
foreach ($ageData as $d) {
    echo "  - '{$d->age_group}' = {$d->total}\n";
}

// Check Tenure Groups
echo "\n=== TENURE GROUPS FOR KP ===\n";
$tenureData = DB::table('data_karyawan')
    ->where('sub_status', 'KP')
    ->whereNotNull('tmt_karyawan')
    ->select(DB::raw("CASE 
        WHEN TIMESTAMPDIFF(YEAR, STR_TO_DATE(tmt_karyawan, '%d/%m/%Y'), CURDATE()) < 1 THEN '0-1 thn'
        WHEN TIMESTAMPDIFF(YEAR, STR_TO_DATE(tmt_karyawan, '%d/%m/%Y'), CURDATE()) BETWEEN 1 AND 3 THEN '2-3 thn'
        WHEN TIMESTAMPDIFF(YEAR, STR_TO_DATE(tmt_karyawan, '%d/%m/%Y'), CURDATE()) BETWEEN 4 AND 6 THEN '4-6 thn'
        WHEN TIMESTAMPDIFF(YEAR, STR_TO_DATE(tmt_karyawan, '%d/%m/%Y'), CURDATE()) BETWEEN 7 AND 10 THEN '7-10 thn'
        ELSE '>10 thn'
    END as tenure_group"), DB::raw('COUNT(*) as total'))
    ->groupBy('tenure_group')
    ->get();

echo "Unique tenure groups:\n";
foreach ($tenureData as $d) {
    echo "  - '{$d->tenure_group}' = {$d->total}\n";
}
