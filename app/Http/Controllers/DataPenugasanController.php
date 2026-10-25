<?php

namespace App\Http\Controllers;

use App\Models\DataPenugasan;
use App\Imports\DataPenugasanImport;
use App\Exports\DataPenugasanExport;
use App\Exports\DataPenugasanTemplateExport;
use Illuminate\Http\Request;
use Maatwebsite\Excel\Facades\Excel;
use Carbon\Carbon;

class DataPenugasanController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $dataPenugasan = DataPenugasan::latest()->get();
        return view('data-penugasan.index', compact('dataPenugasan'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        return view('data-penugasan.create');
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $validatedData = $request->validate([
            'nik' => 'required|string|max:50',
            'nama' => 'required|string|max:255',
            'kj' => 'nullable|string|max:50',
            'jabatan_definitif' => 'nullable|string|max:255',
            'unit_definitif' => 'nullable|string|max:255',
            'lokasi_definitif' => 'nullable|string|max:255',
            'unit_penugasan' => 'nullable|string|max:255',
            'lokasi_penugasan' => 'nullable|string|max:255',
            'nomor_sprint' => 'nullable|string|max:255',
            'tanggal_mulai' => 'required|date',
            'tanggal_selesai' => 'nullable|date|after_or_equal:tanggal_mulai',
            'pic' => 'nullable|string|max:255',
            'keterangan' => 'nullable|string',
        ]);

        // Konversi format tanggal dari Y-m-d ke d/m/Y untuk konsistensi database
        $tanggalMulai = $validatedData['tanggal_mulai'];
        try {
            $tanggalMulai = Carbon::createFromFormat('Y-m-d', $tanggalMulai)->format('d/m/Y');
        } catch (\Exception $e) {
            // Jika gagal convert, gunakan format asli
        }

        $tanggalSelesai = $validatedData['tanggal_selesai'] ?? null;
        if ($tanggalSelesai) {
            try {
                $tanggalSelesai = Carbon::createFromFormat('Y-m-d', $tanggalSelesai)->format('d/m/Y');
            } catch (\Exception $e) {
                // Jika gagal convert, gunakan format asli
            }
        }

        $data = [
            'nik' => $validatedData['nik'],
            'nama' => $validatedData['nama'],
            'kj' => $validatedData['kj'],
            'jabatan_definitif' => $validatedData['jabatan_definitif'],
            'unit_definitif' => $validatedData['unit_definitif'],
            'lokasi_definitif' => $validatedData['lokasi_definitif'],
            'unit_penugasan' => $validatedData['unit_penugasan'],
            'lokasi_penugasan' => $validatedData['lokasi_penugasan'],
            'nomor_sprint' => $validatedData['nomor_sprint'],
            'tanggal_mulai' => $tanggalMulai,
            'tanggal_selesai' => $tanggalSelesai,
            'pic' => $validatedData['pic'],
            'keterangan' => $validatedData['keterangan'],
        ];

        DataPenugasan::create($data);

        return redirect()->route('data-penugasan.index')->with('success', 'Data penugasan berhasil ditambahkan.');
    }

    /**
     * Display the specified resource.
     */
    public function show(DataPenugasan $dataPenugasan)
    {
        return view('data-penugasan.show', compact('dataPenugasan'));
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(DataPenugasan $dataPenugasan)
    {
        return view('data-penugasan.edit', compact('dataPenugasan'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, DataPenugasan $dataPenugasan)
    {
        $validatedData = $request->validate([
            'nik' => 'required|string|max:50',
            'nama' => 'required|string|max:255',
            'kj' => 'nullable|string|max:50',
            'jabatan_definitif' => 'nullable|string|max:255',
            'unit_definitif' => 'nullable|string|max:255',
            'lokasi_definitif' => 'nullable|string|max:255',
            'unit_penugasan' => 'nullable|string|max:255',
            'lokasi_penugasan' => 'nullable|string|max:255',
            'nomor_sprint' => 'nullable|string|max:255',
            'tanggal_mulai' => 'required|date',
            'tanggal_selesai' => 'nullable|date|after_or_equal:tanggal_mulai',
            'pic' => 'nullable|string|max:255',
            'keterangan' => 'nullable|string',
        ]);

        // Konversi format tanggal dari Y-m-d ke d/m/Y untuk konsistensi database
        $tanggalMulai = $validatedData['tanggal_mulai'];
        try {
            $tanggalMulai = Carbon::createFromFormat('Y-m-d', $tanggalMulai)->format('d/m/Y');
        } catch (\Exception $e) {
            // Jika gagal convert, gunakan format asli
        }

        $tanggalSelesai = $validatedData['tanggal_selesai'] ?? null;
        if ($tanggalSelesai) {
            try {
                $tanggalSelesai = Carbon::createFromFormat('Y-m-d', $tanggalSelesai)->format('d/m/Y');
            } catch (\Exception $e) {
                // Jika gagal convert, gunakan format asli
            }
        }

        $data = [
            'nik' => $validatedData['nik'],
            'nama' => $validatedData['nama'],
            'kj' => $validatedData['kj'],
            'jabatan_definitif' => $validatedData['jabatan_definitif'],
            'unit_definitif' => $validatedData['unit_definitif'],
            'lokasi_definitif' => $validatedData['lokasi_definitif'],
            'unit_penugasan' => $validatedData['unit_penugasan'],
            'lokasi_penugasan' => $validatedData['lokasi_penugasan'],
            'nomor_sprint' => $validatedData['nomor_sprint'],
            'tanggal_mulai' => $tanggalMulai,
            'tanggal_selesai' => $tanggalSelesai,
            'pic' => $validatedData['pic'],
            'keterangan' => $validatedData['keterangan'],
        ];

        $dataPenugasan->update($data);

        return redirect()->route('data-penugasan.index')->with('success', 'Data penugasan berhasil diperbarui.');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(DataPenugasan $dataPenugasan)
    {
        $dataPenugasan->delete();

        return redirect()->route('data-penugasan.index')
            ->with('success', 'Data penugasan berhasil dihapus.');
    }

    /**
     * Import data from Excel (mode tambah)
     */
    public function importAdd(Request $request)
    {
        $request->validate([
            'file' => 'required|mimes:xlsx,xls,csv|max:10240',
        ]);

        try {
            Excel::import(new DataPenugasanImport, $request->file('file'));

            return redirect()->route('data-penugasan.index')
                ->with('success', 'Data berhasil ditambahkan.');
        } catch (\Exception $e) {
            return redirect()->route('data-penugasan.index')
                ->with('error', 'Gagal import data: ' . $e->getMessage());
        }
    }

    /**
     * Import data from Excel (mode ganti semua)
     */
    public function importReplace(Request $request)
    {
        $request->validate([
            'file' => 'required|mimes:xlsx,xls,csv|max:10240',
        ]);

        try {
            // Hapus semua data lama
            DataPenugasan::truncate();

            // Import data baru
            Excel::import(new DataPenugasanImport, $request->file('file'));

            return redirect()->route('data-penugasan.index')
                ->with('success', 'Semua data lama berhasil dihapus dan diganti dengan data baru.');
        } catch (\Exception $e) {
            return redirect()->route('data-penugasan.index')
                ->with('error', 'Gagal import data: ' . $e->getMessage());
        }
    }

    /**
     * Export data to Excel
     */
    public function export()
    {
        return Excel::download(new DataPenugasanExport, 'data-penugasan-' . date('Y-m-d-His') . '.xlsx');
    }

    /**
     * Download template Excel
     */
    public function downloadTemplate()
    {
        return Excel::download(new DataPenugasanTemplateExport, 'template-penugasan.xlsx');
    }
}
