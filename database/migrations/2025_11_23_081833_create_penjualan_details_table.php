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
        Schema::create('penjualan_details', function (Blueprint $table) {
            $table->id();
            $table->foreignId('penjualan_id')->constrained('penjualans')->onDelete('cascade');
            $table->foreignId('jenis_sampah_id')->constrained('jenis_sampahs')->onDelete('cascade');
            $table->string('nama_produk')->nullable(); // Nama spesifik produk
            $table->decimal('berat_kg', 8, 2);
            $table->enum('satuan', ['kg', 'pcs'])->default('kg');
            $table->bigInteger('harga_per_kg'); // Harga saat transaksi (untuk history)
            $table->bigInteger('subtotal');
            $table->timestamps();
            
            // Index untuk performa
            $table->index('penjualan_id');
            $table->index('jenis_sampah_id');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('penjualan_details');
    }
};