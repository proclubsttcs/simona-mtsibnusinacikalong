@extends('layouts.app')

@section('title', 'Data Siswa')
@section('page-title', 'Data Siswa')

@section('breadcrumb')
<span class="text-slate-400">/</span>
<span class="text-slate-500">Data Siswa</span>
@endsection

@section('content')

{{-- ── Header & Tombol Tambah ───────────────────────────────────── --}}
<div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4 mb-6">
    <div>
        <p class="text-sm text-slate-500">Total <span class="font-semibold text-slate-700">{{ $siswa->total() }}</span> siswa ditemukan</p>
    </div>
    <a href="{{ route('admin.siswa.create') }}" class="btn-primary">
        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/>
        </svg>
        Tambah Siswa
    </a>
</div>

{{-- ── Filter & Search ─────────────────────────────────────────── --}}
<div class="card mb-6">
    <form action="{{ route('admin.siswa.index') }}" method="GET"
          class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-5 gap-3">

        {{-- Pencarian --}}
        <div class="lg:col-span-2 relative">
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

        {{-- Filter Kelas --}}
        <select name="kelas" class="form-input">
            <option value="">Semua Kelas</option>
            @foreach($kelasList as $kelas)
            <option value="{{ $kelas }}" {{ request('kelas') == $kelas ? 'selected' : '' }}>
                Kelas {{ $kelas }}
            </option>
            @endforeach
        </select>

        {{-- Filter Status SP --}}
        <select name="status_sp" class="form-input">
            <option value="">Semua Status</option>
            <option value="aman" {{ request('status_sp') == 'aman' ? 'selected' : '' }}>Aman</option>
            <option value="SP1"  {{ request('status_sp') == 'SP1'  ? 'selected' : '' }}>SP1</option>
            <option value="SP2"  {{ request('status_sp') == 'SP2'  ? 'selected' : '' }}>SP2</option>
            <option value="SP3"  {{ request('status_sp') == 'SP3'  ? 'selected' : '' }}>SP3</option>
        </select>

        {{-- Tombol filter --}}
        <div class="flex gap-2">
            <button type="submit" class="btn-primary flex-1 justify-center">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                        d="M3 4a1 1 0 011-1h16a1 1 0 011 1v2.586a1 1 0 01-.293.707l-6.414 6.414a1 1 0 00-.293.707V17l-4 4v-6.586a1 1 0 00-.293-.707L3.293 7.293A1 1 0 013 6.586V4z"/>
                </svg>
                Filter
            </button>
            @if(request()->hasAny(['cari','kelas','status_sp','jenis_kelamin']))
            <a href="{{ route('admin.siswa.index') }}" class="btn-secondary px-3">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
                </svg>
            </a>
            @endif
        </div>
    </form>
</div>

{{-- ── Tabel Data Siswa ─────────────────────────────────────────── --}}
<div class="card p-0 overflow-hidden">
    @if($siswa->isEmpty())
    <div class="empty-state py-16">
        <div class="empty-state-icon">
            <svg class="w-8 h-8 text-slate-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                    d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0z"/>
            </svg>
        </div>
        <p class="text-sm font-medium text-slate-600">Tidak ada data siswa</p>
        <p class="text-xs text-slate-400 mt-1 mb-4">Coba ubah filter atau tambah siswa baru</p>
        <a href="{{ route('admin.siswa.create') }}" class="btn-primary">
            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/>
            </svg>
            Tambah Siswa
        </a>
    </div>
    @else
    <div class="overflow-x-auto">
        <table class="w-full table-simon">
            <thead>
                <tr>
                    <th>Siswa</th>
                    <th>NIS</th>
                    <th>Kelas</th>
                    <th>Wali Kelas</th>
                    <th>Poin</th>
                    <th>Status</th>
                    <th class="text-right">Aksi</th>
                </tr>
            </thead>
            <tbody>
                @foreach($siswa as $s)
                <tr>
                    {{-- Nama & Foto --}}
                    <td>
                        <div class="flex items-center gap-3">
                            <img src="{{ $s->foto_url }}" alt="{{ $s->nama }}"
                                 class="w-9 h-9 rounded-xl object-cover flex-shrink-0">
                            <div>
                                <p class="font-semibold text-slate-700">{{ $s->nama }}</p>
                                <p class="text-xs text-slate-400">{{ $s->jenis_kelamin_label }}</p>
                            </div>
                        </div>
                    </td>

                    {{-- NIS --}}
                    <td>
                        <span class="font-mono text-sm text-slate-600">{{ $s->nis }}</span>
                    </td>

                    {{-- Kelas --}}
                    <td>
                        <span class="badge bg-green-50 text-green-700">{{ $s->kelas }}</span>
                    </td>

                    {{-- Wali Kelas --}}
                    <td>
                        <p class="text-sm text-slate-600">{{ Str::words($s->waliKelas?->name, 2, '...') }}</p>
                    </td>

                    {{-- Progress Poin --}}
                    <td>
                        <div class="flex items-center gap-2 min-w-[100px]">
                            <div class="flex-1 progress-bar-wrap">
                                <div class="{{ $s->progress_color }} progress-bar-fill"
                                     style="width: {{ $s->progress_persen }}%"></div>
                            </div>
                            <span class="text-sm font-bold text-slate-600 w-8 text-right">{{ $s->total_poin }}</span>
                        </div>
                    </td>

                    {{-- Status SP --}}
                    <td>
                        <span class="{{ $s->rekapPoin?->badge_class ?? 'badge-aman' }}">
                            {{ $s->status_sp }}
                        </span>
                    </td>

                    {{-- Aksi --}}
                    <td class="text-right">
                        <div class="flex items-center justify-end gap-1">
                            {{-- Lihat Detail --}}
                            <a href="{{ route('admin.siswa.show', $s) }}"
                               class="p-1.5 text-slate-400 hover:text-secondary hover:bg-green-50 rounded-lg transition-colors"
                               title="Lihat Detail">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                        d="M15 12a3 3 0 11-6 0 3 3 0 016 0z M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/>
                                </svg>
                            </a>

                            {{-- Edit --}}
                            <a href="{{ route('admin.siswa.edit', $s) }}"
                               class="p-1.5 text-slate-400 hover:text-amber-600 hover:bg-amber-50 rounded-lg transition-colors"
                               title="Edit">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                        d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/>
                                </svg>
                            </a>

                            {{-- Hapus --}}
                            <button
                                onclick="konfirmasiHapus('{{ $s->id }}', '{{ $s->nama }}')"
                                class="p-1.5 text-slate-400 hover:text-danger hover:bg-red-50 rounded-lg transition-colors"
                                title="Nonaktifkan">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                        d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/>
                                </svg>
                            </button>

                            {{-- Hidden form hapus --}}
                            <form id="form-hapus-{{ $s->id }}"
                                  action="{{ route('admin.siswa.destroy', $s) }}"
                                  method="POST" class="hidden">
                                @csrf
                                @method('DELETE')
                            </form>
                        </div>
                    </td>
                </tr>
                @endforeach
            </tbody>
        </table>
    </div>

    {{-- Pagination --}}
    @if($siswa->hasPages())
    <div class="px-4 py-4 border-t border-slate-100">
        {{ $siswa->links() }}
    </div>
    @endif
    @endif
</div>

@endsection

@push('scripts')
<script>
function konfirmasiHapus(id, nama) {
    if (confirm(`Nonaktifkan siswa "${nama}"?\n\nData tidak akan hilang permanen.`)) {
        document.getElementById('form-hapus-' + id).submit();
    }
}
</script>
@endpush
