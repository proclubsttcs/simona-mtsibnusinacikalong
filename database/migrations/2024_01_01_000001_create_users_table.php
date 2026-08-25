<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

/**
 * Migration tabel users
 * Menyimpan akun login: admin/BK dan wali kelas
 */
return new class extends Migration
{
    public function up(): void
    {
        Schema::create('users', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->string('email')->unique();
            $table->timestamp('email_verified_at')->nullable();
            $table->string('password');

            // Role: admin = BK/Admin, wali_kelas = Wali Kelas
            $table->enum('role', ['admin', 'wali_kelas'])->default('wali_kelas');

            // Kelas yang diampu (hanya untuk wali_kelas, nullable untuk admin)
            $table->string('kelas')->nullable();

            // Foto profil
            $table->string('foto')->nullable();

            // Status akun
            $table->boolean('is_active')->default(true);

            // Tandai jika wali kelas belum pernah ganti password default
            $table->boolean('must_change_password')->default(false);

            $table->rememberToken();
            $table->timestamps();

            // Soft delete: data tidak benar-benar dihapus dari database
            $table->softDeletes();
        });

        Schema::create('password_reset_tokens', function (Blueprint $table) {
            $table->string('email')->primary();
            $table->string('token');
            $table->timestamp('created_at')->nullable();
        });

        Schema::create('sessions', function (Blueprint $table) {
            $table->string('id')->primary();
            $table->foreignId('user_id')->nullable()->index();
            $table->string('ip_address', 45)->nullable();
            $table->text('user_agent')->nullable();
            $table->longText('payload');
            $table->integer('last_activity')->index();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('sessions');
        Schema::dropIfExists('password_reset_tokens');
        Schema::dropIfExists('users');
    }
};
