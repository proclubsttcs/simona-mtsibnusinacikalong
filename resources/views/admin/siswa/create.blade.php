@extends('layouts.app')

@section('title', isset($siswa) ? 'Edit Siswa' : 'Tambah Siswa')
@section('page-title', isset($siswa) ? 'Edit Data Siswa' : 'Tambah Siswa Baru')

@section('breadcrumb')
<span class="text-slate-400">/</span>
<a href="{{ route('admin.siswa.index') }}" class="hover:text-secondary transition-colors">Data Siswa</a>
<span class="text-slate-400">/</span>
<span class="text-slate-500">{{ isset($siswa) ? 'Edit' : 'Tambah' }}</span>
@endsection

@section('content')
<div class="max-w-3xl mx-auto">

    <form
        action="{{ isset($siswa) ? route('admin.siswa.update', $siswa) : route('admin.siswa.store') }}"
        method="POST"
        enctype="multipart/form-data"
        x-data="{
            fotoPreview: '{{ isset($siswa) && $siswa->foto ? asset('storage/'.$siswa->foto) : '' }}',
            handleFoto(e) {
                const file = e.target.files[0];
                if (file) {
                    const reader = new FileReader();
                    reader.onload = (ev) => this.fotoPreview = ev.target.result;
                    reader.readAsDataURL(file);
                }
            }
        }"
    >
        @csrf
        @if(isset($siswa)) @method('PUT') @endif

        <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">

            {{-- ── Kolom Kiri: Foto ─────────────────────────────── --}}
            <div class="lg:col-span-1">
                <div class="card text-center">
                    <h3 class="font-heading font-bold text-slate-700 mb-4">Foto Siswa</h3>

                    {{-- Preview Foto --}}
                    <div class="relative w-36 h-36 mx-auto mb-4">
                        <img
                            :src="fotoPreview || 'https://ui-avatars.com/api/?name={{ isset($siswa) ? urlencode($siswa->nama) : 'S' }}&background=0891B2&color=fff&size=144'"
                            alt="Preview foto"
                            class="w-36 h-36 rounded-2xl object-cover border-2 border-slate-200">

                        {{-- Tombol upload overlay --}}
                        <label for="foto"
                               class="absolute inset-0 flex items-center justify-center rounded-2xl
                                      bg-slate-900/0 hover:bg-slate-900/40 cursor-pointer
                                      transition-all duration-200 group">
                            <div class="opacity-0 group-hover:opacity-100 transition-opacity">
                                <svg class="w-8 h-8 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                        d="M3 9a2 2 0 012-2h.93a2 2 0 001.664-.89l.812-1.22A2 2 0 0110.07 4h3.86a2 2 0 011.664.89l.812 1.22A2 2 0 0018.07 7H19a2 2 0 012 2v9a2 2 0 01-2 2H5a2 2 0 01-2-2V9z"/>
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                        d="M15 13a3 3 0 11-6 0 3 3 0 016 0z"/>
                                </svg>
                            </div>
                        </label>
                    </div>

                    <input type="file" id="foto" name="foto"
                           accept="image/*" class="hidden"
                           @change="handleFoto($event)">

                    <label for="foto"
                           class="btn-secondary text-xs cursor-pointer inline-flex">
                        <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-8l-4-4m0 0L8 8m4-4v12"/>
                        </svg>
                        Upload Foto
                    </label>
                    <p class="text-xs text-slate-400 mt-2">JPG, PNG, WEBP. Maks 2MB</p>

                    @error('foto')
                    <p class="form-error justify-center mt-2">{{ $message }}</p>
                    @enderror
                </div>
            </div>

            {{-- ── Kolom Kanan: Form Data ───────────────────────── --}}
            <div class="lg:col-span-2 space-y-5">

                {{-- Data Utama --}}
                <div class="card">
                    <h3 class="font-heading font-bold text-slate-700 mb-4">Data Utama</h3>

                    <div class="grid grid-cols-2 gap-4">
                        {{-- NIS --}}
                        <div class="col-span-2 sm:col-span-1">
                            <label class="form-label">NIS <span class="text-danger">*</span></label>
                            <input type="text" name="nis"
                                   value="{{ old('nis', $siswa->nis ?? '') }}"
                                   placeholder="Contoh: 2024001"
                                   class="form-input {{ $errors->has('nis') ? 'border-danger ring-2 ring-danger/20' : '' }}">
                            @error('nis') <p class="form-error">{{ $message }}</p> @enderror
                        </div>

                        {{-- Jenis Kelamin --}}
                        <div class="col-span-2 sm:col-span-1">
                            <label class="form-label">Jenis Kelamin <span class="text-danger">*</span></label>
                            <select name="jenis_kelamin"
                                    class="form-input {{ $errors->has('jenis_kelamin') ? 'border-danger ring-2 ring-danger/20' : '' }}">
                                <option value="">Pilih</option>
                                <option value="L" {{ old('jenis_kelamin', $siswa->jenis_kelamin ?? '') == 'L' ? 'selected' : '' }}>Laki-laki</option>
                                <option value="P" {{ old('jenis_kelamin', $siswa->jenis_kelamin ?? '') == 'P' ? 'selected' : '' }}>Perempuan</option>
                            </select>
                            @error('jenis_kelamin') <p class="form-error">{{ $message }}</p> @enderror
                        </div>

                        {{-- Nama --}}
                        <div class="col-span-2">
                            <label class="form-label">Nama Lengkap <span class="text-danger">*</span></label>
                            <input type="text" name="nama"
                                   value="{{ old('nama', $siswa->nama ?? '') }}"
                                   placeholder="Nama lengkap siswa"
                                   class="form-input {{ $errors->has('nama') ? 'border-danger ring-2 ring-danger/20' : '' }}">
                            @error('nama') <p class="form-error">{{ $message }}</p> @enderror
                        </div>

                        {{-- Kelas --}}
                        <div>
                            <label class="form-label">Kelas <span class="text-danger">*</span></label>
                            <select name="kelas"
                                    class="form-input {{ $errors->has('kelas') ? 'border-danger ring-2 ring-danger/20' : '' }}">
                                <option value="">Pilih Kelas</option>
                                @foreach($kelasList as $kls)
                                <option value="{{ $kls }}" {{ old('kelas', $siswa->kelas ?? '') == $kls ? 'selected' : '' }}>
                                    {{ $kls }}
                                </option>
                                @endforeach
                            </select>
                            @error('kelas') <p class="form-error">{{ $message }}</p> @enderror
                        </div>

                        {{-- Wali Kelas --}}
                        <div>
                            <label class="form-label">Wali Kelas <span class="text-danger">*</span></label>
                            <select name="user_id"
                                    class="form-input {{ $errors->has('user_id') ? 'border-danger ring-2 ring-danger/20' : '' }}">
                                <option value="">Pilih Wali Kelas</option>
                                @foreach($waliKelasList as $wk)
                                <option value="{{ $wk->id }}" {{ old('user_id', $siswa->user_id ?? '') == $wk->id ? 'selected' : '' }}>
                                    {{ $wk->name }} ({{ $wk->kelas }})
                                </option>
                                @endforeach
                            </select>
                            @error('user_id') <p class="form-error">{{ $message }}</p> @enderror
                        </div>
                    </div>
                </div>

                {{-- Data Orang Tua --}}
                <div class="card">
                    <h3 class="font-heading font-bold text-slate-700 mb-4">Data Orang Tua / Wali</h3>

                    <div class="space-y-4">
                        {{-- Nama Ortu --}}
                        <div>
                            <label class="form-label">Nama Orang Tua / Wali <span class="text-danger">*</span></label>
                            <input type="text" name="nama_orang_tua"
                                   value="{{ old('nama_orang_tua', $siswa->nama_orang_tua ?? '') }}"
                                   placeholder="Nama lengkap orang tua"
                                   class="form-input {{ $errors->has('nama_orang_tua') ? 'border-danger ring-2 ring-danger/20' : '' }}">
                            @error('nama_orang_tua') <p class="form-error">{{ $message }}</p> @enderror
                        </div>

                        {{-- No HP --}}
                        <div>
                            <label class="form-label">Nomor HP Orang Tua <span class="text-danger">*</span></label>
                            <input type="tel" name="no_hp_orang_tua"
                                   value="{{ old('no_hp_orang_tua', $siswa->no_hp_orang_tua ?? '') }}"
                                   placeholder="08xxxxxxxxxx"
                                   class="form-input {{ $errors->has('no_hp_orang_tua') ? 'border-danger ring-2 ring-danger/20' : '' }}">
                            @error('no_hp_orang_tua') <p class="form-error">{{ $message }}</p> @enderror
                        </div>

                        {{-- Alamat --}}
                        <div>
                            <label class="form-label">Alamat <span class="text-danger">*</span></label>
                            <textarea name="alamat" rows="3"
                                      placeholder="Alamat lengkap siswa"
                                      class="form-input resize-none {{ $errors->has('alamat') ? 'border-danger ring-2 ring-danger/20' : '' }}">{{ old('alamat', $siswa->alamat ?? '') }}</textarea>
                            @error('alamat') <p class="form-error">{{ $message }}</p> @enderror
                        </div>
                    </div>
                </div>

                {{-- Tombol Submit --}}
                <div class="flex gap-3">
                    <button type="submit" class="btn-primary flex-1 justify-center">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/>
                        </svg>
                        {{ isset($siswa) ? 'Simpan Perubahan' : 'Tambah Siswa' }}
                    </button>
                    <a href="{{ route('admin.siswa.index') }}" class="btn-secondary">Batal</a>
                </div>
            </div>
        </div>
    </form>
</div>
@endsection
