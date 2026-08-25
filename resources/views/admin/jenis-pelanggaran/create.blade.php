@extends('layouts.app')

@section('title', isset($jenisPelanggaran) ? 'Edit Jenis Pelanggaran' : 'Tambah Jenis Pelanggaran')
@section('page-title', isset($jenisPelanggaran) ? 'Edit Jenis Pelanggaran' : 'Tambah Jenis Pelanggaran')

@section('breadcrumb')
<span class="text-slate-400">/</span>
<a href="{{ route('admin.jenis-pelanggaran.index') }}" class="hover:text-secondary transition-colors">Jenis Pelanggaran</a>
<span class="text-slate-400">/</span>
<span class="text-slate-500">{{ isset($jenisPelanggaran) ? 'Edit' : 'Tambah' }}</span>
@endsection

@section('content')
<div class="max-w-lg mx-auto">

    <form
        action="{{ isset($jenisPelanggaran) ? route('admin.jenis-pelanggaran.update', $jenisPelanggaran) : route('admin.jenis-pelanggaran.store') }}"
        method="POST"
        x-data="{ kategori: '{{ old('kategori', $jenisPelanggaran->kategori ?? 'ringan') }}' }">
        @csrf
        @if(isset($jenisPelanggaran)) @method('PUT') @endif

        <div class="card space-y-5">
            <h3 class="font-heading font-bold text-slate-700 mb-1">
                {{ isset($jenisPelanggaran) ? 'Ubah Data Jenis Pelanggaran' : 'Data Jenis Pelanggaran Baru' }}
            </h3>

            {{-- Kode --}}
            <div>
                <label class="form-label">
                    Kode <span class="text-danger">*</span>
                    <span class="text-xs text-slate-400 font-normal ml-1">contoh: RNG-001, SDG-002</span>
                </label>
                <input type="text" name="kode"
                       value="{{ old('kode', $jenisPelanggaran->kode ?? '') }}"
                       placeholder="RNG-001"
                       class="form-input font-mono {{ $errors->has('kode') ? 'border-danger ring-2 ring-danger/20' : '' }}">
                @error('kode') <p class="form-error">{{ $message }}</p> @enderror
            </div>

            {{-- Nama --}}
            <div>
                <label class="form-label">Nama Pelanggaran <span class="text-danger">*</span></label>
                <input type="text" name="nama"
                       value="{{ old('nama', $jenisPelanggaran->nama ?? '') }}"
                       placeholder="Contoh: Terlambat masuk sekolah"
                       class="form-input {{ $errors->has('nama') ? 'border-danger ring-2 ring-danger/20' : '' }}">
                @error('nama') <p class="form-error">{{ $message }}</p> @enderror
            </div>

            {{-- Kategori --}}
            <div>
                <label class="form-label">Kategori <span class="text-danger">*</span></label>
                <div class="grid grid-cols-2 gap-2">
                    @foreach([
                        ['value' => 'ringan',       'label' => 'Ringan',       'color' => 'emerald', 'range' => '5–15 poin'],
                        ['value' => 'sedang',       'label' => 'Sedang',       'color' => 'amber',   'range' => '15–40 poin'],
                        ['value' => 'berat',        'label' => 'Berat',        'color' => 'orange',  'range' => '50–75 poin'],
                        ['value' => 'sangat_berat', 'label' => 'Sangat Berat', 'color' => 'red',     'range' => '100–150 poin'],
                    ] as $opt)
                    <label class="relative cursor-pointer">
                        <input type="radio" name="kategori" value="{{ $opt['value'] }}"
                               x-model="kategori"
                               {{ old('kategori', $jenisPelanggaran->kategori ?? 'ringan') === $opt['value'] ? 'checked' : '' }}
                               class="sr-only">
                        <div :class="kategori === '{{ $opt['value'] }}' ? 'border-{{ $opt['color'] }}-400 bg-{{ $opt['color'] }}-50' : 'border-slate-200 bg-white hover:bg-slate-50'"
                             class="border-2 rounded-xl p-3 transition-all duration-150">
                            <p class="text-sm font-bold text-{{ $opt['color'] }}-700">{{ $opt['label'] }}</p>
                            <p class="text-xs text-{{ $opt['color'] }}-500 mt-0.5">{{ $opt['range'] }}</p>
                        </div>
                    </label>
                    @endforeach
                </div>
                @error('kategori') <p class="form-error">{{ $message }}</p> @enderror
            </div>

            {{-- Poin --}}
            <div>
                <label class="form-label">
                    Poin <span class="text-danger">*</span>
                    <span class="text-xs text-slate-400 font-normal ml-1">1–200</span>
                </label>
                <div class="flex items-center gap-3">
                    <input type="number" name="poin"
                           value="{{ old('poin', $jenisPelanggaran->poin ?? '') }}"
                           min="1" max="200"
                           placeholder="10"
                           class="form-input w-28 text-center font-bold text-lg
                                  {{ $errors->has('poin') ? 'border-danger ring-2 ring-danger/20' : '' }}">
                    <p class="text-xs text-slate-500 flex-1">
                        Poin ini yang akan dikurangi dari total poin siswa (max 100 normal, 150 sangat berat).
                    </p>
                </div>
                @error('poin') <p class="form-error">{{ $message }}</p> @enderror
            </div>

            {{-- Keterangan --}}
            <div>
                <label class="form-label">
                    Keterangan
                    <span class="text-xs text-slate-400 font-normal ml-1">opsional</span>
                </label>
                <input type="text" name="keterangan"
                       value="{{ old('keterangan', $jenisPelanggaran->keterangan ?? '') }}"
                       placeholder="Contoh: Per hari, Per kejadian, + ganti rugi"
                       class="form-input {{ $errors->has('keterangan') ? 'border-danger ring-2 ring-danger/20' : '' }}">
                @error('keterangan') <p class="form-error">{{ $message }}</p> @enderror
            </div>

            {{-- Status --}}
            <div class="flex items-center gap-3 p-3 bg-slate-50 rounded-xl border border-slate-200">
                <input type="hidden" name="is_active" value="0">
                <input type="checkbox" id="is_active" name="is_active" value="1"
                       {{ old('is_active', $jenisPelanggaran->is_active ?? true) ? 'checked' : '' }}
                       class="w-4 h-4 text-secondary border-slate-300 rounded focus:ring-secondary">
                <label for="is_active" class="text-sm font-medium text-slate-700">
                    Aktifkan jenis pelanggaran ini
                </label>
            </div>

            {{-- Submit --}}
            <div class="flex gap-3 pt-2">
                <button type="submit" class="btn-primary flex-1 justify-center">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/>
                    </svg>
                    {{ isset($jenisPelanggaran) ? 'Simpan Perubahan' : 'Tambah Jenis Pelanggaran' }}
                </button>
                <a href="{{ route('admin.jenis-pelanggaran.index') }}" class="btn-secondary">Batal</a>
            </div>
        </div>
    </form>
</div>
@endsection
