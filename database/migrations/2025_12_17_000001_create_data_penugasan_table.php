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
        Schema::create('data_penugasan', function (Blueprint $table) {
            $table->id();
            $table->string('nik');
            $table->string('nama');
            $table->string('kj')->nullable(); // Kelompok Jabatan
            $table->string('jabatan_definitif')->nullable();
            $table->string('unit_definitif')->nullable();
            $table->string('lokasi_definitif')->nullable();
            $table->string('unit_penugasan');
            $table->string('lokasi_penugasan');
            $table->string('nomor_sprint')->nullable();
            $table->string('tanggal_mulai'); // Format: d/m/Y
            $table->string('tanggal_selesai')->nullable(); // Format: d/m/Y
            $table->string('pic')->nullable();
            $table->string('keterangan')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('data_penugasan');
    }
};
