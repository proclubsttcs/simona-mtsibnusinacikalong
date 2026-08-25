@extends('layouts.app')

@section('title', 'Dashboard Admin')
@section('page-title', 'Dashboard')

@section('content')

{{-- ── Header Selamat Datang ────────────────────────────────────── --}}
<div class="page-header mb-6">
    <div class="flex items-center justify-between">
        <div>
            <h2 class="font-heading text-2xl font-bold text-white">
                Selamat Datang, {{ Str::words(auth()->user()->name, 2) }}! 👋
            </h2>
            <p class="text-green-200/70 text-sm mt-1">
                {{ now()->isoFormat('dddd, D MMMM Y') }} Panel Admin / BK
            </p>
        </div>
        @if($stats['menunggu_konfirmasi'] > 0)
        <a href="#" class="flex items-center gap-2 px-4 py-2.5 bg-white/15 hover:bg-white/25
                           text-white text-sm font-semibold rounded-xl transition-all duration-200
                           border border-white/20">
            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                    d="M15 17h5l-1.405-1.405A2.032 2.032 0 0118 14.158V11a6.002 6.002 0 00-4-5.659V5a2 2 0 10-4 0v.341C7.67 6.165 6 8.388 6 11v3.159c0 .538-.214 1.055-.595 1.436L4 17h5m6 0v1a3 3 0 11-6 0v-1m6 0H9"/>
            </svg>
            {{ $stats['menunggu_konfirmasi'] }} Menunggu Konfirmasi
        </a>
        @endif
    </div>
</div>

{{-- ── Stat Cards 3×2 Grid ─────────────────────────────────────── --}}
<div class="grid grid-cols-2 lg:grid-cols-3 gap-4 mb-6">

    {{-- 1. Total Siswa --}}
    <div class="stat-card">
        <div class="stat-icon bg-green-50">
            <svg class="w-6 h-6 text-secondary" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                    d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0z"/>
            </svg>
        </div>
        <div>
            <p class="text-xs font-semibold text-slate-500 uppercase tracking-wide">Total Siswa</p>
            <p class="font-heading text-2xl font-bold text-slate-800 mt-0.5">
                {{ number_format($stats['total_siswa']) }}
            </p>
            <p class="text-xs text-slate-400 mt-1">siswa aktif</p>
        </div>
    </div>

    {{-- 2. Pelanggaran Bulan Ini --}}
    <div class="stat-card">
        <div class="stat-icon bg-orange-50">
            <svg class="w-6 h-6 text-warning" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                    d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2"/>
            </svg>
        </div>
        <div>
            <p class="text-xs font-semibold text-slate-500 uppercase tracking-wide">Pelanggaran Bulan Ini</p>
            <p class="font-heading text-2xl font-bold text-slate-800 mt-0.5">
                {{ number_format($stats['pelanggaran_bulan_ini']) }}
            </p>
            @php $beda = $stats['pelanggaran_bulan_ini'] - $bulanLalu['pelanggaran']; @endphp
            <p class="text-xs mt-1 {{ $beda > 0 ? 'text-danger' : ($beda < 0 ? 'text-success' : 'text-slate-400') }}">
                @if($beda > 0) ↑ {{ $beda }} dari bulan lalu
                @elseif($beda < 0) ↓ {{ abs($beda) }} dari bulan lalu
                @else Sama dengan bulan lalu
                @endif
            </p>
        </div>
    </div>

    {{-- 3. Siswa Bermasalah (SP) --}}
    <div class="stat-card">
        <div class="stat-icon bg-red-50">
            <svg class="w-6 h-6 text-danger" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                    d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"/>
            </svg>
        </div>
        <div>
            <p class="text-xs font-semibold text-slate-500 uppercase tracking-wide">Siswa Bermasalah</p>
            <p class="font-heading text-2xl font-bold text-slate-800 mt-0.5">
                {{ number_format($stats['siswa_sp_aktif']) }}
            </p>
            <p class="text-xs text-slate-400 mt-1">memiliki SP aktif</p>
        </div>
    </div>

    {{-- 4. Menunggu Konfirmasi --}}
    <div class="stat-card border-2 {{ $stats['menunggu_konfirmasi'] > 0 ? 'border-amber-200 bg-amber-50/50' : 'border-transparent' }}">
        <div class="stat-icon {{ $stats['menunggu_konfirmasi'] > 0 ? 'bg-amber-100' : 'bg-slate-50' }}">
            <svg class="w-6 h-6 {{ $stats['menunggu_konfirmasi'] > 0 ? 'text-accent animate-pulse-soft' : 'text-slate-400' }}"
                 fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                    d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/>
            </svg>
        </div>
        <div>
            <p class="text-xs font-semibold text-slate-500 uppercase tracking-wide">Menunggu Konfirmasi</p>
            <p class="font-heading text-2xl font-bold text-slate-800 mt-0.5">
                {{ number_format($stats['menunggu_konfirmasi']) }}
            </p>
            <p class="text-xs text-slate-400 mt-1">pelanggaran baru</p>
        </div>
    </div>

    {{-- 5. SP Bulan Ini --}}
    <div class="stat-card">
        <div class="stat-icon bg-purple-50">
            <svg class="w-6 h-6 text-purple" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                    d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/>
            </svg>
        </div>
        <div>
            <p class="text-xs font-semibold text-slate-500 uppercase tracking-wide">SP Terbit Bulan Ini</p>
            <p class="font-heading text-2xl font-bold text-slate-800 mt-0.5">
                {{ number_format($stats['sp_bulan_ini']) }}
            </p>
            <p class="text-xs text-slate-400 mt-1">surat peringatan</p>
        </div>
    </div>

    {{-- 6. Siswa Aman --}}
    <div class="stat-card">
        <div class="stat-icon bg-emerald-50">
            <svg class="w-6 h-6 text-success" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                    d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z"/>
            </svg>
        </div>
        <div>
            <p class="text-xs font-semibold text-slate-500 uppercase tracking-wide">Siswa Aman</p>
            <p class="font-heading text-2xl font-bold text-slate-800 mt-0.5">
                {{ number_format($stats['siswa_aman']) }}
            </p>
            <p class="text-xs text-slate-400 mt-1">poin di bawah 50</p>
        </div>
    </div>
</div>

{{-- ── Baris Bawah: Siswa Risiko + Pelanggaran Terbaru ─────────── --}}
<div class="grid grid-cols-1 lg:grid-cols-5 gap-6">

    {{-- Siswa Poin Tertinggi --}}
    <div class="lg:col-span-3 card">
        <div class="flex items-center justify-between mb-4">
            <h3 class="font-heading font-bold text-slate-800">10 Siswa Poin Tertinggi</h3>
            <a href="{{ route('admin.siswa.index') }}"
               class="text-xs text-secondary hover:text-secondary-700 font-medium">
                Lihat semua →
            </a>
        </div>

        @if($siswaRisiko->isEmpty())
        <div class="empty-state py-10">
            <div class="empty-state-icon">
                <svg class="w-8 h-8 text-slate-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                        d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/>
                </svg>
            </div>
            <p class="text-sm font-medium text-slate-600">Belum ada data pelanggaran</p>
            <p class="text-xs text-slate-400 mt-1">Semua siswa masih aman</p>
        </div>
        @else
        <div class="space-y-3">
            @foreach($siswaRisiko as $i => $s)
            @php $poin = $s->rekapPoin?->total_poin ?? 0; @endphp
            <div class="flex items-center gap-3">
                {{-- Ranking --}}
                <div class="w-7 h-7 rounded-lg {{ $i < 3 ? 'bg-amber-100 text-amber-700' : 'bg-slate-100 text-slate-500' }}
                            text-xs font-bold flex items-center justify-center flex-shrink-0">
                    {{ $i + 1 }}
                </div>

                {{-- Foto & Nama --}}
                <img src="{{ $s->foto_url }}" alt="{{ $s->nama }}"
                     class="w-8 h-8 rounded-xl object-cover flex-shrink-0">
                <div class="flex-1 min-w-0">
                    <div class="flex items-center justify-between">
                        <p class="text-sm font-semibold text-slate-700 truncate">{{ $s->nama }}</p>
                        <span class="text-sm font-bold {{ $poin >= 100 ? 'text-danger' : ($poin >= 75 ? 'text-orange-600' : ($poin >= 50 ? 'text-amber-600' : 'text-slate-600')) }} ml-2 flex-shrink-0">
                            {{ $poin }}
                        </span>
                    </div>
                    <div class="flex items-center gap-2 mt-1">
                        <p class="text-xs text-slate-400">{{ $s->kelas }}</p>
                        <div class="flex-1 progress-bar-wrap">
                            <div class="{{ $s->progress_color }} progress-bar-fill"
                                 style="width: {{ $s->progress_persen }}%"></div>
                        </div>
                        <span class="{{ $s->rekapPoin?->badge_class ?? 'badge-aman' }} text-[10px]">
                            {{ $s->rekapPoin?->status_sp ?? 'aman' }}
                        </span>
                    </div>
                </div>
            </div>
            @endforeach
        </div>
        @endif
    </div>

    {{-- Panel Kanan: Ringkasan & Pelanggaran Terbaru --}}
    <div class="lg:col-span-2 space-y-6">

        {{-- Distribusi Kategori --}}
        <div class="card">
            <h3 class="font-heading font-bold text-slate-800 mb-4">
                Distribusi Kategori Bulan Ini
            </h3>
            @php
                $total = array_sum($distribusiKategori);
                $kategoriConfig = [
                    'ringan'       => ['label' => 'Ringan',       'color' => 'bg-emerald-400'],
                    'sedang'       => ['label' => 'Sedang',       'color' => 'bg-amber-400'],
                    'berat'        => ['label' => 'Berat',        'color' => 'bg-orange-500'],
                    'sangat_berat' => ['label' => 'Sangat Berat', 'color' => 'bg-red-500'],
                ];
            @endphp

            @if($total === 0)
            <p class="text-sm text-slate-400 text-center py-4">Belum ada pelanggaran dikonfirmasi bulan ini</p>
            @else
            <div class="space-y-3">
                @foreach($kategoriConfig as $key => $cfg)
                @php $jml = $distribusiKategori[$key] ?? 0; $pct = $total > 0 ? round(($jml / $total) * 100) : 0; @endphp
                <div>
                    <div class="flex justify-between text-xs mb-1">
                        <span class="text-slate-600 font-medium">{{ $cfg['label'] }}</span>
                        <span class="text-slate-500">{{ $jml }} ({{ $pct }}%)</span>
                    </div>
                    <div class="progress-bar-wrap">
                        <div class="{{ $cfg['color'] }} progress-bar-fill rounded-full"
                             style="width: {{ $pct }}%"></div>
                    </div>
                </div>
                @endforeach
            </div>
            @endif
        </div>

        {{-- Pelanggaran Terbaru Menunggu --}}
        <div class="card">
            <div class="flex items-center justify-between mb-4">
                <h3 class="font-heading font-bold text-slate-800">Menunggu Konfirmasi</h3>
                @if($pelanggaranTerbaru->isNotEmpty())
                <span class="badge bg-amber-100 text-amber-700">{{ $pelanggaranTerbaru->count() }}</span>
                @endif
            </div>

            @if($pelanggaranTerbaru->isEmpty())
            <div class="text-center py-6">
                <svg class="w-10 h-10 text-emerald-300 mx-auto mb-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                        d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/>
                </svg>
                <p class="text-sm text-slate-400">Semua sudah dikonfirmasi</p>
            </div>
            @else
            <div class="space-y-3">
                @foreach($pelanggaranTerbaru as $p)
                <div class="flex items-start gap-2.5 p-2.5 bg-amber-50/60 rounded-xl border border-amber-100">
                    <div class="w-7 h-7 rounded-lg bg-amber-100 flex items-center justify-center flex-shrink-0 mt-0.5">
                        <svg class="w-3.5 h-3.5 text-amber-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
                        </svg>
                    </div>
                    <div class="flex-1 min-w-0">
                        <p class="text-xs font-semibold text-slate-700 truncate">{{ $p->siswa->nama }}</p>
                        <p class="text-xs text-slate-500 truncate">{{ $p->jenisPelanggaran->nama }}</p>
                        <p class="text-[10px] text-slate-400 mt-0.5">
                            {{ $p->tanggal_pelanggaran->isoFormat('D MMM') }}
                            · {{ $p->inputOleh->name }}
                        </p>
                    </div>
                    <span class="text-xs font-bold text-orange-600 flex-shrink-0">+{{ $p->poin_diberikan }}</span>
                </div>
                @endforeach
            </div>
            @endif
        </div>
    </div>
</div>

@endsection
