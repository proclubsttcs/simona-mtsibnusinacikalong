<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Pelanggaran extends Model
{
    use HasFactory;

    protected $table = 'pelanggaran';

    protected $fillable = [
        'siswa_id',
        'jenis_pelanggaran_id',
        'user_id',
        'tanggal_pelanggaran',
        'poin_diberikan',
        'keterangan',
        'status',
        'dikonfirmasi_oleh',
        'dikonfirmasi_at',
        'alasan_tolak',
    ];

    protected function casts(): array
    {
        return [
            'tanggal_pelanggaran' => 'date',
            'dikonfirmasi_at'     => 'datetime',
            'poin_diberikan'      => 'integer',
        ];
    }

    // ─── Scope ──────────────────────────────────────────────────

    public function scopeMenunggu($query)
    {
        return $query->where('status', 'menunggu');
    }

    public function scopeDikonfirmasi($query)
    {
        return $query->where('status', 'dikonfirmasi');
    }

    public function scopeBulanIni($query)
    {
        return $query->whereMonth('tanggal_pelanggaran', now()->month)
                     ->whereYear('tanggal_pelanggaran', now()->year);
    }

    // ─── Helper Methods ─────────────────────────────────────────

    /** Label status yang mudah dibaca */
    public function getStatusLabelAttribute(): string
    {
        return match ($this->status) {
            'menunggu'     => 'Menunggu',
            'dikonfirmasi' => 'Dikonfirmasi',
            'ditolak'      => 'Ditolak',
            default        => ucfirst($this->status),
        };
    }

    /** CSS class badge untuk status */
    public function getStatusBadgeClassAttribute(): string
    {
        return match ($this->status) {
            'menunggu'     => 'badge-menunggu',
            'dikonfirmasi' => 'badge-dikonfirmasi',
            'ditolak'      => 'badge-ditolak',
            default        => 'badge',
        };
    }

    // ─── Relationships ───────────────────────────────────────────

    public function siswa(): BelongsTo
    {
        return $this->belongsTo(Siswa::class, 'siswa_id');
    }

    public function jenisPelanggaran(): BelongsTo
    {
        return $this->belongsTo(JenisPelanggaran::class, 'jenis_pelanggaran_id');
    }

    /** Wali kelas yang menginput */
    public function inputOleh(): BelongsTo
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    /** Admin/BK yang mengkonfirmasi */
    public function konfirmasiOleh(): BelongsTo
    {
        return $this->belongsTo(User::class, 'dikonfirmasi_oleh');
    }
}
