<?php

namespace App\Jobs;

use App\Imports\DataKaryawanImport;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Cache;
use Maatwebsite\Excel\Facades\Excel;

class ImportDataKaryawanJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public $timeout = 3600; // 1 jam batas eksekusi job
    public $tries = 1;

    protected $filePath;
    protected $sessionId;
    protected $mode; // 'add' or 'replace'

    public function __construct($filePath, $sessionId, $mode = 'add')
    {
        $this->filePath = $filePath;
        $this->sessionId = $sessionId;
        $this->mode = $mode;
    }

    public function handle()
    {
        // Alokasikan memori dan waktu tanpa batas untuk file puluhan ribu baris
        @ini_set('memory_limit', '2048M');
        @set_time_limit(0);

        try {
            // Initialize progress
            Cache::put("import_progress_{$this->sessionId}", [
                'progress' => 1,
                'status' => 'processing',
                'message' => 'Memulai proses import...'
            ], 3600);

            if ($this->mode === 'replace') {
                Cache::put("import_progress_{$this->sessionId}", [
                    'progress' => 3,
                    'status' => 'processing',
                    'message' => 'Menghapus data lama...'
                ], 3600);

                \App\Models\DataKaryawan::query()->delete();
            }

            // Hitung total baris file Excel secara instan tanpa load seluruh data ke RAM
            $totalRows = 0;
            try {
                $reader = \PhpOffice\PhpSpreadsheet\IOFactory::createReaderForFile($this->filePath);
                $reader->setReadDataOnly(true);
                $worksheetInfo = $reader->listWorksheetInfo($this->filePath);
                if (!empty($worksheetInfo) && isset($worksheetInfo[0]['totalRows'])) {
                    $totalRows = max(0, $worksheetInfo[0]['totalRows'] - 1); // kurangi 1 baris header
                }
            } catch (\Throwable $e) {
                \Log::warning("Gagal menghitung info worksheet: " . $e->getMessage());
            }

            Cache::put("import_progress_{$this->sessionId}", [
                'progress' => 5,
                'status' => 'processing',
                'message' => 'Membaca file Excel' . ($totalRows > 0 ? " ({$totalRows} baris)..." : "...")
            ], 3600);

            // Track count sebelum import
            $countBefore = \App\Models\DataKaryawan::count();

            $import = new DataKaryawanImport($this->sessionId);
            if ($totalRows > 0) {
                $import->setTotalRows($totalRows);
            }

            Excel::import($import, $this->filePath);

            // Track count setelah import
            $countAfter = \App\Models\DataKaryawan::count();
            $imported = $countAfter - $countBefore;

            $skipped = $import->getSkippedCount();
            $skippedNiks = $import->getSkippedNiks();
            $duplicateNiks = $import->getDuplicateNiks();

            // Hitung yang di-update vs insert baru
            $inserted = max(0, $imported);
            $updated = max(0, $countAfter - $countBefore - $inserted);

            $message = $this->mode === 'replace'
                ? "Data berhasil diganti. Total: {$countAfter} karyawan."
                : "Import selesai! Berhasil: {$imported} data, Dilewati: {$skipped} baris (NIK kosong).";

            if ($skipped > 0) {
                $emptyNikCount = count($skippedNiks);
                $message .= " Detail: {$emptyNikCount} baris dengan NIK kosong di-skip.";
            }

            // Catat ke audit log
            if (\Auth::check()) {
                \App\Models\AuditLog::create([
                    'user_id' => \Auth::id(),
                    'user_name' => \Auth::user()->name,
                    'user_email' => \Auth::user()->email,
                    'action' => $this->mode === 'replace' ? 'bulk_replaced' : 'bulk_created',
                    'model_type' => 'App\\Models\\DataKaryawan',
                    'model_id' => null,
                    'model_identifier' => 'Bulk Import - ' . basename($this->filePath),
                    'old_values' => $this->mode === 'replace' ? ['total_records' => $countBefore] : null,
                    'new_values' => [
                        'imported' => $imported,
                        'skipped' => $skipped,
                        'total_after' => $countAfter,
                        'mode' => $this->mode,
                        'filename' => basename($this->filePath)
                    ],
                    'changes' => null,
                    'ip_address' => request()->ip(),
                    'user_agent' => request()->userAgent(),
                ]);
            }

            Cache::put("import_progress_{$this->sessionId}", [
                'progress' => 100,
                'status' => 'completed',
                'message' => $message,
                'skipped' => $skipped,
                'imported' => $countAfter, // Total data akhir di database
                'skipped_details' => [
                    'empty_nik' => $skippedNiks,
                    'duplicate_nik' => [], // No longer relevant with upsert
                ],
                'total_processed' => $countAfter + $skipped,
            ], 3600);

        } catch (\Exception $e) {
            \Log::error("Import Job Error: " . $e->getMessage());

            Cache::put("import_progress_{$this->sessionId}", [
                'progress' => 0,
                'status' => 'error',
                'message' => 'Terjadi kesalahan: ' . $e->getMessage()
            ], 3600);
        } finally {
            // Cleanup file
            if (file_exists($this->filePath)) {
                @unlink($this->filePath);
            }
        }
    }
}
