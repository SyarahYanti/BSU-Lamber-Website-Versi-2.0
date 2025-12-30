<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('jenis_sampahs', function (Blueprint $table) {
            $table->id();
            $table->foreignId('kategori_id')->constrained('kategori_sampahs')->onDelete('cascade');
            $table->string('kode', 20)->unique(); // P01B, L01, K01, dll
            $table->string('nama'); // PP Gelas Bening Bersih, Besi Tebal, dll
            $table->text('contoh_produk')->nullable(); // Aqua, Club, JS tanpa label
            $table->enum('satuan', ['kg', 'pcs'])->default('kg');
            $table->boolean('is_active')->default(true);
            $table->timestamps();
            
            // Index untuk performa
            $table->index('kategori_id');
            $table->index('is_active');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('jenis_sampahs');
    }
};