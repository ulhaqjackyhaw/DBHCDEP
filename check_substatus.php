<?php

require __DIR__ . '/vendor/autoload.php';

$app = require_once __DIR__ . '/bootstrap/app.php';
$app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();

echo "Checking sub_status values:\n";
echo "============================\n\n";

$results = DB::table('data_karyawan')
    ->select('sub_status', DB::raw('COUNT(*) as total'))
    ->whereNotNull('sub_status')
    ->where('sub_status', '!=', '')
    ->groupBy('sub_status')
    ->orderBy('total', 'desc')
    ->get();

foreach ($results as $r) {
    echo str_pad($r->sub_status, 30) . ': ' . $r->total . "\n";
}

echo "\n============================\n";
echo "Total with sub_status: " . $results->sum('total') . "\n";
echo "Empty/NULL sub_status: " . DB::table('data_karyawan')->where(function ($q) {
    $q->whereNull('sub_status')->orWhere('sub_status', '=', '');
})->count() . "\n";
