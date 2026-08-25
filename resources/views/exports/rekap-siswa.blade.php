<table>
    <tr>
        <td colspan="10"><strong>Rekap Poin Pelanggaran Siswa</strong></td>
    </tr>
    <tr>
        <td colspan="10">MTs Ibnu Sina — Kab. Tasikmalaya</td>
    </tr>
    <tr>
        <td colspan="10">Digenerate: {{ $generatedAt }}</td>
    </tr>
    <tr><td></td></tr>
    <tr>
        <td colspan="10">
            Filter:
            @if(!empty($filters['kelas'])) Kelas: {{ $filters['kelas'] }} @endif
            @if(!empty($filters['status_sp'])) | Status SP: {{ $filters['status_sp'] }} @endif
            @if(empty(array_filter($filters))) Semua Siswa Aktif @endif
        </td>
    </tr>
    <tr><td></td></tr>

    {{-- Header --}}
    <tr>
        <td><strong>No</strong></td>
        <td><strong>NIS</strong></td>
        <td><strong>Nama Siswa</strong></td>
        <td><strong>L/P</strong></td>
        <td><strong>Kelas</strong></td>
        <td><strong>Wali Kelas</strong></td>
        <td><strong>Total Poin</strong></td>
        <td><strong>Status SP</strong></td>
        <td><strong>Jml SP</strong></td>
        <td><strong>No. HP Ortu</strong></td>
    </tr>

    @forelse($siswa as $i => $s)
    <tr>
        <td>{{ $i + 1 }}</td>
        <td>{{ $s->nis }}</td>
        <td>{{ $s->nama }}</td>
        <td>{{ $s->jenis_kelamin }}</td>
        <td>{{ $s->kelas }}</td>
        <td>{{ $s->waliKelas?->name ?? '-' }}</td>
        <td>{{ $s->rekapPoin?->total_poin ?? 0 }}</td>
        <td>{{ $s->rekapPoin?->status_sp ?? 'aman' }}</td>
        <td>{{ $s->suratPeringatan->count() }}</td>
        <td>{{ $s->no_hp_orang_tua }}</td>
    </tr>
    @empty
    <tr>
        <td colspan="10">Tidak ada data</td>
    </tr>
    @endforelse

    @if($siswa->isNotEmpty())
    <tr>
        <td colspan="6"><strong>TOTAL</strong></td>
        <td><strong>{{ $siswa->sum(fn($s) => $s->rekapPoin?->total_poin ?? 0) }}</strong></td>
        <td colspan="3"><strong>{{ $siswa->count() }} siswa</strong></td>
    </tr>
    @endif
</table>
