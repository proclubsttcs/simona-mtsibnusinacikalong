@extends('layouts.app')

@section('title', 'Edit Pelanggaran')
@section('page-title', 'Edit Catatan Pelanggaran')

@section('breadcrumb')
<span class="text-slate-400">/</span>
<a href="{{ route('wali-kelas.pelanggaran.index') }}" class="hover:text-secondary">Pelanggaran</a>
<span class="text-slate-400">/</span>
<span class="text-slate-500">Edit</span>
@endsection

@section('content')
<div class="max-w-2xl mx-auto">
    <form action="{{ route('wali-kelas.pelanggaran.update', $pelanggaran) }}" method="POST"
          x-data="formPelanggaran()">
        @csrf @method('PUT')

        <div class="space-y-5">
            <div class="card">
                <h3 class="font-heading font-bold text-slate-700 mb-4">Informasi Pelanggaran</h3>
                <div class="space-y-4">

                    {{-- Siswa (tidak bisa diubah jika sudah dikonfirmasi) --}}
                    <div>
                        <label class="form-label">Siswa <span class="text-danger">*</span></label>
                        <select name="siswa_id"
                                class="form-input {{ $errors->has('siswa_id') ? 'border-danger ring-2 ring-danger/20' : '' }}">
                            @foreach($siswaList as $s)
                            <option value="{{ $s->id }}" {{ old('siswa_id', $pelanggaran->siswa_id) == $s->id ? 'selected' : '' }}>
                                {{ $s->nama }} ({{ $s->nis }})
                            </option>
                            @endforeach
                        </select>
                        @error('siswa_id') <p class="form-error">{{ $message }}</p> @enderror
                    </div>

                    {{-- Tanggal --}}
                    <div>
                        <label class="form-label">Tanggal Kejadian <span class="text-danger">*</span></label>
                        <input type="date" name="tanggal_pelanggaran"
                               value="{{ old('tanggal_pelanggaran', $pelanggaran->tanggal_pelanggaran->toDateString()) }}"
                               max="{{ today()->toDateString() }}"
                               class="form-input {{ $errors->has('tanggal_pelanggaran') ? 'border-danger ring-2 ring-danger/20' : '' }}">
                        @error('tanggal_pelanggaran') <p class="form-error">{{ $message }}</p> @enderror
                    </div>
                </div>
            </div>

            <div class="card">
                <h3 class="font-heading font-bold text-slate-700 mb-4">Jenis Pelanggaran</h3>

                <div class="mb-4">
                    <label class="form-label">Pilih Jenis <span class="text-danger">*</span></label>
                    <select name="jenis_pelanggaran_id" @change="loadJenisInfo()"
                            class="form-input {{ $errors->has('jenis_pelanggaran_id') ? 'border-danger ring-2 ring-danger/20' : '' }}">
                        @foreach($jenisList as $kategori => $items)
                        <optgroup label="{{ Str::title(str_replace('_',' ',$kategori)) }}">
                            @foreach($items as $j)
                            <option value="{{ $j->id }}"
                                data-poin="{{ $j->poin }}"
                                data-kategori="{{ $j->kategori }}"
                                data-keterangan="{{ $j->keterangan }}"
                                {{ old('jenis_pelanggaran_id', $pelanggaran->jenis_pelanggaran_id) == $j->id ? 'selected' : '' }}>
                                {{ $j->nama }} ({{ $j->poin }} poin)
                            </option>
                            @endforeach
                        </optgroup>
                        @endforeach
                    </select>
                    @error('jenis_pelanggaran_id') <p class="form-error">{{ $message }}</p> @enderror
                </div>

                <div>
                    <label class="form-label">Poin yang Diberikan <span class="text-danger">*</span></label>
                    <input type="number" name="poin_diberikan" x-model="poinDiberikan"
                           min="1" max="200"
                           value="{{ old('poin_diberikan', $pelanggaran->poin_diberikan) }}"
                           class="form-input w-32 text-center font-bold text-lg {{ $errors->has('poin_diberikan') ? 'border-danger ring-2 ring-danger/20' : '' }}">
                    @error('poin_diberikan') <p class="form-error">{{ $message }}</p> @enderror
                </div>
            </div>

            <div class="card">
                <label class="form-label">Keterangan Tambahan</label>
                <textarea name="keterangan" rows="3"
                          class="form-input resize-none">{{ old('keterangan', $pelanggaran->keterangan) }}</textarea>
            </div>

            <div class="flex gap-3">
                <button type="submit" class="btn-primary flex-1 justify-center">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/>
                    </svg>
                    Simpan Perubahan
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
        poinDiberikan: {{ old('poin_diberikan', $pelanggaran->poin_diberikan) }},
        loadJenisInfo() {
            const select = document.querySelector('select[name="jenis_pelanggaran_id"]');
            const option = select.options[select.selectedIndex];
            if (option && option.value) {
                this.poinDiberikan = parseInt(option.dataset.poin) || 0;
            }
        },
    };
}
</script>
@endpush
