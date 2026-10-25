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
        Schema::create('audit_logs', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('user_id')->nullable(); // User yang melakukan action
            $table->string('user_name')->nullable(); // Nama user untuk backup jika user dihapus
            $table->string('user_email')->nullable(); // Email user
            $table->string('action'); // created, updated, deleted
            $table->string('model_type'); // Nama model (DataKaryawan, Formasi, dll)
            $table->unsignedBigInteger('model_id')->nullable(); // ID record yang diubah
            $table->text('model_identifier')->nullable(); // Identifier yang human-readable (nama, kode, dll)
            $table->json('old_values')->nullable(); // Data sebelum diubah (untuk update & delete)
            $table->json('new_values')->nullable(); // Data setelah diubah (untuk create & update)
            $table->json('changes')->nullable(); // Summary perubahan spesifik
            $table->string('ip_address')->nullable(); // IP address user
            $table->text('user_agent')->nullable(); // Browser/device info
            $table->timestamps();

            // Indexes untuk query performance
            $table->index('user_id');
            $table->index('model_type');
            $table->index('model_id');
            $table->index('action');
            $table->index('created_at');

            // Foreign key ke users table
            $table->foreign('user_id')->references('id')->on('users')->onDelete('set null');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('audit_logs');
    }
};
