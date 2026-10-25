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
        Schema::table('data_karyawan', function (Blueprint $table) {
            // Hapus kolom yang tidak ada di Excel
            $table->dropColumn([
                'tmt_jabatan',
                'job_grade',
                'status',
                'tmt',
                'grade'
            ]);

            // Tambah kolom baru yang ada di Excel
            $table->string('penugasan')->nullable()->after('fungsi_kontrak_os');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('data_karyawan', function (Blueprint $table) {
            // Kembalikan kolom yang dihapus
            $table->string('tmt_jabatan')->nullable()->after('jabatan');
            $table->string('job_grade')->nullable()->after('tmt_jabatan');
            $table->string('status')->nullable()->after('awal_lokasi_kerja');
            $table->string('tmt')->nullable()->after('masa_kerja');
            $table->string('grade')->nullable()->after('fungsi_pekerjaan');

            // Hapus kolom baru
            $table->dropColumn('penugasan');
        });
    }
};
