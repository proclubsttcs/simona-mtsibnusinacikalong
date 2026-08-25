@extends('layouts.app')

@section('title', $siswa->nama)
@section('page-title', 'Detail Siswa')

@section('breadcrumb')
<span class="text-slate-400">/</span>
<a href="{{ route('admin.siswa.index') }}" class="hover:text-secondary transition-colors">Data Siswa</a>
<span class="text-slate-400">/</span>
<span class="text-slate-500 truncate max-w-[150px]">{{ $siswa->nama }}</span>
@endsection

@section('content')
<div class="grid grid-cols-1 lg:grid-cols-3 gap-6">

    {{-- ── Kolom Kiri: Profil Card ─────────────────────────────── --}}
    <div class="lg:col-span-1 space-y-5">

        {{-- Profil --}}
        <div class="card text-center">
            <img src="{{ $siswa->foto_url }}" alt="{{ $siswa->nama }}"
                 class="w-28 h-28 rounded-2xl object-cover mx-auto mb-4 border-2 border-slate-100">

            <h2 class="font-heading text-xl font-bold text-slate-800">{{ $siswa->nama }}</h2>
            <p class="text-slate-500 text-sm mt-0.5">{{ $siswa->nis }}</p>

            <div class="flex items-center justify-center gap-2 mt-3">
                <span class="badge bg-green-50 text-green-700">{{ $siswa->kelas }}</span>
                <span class="badge {{ $siswa->jenis_kelamin == 'L' ? 'bg-green-50 text-green-700' : 'bg-pink-50 text-pink-700' }}">
                    {{ $siswa->jenis_kelamin_label }}
                </span>
            </div>

            <div class="divider"></div>

            {{-- Progress Poin --}}
            <div class="text-left mb-4">
                <div class="flex justify-between items-center mb-2">
                    <span class="text-sm font-semibold text-slate-600">Akumulasi Poin</span>
                    <span class="font-heading text-xl font-bold
                        {{ $siswa->total_poin >= 100 ? 'text-danger' : ($siswa->total_poin >= 75 ? 'text-orange-600' : ($siswa->total_poin >= 50 ? 'text-amber-600' : 'text-success')) }}">
                        {{ $siswa->total_poin }}
                    </span>
                </div>
                <div class="progress-bar-wrap mb-2">
                    <div class="{{ $siswa->progress_color }} progress-bar-fill"
                         style="width: {{ $siswa->progress_persen }}%"></div>
                </div>
                <div class="flex justify-between text-xs text-slate-400">
                    <span>0</span>
                    <span>50 (SP1)</span>
                    <span>75 (SP2)</span>
                    <span>100 (SP3)</span>
                </div>
            </div>

            {{-- Badge Status --}}
            <div class="flex justify-center">
                <span class="{{ $siswa->rekapPoin?->badge_class ?? 'badge-aman' }} text-sm px-4 py-1.5">
                    @if($siswa->status_sp === 'aman')
                    ✅ Aman
                    @else
                    ⚠️ {{ $siswa->status_sp }} Aktif
                    @endif
                </span>
            </div>

            <div class="divider"></div>

            {{-- Aksi --}}
            <div class="flex gap-2">
                <a href="{{ route('admin.siswa.edit', $siswa) }}" class="btn-secondary flex-1 justify-center text-xs">
                    <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/>
                    </svg>
                    Edit
                </a>
                <button
                    onclick="if(confirm('Nonaktifkan siswa ini?')) document.getElementById('form-hapus').submit()"
                    class="btn-danger flex-1 justify-center text-xs">
                    <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/>
                    </svg>
                    Hapus
                </button>
                <form id="form-hapus" action="{{ route('admin.siswa.destroy', $siswa) }}" method="POST" class="hidden">
                    @csrf @method('DELETE')
                </form>
            </div>
        </div>

        {{-- Info Kontak --}}
        <div class="card">
            <h3 class="font-heading font-bold text-slate-700 mb-4">Informasi Kontak</h3>
            <div class="space-y-3 text-sm">
                <div>
                    <p class="text-xs text-slate-400 font-medium uppercase tracking-wide">Orang Tua / Wali</p>
                    <p class="text-slate-700 font-semibold mt-0.5">{{ $siswa->nama_orang_tua }}</p>
                </div>
                <div>
                    <p class="text-xs text-slate-400 font-medium uppercase tracking-wide">Nomor HP</p>
                    <a href="tel:{{ $siswa->no_hp_orang_tua }}"
                       class="text-secondary hover:text-secondary-700 font-semibold mt-0.5 flex items-center gap-1.5">
                        <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M3 5a2 2 0 012-2h3.28a1 1 0 01.948.684l1.498 4.493a1 1 0 01-.502 1.21l-2.257 1.13a11.042 11.042 0 005.516 5.516l1.13-2.257a1 1 0 011.21-.502l4.493 1.498a1 1 0 01.684.949V19a2 2 0 01-2 2h-1C9.716 21 3 14.284 3 6V5z"/>
                        </svg>
                        {{ $siswa->no_hp_orang_tua }}
                    </a>
                </div>
                <div>
                    <p class="text-xs text-slate-400 font-medium uppercase tracking-wide">Alamat</p>
                    <p class="text-slate-700 mt-0.5 leading-relaxed">{{ $siswa->alamat }}</p>
                </div>
                <div>
                    <p class="text-xs text-slate-400 font-medium uppercase tracking-wide">Wali Kelas</p>
                    <p class="text-slate-700 font-semibold mt-0.5">{{ $siswa->waliKelas?->name }}</p>
                    <p class="text-xs text-slate-400">{{ $siswa->waliKelas?->email }}</p>
                </div>
            </div>
        </div>

        {{-- Surat Peringatan --}}
        @if($siswa->suratPeringatan->isNotEmpty())
        <div class="card">
            <h3 class="font-heading font-bold text-slate-700 mb-4">Surat Peringatan</h3>
            <div class="space-y-2">
                @foreach($siswa->suratPeringatan as $sp)
                <div class="flex items-center gap-3 p-3 rounded-xl
                            {{ $sp->status === 'aktif' ? 'bg-red-50 border border-red-100' : 'bg-slate-50' }}">
                    <div class="w-10 h-10 {{ $sp->gradient_class }} rounded-xl flex items-center justify-center flex-shrink-0">
                        <span class="text-white text-xs font-bold">{{ $sp->jenis_sp }}</span>
                    </div>
                    <div>
                        <p class="text-sm font-semibold text-slate-700">{{ $sp->jenis_sp }}</p>
                        <p class="text-xs text-slate-400">{{ $sp->tanggal_terbit->isoFormat('D MMM Y') }}</p>
                    </div>
                    @if($sp->file_pdf)
                    <a href="{{ $sp->pdf_url }}" target="_blank"
                       class="ml-auto p-1.5 text-slate-400 hover:text-secondary rounded-lg hover:bg-green-50 transition-colors">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M12 10v6m0 0l-3-3m3 3l3-3m2 8H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/>
                        </svg>
                    </a>
                    @endif
                </div>
                @endforeach
            </div>
        </div>
        @endif
    </div>

    {{-- ── Kolom Kanan: Riwayat Pelanggaran ───────────────────── --}}
    <div class="lg:col-span-2">
        <div class="card">
            <div class="flex items-center justify-between mb-4">
                <h3 class="font-heading font-bold text-slate-800">Riwayat Pelanggaran</h3>
                <span class="badge bg-slate-100 text-slate-600">
                    {{ $siswa->pelanggaran->count() }} catatan
                </span>
            </div>

            @if($siswa->pelanggaran->isEmpty())
            <div class="empty-state py-12">
                <div class="empty-state-icon">
                    <svg class="w-8 h-8 text-emerald-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/>
                    </svg>
                </div>
                <p class="text-sm font-medium text-slate-600">Belum ada catatan pelanggaran</p>
                <p class="text-xs text-slate-400 mt-1">Siswa ini masih bersih!</p>
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
                            <th>Status</th>
                            <th>Dicatat Oleh</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($siswa->pelanggaran as $p)
                        <tr>
                            <td class="whitespace-nowrap">
                                {{ $p->tanggal_pelanggaran->isoFormat('D MMM Y') }}
                            </td>
                            <td>
                                <p class="font-medium text-slate-700">{{ $p->jenisPelanggaran->nama }}</p>
                                @if($p->keterangan)
                                <p class="text-xs text-slate-400 mt-0.5">{{ Str::limit($p->keterangan, 50) }}</p>
                                @endif
                            </td>
                            <td>
                                <span class="{{ $p->jenisPelanggaran->badge_class }}">
                                    {{ $p->jenisPelanggaran->kategori_label }}
                                </span>
                            </td>
                            <td class="text-center">
                                <span class="font-bold {{ $p->poin_diberikan >= 50 ? 'text-danger' : ($p->poin_diberikan >= 20 ? 'text-warning' : 'text-slate-600') }}">
                                    +{{ $p->poin_diberikan }}
                                </span>
                            </td>
                            <td>
                                <span class="{{ $p->status_badge_class }}">{{ $p->status_label }}</span>
                            </td>
                            <td>
                                <p class="text-xs text-slate-500">{{ Str::words($p->inputOleh->name, 2, '...') }}</p>
                            </td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
            @endif
        </div>
    </div>
</div>
@endsection
