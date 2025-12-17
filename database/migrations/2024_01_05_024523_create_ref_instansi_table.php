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
        Schema::create('ref_instansi', function (Blueprint $table) {
            $table->id();
            $table->string('nama_instansi');
            
            // --- BAGIAN PERBAIKAN ---
            // Menambahkan kolom yang dibutuhkan oleh Seeder
            $table->text('alamat')->nullable();
            $table->string('telepon')->nullable();
            $table->string('email')->nullable();
            // ------------------------

            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('ref_instansi');
    }
};