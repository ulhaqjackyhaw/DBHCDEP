<?php
require __DIR__ . '/vendor/autoload.php';
$app = require_once __DIR__ . '/bootstrap/app.php';
$app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();

use Illuminate\Support\Facades\DB;

echo "Total KP: " . DB::table('data_karyawan')->where('sub_status', 'KP')->count() . "\n";
echo "KP with gender: " . DB::table('data_karyawan')->where('sub_status', 'KP')->whereNotNull('jenis_kelamin')->count() . "\n";
echo "KP with lokasi: " . DB::table('data_karyawan')->where('sub_status', 'KP')->whereNotNull('lokasi_kerja')->count() . "\n\n";

// Check unique values
echo "Unique sub_status: \n";
$statuses = DB::table('data_karyawan')->select('sub_status')->distinct()->get();
foreach ($statuses as $s) {
    echo "  - '{$s->sub_status}'\n";
}

echo "\nUnique jenis_kelamin for KP:\n";
$genders = DB::table('data_karyawan')->where('sub_status', 'KP')->select('jenis_kelamin')->distinct()->get();
foreach ($genders as $g) {
    echo "  - '{$g->jenis_kelamin}'\n";
}

$data = DB::table('data_karyawan')
    ->where('sub_status', 'KP')
    ->select('lokasi_kerja', 'jenis_kelamin', DB::raw('COUNT(*) as total'))
    ->whereNotNull('jenis_kelamin')
    ->whereNotNull('lokasi_kerja')
    ->groupBy('lokasi_kerja', 'jenis_kelamin')
    ->orderBy('lokasi_kerja')
    ->get();

echo "\nGender Data for Organic (KP):\n";
echo json_encode($data, JSON_PRETTY_PRINT);
