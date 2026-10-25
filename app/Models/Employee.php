<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Employee extends Model
{
    use HasFactory;

    /**
     * Mengizinkan semua atribut untuk diisi secara massal.
     */
    protected $guarded = [];
}