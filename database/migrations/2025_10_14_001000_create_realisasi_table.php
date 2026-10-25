<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {


    public function up(): void
    {
        Schema::create('realisasi', function (Blueprint $table) {
            $table->id();
            $table->string('program_kerja');
            $table->integer('tahun')->index();
            $table->decimal('rkap', 15, 2)->default(0);
            $table->decimal('realisasi_jan_mar', 15, 2)->default(0);
            $table->decimal('realisasi_jan_jun', 15, 2)->default(0);
            $table->decimal('realisasi_jul_sep', 15, 2)->default(0);
            $table->decimal('realisasi_jul_des', 15, 2)->default(0);
            $table->decimal('total', 15, 2)->default(0);
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('realisasi');
    }
};
