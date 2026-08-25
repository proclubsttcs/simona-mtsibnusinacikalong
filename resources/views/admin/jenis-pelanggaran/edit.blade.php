@extends('layouts.app')

@section('title', 'Edit Jenis Pelanggaran')
@section('page-title', 'Edit Jenis Pelanggaran')

@section('breadcrumb')
<span class="text-slate-400">/</span>
<a href="{{ route('admin.jenis-pelanggaran.index') }}" class="hover:text-secondary transition-colors">Jenis Pelanggaran</a>
<span class="text-slate-400">/</span>
<span class="text-slate-500">Edit — {{ $jenisPelanggaran->kode }}</span>
@endsection

@section('content')
<div class="max-w-lg mx-auto">
    <form action="{{ route('admin.jenis-pelanggaran.update', $jenisPelanggaran) }}" method="POST"
          x-data="{ kategori: '{{ old('kategori', $jenisPelanggaran->kategori) }}' }">
        @csrf @method('PUT')

        <div class="card space-y-5">
            <h3 class="font-heading font-bold text-slate-700">Edit: {{ $jenisPelanggaran->nama }}</h3>

            <div>
                <label class="form-label">Kode <span class="text-danger">*</span></label>
                <input type="text" name="kode" value="{{ old('kode', $jenisPelanggaran->kode) }}"
                       class="form-input font-mono {{ $errors->has('kode') ? 'border-danger ring-2 ring-danger/20' : '' }}">
                @error('kode') <p class="form-error">{{ $message }}</p> @enderror
            </div>

            <div>
                <label class="form-label">Nama Pelanggaran <span class="text-danger">*</span></label>
                <input type="text" name="nama" value="{{ old('nama', $jenisPelanggaran->nama) }}"
                       class="form-input {{ $errors->has('nama') ? 'border-danger ring-2 ring-danger/20' : '' }}">
                @error('nama') <p class="form-error">{{ $message }}</p> @enderror
            </div>

            <div>
                <label class="form-label">Kategori <span class="text-danger">*</span></label>
                <div class="grid grid-cols-2 gap-2">
                    @foreach([
                        ['value' => 'ringan',       'label' => 'Ringan',       'color' => 'emerald'],
                        ['value' => 'sedang',       'label' => 'Sedang',       'color' => 'amber'],
                        ['value' => 'berat',        'label' => 'Berat',        'color' => 'orange'],
                        ['value' => 'sangat_berat', 'label' => 'Sangat Berat', 'color' => 'red'],
                    ] as $opt)
                    <label class="relative cursor-pointer">
                        <input type="radio" name="kategori" value="{{ $opt['value'] }}"
                               x-model="kategori"
                               {{ old('kategori', $jenisPelanggaran->kategori) === $opt['value'] ? 'checked' : '' }}
                               class="sr-only">
                        <div :class="kategori === '{{ $opt['value'] }}' ? 'border-{{ $opt['color'] }}-400 bg-{{ $opt['color'] }}-50' : 'border-slate-200 bg-white hover:bg-slate-50'"
                             class="border-2 rounded-xl p-3 transition-all duration-150">
                            <p class="text-sm font-bold text-{{ $opt['color'] }}-700">{{ $opt['label'] }}</p>
                        </div>
                    </label>
                    @endforeach
                </div>
                @error('kategori') <p class="form-error">{{ $message }}</p> @enderror
            </div>

            <div>
                <label class="form-label">Poin <span class="text-danger">*</span></label>
                <input type="number" name="poin" value="{{ old('poin', $jenisPelanggaran->poin) }}"
                       min="1" max="200"
                       class="form-input w-28 text-center font-bold text-lg
                              {{ $errors->has('poin') ? 'border-danger ring-2 ring-danger/20' : '' }}">
                @error('poin') <p class="form-error">{{ $message }}</p> @enderror
            </div>

            <div>
                <label class="form-label">Keterangan</label>
                <input type="text" name="keterangan"
                       value="{{ old('keterangan', $jenisPelanggaran->keterangan) }}"
                       class="form-input">
            </div>

            <div class="flex items-center gap-3 p-3 bg-slate-50 rounded-xl border border-slate-200">
                <input type="hidden" name="is_active" value="0">
                <input type="checkbox" id="is_active" name="is_active" value="1"
                       {{ old('is_active', $jenisPelanggaran->is_active) ? 'checked' : '' }}
                       class="w-4 h-4 text-secondary border-slate-300 rounded focus:ring-secondary">
                <label for="is_active" class="text-sm font-medium text-slate-700">Aktif</label>
            </div>

            @if($jenisPelanggaran->pelanggaran()->exists())
            <div class="p-3 bg-amber-50 border border-amber-200 rounded-xl">
                <p class="text-xs text-amber-700">
                    ⚠ Jenis ini sudah digunakan di
                    <strong>{{ $jenisPelanggaran->pelanggaran()->count() }}</strong> catatan pelanggaran.
                    Perubahan poin tidak akan mempengaruhi catatan yang sudah ada.
                </p>
            </div>
            @endif

            <div class="flex gap-3">
                <button type="submit" class="btn-primary flex-1 justify-center">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/>
                    </svg>
                    Simpan Perubahan
                </button>
                <a href="{{ route('admin.jenis-pelanggaran.index') }}" class="btn-secondary">Batal</a>
            </div>
        </div>
    </form>
</div>
@endsection
