<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

/**
 * Migration tabel rekap_poin_siswa
 * Tabel ini di-update secara otomatis oleh PoinService setiap kali
 * ada pelanggaran yang dikonfirmasi atau dibatalkan.
 * Berfungsi sebagai cache/denormalized data agar dashboard cepat.
 */
return new class extends Migration
{
    public function up(): void
    {
        Schema::create('rekap_poin_siswa', function (Blueprint $table) {
            $table->id();

            // Satu record per siswa
            $table->foreignId('siswa_id')
                  ->unique()
                  ->constrained('siswa')
                  ->onDelete('cascade');

            // Total akumulasi poin pelanggaran yang sudah dikonfirmasi
            $table->unsignedSmallInteger('total_poin')->default(0);

            // Status berdasarkan total poin:
            // aman   = 0–49 poin
            // SP1    = 50–74 poin
            // SP2    = 75–99 poin
            // SP3    = 100+ poin
            $table->enum('status_sp', ['aman', 'SP1', 'SP2', 'SP3'])->default('aman');

            // Timestamp terakhir rekap diperbarui
            $table->timestamp('updated_at')->nullable();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('rekap_poin_siswa');
    }
};
