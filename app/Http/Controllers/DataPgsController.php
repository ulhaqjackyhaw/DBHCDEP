<?php

namespace App\Http\Controllers;

use App\Models\DataPgs;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;
use Maatwebsite\Excel\Facades\Excel;
use App\Imports\DataPgsImport;
use App\Exports\DataPgsExport;
use App\Exports\DataPgsTemplateExport;

class DataPgsController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $dataPgs = DataPgs::latest()->get();
        return view('data-pgs.index', compact('dataPgs'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        return view('data-pgs.create');
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $validatedData = $request->validate([
            'nik' => 'required|string|unique:data_pgs,nik',
            'nama' => 'required|string|max:255',
            'jabatan_definitif' => 'required|string|max:255',
            'jabatan_pgs' => 'required|string|max:255',
            'lokasi_unit_kerja' => 'required|string|max:255',
            'tanggal_pgs' => 'required|date',
            'tanggal_selesai_pgs' => 'nullable|date|after_or_equal:tanggal_pgs',
        ]);

        // Konversi format tanggal dari Y-m-d ke d/m/Y untuk konsistensi database
        $tanggalPgs = $validatedData['tanggal_pgs'];
        try {
            $tanggalPgs = \Carbon\Carbon::createFromFormat('Y-m-d', $tanggalPgs)->format('d/m/Y');
        } catch (\Exception $e) {
            // Jika gagal convert, gunakan format asli
        }

        $tanggalSelesaiPgs = $validatedData['tanggal_selesai_pgs'] ?? null;
        if ($tanggalSelesaiPgs) {
            try {
                $tanggalSelesaiPgs = \Carbon\Carbon::createFromFormat('Y-m-d', $tanggalSelesaiPgs)->format('d/m/Y');
            } catch (\Exception $e) {
                // Jika gagal convert, gunakan format asli
            }
        }

        $data = [
            'nik' => $validatedData['nik'],
            'nama' => $validatedData['nama'],
            'jabatan_definitif' => $validatedData['jabatan_definitif'],
            'jabatan_pgs' => $validatedData['jabatan_pgs'],
            'lokasi_unit_kerja' => $validatedData['lokasi_unit_kerja'],
            'tanggal_pgs' => $tanggalPgs,
            'tanggal_selesai_pgs' => $tanggalSelesaiPgs,
        ];

        DataPgs::create($data);

        return redirect()->route('data-pgs.index')->with('success', 'Data PGS berhasil ditambahkan.');
    }

    /**
     * Display the specified resource.
     */
    public function show(DataPgs $dataPgs)
    {
        return view('data-pgs.show', compact('dataPgs'));
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(DataPgs $dataPgs)
    {
        return view('data-pgs.edit', compact('dataPgs'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, DataPgs $dataPgs)
    {
        $validatedData = $request->validate([
            'nik' => ['required', 'string', Rule::unique('data_pgs')->ignore($dataPgs->id)],
            'nama' => 'required|string|max:255',
            'jabatan_definitif' => 'required|string|max:255',
            'jabatan_pgs' => 'required|string|max:255',
            'lokasi_unit_kerja' => 'required|string|max:255',
            'tanggal_pgs' => 'required|date',
            'tanggal_selesai_pgs' => 'nullable|date|after_or_equal:tanggal_pgs',
        ]);

        // Konversi format tanggal dari Y-m-d ke d/m/Y untuk konsistensi database
        $tanggalPgs = $validatedData['tanggal_pgs'];
        try {
            $tanggalPgs = \Carbon\Carbon::createFromFormat('Y-m-d', $tanggalPgs)->format('d/m/Y');
        } catch (\Exception $e) {
            // Jika gagal convert, gunakan format asli
        }

        $tanggalSelesaiPgs = $validatedData['tanggal_selesai_pgs'] ?? null;
        if ($tanggalSelesaiPgs) {
            try {
                $tanggalSelesaiPgs = \Carbon\Carbon::createFromFormat('Y-m-d', $tanggalSelesaiPgs)->format('d/m/Y');
            } catch (\Exception $e) {
                // Jika gagal convert, gunakan format asli
            }
        }

        $data = [
            'nik' => $validatedData['nik'],
            'nama' => $validatedData['nama'],
            'jabatan_definitif' => $validatedData['jabatan_definitif'],
            'jabatan_pgs' => $validatedData['jabatan_pgs'],
            'lokasi_unit_kerja' => $validatedData['lokasi_unit_kerja'],
            'tanggal_pgs' => $tanggalPgs,
            'tanggal_selesai_pgs' => $tanggalSelesaiPgs,
        ];

        $dataPgs->update($data);

        return redirect()->route('data-pgs.index')->with('success', 'Data PGS berhasil diperbarui.');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(DataPgs $dataPgs)
    {
        $dataPgs->delete();

        return redirect()->route('data-pgs.index')->with('success', 'Data PGS berhasil dihapus.');
    }

    /**
     * Download template Excel untuk import data PGS
     */
    public function downloadTemplate()
    {
        return Excel::download(new DataPgsTemplateExport, 'template-data-pgs.xlsx');
    }

    /**
     * Export data PGS ke Excel
     */
    public function export()
    {
        return Excel::download(new DataPgsExport, 'data-pgs-' . date('Y-m-d') . '.xlsx');
    }

    /**
     * Import data PGS dari Excel (mode tambah)
     */
    public function importAdd(Request $request)
    {
        $request->validate([
            'file' => 'required|mimes:xlsx,xls,csv|max:10240', // Max 10MB
        ]);

        try {
            $countBefore = DataPgs::count();
            Excel::import(new DataPgsImport, $request->file('file'));
            $countAfter = DataPgs::count();
            $imported = $countAfter - $countBefore;

            // Catat ke audit log
            if (\Auth::check()) {
                \App\Models\AuditLog::create([
                    'user_id' => \Auth::id(),
                    'user_name' => \Auth::user()->name,
                    'user_email' => \Auth::user()->email,
                    'action' => 'bulk_created',
                    'model_type' => 'App\\Models\\DataPgs',
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

            return redirect()->route('data-pgs.index')->with('success', 'Data PGS berhasil diimport. Total data ditambahkan: ' . $imported);
        } catch (\Exception $e) {
            return redirect()->route('data-pgs.index')->with('error', 'Error import data: ' . $e->getMessage());
        }
    }

    /**
     * Import data PGS dari Excel (mode ganti semua)
     */
    public function importReplace(Request $request)
    {
        $request->validate([
            'file' => 'required|mimes:xlsx,xls,csv|max:10240', // Max 10MB
        ]);

        try {
            // Hapus semua data yang ada
            $countBefore = DataPgs::count();
            DataPgs::truncate();

            // Import data baru
            Excel::import(new DataPgsImport, $request->file('file'));
            $countAfter = DataPgs::count();

            // Catat ke audit log
            if (\Auth::check()) {
                \App\Models\AuditLog::create([
                    'user_id' => \Auth::id(),
                    'user_name' => \Auth::user()->name,
                    'user_email' => \Auth::user()->email,
                    'action' => 'bulk_replaced',
                    'model_type' => 'App\\Models\\DataPgs',
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

            return redirect()->route('data-pgs.index')->with('success', 'Data PGS berhasil diganti dengan data baru dari file import.');
        } catch (\Exception $e) {
            return redirect()->route('data-pgs.index')->with('error', 'Error import data: ' . $e->getMessage());
        }
    }
}
