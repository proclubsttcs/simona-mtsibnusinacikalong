<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

/**
 * Migration tabel pelanggaran
 * Mencatat setiap kejadian pelanggaran siswa yang diinput wali kelas
 * dan dikonfirmasi admin/BK
 */
return new class extends Migration
{
    public function up(): void
    {
        Schema::create('pelanggaran', function (Blueprint $table) {
            $table->id();

            // Siswa yang melanggar
            $table->foreignId('siswa_id')
                  ->constrained('siswa')
                  ->onDelete('restrict');

            // Jenis pelanggaran yang dilakukan
            $table->foreignId('jenis_pelanggaran_id')
                  ->constrained('jenis_pelanggaran')
                  ->onDelete('restrict');

            // Wali kelas yang menginput pelanggaran ini
            $table->foreignId('user_id')
                  ->constrained('users')
                  ->onDelete('restrict');

            // Tanggal kejadian pelanggaran
            $table->date('tanggal_pelanggaran');

            // Poin yang diberikan (bisa berbeda dari poin default jika ada pertimbangan)
            $table->unsignedSmallInteger('poin_diberikan');

            // Keterangan tambahan dari wali kelas
            $table->text('keterangan')->nullable();

            // Status konfirmasi pelanggaran oleh admin/BK
            $table->enum('status', ['menunggu', 'dikonfirmasi', 'ditolak'])
                  ->default('menunggu');

            // Admin/BK yang mengkonfirmasi atau menolak
            $table->foreignId('dikonfirmasi_oleh')
                  ->nullable()
                  ->constrained('users')
                  ->onDelete('set null');

            // Waktu konfirmasi/penolakan
            $table->timestamp('dikonfirmasi_at')->nullable();

            // Alasan penolakan (jika status = ditolak)
            $table->text('alasan_tolak')->nullable();

            $table->timestamps();

            // Index untuk mempercepat query laporan
            $table->index(['siswa_id', 'status']);
            $table->index('tanggal_pelanggaran');
            $table->index('status');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('pelanggaran');
    }
};
