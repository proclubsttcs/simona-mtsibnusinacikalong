@extends('layouts.app')

@section('title', 'Detail Pelanggaran')
@section('page-title', 'Detail Pelanggaran')

@section('breadcrumb')
<span class="text-slate-400">/</span>
<a href="{{ route('admin.pelanggaran.index') }}" class="hover:text-secondary">Pelanggaran</a>
<span class="text-slate-400">/</span>
<span class="text-slate-500">Detail</span>
@endsection

@section('content')
<div class="max-w-3xl mx-auto">
<div class="grid grid-cols-1 lg:grid-cols-5 gap-6">

    {{-- Kolom Kiri: Info Pelanggaran --}}
    <div class="lg:col-span-3 space-y-5">

        {{-- Status Banner --}}
        <div class="p-4 rounded-2xl border flex items-start gap-3
            {{ $pelanggaran->status === 'menunggu'     ? 'bg-amber-50   border-amber-200'   :
               ($pelanggaran->status === 'dikonfirmasi' ? 'bg-emerald-50 border-emerald-200' :
                                                          'bg-red-50     border-red-200') }}">
            @if($pelanggaran->status === 'menunggu')
            <svg class="w-5 h-5 text-accent mt-0.5 animate-pulse-soft flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/>
            </svg>
            <p class="text-sm font-semibold text-amber-700">Menunggu Konfirmasi BK</p>
            @elseif($pelanggaran->status === 'dikonfirmasi')
            <svg class="w-5 h-5 text-success mt-0.5 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/>
            </svg>
            <div>
                <p class="text-sm font-semibold text-emerald-700">Dikonfirmasi — Poin Sudah Dihitung</p>
                @if($pelanggaran->konfirmasiOleh)
                <p class="text-xs text-emerald-600 mt-0.5">
                    oleh <strong>{{ $pelanggaran->konfirmasiOleh->name }}</strong>
                    pada {{ $pelanggaran->dikonfirmasi_at->isoFormat('D MMMM Y, HH:mm') }}
                </p>
                @endif
            </div>
            @else
            <svg class="w-5 h-5 text-danger mt-0.5 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
            </svg>
            <div>
                <p class="text-sm font-semibold text-red-700">Ditolak</p>
                <p class="text-sm text-red-600 mt-0.5">{{ $pelanggaran->alasan_tolak }}</p>
                @if($pelanggaran->konfirmasiOleh)
                <p class="text-xs text-red-400 mt-1">
                    oleh {{ $pelanggaran->konfirmasiOleh->name }}
                    · {{ $pelanggaran->dikonfirmasi_at->isoFormat('D MMM Y, HH:mm') }}
                </p>
                @endif
            </div>
            @endif
        </div>

        {{-- Detail Rincian --}}
        <div class="card">
            <h3 class="font-heading font-bold text-slate-800 mb-5">Rincian Pelanggaran</h3>
            <dl class="grid grid-cols-2 gap-4">
                <div class="col-span-2">
                    <dt class="text-xs text-slate-400 uppercase tracking-wide font-medium">Jenis Pelanggaran</dt>
                    <dd class="mt-1.5 flex items-center gap-2 flex-wrap">
                        <span class="{{ $pelanggaran->jenisPelanggaran->badge_class }}">
                            {{ $pelanggaran->jenisPelanggaran->kategori_label }}
                        </span>
                        <span class="text-base font-bold text-slate-700">
                            {{ $pelanggaran->jenisPelanggaran->nama }}
                        </span>
                    </dd>
                    @if($pelanggaran->jenisPelanggaran->keterangan)
                    <dd class="text-xs text-slate-400 mt-1">{{ $pelanggaran->jenisPelanggaran->keterangan }}</dd>
                    @endif
                </div>
                <div>
                    <dt class="text-xs text-slate-400 uppercase tracking-wide font-medium">Tanggal Kejadian</dt>
                    <dd class="mt-1.5 text-sm font-semibold text-slate-700">
                        {{ $pelanggaran->tanggal_pelanggaran->isoFormat('dddd, D MMMM Y') }}
                    </dd>
                </div>
                <div>
                    <dt class="text-xs text-slate-400 uppercase tracking-wide font-medium">Dicatat Oleh</dt>
                    <dd class="mt-1.5 text-sm font-semibold text-slate-700">{{ $pelanggaran->inputOleh->name }}</dd>
                    <dd class="text-xs text-slate-400">{{ $pelanggaran->created_at->isoFormat('D MMM Y, HH:mm') }}</dd>
                </div>
                <div>
                    <dt class="text-xs text-slate-400 uppercase tracking-wide font-medium">Poin Default</dt>
                    <dd class="mt-1.5 text-sm font-semibold text-slate-600">{{ $pelanggaran->jenisPelanggaran->poin }} poin</dd>
                </div>
                <div>
                    <dt class="text-xs text-slate-400 uppercase tracking-wide font-medium">Poin Diberikan</dt>
                    <dd class="mt-1.5 text-xl font-bold
                        {{ $pelanggaran->poin_diberikan >= 50 ? 'text-danger' : ($pelanggaran->poin_diberikan >= 20 ? 'text-warning' : 'text-slate-700') }}">
                        +{{ $pelanggaran->poin_diberikan }} poin
                    </dd>
                </div>
                @if($pelanggaran->keterangan)
                <div class="col-span-2">
                    <dt class="text-xs text-slate-400 uppercase tracking-wide font-medium">Keterangan</dt>
                    <dd class="mt-1.5 text-sm text-slate-600 leading-relaxed">{{ $pelanggaran->keterangan }}</dd>
                </div>
                @endif
            </dl>
        </div>

        {{-- Tombol Aksi --}}
        @if($pelanggaran->status === 'menunggu')
        <div class="card" x-data="{ showTolak: false }">
            <h3 class="font-heading font-bold text-slate-700 mb-4">Tindakan</h3>
            <div class="flex gap-3">
                <form action="{{ route('admin.pelanggaran.konfirmasi', $pelanggaran) }}" method="POST" class="flex-1">
                    @csrf
                    <button type="submit" class="btn-success w-full justify-center">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/>
                        </svg>
                        Konfirmasi Pelanggaran
                    </button>
                </form>
                <button @click="showTolak = !showTolak" class="btn-danger flex-1 justify-center">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
                    </svg>
                    Tolak
                </button>
            </div>

            {{-- Form Tolak --}}
            <div x-show="showTolak" x-transition class="mt-4 pt-4 border-t border-slate-100" style="display:none;">
                <form action="{{ route('admin.pelanggaran.tolak', $pelanggaran) }}" method="POST">
                    @csrf
                    <label class="form-label">Alasan Penolakan <span class="text-danger">*</span></label>
                    <textarea name="alasan_tolak" rows="3" required minlength="10"
                              placeholder="Jelaskan alasan penolakan pelanggaran ini... (min. 10 karakter)"
                              class="form-input resize-none mb-3
                                     {{ $errors->has('alasan_tolak') ? 'border-danger ring-2 ring-danger/20' : '' }}"></textarea>
                    @error('alasan_tolak') <p class="form-error mb-2">{{ $message }}</p> @enderror
                    <div class="flex gap-2">
                        <button type="submit" class="btn-danger flex-1 justify-center">Kirim Penolakan</button>
                        <button type="button" @click="showTolak=false" class="btn-secondary">Batal</button>
                    </div>
                </form>
            </div>
        </div>
        @endif

        @if($pelanggaran->status !== 'menunggu')
        <form action="{{ route('admin.pelanggaran.batal-konfirmasi', $pelanggaran) }}" method="POST">
            @csrf
            <button type="submit"
                onclick="return confirm('Batalkan status ini dan kembalikan ke Menunggu?')"
                class="btn-secondary w-full justify-center text-sm">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                        d="M3 10h10a8 8 0 018 8v2M3 10l6 6m-6-6l6-6"/>
                </svg>
                Batalkan & Kembalikan ke Menunggu
            </button>
        </form>
        @endif
    </div>

    {{-- Kolom Kanan: Info Siswa --}}
    <div class="lg:col-span-2 space-y-5">
        <div class="card text-center">
            <img src="{{ $pelanggaran->siswa->foto_url }}" alt="{{ $pelanggaran->siswa->nama }}"
                 class="w-20 h-20 rounded-2xl object-cover mx-auto mb-3 border-2 border-slate-100">
            <h3 class="font-heading font-bold text-slate-800">{{ $pelanggaran->siswa->nama }}</h3>
            <p class="text-xs text-slate-400 font-mono mt-0.5">{{ $pelanggaran->siswa->nis }}</p>
            <div class="flex justify-center gap-2 mt-2">
                <span class="badge bg-green-50 text-green-700">{{ $pelanggaran->siswa->kelas }}</span>
            </div>

            <div class="divider"></div>

            <div class="text-left">
                <div class="flex justify-between items-center mb-1.5">
                    <span class="text-xs text-slate-500">Total Poin Saat Ini</span>
                    <span class="font-bold text-lg text-slate-800">{{ $pelanggaran->siswa->total_poin }}</span>
                </div>
                <div class="progress-bar-wrap mb-2">
                    <div class="{{ $pelanggaran->siswa->progress_color }} progress-bar-fill"
                         style="width: {{ $pelanggaran->siswa->progress_persen }}%"></div>
                </div>
                @if($pelanggaran->status === 'menunggu')
                <p class="text-xs text-amber-600 bg-amber-50 rounded-lg p-2 text-center">
                    Jika dikonfirmasi, poin akan menjadi
                    <strong>{{ $pelanggaran->siswa->total_poin + $pelanggaran->poin_diberikan }}</strong>
                </p>
                @endif
            </div>

            <div class="mt-3">
                <span class="{{ $pelanggaran->siswa->rekapPoin?->badge_class ?? 'badge-aman' }} text-sm px-4 py-1.5">
                    Status: {{ $pelanggaran->siswa->status_sp }}
                </span>
            </div>

            <div class="divider"></div>

            <a href="{{ route('admin.siswa.show', $pelanggaran->siswa) }}"
               class="btn-secondary w-full justify-center text-sm">
                Lihat Profil Lengkap →
            </a>
        </div>
    </div>
</div>
</div>
@endsection
