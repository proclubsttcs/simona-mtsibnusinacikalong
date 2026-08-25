@extends('layouts.app')

@section('title', 'Input Pelanggaran')
@section('page-title', 'Input Pelanggaran Baru')

@section('breadcrumb')
<span class="text-slate-400">/</span>
<a href="{{ route('wali-kelas.pelanggaran.index') }}" class="hover:text-secondary transition-colors">Pelanggaran</a>
<span class="text-slate-400">/</span>
<span class="text-slate-500">Input Baru</span>
@endsection

@section('content')
<div class="max-w-2xl mx-auto">

    {{-- Info alur kerja --}}
    <div class="mb-5 p-4 bg-green-50 border border-green-200 rounded-2xl flex items-start gap-3">
        <svg class="w-5 h-5 text-secondary flex-shrink-0 mt-0.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
        </svg>
        <div class="text-sm text-green-700">
            <span class="font-semibold">Alur kerja:</span>
            Pelanggaran yang Anda input akan berstatus <strong>Menunggu</strong> dan perlu dikonfirmasi
            oleh BK sebelum poin dihitung ke akumulasi siswa.
        </div>
    </div>

    <form action="{{ route('wali-kelas.pelanggaran.store') }}" method="POST"
          x-data="formPelanggaran()"
          @submit.prevent="submitForm">
        @csrf

        <div class="space-y-5">
            {{-- Card Siswa & Tanggal --}}
            <div class="card">
                <h3 class="font-heading font-bold text-slate-700 mb-4">Informasi Pelanggaran</h3>
                <div class="space-y-4">

                    {{-- Pilih Siswa --}}
                    <div>
                        <label class="form-label">Siswa <span class="text-danger">*</span></label>
                        <select name="siswa_id" x-model="selectedSiswaId"
                                @change="loadSiswaInfo()"
                                class="form-input {{ $errors->has('siswa_id') ? 'border-danger ring-2 ring-danger/20' : '' }}">
                            <option value="">— Pilih Siswa —</option>
                            @foreach($siswaList as $s)
                            <option value="{{ $s->id }}"
                                data-poin="{{ $s->total_poin }}"
                                data-status="{{ $s->status_sp }}"
                                data-foto="{{ $s->foto_url }}"
                                {{ (old('siswa_id', $selectedSiswaId) == $s->id) ? 'selected' : '' }}>
                                {{ $s->nama }} ({{ $s->nis }})
                            </option>
                            @endforeach
                        </select>
                        @error('siswa_id') <p class="form-error">{{ $message }}</p> @enderror
                    </div>

                    {{-- Info Poin Siswa (muncul setelah pilih) --}}
                    <div x-show="siswaInfo.nama" x-transition class="p-3 bg-slate-50 rounded-xl border border-slate-200">
                        <div class="flex items-center gap-3">
                            <img :src="siswaInfo.foto" class="w-10 h-10 rounded-xl object-cover">
                            <div class="flex-1">
                                <p class="text-sm font-semibold text-slate-700" x-text="siswaInfo.nama"></p>
                                <div class="flex items-center gap-2 mt-1">
                                    <span class="text-xs text-slate-500">Poin saat ini:</span>
                                    <span class="text-sm font-bold" :class="siswaInfo.poin >= 75 ? 'text-danger' : (siswaInfo.poin >= 50 ? 'text-warning' : 'text-slate-700')"
                                          x-text="siswaInfo.poin"></span>
                                    <span class="badge text-[10px]"
                                          :class="siswaInfo.statusClass"
                                          x-text="siswaInfo.status"></span>
                                </div>
                                {{-- Progress bar --}}
                                <div class="mt-1.5 w-full bg-slate-200 rounded-full h-1.5">
                                    <div class="h-1.5 rounded-full transition-all duration-500"
                                         :class="siswaInfo.barColor"
                                         :style="`width: ${Math.min(100, (siswaInfo.poin/150)*100)}%`"></div>
                                </div>
                            </div>
                        </div>
                        {{-- Peringatan jika sudah dekat threshold --}}
                        <div x-show="siswaInfo.poin >= 45"
                             class="mt-2.5 p-2 bg-amber-50 rounded-lg border border-amber-200">
                            <p class="text-xs text-amber-700 font-medium">
                                ⚠ Pelanggaran ini mungkin akan menaikkan level SP siswa.
                                Poin saat ini: <span x-text="siswaInfo.poin"></span>/50 (SP1).
                            </p>
                        </div>
                    </div>

                    {{-- Tanggal Pelanggaran --}}
                    <div>
                        <label class="form-label">Tanggal Kejadian <span class="text-danger">*</span></label>
                        <input type="date" name="tanggal_pelanggaran"
                               value="{{ old('tanggal_pelanggaran', today()->toDateString()) }}"
                               max="{{ today()->toDateString() }}"
                               class="form-input {{ $errors->has('tanggal_pelanggaran') ? 'border-danger ring-2 ring-danger/20' : '' }}">
                        @error('tanggal_pelanggaran') <p class="form-error">{{ $message }}</p> @enderror
                    </div>
                </div>
            </div>

            {{-- Card Jenis Pelanggaran --}}
            <div class="card">
                <h3 class="font-heading font-bold text-slate-700 mb-4">Jenis Pelanggaran</h3>

                {{-- Pilih Jenis --}}
                <div class="mb-4">
                    <label class="form-label">Pilih Jenis <span class="text-danger">*</span></label>
                    <select name="jenis_pelanggaran_id" x-model="selectedJenisId"
                            @change="loadJenisInfo()"
                            class="form-input {{ $errors->has('jenis_pelanggaran_id') ? 'border-danger ring-2 ring-danger/20' : '' }}">
                        <option value="">— Pilih Jenis Pelanggaran —</option>
                        @foreach($jenisList as $kategori => $items)
                        <optgroup label="{{ Str::title(str_replace('_',' ',$kategori)) }}">
                            @foreach($items as $j)
                            <option value="{{ $j->id }}"
                                data-poin="{{ $j->poin }}"
                                data-kategori="{{ $j->kategori }}"
                                data-keterangan="{{ $j->keterangan }}"
                                {{ old('jenis_pelanggaran_id') == $j->id ? 'selected' : '' }}>
                                {{ $j->nama }} ({{ $j->poin }} poin)
                            </option>
                            @endforeach
                        </optgroup>
                        @endforeach
                    </select>
                    @error('jenis_pelanggaran_id') <p class="form-error">{{ $message }}</p> @enderror
                </div>

                {{-- Info Jenis yang Dipilih --}}
                <div x-show="jenisInfo.nama" x-transition
                     class="mb-4 p-3 rounded-xl border"
                     :class="jenisInfo.bgClass">
                    <div class="flex items-start gap-2.5">
                        <div class="w-8 h-8 rounded-lg flex items-center justify-center flex-shrink-0"
                             :class="jenisInfo.iconBg">
                            <span class="text-xs font-bold" :class="jenisInfo.textClass" x-text="jenisInfo.poin + 'P'"></span>
                        </div>
                        <div>
                            <p class="text-sm font-semibold" :class="jenisInfo.textClass" x-text="jenisInfo.nama"></p>
                            <p class="text-xs text-slate-500 mt-0.5" x-text="jenisInfo.keterangan" x-show="jenisInfo.keterangan"></p>
                        </div>
                    </div>
                </div>

                {{-- Poin yang Diberikan --}}
                <div>
                    <label class="form-label">
                        Poin yang Diberikan <span class="text-danger">*</span>
                        <span class="text-xs text-slate-400 font-normal ml-1">(bisa disesuaikan)</span>
                    </label>
                    <div class="flex items-center gap-3">
                        <input type="number" name="poin_diberikan"
                               x-model="poinDiberikan"
                               min="1" max="200"
                               class="form-input w-32 text-center font-bold text-lg
                                      {{ $errors->has('poin_diberikan') ? 'border-danger ring-2 ring-danger/20' : '' }}"
                               value="{{ old('poin_diberikan') }}">
                        <div class="flex-1 p-2.5 bg-slate-50 rounded-xl border border-slate-200">
                            <p class="text-xs text-slate-500">
                                Poin default: <span class="font-bold text-slate-700" x-text="jenisInfo.poin || '—'"></span>
                            </p>
                            <p class="text-xs text-slate-400 mt-0.5">
                                Ubah jika ada pertimbangan khusus dari BK
                            </p>
                        </div>
                    </div>
                    @error('poin_diberikan') <p class="form-error">{{ $message }}</p> @enderror
                </div>
            </div>

            {{-- Keterangan --}}
            <div class="card">
                <label class="form-label">Keterangan Tambahan</label>
                <textarea name="keterangan" rows="3"
                          placeholder="Deskripsikan kejadian secara singkat (opsional)..."
                          class="form-input resize-none {{ $errors->has('keterangan') ? 'border-danger ring-2 ring-danger/20' : '' }}">{{ old('keterangan') }}</textarea>
                @error('keterangan') <p class="form-error">{{ $message }}</p> @enderror
                <p class="text-xs text-slate-400 mt-1.5">Maksimal 500 karakter</p>
            </div>

            {{-- Tombol --}}
            <div class="flex gap-3">
                <button type="submit" class="btn-primary flex-1 justify-center">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2"/>
                    </svg>
                    Catat Pelanggaran
                </button>
                <a href="{{ route('wali-kelas.pelanggaran.index') }}" class="btn-secondary">Batal</a>
            </div>
        </div>
    </form>
</div>
@endsection

@push('scripts')
<script>
function formPelanggaran() {
    return {
        selectedSiswaId: '{{ old('siswa_id', $selectedSiswaId ?? '') }}',
        selectedJenisId: '{{ old('jenis_pelanggaran_id', '') }}',
        poinDiberikan: {{ old('poin_diberikan', 0) }},
        siswaInfo: { nama: '', poin: 0, status: 'aman', foto: '', statusClass: '', barColor: '' },
        jenisInfo: { nama: '', poin: 0, keterangan: '', bgClass: '', iconBg: '', textClass: '' },

        init() {
            // Auto-load jika ada nilai dari old() atau query string
            if (this.selectedSiswaId) this.loadSiswaInfo();
            if (this.selectedJenisId) this.loadJenisInfo();
        },

        loadSiswaInfo() {
            const select  = document.querySelector('select[name="siswa_id"]');
            const option  = select.options[select.selectedIndex];
            if (!option || !option.value) {
                this.siswaInfo = { nama: '' };
                return;
            }
            const poin   = parseInt(option.dataset.poin) || 0;
            const status = option.dataset.status || 'aman';

            this.siswaInfo = {
                nama:        option.text.split(' (')[0],
                foto:        option.dataset.foto,
                poin,
                status,
                statusClass: status === 'aman' ? 'bg-emerald-100 text-emerald-700'
                           : status === 'SP1'  ? 'bg-amber-100 text-amber-700'
                           : status === 'SP2'  ? 'bg-orange-100 text-orange-700'
                           : 'bg-red-100 text-red-700',
                barColor:    poin >= 100 ? 'bg-red-500'
                           : poin >= 75  ? 'bg-orange-500'
                           : poin >= 50  ? 'bg-amber-400'
                           : 'bg-emerald-400',
            };
        },

        loadJenisInfo() {
            const select  = document.querySelector('select[name="jenis_pelanggaran_id"]');
            const option  = select.options[select.selectedIndex];
            if (!option || !option.value) {
                this.jenisInfo = { nama: '' };
                return;
            }
            const poin     = parseInt(option.dataset.poin) || 0;
            const kategori = option.dataset.kategori;

            // Auto-isi poin
            this.poinDiberikan = poin;

            const styleMap = {
                ringan:       { bgClass: 'bg-emerald-50 border-emerald-200', iconBg: 'bg-emerald-100', textClass: 'text-emerald-700' },
                sedang:       { bgClass: 'bg-amber-50 border-amber-200',     iconBg: 'bg-amber-100',   textClass: 'text-amber-700'   },
                berat:        { bgClass: 'bg-orange-50 border-orange-200',   iconBg: 'bg-orange-100',  textClass: 'text-orange-700'  },
                sangat_berat: { bgClass: 'bg-red-50 border-red-200',         iconBg: 'bg-red-100',     textClass: 'text-red-700'     },
            };
            const style = styleMap[kategori] || styleMap.ringan;

            this.jenisInfo = {
                nama:       option.text.split(' (')[0],
                poin,
                keterangan: option.dataset.keterangan,
                ...style,
            };
        },

        submitForm() {
            this.$el.submit();
        },
    };
}
</script>
@endpush
