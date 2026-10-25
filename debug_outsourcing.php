<?php

require __DIR__ . '/vendor/autoload.php';

$app = require_once __DIR__ . '/bootstrap/app.php';
$app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();

use Illuminate\Support\Facades\DB;

echo "=== OUTSOURCING DATA ===\n\n";

echo "=== Distinct Gender Values (ALIH DAYA) ===\n";
$distinctGender = DB::table('data_karyawan')
    ->whereNotNull('sub_status')
    ->where('sub_status', '=', 'ALIH DAYA')
    ->whereNotNull('jenis_kelamin')
    ->select('jenis_kelamin', DB::raw('COUNT(*) as cnt'))
    ->groupBy('jenis_kelamin')
    ->orderBy('cnt', 'desc')
    ->get();

foreach ($distinctGender as $item) {
    echo "'{$item->jenis_kelamin}': {$item->cnt}\n";
}

echo "\n=== Gender per Lokasi ===\n";
$genderLokasi = DB::table('data_karyawan')
    ->whereNotNull('sub_status')
    ->where('sub_status', '=', 'ALIH DAYA')
    ->whereNotNull('jenis_kelamin')
    ->whereNotNull('lokasi_kerja')
    ->select('lokasi_kerja', 'jenis_kelamin', DB::raw('COUNT(*) as jumlah'))
    ->groupBy('lokasi_kerja', 'jenis_kelamin')
    ->orderBy('lokasi_kerja')
    ->orderBy('jumlah', 'desc')
    ->get();

foreach ($genderLokasi as $item) {
    echo "{$item->lokasi_kerja} | '{$item->jenis_kelamin}' | {$item->jumlah}\n";
}

echo "\n=== Distinct Asal Instansi ===\n";
$distinctInstansi = DB::table('data_karyawan')
    ->whereNotNull('sub_status')
    ->where('sub_status', '=', 'ALIH DAYA')
    ->select('asal_instansi', DB::raw('COUNT(*) as cnt'))
    ->groupBy('asal_instansi')
    ->orderBy('cnt', 'desc')
    ->get();

foreach ($distinctInstansi as $item) {
    $instansi = $item->asal_instansi ?? '(NULL)';
    echo "'{$instansi}': {$item->cnt}\n";
}

echo "\n=== Distinct INSTANSI (kolom instansi) ===\n";
$distinctInstansiColumn = DB::table('data_karyawan')
    ->whereNotNull('sub_status')
    ->where('sub_status', '=', 'ALIH DAYA')
    ->select('instansi', DB::raw('COUNT(*) as cnt'))
    ->groupBy('instansi')
    ->orderBy('cnt', 'desc')
    ->get();

foreach ($distinctInstansiColumn as $item) {
    $instansi = $item->instansi ?? '(NULL)';
    echo "'{$instansi}': {$item->cnt}\n";
}

echo "\n=== Instansi per Lokasi (kolom instansi) ===\n";
$instansiLokasi = DB::table('data_karyawan')
    ->whereNotNull('sub_status')
    ->where('sub_status', '=', 'ALIH DAYA')
    ->whereNotNull('lokasi_kerja')
    ->select('lokasi_kerja', 'instansi', DB::raw('COUNT(*) as jumlah'))
    ->groupBy('lokasi_kerja', 'instansi')
    ->orderBy('lokasi_kerja')
    ->orderBy('jumlah', 'desc')
    ->get();

foreach ($instansiLokasi as $item) {
    $instansi = $item->instansi ?? '(NULL)';
    echo "{$item->lokasi_kerja} | '{$instansi}' | {$item->jumlah}\n";
}

echo "\n=== Sample Row Data (First 3) ===\n";
$sampleRows = DB::table('data_karyawan')
    ->whereNotNull('sub_status')
    ->where('sub_status', '=', 'ALIH DAYA')
    ->limit(3)
    ->get();

foreach ($sampleRows as $row) {
    echo "Nama: {$row->nama}\n";
    echo "Lokasi: {$row->lokasi_kerja}\n";
    echo "Unit: {$row->unit_kerja}\n";
    echo "Asal Instansi: " . ($row->asal_instansi ?? '(null)') . "\n";
    echo "Instansi: " . ($row->instansi ?? '(null)') . "\n";
    echo "Sub Status: {$row->sub_status}\n";
    echo "---\n";
}
