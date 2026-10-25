<?php

namespace App\Http\Controllers;

use App\Models\Employee;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Maatwebsite\Excel\Facades\Excel;
use App\Imports\EmployeesImport;

class AnalitikController extends Controller
{
    /**
     * Method untuk menampilkan halaman dashboard utama (dengan chart)
     */
    public function dashboard()
    {
        $employeeStatus = DB::table('employees')
            ->select('STATUS_KEPEGAWAIAN', DB::raw('count(*) as total'))
            ->groupBy('STATUS_KEPEGAWAIAN')
            ->get();

        $labels = $employeeStatus->pluck('STATUS_KEPEGAWAIAN');
        $data = $employeeStatus->pluck('total');

        return view('dashboard', compact('labels', 'data'));
    }

    /**
     * Method untuk menampilkan halaman data karyawan (dengan tabel)
     */
    public function tabelKaryawan()
    {
        $employees = Employee::all();
        return view('data-karyawan', compact('employees'));
    }

    /**
     * Method untuk proses tambah data dari impor excel
     */
    public function importAdd(Request $request)
    {
        $request->validate(['file' => 'required|mimes:xlsx']);
        Excel::import(new EmployeesImport, $request->file('file'));
        return redirect()->route('karyawan.tabel')->with('success', 'Data berhasil ditambahkan.');
    }

    /**
     * Method untuk proses ganti semua data dari impor excel
     */
    public function importReplace(Request $request)
    {
        $request->validate(['file' => 'required|mimes:xlsx']);
        Employee::truncate();
        Excel::import(new EmployeesImport, $request->file('file'));
        return redirect()->route('karyawan.tabel')->with('success', 'Data berhasil diperbarui.');
    }
}