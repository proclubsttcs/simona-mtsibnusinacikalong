<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class SuratPeringatan extends Model
{
    use HasFactory;

    protected $table = 'surat_peringatan';

    protected $fillable = [
        'siswa_id',
        'jenis_sp',
        'total_poin_saat_ini',
        'tanggal_terbit',
        'keterangan',
        'diterbitkan_oleh',
        'file_pdf',
        'status',
    ];

    protected function casts(): array
    {
        return [
            'tanggal_terbit'     => 'date',
            'total_poin_saat_ini' => 'integer',
        ];
    }

    // ─── Helper Methods ─────────────────────────────────────────

    /** CSS class gradient berdasarkan jenis SP */
    public function getGradientClassAttribute(): string
    {
        return match ($this->jenis_sp) {
            'SP1' => 'bg-sp1-gradient',
            'SP2' => 'bg-sp2-gradient',
            'SP3' => 'bg-sp3-gradient',
            default => 'bg-header-gradient',
        };
    }

    /** Warna teks berdasarkan jenis SP */
    public function getWarnaTeksAttribute(): string
    {
        return match ($this->jenis_sp) {
            'SP1' => 'text-amber-700',
            'SP2' => 'text-orange-700',
            'SP3' => 'text-red-700',
            default => 'text-slate-700',
        };
    }

    /** URL file PDF jika ada */
    public function getPdfUrlAttribute(): ?string
    {
        return $this->file_pdf ? asset('storage/' . $this->file_pdf) : null;
    }

    // ─── Relationships ───────────────────────────────────────────

    public function siswa(): BelongsTo
    {
        return $this->belongsTo(Siswa::class, 'siswa_id');
    }

    public function diterbitkanOleh(): BelongsTo
    {
        return $this->belongsTo(User::class, 'diterbitkan_oleh');
    }
}
