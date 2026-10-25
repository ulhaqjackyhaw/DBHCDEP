<?php

namespace App\Http\Controllers;

use App\Models\Formasi;
use Illuminate\Http\Request;
use Maatwebsite\Excel\Facades\Excel;
use App\Imports\FormasiImport;
use App\Exports\FormasiExport;
use App\Exports\FormasiTemplateExport;
use App\Exports\SemuaJabatanLengkapExport;
use Illuminate\Validation\Rule;

class FormasiController extends Controller
{
    public function index()
    {
        $formasi = Formasi::latest()->get();
        return view('formasi.index', compact('formasi'));
    }

    public function create()
    {
        return view('formasi.create');
    }

    public function store(Request $request)
    {
        $validatedData = $request->validate([
            'kode_jabatan' => 'required|string',
            'unit_deputy_egm' => 'nullable|string|max:255',
            'unit_assistant_deputy' => 'nullable|string|max:255',
            'unit_division_head' => 'nullable|string|max:255',
            'unit_department_head' => 'nullable|string|max:255',
            'lokasi_kerja' => 'required|string|max:255',
            'unit_kerja' => 'required|string|max:255',
            'jabatan' => 'required|string|max:255',
            'kelompok_kelas_jabatan' => 'required|string|max:255',
            'grade' => 'required|string|max:255',
            'kuota' => 'required|integer|min:1',
        ]);

        // Cek unique kombinasi kode_jabatan, lokasi_kerja, unit_kerja
        if (
            Formasi::where('kode_jabatan', $validatedData['kode_jabatan'])
                ->where('lokasi_kerja', $validatedData['lokasi_kerja'])
                ->where('unit_kerja', $validatedData['unit_kerja'])
                ->exists()
        ) {
            return redirect()->back()->with('error', 'Formasi dengan kombinasi kode jabatan, lokasi kerja, dan unit kerja sudah ada.');
        }

        Formasi::create($validatedData);
        return redirect()->route('formasi.index')->with('success', 'Data formasi berhasil ditambahkan.');
    }

    public function show(Formasi $formasi)
    {
        return view('formasi.show', compact('formasi'));
    }

    public function edit(Formasi $formasi)
    {
        return view('formasi.edit', compact('formasi'));
    }

    public function update(Request $request, Formasi $formasi)
    {
        $validatedData = $request->validate([
            'kode_jabatan' => ['required', 'string'],
            'unit_deputy_egm' => 'nullable|string|max:255',
            'unit_assistant_deputy' => 'nullable|string|max:255',
            'unit_division_head' => 'nullable|string|max:255',
            'unit_department_head' => 'nullable|string|max:255',
            'lokasi_kerja' => 'required|string|max:255',
            'unit_kerja' => 'required|string|max:255',
            'jabatan' => 'required|string|max:255',
            'kelompok_kelas_jabatan' => 'required|string|max:255',
            'grade' => 'required|string|max:255',
            'kuota' => 'required|integer|min:1',
        ]);

        // Cek unique kombinasi kode_jabatan, lokasi_kerja, unit_kerja (kecuali diri sendiri)
        if (
            Formasi::where('kode_jabatan', $validatedData['kode_jabatan'])
                ->where('lokasi_kerja', $validatedData['lokasi_kerja'])
                ->where('unit_kerja', $validatedData['unit_kerja'])
                ->where('id', '!=', $formasi->id)
                ->exists()
        ) {
            return redirect()->back()->with('error', 'Formasi dengan kombinasi kode jabatan, lokasi kerja, dan unit kerja sudah ada.');
        }

        $formasi->update($validatedData);
        return redirect()->route('formasi.index')->with('success', 'Data formasi berhasil diperbarui.');
    }

    public function destroy(Formasi $formasi)
    {
        $formasi->delete();
        return redirect()->route('formasi.index')->with('success', 'Data formasi berhasil dihapus.');
    }

    public function export()
    {
        return Excel::download(new FormasiExport, 'formasi_' . date('Y-m-d') . '.xlsx');
    }

    public function importAdd(Request $request)
    {
        $request->validate(['file' => 'required|mimes:xlsx,csv']);

        try {
            // OPTIMISASI UNTUK FILE BESAR: Tanpa batasan jumlah baris
            // - Memory limit 1GB untuk menangani puluhan ribu baris
            // - Execution time 5 menit untuk proses import yang lama
            // - Batch size 5000 untuk performa optimal
            // - Chunk reading untuk efisiensi memory
            ini_set('memory_limit', '1024M');
            ini_set('max_execution_time', 300); // 5 menit

            $countBefore = Formasi::count();
            Excel::import(new FormasiImport, $request->file('file'));
            $countAfter = Formasi::count();
            $imported = $countAfter - $countBefore;

            // Catat ke audit log
            if (\Auth::check()) {
                \App\Models\AuditLog::create([
                    'user_id' => \Auth::id(),
                    'user_name' => \Auth::user()->name,
                    'user_email' => \Auth::user()->email,
                    'action' => 'bulk_created',
                    'model_type' => 'App\\Models\\Formasi',
                    'model_id' => null,
                    'model_identifier' => 'Bulk Import - ' . $request->file('file')->getClientOriginalName(),
                    'old_values' => null,
                    'new_values' => [
                        'imported' => $imported,
                        'total_after' => $countAfter,
                        'mode' => 'add',
                        'filename' => $request->file('file')->getClientOriginalName()
                    ],
                    'changes' => null,
                    'ip_address' => request()->ip(),
                    'user_agent' => request()->userAgent(),
                ]);
            }

            return redirect()->route('formasi.index')->with('success', 'Data formasi berhasil ditambahkan.');
        } catch (\Exception $e) {
            return redirect()->back()->with('error', 'Terjadi kesalahan: ' . $e->getMessage());
        }
    }

    public function importReplace(Request $request)
    {
        $request->validate(['file' => 'required|mimes:xlsx,csv']);

        try {
            // OPTIMISASI UNTUK FILE BESAR: Tanpa batasan jumlah baris
            // - Memory limit 1GB untuk menangani puluhan ribu baris  
            // - Execution time 5 menit untuk proses import yang lama
            // - Hapus semua data lama kemudian import data baru
            ini_set('memory_limit', '1024M');
            ini_set('max_execution_time', 300); // 5 menit

            $countBefore = Formasi::count();
            Formasi::query()->delete();
            Excel::import(new FormasiImport, $request->file('file'));
            $countAfter = Formasi::count();

            // Catat ke audit log
            if (\Auth::check()) {
                \App\Models\AuditLog::create([
                    'user_id' => \Auth::id(),
                    'user_name' => \Auth::user()->name,
                    'user_email' => \Auth::user()->email,
                    'action' => 'bulk_replaced',
                    'model_type' => 'App\\Models\\Formasi',
                    'model_id' => null,
                    'model_identifier' => 'Bulk Import - ' . $request->file('file')->getClientOriginalName(),
                    'old_values' => ['total_records' => $countBefore],
                    'new_values' => [
                        'imported' => $countAfter,
                        'total_after' => $countAfter,
                        'mode' => 'replace',
                        'filename' => $request->file('file')->getClientOriginalName()
                    ],
                    'changes' => null,
                    'ip_address' => request()->ip(),
                    'user_agent' => request()->userAgent(),
                ]);
            }

            return redirect()->route('formasi.index')->with('success', 'Semua data formasi berhasil diganti.');
        } catch (\Exception $e) {
            return redirect()->back()->with('error', 'Terjadi kesalahan: ' . $e->getMessage());
        }
    }

    public function downloadTemplate()
    {
        return Excel::download(new FormasiTemplateExport, 'template_formasi.xlsx');
    }

    public function exportSemuaJabatanLengkap()
    {
        return Excel::download(new SemuaJabatanLengkapExport, 'semua_jabatan_lengkap_' . date('Y-m-d_His') . '.xlsx');
    }
}
