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
        Schema::create('kategori_sampahs', function (Blueprint $table) {
            $table->id();
            $table->string('kode', 10)->unique(); // P, L, K, B, M
            $table->string('nama', 100); // Plastik, Logam, Kertas, dll
            $table->string('warna', 20)->nullable(); // untuk UI (red, blue, green, dll)
            $table->boolean('aktif')->default(true);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('kategori_sampahs');
    }
};