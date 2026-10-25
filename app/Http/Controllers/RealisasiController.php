<?php

namespace App\Http\Controllers;

use App\Models\Realisasi;
use Illuminate\Http\Request;
use App\Imports\RealisasiImport;
use App\Exports\RealisasiExport;
use App\Exports\RealisasiTemplateExport;
use Maatwebsite\Excel\Facades\Excel;

class RealisasiController extends Controller
{
    public function index(Request $request)
    {
        $requestedYear = $request->integer('tahun');
        $maxYear = Realisasi::max('tahun');
        $selectedYear = $requestedYear ?: ($maxYear ?: (int) date('Y'));

        $items = Realisasi::when($selectedYear, fn($q) => $q->where('tahun', $selectedYear))
            ->latest()
            ->get();

        $years = Realisasi::select('tahun')->distinct()->orderBy('tahun', 'desc')->pluck('tahun');

        // Pastikan dropdown tetap punya opsi tahun yang dipilih meski belum ada data
        if ($selectedYear && !$years->contains($selectedYear)) {
            $years = $years->prepend($selectedYear);
        }

        return view('realisasi.index', [
            'items' => $items,
            'selectedYear' => $selectedYear,
            'years' => $years,
        ]);
    }

    public function create(Request $request)
    {
        $defaultYear = $request->integer('tahun') ?: (int) date('Y');
        return view('realisasi.create', compact('defaultYear'));
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'program_kerja' => 'required|string|max:255',
            'tahun' => 'required|integer|min:2000|max:2100',
            'rkap' => 'required|numeric|min:0',
            'realisasi_jan_mar' => 'nullable|numeric|min:0',
            'realisasi_jan_jun' => 'nullable|numeric|min:0',
            'realisasi_jul_sep' => 'nullable|numeric|min:0',
            'realisasi_jul_des' => 'nullable|numeric|min:0',
            'total' => 'nullable|numeric|min:0',
        ]);

        Realisasi::create($data);

        return redirect()->route('realisasi.index', ['tahun' => $data['tahun']])
            ->with('success', 'Data realisasi berhasil ditambahkan.');
    }

    public function show(Realisasi $realisasi)
    {
        return view('realisasi.show', compact('realisasi'));
    }

    public function edit(Realisasi $realisasi)
    {
        return view('realisasi.edit', compact('realisasi'));
    }

    public function update(Request $request, Realisasi $realisasi)
    {
        $data = $request->validate([
            'program_kerja' => 'required|string|max:255',
            'tahun' => 'required|integer|min:2000|max:2100',
            'rkap' => 'required|numeric|min:0',
            'realisasi_jan_mar' => 'nullable|numeric|min:0',
            'realisasi_jan_jun' => 'nullable|numeric|min:0',
            'realisasi_jul_sep' => 'nullable|numeric|min:0',
            'realisasi_jul_des' => 'nullable|numeric|min:0',
            'total' => 'nullable|numeric|min:0',
        ]);

        $realisasi->update($data);

        return redirect()->route('realisasi.index', ['tahun' => $data['tahun']])
            ->with('success', 'Data realisasi berhasil diperbarui.');
    }

    public function destroy(Realisasi $realisasi)
    {
        $year = $realisasi->tahun;
        $realisasi->delete();
        return redirect()->route('realisasi.index', ['tahun' => $year])
            ->with('success', 'Data realisasi berhasil dihapus.');
    }

    /**
     * Export data realisasi ke Excel
     */
    public function export(Request $request)
    {
        $tahun = $request->integer('tahun') ?: date('Y');
        $fileName = 'Realisasi_' . $tahun . '_' . date('YmdHis') . '.xlsx';

        return Excel::download(new RealisasiExport($tahun), $fileName);
    }

    /**
     * Download template Excel untuk import
     */
    public function downloadTemplate(Request $request)
    {
        $tahun = $request->integer('tahun') ?: date('Y');
        $fileName = 'Template_Realisasi_' . $tahun . '.xlsx';

        return Excel::download(new RealisasiTemplateExport($tahun), $fileName);
    }

    /**
     * Import data realisasi dari Excel
     */
    public function import(Request $request)
    {
        $request->validate([
            'file' => 'required|mimes:xlsx,xls,csv|max:5120', // Max 5MB
            'tahun' => 'required|integer|min:2000|max:2100',
            'import_mode' => 'required|in:add,replace',
        ]);

        try {
            $tahun = $request->integer('tahun');
            $file = $request->file('file');
            $mode = $request->input('import_mode');

            // Log untuk debugging
            \Log::info('Import Mode Received:', [
                'mode' => $mode,
                'tahun' => $tahun,
                'mode_type' => gettype($mode),
                'is_replace' => $mode === 'replace',
                'is_add' => $mode === 'add'
            ]);

            $countBefore = \App\Models\Realisasi::where('tahun', $tahun)->count();

            // Mode Ganti Semua: Hapus semua data tahun tersebut sebelum import
            if ($mode === 'replace') {
                $deleted = \App\Models\Realisasi::where('tahun', $tahun)->delete();
                \Log::info('Data deleted for replace mode:', ['count' => $deleted]);
            }

            Excel::import(new RealisasiImport($tahun, $mode), $file);
            $countAfter = \App\Models\Realisasi::where('tahun', $tahun)->count();
            $imported = $countAfter - $countBefore;

            // Catat ke audit log
            if (\Auth::check()) {
                \App\Models\AuditLog::create([
                    'user_id' => \Auth::id(),
                    'user_name' => \Auth::user()->name,
                    'user_email' => \Auth::user()->email,
                    'action' => 'bulk_created',
                    'model_type' => 'App\\Models\\Realisasi',
                    'model_id' => null,
                    'model_identifier' => 'Bulk Import - ' . $file->getClientOriginalName() . ' (Tahun: ' . $tahun . ')',
                    'old_values' => null,
                    'new_values' => [
                        'imported' => $imported,
                        'total_after' => $countAfter,
                        'tahun' => $tahun,
                        'mode' => $mode,
                        'filename' => $file->getClientOriginalName()
                    ],
                    'changes' => null,
                    'ip_address' => request()->ip(),
                    'user_agent' => request()->userAgent(),
                ]);
            }

            $modeText = $mode === 'replace' ? 'Mode Ganti Semua' : 'Mode Tambah';
            return redirect()->route('realisasi.index', ['tahun' => $tahun])
                ->with('success', "Data realisasi berhasil diimport ({$modeText}). Total data: {$countAfter}");
        } catch (\Maatwebsite\Excel\Validators\ValidationException $e) {
            $failures = $e->failures();

            foreach ($failures as $failure) {
                foreach ($failure->errors() as $error) {
                    session()->flash('error', "Baris {$failure->row()}: {$error}");
                }
            }

            return redirect()->back()
                ->withErrors($failures)
                ->withInput();
        } catch (\Exception $e) {
            return redirect()->back()
                ->withErrors(['file' => 'Terjadi kesalahan saat import: ' . $e->getMessage()])
                ->withInput();
        }
    }
}
