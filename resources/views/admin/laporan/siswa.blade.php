@extends('layouts.app')

@section('title', 'Laporan Detail Siswa')
@section('page-title', 'Laporan Detail per Siswa')

@section('breadcrumb')
<span class="text-slate-400">/</span>
<a href="{{ route('admin.laporan.index') }}" class="hover:text-secondary transition-colors">Laporan</a>
<span class="text-slate-400">/</span>
<span class="text-slate-500">Detail Siswa</span>
@endsection

@section('content')

{{-- Filter & Export --}}
<div class="card mb-5">
    <form action="{{ route('admin.laporan.siswa') }}" method="GET"
          class="flex flex-wrap gap-3 items-end">

        <div class="flex-1 min-w-[180px] relative">
            <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                <svg class="w-4 h-4 text-slate-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                        d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/>
                </svg>
            </div>
            <input type="text" name="cari" value="{{ $cari }}"
                   placeholder="Cari nama / NIS..." class="form-input pl-9">
        </div>

        <select name="kelas" class="form-input w-auto">
            <option value="">Semua Kelas</option>
            @foreach($kelasList as $k)
            <option value="{{ $k }}" {{ $kelas == $k ? 'selected' : '' }}>{{ $k }}</option>
            @endforeach
        </select>

        <select name="status_sp" class="form-input w-auto">
            <option value="">Semua Status</option>
            <option value="aman" {{ $statusSp == 'aman' ? 'selected' : '' }}>Aman</option>
            <option value="SP1"  {{ $statusSp == 'SP1'  ? 'selected' : '' }}>SP1</option>
            <option value="SP2"  {{ $statusSp == 'SP2'  ? 'selected' : '' }}>SP2</option>
            <option value="SP3"  {{ $statusSp == 'SP3'  ? 'selected' : '' }}>SP3</option>
        </select>

        <button type="submit" class="btn-primary">Filter</button>
        @if($cari || $kelas || $statusSp)
        <a href="{{ route('admin.laporan.siswa') }}" class="btn-secondary">Reset</a>
        @endif

        {{-- Export --}}
        <div class="ml-auto flex gap-2">
            <a href="{{ route('admin.laporan.export-rekap-excel', ['kelas' => $kelas, 'status_sp' => $statusSp]) }}"
               class="btn-success text-sm">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                        d="M12 10v6m0 0l-3-3m3 3l3-3m2 8H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/>
                </svg>
                Excel
            </a>
        </div>
    </form>
</div>

{{-- Info jumlah --}}
<div class="flex items-center justify-between mb-4">
    <p class="text-sm text-slate-500">
        Menampilkan <span class="font-semibold text-slate-700">{{ $siswa->total() }}</span> siswa
        @if($kelas) di kelas <span class="font-semibold text-secondary">{{ $kelas }}</span> @endif
        @if($statusSp) · Status <span class="font-semibold">{{ $statusSp }}</span> @endif
    </p>
</div>

{{-- Tabel --}}
<div class="card p-0 overflow-hidden">
    @if($siswa->isEmpty())
    <div class="empty-state py-14">
        <div class="empty-state-icon">
            <svg class="w-8 h-8 text-slate-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                    d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0z"/>
            </svg>
        </div>
        <p class="text-sm font-medium text-slate-600">Tidak ada data siswa</p>
    </div>
    @else
    <div class="overflow-x-auto">
        <table class="w-full table-simon">
            <thead>
                <tr>
                    <th>Siswa</th>
                    <th>Kelas</th>
                    <th>Wali Kelas</th>
                    <th>Akumulasi Poin</th>
                    <th class="text-center">Status SP</th>
                    <th class="text-center">Jml Pelanggaran</th>
                    <th class="text-center">Jml SP</th>
                    <th class="text-right">Aksi</th>
                </tr>
            </thead>
            <tbody>
                @foreach($siswa as $s)
                @php
                    $poin     = $s->rekapPoin?->total_poin ?? 0;
                    $statusSp = $s->rekapPoin?->status_sp ?? 'aman';
                @endphp
                <tr>
                    {{-- Siswa --}}
                    <td>
                        <div class="flex items-center gap-2.5">
                            <img src="{{ $s->foto_url }}" alt="{{ $s->nama }}"
                                 class="w-8 h-8 rounded-lg object-cover flex-shrink-0">
                            <div>
                                <p class="font-semibold text-sm text-slate-700">{{ $s->nama }}</p>
                                <p class="text-xs text-slate-400 font-mono">{{ $s->nis }}</p>
                            </div>
                        </div>
                    </td>

                    {{-- Kelas --}}
                    <td>
                        <span class="badge bg-green-50 text-green-700">{{ $s->kelas }}</span>
                    </td>

                    {{-- Wali Kelas --}}
                    <td class="text-xs text-slate-500">
                        {{ Str::words($s->waliKelas?->name ?? '—', 2, '...') }}
                    </td>

                    {{-- Poin + Progress --}}
                    <td>
                        <div class="flex items-center gap-2 min-w-[120px]">
                            <div class="flex-1 progress-bar-wrap">
                                <div class="{{ $s->progress_color }} progress-bar-fill"
                                     style="width: {{ $s->progress_persen }}%"></div>
                            </div>
                            <span class="text-sm font-bold w-8 text-right
                                {{ $poin >= 100 ? 'text-danger' : ($poin >= 75 ? 'text-orange-600' : ($poin >= 50 ? 'text-amber-600' : 'text-slate-600')) }}">
                                {{ $poin }}
                            </span>
                        </div>
                    </td>

                    {{-- Status SP --}}
                    <td class="text-center">
                        <span class="{{ $s->rekapPoin?->badge_class ?? 'badge-aman' }}">
                            {{ $statusSp }}
                        </span>
                    </td>

                    {{-- Jumlah Pelanggaran --}}
                    <td class="text-center">
                        <span class="font-semibold text-slate-700">
                            {{ $s->pelanggaran()->dikonfirmasi()->count() }}
                        </span>
                    </td>

                    {{-- Jumlah SP --}}
                    <td class="text-center">
                        @php $jmlSp = $s->suratPeringatan->count(); @endphp
                        @if($jmlSp > 0)
                        <span class="badge bg-red-100 text-red-700">{{ $jmlSp }}</span>
                        @else
                        <span class="text-slate-300">—</span>
                        @endif
                    </td>

                    {{-- Aksi --}}
                    <td class="text-right">
                        <a href="{{ route('admin.siswa.show', $s) }}"
                           class="text-xs text-secondary hover:text-secondary-700 font-semibold">
                            Detail →
                        </a>
                    </td>
                </tr>
                @endforeach
            </tbody>
        </table>
    </div>
    @if($siswa->hasPages())
    <div class="px-4 py-4 border-t border-slate-100">
        {{ $siswa->links() }}
    </div>
    @endif
    @endif
</div>

@endsection
