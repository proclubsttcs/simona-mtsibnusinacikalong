@extends('layouts.app')

@section('title', $suratPeringatan->jenis_sp . ' — ' . $suratPeringatan->siswa->nama)
@section('page-title', 'Detail Surat Peringatan')

@section('breadcrumb')
<span class="text-slate-400">/</span>
<a href="{{ route('admin.surat-peringatan.index') }}" class="hover:text-secondary transition-colors">Surat Peringatan</a>
<span class="text-slate-400">/</span>
<span class="text-slate-500">{{ $suratPeringatan->jenis_sp }} — {{ $suratPeringatan->siswa->nama }}</span>
@endsection

@section('content')
<div class="grid grid-cols-1 lg:grid-cols-3 gap-6">

    {{-- ── Kolom Kiri: Info SP & Aksi ──────────────────────────── --}}
    <div class="lg:col-span-1 space-y-5">

        {{-- Card SP --}}
        <div class="card text-center overflow-hidden">
            {{-- Header gradient berdasarkan jenis SP --}}
            <div class="rounded-xl mb-4 py-6
                {{ $suratPeringatan->jenis_sp === 'SP1' ? 'bg-gradient-to-br from-amber-400 to-amber-600'
                 : ($suratPeringatan->jenis_sp === 'SP2' ? 'bg-gradient-to-br from-orange-500 to-orange-700'
                 : 'bg-gradient-to-br from-red-500 to-red-700') }}">
                <p class="text-white/80 text-sm font-medium">Surat Peringatan</p>
                <p class="text-white font-heading text-5xl font-extrabold mt-1">{{ $suratPeringatan->jenis_sp }}</p>
                <p class="text-white/70 text-xs mt-2">{{ $suratPeringatan->tanggal_terbit->isoFormat('D MMMM Y') }}</p>
            </div>

            {{-- Status --}}
            <div class="flex justify-center mb-4">
                @if($suratPeringatan->status === 'aktif')
                <span class="badge bg-red-100 text-red-700 text-sm px-4 py-1.5">🔴 Aktif</span>
                @else
                <span class="badge bg-slate-100 text-slate-500 text-sm px-4 py-1.5">✅ Selesai</span>
                @endif
            </div>

            {{-- Poin --}}
            <div class="p-3 rounded-xl mb-4
                {{ $suratPeringatan->jenis_sp === 'SP1' ? 'bg-amber-50 border border-amber-200'
                 : ($suratPeringatan->jenis_sp === 'SP2' ? 'bg-orange-50 border border-orange-200'
                 : 'bg-red-50 border border-red-200') }}">
                <p class="text-xs text-slate-500 font-medium">Poin Saat Diterbitkan</p>
                <p class="font-heading text-3xl font-extrabold
                    {{ $suratPeringatan->jenis_sp === 'SP1' ? 'text-amber-600'
                     : ($suratPeringatan->jenis_sp === 'SP2' ? 'text-orange-600' : 'text-red-600') }}">
                    {{ $suratPeringatan->total_poin_saat_ini }}
                </p>
                <p class="text-xs text-slate-400 mt-0.5">poin pelanggaran</p>
            </div>

            {{-- Info --}}
            <div class="text-left space-y-2 text-sm mb-4">
                <div class="flex justify-between">
                    <span class="text-slate-500">Nomor Surat</span>
                    <span class="font-mono text-xs text-slate-600 text-right max-w-[150px] truncate">
                        {{ $suratPeringatan->jenis_sp }}/MTs-IS/{{ now()->month }}/{{ $suratPeringatan->tanggal_terbit->year }}/{{ $suratPeringatan->id }}
                    </span>
                </div>
                <div class="flex justify-between">
                    <span class="text-slate-500">Diterbitkan Oleh</span>
                    <span class="font-medium text-slate-700">
                        {{ Str::words($suratPeringatan->diterbitkanOleh?->name ?? '—', 2, '...') }}
                    </span>
                </div>
                <div class="flex justify-between">
                    <span class="text-slate-500">Dibuat</span>
                    <span class="font-medium text-slate-700">
                        {{ $suratPeringatan->created_at->isoFormat('D MMM Y, HH:mm') }}
                    </span>
                </div>
            </div>

            <div class="divider"></div>

            {{-- Tombol Aksi --}}
            <div class="space-y-2">
                {{-- Preview PDF --}}
                <a href="{{ route('admin.surat-peringatan.preview', $suratPeringatan) }}"
                   target="_blank"
                   class="btn-secondary w-full justify-center text-sm">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M15 12a3 3 0 11-6 0 3 3 0 016 0z M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/>
                    </svg>
                    Preview PDF
                </a>

                {{-- Download PDF --}}
                <a href="{{ route('admin.surat-peringatan.download', $suratPeringatan) }}"
                   class="btn-success w-full justify-center text-sm">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M12 10v6m0 0l-3-3m3 3l3-3m2 8H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/>
                    </svg>
                    Download PDF
                </a>

                {{-- Regenerate PDF --}}
                <form action="{{ route('admin.surat-peringatan.regenerate-pdf', $suratPeringatan) }}" method="POST">
                    @csrf @method('PATCH')
                    <button type="submit"
                        onclick="return confirm('Generate ulang PDF? File lama akan ditimpa.')"
                        class="btn-secondary w-full justify-center text-sm">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15"/>
                        </svg>
                        Generate Ulang PDF
                    </button>
                </form>

                {{-- Toggle Status --}}
                <form action="{{ route('admin.surat-peringatan.toggle-status', $suratPeringatan) }}" method="POST">
                    @csrf @method('PATCH')
                    <button type="submit"
                        class="w-full justify-center text-sm
                               {{ $suratPeringatan->status === 'aktif' ? 'btn-secondary' : 'btn-primary' }} flex items-center gap-2">
                        @if($suratPeringatan->status === 'aktif')
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/>
                        </svg>
                        Tandai Selesai
                        @else
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15"/>
                        </svg>
                        Aktifkan Kembali
                        @endif
                    </button>
                </form>

                {{-- Hapus --}}
                <button
                    onclick="if(confirm('Hapus SP ini? File PDF juga akan dihapus permanen.')) document.getElementById('form-del-sp').submit()"
                    class="btn-danger w-full justify-center text-sm">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/>
                    </svg>
                    Hapus SP
                </button>
                <form id="form-del-sp" action="{{ route('admin.surat-peringatan.destroy', $suratPeringatan) }}"
                      method="POST" class="hidden">
                    @csrf @method('DELETE')
                </form>
            </div>
        </div>

        {{-- Riwayat SP Siswa Ini --}}
        @if($allSpSiswa->count() > 1)
        <div class="card">
            <h3 class="font-heading font-bold text-slate-700 mb-3">Riwayat SP Siswa Ini</h3>
            <div class="space-y-2">
                @foreach($allSpSiswa as $spLain)
                <a href="{{ route('admin.surat-peringatan.show', $spLain) }}"
                   class="flex items-center gap-3 p-2.5 rounded-xl transition-colors
                          {{ $spLain->id === $suratPeringatan->id ? 'bg-green-50 border border-green-200' : 'hover:bg-slate-50' }}">
                    <div class="w-9 h-9 rounded-xl flex items-center justify-center flex-shrink-0 font-bold text-white text-xs
                        {{ $spLain->jenis_sp === 'SP1' ? 'bg-amber-500' : ($spLain->jenis_sp === 'SP2' ? 'bg-orange-600' : 'bg-red-600') }}">
                        {{ $spLain->jenis_sp }}
                    </div>
                    <div>
                        <p class="text-sm font-semibold text-slate-700">{{ $spLain->jenis_sp }}</p>
                        <p class="text-xs text-slate-400">{{ $spLain->tanggal_terbit->isoFormat('D MMM Y') }}</p>
                    </div>
                    <span class="ml-auto badge {{ $spLain->status === 'aktif' ? 'bg-red-100 text-red-700' : 'bg-slate-100 text-slate-500' }}">
                        {{ ucfirst($spLain->status) }}
                    </span>
                </a>
                @endforeach
            </div>
        </div>
        @endif
    </div>

    {{-- ── Kolom Kanan: Info Siswa & Pelanggaran ───────────────── --}}
    <div class="lg:col-span-2 space-y-5">

        {{-- Profil Siswa --}}
        <div class="card">
            <h3 class="font-heading font-bold text-slate-800 mb-4">Data Siswa</h3>
            <div class="flex items-start gap-4">
                <img src="{{ $suratPeringatan->siswa->foto_url }}"
                     alt="{{ $suratPeringatan->siswa->nama }}"
                     class="w-16 h-16 rounded-2xl object-cover border-2 border-slate-100 flex-shrink-0">
                <div class="flex-1 grid grid-cols-2 gap-3 text-sm">
                    <div>
                        <p class="text-xs text-slate-400 uppercase tracking-wide font-medium">Nama</p>
                        <p class="font-bold text-slate-700 mt-0.5">{{ $suratPeringatan->siswa->nama }}</p>
                    </div>
                    <div>
                        <p class="text-xs text-slate-400 uppercase tracking-wide font-medium">NIS</p>
                        <p class="font-mono font-semibold text-slate-700 mt-0.5">{{ $suratPeringatan->siswa->nis }}</p>
                    </div>
                    <div>
                        <p class="text-xs text-slate-400 uppercase tracking-wide font-medium">Kelas</p>
                        <span class="badge bg-green-50 text-green-700 mt-0.5 inline-flex">{{ $suratPeringatan->siswa->kelas }}</span>
                    </div>
                    <div>
                        <p class="text-xs text-slate-400 uppercase tracking-wide font-medium">Status SP Sekarang</p>
                        <span class="{{ $suratPeringatan->siswa->rekapPoin?->badge_class ?? 'badge-aman' }} mt-0.5 inline-flex">
                            {{ $suratPeringatan->siswa->status_sp }}
                        </span>
                    </div>
                    <div>
                        <p class="text-xs text-slate-400 uppercase tracking-wide font-medium">Orang Tua/Wali</p>
                        <p class="font-semibold text-slate-700 mt-0.5">{{ $suratPeringatan->siswa->nama_orang_tua }}</p>
                    </div>
                    <div>
                        <p class="text-xs text-slate-400 uppercase tracking-wide font-medium">No. HP</p>
                        <a href="tel:{{ $suratPeringatan->siswa->no_hp_orang_tua }}"
                           class="font-semibold text-secondary mt-0.5 block">
                            {{ $suratPeringatan->siswa->no_hp_orang_tua }}
                        </a>
                    </div>
                    <div>
                        <p class="text-xs text-slate-400 uppercase tracking-wide font-medium">Wali Kelas</p>
                        <p class="font-semibold text-slate-700 mt-0.5">{{ $suratPeringatan->siswa->waliKelas?->name ?? '—' }}</p>
                    </div>
                    <div>
                        <p class="text-xs text-slate-400 uppercase tracking-wide font-medium">Poin Saat Ini</p>
                        <p class="font-bold text-lg mt-0.5
                            {{ $suratPeringatan->siswa->total_poin >= 100 ? 'text-danger' : ($suratPeringatan->siswa->total_poin >= 75 ? 'text-orange-600' : 'text-amber-600') }}">
                            {{ $suratPeringatan->siswa->total_poin }}
                        </p>
                    </div>
                </div>
            </div>

            @if($suratPeringatan->keterangan)
            <div class="mt-4 p-3 bg-slate-50 rounded-xl border border-slate-200">
                <p class="text-xs text-slate-500 font-semibold uppercase tracking-wide mb-1">Catatan BK</p>
                <p class="text-sm text-slate-700">{{ $suratPeringatan->keterangan }}</p>
            </div>
            @endif

            <div class="mt-4 flex gap-2">
                <a href="{{ route('admin.siswa.show', $suratPeringatan->siswa) }}"
                   class="btn-secondary text-sm flex-1 justify-center">
                    Lihat Profil Lengkap →
                </a>
            </div>
        </div>

        {{-- Riwayat Pelanggaran yang Berkontribusi --}}
        <div class="card">
            <div class="flex items-center justify-between mb-4">
                <h3 class="font-heading font-bold text-slate-800">Pelanggaran Dikonfirmasi</h3>
                <span class="badge bg-slate-100 text-slate-600">
                    {{ $suratPeringatan->siswa->pelanggaran->count() }} catatan
                </span>
            </div>

            @if($suratPeringatan->siswa->pelanggaran->isEmpty())
            <div class="text-center py-8 text-slate-400">
                <p class="text-sm">Belum ada pelanggaran tercatat</p>
            </div>
            @else
            <div class="overflow-x-auto">
                <table class="w-full table-simon">
                    <thead>
                        <tr>
                            <th>Tanggal</th>
                            <th>Jenis Pelanggaran</th>
                            <th>Kategori</th>
                            <th class="text-center">Poin</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($suratPeringatan->siswa->pelanggaran as $p)
                        <tr>
                            <td class="text-sm whitespace-nowrap">
                                {{ $p->tanggal_pelanggaran->isoFormat('D MMM Y') }}
                            </td>
                            <td class="text-sm text-slate-700">{{ $p->jenisPelanggaran->nama }}</td>
                            <td>
                                <span class="{{ $p->jenisPelanggaran->badge_class }}">
                                    {{ $p->jenisPelanggaran->kategori_label }}
                                </span>
                            </td>
                            <td class="text-center font-bold text-sm
                                {{ $p->poin_diberikan >= 50 ? 'text-danger' : ($p->poin_diberikan >= 20 ? 'text-warning' : 'text-slate-600') }}">
                                +{{ $p->poin_diberikan }}
                            </td>
                        </tr>
                        @endforeach
                    </tbody>
                    <tfoot class="bg-slate-50">
                        <tr>
                            <td colspan="3" class="px-4 py-2.5 text-sm font-bold text-slate-700 text-right">
                                Total Poin:
                            </td>
                            <td class="px-4 py-2.5 text-center font-extrabold text-base
                                {{ $suratPeringatan->siswa->total_poin >= 100 ? 'text-danger' : ($suratPeringatan->siswa->total_poin >= 75 ? 'text-orange-600' : 'text-amber-600') }}">
                                {{ $suratPeringatan->siswa->total_poin }}
                            </td>
                        </tr>
                    </tfoot>
                </table>
            </div>
            @endif
        </div>
    </div>
</div>
@endsection
