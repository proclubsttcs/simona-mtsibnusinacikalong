@extends('layouts.app')

@section('title', 'Edit Akun — ' . $user->name)
@section('page-title', 'Edit Akun')

@section('breadcrumb')
<span class="text-slate-400">/</span>
<a href="{{ route('admin.users.index') }}" class="hover:text-secondary transition-colors">Akun Pengguna</a>
<span class="text-slate-400">/</span>
<a href="{{ route('admin.users.show', $user) }}" class="hover:text-secondary transition-colors truncate max-w-[120px]">{{ $user->name }}</a>
<span class="text-slate-400">/</span>
<span class="text-slate-500">Edit</span>
@endsection

@section('content')
<div class="max-w-2xl mx-auto">
    <form action="{{ route('admin.users.update', $user) }}" method="POST" enctype="multipart/form-data"
          x-data="{ role: '{{ old('role', $user->role) }}', showPass: false, showConfirm: false }">
        @csrf
        @method('PUT')

        <div class="space-y-5">
            <div class="card">
                <h3 class="font-heading font-bold text-slate-700 mb-5">Informasi Akun</h3>
                <div class="space-y-4">

                    <div>
                        <label class="form-label">Nama Lengkap <span class="text-danger">*</span></label>
                        <input type="text" name="name" value="{{ old('name', $user->name) }}"
                               class="form-input {{ $errors->has('name') ? 'border-danger ring-2 ring-danger/20' : '' }}">
                        @error('name') <p class="form-error">{{ $message }}</p> @enderror
                    </div>

                    <div>
                        <label class="form-label">Email <span class="text-danger">*</span></label>
                        <input type="email" name="email" value="{{ old('email', $user->email) }}"
                               class="form-input {{ $errors->has('email') ? 'border-danger ring-2 ring-danger/20' : '' }}">
                        @error('email') <p class="form-error">{{ $message }}</p> @enderror
                    </div>

                    <div>
                        <label class="form-label">Role <span class="text-danger">*</span></label>
                        <select name="role" x-model="role"
                                class="form-input {{ $errors->has('role') ? 'border-danger ring-2 ring-danger/20' : '' }}"
                                {{ $user->id === auth()->id() ? 'disabled' : '' }}>
                            <option value="wali_kelas" {{ old('role', $user->role) == 'wali_kelas' ? 'selected' : '' }}>Wali Kelas</option>
                            <option value="admin" {{ old('role', $user->role) == 'admin' ? 'selected' : '' }}>Admin / BK</option>
                        </select>
                        {{-- Kirim role via hidden jika disabled --}}
                        @if($user->id === auth()->id())
                        <input type="hidden" name="role" value="{{ $user->role }}">
                        <p class="text-xs text-slate-400 mt-1">Anda tidak dapat mengubah role akun sendiri.</p>
                        @endif
                        @error('role') <p class="form-error">{{ $message }}</p> @enderror
                    </div>

                    <div x-show="role === 'wali_kelas'" x-transition>
                        <label class="form-label">Kelas yang Diampu <span class="text-danger">*</span></label>
                        <select name="kelas" class="form-input {{ $errors->has('kelas') ? 'border-danger ring-2 ring-danger/20' : '' }}">
                            <option value="">Pilih Kelas</option>
                            @foreach($kelasList as $kelas)
                            <option value="{{ $kelas }}" {{ old('kelas', $user->kelas) == $kelas ? 'selected' : '' }}>{{ $kelas }}</option>
                            @endforeach
                        </select>
                        @error('kelas') <p class="form-error">{{ $message }}</p> @enderror
                    </div>

                    {{-- Status Aktif --}}
                    @if($user->id !== auth()->id())
                    <div class="flex items-center gap-3 p-3 bg-slate-50 rounded-xl">
                        <input type="hidden" name="is_active" value="0">
                        <input type="checkbox" id="is_active" name="is_active" value="1"
                               {{ old('is_active', $user->is_active) ? 'checked' : '' }}
                               class="w-4 h-4 text-secondary border-slate-300 rounded focus:ring-secondary">
                        <label for="is_active" class="text-sm font-medium text-slate-700">
                            Akun Aktif
                        </label>
                    </div>
                    @endif
                </div>
            </div>

            {{-- Reset Password (opsional) --}}
            <div class="card">
                <h3 class="font-heading font-bold text-slate-700 mb-1">Reset Password</h3>
                <p class="text-sm text-slate-500 mb-5">Kosongkan jika tidak ingin mengubah password.</p>

                <div class="space-y-4">
                    <div>
                        <label class="form-label">Password Baru</label>
                        <div class="relative">
                            <input :type="showPass ? 'text' : 'password'" name="password"
                                   placeholder="Kosongkan jika tidak diubah"
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
                        <label class="form-label">Konfirmasi Password Baru</label>
                        <div class="relative">
                            <input :type="showConfirm ? 'text' : 'password'" name="password_confirmation"
                                   placeholder="Ulangi password baru"
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
                </div>
            </div>

            <div class="flex gap-3">
                <button type="submit" class="btn-primary flex-1 justify-center">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/>
                    </svg>
                    Simpan Perubahan
                </button>
                <a href="{{ route('admin.users.show', $user) }}" class="btn-secondary">Batal</a>
            </div>
        </div>
    </form>
</div>
@endsection
