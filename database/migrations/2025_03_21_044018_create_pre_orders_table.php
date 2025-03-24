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
        Schema::create('pre_order', function (Blueprint $table) {
            $table->id();
            $table->foreignId('supplier_id')
                  ->nullable()
                  ->constrained('supplier')
                  ->nullOnDelete();
            $table->date('tanggal_po');
            $table->date('batas_pengiriman');
            $table->text('catatan')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('pre_order');
    }
};
