<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('employees', function (Blueprint $table) {
            $table->id();
            // PERBAIKAN: Tambahkan ->unique() untuk memastikan NIK tidak ada yang sama
            $table->bigInteger('NIK')->unique();
            $table->string('Nama');
            $table->string('GENDER');
            $table->string('UNIT');
            $table->string('JABATAN')->nullable();
            $table->string('KELOMPOK_KELAS_JABATAN')->nullable();
            $table->integer('GRADE')->nullable();
            $table->string('STATUS_KEPEGAWAIAN');
            $table->integer('USIA');
            $table->string('PENDIDIKAN');
            $table->integer('MASA_KERJA');
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('employees');
    }
};