<?php

namespace App\Http\Controllers;

use App\Models\Snapshot;
use App\Exports\EmployeesExport;
use Illuminate\Http\Request;
use Maatwebsite\Excel\Facades\Excel;
use Illuminate\Support\Facades\Storage;

class SnapshotController extends Controller
{
    /**
     * Menampilkan daftar snapshot yang tersedia.
     */
    public function index()
    {
        $snapshots = Snapshot::latest()->paginate(15);
        return view('snapshots.index', compact('snapshots'));
    }

    /**
     * Membuat dan menyimpan snapshot baru.
     */
    public function store(Request $request)
    {
        $request->validate([
            'description' => 'nullable|string|max:255',
        ]);

        // 1. Buat nama file yang unik berdasarkan tanggal dan waktu
        $fileName = 'database_karyawan_' . now()->format('Y-m-d_H-i-s') . '.xlsx';
        $filePath = 'snapshots/' . $fileName;

        // 2. Simpan file Excel ke storage (storage/app/snapshots/)
        Excel::store(new EmployeesExport(), $filePath);

        // 3. Buat catatan di database
        Snapshot::create([
            'file_name' => $fileName,
            'file_path' => $filePath,
            'description' => $request->description ?? 'Snapshot data karyawan pada ' . now()->format('d F Y H:i'),
        ]);

        return redirect()->route('snapshots.index')->with('success', 'Snapshot berhasil dibuat!');
    }

    /**
     * Mengunduh file snapshot yang dipilih.
     */
    public function download(Snapshot $snapshot)
    {
        // Pastikan file ada di storage sebelum di-download
        if (!Storage::exists($snapshot->file_path)) {
            abort(404, 'File snapshot tidak ditemukan.');
        }

        return Storage::download($snapshot->file_path, $snapshot->file_name);
    }

    /**
     * Menghapus file snapshot.
     */
    public function destroy(Snapshot $snapshot)
    {
        // Hapus file dari storage
        if (Storage::exists($snapshot->file_path)) {
            Storage::delete($snapshot->file_path);
        }

        // Hapus record dari database
        $snapshot->delete();

        return redirect()->route('snapshots.index')->with('success', 'Snapshot berhasil dihapus.');
    }
}