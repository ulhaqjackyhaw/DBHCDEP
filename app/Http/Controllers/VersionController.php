<?php

namespace App\Http\Controllers;

use App\Models\DataKaryawan;
use App\Models\EmployeeHistory;
use App\Models\Version;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Schema;

class VersionController extends Controller
{
    public function index(Request $request)
    {
        $request->validate([
            'year' => 'nullable|integer|digits:4',
            'month' => 'nullable|integer|between:1,12',
            'day' => 'nullable|date_format:Y-m-d',
        ]);

        $query = Version::query();

        if ($request->filled('year'))
            $query->whereYear('created_at', $request->year);
        if ($request->filled('month'))
            $query->whereMonth('created_at', $request->month);
        if ($request->filled('day'))
            $query->whereDate('created_at', $request->day);

        $versions = $query->withCount('history')->latest()->paginate(15)->appends($request->query());
        $years = Version::selectRaw('YEAR(created_at) as year')->distinct()->orderBy('year', 'desc')->pluck('year');

        return view('versions.index', [
            'versions' => $versions,
            'years' => $years,
            'filters' => $request->only(['year', 'month', 'day']),
        ]);
    }

    public function store(Request $request)
    {
        // PERBAIKAN 1: Validasi diubah menjadi 'nullable' agar deskripsi boleh kosong.
        $request->validate(['description' => 'nullable|string|max:255']);

        try {
            DB::transaction(function () use ($request) {
                // Memberikan deskripsi default jika input kosong, agar data tetap bermakna.
                $description = $request->input('description') ?? 'DATABASE KEPEGAWAIAN ' . now()->format('d-m-Y H:i:s');

                $version = Version::create(['description' => $description]);

                // PERBAIKAN 2 (KUNCI PERFORMA): Menggunakan chunkById untuk memproses data
                // per 500 baris agar tidak membebani memori server.
                DataKaryawan::query()->chunkById(500, function ($employees) use ($version) {
                    $historyData = $employees->map(function ($employee) use ($version) {
                        return [
                            'version_id' => $version->id,
                            // Basic Info
                            'nik' => $employee->nik,
                            'nama' => $employee->nama,
                            'kode_jabatan' => $employee->kode_jabatan,
                            // Unit Structure
                            'unit_deputy_egm' => $employee->unit_deputy_egm,
                            'unit_assistant_deputy' => $employee->unit_assistant_deputy,
                            'unit_division_head' => $employee->unit_division_head,
                            'unit_department_head' => $employee->unit_department_head,
                            'unit_kerja' => $employee->unit_kerja,
                            // Job Information
                            'jabatan' => $employee->jabatan,
                            'person_grade' => $employee->person_grade,
                            'lokasi_kerja' => $employee->lokasi_kerja,
                            'awal_lokasi_kerja' => $employee->awal_lokasi_kerja,
                            'status_jabatan' => $employee->status_jabatan,
                            'sub_status' => $employee->sub_status,
                            'asal_instansi' => $employee->asal_instansi,
                            'instansi' => $employee->instansi,
                            // Personal Information
                            'jenis_kelamin' => $employee->jenis_kelamin,
                            'tanggal_lahir' => $employee->tanggal_lahir,
                            'usia' => $employee->usia,
                            'rencana_mpp' => $employee->rencana_mpp,
                            'rencana_pensiun' => $employee->rencana_pensiun,
                            'pendidikan_diakui' => $employee->pendidikan_diakui,
                            'pendidikan_dimiliki' => $employee->pendidikan_dimiliki,
                            'jurusan' => $employee->jurusan,
                            'tmt_karyawan' => $employee->tmt_karyawan,
                            'masa_kerja' => $employee->masa_kerja,
                            'tmt_kj_tertinggi' => $employee->tmt_kj_tertinggi,
                            'masa_kj_tertinggi_tahun' => $employee->masa_kj_tertinggi_tahun,
                            // Job Classification
                            'sub_keluarga_jabatan' => $employee->sub_keluarga_jabatan,
                            'keluarga_jabatan' => $employee->keluarga_jabatan,
                            'fungsi_jabatan' => $employee->fungsi_jabatan,
                            'jalur_karir' => $employee->jalur_karir,
                            'jenjang_karir' => $employee->jenjang_karir,
                            'kelompok_kelas_jabatan' => $employee->kelompok_kelas_jabatan,
                            'fungsi_pekerjaan' => $employee->fungsi_pekerjaan,
                            // License Information
                            'lisence_dimiliki' => $employee->lisence_dimiliki,
                            'rating' => $employee->rating,
                            'no_stkp' => $employee->no_stkp,
                            'masa_berlaku' => $employee->masa_berlaku,
                            'lisence_dibayarkan_januari' => $employee->lisence_dibayarkan_januari,
                            // Additional Personal Data
                            'agama' => $employee->agama,
                            'nilai_npi_2022' => $employee->nilai_npi_2022,
                            'kategori' => $employee->kategori,
                            'no_ktp' => $employee->no_ktp,
                            'alamat_ktp' => $employee->alamat_ktp,
                            'no_kontrak' => $employee->no_kontrak,
                            'email' => $employee->email,
                            'no_hp' => $employee->no_hp,
                            'status_pernikahan' => $employee->status_pernikahan,
                            'generasi' => $employee->generasi,
                            // Job History & Performance
                            'no_sk_jabatan_terakhir' => $employee->no_sk_jabatan_terakhir,
                            'tgl_sk_jabatan_terakhir' => $employee->tgl_sk_jabatan_terakhir,
                            'cek_lisence_serkom' => $employee->cek_lisence_serkom,
                            'kpi_2023' => $employee->kpi_2023,
                            'kriteria' => $employee->kriteria,
                            'fungsi_kontrak_os' => $employee->fungsi_kontrak_os,
                            'penugasan' => $employee->penugasan,
                            'created_at' => now(),
                            'updated_at' => now()
                        ];
                    })->toArray();

                    if (!empty($historyData)) {
                        EmployeeHistory::insert($historyData);
                    }
                });
            });
        } catch (\Exception $e) {
            Log::error('Gagal membuat versi: ' . $e->getMessage());
            return redirect()->route('karyawan.index')->with('error', 'Terjadi kesalahan saat membuat versi data.');
        }

        return redirect()->route('karyawan.index')->with('success', 'Versi data berhasil disimpan!');
    }

    public function restore(Version $version)
    {
        if (!$version->history()->exists()) {
            return redirect()->route('versions.index')->with('error', 'Versi ini tidak memiliki data untuk dipulihkan.');
        }

        DB::beginTransaction();
        try {
            Schema::disableForeignKeyConstraints();
            DataKaryawan::query()->delete();

            EmployeeHistory::where('version_id', $version->id)
                ->chunkById(500, function ($histories) {
                    $restoredData = $histories->map(function ($history) {
                        return [
                            // Basic Info
                            'nik' => $history->nik,
                            'nama' => $history->nama,
                            'kode_jabatan' => $history->kode_jabatan,
                            // Unit Structure
                            'unit_deputy_egm' => $history->unit_deputy_egm,
                            'unit_assistant_deputy' => $history->unit_assistant_deputy,
                            'unit_division_head' => $history->unit_division_head,
                            'unit_department_head' => $history->unit_department_head,
                            'unit_kerja' => $history->unit_kerja,
                            // Job Information
                            'jabatan' => $history->jabatan,
                            'person_grade' => $history->person_grade,
                            'lokasi_kerja' => $history->lokasi_kerja,
                            'awal_lokasi_kerja' => $history->awal_lokasi_kerja,
                            'status_jabatan' => $history->status_jabatan,
                            'sub_status' => $history->sub_status,
                            'asal_instansi' => $history->asal_instansi,
                            'instansi' => $history->instansi,
                            // Personal Information
                            'jenis_kelamin' => $history->jenis_kelamin,
                            'tanggal_lahir' => $history->tanggal_lahir,
                            'usia' => $history->usia,
                            'rencana_mpp' => $history->rencana_mpp,
                            'rencana_pensiun' => $history->rencana_pensiun,
                            'pendidikan_diakui' => $history->pendidikan_diakui,
                            'pendidikan_dimiliki' => $history->pendidikan_dimiliki,
                            'jurusan' => $history->jurusan,
                            'tmt_karyawan' => $history->tmt_karyawan,
                            'masa_kerja' => $history->masa_kerja,
                            'tmt_kj_tertinggi' => $history->tmt_kj_tertinggi,
                            'masa_kj_tertinggi_tahun' => $history->masa_kj_tertinggi_tahun,
                            // Job Classification
                            'sub_keluarga_jabatan' => $history->sub_keluarga_jabatan,
                            'keluarga_jabatan' => $history->keluarga_jabatan,
                            'fungsi_jabatan' => $history->fungsi_jabatan,
                            'jalur_karir' => $history->jalur_karir,
                            'jenjang_karir' => $history->jenjang_karir,
                            'kelompok_kelas_jabatan' => $history->kelompok_kelas_jabatan,
                            'fungsi_pekerjaan' => $history->fungsi_pekerjaan,
                            // License Information
                            'lisence_dimiliki' => $history->lisence_dimiliki,
                            'rating' => $history->rating,
                            'no_stkp' => $history->no_stkp,
                            'masa_berlaku' => $history->masa_berlaku,
                            'lisence_dibayarkan_januari' => $history->lisence_dibayarkan_januari,
                            // Additional Personal Data
                            'agama' => $history->agama,
                            'nilai_npi_2022' => $history->nilai_npi_2022,
                            'kategori' => $history->kategori,
                            'no_ktp' => $history->no_ktp,
                            'alamat_ktp' => $history->alamat_ktp,
                            'no_kontrak' => $history->no_kontrak,
                            'email' => $history->email,
                            'no_hp' => $history->no_hp,
                            'status_pernikahan' => $history->status_pernikahan,
                            'generasi' => $history->generasi,
                            // Job History & Performance
                            'no_sk_jabatan_terakhir' => $history->no_sk_jabatan_terakhir,
                            'tgl_sk_jabatan_terakhir' => $history->tgl_sk_jabatan_terakhir,
                            'cek_lisence_serkom' => $history->cek_lisence_serkom,
                            'kpi_2023' => $history->kpi_2023,
                            'kriteria' => $history->kriteria,
                            'fungsi_kontrak_os' => $history->fungsi_kontrak_os,
                            'penugasan' => $history->penugasan,
                            'created_at' => now(),
                            'updated_at' => now()
                        ];
                    })->toArray();

                    if (!empty($restoredData)) {
                        DB::table('data_karyawan')->insert($restoredData);
                    }
                });

            Schema::enableForeignKeyConstraints();
            DB::commit();

            // Audit log untuk restore version
            \App\Models\AuditLog::create([
                'user_id' => auth()->id(),
                'model_type' => Version::class,
                'model_id' => $version->id,
                'action' => 'version_restored',
                'model_identifier' => $version->description,
                'new_values' => [
                    'description' => $version->description,
                    'snapshot_date' => $version->created_at->format('d/m/Y H:i:s'),
                    'records_count' => $version->history_count ?? $version->history()->count(),
                ],
                'ip_address' => request()->ip(),
                'user_agent' => request()->userAgent(),
            ]);
        } catch (\Exception $e) {
            DB::rollBack();
            Schema::enableForeignKeyConstraints();
            Log::error('Gagal memulihkan versi: ' . $e->getMessage());
            return redirect()->route('versions.index')->with('error', 'Gagal memulihkan data. Error: ' . $e->getMessage());
        }

        return redirect()->route('versions.index')->with('success', "Data berhasil dipulihkan dari versi: '{$version->description}'.");
    }

    public function download(Version $version)
    {
        // Check if version has data
        if (!$version->history()->exists()) {
            return redirect()->route('versions.index')->with('error', 'Versi ini tidak memiliki data untuk diunduh.');
        }

        // Create filename with version info
        $filename = 'Data_Karyawan_' . str_replace(['/', ' ', ':', '-'], '_', $version->description) . '_' . $version->created_at->format('Y_m_d') . '.xlsx';

        return \Maatwebsite\Excel\Facades\Excel::download(new \App\Exports\VersionDataExport($version->id), $filename);
    }

    public function destroy(Version $version)
    {
        $version->delete();
        return redirect()->route('versions.index')->with('success', 'Versi histori berhasil dihapus.');
    }
}