<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

/**
 * Migration tabel jenis_pelanggaran
 * Menyimpan katalog jenis pelanggaran beserta poin dan kategorinya
 */
return new class extends Migration
{
    public function up(): void
    {
        Schema::create('jenis_pelanggaran', function (Blueprint $table) {
            $table->id();

            // Kode unik, misal: RNG-001, SDG-001, BRT-001, SBT-001
            $table->string('kode', 20)->unique();

            $table->string('nama');

            // Kategori berdasarkan tingkat pelanggaran
            $table->enum('kategori', ['ringan', 'sedang', 'berat', 'sangat_berat']);

            // Jumlah poin yang dikurangkan per pelanggaran
            $table->unsignedSmallInteger('poin');

            // Keterangan tambahan (misal: per hari, per kejadian, + ganti rugi)
            $table->string('keterangan')->nullable();

            // Jenis pelanggaran bisa dinonaktifkan tanpa dihapus
            $table->boolean('is_active')->default(true);

            $table->timestamps();

            // Index untuk filter berdasarkan kategori
            $table->index('kategori');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('jenis_pelanggaran');
    }
};
