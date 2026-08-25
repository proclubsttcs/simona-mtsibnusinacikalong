@extends('layouts.app')

@section('title', 'Manajemen Akun')
@section('page-title', 'Manajemen Akun')

@section('breadcrumb')
<span class="text-slate-400">/</span>
<span class="text-slate-500">Akun Pengguna</span>
@endsection

@section('content')

{{-- Header --}}
<div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4 mb-6">
    <p class="text-sm text-slate-500">Total <span class="font-semibold text-slate-700">{{ $users->total() }}</span> akun</p>
    <a href="{{ route('admin.users.create') }}" class="btn-primary">
        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                d="M18 9v3m0 0v3m0-3h3m-3 0h-3m-2-5a4 4 0 11-8 0 4 4 0 018 0zM3 20a6 6 0 0112 0v1H3v-1z"/>
        </svg>
        Tambah Akun
    </a>
</div>

{{-- Filter --}}
<div class="card mb-6">
    <form action="{{ route('admin.users.index') }}" method="GET"
          class="flex flex-wrap gap-3">
        <div class="flex-1 min-w-[200px] relative">
            <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                <svg class="w-4 h-4 text-slate-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                        d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/>
                </svg>
            </div>
            <input type="text" name="cari" value="{{ request('cari') }}"
                   placeholder="Cari nama atau email..."
                   class="form-input pl-9">
        </div>
        <select name="role" class="form-input w-auto">
            <option value="">Semua Role</option>
            <option value="admin" {{ request('role') == 'admin' ? 'selected' : '' }}>Admin / BK</option>
            <option value="wali_kelas" {{ request('role') == 'wali_kelas' ? 'selected' : '' }}>Wali Kelas</option>
        </select>
        <button type="submit" class="btn-primary">Filter</button>
        @if(request()->hasAny(['cari','role']))
        <a href="{{ route('admin.users.index') }}" class="btn-secondary">Reset</a>
        @endif
    </form>
</div>

{{-- Tabel --}}
<div class="card p-0 overflow-hidden">
    @if($users->isEmpty())
    <div class="empty-state py-16">
        <div class="empty-state-icon">
            <svg class="w-8 h-8 text-slate-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                    d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"/>
            </svg>
        </div>
        <p class="text-sm font-medium text-slate-600">Tidak ada akun ditemukan</p>
    </div>
    @else
    <div class="overflow-x-auto">
        <table class="w-full table-simon">
            <thead>
                <tr>
                    <th>Pengguna</th>
                    <th>Role</th>
                    <th>Kelas</th>
                    <th class="text-center">Status</th>
                    <th>Bergabung</th>
                    <th class="text-right">Aksi</th>
                </tr>
            </thead>
            <tbody>
                @foreach($users as $user)
                <tr>
                    <td>
                        <div class="flex items-center gap-3">
                            <img src="{{ $user->foto_url }}" alt="{{ $user->name }}"
                                 class="w-9 h-9 rounded-xl object-cover flex-shrink-0">
                            <div>
                                <div class="flex items-center gap-1.5">
                                    <p class="font-semibold text-slate-700">{{ $user->name }}</p>
                                    @if($user->id === auth()->id())
                                    <span class="badge bg-secondary/10 text-secondary text-[10px]">Saya</span>
                                    @endif
                                    @if($user->must_change_password)
                                    <span class="badge bg-amber-50 text-amber-600 text-[10px]" title="Belum ganti password">⚠</span>
                                    @endif
                                </div>
                                <p class="text-xs text-slate-400">{{ $user->email }}</p>
                            </div>
                        </div>
                    </td>
                    <td>
                        <span class="badge {{ $user->isAdmin() ? 'bg-purple-50 text-purple-700' : 'bg-green-50 text-green-700' }}">
                            {{ $user->role_label }}
                        </span>
                    </td>
                    <td>
                        @if($user->kelas)
                        <span class="badge bg-slate-100 text-slate-600">{{ $user->kelas }}</span>
                        @else
                        <span class="text-slate-300">—</span>
                        @endif
                    </td>
                    <td class="text-center">
                        @if($user->is_active)
                        <span class="badge bg-emerald-100 text-emerald-700">Aktif</span>
                        @else
                        <span class="badge bg-slate-100 text-slate-500">Nonaktif</span>
                        @endif
                    </td>
                    <td>
                        <p class="text-xs text-slate-500">
                            {{ $user->created_at->isoFormat('D MMM Y') }}
                        </p>
                    </td>
                    <td class="text-right">
                        <div class="flex items-center justify-end gap-1">
                            {{-- Lihat --}}
                            <a href="{{ route('admin.users.show', $user) }}"
                               class="p-1.5 text-slate-400 hover:text-secondary hover:bg-green-50 rounded-lg transition-colors"
                               title="Lihat Detail">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                        d="M15 12a3 3 0 11-6 0 3 3 0 016 0z M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/>
                                </svg>
                            </a>
                            {{-- Edit --}}
                            <a href="{{ route('admin.users.edit', $user) }}"
                               class="p-1.5 text-slate-400 hover:text-amber-600 hover:bg-amber-50 rounded-lg transition-colors">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                        d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/>
                                </svg>
                            </a>
                            {{-- Hapus --}}
                            <button
                                onclick="if(confirm('Hapus akun {{ addslashes($user->name) }}?')) document.getElementById('form-del-{{ $user->id }}').submit()"
                                class="p-1.5 text-slate-400 hover:text-danger hover:bg-red-50 rounded-lg transition-colors">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                        d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/>
                                </svg>
                            </button>
                            <form id="form-del-{{ $user->id }}"
                                  action="{{ route('admin.users.destroy', $user) }}"
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
    @if($users->hasPages())
    <div class="px-4 py-4 border-t border-slate-100">
        {{ $users->links() }}
    </div>
    @endif
    @endif
</div>
@endsection
