@extends('layouts.app')

@section('title', 'Ganti Password')
@section('page-title', 'Ganti Password')

@section('content')
<div class="max-w-lg mx-auto">

    {{-- Header peringatan --}}
    @if(auth()->user()->must_change_password)
    <div class="mb-6 p-4 bg-amber-50 border border-amber-200 rounded-2xl flex items-start gap-3">
        <svg class="w-5 h-5 text-amber-500 flex-shrink-0 mt-0.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"/>
        </svg>
        <div>
            <p class="text-sm font-semibold text-amber-700">Wajib Ganti Password</p>
            <p class="text-sm text-amber-600 mt-0.5">
                Anda menggunakan password default. Silakan buat password baru yang kuat sebelum melanjutkan.
            </p>
        </div>
    </div>
    @endif

    <div class="card">
        <div class="mb-6">
            <h3 class="font-heading text-xl font-bold text-slate-800">
                {{ auth()->user()->must_change_password ? 'Buat Password Baru' : 'Ubah Password' }}
            </h3>
            <p class="text-sm text-slate-500 mt-1">
                Password minimal 8 karakter. Gunakan kombinasi huruf, angka, dan simbol.
            </p>
        </div>

        <form action="{{ route('password.change.update') }}" method="POST" class="space-y-5" x-data="{ show: { lama: false, baru: false, konfirmasi: false } }">
            @csrf

            {{-- Password Lama (hanya jika bukan first-time) --}}
            @if(!auth()->user()->must_change_password)
            <div>
                <label class="form-label">Password Lama</label>
                <div class="relative">
                    <input
                        :type="show.lama ? 'text' : 'password'"
                        name="password_lama"
                        placeholder="Masukkan password lama"
                        class="form-input pr-10 {{ $errors->has('password_lama') ? 'border-danger ring-2 ring-danger/20' : '' }}">
                    <button type="button" @click="show.lama = !show.lama"
                        class="absolute inset-y-0 right-0 pr-3.5 flex items-center text-slate-400 hover:text-slate-600">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M15 12a3 3 0 11-6 0 3 3 0 016 0z M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/>
                        </svg>
                    </button>
                </div>
                @error('password_lama')
                <p class="form-error">{{ $message }}</p>
                @enderror
            </div>
            @endif

            {{-- Password Baru --}}
            <div>
                <label class="form-label">Password Baru</label>
                <div class="relative">
                    <input
                        :type="show.baru ? 'text' : 'password'"
                        name="password_baru"
                        placeholder="Minimal 8 karakter"
                        class="form-input pr-10 {{ $errors->has('password_baru') ? 'border-danger ring-2 ring-danger/20' : '' }}">
                    <button type="button" @click="show.baru = !show.baru"
                        class="absolute inset-y-0 right-0 pr-3.5 flex items-center text-slate-400 hover:text-slate-600">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M15 12a3 3 0 11-6 0 3 3 0 016 0z M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/>
                        </svg>
                    </button>
                </div>
                @error('password_baru')
                <p class="form-error">{{ $message }}</p>
                @enderror
            </div>

            {{-- Konfirmasi Password Baru --}}
            <div>
                <label class="form-label">Konfirmasi Password Baru</label>
                <div class="relative">
                    <input
                        :type="show.konfirmasi ? 'text' : 'password'"
                        name="password_baru_confirmation"
                        placeholder="Ulangi password baru"
                        class="form-input pr-10 {{ $errors->has('password_baru_confirmation') ? 'border-danger ring-2 ring-danger/20' : '' }}">
                    <button type="button" @click="show.konfirmasi = !show.konfirmasi"
                        class="absolute inset-y-0 right-0 pr-3.5 flex items-center text-slate-400 hover:text-slate-600">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M15 12a3 3 0 11-6 0 3 3 0 016 0z M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/>
                        </svg>
                    </button>
                </div>
            </div>

            {{-- Tips keamanan --}}
            <div class="p-4 bg-slate-50 rounded-xl border border-slate-200">
                <p class="text-xs font-semibold text-slate-600 mb-2">Tips password kuat:</p>
                <ul class="text-xs text-slate-500 space-y-1">
                    <li class="flex items-center gap-1.5">
                        <div class="w-1 h-1 bg-slate-400 rounded-full"></div>
                        Minimal 8 karakter
                    </li>
                    <li class="flex items-center gap-1.5">
                        <div class="w-1 h-1 bg-slate-400 rounded-full"></div>
                        Kombinasi huruf besar, kecil, dan angka
                    </li>
                    <li class="flex items-center gap-1.5">
                        <div class="w-1 h-1 bg-slate-400 rounded-full"></div>
                        Tambahkan simbol (!, @, #, dll.) untuk keamanan ekstra
                    </li>
                </ul>
            </div>

            <div class="flex gap-3 pt-2">
                <button type="submit" class="btn-primary flex-1 justify-center">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M5 13l4 4L19 7"/>
                    </svg>
                    Simpan Password Baru
                </button>

                @if(!auth()->user()->must_change_password)
                <a href="{{ route('dashboard') }}" class="btn-secondary">Batal</a>
                @endif
            </div>
        </form>
    </div>
</div>
@endsection
