<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class JenisPelanggaran extends Model
{
    use HasFactory;

    protected $table = 'jenis_pelanggaran';

    protected $fillable = [
        'kode',
        'nama',
        'kategori',
        'poin',
        'keterangan',
        'is_active',
    ];

    protected function casts(): array
    {
        return [
            'is_active' => 'boolean',
            'poin'      => 'integer',
        ];
    }

    // ─── Scope ──────────────────────────────────────────────────

    public function scopeAktif($query)
    {
        return $query->where('is_active', true);
    }

    public function scopeKategori($query, string $kategori)
    {
        return $query->where('kategori', $kategori);
    }

    // ─── Helper Methods ─────────────────────────────────────────

    /** Label kategori yang mudah dibaca */
    public function getKategoriLabelAttribute(): string
    {
        return match ($this->kategori) {
            'ringan'       => 'Ringan',
            'sedang'       => 'Sedang',
            'berat'        => 'Berat',
            'sangat_berat' => 'Sangat Berat',
            default        => ucfirst($this->kategori),
        };
    }

    /** CSS class badge berdasarkan kategori */
    public function getBadgeClassAttribute(): string
    {
        return match ($this->kategori) {
            'ringan'       => 'badge-ringan',
            'sedang'       => 'badge-sedang',
            'berat'        => 'badge-berat',
            'sangat_berat' => 'badge-sangat-berat',
            default        => 'badge',
        };
    }

    /** Warna teks berdasarkan kategori */
    public function getWarnaTeksAttribute(): string
    {
        return match ($this->kategori) {
            'ringan'       => 'text-emerald-600',
            'sedang'       => 'text-amber-600',
            'berat'        => 'text-orange-600',
            'sangat_berat' => 'text-red-600',
            default        => 'text-slate-600',
        };
    }

    // ─── Relationships ───────────────────────────────────────────

    /** Semua pelanggaran dengan jenis ini */
    public function pelanggaran(): HasMany
    {
        return $this->hasMany(Pelanggaran::class, 'jenis_pelanggaran_id');
    }
}
