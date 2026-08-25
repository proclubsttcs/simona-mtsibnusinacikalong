@extends('layouts.app')

@section('title', 'Kelola Pelanggaran')
@section('page-title', 'Kelola Pelanggaran')

@section('breadcrumb')
<span class="text-slate-400">/</span>
<span class="text-slate-500">Pelanggaran</span>
@endsection

@section('content')

{{-- Stat Tab Bar --}}
<div class="flex items-center gap-2 mb-6 flex-wrap">
    @php
        $tabItems = [
            ['label' => 'Semua',         'value' => '',             'count' => $stats['semua'],        'color' => 'bg-slate-100 text-slate-700'],
            ['label' => 'Menunggu',      'value' => 'menunggu',     'count' => $stats['menunggu'],     'color' => 'bg-amber-100 text-amber-700'],
            ['label' => 'Dikonfirmasi',  'value' => 'dikonfirmasi', 'count' => $stats['dikonfirmasi'], 'color' => 'bg-emerald-100 text-emerald-700'],
            ['label' => 'Ditolak',       'value' => 'ditolak',      'count' => $stats['ditolak'],      'color' => 'bg-red-100 text-red-700'],
        ];
    @endphp
    @foreach($tabItems as $tab)
    <a href="{{ route('admin.pelanggaran.index', array_merge(request()->except('status','page'), $tab['value'] ? ['status' => $tab['value']] : [])) }}"
       class="flex items-center gap-2 px-4 py-2 rounded-xl text-sm font-semibold transition-all duration-200
              {{ request('status') == $tab['value'] ? 'bg-header-gradient text-white shadow-sm' : 'bg-white text-slate-600 border border-slate-200 hover:bg-slate-50' }}">
        {{ $tab['label'] }}
        <span class="px-1.5 py-0.5 rounded-md text-xs font-bold
                     {{ request('status') == $tab['value'] ? 'bg-white/20 text-white' : $tab['color'] }}">
            {{ $tab['count'] }}
        </span>
    </a>
    @endforeach
</div>

{{-- Filter --}}
<div class="card mb-5">
    <form action="{{ route('admin.pelanggaran.index') }}" method="GET"
          class="flex flex-wrap gap-3 items-end">

        @if(request('status'))
        <input type="hidden" name="status" value="{{ request('status') }}">
        @endif

        <div class="flex-1 min-w-[180px] relative">
            <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                <svg class="w-4 h-4 text-slate-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                        d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/>
                </svg>
            </div>
            <input type="text" name="cari" value="{{ request('cari') }}"
                   placeholder="Nama / NIS siswa..." class="form-input pl-9">
        </div>

        <select name="kelas" class="form-input w-auto">
            <option value="">Semua Kelas</option>
            @foreach($kelasList as $k)
            <option value="{{ $k }}" {{ request('kelas') == $k ? 'selected' : '' }}>{{ $k }}</option>
            @endforeach
        </select>

        <select name="kategori" class="form-input w-auto">
            <option value="">Semua Kategori</option>
            <option value="ringan"       {{ request('kategori') == 'ringan'       ? 'selected' : '' }}>Ringan</option>
            <option value="sedang"       {{ request('kategori') == 'sedang'       ? 'selected' : '' }}>Sedang</option>
            <option value="berat"        {{ request('kategori') == 'berat'        ? 'selected' : '' }}>Berat</option>
            <option value="sangat_berat" {{ request('kategori') == 'sangat_berat' ? 'selected' : '' }}>Sangat Berat</option>
        </select>

        <input type="month" name="bulan" value="{{ request('bulan') }}" class="form-input w-auto">

        <button type="submit" class="btn-primary">Filter</button>
        @if(request()->hasAny(['cari','kelas','kategori','bulan']))
        <a href="{{ route('admin.pelanggaran.index', request('status') ? ['status' => request('status')] : []) }}"
           class="btn-secondary">Reset</a>
        @endif
    </form>
</div>

{{-- Bulk Konfirmasi (hanya jika tab menunggu) --}}
@if(request('status') === 'menunggu' && $pelanggaran->isNotEmpty())
<div class="mb-4" x-data="bulkKonfirmasi()">
    <div class="flex items-center gap-3 p-3 bg-amber-50 border border-amber-200 rounded-xl">
        <input type="checkbox" id="selectAll" @change="toggleAll()"
               class="w-4 h-4 text-secondary border-slate-300 rounded">
        <label for="selectAll" class="text-sm text-amber-700 font-medium flex-1">
            Pilih semua untuk konfirmasi massal
            (<span x-text="selected.length"></span> dipilih)
        </label>
        <button @click="konfirmasiBulk()" x-show="selected.length > 0"
                class="btn-success text-xs">
            <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/>
            </svg>
            Konfirmasi (<span x-text="selected.length"></span>)
        </button>
    </div>

    <form id="form-bulk" action="{{ route('admin.pelanggaran.konfirmasi-bulk') }}" method="POST" class="hidden">
        @csrf
        <template x-for="id in selected">
            <input type="hidden" name="ids[]" :value="id">
        </template>
    </form>
</div>
@endif

{{-- Tabel --}}
<div class="card p-0 overflow-hidden">
    @if($pelanggaran->isEmpty())
    <div class="empty-state py-16">
        <div class="empty-state-icon">
            <svg class="w-8 h-8 text-slate-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                    d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2"/>
            </svg>
        </div>
        <p class="text-sm font-medium text-slate-600">Tidak ada pelanggaran ditemukan</p>
        @if(request('status') === 'menunggu')
        <p class="text-xs text-emerald-500 mt-1">✅ Semua pelanggaran sudah diproses!</p>
        @endif
    </div>
    @else
    <div class="overflow-x-auto" x-data="bulkKonfirmasi()">
        <table class="w-full table-simon">
            <thead>
                <tr>
                    @if(request('status') === 'menunggu')
                    <th class="w-8"></th>
                    @endif
                    <th>Siswa</th>
                    <th>Jenis Pelanggaran</th>
                    <th>Tanggal</th>
                    <th class="text-center">Poin</th>
                    <th>Dicatat Oleh</th>
                    <th>Status</th>
                    <th class="text-right">Aksi</th>
                </tr>
            </thead>
            <tbody>
                @foreach($pelanggaran as $p)
                <tr class="{{ $p->status === 'menunggu' ? 'bg-amber-50/30' : '' }}">

                    @if(request('status') === 'menunggu')
                    <td>
                        @if($p->status === 'menunggu')
                        <input type="checkbox" value="{{ $p->id }}"
                               @change="toggleItem({{ $p->id }})"
                               :checked="selected.includes({{ $p->id }})"
                               class="w-4 h-4 text-secondary border-slate-300 rounded">
                        @endif
                    </td>
                    @endif

                    {{-- Siswa --}}
                    <td>
                        <a href="{{ route('admin.siswa.show', $p->siswa) }}"
                           class="flex items-center gap-2.5 group">
                            <img src="{{ $p->siswa->foto_url }}" alt="{{ $p->siswa->nama }}"
                                 class="w-8 h-8 rounded-lg object-cover flex-shrink-0">
                            <div>
                                <p class="font-semibold text-sm text-slate-700 group-hover:text-secondary transition-colors">
                                    {{ $p->siswa->nama }}
                                </p>
                                <p class="text-xs text-slate-400">{{ $p->siswa->kelas }}</p>
                            </div>
                        </a>
                    </td>

                    {{-- Jenis --}}
                    <td>
                        <p class="text-sm text-slate-700">{{ Str::limit($p->jenisPelanggaran->nama, 35) }}</p>
                        <span class="{{ $p->jenisPelanggaran->badge_class }} text-[10px] mt-0.5">
                            {{ $p->jenisPelanggaran->kategori_label }}
                        </span>
                    </td>

                    {{-- Tanggal --}}
                    <td class="text-sm text-slate-600 whitespace-nowrap">
                        {{ $p->tanggal_pelanggaran->isoFormat('D MMM Y') }}
                    </td>

                    {{-- Poin --}}
                    <td class="text-center">
                        <span class="font-bold text-sm
                            {{ $p->poin_diberikan >= 50 ? 'text-danger' : ($p->poin_diberikan >= 20 ? 'text-warning' : 'text-slate-700') }}">
                            +{{ $p->poin_diberikan }}
                        </span>
                    </td>

                    {{-- Wali Kelas --}}
                    <td class="text-xs text-slate-500">
                        {{ Str::words($p->inputOleh->name, 2, '...') }}
                    </td>

                    {{-- Status --}}
                    <td>
                        <span class="{{ $p->status_badge_class }}">{{ $p->status_label }}</span>
                    </td>

                    {{-- Aksi --}}
                    <td class="text-right" x-data="{ showTolakForm: false }">
                        <div class="flex items-center justify-end gap-1">
                            {{-- Lihat detail --}}
                            <a href="{{ route('admin.pelanggaran.show', $p) }}"
                               class="p-1.5 text-slate-400 hover:text-secondary hover:bg-green-50 rounded-lg transition-colors">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                        d="M15 12a3 3 0 11-6 0 3 3 0 016 0z M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/>
                                </svg>
                            </a>

                            @if($p->status === 'menunggu')
                            {{-- Tombol Konfirmasi --}}
                            <form action="{{ route('admin.pelanggaran.konfirmasi', $p) }}" method="POST">
                                @csrf
                                <button type="submit"
                                    class="p-1.5 text-slate-400 hover:text-success hover:bg-emerald-50 rounded-lg transition-colors"
                                    title="Konfirmasi">
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                            d="M5 13l4 4L19 7"/>
                                    </svg>
                                </button>
                            </form>
                            {{-- Tombol Tolak --}}
                            <button @click="showTolakForm = !showTolakForm"
                                class="p-1.5 text-slate-400 hover:text-danger hover:bg-red-50 rounded-lg transition-colors"
                                title="Tolak">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                        d="M6 18L18 6M6 6l12 12"/>
                                </svg>
                            </button>
                            @endif

                            @if($p->status !== 'menunggu')
                            {{-- Batalkan konfirmasi --}}
                            <form action="{{ route('admin.pelanggaran.batal-konfirmasi', $p) }}" method="POST">
                                @csrf
                                <button type="submit"
                                    onclick="return confirm('Batalkan proses pelanggaran ini?')"
                                    class="p-1.5 text-slate-400 hover:text-warning hover:bg-amber-50 rounded-lg transition-colors"
                                    title="Batalkan">
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                            d="M3 10h10a8 8 0 018 8v2M3 10l6 6m-6-6l6-6"/>
                                    </svg>
                                </button>
                            </form>
                            @endif
                        </div>

                        {{-- Form Tolak (inline dropdown) --}}
                        @if($p->status === 'menunggu')
                        <div x-show="showTolakForm" x-transition
                             class="mt-2 p-3 bg-white border border-red-200 rounded-xl shadow-lg text-left min-w-[220px]"
                             style="display:none;">
                            <form action="{{ route('admin.pelanggaran.tolak', $p) }}" method="POST">
                                @csrf
                                <label class="text-xs font-semibold text-slate-600 block mb-1.5">
                                    Alasan Penolakan
                                </label>
                                <textarea name="alasan_tolak" rows="2" required minlength="10"
                                          placeholder="Minimal 10 karakter..."
                                          class="w-full text-xs border border-slate-200 rounded-lg p-2 resize-none
                                                 focus:outline-none focus:ring-1 focus:ring-danger/30 focus:border-danger"></textarea>
                                <div class="flex gap-2 mt-2">
                                    <button type="submit" class="btn-danger text-xs flex-1 justify-center py-1.5">Tolak</button>
                                    <button type="button" @click="showTolakForm=false"
                                            class="btn-secondary text-xs px-3 py-1.5">Batal</button>
                                </div>
                            </form>
                        </div>
                        @endif
                    </td>
                </tr>
                @endforeach
            </tbody>
        </table>
    </div>
    @if($pelanggaran->hasPages())
    <div class="px-4 py-4 border-t border-slate-100">
        {{ $pelanggaran->links() }}
    </div>
    @endif
    @endif
</div>

@endsection

@push('scripts')
<script>
function bulkKonfirmasi() {
    return {
        selected: [],
        toggleAll() {
            const checkboxes = document.querySelectorAll('input[type="checkbox"][value]');
            if (this.selected.length === checkboxes.length) {
                this.selected = [];
                checkboxes.forEach(c => c.checked = false);
            } else {
                this.selected = Array.from(checkboxes).map(c => parseInt(c.value));
                checkboxes.forEach(c => c.checked = true);
            }
        },
        toggleItem(id) {
            const idx = this.selected.indexOf(id);
            if (idx > -1) this.selected.splice(idx, 1);
            else this.selected.push(id);
        },
        konfirmasiBulk() {
            if (this.selected.length === 0) return;
            if (!confirm(`Konfirmasi ${this.selected.length} pelanggaran sekaligus?`)) return;
            document.getElementById('form-bulk').submit();
        },
    };
}
</script>
@endpush
