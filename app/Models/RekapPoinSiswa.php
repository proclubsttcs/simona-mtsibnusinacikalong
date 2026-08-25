<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class RekapPoinSiswa extends Model
{
    // Tabel ini tidak punya created_at, hanya updated_at
    public $timestamps = false;

    protected $table = 'rekap_poin_siswa';

    protected $fillable = [
        'siswa_id',
        'total_poin',
        'status_sp',
        'updated_at',
    ];

    protected function casts(): array
    {
        return [
            'total_poin' => 'integer',
            'updated_at' => 'datetime',
        ];
    }

    // ─── Helper Methods ─────────────────────────────────────────

    /**
     * Tentukan status SP berdasarkan total poin:
     * 0–49   = aman
     * 50–74  = SP1
     * 75–99  = SP2
     * 100+   = SP3
     */
    public static function hitungStatusSp(int $poin): string
    {
        if ($poin >= 100) return 'SP3';
        if ($poin >= 75)  return 'SP2';
        if ($poin >= 50)  return 'SP1';
        return 'aman';
    }

    /** CSS badge class berdasarkan status SP */
    public function getBadgeClassAttribute(): string
    {
        return match ($this->status_sp) {
            'aman' => 'badge-aman',
            'SP1'  => 'badge-sp1',
            'SP2'  => 'badge-sp2',
            'SP3'  => 'badge-sp3',
            default => 'badge',
        };
    }

    // ─── Relationships ───────────────────────────────────────────

    public function siswa(): BelongsTo
    {
        return $this->belongsTo(Siswa::class, 'siswa_id');
    }
}
