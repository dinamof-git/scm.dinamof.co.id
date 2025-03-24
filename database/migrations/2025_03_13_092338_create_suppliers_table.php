<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('supplier', function (Blueprint $table) {
            $table->id();
            $table->string('nama_supplier');
            $table->string('nama_kontak');
            $table->string('nomor_telepon');
            $table->string('email');
            $table->string('nomor_izin_usaha')->nullable();
            $table->string('kode_provinsi');
            $table->string('kode_kabupaten');
            $table->string('kode_kecamatan');
            $table->string('kode_desa');
            $table->text('alamat');
            $table->string('lokasi_supplier')->nullable();
            $table->text('alamat_gudang');
            $table->string('lokasi_gudang')->nullable();
            $table->string('kabupaten_koordinator');
            $table->string('wilayah_koordinator');
            $table->string('nama_koordinator_wilayah');
            $table->string('kabupaten_sppg');
            $table->string('wilayah_koordinator_sppg');
            $table->string('sppg')->nullable();
            $table->string('sppg2')->nullable();
            $table->string('sppg3')->nullable();
            $table->string('sppg4')->nullable();
            $table->string('sppg5')->nullable();
            $table->string('sppg6')->nullable();
            $table->string('sppg7')->nullable();
            $table->string('sppg8')->nullable();
            $table->string('sppg9')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('supplier');
    }
};