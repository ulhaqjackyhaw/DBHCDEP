<?php

require __DIR__ . '/vendor/autoload.php';

$app = require_once __DIR__ . '/bootstrap/app.php';
$app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();

use Illuminate\Support\Facades\DB;

echo "=== Distinct Pendidikan Values (KP) ===\n";
$distinctPendidikan = DB::table('data_karyawan')
    ->whereNotNull('sub_status')
    ->where('sub_status', '=', 'KP')
    ->whereNotNull('pendidikan_diakui')
    ->where('pendidikan_diakui', '!=', '')
    ->select('pendidikan_diakui', DB::raw('COUNT(*) as cnt'))
    ->groupBy('pendidikan_diakui')
    ->orderBy('cnt', 'desc')
    ->get();

foreach ($distinctPendidikan as $item) {
    echo "{$item->pendidikan_diakui}: {$item->cnt}\n";
}

echo "\n=== Sample Lokasi + Pendidikan ===\n";
$sample = DB::table('data_karyawan')
    ->whereNotNull('sub_status')
    ->where('sub_status', '=', 'KP')
    ->whereNotNull('pendidikan_diakui')
    ->where('pendidikan_diakui', '!=', '')
    ->whereNotNull('lokasi_kerja')
    ->where('lokasi_kerja', '!=', '')
    ->select('lokasi_kerja', 'pendidikan_diakui')
    ->limit(20)
    ->get();

foreach ($sample as $item) {
    echo "{$item->lokasi_kerja} => {$item->pendidikan_diakui}\n";
}

echo "\n=== Grouped by Lokasi + Pendidikan ===\n";
$grouped = DB::table('data_karyawan')
    ->whereNotNull('sub_status')
    ->where('sub_status', '=', 'KP')
    ->whereNotNull('pendidikan_diakui')
    ->where('pendidikan_diakui', '!=', '')
    ->whereNotNull('lokasi_kerja')
    ->where('lokasi_kerja', '!=', '')
    ->select('lokasi_kerja', 'pendidikan_diakui', DB::raw('COUNT(*) as jumlah'))
    ->groupBy('lokasi_kerja', 'pendidikan_diakui')
    ->orderBy('lokasi_kerja')
    ->orderBy('jumlah', 'desc')
    ->get();

foreach ($grouped as $item) {
    echo "{$item->lokasi_kerja} | {$item->pendidikan_diakui} | {$item->jumlah}\n";
}
