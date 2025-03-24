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
        Schema::create('barang_po', function (Blueprint $table) {
            $table->id();
            $table->foreignId('po_id')->constrained('pre_order')->onDelete('cascade');
            $table->string('nama_barang'); // Simpan nama barang langsung tanpa relasi
            $table->decimal('harga_satuan', 15, 2);
            $table->integer('jumlah');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('barang_po');
    }
};
