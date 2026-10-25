<?php
/**
 * Script untuk validasi file Excel sebelum import
 * Gunakan: php validate_excel.php "nama_file.xlsx"
 */

require __DIR__ . '/vendor/autoload.php';

use PhpOffice\PhpSpreadsheet\IOFactory;
use PhpOffice\PhpSpreadsheet\Shared\Date;

// Get file from argument
$file = $argv[1] ?? null;

if (!$file) {
    echo "❌ Usage: php validate_excel.php \"path/to/file.xlsx\"\n";
    exit(1);
}

if (!file_exists($file)) {
    echo "❌ File tidak ditemukan: $file\n";
    exit(1);
}

echo "🔍 Validasi Excel: $file\n";
echo str_repeat("=", 70) . "\n\n";

try {
    $spreadsheet = IOFactory::load($file);
    $worksheet = $spreadsheet->getActiveSheet();
    $rows = $worksheet->toArray();

    // Get header
    $header = array_shift($rows);

    echo "📋 Header Columns:\n";
    foreach ($header as $index => $col) {
        echo "   [" . chr(65 + $index) . "] $col\n";
    }
    echo "\n";

    $totalRows = count($rows);
    $validRows = 0;
    $emptyNik = [];
    $duplicates = [];
    $niks = [];
    $invalidData = [];

    echo "📊 Processing $totalRows baris data...\n\n";

    foreach ($rows as $index => $row) {
        $rowNum = $index + 2; // +2 karena header + 0-indexed
        $nik = trim($row[0] ?? '');
        $nama = trim($row[1] ?? '');

        // Check NIK kosong
        if (empty($nik)) {
            $emptyNik[] = [
                'row' => $rowNum,
                'nama' => $nama ?: 'N/A'
            ];
            continue;
        }

        // Check NIK duplikat dalam file
        if (isset($niks[$nik])) {
            $duplicates[] = [
                'row' => $rowNum,
                'nik' => $nik,
                'nama' => $nama,
                'first_row' => $niks[$nik]
            ];
            continue;
        }

        $niks[$nik] = $rowNum;
        $validRows++;
    }

    // Display results
    echo "✅ HASIL VALIDASI\n";
    echo str_repeat("-", 70) . "\n";
    echo "📊 Total baris dalam Excel: $totalRows\n";
    echo "✅ Baris valid (siap import): $validRows\n";
    echo "⚠️  NIK kosong: " . count($emptyNik) . "\n";
    echo "⚠️  NIK duplikat: " . count($duplicates) . "\n";
    echo str_repeat("=", 70) . "\n\n";

    // Show empty NIK details
    if (!empty($emptyNik)) {
        echo "❌ DETAIL NIK KOSONG (" . count($emptyNik) . " baris):\n";
        echo str_repeat("-", 70) . "\n";
        $shown = 0;
        foreach ($emptyNik as $item) {
            if ($shown >= 10) {
                echo "   ... dan " . (count($emptyNik) - 10) . " baris lainnya\n";
                break;
            }
            echo "   Baris {$item['row']}: {$item['nama']}\n";
            $shown++;
        }
        echo "\n";
    }

    // Show duplicate NIK details
    if (!empty($duplicates)) {
        echo "⚠️  DETAIL NIK DUPLIKAT (" . count($duplicates) . " baris):\n";
        echo str_repeat("-", 70) . "\n";
        $shown = 0;
        foreach ($duplicates as $item) {
            if ($shown >= 10) {
                echo "   ... dan " . (count($duplicates) - 10) . " baris lainnya\n";
                break;
            }
            echo "   Baris {$item['row']}: NIK {$item['nik']} - {$item['nama']}\n";
            echo "      (Duplikat dari baris {$item['first_row']})\n";
            $shown++;
        }
        echo "\n";
    }

    // Recommendations
    echo "💡 REKOMENDASI:\n";
    echo str_repeat("-", 70) . "\n";

    if (count($emptyNik) > 0) {
        echo "   1. Hapus atau isi NIK untuk " . count($emptyNik) . " baris yang kosong\n";
    }

    if (count($duplicates) > 0) {
        echo "   2. Hapus atau perbaiki " . count($duplicates) . " baris dengan NIK duplikat\n";
    }

    if ($validRows == $totalRows) {
        echo "   ✅ File siap untuk di-import! Semua data valid.\n";
    } else {
        echo "   ⚠️  Perbaiki masalah di atas sebelum import untuk hasil maksimal.\n";
    }

    echo "\n";

    // Check database duplicates
    echo "🔍 CEK DATABASE DUPLIKAT:\n";
    echo str_repeat("-", 70) . "\n";
    echo "Jalankan query ini untuk cek NIK yang sudah ada di database:\n\n";

    $nikList = array_slice(array_keys($niks), 0, 5);
    echo "SELECT nik, nama FROM data_karyawan WHERE nik IN ('" . implode("','", $nikList) . "', ...);\n\n";

    echo "Atau gunakan Laravel Tinker:\n";
    echo "php artisan tinker\n";
    echo "\$niks = ['" . implode("','", $nikList) . "'];\n";
    echo "\$existing = \\App\\Models\\DataKaryawan::whereIn('nik', \$niks)->pluck('nik')->toArray();\n";
    echo "print_r(\$existing);\n";
    echo "\n";

    exit($validRows == $totalRows ? 0 : 1);

} catch (\Exception $e) {
    echo "❌ ERROR: " . $e->getMessage() . "\n";
    exit(1);
}
