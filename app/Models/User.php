<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

class User extends Authenticatable
{
    use HasFactory, Notifiable, SoftDeletes;

    protected $fillable = [
        'name',
        'email',
        'password',
        'role',
        'kelas',
        'foto',
        'is_active',
        'must_change_password',
    ];

    protected $hidden = [
        'password',
        'remember_token',
    ];

    protected function casts(): array
    {
        return [
            'email_verified_at'    => 'datetime',
            'password'             => 'hashed',
            'is_active'            => 'boolean',
            'must_change_password' => 'boolean',
        ];
    }

    // ─── Scope ──────────────────────────────────────────────────

    /** Hanya user yang aktif */
    public function scopeAktif($query)
    {
        return $query->where('is_active', true);
    }

    /** Hanya wali kelas */
    public function scopeWaliKelas($query)
    {
        return $query->where('role', 'wali_kelas');
    }

    /** Hanya admin */
    public function scopeAdmin($query)
    {
        return $query->where('role', 'admin');
    }

    // ─── Helper Methods ─────────────────────────────────────────

    /** Cek apakah user adalah admin/BK */
    public function isAdmin(): bool
    {
        return $this->role === 'admin';
    }

    /** Cek apakah user adalah wali kelas */
    public function isWaliKelas(): bool
    {
        return $this->role === 'wali_kelas';
    }

    /** Dapatkan URL foto profil atau inisial avatar */
    public function getFotoUrlAttribute(): string
    {
        if ($this->foto) {
            return asset('storage/' . $this->foto);
        }
        return 'https://ui-avatars.com/api/?name=' . urlencode($this->name)
             . '&background=1E3A5F&color=fff&size=128';
    }

    /** Label role yang mudah dibaca */
    public function getRoleLabelAttribute(): string
    {
        return match ($this->role) {
            'admin'      => 'Admin / BK',
            'wali_kelas' => 'Wali Kelas',
            default      => ucfirst($this->role),
        };
    }

    // ─── Relationships ───────────────────────────────────────────

    /** Siswa yang diampu oleh wali kelas ini */
    public function siswa(): HasMany
    {
        return $this->hasMany(Siswa::class, 'user_id');
    }

    /** Pelanggaran yang diinput oleh user ini */
    public function pelanggaranDiinput(): HasMany
    {
        return $this->hasMany(Pelanggaran::class, 'user_id');
    }

    /** Pelanggaran yang dikonfirmasi oleh user ini (admin/BK) */
    public function pelanggaranDikonfirmasi(): HasMany
    {
        return $this->hasMany(Pelanggaran::class, 'dikonfirmasi_oleh');
    }

    /** Surat peringatan yang diterbitkan oleh user ini */
    public function suratPeringatan(): HasMany
    {
        return $this->hasMany(SuratPeringatan::class, 'diterbitkan_oleh');
    }
}
