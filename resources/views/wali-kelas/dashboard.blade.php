@extends('layouts.app')

@section('title', 'Dashboard')
@section('page-title', 'Dashboard')

@section('content')

{{-- Header --}}
<div class="page-header mb-6">
    <h2 class="font-heading text-2xl font-bold text-white">
        Selamat Datang, {{ Str::words(auth()->user()->name, 2) }}! 👋
    </h2>
    <p class="text-green-200/70 text-sm mt-1">
        {{ now()->isoFormat('dddd, D MMMM Y') }} — Wali Kelas {{ auth()->user()->kelas }}
    </p>
</div>

{{-- Stat Cards --}}
<div class="grid grid-cols-2 lg:grid-cols-4 gap-4 mb-6">
    <div class="stat-card">
        <div class="stat-icon bg-green-50">
            <svg class="w-6 h-6 text-secondary" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                    d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0z"/>
            </svg>
        </div>
        <div>
            <p class="text-xs font-semibold text-slate-500 uppercase tracking-wide">Total Siswa</p>
            <p class="font-heading text-2xl font-bold text-slate-800 mt-0.5">{{ $stats['total_siswa'] }}</p>
            <p class="text-xs text-slate-400 mt-1">Kelas {{ auth()->user()->kelas }}</p>
        </div>
    </div>

    <div class="stat-card">
        <div class="stat-icon bg-red-50">
            <svg class="w-6 h-6 text-danger" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                    d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"/>
            </svg>
        </div>
        <div>
            <p class="text-xs font-semibold text-slate-500 uppercase tracking-wide">Siswa SP</p>
            <p class="font-heading text-2xl font-bold text-slate-800 mt-0.5">{{ $stats['siswa_sp'] }}</p>
            <p class="text-xs text-slate-400 mt-1">memiliki SP aktif</p>
        </div>
    </div>

    <div class="stat-card">
        <div class="stat-icon bg-orange-50">
            <svg class="w-6 h-6 text-warning" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                    d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2"/>
            </svg>
        </div>
        <div>
            <p class="text-xs font-semibold text-slate-500 uppercase tracking-wide">Saya Input</p>
            <p class="font-heading text-2xl font-bold text-slate-800 mt-0.5">{{ $stats['pelanggaran_bulan'] }}</p>
            <p class="text-xs text-slate-400 mt-1">bulan ini</p>
        </div>
    </div>

    <div class="stat-card {{ $stats['menunggu'] > 0 ? 'border-2 border-amber-200 bg-amber-50/40' : '' }}">
        <div class="stat-icon {{ $stats['menunggu'] > 0 ? 'bg-amber-100' : 'bg-slate-50' }}">
            <svg class="w-6 h-6 {{ $stats['menunggu'] > 0 ? 'text-accent animate-pulse-soft' : 'text-slate-400' }}"
                 fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                    d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/>
            </svg>
        </div>
        <div>
            <p class="text-xs font-semibold text-slate-500 uppercase tracking-wide">Menunggu</p>
            <p class="font-heading text-2xl font-bold text-slate-800 mt-0.5">{{ $stats['menunggu'] }}</p>
            <p class="text-xs text-slate-400 mt-1">menunggu BK</p>
        </div>
    </div>
</div>

{{-- Konten Bawah --}}
<div class="grid grid-cols-1 lg:grid-cols-2 gap-6">

    {{-- Siswa Risiko di Kelas --}}
    <div class="card">
        <div class="flex items-center justify-between mb-4">
            <h3 class="font-heading font-bold text-slate-800">Siswa Perlu Perhatian</h3>
            <a href="{{ route('wali-kelas.siswa.index') }}"
               class="text-xs text-secondary hover:text-secondary-700 font-medium">
                Lihat kelas →
            </a>
        </div>

        @if($siswaRisikoKelas->isEmpty())
        <div class="empty-state py-8">
            <div class="empty-state-icon">
                <svg class="w-8 h-8 text-emerald-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                        d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/>
                </svg>
            </div>
            <p class="text-sm font-medium text-slate-600">Kelas aman!</p>
            <p class="text-xs text-slate-400 mt-1">Tidak ada siswa dengan poin tinggi</p>
        </div>
        @else
        <div class="space-y-3">
            @foreach($siswaRisikoKelas as $s)
            @php $poin = $s->rekapPoin?->total_poin ?? 0; @endphp
            <a href="{{ route('wali-kelas.siswa.show', $s) }}"
               class="flex items-center gap-3 p-2.5 hover:bg-slate-50 rounded-xl transition-colors group">
                <img src="{{ $s->foto_url }}" alt="{{ $s->nama }}"
                     class="w-9 h-9 rounded-xl object-cover">
                <div class="flex-1 min-w-0">
                    <p class="text-sm font-semibold text-slate-700 group-hover:text-secondary transition-colors">
                        {{ $s->nama }}
                    </p>
                    <div class="flex items-center gap-2 mt-1">
                        <div class="flex-1 progress-bar-wrap">
                            <div class="{{ $s->progress_color }} progress-bar-fill"
                                 style="width: {{ $s->progress_persen }}%"></div>
                        </div>
                        <span class="text-xs font-bold text-slate-600">{{ $poin }} poin</span>
                    </div>
                </div>
                <span class="{{ $s->rekapPoin?->badge_class ?? 'badge-aman' }}">
                    {{ $s->status_sp }}
                </span>
            </a>
            @endforeach
        </div>
        @endif
    </div>

    {{-- Riwayat Input Saya --}}
    <div class="card">
        <div class="flex items-center justify-between mb-4">
            <h3 class="font-heading font-bold text-slate-800">Input Saya Terbaru</h3>
        </div>

        @if($pelanggaranSaya->isEmpty())
        <div class="empty-state py-8">
            <div class="empty-state-icon">
                <svg class="w-8 h-8 text-slate-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                        d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2"/>
                </svg>
            </div>
            <p class="text-sm font-medium text-slate-600">Belum ada input pelanggaran</p>
            <p class="text-xs text-slate-400 mt-1">Fitur input tersedia di Increment 2</p>
        </div>
        @else
        <div class="space-y-3">
            @foreach($pelanggaranSaya as $p)
            <div class="flex items-center gap-3 p-2.5 rounded-xl border border-slate-100">
                <div class="w-9 h-9 rounded-xl {{ $p->jenisPelanggaran->badge_class }} flex items-center justify-center flex-shrink-0">
                    <span class="text-xs font-bold">{{ $p->poin_diberikan }}</span>
                </div>
                <div class="flex-1 min-w-0">
                    <p class="text-sm font-semibold text-slate-700 truncate">{{ $p->siswa->nama }}</p>
                    <p class="text-xs text-slate-500 truncate">{{ $p->jenisPelanggaran->nama }}</p>
                </div>
                <div class="text-right flex-shrink-0">
                    <span class="{{ $p->status_badge_class }}">{{ $p->status_label }}</span>
                    <p class="text-[10px] text-slate-400 mt-0.5">{{ $p->tanggal_pelanggaran->isoFormat('D MMM') }}</p>
                </div>
            </div>
            @endforeach
        </div>
        @endif
    </div>
</div>

@endsection
