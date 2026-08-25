<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Illuminate\Database\Eloquent\SoftDeletes;

class Siswa extends Model
{
    use HasFactory, SoftDeletes;

    // Nama tabel tidak mengikuti konvensi Laravel (siswa, bukan siswas)
    protected $table = 'siswa';

    protected $fillable = [
        'nis',
        'nama',
        'kelas',
        'jenis_kelamin',
        'foto',
        'nama_orang_tua',
        'no_hp_orang_tua',
        'alamat',
        'user_id',
        'is_active',
    ];

    protected function casts(): array
    {
        return [
            'is_active' => 'boolean',
        ];
    }

    // ─── Scope ──────────────────────────────────────────────────

    /** Hanya siswa yang aktif */
    public function scopeAktif($query)
    {
        return $query->where('is_active', true);
    }

    /** Filter berdasarkan kelas */
    public function scopeKelas($query, string $kelas)
    {
        return $query->where('kelas', $kelas);
    }

    /** Filter berdasarkan kelas wali kelas yang login */
    public function scopeKelasWaliKelas($query, User $user)
    {
        return $query->where('user_id', $user->id);
    }

    // ─── Helper Methods ─────────────────────────────────────────

    /** URL foto siswa atau avatar inisial */
    public function getFotoUrlAttribute(): string
    {
        if ($this->foto) {
            return asset('storage/' . $this->foto);
        }
        return 'https://ui-avatars.com/api/?name=' . urlencode($this->nama)
             . '&background=' . ($this->jenis_kelamin === 'L' ? '0891B2' : 'F59E0B')
             . '&color=fff&size=128';
    }

    /** Label jenis kelamin lengkap */
    public function getJenisKelaminLabelAttribute(): string
    {
        return $this->jenis_kelamin === 'L' ? 'Laki-laki' : 'Perempuan';
    }

    /** Total poin dari rekap (shortcut) */
    public function getTotalPoinAttribute(): int
    {
        return $this->rekapPoin?->total_poin ?? 0;
    }

    /** Status SP dari rekap (shortcut) */
    public function getStatusSpAttribute(): string
    {
        return $this->rekapPoin?->status_sp ?? 'aman';
    }

    /** Warna progress bar berdasarkan poin */
    public function getProgressColorAttribute(): string
    {
        $poin = $this->total_poin;
        if ($poin >= 100) return 'bg-gradient-to-r from-red-500 to-purple-600';
        if ($poin >= 75)  return 'bg-gradient-to-r from-orange-500 to-red-500';
        if ($poin >= 50)  return 'bg-gradient-to-r from-amber-400 to-orange-500';
        return 'bg-gradient-to-r from-emerald-400 to-cyan-500';
    }

    /** Persentase progress bar (maks 150 poin = 100%) */
    public function getProgressPersenAttribute(): int
    {
        return min(100, (int) (($this->total_poin / 150) * 100));
    }

    // ─── Relationships ───────────────────────────────────────────

    /** Wali kelas yang bertanggung jawab atas siswa ini */
    public function waliKelas(): BelongsTo
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    /** Semua pelanggaran siswa ini */
    public function pelanggaran(): HasMany
    {
        return $this->hasMany(Pelanggaran::class, 'siswa_id');
    }

    /** Pelanggaran yang sudah dikonfirmasi */
    public function pelanggaranDikonfirmasi(): HasMany
    {
        return $this->hasMany(Pelanggaran::class, 'siswa_id')
                    ->where('status', 'dikonfirmasi');
    }

    /** Semua surat peringatan siswa ini */
    public function suratPeringatan(): HasMany
    {
        return $this->hasMany(SuratPeringatan::class, 'siswa_id');
    }

    /** SP aktif saat ini */
    public function suratPeringatanAktif(): HasMany
    {
        return $this->hasMany(SuratPeringatan::class, 'siswa_id')
                    ->where('status', 'aktif');
    }

    /** Rekap poin (satu record per siswa) */
    public function rekapPoin(): HasOne
    {
        return $this->hasOne(RekapPoinSiswa::class, 'siswa_id');
    }
}
