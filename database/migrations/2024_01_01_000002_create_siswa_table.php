<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

/**
 * Migration tabel siswa
 * Menyimpan data lengkap siswa MTs Ibnu Sina
 */
return new class extends Migration
{
    public function up(): void
    {
        Schema::create('siswa', function (Blueprint $table) {
            $table->id();

            // Nomor Induk Siswa — unik per siswa
            $table->string('nis', 20)->unique();

            $table->string('nama');

            // Contoh: VII-A, VIII-B, IX-C
            $table->string('kelas', 20);

            $table->enum('jenis_kelamin', ['L', 'P']);

            // Foto profil siswa (opsional)
            $table->string('foto')->nullable();

            // Data orang tua/wali
            $table->string('nama_orang_tua');
            $table->string('no_hp_orang_tua', 20);
            $table->text('alamat');

            // Foreign key ke tabel users (wali kelas yang bertanggung jawab)
            $table->foreignId('user_id')
                  ->constrained('users')
                  ->onDelete('restrict'); // jangan hapus wali kelas jika masih ada siswa

            // Status keaktifan siswa
            $table->boolean('is_active')->default(true);

            $table->timestamps();
            $table->softDeletes();

            // Index untuk mempercepat pencarian berdasarkan kelas
            $table->index('kelas');
            $table->index('is_active');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('siswa');
    }
};
