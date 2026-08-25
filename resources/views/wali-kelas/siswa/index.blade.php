@extends('layouts.app')

@section('title', 'Siswa Kelas ' . auth()->user()->kelas)
@section('page-title', 'Siswa Kelas ' . auth()->user()->kelas)

@section('breadcrumb')
<span class="text-slate-400">/</span>
<span class="text-slate-500">Siswa Saya</span>
@endsection

@section('content')

{{-- Filter & Search --}}
<div class="card mb-6">
    <form action="{{ route('wali-kelas.siswa.index') }}" method="GET"
          class="flex flex-wrap gap-3">
        <div class="flex-1 min-w-[200px] relative">
            <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                <svg class="w-4 h-4 text-slate-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                        d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/>
                </svg>
            </div>
            <input type="text" name="cari" value="{{ request('cari') }}"
                   placeholder="Cari nama atau NIS..."
                   class="form-input pl-9">
        </div>
        <select name="jenis_kelamin" class="form-input w-auto">
            <option value="">Semua J.K.</option>
            <option value="L" {{ request('jenis_kelamin') == 'L' ? 'selected' : '' }}>Laki-laki</option>
            <option value="P" {{ request('jenis_kelamin') == 'P' ? 'selected' : '' }}>Perempuan</option>
        </select>
        <button type="submit" class="btn-primary">
            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                    d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/>
            </svg>
            Cari
        </button>
        @if(request()->hasAny(['cari','jenis_kelamin']))
        <a href="{{ route('wali-kelas.siswa.index') }}" class="btn-secondary">Reset</a>
        @endif
    </form>
</div>

{{-- Info kelas --}}
<div class="mb-4 flex items-center gap-2">
    <svg class="w-4 h-4 text-slate-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
            d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0z"/>
    </svg>
    <p class="text-sm text-slate-500">
        Menampilkan <span class="font-semibold text-slate-700">{{ $siswa->total() }}</span> siswa
        di kelas <span class="font-semibold text-secondary">{{ auth()->user()->kelas }}</span>
    </p>
</div>

{{-- Grid Card Siswa --}}
@if($siswa->isEmpty())
<div class="card">
    <div class="empty-state py-16">
        <div class="empty-state-icon">
            <svg class="w-8 h-8 text-slate-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                    d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0z"/>
            </svg>
        </div>
        <p class="text-sm font-medium text-slate-600">Tidak ada siswa ditemukan</p>
        <p class="text-xs text-slate-400 mt-1">Hubungi Admin untuk menambah data siswa ke kelas ini</p>
    </div>
</div>
@else
<div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 xl:grid-cols-4 gap-4">
    @foreach($siswa as $s)
    @php $poin = $s->rekapPoin?->total_poin ?? 0; @endphp
    <a href="{{ route('wali-kelas.siswa.show', $s) }}"
       class="card hover:shadow-card-hover group cursor-pointer p-4 block">

        {{-- Header kartu --}}
        <div class="flex items-start gap-3 mb-3">
            <div class="relative">
                <img src="{{ $s->foto_url }}" alt="{{ $s->nama }}"
                     class="w-12 h-12 rounded-xl object-cover flex-shrink-0">
                {{-- Indikator gender --}}
                <div class="absolute -bottom-1 -right-1 w-5 h-5 rounded-full border-2 border-white
                            {{ $s->jenis_kelamin == 'L' ? 'bg-green-400' : 'bg-pink-400' }}
                            flex items-center justify-center">
                    <span class="text-white text-[8px] font-bold">{{ $s->jenis_kelamin }}</span>
                </div>
            </div>
            <div class="flex-1 min-w-0">
                <p class="font-semibold text-slate-800 truncate group-hover:text-secondary transition-colors">
                    {{ $s->nama }}
                </p>
                <p class="text-xs text-slate-400 font-mono">{{ $s->nis }}</p>
            </div>
        </div>

        {{-- Progress Poin --}}
        <div class="mb-3">
            <div class="flex justify-between items-center mb-1.5">
                <span class="text-xs text-slate-500">Poin Pelanggaran</span>
                <span class="text-sm font-bold {{ $poin >= 100 ? 'text-danger' : ($poin >= 75 ? 'text-orange-600' : ($poin >= 50 ? 'text-amber-600' : 'text-slate-600')) }}">
                    {{ $poin }}
                </span>
            </div>
            <div class="progress-bar-wrap">
                <div class="{{ $s->progress_color }} progress-bar-fill"
                     style="width: {{ $s->progress_persen }}%"></div>
            </div>
        </div>

        {{-- Footer status --}}
        <div class="flex items-center justify-between">
            <span class="{{ $s->rekapPoin?->badge_class ?? 'badge-aman' }}">
                {{ $s->status_sp === 'aman' ? '✓ Aman' : '⚠ ' . $s->status_sp }}
            </span>
            <span class="text-xs text-slate-400">
                {{ $s->pelanggaran_count ?? 0 }} pelanggaran
            </span>
        </div>
    </a>
    @endforeach
</div>

{{-- Pagination --}}
@if($siswa->hasPages())
<div class="mt-6">
    {{ $siswa->links() }}
</div>
@endif
@endif

@endsection
