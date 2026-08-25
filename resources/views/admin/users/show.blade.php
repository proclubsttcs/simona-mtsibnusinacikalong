@extends('layouts.app')

@section('title', $user->name)
@section('page-title', 'Detail Akun')

@section('breadcrumb')
<span class="text-slate-400">/</span>
<a href="{{ route('admin.users.index') }}" class="hover:text-secondary transition-colors">Akun Pengguna</a>
<span class="text-slate-400">/</span>
<span class="text-slate-500 truncate max-w-[150px]">{{ $user->name }}</span>
@endsection

@section('content')
<div class="grid grid-cols-1 lg:grid-cols-3 gap-6">

    {{-- ── Kolom Kiri: Profil ──────────────────────────────────── --}}
    <div class="lg:col-span-1 space-y-5">
        <div class="card text-center">
            <img src="{{ $user->foto_url }}" alt="{{ $user->name }}"
                 class="w-28 h-28 rounded-2xl object-cover mx-auto mb-4 border-2 border-slate-100">

            <h2 class="font-heading text-xl font-bold text-slate-800">{{ $user->name }}</h2>
            <p class="text-slate-500 text-sm mt-0.5">{{ $user->email }}</p>

            <div class="flex items-center justify-center gap-2 mt-3">
                <span class="badge {{ $user->isAdmin() ? 'bg-purple-50 text-purple-700' : 'bg-green-50 text-green-700' }}">
                    {{ $user->role_label }}
                </span>
                @if($user->kelas)
                <span class="badge bg-slate-100 text-slate-600">{{ $user->kelas }}</span>
                @endif
                @if($user->is_active)
                <span class="badge bg-emerald-100 text-emerald-700">Aktif</span>
                @else
                <span class="badge bg-red-100 text-red-600">Nonaktif</span>
                @endif
            </div>

            @if($user->must_change_password)
            <div class="mt-3 p-2.5 bg-amber-50 rounded-xl border border-amber-200">
                <p class="text-xs text-amber-700 font-medium">⚠ Belum mengganti password default</p>
            </div>
            @endif

            <div class="divider"></div>

            <div class="text-left space-y-2 text-sm">
                <div class="flex justify-between">
                    <span class="text-slate-500">Bergabung</span>
                    <span class="font-medium text-slate-700">{{ $user->created_at->isoFormat('D MMM Y') }}</span>
                </div>
                <div class="flex justify-between">
                    <span class="text-slate-500">Login Terakhir</span>
                    <span class="font-medium text-slate-700">{{ $user->updated_at->diffForHumans() }}</span>
                </div>
            </div>

            <div class="divider"></div>

            <div class="flex gap-2">
                <a href="{{ route('admin.users.edit', $user) }}" class="btn-secondary flex-1 justify-center text-xs">
                    <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/>
                    </svg>
                    Edit
                </a>
            </div>
        </div>
    </div>

    {{-- ── Kolom Kanan: Statistik & Daftar Siswa ──────────────── --}}
    <div class="lg:col-span-2 space-y-6">

        @if($user->isWaliKelas())
        {{-- Stat Cards --}}
        <div class="grid grid-cols-3 gap-4">
            <div class="card text-center p-4">
                <p class="font-heading text-2xl font-bold text-secondary">{{ $stats['total_siswa'] }}</p>
                <p class="text-xs text-slate-500 mt-1">Total Siswa</p>
            </div>
            <div class="card text-center p-4">
                <p class="font-heading text-2xl font-bold text-danger">{{ $stats['siswa_sp'] }}</p>
                <p class="text-xs text-slate-500 mt-1">Siswa SP</p>
            </div>
            <div class="card text-center p-4">
                <p class="font-heading text-2xl font-bold text-warning">{{ $stats['pelanggaran_bln'] }}</p>
                <p class="text-xs text-slate-500 mt-1">Input Bulan Ini</p>
            </div>
        </div>

        {{-- Daftar Siswa yang Diampu --}}
        <div class="card">
            <h3 class="font-heading font-bold text-slate-800 mb-4">
                Siswa Kelas {{ $user->kelas }}
            </h3>

            @if($user->siswa->isEmpty())
            <div class="empty-state py-10">
                <div class="empty-state-icon">
                    <svg class="w-7 h-7 text-slate-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0z"/>
                    </svg>
                </div>
                <p class="text-sm text-slate-500">Belum ada siswa yang diampu</p>
            </div>
            @else
            <div class="overflow-x-auto">
                <table class="w-full table-simon">
                    <thead>
                        <tr>
                            <th>Siswa</th>
                            <th>NIS</th>
                            <th>Poin</th>
                            <th>Status</th>
                            <th class="text-right">Aksi</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($user->siswa->sortBy('nama') as $s)
                        <tr>
                            <td>
                                <div class="flex items-center gap-2.5">
                                    <img src="{{ $s->foto_url }}" alt="{{ $s->nama }}"
                                         class="w-8 h-8 rounded-lg object-cover">
                                    <p class="font-medium text-slate-700">{{ $s->nama }}</p>
                                </div>
                            </td>
                            <td><span class="font-mono text-xs text-slate-500">{{ $s->nis }}</span></td>
                            <td>
                                <div class="flex items-center gap-2 min-w-[80px]">
                                    <div class="flex-1 progress-bar-wrap">
                                        <div class="{{ $s->progress_color }} progress-bar-fill"
                                             style="width: {{ $s->progress_persen }}%"></div>
                                    </div>
                                    <span class="text-xs font-bold text-slate-600">{{ $s->total_poin }}</span>
                                </div>
                            </td>
                            <td>
                                <span class="{{ $s->rekapPoin?->badge_class ?? 'badge-aman' }}">
                                    {{ $s->status_sp }}
                                </span>
                            </td>
                            <td class="text-right">
                                <a href="{{ route('admin.siswa.show', $s) }}"
                                   class="text-xs text-secondary hover:text-secondary-700 font-medium">
                                    Detail →
                                </a>
                            </td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
            @endif
        </div>
        @else
        {{-- Admin tidak punya siswa --}}
        <div class="card">
            <div class="empty-state py-12">
                <div class="empty-state-icon">
                    <svg class="w-8 h-8 text-purple-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z"/>
                    </svg>
                </div>
                <p class="text-sm font-medium text-slate-600">Akun Administrator</p>
                <p class="text-xs text-slate-400 mt-1">Admin memiliki akses penuh ke semua data</p>
            </div>
        </div>
        @endif
    </div>
</div>
@endsection
