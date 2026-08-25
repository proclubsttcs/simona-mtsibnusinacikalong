@extends('layouts.app')

@section('title', 'Detail Pelanggaran')
@section('page-title', 'Detail Pelanggaran')

@section('breadcrumb')
<span class="text-slate-400">/</span>
<a href="{{ route('wali-kelas.pelanggaran.index') }}" class="hover:text-secondary">Pelanggaran</a>
<span class="text-slate-400">/</span>
<span class="text-slate-500">Detail</span>
@endsection

@section('content')
<div class="max-w-2xl mx-auto space-y-5">

    {{-- Status Banner --}}
    @if($pelanggaran->status === 'menunggu')
    <div class="p-4 bg-amber-50 border border-amber-200 rounded-2xl flex items-center gap-3">
        <svg class="w-5 h-5 text-amber-500 animate-pulse-soft flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/>
        </svg>
        <p class="text-sm text-amber-700 font-medium">Menunggu konfirmasi dari Guru BK</p>
    </div>
    @elseif($pelanggaran->status === 'dikonfirmasi')
    <div class="p-4 bg-emerald-50 border border-emerald-200 rounded-2xl flex items-center gap-3">
        <svg class="w-5 h-5 text-success flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/>
        </svg>
        <div>
            <p class="text-sm text-emerald-700 font-semibold">Dikonfirmasi — Poin sudah dihitung</p>
            @if($pelanggaran->konfirmasiOleh)
            <p class="text-xs text-emerald-600 mt-0.5">
                oleh {{ $pelanggaran->konfirmasiOleh->name }}
                · {{ $pelanggaran->dikonfirmasi_at->isoFormat('D MMM Y, HH:mm') }}
            </p>
            @endif
        </div>
    </div>
    @else
    <div class="p-4 bg-red-50 border border-red-200 rounded-2xl">
        <div class="flex items-start gap-3">
            <svg class="w-5 h-5 text-danger flex-shrink-0 mt-0.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                    d="M6 18L18 6M6 6l12 12"/>
            </svg>
            <div>
                <p class="text-sm text-red-700 font-semibold">Ditolak oleh BK</p>
                <p class="text-sm text-red-600 mt-0.5">{{ $pelanggaran->alasan_tolak }}</p>
                @if($pelanggaran->konfirmasiOleh)
                <p class="text-xs text-red-400 mt-1">
                    oleh {{ $pelanggaran->konfirmasiOleh->name }}
                    · {{ $pelanggaran->dikonfirmasi_at->isoFormat('D MMM Y, HH:mm') }}
                </p>
                @endif
            </div>
        </div>
    </div>
    @endif

    {{-- Detail Pelanggaran --}}
    <div class="card">
        <h3 class="font-heading font-bold text-slate-800 mb-5">Rincian Pelanggaran</h3>
        <div class="grid grid-cols-2 gap-4">
            <div>
                <p class="text-xs text-slate-400 uppercase tracking-wide font-medium">Siswa</p>
                <div class="flex items-center gap-2 mt-1.5">
                    <img src="{{ $pelanggaran->siswa->foto_url }}" class="w-8 h-8 rounded-lg object-cover">
                    <div>
                        <p class="text-sm font-semibold text-slate-700">{{ $pelanggaran->siswa->nama }}</p>
                        <p class="text-xs text-slate-400">{{ $pelanggaran->siswa->nis }}</p>
                    </div>
                </div>
            </div>
            <div>
                <p class="text-xs text-slate-400 uppercase tracking-wide font-medium">Tanggal Kejadian</p>
                <p class="text-sm font-semibold text-slate-700 mt-1.5">
                    {{ $pelanggaran->tanggal_pelanggaran->isoFormat('dddd, D MMMM Y') }}
                </p>
            </div>
            <div class="col-span-2">
                <p class="text-xs text-slate-400 uppercase tracking-wide font-medium">Jenis Pelanggaran</p>
                <div class="flex items-center gap-2 mt-1.5">
                    <span class="{{ $pelanggaran->jenisPelanggaran->badge_class }}">
                        {{ $pelanggaran->jenisPelanggaran->kategori_label }}
                    </span>
                    <p class="text-sm font-semibold text-slate-700">
                        {{ $pelanggaran->jenisPelanggaran->nama }}
                    </p>
                </div>
                @if($pelanggaran->jenisPelanggaran->keterangan)
                <p class="text-xs text-slate-400 mt-1">{{ $pelanggaran->jenisPelanggaran->keterangan }}</p>
                @endif
            </div>
            <div>
                <p class="text-xs text-slate-400 uppercase tracking-wide font-medium">Poin Default</p>
                <p class="text-sm font-semibold text-slate-700 mt-1.5">
                    {{ $pelanggaran->jenisPelanggaran->poin }} poin
                </p>
            </div>
            <div>
                <p class="text-xs text-slate-400 uppercase tracking-wide font-medium">Poin Diberikan</p>
                <p class="text-xl font-bold mt-1.5
                    {{ $pelanggaran->poin_diberikan >= 50 ? 'text-danger' : ($pelanggaran->poin_diberikan >= 20 ? 'text-warning' : 'text-slate-700') }}">
                    +{{ $pelanggaran->poin_diberikan }} poin
                </p>
            </div>
            @if($pelanggaran->keterangan)
            <div class="col-span-2">
                <p class="text-xs text-slate-400 uppercase tracking-wide font-medium">Keterangan</p>
                <p class="text-sm text-slate-600 mt-1.5 leading-relaxed">{{ $pelanggaran->keterangan }}</p>
            </div>
            @endif
        </div>
    </div>

    {{-- Aksi --}}
    @if($pelanggaran->status === 'menunggu')
    <div class="flex gap-3">
        <a href="{{ route('wali-kelas.pelanggaran.edit', $pelanggaran) }}" class="btn-secondary flex-1 justify-center">
            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                    d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/>
            </svg>
            Edit
        </a>
        <button onclick="if(confirm('Hapus catatan ini?')) document.getElementById('form-del').submit()"
                class="btn-danger flex-1 justify-center">
            Hapus
        </button>
        <form id="form-del" action="{{ route('wali-kelas.pelanggaran.destroy', $pelanggaran) }}"
              method="POST" class="hidden">
            @csrf @method('DELETE')
        </form>
    </div>
    @endif

    <a href="{{ route('wali-kelas.pelanggaran.index') }}" class="btn-secondary w-full justify-center">
        ← Kembali ke Daftar
    </a>
</div>
@endsection
