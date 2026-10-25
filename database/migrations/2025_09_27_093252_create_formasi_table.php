<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('formasi', function (Blueprint $table) {
            $table->id();
            $table->string('kode_jabatan');
            $table->string('lokasi');
            $table->string('unit');
            $table->string('jabatan');
            $table->string('kelompok_kelas_jabatan');
            $table->string('grade');
            $table->unsignedInteger('kuota')->default(1); // Tambah kolom kuota
            $table->timestamps();
            $table->unique(['kode_jabatan', 'lokasi', 'unit']); // Unik kombinasi jabatan-lokasi-unit
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('formasi');
    }
};
