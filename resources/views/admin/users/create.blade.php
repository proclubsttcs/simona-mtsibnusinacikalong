@extends('layouts.app')

@section('title', 'Tambah Akun')
@section('page-title', 'Tambah Akun Baru')

@section('breadcrumb')
<span class="text-slate-400">/</span>
<a href="{{ route('admin.users.index') }}" class="hover:text-secondary transition-colors">Akun Pengguna</a>
<span class="text-slate-400">/</span>
<span class="text-slate-500">Tambah</span>
@endsection

@section('content')
<div class="max-w-2xl mx-auto">
    <form action="{{ route('admin.users.store') }}" method="POST" enctype="multipart/form-data"
          x-data="{ role: '{{ old('role', 'wali_kelas') }}', showPass: false, showConfirm: false }">
        @csrf

        <div class="space-y-5">
            <div class="card">
                <h3 class="font-heading font-bold text-slate-700 mb-5">Informasi Akun</h3>

                <div class="space-y-4">
                    {{-- Nama --}}
                    <div>
                        <label class="form-label">Nama Lengkap <span class="text-danger">*</span></label>
                        <input type="text" name="name" value="{{ old('name') }}"
                               placeholder="Nama lengkap pengguna"
                               class="form-input {{ $errors->has('name') ? 'border-danger ring-2 ring-danger/20' : '' }}">
                        @error('name') <p class="form-error">{{ $message }}</p> @enderror
                    </div>

                    {{-- Email --}}
                    <div>
                        <label class="form-label">Email <span class="text-danger">*</span></label>
                        <input type="email" name="email" value="{{ old('email') }}"
                               placeholder="email@simon.sch.id"
                               class="form-input {{ $errors->has('email') ? 'border-danger ring-2 ring-danger/20' : '' }}">
                        @error('email') <p class="form-error">{{ $message }}</p> @enderror
                    </div>

                    {{-- Role --}}
                    <div>
                        <label class="form-label">Role <span class="text-danger">*</span></label>
                        <select name="role" x-model="role" class="form-input {{ $errors->has('role') ? 'border-danger ring-2 ring-danger/20' : '' }}">
                            <option value="wali_kelas">Wali Kelas</option>
                            <option value="admin">Admin / BK</option>
                        </select>
                        @error('role') <p class="form-error">{{ $message }}</p> @enderror
                    </div>

                    {{-- Kelas (hanya jika wali_kelas) --}}
                    <div x-show="role === 'wali_kelas'" x-transition>
                        <label class="form-label">Kelas yang Diampu <span class="text-danger">*</span></label>
                        <select name="kelas" class="form-input {{ $errors->has('kelas') ? 'border-danger ring-2 ring-danger/20' : '' }}">
                            <option value="">Pilih Kelas</option>
                            @foreach($kelasList as $kelas)
                            <option value="{{ $kelas }}" {{ old('kelas') == $kelas ? 'selected' : '' }}>{{ $kelas }}</option>
                            @endforeach
                        </select>
                        @error('kelas') <p class="form-error">{{ $message }}</p> @enderror
                    </div>
                </div>
            </div>

            <div class="card">
                <h3 class="font-heading font-bold text-slate-700 mb-5">Password</h3>

                <div class="space-y-4">
                    <div>
                        <label class="form-label">Password <span class="text-danger">*</span></label>
                        <div class="relative">
                            <input :type="showPass ? 'text' : 'password'" name="password"
                                   placeholder="Minimal 8 karakter"
                                   class="form-input pr-10 {{ $errors->has('password') ? 'border-danger ring-2 ring-danger/20' : '' }}">
                            <button type="button" @click="showPass = !showPass"
                                class="absolute inset-y-0 right-0 pr-3.5 flex items-center text-slate-400 hover:text-slate-600">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                        d="M15 12a3 3 0 11-6 0 3 3 0 016 0z M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/>
                                </svg>
                            </button>
                        </div>
                        @error('password') <p class="form-error">{{ $message }}</p> @enderror
                    </div>

                    <div>
                        <label class="form-label">Konfirmasi Password <span class="text-danger">*</span></label>
                        <div class="relative">
                            <input :type="showConfirm ? 'text' : 'password'" name="password_confirmation"
                                   placeholder="Ulangi password"
                                   class="form-input pr-10">
                            <button type="button" @click="showConfirm = !showConfirm"
                                class="absolute inset-y-0 right-0 pr-3.5 flex items-center text-slate-400 hover:text-slate-600">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                        d="M15 12a3 3 0 11-6 0 3 3 0 016 0z M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/>
                                </svg>
                            </button>
                        </div>
                    </div>

                    <div x-show="role === 'wali_kelas'"
                         class="p-3 bg-amber-50 border border-amber-200 rounded-xl">
                        <p class="text-xs text-amber-700">
                            <span class="font-semibold">Info:</span>
                            Wali kelas akan diminta mengganti password saat pertama kali login.
                        </p>
                    </div>
                </div>
            </div>

            <div class="flex gap-3">
                <button type="submit" class="btn-primary flex-1 justify-center">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M18 9v3m0 0v3m0-3h3m-3 0h-3m-2-5a4 4 0 11-8 0 4 4 0 018 0zM3 20a6 6 0 0112 0v1H3v-1z"/>
                    </svg>
                    Buat Akun
                </button>
                <a href="{{ route('admin.users.index') }}" class="btn-secondary">Batal</a>
            </div>
        </div>
    </form>
</div>
@endsection
