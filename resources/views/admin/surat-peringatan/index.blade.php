@extends('layouts.app')

@section('title', 'Surat Peringatan')
@section('page-title', 'Surat Peringatan')

@section('breadcrumb')
<span class="text-slate-400">/</span>
<span class="text-slate-500">Surat Peringatan</span>
@endsection

@section('content')

{{-- Stat Cards --}}
<div class="grid grid-cols-2 lg:grid-cols-6 gap-4 mb-6">
    @php
        $statItems = [
            ['label' => 'Total SP',    'value' => $stats['total'],  'bg' => 'bg-slate-50',   'text' => 'text-slate-700'],
            ['label' => 'SP Aktif',    'value' => $stats['aktif'],  'bg' => 'bg-red-50',     'text' => 'text-danger'],
            ['label' => 'SP1',         'value' => $stats['sp1'],    'bg' => 'bg-amber-50',   'text' => 'text-amber-700'],
            ['label' => 'SP2',         'value' => $stats['sp2'],    'bg' => 'bg-orange-50',  'text' => 'text-orange-700'],
            ['label' => 'SP3',         'value' => $stats['sp3'],    'bg' => 'bg-red-50',     'text' => 'text-red-700'],
            ['label' => 'Bulan Ini',   'value' => $stats['bulan'],  'bg' => 'bg-green-50',    'text' => 'text-secondary'],
        ];
    @endphp
    @foreach($statItems as $s)
    <div class="card p-4 text-center">
        <p class="font-heading text-2xl font-bold {{ $s['text'] }}">{{ $s['value'] }}</p>
        <p class="text-xs text-slate-500 mt-1 font-medium">{{ $s['label'] }}</p>
    </div>
    @endforeach
</div>

{{-- Header + Tombol Buat Manual --}}
<div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4 mb-5">
    <p class="text-sm text-slate-500">
        Total <span class="font-semibold text-slate-700">{{ $suratPeringatan->total() }}</span> surat peringatan
    </p>
    <a href="{{ route('admin.surat-peringatan.create') }}" class="btn-primary">
        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/>
        </svg>
        Buat SP Manual
    </a>
</div>

{{-- Filter --}}
<div class="card mb-5">
    <form action="{{ route('admin.surat-peringatan.index') }}" method="GET"
          class="flex flex-wrap gap-3">

        <div class="flex-1 min-w-[180px] relative">
            <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                <svg class="w-4 h-4 text-slate-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                        d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/>
                </svg>
            </div>
            <input type="text" name="cari" value="{{ request('cari') }}"
                   placeholder="Cari nama / NIS siswa..." class="form-input pl-9">
        </div>

        <select name="jenis_sp" class="form-input w-auto">
            <option value="">Semua Jenis</option>
            <option value="SP1" {{ request('jenis_sp') == 'SP1' ? 'selected' : '' }}>SP1</option>
            <option value="SP2" {{ request('jenis_sp') == 'SP2' ? 'selected' : '' }}>SP2</option>
            <option value="SP3" {{ request('jenis_sp') == 'SP3' ? 'selected' : '' }}>SP3</option>
        </select>

        <select name="status" class="form-input w-auto">
            <option value="">Semua Status</option>
            <option value="aktif"   {{ request('status') == 'aktif'   ? 'selected' : '' }}>Aktif</option>
            <option value="selesai" {{ request('status') == 'selesai' ? 'selected' : '' }}>Selesai</option>
        </select>

        <select name="kelas" class="form-input w-auto">
            <option value="">Semua Kelas</option>
            @foreach($kelasList as $k)
            <option value="{{ $k }}" {{ request('kelas') == $k ? 'selected' : '' }}>{{ $k }}</option>
            @endforeach
        </select>

        <input type="month" name="bulan" value="{{ request('bulan') }}" class="form-input w-auto">

        <button type="submit" class="btn-primary">Filter</button>
        @if(request()->hasAny(['cari','jenis_sp','status','kelas','bulan']))
        <a href="{{ route('admin.surat-peringatan.index') }}" class="btn-secondary">Reset</a>
        @endif
    </form>
</div>

{{-- Tabel --}}
<div class="card p-0 overflow-hidden">
    @if($suratPeringatan->isEmpty())
    <div class="empty-state py-16">
        <div class="empty-state-icon">
            <svg class="w-8 h-8 text-slate-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                    d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/>
            </svg>
        </div>
        <p class="text-sm font-medium text-slate-600">Belum ada surat peringatan</p>
        <p class="text-xs text-slate-400 mt-1 mb-4">SP akan otomatis terbit saat poin siswa melebihi batas</p>
        <a href="{{ route('admin.surat-peringatan.create') }}" class="btn-primary">Buat SP Manual</a>
    </div>
    @else
    <div class="overflow-x-auto">
        <table class="w-full table-simon">
            <thead>
                <tr>
                    <th>Siswa</th>
                    <th class="text-center">Jenis SP</th>
                    <th class="text-center">Poin Saat Itu</th>
                    <th>Tanggal Terbit</th>
                    <th>Diterbitkan Oleh</th>
                    <th class="text-center">Status</th>
                    <th class="text-right">Aksi</th>
                </tr>
            </thead>
            <tbody>
                @foreach($suratPeringatan as $sp)
                <tr class="{{ $sp->status === 'selesai' ? 'opacity-60' : '' }}">

                    {{-- Siswa --}}
                    <td>
                        <a href="{{ route('admin.siswa.show', $sp->siswa) }}"
                           class="flex items-center gap-2.5 group">
                            <img src="{{ $sp->siswa->foto_url }}" alt="{{ $sp->siswa->nama }}"
                                 class="w-8 h-8 rounded-lg object-cover flex-shrink-0">
                            <div>
                                <p class="font-semibold text-sm text-slate-700 group-hover:text-secondary transition-colors">
                                    {{ $sp->siswa->nama }}
                                </p>
                                <p class="text-xs text-slate-400">{{ $sp->siswa->kelas }}</p>
                            </div>
                        </a>
                    </td>

                    {{-- Jenis SP --}}
                    <td class="text-center">
                        <span class="inline-flex items-center justify-center w-12 h-8 rounded-lg font-bold text-sm text-white
                            {{ $sp->jenis_sp === 'SP1' ? 'bg-amber-500' : ($sp->jenis_sp === 'SP2' ? 'bg-orange-600' : 'bg-red-600') }}">
                            {{ $sp->jenis_sp }}
                        </span>
                    </td>

                    {{-- Poin --}}
                    <td class="text-center">
                        <span class="font-bold text-sm
                            {{ $sp->jenis_sp === 'SP3' ? 'text-danger' : ($sp->jenis_sp === 'SP2' ? 'text-orange-600' : 'text-amber-600') }}">
                            {{ $sp->total_poin_saat_ini }}
                        </span>
                    </td>

                    {{-- Tanggal --}}
                    <td class="text-sm text-slate-600 whitespace-nowrap">
                        {{ $sp->tanggal_terbit->isoFormat('D MMM Y') }}
                    </td>

                    {{-- Diterbitkan Oleh --}}
                    <td class="text-xs text-slate-500">
                        {{ Str::words($sp->diterbitkanOleh?->name ?? '—', 2, '...') }}
                    </td>

                    {{-- Status --}}
                    <td class="text-center">
                        @if($sp->status === 'aktif')
                        <span class="badge bg-red-100 text-red-700">Aktif</span>
                        @else
                        <span class="badge bg-slate-100 text-slate-500">Selesai</span>
                        @endif
                    </td>

                    {{-- Aksi --}}
                    <td class="text-right">
                        <div class="flex items-center justify-end gap-1">
                            {{-- Detail --}}
                            <a href="{{ route('admin.surat-peringatan.show', $sp) }}"
                               class="p-1.5 text-slate-400 hover:text-secondary hover:bg-green-50 rounded-lg transition-colors"
                               title="Lihat Detail">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                        d="M15 12a3 3 0 11-6 0 3 3 0 016 0z M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/>
                                </svg>
                            </a>

                            {{-- Download PDF --}}
                            <a href="{{ route('admin.surat-peringatan.download', $sp) }}"
                               class="p-1.5 text-slate-400 hover:text-emerald-600 hover:bg-emerald-50 rounded-lg transition-colors"
                               title="Download PDF">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                        d="M12 10v6m0 0l-3-3m3 3l3-3m2 8H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/>
                                </svg>
                            </a>

                            {{-- Toggle Status --}}
                            <form action="{{ route('admin.surat-peringatan.toggle-status', $sp) }}" method="POST">
                                @csrf @method('PATCH')
                                <button type="submit"
                                    class="p-1.5 rounded-lg transition-colors
                                           {{ $sp->status === 'aktif'
                                               ? 'text-slate-400 hover:text-success hover:bg-emerald-50'
                                               : 'text-slate-400 hover:text-warning hover:bg-amber-50' }}"
                                    title="{{ $sp->status === 'aktif' ? 'Tandai Selesai' : 'Aktifkan Kembali' }}">
                                    @if($sp->status === 'aktif')
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                            d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/>
                                    </svg>
                                    @else
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                            d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15"/>
                                    </svg>
                                    @endif
                                </button>
                            </form>

                            {{-- Hapus --}}
                            <button
                                onclick="if(confirm('Hapus SP {{ $sp->jenis_sp }} untuk {{ addslashes($sp->siswa->nama) }}?')) document.getElementById('del-sp-{{ $sp->id }}').submit()"
                                class="p-1.5 text-slate-400 hover:text-danger hover:bg-red-50 rounded-lg transition-colors"
                                title="Hapus">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                        d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/>
                                </svg>
                            </button>
                            <form id="del-sp-{{ $sp->id }}"
                                  action="{{ route('admin.surat-peringatan.destroy', $sp) }}"
                                  method="POST" class="hidden">
                                @csrf @method('DELETE')
                            </form>
                        </div>
                    </td>
                </tr>
                @endforeach
            </tbody>
        </table>
    </div>
    @if($suratPeringatan->hasPages())
    <div class="px-4 py-4 border-t border-slate-100">
        {{ $suratPeringatan->links() }}
    </div>
    @endif
    @endif
</div>

@endsection
