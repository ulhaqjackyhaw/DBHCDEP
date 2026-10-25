<?php

namespace App\Http\Controllers;

use App\Models\DataKaryawan;
use Illuminate\Http\Request;
use Maatwebsite\Excel\Facades\Excel;
use App\Imports\DataKaryawanImport;
use App\Exports\DataKaryawanExport;
use App\Exports\DataKaryawanTemplateExport;
use Illuminate\Validation\Rule;

class DataKaryawanController extends Controller
{
    public function index()
    {
        $employees = DataKaryawan::latest()->get();
        return view('karyawan.index', compact('employees'));
    }

    public function create()
    {
        $formasiList = \App\Models\Formasi::all();
        return view('karyawan.create', compact('formasiList'));
    }

    public function show(DataKaryawan $dataKaryawan)
    {
        $employee = $dataKaryawan; // untuk kompatibilitas dengan view yang sudah ada
        return view('karyawan.show', compact('employee'));
    }

    public function store(Request $request)
    {
        $validatedData = $request->validate([
            // Required fields
            'nik' => 'required|string|max:255|unique:data_karyawan,nik',
            'nama' => 'required|string|max:255',

            // Optional basic fields
            'jenis_kelamin' => 'nullable|string|in:Laki-laki,Perempuan',
            'formasi_select' => 'nullable|exists:formasi,id',
            'asal_instansi' => 'nullable|string',
            'tanggal_lahir' => 'nullable|date',
            'pendidikan_diakui' => 'nullable|string',

            // Unit & Struktur
            'unit_deputy_egm' => 'nullable|string',
            'unit_assistant_deputy' => 'nullable|string',
            'unit_division_head' => 'nullable|string',
            'unit_department_head' => 'nullable|string',

            // Info Jabatan
            'person_grade' => 'nullable|string',
            'kelompok_kelas_jabatan' => 'nullable|string',
            'awal_lokasi_kerja' => 'nullable|string',
            'status_jabatan' => 'nullable|string|in:KARYAWAN,PEJABAT',
            'sub_status' => 'nullable|string|in:KP,ALIH DAYA',
            'instansi' => 'nullable|string',
            'fungsi_kontrak_os' => 'nullable|string',
            'penugasan' => 'nullable|string',

            // Data Personal
            'agama' => 'nullable|string',
            'status_pernikahan' => 'nullable|string',
            'jurusan' => 'nullable|string',
            'pendidikan_dimiliki' => 'nullable|string',
            'no_ktp' => 'nullable|string',
            'alamat_ktp' => 'nullable|string',
            'no_kontrak' => 'nullable|string',
            'generasi' => 'nullable|string',
            'rencana_mpp' => 'nullable|string',
            'rencana_pensiun' => 'nullable|string',
            'tmt_karyawan' => 'nullable|date',
            'masa_kerja' => 'nullable|string',
            'kategori' => 'nullable|string',
            'nilai_npi_2022' => 'nullable|numeric',
            'usia' => 'nullable|integer',

            // Karir & Klasifikasi
            'keluarga_jabatan' => 'nullable|string',
            'sub_keluarga_jabatan' => 'nullable|string',
            'fungsi_jabatan' => 'nullable|string',
            'fungsi_pekerjaan' => 'nullable|string',
            'jalur_karir' => 'nullable|string',
            'jenjang_karir' => 'nullable|string',
            'tmt_kj_tertinggi' => 'nullable|date',
            'masa_kj_tertinggi_tahun' => 'nullable|integer',
            'no_sk_jabatan_terakhir' => 'nullable|string',
            'tgl_sk_jabatan_terakhir' => 'nullable|date',
            'kpi_2023' => 'nullable|numeric',
            'kriteria' => 'nullable|string',

            // Lisensi
            'lisence_dimiliki' => 'nullable|string',
            'rating' => 'nullable|string',
            'no_stkp' => 'nullable|string',
            'masa_berlaku' => 'nullable|date',
            'lisence_dibayarkan_januari' => 'nullable|string',
            'cek_lisence_serkom' => 'nullable|string',

            // Kontak
            'email' => 'nullable|email',
            'no_hp' => 'nullable|string',
        ]);

        // Data dasar
        $data = [
            'nik' => $validatedData['nik'],
            'nama' => $validatedData['nama'],
        ];

        // Jika ada formasi dipilih, tambahkan data formasi
        if ($request->filled('formasi_select')) {
            $formasi = \App\Models\Formasi::findOrFail($request->formasi_select);
            $data['kode_jabatan'] = $formasi->kode_jabatan;
            $data['lokasi_kerja'] = $formasi->lokasi_kerja;
            $data['unit_kerja'] = $formasi->unit_kerja;
            $data['jabatan'] = $formasi->jabatan;
            $data['kelompok_kelas_jabatan'] = $formasi->kelompok_kelas_jabatan;
            $data['grade'] = $formasi->grade;
        }

        // Konversi semua field tanggal dari Y-m-d ke d/m/Y
        $dateFields = [
            'tanggal_lahir',
            'tmt_karyawan',
            'tmt_kj_tertinggi',
            'tgl_sk_jabatan_terakhir',
            'masa_berlaku',
            'rencana_mpp',
            'rencana_pensiun',
        ];

        foreach ($dateFields as $field) {
            if ($request->filled($field)) {
                try {
                    $data[$field] = \Carbon\Carbon::createFromFormat('Y-m-d', $validatedData[$field])->format('d/m/Y');
                } catch (\Exception $e) {
                    $data[$field] = $validatedData[$field];
                }
            }
        }

        // Tambahkan field lainnya yang tidak kosong
        $otherFields = [
            'jenis_kelamin',
            'asal_instansi',
            'pendidikan_diakui',
            'awal_lokasi_kerja',
            'unit_deputy_egm',
            'unit_assistant_deputy',
            'unit_division_head',
            'unit_department_head',
            'person_grade',
            'kelompok_kelas_jabatan',
            'status_jabatan',
            'sub_status',
            'instansi',
            'fungsi_kontrak_os',
            'agama',
            'status_pernikahan',
            'jurusan',
            'pendidikan_dimiliki',
            'no_ktp',
            'alamat_ktp',
            'no_kontrak',
            'generasi',
            'rencana_mpp',
            'rencana_pensiun',
            'masa_kerja',
            'kategori',
            'nilai_npi_2022',
            'usia',
            'keluarga_jabatan',
            'sub_keluarga_jabatan',
            'fungsi_jabatan',
            'fungsi_pekerjaan',
            'jalur_karir',
            'jenjang_karir',
            'masa_kj_tertinggi_tahun',
            'no_sk_jabatan_terakhir',
            'kpi_2023',
            'kriteria',
            'lisence_dimiliki',
            'rating',
            'no_stkp',
            'lisence_dibayarkan_januari',
            'cek_lisence_serkom',
            'email',
            'no_hp',
            'penugasan'
        ];

        foreach ($otherFields as $field) {
            if ($request->filled($field)) {
                $data[$field] = $validatedData[$field];
            }
        }

        DataKaryawan::create($data);
        return redirect()->route('karyawan.index')->with('success', 'Data karyawan berhasil ditambahkan.');
    }

    public function edit(DataKaryawan $dataKaryawan)
    {
        $karyawan = $dataKaryawan;
        $formasiList = \App\Models\Formasi::all();
        return view('karyawan.edit', compact('karyawan', 'formasiList'));
    }

    // PERBAIKAN: Menyamakan nama variabel menjadi '$dataKaryawan'
    public function update(Request $request, DataKaryawan $dataKaryawan)
    {
        $validatedData = $request->validate([
            // Required fields
            'nik' => ['required', 'string', 'max:255', Rule::unique('data_karyawan', 'nik')->ignore($dataKaryawan->id)],
            'nama' => 'required|string|max:255',

            // Optional basic fields
            'jenis_kelamin' => 'nullable|string|in:Laki-laki,Perempuan',
            'formasi_select' => 'nullable|exists:formasi,id',
            'asal_instansi' => 'nullable|string',
            'tanggal_lahir' => 'nullable|date',
            'pendidikan_diakui' => 'nullable|string',
            'tmt_karyawan' => 'nullable|date',

            // Unit & Struktur
            'unit_deputy_egm' => 'nullable|string',
            'unit_assistant_deputy' => 'nullable|string',
            'unit_division_head' => 'nullable|string',
            'unit_department_head' => 'nullable|string',

            // Info Jabatan
            'person_grade' => 'nullable|string',
            'kelompok_kelas_jabatan' => 'nullable|string',
            'awal_lokasi_kerja' => 'nullable|string',
            'status_jabatan' => 'nullable|string|in:KARYAWAN,PEJABAT',
            'sub_status' => 'nullable|string|in:KP,ALIH DAYA',
            'instansi' => 'nullable|string',
            'fungsi_kontrak_os' => 'nullable|string',
            'penugasan' => 'nullable|string',

            // Data Personal
            'agama' => 'nullable|string',
            'status_pernikahan' => 'nullable|string',
            'jurusan' => 'nullable|string',
            'pendidikan_dimiliki' => 'nullable|string',
            'no_ktp' => 'nullable|string',
            'alamat_ktp' => 'nullable|string',
            'no_kontrak' => 'nullable|string',
            'generasi' => 'nullable|string',
            'rencana_mpp' => 'nullable|string',
            'rencana_pensiun' => 'nullable|string',
            'masa_kerja' => 'nullable|string',
            'kategori' => 'nullable|string',
            'nilai_npi_2022' => 'nullable|numeric',
            'usia' => 'nullable|integer',

            // Karir & Klasifikasi
            'keluarga_jabatan' => 'nullable|string',
            'sub_keluarga_jabatan' => 'nullable|string',
            'fungsi_jabatan' => 'nullable|string',
            'fungsi_pekerjaan' => 'nullable|string',
            'jalur_karir' => 'nullable|string',
            'jenjang_karir' => 'nullable|string',
            'tmt_kj_tertinggi' => 'nullable|date',
            'masa_kj_tertinggi_tahun' => 'nullable|integer',
            'no_sk_jabatan_terakhir' => 'nullable|string',
            'tgl_sk_jabatan_terakhir' => 'nullable|date',
            'kpi_2023' => 'nullable|numeric',
            'kriteria' => 'nullable|string',

            // Lisensi
            'lisence_dimiliki' => 'nullable|string',
            'rating' => 'nullable|string',
            'no_stkp' => 'nullable|string',
            'masa_berlaku' => 'nullable|date',
            'lisence_dibayarkan_januari' => 'nullable|string',
            'cek_lisence_serkom' => 'nullable|string',

            // Kontak
            'email' => 'nullable|email',
            'no_hp' => 'nullable|string',
        ]);

        // Data dasar
        $data = [
            'nik' => $validatedData['nik'],
            'nama' => $validatedData['nama'],
        ];

        // Jika ada formasi dipilih, update data formasi
        if ($request->filled('formasi_select')) {
            $formasi = \App\Models\Formasi::findOrFail($request->formasi_select);
            $data['kode_jabatan'] = $formasi->kode_jabatan;
            $data['lokasi_kerja'] = $formasi->lokasi_kerja;
            $data['unit_kerja'] = $formasi->unit_kerja;
            $data['jabatan'] = $formasi->jabatan;
            $data['kelompok_kelas_jabatan'] = $formasi->kelompok_kelas_jabatan;
            $data['grade'] = $formasi->grade;
        }

        // Konversi semua field tanggal dari Y-m-d ke d/m/Y
        $dateFields = [
            'tanggal_lahir',
            'tmt_karyawan',
            'tmt_kj_tertinggi',
            'tgl_sk_jabatan_terakhir',
            'masa_berlaku',
            'rencana_mpp',
            'rencana_pensiun'
        ];

        foreach ($dateFields as $field) {
            if ($request->filled($field)) {
                try {
                    $data[$field] = \Carbon\Carbon::createFromFormat('Y-m-d', $validatedData[$field])->format('d/m/Y');
                } catch (\Exception $e) {
                    $data[$field] = $validatedData[$field];
                }
            }
        }

        // Tambahkan field lainnya yang tidak kosong
        $otherFields = [
            'jenis_kelamin',
            'asal_instansi',
            'pendidikan_diakui',
            'awal_lokasi_kerja',
            'unit_deputy_egm',
            'unit_assistant_deputy',
            'unit_division_head',
            'unit_department_head',
            'person_grade',
            'kelompok_kelas_jabatan',
            'status_jabatan',
            'sub_status',
            'instansi',
            'fungsi_kontrak_os',
            'agama',
            'status_pernikahan',
            'jurusan',
            'pendidikan_dimiliki',
            'no_ktp',
            'alamat_ktp',
            'no_kontrak',
            'generasi',
            'rencana_mpp',
            'rencana_pensiun',
            'masa_kerja',
            'kategori',
            'nilai_npi_2022',
            'usia',
            'keluarga_jabatan',
            'sub_keluarga_jabatan',
            'fungsi_jabatan',
            'fungsi_pekerjaan',
            'jalur_karir',
            'jenjang_karir',
            'masa_kj_tertinggi_tahun',
            'no_sk_jabatan_terakhir',
            'kpi_2023',
            'kriteria',
            'lisence_dimiliki',
            'rating',
            'no_stkp',
            'lisence_dibayarkan_januari',
            'cek_lisence_serkom',
            'email',
            'no_hp',
            'penugasan'
        ];

        foreach ($otherFields as $field) {
            if ($request->filled($field)) {
                $data[$field] = $validatedData[$field];
            }
        }

        $dataKaryawan->update($data);
        return redirect()->route('karyawan.index')->with('success', 'Data karyawan berhasil diperbarui.');
    }

    // PERBAIKAN: Menyamakan nama variabel menjadi '$dataKaryawan'
    public function destroy(DataKaryawan $dataKaryawan)
    {
        $dataKaryawan->delete();
        return redirect()->route('karyawan.index')->with('success', 'Data karyawan berhasil dihapus.');
    }

    public function importAdd(Request $request)
    {
        $request->validate(['file' => 'required|mimes:xlsx,csv']);

        try {
            // Alokasikan memori dan waktu eksekusi langsung untuk menangani puluhan ribu baris
            @ini_set('memory_limit', '2048M');
            @ini_set('max_execution_time', 900);
            @set_time_limit(900);

            $countBefore = DataKaryawan::count();
            $import = new DataKaryawanImport();
            Excel::import($import, $request->file('file'));
            $countAfter = DataKaryawan::count();
            $imported = $countAfter - $countBefore;
            $skipped = $import->getSkippedCount();

            // Catat ke audit log
            if (\Auth::check()) {
                \App\Models\AuditLog::create([
                    'user_id' => \Auth::id(),
                    'user_name' => \Auth::user()->name,
                    'user_email' => \Auth::user()->email,
                    'action' => 'bulk_created',
                    'model_type' => 'App\\Models\\DataKaryawan',
                    'model_id' => null,
                    'model_identifier' => 'Bulk Import - ' . $request->file('file')->getClientOriginalName(),
                    'old_values' => null,
                    'new_values' => [
                        'imported' => $imported,
                        'skipped' => $skipped,
                        'total_after' => $countAfter,
                        'mode' => 'add',
                        'filename' => $request->file('file')->getClientOriginalName()
                    ],
                    'changes' => null,
                    'ip_address' => request()->ip(),
                    'user_agent' => request()->userAgent(),
                ]);
            }

            $message = "Import berhasil! {$imported} data berhasil ditambahkan/diperbarui.";
            if ($skipped > 0) {
                $message .= " ({$skipped} baris dilewati karena NIK kosong).";
            }

            return redirect()->route('karyawan.index')->with('success', $message);
        } catch (\Exception $e) {
            \Log::error('Import Add Error: ' . $e->getMessage());
            return redirect()->back()->with('error', 'Terjadi kesalahan saat import: ' . $e->getMessage());
        }
    }

    public function importReplace(Request $request)
    {
        $request->validate(['file' => 'required|mimes:xlsx,csv']);

        try {
            // Alokasikan memori dan waktu eksekusi langsung untuk menangani puluhan ribu baris
            @ini_set('memory_limit', '2048M');
            @ini_set('max_execution_time', 900);
            @set_time_limit(900);

            $countBefore = DataKaryawan::count();
            DataKaryawan::query()->delete();

            $import = new DataKaryawanImport();
            Excel::import($import, $request->file('file'));
            $countAfter = DataKaryawan::count();
            $skipped = $import->getSkippedCount();

            // Catat ke audit log
            if (\Auth::check()) {
                \App\Models\AuditLog::create([
                    'user_id' => \Auth::id(),
                    'user_name' => \Auth::user()->name,
                    'user_email' => \Auth::user()->email,
                    'action' => 'bulk_replaced',
                    'model_type' => 'App\\Models\\DataKaryawan',
                    'model_id' => null,
                    'model_identifier' => 'Bulk Replace - ' . $request->file('file')->getClientOriginalName(),
                    'old_values' => ['total_records' => $countBefore],
                    'new_values' => [
                        'total_after' => $countAfter,
                        'skipped' => $skipped,
                        'mode' => 'replace',
                        'filename' => $request->file('file')->getClientOriginalName()
                    ],
                    'changes' => null,
                    'ip_address' => request()->ip(),
                    'user_agent' => request()->userAgent(),
                ]);
            }

            $message = "Semua data lama berhasil diganti! Total {$countAfter} data karyawan tersimpan.";
            if ($skipped > 0) {
                $message .= " ({$skipped} baris dilewati karena NIK kosong).";
            }

            return redirect()->route('karyawan.index')->with('success', $message);
        } catch (\Exception $e) {
            \Log::error('Import Replace Error: ' . $e->getMessage());
            return redirect()->back()->with('error', 'Terjadi kesalahan saat import: ' . $e->getMessage());
        }
    }

    public function export()
    {
        return Excel::download(new DataKaryawanExport, 'data_karyawan_' . date('Y-m-d') . '.xlsx');
    }

    public function downloadTemplate()
    {
        return Excel::download(new DataKaryawanTemplateExport, 'template_data_karyawan.xlsx');
    }

    public function checkImportProgress($sessionId)
    {
        try {
            $progress = \Cache::get("import_progress_{$sessionId}");

            if (!$progress) {
                // Check if session just started (less than 5 seconds ago)
                if (strtotime('now') - hexdec(substr($sessionId, 7, 8)) < 5) {
                    return response()->json([
                        'progress' => 0,
                        'status' => 'initializing',
                        'message' => 'Menginisialisasi...'
                    ]);
                }

                return response()->json([
                    'progress' => 0,
                    'status' => 'not_found',
                    'message' => 'Session tidak ditemukan atau sudah expired'
                ]);
            }

            return response()->json($progress);
        } catch (\Exception $e) {
            \Log::error('Check Progress Error: ' . $e->getMessage());
            return response()->json([
                'progress' => 0,
                'status' => 'error',
                'message' => 'Error: ' . $e->getMessage()
            ]);
        }
    }

    /**
     * Memastikan worker berjalan di background jika server belum setup supervisor daemon
     */
    private function triggerQueueWorkerIfIdle(): void
    {
        if (config('queue.default') === 'sync') {
            return;
        }

        try {
            $artisan = base_path('artisan');
            $php = defined('PHP_BINARY') && PHP_BINARY ? PHP_BINARY : 'php';

            if (strtoupper(substr(PHP_OS, 0, 3)) === 'WIN') {
                pclose(popen("start /B \"\" \"{$php}\" \"{$artisan}\" queue:work --stop-when-empty --timeout=3600 --memory=2048 > NUL 2>&1", "r"));
            } else {
                exec("{$php} \"{$artisan}\" queue:work --stop-when-empty --timeout=3600 --memory=2048 > /dev/null 2>&1 &");
            }
        } catch (\Throwable $e) {
            \Log::warning("Could not auto-trigger background queue worker: " . $e->getMessage());
        }
    }
}

