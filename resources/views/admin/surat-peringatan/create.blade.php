@extends('layouts.app')

@section('title', 'Buat SP Manual')
@section('page-title', 'Buat Surat Peringatan Manual')

@section('breadcrumb')
<span class="text-slate-400">/</span>
<a href="{{ route('admin.surat-peringatan.index') }}" class="hover:text-secondary transition-colors">Surat Peringatan</a>
<span class="text-slate-400">/</span>
<span class="text-slate-500">Buat Manual</span>
@endsection

@section('content')
<div class="max-w-2xl mx-auto">

    {{-- Info --}}
    <div class="mb-5 p-4 bg-green-50 border border-green-200 rounded-2xl flex items-start gap-3">
        <svg class="w-5 h-5 text-secondary flex-shrink-0 mt-0.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
        </svg>
        <div class="text-sm text-green-700">
            <p class="font-semibold mb-1">SP Otomatis vs Manual</p>
            <p>SP biasanya diterbitkan <strong>otomatis</strong> saat poin siswa melebihi batas (50/75/100).
            Gunakan form ini hanya untuk menerbitkan SP secara manual, misalnya untuk koreksi atau situasi khusus.</p>
        </div>
    </div>

    <form action="{{ route('admin.surat-peringatan.store') }}" method="POST"
          x-data="formSp()">
        @csrf

        <div class="space-y-5">
            {{-- Pilih Siswa --}}
            <div class="card">
                <h3 class="font-heading font-bold text-slate-700 mb-4">Pilih Siswa</h3>

                <div>
                    <label class="form-label">Siswa <span class="text-danger">*</span></label>
                    <select name="siswa_id" x-model="selectedSiswaId" @change="loadSiswa()"
                            class="form-input {{ $errors->has('siswa_id') ? 'border-danger ring-2 ring-danger/20' : '' }}">
                        <option value="">— Pilih Siswa —</option>
                        @foreach($siswaList as $s)
                        <option value="{{ $s->id }}"
                            data-poin="{{ $s->total_poin }}"
                            data-status="{{ $s->status_sp }}"
                            data-kelas="{{ $s->kelas }}"
                            data-nama="{{ $s->nama }}"
                            data-foto="{{ $s->foto_url }}"
                            {{ old('siswa_id', $selectedSiswaId) == $s->id ? 'selected' : '' }}>
                            {{ $s->nama }} — {{ $s->kelas }} ({{ $s->total_poin }} poin)
                        </option>
                        @endforeach
                    </select>
                    @error('siswa_id') <p class="form-error">{{ $message }}</p> @enderror
                </div>

                {{-- Info Siswa --}}
                <div x-show="siswa.nama" x-transition class="mt-4 p-3 bg-slate-50 rounded-xl border border-slate-200">
                    <div class="flex items-center gap-3">
                        <img :src="siswa.foto" class="w-12 h-12 rounded-xl object-cover flex-shrink-0">
                        <div class="flex-1">
                            <p class="font-semibold text-slate-800" x-text="siswa.nama"></p>
                            <p class="text-xs text-slate-500" x-text="'Kelas ' + siswa.kelas"></p>
                            <div class="flex items-center gap-2 mt-1.5">
                                <div class="flex-1 bg-slate-200 rounded-full h-2 overflow-hidden">
                                    <div class="h-full rounded-full transition-all duration-500"
                                         :class="siswa.poin >= 100 ? 'bg-red-500' : (siswa.poin >= 75 ? 'bg-orange-500' : (siswa.poin >= 50 ? 'bg-amber-400' : 'bg-emerald-400'))"
                                         :style="`width: ${Math.min(100, (siswa.poin/150)*100)}%`"></div>
                                </div>
                                <span class="text-sm font-bold text-slate-700" x-text="siswa.poin + ' poin'"></span>
                            </div>
                        </div>
                        <span class="badge text-sm px-3 py-1"
                              :class="siswa.status === 'aman' ? 'bg-emerald-100 text-emerald-700' :
                                      (siswa.status === 'SP1' ? 'bg-amber-100 text-amber-700' :
                                      (siswa.status === 'SP2' ? 'bg-orange-100 text-orange-700' :
                                       'bg-red-100 text-red-700'))"
                              x-text="siswa.status">
                        </span>
                    </div>
                </div>
            </div>

            {{-- Pilih Jenis SP --}}
            <div class="card">
                <h3 class="font-heading font-bold text-slate-700 mb-4">Jenis Surat Peringatan</h3>

                <div class="grid grid-cols-3 gap-3">
                    @foreach([
                        ['value'=>'SP1','label'=>'SP1','sub'=>'50–74 poin','bg'=>'amber','border'=>'border-amber-400','bgSel'=>'bg-amber-50'],
                        ['value'=>'SP2','label'=>'SP2','sub'=>'75–99 poin','bg'=>'orange','border'=>'border-orange-500','bgSel'=>'bg-orange-50'],
                        ['value'=>'SP3','label'=>'SP3','sub'=>'100+ poin', 'bg'=>'red',   'border'=>'border-red-500',  'bgSel'=>'bg-red-50'],
                    ] as $opt)
                    <label class="cursor-pointer">
                        <input type="radio" name="jenis_sp" value="{{ $opt['value'] }}"
                               x-model="jenisSp"
                               {{ old('jenis_sp', 'SP1') === $opt['value'] ? 'checked' : '' }}
                               class="sr-only">
                        <div :class="jenisSp === '{{ $opt['value'] }}' ? '{{ $opt['border'] }} {{ $opt['bgSel'] }}' : 'border-slate-200 bg-white hover:bg-slate-50'"
                             class="border-2 rounded-xl p-4 text-center transition-all duration-150">
                            <p class="font-heading text-2xl font-extrabold text-{{ $opt['bg'] }}-600">{{ $opt['label'] }}</p>
                            <p class="text-xs text-slate-500 mt-0.5">{{ $opt['sub'] }}</p>
                        </div>
                    </label>
                    @endforeach
                </div>
                @error('jenis_sp') <p class="form-error mt-2">{{ $message }}</p> @enderror

                {{-- Deskripsi jenis SP yang dipilih --}}
                <div class="mt-3 p-3 rounded-xl text-sm"
                     :class="jenisSp === 'SP1' ? 'bg-amber-50 text-amber-800' :
                             (jenisSp === 'SP2' ? 'bg-orange-50 text-orange-800' :
                              'bg-red-50 text-red-800')">
                    <template x-if="jenisSp === 'SP1'">
                        <p>⚠️ <strong>Peringatan Pertama</strong> — Orang tua dipanggil, siswa mengikuti program pembinaan BK.</p>
                    </template>
                    <template x-if="jenisSp === 'SP2'">
                        <p>🔶 <strong>Peringatan Kedua</strong> — Orang tua menandatangani surat pernyataan, pengawasan ketat.</p>
                    </template>
                    <template x-if="jenisSp === 'SP3'">
                        <p>🚨 <strong>Peringatan Terakhir</strong> — Orang tua wajib hadir 3×24 jam, sidang kasus, ancaman dikeluarkan.</p>
                    </template>
                </div>
            </div>

            {{-- Keterangan --}}
            <div class="card">
                <label class="form-label">
                    Keterangan / Catatan BK
                    <span class="text-xs text-slate-400 font-normal ml-1">(opsional)</span>
                </label>
                <textarea name="keterangan" rows="3"
                          placeholder="Catatan tambahan untuk dicantumkan dalam surat..."
                          class="form-input resize-none {{ $errors->has('keterangan') ? 'border-danger ring-2 ring-danger/20' : '' }}">{{ old('keterangan') }}</textarea>
                @error('keterangan') <p class="form-error">{{ $message }}</p> @enderror
            </div>

            {{-- Preview info sebelum submit --}}
            <div x-show="siswa.nama && jenisSp" x-transition
                 class="p-4 rounded-2xl border-2"
                 :class="jenisSp === 'SP1' ? 'border-amber-300 bg-amber-50/50' :
                         (jenisSp === 'SP2' ? 'border-orange-300 bg-orange-50/50' :
                          'border-red-300 bg-red-50/50')">
                <p class="text-sm font-semibold text-slate-700 mb-1">Ringkasan:</p>
                <p class="text-sm text-slate-600">
                    Akan menerbitkan <strong x-text="jenisSp"></strong> untuk
                    <strong x-text="siswa.nama"></strong>
                    (kelas <span x-text="siswa.kelas"></span>)
                    dengan poin saat ini: <strong x-text="siswa.poin"></strong>.
                </p>
                <p class="text-xs text-slate-400 mt-1">PDF akan digenerate otomatis setelah disimpan.</p>
            </div>

            <div class="flex gap-3">
                <button type="submit" class="btn-primary flex-1 justify-center"
                        :disabled="!siswa.nama || !jenisSp">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/>
                    </svg>
                    Terbitkan SP & Generate PDF
                </button>
                <a href="{{ route('admin.surat-peringatan.index') }}" class="btn-secondary">Batal</a>
            </div>
        </div>
    </form>
</div>
@endsection

@push('scripts')
<script>
function formSp() {
    return {
        selectedSiswaId: '{{ old('siswa_id', $selectedSiswaId ?? '') }}',
        jenisSp: '{{ old('jenis_sp', 'SP1') }}',
        siswa: {
            nama: '',
            kelas: '',
            poin: 0,
            status: 'aman',
            foto: '',
        },

        init() {
            if (this.selectedSiswaId) this.loadSiswa();
        },

        loadSiswa() {
            const select = document.querySelector('select[name="siswa_id"]');
            const opt    = select.options[select.selectedIndex];
            if (!opt || !opt.value) { this.siswa = { nama: '' }; return; }

            this.siswa = {
                nama:   opt.dataset.nama,
                kelas:  opt.dataset.kelas,
                poin:   parseInt(opt.dataset.poin) || 0,
                status: opt.dataset.status,
                foto:   opt.dataset.foto,
            };

            // Saran jenis SP berdasarkan poin
            const poin = this.siswa.poin;
            if (poin >= 100)     this.jenisSp = 'SP3';
            else if (poin >= 75) this.jenisSp = 'SP2';
            else                 this.jenisSp = 'SP1';
        },
    };
}
</script>
@endpush
