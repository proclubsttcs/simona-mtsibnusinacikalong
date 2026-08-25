<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

/**
 * Migration tabel surat_peringatan
 * Menyimpan Surat Peringatan (SP1/SP2/SP3) yang diterbitkan oleh BK/Admin
 */
return new class extends Migration
{
    public function up(): void
    {
        Schema::create('surat_peringatan', function (Blueprint $table) {
            $table->id();

            // Siswa yang menerima SP
            $table->foreignId('siswa_id')
                  ->constrained('siswa')
                  ->onDelete('restrict');

            // Jenis surat peringatan
            $table->enum('jenis_sp', ['SP1', 'SP2', 'SP3']);

            // Total poin akumulasi saat SP diterbitkan
            $table->unsignedSmallInteger('total_poin_saat_ini');

            // Tanggal penerbitan SP
            $table->date('tanggal_terbit');

            // Keterangan tambahan
            $table->text('keterangan')->nullable();

            // Admin/BK yang menerbitkan SP
            $table->foreignId('diterbitkan_oleh')
                  ->constrained('users')
                  ->onDelete('restrict');

            // Path file PDF yang di-generate
            $table->string('file_pdf')->nullable();

            // Status SP: aktif atau sudah diselesaikan/diarsipkan
            $table->enum('status', ['aktif', 'selesai'])->default('aktif');

            $table->timestamps();

            // Index untuk query SP per siswa
            $table->index(['siswa_id', 'jenis_sp']);
            $table->index('status');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('surat_peringatan');
    }
};
