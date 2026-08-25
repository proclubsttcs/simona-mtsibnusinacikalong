<!DOCTYPE html>
<html lang="id" class="h-full">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>@yield('title', 'Dashboard') — SiMON MTs Ibnu Sina</title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;500;600;700;800&family=Inter:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    <script src="https://cdn.jsdelivr.net/npm/chart.js@4.4.0/dist/chart.umd.min.js" defer></script>
    @stack('styles')
</head>
<body class="h-full bg-[#F8FAFC] font-body" x-data="sidebar()">

{{-- Overlay mobile --}}
<div x-show="open" @click="close()"
     x-transition:enter="transition-opacity duration-300"
     x-transition:enter-start="opacity-0" x-transition:enter-end="opacity-100"
     x-transition:leave="transition-opacity duration-300"
     x-transition:leave-start="opacity-100" x-transition:leave-end="opacity-0"
     class="fixed inset-0 z-20 bg-slate-900/50 lg:hidden" style="display:none;"></div>

{{-- Sidebar --}}
<aside :class="open ? 'translate-x-0' : '-translate-x-full lg:translate-x-0'"
       class="fixed inset-y-0 left-0 z-30 w-64 flex flex-col bg-sidebar-gradient shadow-sidebar transition-transform duration-300 ease-in-out">

    {{-- Logo --}}
    <div class="flex items-center gap-3 px-5 py-5 border-b border-white/10">
        <div class="w-10 h-10 rounded-xl bg-white/15 flex items-center justify-center flex-shrink-0">
            <svg class="w-6 h-6 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                    d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z"/>
            </svg>
        </div>
        <div>
            <h1 class="text-white font-heading font-bold text-lg leading-none">SiMON</h1>
            <p class="text-green-200/60 text-xs mt-0.5">MTs Ibnu Sina</p>
        </div>
    </div>

    {{-- Navigasi --}}
    <nav class="flex-1 overflow-y-auto px-3 py-4 space-y-1">
        @php
            $user = auth()->user();
            $isAdmin = $user->isAdmin();
            $cur = Route::currentRouteName();
        @endphp

        {{-- Dashboard --}}
        <a href="{{ route('dashboard') }}"
           class="nav-item {{ Str::startsWith($cur, 'dashboard') ? 'active' : '' }}">
            <svg class="w-5 h-5 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                    d="M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6"/>
            </svg>
            <span>Dashboard</span>
        </a>

        @if($isAdmin)
        {{-- ═══ ADMIN ═══ --}}
        <p class="text-green-200/40 text-[10px] font-bold uppercase tracking-widest px-3 pt-4 pb-1">Manajemen Data</p>

        <a href="{{ route('admin.siswa.index') }}"
           class="nav-item {{ Str::startsWith($cur, 'admin.siswa') ? 'active' : '' }}">
            <svg class="w-5 h-5 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                    d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0z"/>
            </svg>
            <span>Data Siswa</span>
        </a>

        <a href="{{ route('admin.users.index') }}"
           class="nav-item {{ Str::startsWith($cur, 'admin.users') ? 'active' : '' }}">
            <svg class="w-5 h-5 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                    d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197M13 7a4 4 0 11-8 0 4 4 0 018 0z"/>
            </svg>
            <span>Akun Wali Kelas</span>
        </a>

        <p class="text-green-200/40 text-[10px] font-bold uppercase tracking-widest px-3 pt-4 pb-1">Pelanggaran</p>

        {{-- Konfirmasi Pelanggaran --}}
        <a href="{{ route('admin.pelanggaran.index') }}"
           class="nav-item {{ Str::startsWith($cur, 'admin.pelanggaran') ? 'active' : '' }}">
            <svg class="w-5 h-5 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                    d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2m-6 9l2 2 4-4"/>
            </svg>
            <span>Konfirmasi Pelanggaran</span>
            @php $menunggu = \App\Models\Pelanggaran::menunggu()->count(); @endphp
            @if($menunggu > 0)
            <span class="ml-auto bg-amber-400 text-white text-[10px] font-bold px-1.5 py-0.5 rounded-full animate-pulse-soft">
                {{ $menunggu > 99 ? '99+' : $menunggu }}
            </span>
            @endif
        </a>

        {{-- Jenis Pelanggaran --}}
        <a href="{{ route('admin.jenis-pelanggaran.index') }}"
           class="nav-item {{ Str::startsWith($cur, 'admin.jenis-pelanggaran') ? 'active' : '' }}">
            <svg class="w-5 h-5 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                    d="M4 6h16M4 10h16M4 14h16M4 18h16"/>
            </svg>
            <span>Jenis Pelanggaran</span>
        </a>

        <p class="text-green-200/40 text-[10px] font-bold uppercase tracking-widest px-3 pt-4 pb-1">Lainnya</p>

        {{-- Surat Peringatan --}}
        <a href="{{ route('admin.surat-peringatan.index') }}"
           class="nav-item {{ Str::startsWith($cur, 'admin.surat-peringatan') ? 'active' : '' }}">
            <svg class="w-5 h-5 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                    d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/>
            </svg>
            <span>Surat Peringatan</span>
            @php $totalSpAktif = \App\Models\SuratPeringatan::where('status','aktif')->count(); @endphp
            @if($totalSpAktif > 0)
            <span class="ml-auto bg-red-500 text-white text-[10px] font-bold px-1.5 py-0.5 rounded-full">
                {{ $totalSpAktif }}
            </span>
            @endif
        </a>

        {{-- Laporan & Export --}}
        <a href="{{ route('admin.laporan.index') }}"
           class="nav-item {{ Str::startsWith($cur, 'admin.laporan') ? 'active' : '' }}">
            <svg class="w-5 h-5 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                    d="M9 17v-2m3 2v-4m3 4v-6m2 10H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/>
            </svg>
            <span>Laporan & Export</span>
        </a>

        @else
        {{-- ═══ WALI KELAS ═══ --}}
        <p class="text-green-200/40 text-[10px] font-bold uppercase tracking-widest px-3 pt-4 pb-1">Kelas Saya</p>

        <a href="{{ route('wali-kelas.siswa.index') }}"
           class="nav-item {{ Str::startsWith($cur, 'wali-kelas.siswa') ? 'active' : '' }}">
            <svg class="w-5 h-5 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                    d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0z"/>
            </svg>
            <span>Siswa Kelas {{ $user->kelas }}</span>
        </a>

        <a href="{{ route('wali-kelas.pelanggaran.index') }}"
           class="nav-item {{ Str::startsWith($cur, 'wali-kelas.pelanggaran') ? 'active' : '' }}">
            <svg class="w-5 h-5 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                    d="M12 4v16m8-8H4"/>
            </svg>
            <span>Input Pelanggaran</span>
            @php $menungguSaya = \App\Models\Pelanggaran::where('user_id', auth()->id())->menunggu()->count(); @endphp
            @if($menungguSaya > 0)
            <span class="ml-auto bg-amber-400 text-white text-[10px] font-bold px-1.5 py-0.5 rounded-full">
                {{ $menungguSaya }}
            </span>
            @endif
        </a>
        @endif
    </nav>

    {{-- Footer Sidebar --}}
    <div class="px-3 py-4 border-t border-white/10">
        <div class="flex items-center gap-3 px-2">
            <img src="{{ auth()->user()->foto_url }}" alt="{{ auth()->user()->name }}"
                 class="w-9 h-9 rounded-xl object-cover flex-shrink-0 border-2 border-white/20">
            <div class="flex-1 min-w-0">
                <p class="text-white text-sm font-semibold truncate">{{ auth()->user()->name }}</p>
                <p class="text-green-200/50 text-xs">{{ auth()->user()->role_label }}</p>
            </div>
        </div>
        <form action="{{ route('logout') }}" method="POST" class="mt-3">
            @csrf
            <button type="submit"
                class="w-full flex items-center gap-2 px-3 py-2 rounded-xl text-sm
                       text-green-200/60 hover:text-white hover:bg-white/10 transition-all duration-200">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                        d="M17 16l4-4m0 0l-4-4m4 4H7m6 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h4a3 3 0 013 3v1"/>
                </svg>
                Keluar
            </button>
        </form>
    </div>
</aside>

{{-- Konten Utama --}}
<div class="lg:pl-64 flex flex-col min-h-screen">

    {{-- Topbar --}}
    <header class="sticky top-0 z-10 bg-white/80 backdrop-blur-md border-b border-slate-200/80
                   flex items-center justify-between px-4 sm:px-6 h-16">
        <button @click="toggle()"
            class="lg:hidden p-2 rounded-xl text-slate-500 hover:text-slate-700 hover:bg-slate-100 transition-colors">
            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16"/>
            </svg>
        </button>

        <div class="flex-1 lg:flex-none ml-4 lg:ml-0">
            <h2 class="font-heading font-bold text-slate-800 text-lg">@yield('page-title', 'Dashboard')</h2>
            @hasSection('breadcrumb')
            <div class="flex items-center gap-1.5 text-xs text-slate-400 mt-0.5">
                <a href="{{ route('dashboard') }}" class="hover:text-secondary transition-colors">Beranda</a>
                @yield('breadcrumb')
            </div>
            @endif
        </div>

        <div class="flex items-center gap-2 sm:gap-3">
            {{-- Badge notif menunggu (admin) --}}
            @if(auth()->user()->isAdmin())
            @php $totalMenunggu = \App\Models\Pelanggaran::menunggu()->count(); @endphp
            @if($totalMenunggu > 0)
            <a href="{{ route('admin.pelanggaran.index', ['status' => 'menunggu']) }}"
               class="relative p-2 rounded-xl text-slate-500 hover:text-amber-600 hover:bg-amber-50 transition-colors">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                        d="M15 17h5l-1.405-1.405A2.032 2.032 0 0118 14.158V11a6.002 6.002 0 00-4-5.659V5a2 2 0 10-4 0v.341C7.67 6.165 6 8.388 6 11v3.159c0 .538-.214 1.055-.595 1.436L4 17h5m6 0v1a3 3 0 11-6 0v-1m6 0H9"/>
                </svg>
                <span class="absolute top-1 right-1 w-4 h-4 bg-amber-500 text-white text-[9px] font-bold
                             rounded-full flex items-center justify-center animate-pulse-soft">
                    {{ $totalMenunggu > 9 ? '9+' : $totalMenunggu }}
                </span>
            </a>
            @endif
            @endif

            {{-- Profil Dropdown --}}
            <div class="relative" x-data="dropdown()">
                <button @click="toggle()"
                    class="flex items-center gap-2.5 pl-2 pr-3 py-1.5 rounded-xl hover:bg-slate-100 transition-colors">
                    <img src="{{ auth()->user()->foto_url }}" alt="{{ auth()->user()->name }}"
                         class="w-8 h-8 rounded-lg object-cover border border-slate-200">
                    <div class="hidden sm:block text-left">
                        <p class="text-sm font-semibold text-slate-700 leading-none">
                            {{ Str::words(auth()->user()->name, 2, '') }}
                        </p>
                        <p class="text-xs text-slate-400 mt-0.5">{{ auth()->user()->role_label }}</p>
                    </div>
                    <svg class="w-4 h-4 text-slate-400 hidden sm:block" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"/>
                    </svg>
                </button>

                <div x-show="open" @click.away="close()"
                     x-transition:enter="transition ease-out duration-150"
                     x-transition:enter-start="opacity-0 scale-95 -translate-y-1"
                     x-transition:enter-end="opacity-100 scale-100 translate-y-0"
                     x-transition:leave="transition ease-in duration-100"
                     x-transition:leave-start="opacity-100 scale-100"
                     x-transition:leave-end="opacity-0 scale-95"
                     class="absolute right-0 top-full mt-2 w-52 bg-white rounded-2xl shadow-xl border border-slate-100 py-2 z-50"
                     style="display:none;">
                    <div class="px-4 py-2 border-b border-slate-100 mb-1">
                        <p class="text-sm font-semibold text-slate-700">{{ auth()->user()->name }}</p>
                        <p class="text-xs text-slate-400">{{ auth()->user()->email }}</p>
                    </div>
                    <a href="{{ route('password.change') }}"
                       class="flex items-center gap-3 px-4 py-2 text-sm text-slate-600 hover:bg-slate-50 hover:text-secondary transition-colors">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M15 7a2 2 0 012 2m4 0a6 6 0 01-7.743 5.743L11 17H9v2H7v2H4a1 1 0 01-1-1v-2.586a1 1 0 01.293-.707l5.964-5.964A6 6 0 1121 9z"/>
                        </svg>
                        Ganti Password
                    </a>
                    <div class="border-t border-slate-100 mt-1 pt-1">
                        <form action="{{ route('logout') }}" method="POST">
                            @csrf
                            <button type="submit"
                                class="w-full flex items-center gap-3 px-4 py-2 text-sm text-red-500 hover:bg-red-50 transition-colors">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                        d="M17 16l4-4m0 0l-4-4m4 4H7m6 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h4a3 3 0 013 3v1"/>
                                </svg>
                                Keluar
                            </button>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </header>

    {{-- Flash Messages --}}
    @if(session()->hasAny(['success','error','warning']))
    <div id="toast-container" class="fixed top-4 right-4 z-50 space-y-3">
        @foreach(['success' => 'border-success', 'error' => 'border-danger', 'warning' => 'border-warning'] as $type => $borderColor)
        @if(session($type))
        <div class="flex items-start gap-3 p-4 bg-white rounded-2xl shadow-xl border-l-4 {{ $borderColor }}
                    min-w-[300px] max-w-sm animate-slide-down toast-item" data-flash-auto-dismiss="4500">
            @if($type === 'success')
            <svg class="w-5 h-5 text-success flex-shrink-0 mt-0.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/>
            </svg>
            @elseif($type === 'error')
            <svg class="w-5 h-5 text-danger flex-shrink-0 mt-0.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
            </svg>
            @else
            <svg class="w-5 h-5 text-warning flex-shrink-0 mt-0.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"/>
            </svg>
            @endif
            <p class="text-sm font-medium text-slate-700 flex-1">{{ session($type) }}</p>
            <button onclick="this.closest('.toast-item').remove()" class="text-slate-400 hover:text-slate-600 transition-colors flex-shrink-0">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
                </svg>
            </button>
        </div>
        @endif
        @endforeach
    </div>
    @endif

    {{-- Konten Halaman --}}
    <main class="flex-1 p-4 sm:p-6">
        @yield('content')
    </main>

    <footer class="text-center py-4 text-xs text-slate-400 border-t border-slate-200/80">
        © {{ date('Y') }} SiMON — Sistem Monitoring Pelanggaran Siswa · MTs Ibnu Sina, Kab. Tasikmalaya
    </footer>
</div>

@stack('scripts')
</body>
</html>
