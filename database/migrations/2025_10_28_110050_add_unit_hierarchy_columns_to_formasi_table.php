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
        Schema::table('formasi', function (Blueprint $table) {
            $table->string('unit_deputy_egm')->nullable()->after('kode_jabatan');
            $table->string('unit_assistant_deputy')->nullable()->after('unit_deputy_egm');
            $table->string('unit_division_head')->nullable()->after('unit_assistant_deputy');
            $table->string('unit_department_head')->nullable()->after('unit_division_head');

            // Rename kolom yang sudah ada
            $table->renameColumn('lokasi', 'lokasi_kerja');
            $table->renameColumn('unit', 'unit_kerja');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('formasi', function (Blueprint $table) {
            $table->dropColumn([
                'unit_deputy_egm',
                'unit_assistant_deputy',
                'unit_division_head',
                'unit_department_head'
            ]);

            $table->renameColumn('lokasi_kerja', 'lokasi');
            $table->renameColumn('unit_kerja', 'unit');
        });
    }
};
