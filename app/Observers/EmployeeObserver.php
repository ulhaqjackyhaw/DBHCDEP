<?php

namespace App\Observers;

use App\Models\Employee;
use App\Models\EmployeeHistory;

class EmployeeObserver
{
    public function created(Employee $employee): void
    {
        EmployeeHistory::create([
            'employee_id' => $employee->id,
            'NIK' => $employee->NIK,
            'Nama' => $employee->Nama,
            'action' => 'DIBUAT',
            'details' => ['data_baru' => $employee->toArray()]
        ]);
    }

    public function updated(Employee $employee): void
    {
        EmployeeHistory::create([
            'employee_id' => $employee->id,
            'NIK' => $employee->NIK,
            'Nama' => $employee->Nama,
            'action' => 'DIPERBARUI',
            'details' => [
                'data_lama' => $employee->getOriginal(),
                'data_baru' => $employee->getChanges()
            ]
        ]);
    }

    public function deleted(Employee $employee): void
    {
        EmployeeHistory::create([
            'employee_id' => $employee->id,
            'NIK' => $employee->NIK,
            'Nama' => $employee->Nama,
            'action' => 'DIHAPUS',
            'details' => ['data_terhapus' => $employee->toArray()]
        ]);
    }
}