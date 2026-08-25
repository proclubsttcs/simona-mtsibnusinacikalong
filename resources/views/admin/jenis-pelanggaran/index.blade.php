@extends('layouts.app')

@section('title', 'Jenis Pelanggaran')
@section('page-title', 'Katalog Jenis Pelanggaran')

@section('breadcrumb')
<span class="text-slate-400">/</span>
<span class="text-slate-500">Jenis Pelanggaran</span>
@endsection

@section('content')

{{-- Header --}}
<div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4 mb-6">
    <p class="text-sm text-slate-500">
        Total <span class="font-semibold text-slate-700">{{ $jenis->total() }}</span> jenis pelanggaran
    </p>
    <a href="{{ route('admin.jenis-pelanggaran.create') }}" class="btn-primary">
        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/>
        </svg>
        Tambah Jenis
    </a>
</div>

{{-- Filter --}}
<div class="card mb-5">
    <form action="{{ route('admin.jenis-pelanggaran.index') }}" method="GET"
          class="flex flex-wrap gap-3">
        <div class="flex-1 min-w-[180px] relative">
            <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                <svg class="w-4 h-4 text-slate-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                        d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/>
                </svg>
            </div>
            <input type="text" name="cari" value="{{ request('cari') }}"
                   placeholder="Cari nama pelanggaran..." class="form-input pl-9">
        </div>
        <select name="kategori" class="form-input w-auto">
            <option value="">Semua Kategori</option>
            <option value="ringan"       {{ request('kategori') == 'ringan'       ? 'selected' : '' }}>Ringan</option>
            <option value="sedang"       {{ request('kategori') == 'sedang'       ? 'selected' : '' }}>Sedang</option>
            <option value="berat"        {{ request('kategori') == 'berat'        ? 'selected' : '' }}>Berat</option>
            <option value="sangat_berat" {{ request('kategori') == 'sangat_berat' ? 'selected' : '' }}>Sangat Berat</option>
        </select>
        <button type="submit" class="btn-primary">Filter</button>
        @if(request()->hasAny(['cari','kategori']))
        <a href="{{ route('admin.jenis-pelanggaran.index') }}" class="btn-secondary">Reset</a>
        @endif
    </form>
</div>

{{-- Tabel --}}
<div class="card p-0 overflow-hidden">
    @if($jenis->isEmpty())
    <div class="empty-state py-14">
        <div class="empty-state-icon">
            <svg class="w-8 h-8 text-slate-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                    d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2"/>
            </svg>
        </div>
        <p class="text-sm font-medium text-slate-600">Belum ada jenis pelanggaran</p>
        <p class="text-xs text-slate-400 mt-1 mb-4">Jalankan seeder atau tambah manual</p>
        <a href="{{ route('admin.jenis-pelanggaran.create') }}" class="btn-primary">Tambah Jenis</a>
    </div>
    @else
    <div class="overflow-x-auto">
        <table class="w-full table-simon">
            <thead>
                <tr>
                    <th>Kode</th>
                    <th>Nama Pelanggaran</th>
                    <th>Kategori</th>
                    <th class="text-center">Poin</th>
                    <th>Keterangan</th>
                    <th class="text-center">Digunakan</th>
                    <th class="text-center">Status</th>
                    <th class="text-right">Aksi</th>
                </tr>
            </thead>
            <tbody>
                @foreach($jenis as $j)
                <tr class="{{ ! $j->is_active ? 'opacity-60' : '' }}">
                    <td>
                        <span class="font-mono text-xs font-bold text-slate-500 bg-slate-100 px-2 py-1 rounded-lg">
                            {{ $j->kode }}
                        </span>
                    </td>
                    <td>
                        <p class="font-semibold text-slate-700 text-sm">{{ $j->nama }}</p>
                    </td>
                    <td>
                        <span class="{{ $j->badge_class }}">{{ $j->kategori_label }}</span>
                    </td>
                    <td class="text-center">
                        <span class="font-bold text-sm
                            {{ $j->poin >= 50 ? 'text-danger' : ($j->poin >= 20 ? 'text-warning' : 'text-success') }}">
                            {{ $j->poin }}
                        </span>
                    </td>
                    <td class="text-xs text-slate-500">
                        {{ $j->keterangan ?: '—' }}
                    </td>
                    <td class="text-center">
                        <span class="badge bg-slate-100 text-slate-600">
                            {{ number_format($j->pelanggaran_count) }}×
                        </span>
                    </td>
                    <td class="text-center">
                        @if($j->is_active)
                        <span class="badge bg-emerald-100 text-emerald-700">Aktif</span>
                        @else
                        <span class="badge bg-slate-100 text-slate-500">Nonaktif</span>
                        @endif
                    </td>
                    <td class="text-right">
                        <div class="flex items-center justify-end gap-1">
                            <a href="{{ route('admin.jenis-pelanggaran.edit', $j) }}"
                               class="p-1.5 text-slate-400 hover:text-amber-600 hover:bg-amber-50 rounded-lg transition-colors">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                        d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/>
                                </svg>
                            </a>

                            <form action="{{ route('admin.jenis-pelanggaran.toggle-status', $j) }}" method="POST">
                                @csrf @method('PATCH')
                                <button type="submit"
                                    class="p-1.5 rounded-lg transition-colors
                                           {{ $j->is_active ? 'text-slate-400 hover:text-warning hover:bg-amber-50' : 'text-slate-400 hover:text-success hover:bg-emerald-50' }}"
                                    title="{{ $j->is_active ? 'Nonaktifkan' : 'Aktifkan' }}">
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        @if($j->is_active)
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                            d="M18.364 18.364A9 9 0 005.636 5.636m12.728 12.728A9 9 0 015.636 5.636m12.728 12.728L5.636 5.636"/>
                                        @else
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                            d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/>
                                        @endif
                                    </svg>
                                </button>
                            </form>

                            @if($j->pelanggaran_count === 0)
                            <button
                                onclick="if(confirm('Hapus jenis pelanggaran \'{{ addslashes($j->nama) }}\'?')) document.getElementById('del-jp-{{ $j->id }}').submit()"
                                class="p-1.5 text-slate-400 hover:text-danger hover:bg-red-50 rounded-lg transition-colors">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                        d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/>
                                </svg>
                            </button>
                            <form id="del-jp-{{ $j->id }}"
                                  action="{{ route('admin.jenis-pelanggaran.destroy', $j) }}"
                                  method="POST" class="hidden">
                                @csrf @method('DELETE')
                            </form>
                            @endif
                        </div>
                    </td>
                </tr>
                @endforeach
            </tbody>
        </table>
    </div>
    @if($jenis->hasPages())
    <div class="px-4 py-4 border-t border-slate-100">
        {{ $jenis->links() }}
    </div>
    @endif
    @endif
</div>

@endsection
