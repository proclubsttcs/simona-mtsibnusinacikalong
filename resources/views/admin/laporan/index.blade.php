@extends('layouts.app')

@section('title', 'Laporan')
@section('page-title', 'Laporan & Statistik')

@section('breadcrumb')
<span class="text-slate-400">/</span>
<span class="text-slate-500">Laporan</span>
@endsection

@section('content')

{{-- ── Filter Tahun & Kelas ──────────────────────────────────── --}}
<div class="card mb-6">
    <form action="{{ route('admin.laporan.index') }}" method="GET"
          class="flex flex-wrap gap-3 items-end" id="form-filter">
        <div>
            <label class="form-label">Tahun</label>
            <select name="tahun" id="sel-tahun" class="form-input w-auto"
                    onchange="this.form.submit()">
                @foreach($tahunList as $t)
                <option value="{{ $t }}" {{ $tahun == $t ? 'selected' : '' }}>{{ $t }}</option>
                @endforeach
            </select>
        </div>
        <div>
            <label class="form-label">Kelas</label>
            <select name="kelas" id="sel-kelas" class="form-input w-auto"
                    onchange="this.form.submit()">
                <option value="">Semua Kelas</option>
                @foreach($kelasList as $k)
                <option value="{{ $k }}" {{ $kelas == $k ? 'selected' : '' }}>{{ $k }}</option>
                @endforeach
            </select>
        </div>
        <div class="flex gap-2 ml-auto flex-wrap">
            {{-- Export Pelanggaran Excel --}}
            <a href="{{ route('admin.laporan.export-pelanggaran-excel', ['tahun' => $tahun, 'kelas' => $kelas]) }}"
               class="btn-success text-sm">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                        d="M12 10v6m0 0l-3-3m3 3l3-3m2 8H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/>
                </svg>
                Export Pelanggaran (.xlsx)
            </a>
            {{-- Export Rekap Excel --}}
            <a href="{{ route('admin.laporan.export-rekap-excel', ['kelas' => $kelas]) }}"
               class="btn-secondary text-sm">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                        d="M9 17v-2m3 2v-4m3 4v-6m2 10H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/>
                </svg>
                Rekap Poin (.xlsx)
            </a>
        </div>
    </form>
</div>

{{-- ── Stat Cards ────────────────────────────────────────────── --}}
<div class="grid grid-cols-2 lg:grid-cols-6 gap-4 mb-6">
    @php
        $cards = [
            ['label' => 'Total Siswa',       'value' => $stats['total_siswa'],       'color' => 'text-secondary',  'bg' => 'bg-green-50'],
            ['label' => 'Total Pelanggaran', 'value' => $stats['total_pelanggaran'], 'color' => 'text-warning',    'bg' => 'bg-orange-50'],
            ['label' => 'Siswa Punya SP',    'value' => $stats['siswa_punya_sp'],    'color' => 'text-danger',     'bg' => 'bg-red-50'],
            ['label' => 'SP Terbit Tahun',   'value' => $stats['sp_terbit_tahun'],   'color' => 'text-purple',     'bg' => 'bg-purple-50'],
            ['label' => 'Rata-rata Poin',    'value' => $stats['rata_poin'],         'color' => 'text-slate-700',  'bg' => 'bg-slate-50'],
            ['label' => 'Poin Tertinggi',    'value' => $stats['poin_tertinggi'],    'color' => 'text-danger',     'bg' => 'bg-red-50'],
        ];
    @endphp
    @foreach($cards as $c)
    <div class="card p-4 text-center">
        <p class="font-heading text-2xl font-bold {{ $c['color'] }}">{{ $c['value'] }}</p>
        <p class="text-xs text-slate-500 mt-1 font-medium">{{ $c['label'] }}</p>
    </div>
    @endforeach
</div>

{{-- ── Grafik Baris 1: Pelanggaran per Bulan + Distribusi SP ─── --}}
<div class="grid grid-cols-1 lg:grid-cols-3 gap-6 mb-6">

    {{-- Grafik Pelanggaran per Bulan (line + bar) --}}
    <div class="lg:col-span-2 card">
        <div class="flex items-center justify-between mb-4">
            <h3 class="font-heading font-bold text-slate-800">Pelanggaran per Bulan</h3>
            <span class="badge bg-green-50 text-secondary">{{ $tahun }}</span>
        </div>
        <div class="relative h-64">
            <canvas id="chartBulan"></canvas>
        </div>
    </div>

    {{-- Distribusi Status SP (donut) --}}
    <div class="card">
        <h3 class="font-heading font-bold text-slate-800 mb-4">Distribusi Status SP</h3>
        <div class="relative h-48 flex items-center justify-center">
            <canvas id="chartSp"></canvas>
        </div>
        {{-- Legend --}}
        <div class="grid grid-cols-2 gap-2 mt-4">
            @php
                $spItems = [
                    ['label' => 'Aman', 'value' => $distribusiSp['aman'], 'color' => 'bg-emerald-500', 'text' => 'text-emerald-700'],
                    ['label' => 'SP1',  'value' => $distribusiSp['SP1'],  'color' => 'bg-amber-500',   'text' => 'text-amber-700'],
                    ['label' => 'SP2',  'value' => $distribusiSp['SP2'],  'color' => 'bg-orange-500',  'text' => 'text-orange-700'],
                    ['label' => 'SP3',  'value' => $distribusiSp['SP3'],  'color' => 'bg-red-600',     'text' => 'text-red-700'],
                ];
                $totalSp = array_sum($distribusiSp);
            @endphp
            @foreach($spItems as $sp)
            <div class="flex items-center gap-2">
                <div class="w-3 h-3 rounded-full {{ $sp['color'] }} flex-shrink-0"></div>
                <span class="text-xs text-slate-600">{{ $sp['label'] }}</span>
                <span class="ml-auto text-xs font-bold {{ $sp['text'] }}">{{ $sp['value'] }}</span>
            </div>
            @endforeach
        </div>
    </div>
</div>

{{-- ── Grafik Baris 2: Per Kategori + Per Kelas ─────────────── --}}
<div class="grid grid-cols-1 lg:grid-cols-2 gap-6 mb-6">

    {{-- Pelanggaran per Kategori (bar horizontal) --}}
    <div class="card">
        <h3 class="font-heading font-bold text-slate-800 mb-4">Pelanggaran per Kategori</h3>
        @php
            $kategoriData = [
                ['key' => 'ringan',       'label' => 'Ringan',       'color' => 'bg-emerald-400', 'text' => 'text-emerald-700'],
                ['key' => 'sedang',       'label' => 'Sedang',       'color' => 'bg-amber-400',   'text' => 'text-amber-700'],
                ['key' => 'berat',        'label' => 'Berat',        'color' => 'bg-orange-500',  'text' => 'text-orange-700'],
                ['key' => 'sangat_berat', 'label' => 'Sangat Berat', 'color' => 'bg-red-500',     'text' => 'text-red-700'],
            ];
            $maxKat = max(array_values($perKategori) ?: [1]);
        @endphp
        <div class="space-y-4">
            @foreach($kategoriData as $k)
            @php $jml = $perKategori[$k['key']] ?? 0; $pct = $maxKat > 0 ? ($jml / $maxKat) * 100 : 0; @endphp
            <div>
                <div class="flex justify-between items-center mb-1.5">
                    <span class="text-sm font-semibold text-slate-600">{{ $k['label'] }}</span>
                    <span class="text-sm font-bold {{ $k['text'] }}">{{ $jml }}</span>
                </div>
                <div class="w-full bg-slate-100 rounded-full h-3 overflow-hidden">
                    <div class="{{ $k['color'] }} h-3 rounded-full transition-all duration-700"
                         style="width: {{ $pct }}%"></div>
                </div>
            </div>
            @endforeach
        </div>
        <div class="mt-4 pt-4 border-t border-slate-100 flex justify-between text-sm">
            <span class="text-slate-500">Total pelanggaran dikonfirmasi:</span>
            <span class="font-bold text-slate-700">{{ array_sum($perKategori) }}</span>
        </div>
    </div>

    {{-- Pelanggaran per Kelas (bar) --}}
    <div class="card">
        <h3 class="font-heading font-bold text-slate-800 mb-4">Pelanggaran per Kelas</h3>
        @if($perKelas->isEmpty())
        <div class="empty-state py-10">
            <p class="text-sm text-slate-400">Belum ada data pelanggaran tahun {{ $tahun }}</p>
        </div>
        @else
        <div class="relative h-56">
            <canvas id="chartKelas"></canvas>
        </div>
        @endif
    </div>
</div>

{{-- ── Top 10 Jenis Pelanggaran ─────────────────────────────── --}}
<div class="card mb-6">
    <div class="flex items-center justify-between mb-4">
        <h3 class="font-heading font-bold text-slate-800">Top 10 Jenis Pelanggaran Terbanyak</h3>
        <span class="text-xs text-slate-400">Tahun {{ $tahun }}</span>
    </div>
    @if($topJenis->isEmpty())
    <div class="empty-state py-8">
        <p class="text-sm text-slate-400">Belum ada data pelanggaran dikonfirmasi</p>
    </div>
    @else
    @php $maxJenis = $topJenis->first()?->jumlah ?? 1; @endphp
    <div class="space-y-3">
        @foreach($topJenis as $i => $j)
        @php
            $pct = ($j->jumlah / $maxJenis) * 100;
            $barColor = match($j->kategori) {
                'ringan'       => 'bg-emerald-400',
                'sedang'       => 'bg-amber-400',
                'berat'        => 'bg-orange-500',
                'sangat_berat' => 'bg-red-500',
                default        => 'bg-slate-400',
            };
            $badgeClass = match($j->kategori) {
                'ringan'       => 'badge-ringan',
                'sedang'       => 'badge-sedang',
                'berat'        => 'badge-berat',
                'sangat_berat' => 'badge-sangat-berat',
                default        => 'badge',
            };
        @endphp
        <div class="flex items-center gap-3">
            <div class="w-6 h-6 rounded-lg {{ $i < 3 ? 'bg-amber-100 text-amber-700' : 'bg-slate-100 text-slate-500' }}
                        text-xs font-bold flex items-center justify-center flex-shrink-0">
                {{ $i + 1 }}
            </div>
            <div class="flex-1 min-w-0">
                <div class="flex items-center justify-between mb-1">
                    <p class="text-sm font-semibold text-slate-700 truncate pr-2">{{ $j->nama }}</p>
                    <div class="flex items-center gap-2 flex-shrink-0">
                        <span class="{{ $badgeClass }} text-[10px]">{{ Str::title(str_replace('_',' ',$j->kategori)) }}</span>
                        <span class="text-sm font-bold text-slate-700">{{ $j->jumlah }}×</span>
                    </div>
                </div>
                <div class="w-full bg-slate-100 rounded-full h-2 overflow-hidden">
                    <div class="{{ $barColor }} h-2 rounded-full" style="width: {{ $pct }}%"></div>
                </div>
            </div>
        </div>
        @endforeach
    </div>
    @endif
</div>

{{-- ── Link ke Detail Laporan Siswa ────────────────────────── --}}
<div class="card bg-gradient-to-r from-primary to-secondary text-white">
    <div class="flex items-center justify-between">
        <div>
            <h3 class="font-heading font-bold text-lg mb-1">Laporan Detail per Siswa</h3>
            <p class="text-green-100/80 text-sm">Lihat rekap poin dan riwayat lengkap setiap siswa</p>
        </div>
        <a href="{{ route('admin.laporan.siswa') }}"
           class="flex items-center gap-2 px-5 py-2.5 bg-white/15 hover:bg-white/25
                  text-white font-semibold rounded-xl transition-all duration-200 border border-white/20 flex-shrink-0">
            Lihat Detail
            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/>
            </svg>
        </a>
    </div>
</div>

@endsection

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', () => {

    const bulanLabels = ['Jan','Feb','Mar','Apr','Mei','Jun','Jul','Agu','Sep','Okt','Nov','Des'];

    // ── 1. Grafik Pelanggaran per Bulan ──────────────────────────
    new Chart(document.getElementById('chartBulan'), {
        type: 'bar',
        data: {
            labels: bulanLabels,
            datasets: [{
                label: 'Pelanggaran',
                data: @json($pelanggaranPerBulan),
                backgroundColor: 'rgba(21,128,61,0.25)',
                borderColor: 'rgba(21,128,61,1)',
                borderWidth: 2,
                borderRadius: 6,
                borderSkipped: false,
            }],
        },
        options: {
            responsive: true,
            maintainAspectRatio: false,
            plugins: {
                legend: { display: false },
                tooltip: {
                    callbacks: {
                        label: ctx => ` ${ctx.parsed.y} pelanggaran`,
                    },
                },
            },
            scales: {
                y: {
                    beginAtZero: true,
                    ticks: { stepSize: 1, font: { size: 11 } },
                    grid: { color: 'rgba(0,0,0,0.05)' },
                },
                x: {
                    ticks: { font: { size: 11 } },
                    grid: { display: false },
                },
            },
        },
    });

    // ── 2. Distribusi SP (Donut) ─────────────────────────────────
    const spData = @json(array_values($distribusiSp));
    const spTotal = spData.reduce((a, b) => a + b, 0);

    new Chart(document.getElementById('chartSp'), {
        type: 'doughnut',
        data: {
            labels: ['Aman', 'SP1', 'SP2', 'SP3'],
            datasets: [{
                data: spData,
                backgroundColor: ['#10B981','#F59E0B','#F97316','#EF4444'],
                borderWidth: 2,
                borderColor: '#fff',
                hoverOffset: 6,
            }],
        },
        options: {
            responsive: true,
            maintainAspectRatio: false,
            cutout: '65%',
            plugins: {
                legend: { display: false },
                tooltip: {
                    callbacks: {
                        label: ctx => {
                            const pct = spTotal > 0 ? ((ctx.parsed / spTotal) * 100).toFixed(1) : 0;
                            return ` ${ctx.parsed} siswa (${pct}%)`;
                        },
                    },
                },
            },
        },
    });

    // ── 3. Pelanggaran per Kelas (Bar) ───────────────────────────
    const kelasCanvas = document.getElementById('chartKelas');
    if (kelasCanvas) {
        const kelasData = @json($perKelas);
        new Chart(kelasCanvas, {
            type: 'bar',
            data: {
                labels: kelasData.map(d => d.kelas),
                datasets: [{
                    label: 'Jumlah Pelanggaran',
                    data: kelasData.map(d => d.jumlah),
                    backgroundColor: kelasData.map((_, i) => {
                        const colors = ['rgba(21,128,61,0.7)','rgba(30,86,49,0.7)','rgba(124,58,237,0.7)',
                                        'rgba(245,158,11,0.7)','rgba(239,68,68,0.7)','rgba(16,185,129,0.7)'];
                        return colors[i % colors.length];
                    }),
                    borderRadius: 4,
                    borderSkipped: false,
                }],
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                plugins: {
                    legend: { display: false },
                    tooltip: {
                        callbacks: {
                            label: ctx => ` ${ctx.parsed.y} pelanggaran`,
                        },
                    },
                },
                scales: {
                    y: { beginAtZero: true, ticks: { stepSize: 1, font: { size: 10 } }, grid: { color: 'rgba(0,0,0,0.05)' } },
                    x: { ticks: { font: { size: 10 } }, grid: { display: false } },
                },
            },
        });
    }
});
</script>
@endpush
