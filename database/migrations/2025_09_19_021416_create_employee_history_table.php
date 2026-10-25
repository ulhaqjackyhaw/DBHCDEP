<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('employee_history', function (Blueprint $table) {
            $table->id();
            $table->foreignId('version_id')->constrained('versions')->onDelete('cascade');

            // Basic Info
            $table->string('nik');
            $table->string('nama');
            $table->string('kode_jabatan')->nullable();

            // Unit Structure
            $table->string('unit_deputy_egm')->nullable();
            $table->string('unit_assistant_deputy')->nullable();
            $table->string('unit_division_head')->nullable();
            $table->string('unit_department_head')->nullable();
            $table->string('unit_kerja')->nullable();

            // Job Information
            $table->string('jabatan')->nullable();
            $table->string('person_grade')->nullable();
            $table->string('lokasi_kerja')->nullable();
            $table->string('awal_lokasi_kerja')->nullable();
            $table->string('status_jabatan')->nullable();
            $table->string('sub_status')->nullable();
            $table->string('asal_instansi')->nullable();
            $table->string('instansi')->nullable();

            // Personal Information
            $table->string('jenis_kelamin')->nullable();
            $table->string('tanggal_lahir')->nullable();
            $table->integer('usia')->nullable();
            $table->string('rencana_mpp')->nullable();
            $table->string('rencana_pensiun')->nullable();
            $table->string('pendidikan_diakui')->nullable();
            $table->string('pendidikan_dimiliki')->nullable();
            $table->string('jurusan')->nullable();
            $table->string('tmt_karyawan')->nullable();
            $table->string('masa_kerja')->nullable();
            $table->string('tmt_kj_tertinggi')->nullable();
            $table->integer('masa_kj_tertinggi_tahun')->nullable();

            // Job Classification
            $table->string('sub_keluarga_jabatan')->nullable();
            $table->string('keluarga_jabatan')->nullable();
            $table->string('fungsi_jabatan')->nullable();
            $table->string('jalur_karir')->nullable();
            $table->string('jenjang_karir')->nullable();
            $table->string('kelompok_kelas_jabatan')->nullable();
            $table->string('fungsi_pekerjaan')->nullable();

            // License Information
            $table->text('lisence_dimiliki')->nullable();
            $table->string('rating')->nullable();
            $table->string('no_stkp')->nullable();
            $table->string('masa_berlaku')->nullable();
            $table->string('lisence_dibayarkan_januari')->nullable();

            // Additional Personal Data
            $table->string('agama')->nullable();
            $table->decimal('nilai_npi_2022', 5, 2)->nullable();
            $table->string('kategori')->nullable();
            $table->string('no_ktp')->nullable();
            $table->text('alamat_ktp')->nullable();
            $table->string('no_kontrak')->nullable();
            $table->string('email')->nullable();
            $table->string('no_hp')->nullable();
            $table->string('status_pernikahan')->nullable();
            $table->string('generasi')->nullable();

            // Job History & Performance
            $table->string('no_sk_jabatan_terakhir')->nullable();
            $table->string('tgl_sk_jabatan_terakhir')->nullable();
            $table->text('cek_lisence_serkom')->nullable();
            $table->decimal('kpi_2023', 5, 2)->nullable();
            $table->string('kriteria')->nullable();
            $table->string('fungsi_kontrak_os')->nullable();
            $table->string('penugasan')->nullable();

            $table->timestamps();
        });
    }
    public function down(): void
    {
        Schema::dropIfExists('employee_history');
    }
};