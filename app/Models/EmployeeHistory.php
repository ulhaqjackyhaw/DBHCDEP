<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class EmployeeHistory extends Model
{
    use HasFactory;

    // Definisikan nama tabel secara eksplisit
    protected $table = 'employee_history';

    // Izinkan semua atribut untuk diisi
    protected $guarded = [];
}