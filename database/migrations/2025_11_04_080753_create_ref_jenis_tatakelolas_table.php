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
    Schema::create('ref_jenis_tatakelolas', function (Blueprint $table) {
        $table->id();
        // TAMBAHKAN KOLOM ANDA DI SINI
        $table->string('nama'); // <-- Contoh
        $table->text('deskripsi')->nullable(); // <-- Contoh
        $table->timestamps();
    });
}

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('ref_jenis_tatakelolas');
    }
};
