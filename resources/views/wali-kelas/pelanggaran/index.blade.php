@extends('layouts.app')

@section('title', 'Input Pelanggaran')
@section('page-title', 'Pelanggaran Saya')

@section('breadcrumb')
<span class="text-slate-400">/</span>
<span class="text-slate-500">Pelanggaran</span>
@endsection

@section('content')

{{-- Stat Cards --}}
<div class="grid grid-cols-3 gap-4 mb-6">
    <div class="stat-card">
        <div class="stat-icon bg-amber-50">
            <svg class="w-6 h-6 text-accent" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                    d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/>
            </svg>
        </div>
        <div>
            <p class="text-xs font-semibold text-slate-500 uppercase tracking-wide">Menunggu BK</p>
            <p class="font-heading text-2xl font-bold text-slate-800">{{ $stats['menunggu'] }}</p>
        </div>
    </div>
    <div class="stat-card">
        <div class="stat-icon bg-emerald-50">
            <svg class="w-6 h-6 text-success" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                    d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/>
            </svg>
        </div>
        <div>
            <p class="text-xs font-semibold text-slate-500 uppercase tracking-wide">Dikonfirmasi</p>
            <p class="font-heading text-2xl font-bold text-slate-800">{{ $stats['dikonfirmasi'] }}</p>
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
            <p class="text-xs font-semibold text-slate-500 uppercase tracking-wide">Bulan Ini</p>
            <p class="font-heading text-2xl font-bold text-slate-800">{{ $stats['bulan_ini'] }}</p>
        </div>
    </div>
</div>

{{-- Header + Tombol Tambah --}}
<div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4 mb-5">
    <p class="text-sm text-slate-500">
        Total <span class="font-semibold text-slate-700">{{ $pelanggaran->total() }}</span> catatan pelanggaran
    </p>
    <a href="{{ route('wali-kelas.pelanggaran.create') }}" class="btn-primary">
        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/>
        </svg>
        Input Pelanggaran Baru
    </a>
</div>

{{-- Filter --}}
<div class="card mb-5">
    <form action="{{ route('wali-kelas.pelanggaran.index') }}" method="GET"
          class="flex flex-wrap gap-3">
        <div class="flex-1 min-w-[180px] relative">
            <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                <svg class="w-4 h-4 text-slate-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                        d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/>
                </svg>
            </div>
            <input type="text" name="cari" value="{{ request('cari') }}"
                   placeholder="Cari nama siswa..." class="form-input pl-9">
        </div>

        <select name="status" class="form-input w-auto">
            <option value="">Semua Status</option>
            <option value="menunggu"     {{ request('status') == 'menunggu'     ? 'selected' : '' }}>Menunggu</option>
            <option value="dikonfirmasi" {{ request('status') == 'dikonfirmasi' ? 'selected' : '' }}>Dikonfirmasi</option>
            <option value="ditolak"      {{ request('status') == 'ditolak'      ? 'selected' : '' }}>Ditolak</option>
        </select>

        <input type="month" name="bulan" value="{{ request('bulan') }}"
               class="form-input w-auto">

        <button type="submit" class="btn-primary">Filter</button>
        @if(request()->hasAny(['cari','status','bulan']))
        <a href="{{ route('wali-kelas.pelanggaran.index') }}" class="btn-secondary">Reset</a>
        @endif
    </form>
</div>

{{-- Tabel --}}
<div class="card p-0 overflow-hidden">
    @if($pelanggaran->isEmpty())
    <div class="empty-state py-16">
        <div class="empty-state-icon">
            <svg class="w-8 h-8 text-slate-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                    d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2"/>
            </svg>
        </div>
        <p class="text-sm font-medium text-slate-600">Belum ada catatan pelanggaran</p>
        <p class="text-xs text-slate-400 mt-1 mb-4">Mulai catat pelanggaran siswa di kelas Anda</p>
        <a href="{{ route('wali-kelas.pelanggaran.create') }}" class="btn-primary">
            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/>
            </svg>
            Input Pertama
        </a>
    </div>
    @else
    <div class="overflow-x-auto">
        <table class="w-full table-simon">
            <thead>
                <tr>
                    <th>Siswa</th>
                    <th>Jenis Pelanggaran</th>
                    <th>Tanggal</th>
                    <th class="text-center">Poin</th>
                    <th>Status</th>
                    <th class="text-right">Aksi</th>
                </tr>
            </thead>
            <tbody>
                @foreach($pelanggaran as $p)
                <tr>
                    <td>
                        <div class="flex items-center gap-2.5">
                            <img src="{{ $p->siswa->foto_url }}" alt="{{ $p->siswa->nama }}"
                                 class="w-8 h-8 rounded-lg object-cover flex-shrink-0">
                            <div>
                                <p class="font-semibold text-slate-700 text-sm">{{ $p->siswa->nama }}</p>
                                <p class="text-xs text-slate-400">{{ $p->siswa->nis }}</p>
                            </div>
                        </div>
                    </td>
                    <td>
                        <p class="text-sm text-slate-700">{{ Str::limit($p->jenisPelanggaran->nama, 40) }}</p>
                        <span class="{{ $p->jenisPelanggaran->badge_class }} text-[10px] mt-0.5">
                            {{ $p->jenisPelanggaran->kategori_label }}
                        </span>
                    </td>
                    <td class="whitespace-nowrap text-sm text-slate-600">
                        {{ $p->tanggal_pelanggaran->isoFormat('D MMM Y') }}
                    </td>
                    <td class="text-center">
                        <span class="font-bold text-sm
                            {{ $p->poin_diberikan >= 50 ? 'text-danger' : ($p->poin_diberikan >= 20 ? 'text-warning' : 'text-slate-600') }}">
                            +{{ $p->poin_diberikan }}
                        </span>
                    </td>
                    <td>
                        <span class="{{ $p->status_badge_class }}">{{ $p->status_label }}</span>
                        @if($p->status === 'ditolak' && $p->alasan_tolak)
                        <p class="text-[10px] text-danger mt-0.5">{{ Str::limit($p->alasan_tolak, 30) }}</p>
                        @endif
                    </td>
                    <td class="text-right">
                        <div class="flex items-center justify-end gap-1">
                            <a href="{{ route('wali-kelas.pelanggaran.show', $p) }}"
                               class="p-1.5 text-slate-400 hover:text-secondary hover:bg-green-50 rounded-lg transition-colors">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                        d="M15 12a3 3 0 11-6 0 3 3 0 016 0z M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/>
                                </svg>
                            </a>

                            @if($p->status === 'menunggu')
                            <a href="{{ route('wali-kelas.pelanggaran.edit', $p) }}"
                               class="p-1.5 text-slate-400 hover:text-amber-600 hover:bg-amber-50 rounded-lg transition-colors">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                        d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/>
                                </svg>
                            </a>
                            <button onclick="if(confirm('Hapus catatan pelanggaran ini?')) document.getElementById('del-{{ $p->id }}').submit()"
                                class="p-1.5 text-slate-400 hover:text-danger hover:bg-red-50 rounded-lg transition-colors">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                        d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/>
                                </svg>
                            </button>
                            <form id="del-{{ $p->id }}"
                                  action="{{ route('wali-kelas.pelanggaran.destroy', $p) }}"
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
    @if($pelanggaran->hasPages())
    <div class="px-4 py-4 border-t border-slate-100">
        {{ $pelanggaran->links() }}
    </div>
    @endif
    @endif
</div>

@endsection
