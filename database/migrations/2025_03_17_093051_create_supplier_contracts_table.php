<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('supplier_contract', function (Blueprint $table) {
            $table->bigIncrements('contract_id');
            $table->foreignId('supplier_id')->constrained('supplier')->onDelete('cascade');
            $table->string('nomor_kontrak')->unique();
            $table->date('tanggal_kontrak');
            $table->date('tanggal_mulai_kontrak');
            $table->date('tanggal_berakhir_kontrak');
            $table->string('jenis_barang');
            $table->text('sanksi_penalti')->nullable();
            $table->text('ketentuan_pembayaran')->nullable();
            $table->text('hak_kewajiban')->nullable();
            $table->string('copy_kontrak_path')->nullable();
            $table->string('photo_kantor_path')->nullable();
            $table->string('photo_gudang_path')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('supplier_contract');
    }
};
