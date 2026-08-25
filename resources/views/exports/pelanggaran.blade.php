<table>
    {{-- Baris 1-6: Header dokumen --}}
    <tr>
        <td colspan="10"><strong>{{ $judulLaporan }}</strong></td>
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
            @if(!empty($filters['status'])) | Status: {{ ucfirst($filters['status']) }} @endif
            @if(!empty($filters['kategori'])) | Kategori: {{ ucfirst(str_replace('_',' ',$filters['kategori'])) }} @endif
            @if(!empty($filters['bulan'])) | Bulan: {{ $filters['bulan'] }} @endif
            @if(!empty($filters['tahun'])) | Tahun: {{ $filters['tahun'] }} @endif
            @if(empty(array_filter($filters))) Semua Data @endif
        </td>
    </tr>
    <tr><td></td></tr>

    {{-- Baris 7: Header kolom --}}
    <tr>
        <td><strong>No</strong></td>
        <td><strong>NIS</strong></td>
        <td><strong>Nama Siswa</strong></td>
        <td><strong>Kelas</strong></td>
        <td><strong>Jenis Pelanggaran</strong></td>
        <td><strong>Kategori</strong></td>
        <td><strong>Tanggal</strong></td>
        <td><strong>Poin</strong></td>
        <td><strong>Status</strong></td>
        <td><strong>Dicatat Oleh</strong></td>
    </tr>

    {{-- Data --}}
    @forelse($pelanggaran as $i => $p)
    <tr>
        <td>{{ $i + 1 }}</td>
        <td>{{ $p->siswa->nis }}</td>
        <td>{{ $p->siswa->nama }}</td>
        <td>{{ $p->siswa->kelas }}</td>
        <td>{{ $p->jenisPelanggaran->nama }}</td>
        <td>{{ $p->jenisPelanggaran->kategori_label }}</td>
        <td>{{ $p->tanggal_pelanggaran->format('d/m/Y') }}</td>
        <td>{{ $p->poin_diberikan }}</td>
        <td>{{ $p->status_label }}</td>
        <td>{{ $p->inputOleh->name }}</td>
    </tr>
    @empty
    <tr>
        <td colspan="10">Tidak ada data</td>
    </tr>
    @endforelse

    {{-- Baris total --}}
    @if($pelanggaran->isNotEmpty())
    <tr>
        <td colspan="7"><strong>TOTAL</strong></td>
        <td><strong>{{ $pelanggaran->sum('poin_diberikan') }}</strong></td>
        <td colspan="2"><strong>{{ $pelanggaran->count() }} catatan</strong></td>
    </tr>
    @endif
</table>
