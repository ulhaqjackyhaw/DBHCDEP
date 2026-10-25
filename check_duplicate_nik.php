<?php
/**
 * Script untuk mengecek NIK duplikat di file Excel
 * Usage: php check_duplicate_nik.php path/to/file.xlsx
 */

require __DIR__ . '/vendor/autoload.php';

use PhpOffice\PhpSpreadsheet\IOFactory;

if ($argc < 2) {
    echo "Usage: php check_duplicate_nik.php <path-to-excel-file>\n";
    exit(1);
}

$filePath = $argv[1];

if (!file_exists($filePath)) {
    echo "Error: File tidak ditemukan: {$filePath}\n";
    exit(1);
}

echo "Membaca file: {$filePath}\n";
echo str_repeat("=", 80) . "\n\n";

$spreadsheet = IOFactory::load($filePath);
$sheet = $spreadsheet->getActiveSheet();
$highestRow = $sheet->getHighestRow();

echo "Total baris: {$highestRow}\n\n";

// Ambil header dari baris pertama
$headers = [];
$highestColumn = $sheet->getHighestColumn();
$highestColumnIndex = \PhpOffice\PhpSpreadsheet\Cell\Coordinate::columnIndexFromString($highestColumn);

for ($col = 1; $col <= $highestColumnIndex; $col++) {
    $value = $sheet->getCellByColumnAndRow($col, 1)->getValue();
    $headers[$col] = strtolower(str_replace(' ', '_', trim($value)));
}

// Cari kolom NIK
$nikColumn = null;
foreach ($headers as $col => $header) {
    if ($header === 'nik') {
        $nikColumn = $col;
        break;
    }
}

if (!$nikColumn) {
    echo "Error: Kolom NIK tidak ditemukan!\n";
    echo "Header yang ditemukan: " . implode(', ', $headers) . "\n";
    exit(1);
}

echo "✓ Kolom NIK ditemukan di kolom " . \PhpOffice\PhpSpreadsheet\Cell\Coordinate::stringFromColumnIndex($nikColumn) . "\n\n";

// Scan semua NIK
$nikList = [];
$duplicates = [];
$emptyNik = [];

for ($row = 2; $row <= $highestRow; $row++) {
    $nik = trim($sheet->getCellByColumnAndRow($nikColumn, $row)->getValue());

    if (empty($nik)) {
        $emptyNik[] = $row;
        continue;
    }

    if (isset($nikList[$nik])) {
        if (!isset($duplicates[$nik])) {
            $duplicates[$nik] = [$nikList[$nik]];
        }
        $duplicates[$nik][] = $row;
    } else {
        $nikList[$nik] = $row;
    }
}

// Tampilkan hasil
echo "HASIL ANALISIS:\n";
echo str_repeat("=", 80) . "\n\n";

if (empty($emptyNik) && empty($duplicates)) {
    echo "✅ TIDAK ADA MASALAH\n";
    echo "   - Semua NIK terisi\n";
    echo "   - Tidak ada NIK duplikat\n";
    echo "   - Total NIK unik: " . count($nikList) . "\n";
} else {
    $hasIssues = false;

    if (!empty($emptyNik)) {
        $hasIssues = true;
        echo "⚠️  NIK KOSONG: " . count($emptyNik) . " baris\n";
        echo "   Baris: " . implode(', ', array_slice($emptyNik, 0, 10));
        if (count($emptyNik) > 10) {
            echo " ... (+" . (count($emptyNik) - 10) . " lagi)";
        }
        echo "\n\n";
    }

    if (!empty($duplicates)) {
        $hasIssues = true;
        echo "❌ NIK DUPLIKAT: " . count($duplicates) . " NIK\n\n";

        $count = 0;
        foreach ($duplicates as $nik => $rows) {
            $count++;
            echo "   {$count}. NIK: {$nik}\n";
            echo "      Muncul di baris: " . implode(', ', $rows) . " (" . count($rows) . "x)\n";

            // Tampilkan nama karyawan jika ada
            $namaColumn = array_search('nama', $headers);
            if ($namaColumn) {
                echo "      Nama karyawan:\n";
                foreach ($rows as $r) {
                    $nama = $sheet->getCellByColumnAndRow($namaColumn, $r)->getValue();
                    echo "        - Baris {$r}: {$nama}\n";
                }
            }
            echo "\n";

            if ($count >= 20 && count($duplicates) > 20) {
                echo "   ... (" . (count($duplicates) - 20) . " NIK duplikat lainnya)\n\n";
                break;
            }
        }
    }

    echo str_repeat("=", 80) . "\n";
    echo "RINGKASAN:\n";
    echo "   - Total baris data: " . ($highestRow - 1) . "\n";
    echo "   - NIK unik: " . count($nikList) . "\n";
    echo "   - NIK kosong: " . count($emptyNik) . "\n";
    echo "   - NIK duplikat: " . count($duplicates) . "\n";
    echo "   - Total baris bermasalah: " . (count($emptyNik) + array_sum(array_map('count', $duplicates)) - count($duplicates)) . "\n";
}

echo "\n" . str_repeat("=", 80) . "\n";
echo "REKOMENDASI:\n";

if (!empty($duplicates)) {
    echo "   1. Periksa baris-baris dengan NIK duplikat di atas\n";
    echo "   2. Pastikan setiap karyawan memiliki NIK yang unik\n";
    echo "   3. Jika ada karyawan yang sama, hapus baris duplikatnya\n";
    echo "   4. Jika NIK salah, perbaiki NIK yang salah\n";
}

if (!empty($emptyNik)) {
    echo "   5. Isi NIK yang kosong atau hapus baris tersebut\n";
}

if (empty($emptyNik) && empty($duplicates)) {
    echo "   ✅ File siap untuk diupload!\n";
}

echo "\n";
