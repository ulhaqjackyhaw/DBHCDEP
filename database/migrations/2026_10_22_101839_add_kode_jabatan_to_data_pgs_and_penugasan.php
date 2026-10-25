<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        if (Schema::hasTable('data_pgs') && !Schema::hasColumn('data_pgs', 'kode_jabatan')) {
            Schema::table('data_pgs', function (Blueprint $table) {
                $table->string('kode_jabatan')->nullable()->after('jabatan_pgs');
            });
        }

        if (Schema::hasTable('data_penugasan') && !Schema::hasColumn('data_penugasan', 'kode_jabatan')) {
            Schema::table('data_penugasan', function (Blueprint $table) {
                $table->string('kode_jabatan')->nullable()->after('jabatan_definitif');
            });
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        if (Schema::hasTable('data_pgs') && Schema::hasColumn('data_pgs', 'kode_jabatan')) {
            Schema::table('data_pgs', function (Blueprint $table) {
                $table->dropColumn('kode_jabatan');
            });
        }

        if (Schema::hasTable('data_penugasan') && Schema::hasColumn('data_penugasan', 'kode_jabatan')) {
            Schema::table('data_penugasan', function (Blueprint $table) {
                $table->dropColumn('kode_jabatan');
            });
        }
    }
};
