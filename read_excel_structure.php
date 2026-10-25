<?php

require __DIR__ . '/vendor/autoload.php';

use PhpOffice\PhpSpreadsheet\IOFactory;

$excelFile = __DIR__ . '/data_karyawan_2025.xlsx';

if (!file_exists($excelFile)) {
    echo "❌ File tidak ditemukan: $excelFile\n";
    exit;
}

echo "📂 Membaca file: data_karyawan_2025.xlsx\n\n";

$spreadsheet = IOFactory::load($excelFile);
$sheet = $spreadsheet->getActiveSheet();

// Baca header (baris pertama)
$headers = [];
$highestColumn = $sheet->getHighestColumn();
$highestColumnIndex = \PhpOffice\PhpSpreadsheet\Cell\Coordinate::columnIndexFromString($highestColumn);

echo "📋 STRUKTUR KOLOM EXCEL:\n";
echo str_repeat('=', 80) . "\n";

for ($col = 1; $col <= $highestColumnIndex; $col++) {
    $cellValue = $sheet->getCellByColumnAndRow($col, 1)->getValue();
    if ($cellValue) {
        $headers[] = $cellValue;
        echo sprintf("%2d. %-50s", $col, $cellValue);

        // Konversi ke nama field database
        $fieldName = strtolower(str_replace([' ', '/', '(', ')'], ['_', '_', '', ''], $cellValue));
        $fieldName = preg_replace('/_+/', '_', $fieldName);
        $fieldName = trim($fieldName, '_');

        echo " → " . $fieldName . "\n";
    }
}

echo "\n" . str_repeat('=', 80) . "\n";
echo "📊 Total kolom: " . count($headers) . "\n\n";

// Baca beberapa baris data untuk melihat format
echo "📊 SAMPLE DATA (3 baris pertama):\n";
echo str_repeat('=', 80) . "\n";

for ($row = 2; $row <= min(4, $sheet->getHighestRow()); $row++) {
    echo "\nBaris $row:\n";
    for ($col = 1; $col <= min(10, $highestColumnIndex); $col++) {
        $header = $sheet->getCellByColumnAndRow($col, 1)->getValue();
        $value = $sheet->getCellByColumnAndRow($col, $row)->getValue();
        echo sprintf("  %-30s: %s\n", $header, $value);
    }
}

echo "\n" . str_repeat('=', 80) . "\n";
echo "✅ Selesai membaca struktur Excel\n";
