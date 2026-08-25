@extends('layouts.app')

@section('title', $siswa->nama)
@section('page-title', 'Detail Siswa')

@section('breadcrumb')
<span class="text-slate-400">/</span>
<a href="{{ route('wali-kelas.siswa.index') }}" class="hover:text-secondary transition-colors">Siswa Saya</a>
<span class="text-slate-400">/</span>
<span class="text-slate-500 truncate max-w-[150px]">{{ $siswa->nama }}</span>
@endsection

@section('content')
<div class="grid grid-cols-1 lg:grid-cols-3 gap-6">

    {{-- ── Profil Card ──────────────────────────────────────────── --}}
    <div class="lg:col-span-1 space-y-5">
        <div class="card text-center">
            <img src="{{ $siswa->foto_url }}" alt="{{ $siswa->nama }}"
                 class="w-28 h-28 rounded-2xl object-cover mx-auto mb-4 border-2 border-slate-100">

            <h2 class="font-heading text-xl font-bold text-slate-800">{{ $siswa->nama }}</h2>
            <p class="text-slate-400 text-sm font-mono mt-0.5">{{ $siswa->nis }}</p>

            <div class="flex items-center justify-center gap-2 mt-3">
                <span class="badge bg-green-50 text-green-700">{{ $siswa->kelas }}</span>
                <span class="badge {{ $siswa->jenis_kelamin == 'L' ? 'bg-green-50 text-green-700' : 'bg-pink-50 text-pink-700' }}">
                    {{ $siswa->jenis_kelamin_label }}
                </span>
            </div>

            <div class="divider"></div>

            {{-- Poin Progress --}}
            <div class="text-left mb-4">
                <div class="flex justify-between items-center mb-2">
                    <span class="text-sm font-semibold text-slate-600">Akumulasi Poin</span>
                    <span class="font-heading text-2xl font-bold
                        {{ $siswa->total_poin >= 100 ? 'text-danger' : ($siswa->total_poin >= 75 ? 'text-orange-600' : ($siswa->total_poin >= 50 ? 'text-amber-600' : 'text-success')) }}">
                        {{ $siswa->total_poin }}
                    </span>
                </div>
                <div class="progress-bar-wrap mb-2">
                    <div class="{{ $siswa->progress_color }} progress-bar-fill"
                         style="width: {{ $siswa->progress_persen }}%"></div>
                </div>
                <div class="flex justify-between text-[10px] text-slate-400">
                    <span>0</span><span>50</span><span>75</span><span>100+</span>
                </div>
            </div>

            <div class="flex justify-center">
                <span class="{{ $siswa->rekapPoin?->badge_class ?? 'badge-aman' }} text-sm px-4 py-1.5">
                    {{ $siswa->status_sp === 'aman' ? '✅ Aman' : '⚠️ ' . $siswa->status_sp . ' Aktif' }}
                </span>
            </div>
        </div>

        {{-- Kontak --}}
        <div class="card">
            <h3 class="font-heading font-bold text-slate-700 mb-4">Kontak Orang Tua</h3>
            <div class="space-y-3 text-sm">
                <div>
                    <p class="text-xs text-slate-400 uppercase tracking-wide font-medium">Nama</p>
                    <p class="font-semibold text-slate-700 mt-0.5">{{ $siswa->nama_orang_tua }}</p>
                </div>
                <div>
                    <p class="text-xs text-slate-400 uppercase tracking-wide font-medium">Nomor HP</p>
                    <a href="https://wa.me/{{ preg_replace('/^0/', '62', $siswa->no_hp_orang_tua) }}"
                       target="_blank"
                       class="inline-flex items-center gap-1.5 text-emerald-600 font-semibold mt-0.5 hover:text-emerald-700">
                        <svg class="w-3.5 h-3.5" fill="currentColor" viewBox="0 0 24 24">
                            <path d="M17.472 14.382c-.297-.149-1.758-.867-2.03-.967-.273-.099-.471-.148-.67.15-.197.297-.767.966-.94 1.164-.173.199-.347.223-.644.075-.297-.15-1.255-.463-2.39-1.475-.883-.788-1.48-1.761-1.653-2.059-.173-.297-.018-.458.13-.606.134-.133.298-.347.446-.52.149-.174.198-.298.298-.497.099-.198.05-.371-.025-.52-.075-.149-.669-1.612-.916-2.207-.242-.579-.487-.5-.669-.51-.173-.008-.371-.01-.57-.01-.198 0-.52.074-.792.372-.272.297-1.04 1.016-1.04 2.479 0 1.462 1.065 2.875 1.213 3.074.149.198 2.096 3.2 5.077 4.487.709.306 1.262.489 1.694.625.712.227 1.36.195 1.871.118.571-.085 1.758-.719 2.006-1.413.248-.694.248-1.289.173-1.413-.074-.124-.272-.198-.57-.347m-5.421 7.403h-.004a9.87 9.87 0 01-5.031-1.378l-.361-.214-3.741.982.998-3.648-.235-.374a9.86 9.86 0 01-1.51-5.26c.001-5.45 4.436-9.884 9.888-9.884 2.64 0 5.122 1.03 6.988 2.898a9.825 9.825 0 012.893 6.994c-.003 5.45-4.437 9.884-9.885 9.884m8.413-18.297A11.815 11.815 0 0012.05 0C5.495 0 .16 5.335.157 11.892c0 2.096.547 4.142 1.588 5.945L.057 24l6.305-1.654a11.882 11.882 0 005.683 1.448h.005c6.554 0 11.89-5.335 11.893-11.893a11.821 11.821 0 00-3.48-8.413Z"/>
                        </svg>
                        {{ $siswa->no_hp_orang_tua }}
                    </a>
                </div>
                <div>
                    <p class="text-xs text-slate-400 uppercase tracking-wide font-medium">Alamat</p>
                    <p class="text-slate-600 mt-0.5 leading-relaxed text-xs">{{ $siswa->alamat }}</p>
                </div>
            </div>
        </div>

        {{-- SP yang pernah diterima --}}
        @if($siswa->suratPeringatan->isNotEmpty())
        <div class="card">
            <h3 class="font-heading font-bold text-slate-700 mb-3">Surat Peringatan</h3>
            <div class="space-y-2">
                @foreach($siswa->suratPeringatan as $sp)
                <div class="flex items-center gap-3 p-2.5 rounded-xl
                            {{ $sp->status === 'aktif' ? 'bg-red-50 border border-red-100' : 'bg-slate-50' }}">
                    <div class="w-9 h-9 {{ $sp->gradient_class }} rounded-xl flex items-center justify-center flex-shrink-0">
                        <span class="text-white text-xs font-bold">{{ $sp->jenis_sp }}</span>
                    </div>
                    <div>
                        <p class="text-sm font-semibold text-slate-700">{{ $sp->jenis_sp }}</p>
                        <p class="text-xs text-slate-400">{{ $sp->tanggal_terbit->isoFormat('D MMM Y') }}</p>
                    </div>
                    <span class="ml-auto badge {{ $sp->status === 'aktif' ? 'bg-red-100 text-red-600' : 'bg-slate-100 text-slate-500' }}">
                        {{ ucfirst($sp->status) }}
                    </span>
                </div>
                @endforeach
            </div>
        </div>
        @endif
    </div>

    {{-- ── Riwayat Pelanggaran ──────────────────────────────────── --}}
    <div class="lg:col-span-2">
        <div class="card">
            <div class="flex items-center justify-between mb-5">
                <h3 class="font-heading font-bold text-slate-800">Riwayat Pelanggaran</h3>
                <div class="flex items-center gap-2">
                    <span class="badge bg-slate-100 text-slate-600">
                        {{ $siswa->pelanggaran->count() }} catatan
                    </span>
                    {{-- Tombol input pelanggaran (aktif di increment 2) --}}
                    <span class="btn-primary opacity-50 cursor-not-allowed text-xs"
                          title="Tersedia di Increment 2">
                        <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/>
                        </svg>
                        Input Pelanggaran
                    </span>
                </div>
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
                <p class="text-xs text-slate-400 mt-1">{{ $siswa->nama }} masih bersih 🎉</p>
            </div>
            @else
            <div class="space-y-3">
                @foreach($siswa->pelanggaran as $p)
                <div class="flex items-start gap-3 p-3 rounded-xl border border-slate-100 hover:border-slate-200 transition-colors">

                    {{-- Icon kategori --}}
                    <div class="w-10 h-10 rounded-xl flex items-center justify-center flex-shrink-0 mt-0.5
                        {{ $p->jenisPelanggaran->kategori === 'ringan' ? 'bg-emerald-100' :
                           ($p->jenisPelanggaran->kategori === 'sedang' ? 'bg-amber-100' :
                           ($p->jenisPelanggaran->kategori === 'berat' ? 'bg-orange-100' : 'bg-red-100')) }}">
                        <span class="font-heading font-bold text-sm
                            {{ $p->jenisPelanggaran->warna_teks }}">
                            {{ $p->poin_diberikan }}
                        </span>
                    </div>

                    <div class="flex-1 min-w-0">
                        <div class="flex items-start justify-between gap-2">
                            <p class="text-sm font-semibold text-slate-700">
                                {{ $p->jenisPelanggaran->nama }}
                            </p>
                            <span class="{{ $p->status_badge_class }} flex-shrink-0">
                                {{ $p->status_label }}
                            </span>
                        </div>
                        <div class="flex items-center gap-3 mt-1">
                            <span class="{{ $p->jenisPelanggaran->badge_class }} text-[10px]">
                                {{ $p->jenisPelanggaran->kategori_label }}
                            </span>
                            <span class="text-xs text-slate-400">
                                {{ $p->tanggal_pelanggaran->isoFormat('D MMMM Y') }}
                            </span>
                        </div>
                        @if($p->keterangan)
                        <p class="text-xs text-slate-500 mt-1.5 italic">{{ $p->keterangan }}</p>
                        @endif
                        @if($p->status === 'ditolak' && $p->alasan_tolak)
                        <p class="text-xs text-danger mt-1.5 flex items-center gap-1">
                            <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M6 18L18 6M6 6l12 12"/>
                            </svg>
                            Ditolak: {{ $p->alasan_tolak }}
                        </p>
                        @endif
                    </div>
                </div>
                @endforeach
            </div>
            @endif
        </div>
    </div>
</div>
@endsection
