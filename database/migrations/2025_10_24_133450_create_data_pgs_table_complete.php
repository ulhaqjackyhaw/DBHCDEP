<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    /**
     * Run the migrations.
     * 
     * NIK tidak menggunakan unique constraint karena satu karyawan 
     * bisa memiliki beberapa penugasan PGS berbeda
     */
    public function up(): void
    {
        Schema::create('data_pgs', function (Blueprint $table) {
            $table->id();
            $table->string('nik')->comment('Nomor Induk Karyawan');
            $table->string('nama')->comment('Nama Karyawan');
            $table->string('jabatan_definitif')->comment('Jabatan Definitif/Asli');
            $table->string('jabatan_pgs')->comment('Jabatan PGS (Pelaksana Tugas Sementara)');
            $table->string('lokasi_unit_kerja')->comment('Lokasi atau Unit Kerja');
            $table->string('tanggal_pgs')->comment('Tanggal Mulai PGS (format: dd/mm/yyyy)');
            $table->string('tanggal_selesai_pgs')->nullable()->comment('Tanggal Target Selesai PGS (format: dd/mm/yyyy)');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('data_pgs');
    }
};
