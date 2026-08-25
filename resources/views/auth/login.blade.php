<!DOCTYPE html>
<html lang="id" class="h-full">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login — SiMON MTs Ibnu Sina</title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;500;600;700;800&family=Inter:wght@300;400;500;600&display=swap" rel="stylesheet">
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="h-full bg-[#F8FAF6] font-body">

<div class="min-h-screen flex">

    {{-- ── Panel Kiri — Branding dengan foto gerbang sekolah ───── --}}
    <div class="hidden lg:flex lg:w-1/2 relative overflow-hidden flex-col items-center justify-center p-12">

        {{-- Foto latar gerbang sekolah --}}
        <div class="absolute inset-0">
            <img src="{{ asset('images/ai.png') }}" alt="Gerbang MTs Ibnu Sina Cikalong"
                 class="w-full h-full object-cover">
            {{-- Overlay gradasi hijau supaya teks tetap terbaca dan senada dengan logo --}}
            <div class="absolute inset-0 bg-gradient-to-br from-[#0F3D24]/95 via-[#15532D]/90 to-[#1E6B3A]/15"></div>
        </div>

        {{-- Dekorasi lingkaran --}}
        <div class="absolute top-0 right-0 w-96 h-96 bg-white/5 rounded-full -translate-y-1/2 translate-x-1/2"></div>
        <div class="absolute bottom-0 left-0 w-72 h-72 bg-white/5 rounded-full translate-y-1/3 -translate-x-1/3"></div>
        
        {{-- Konten branding --}}
        <div class="relative z-10 text-center">
            {{-- Logo asli madrasah --}}
            <div class="w-28 h-28 rounded-3xl bg-white/95 backdrop-blur-sm flex items-center justify-center mx-auto border border-white/30 shadow-lg p-1">
                <img src="{{ asset('images/mtsibnusina.png') }}" alt="Logo MTs Ibnu Sina Cikalong" class="w-full h-full object-contain">
            </div>

            <h1 class="font-heading text-5xl font-extrabold text-white mb-3">SiMON</h1>
            <p class="text-[#D4EAD9] text-lg font-medium mb-2">Sistem Monitoring Pelanggaran Siswa</p>
            <p class="text-[#D4EAD9]/60 text-sm">MTs Ibnu Sina Cikalong </p>

            {{-- Fitur key points --}}
            <div class="mt-12 space-y-4 text-left">
                @foreach([
                    ['icon' => 'M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2m-6 9l2 2 4-4', 'text' => 'Pencatatan pelanggaran terstruktur'],
                    ['icon' => 'M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z', 'text' => 'Surat peringatan otomatis (SP1/SP2/SP3)'],
                    ['icon' => 'M9 17v-2m3 2v-4m3 4v-6m2 10H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z', 'text' => 'Laporan & ekspor data Excel/PDF'],
                ] as $item)
                <div class="flex items-center gap-3">
                    <div class="w-8 h-8 rounded-xl bg-[#D4AF37]/20 flex items-center justify-center flex-shrink-0">
                        <svg class="w-4 h-4 text-[#F0D878]" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="{{ $item['icon'] }}"/>
                        </svg>
                    </div>
                    <p class="text-[#E5F0E8]/90 text-sm">{{ $item['text'] }}</p>
                </div>
                @endforeach
            </div>
        </div>
    </div>

    {{-- ── Panel Kanan — Form Login ────────────────────────────── --}}
    <div class="flex-1 flex items-center justify-center p-6 sm:p-12 bg-[#F8FAF6]">
        <div class="w-full max-w-md">

            {{-- Header mobile --}}
            <div class="lg:hidden text-center mb-10">
                <div class="w-20 h-20 rounded-2xl bg-white flex items-center justify-center mx-auto mb-4 shadow-md border border-[#15532D]/10 p-2.5">
                    <img src="{{ asset('images/mtsibnusina.png') }}" alt="Logo MTs Ibnu Sina Cikalong" class="w-full h-full object-contain">
                </div>
                <h1 class="font-heading text-3xl font-extrabold text-[#15532D]">SiMON</h1>
                <p class="text-slate-500 text-sm mt-1">MTs Ibnu Sina Cikalong, Kab. Tasikmalaya</p>
            </div>

            {{-- Form Card --}}
            <div class="bg-white rounded-3xl shadow-card p-8 sm:p-10 border border-[#15532D]/5">
                <h2 class="font-heading text-2xl font-bold text-slate-800 mb-1">Selamat Datang</h2>
                <p class="text-slate-400 text-sm mb-8">Masuk dengan akun yang telah didaftarkan</p>

                {{-- Error validasi global --}}
                @if($errors->any())
                <div class="mb-6 p-4 bg-red-50 border border-red-200 rounded-xl flex items-start gap-3">
                    <svg class="w-5 h-5 text-danger flex-shrink-0 mt-0.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
                    </svg>
                    <div>
                        @foreach($errors->all() as $error)
                        <p class="text-sm text-red-600">{{ $error }}</p>
                        @endforeach
                    </div>
                </div>
                @endif

                <form action="{{ route('login.post') }}" method="POST" class="space-y-5">
                    @csrf

                    {{-- Email --}}
                    <div>
                        <label for="email" class="form-label">Email</label>
                        <div class="relative">
                            <div class="absolute inset-y-0 left-0 pl-3.5 flex items-center pointer-events-none">
                                <svg class="w-4 h-4 text-slate-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                        d="M16 12a4 4 0 10-8 0 4 4 0 008 0zm0 0v1.5a2.5 2.5 0 005 0V12a9 9 0 10-9 9m4.5-1.206a8.959 8.959 0 01-4.5 1.207"/>
                                </svg>
                            </div>
                            <input
                                id="email"
                                type="email"
                                name="email"
                                value="{{ old('email') }}"
                                placeholder="nama@simon.sch.id"
                                autocomplete="email"
                                class="form-input pl-10 {{ $errors->has('email') ? 'border-danger ring-2 ring-danger/20' : '' }}"
                                required autofocus>
                        </div>
                        @error('email')
                        <p class="form-error">
                            <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
                            </svg>
                            {{ $message }}
                        </p>
                        @enderror
                    </div>

                    {{-- Password --}}
                    <div x-data="{ show: false }">
                        <label for="password" class="form-label">Password</label>
                        <div class="relative">
                            <div class="absolute inset-y-0 left-0 pl-3.5 flex items-center pointer-events-none">
                                <svg class="w-4 h-4 text-slate-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                        d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z"/>
                                </svg>
                            </div>
                            <input
                                id="password"
                                :type="show ? 'text' : 'password'"
                                name="password"
                                placeholder="Masukkan password"
                                autocomplete="current-password"
                                class="form-input pl-10 pr-10 {{ $errors->has('password') ? 'border-danger ring-2 ring-danger/20' : '' }}"
                                required>
                            <button type="button" @click="show = !show"
                                class="absolute inset-y-0 right-0 pr-3.5 flex items-center text-slate-400 hover:text-slate-600">
                                <svg x-show="!show" class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                        d="M15 12a3 3 0 11-6 0 3 3 0 016 0z M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/>
                                </svg>
                                <svg x-show="show" class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24" style="display:none;">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                        d="M13.875 18.825A10.05 10.05 0 0112 19c-4.478 0-8.268-2.943-9.543-7a9.97 9.97 0 011.563-3.029m5.858.908a3 3 0 114.243 4.243M9.878 9.878l4.242 4.242M9.88 9.88l-3.29-3.29m7.532 7.532l3.29 3.29M3 3l3.59 3.59m0 0A9.953 9.953 0 0112 5c4.478 0 8.268 2.943 9.543 7a10.025 10.025 0 01-4.132 5.411m0 0L21 21"/>
                                </svg>
                            </button>
                        </div>
                        @error('password')
                        <p class="form-error">
                            <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
                            </svg>
                            {{ $message }}
                        </p>
                        @enderror
                    </div>

                    {{-- Submit Button --}}
                    <button type="submit"
                        class="w-full py-3 bg-gradient-to-r from-[#15532D] to-[#1E6B3A] text-white font-semibold rounded-xl
                               shadow-sm hover:opacity-90 active:opacity-80
                               transition-all duration-200 font-heading text-base
                               focus:outline-none focus:ring-2 focus:ring-[#D4AF37]/50">
                        Masuk ke SiMON
                    </button>
                </form>
            </div>

            <p class="text-center text-xs text-slate-400 mt-6">
                Lupa password? Hubungi Administrator BK
            </p>
        </div>
    </div>
</div>

</body>
</html>