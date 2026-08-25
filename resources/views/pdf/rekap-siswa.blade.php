<!DOCTYPE html>
<html lang="id">
<head>
<meta charset="UTF-8">
<meta http-equiv="Content-Type" content="text/html; charset=utf-8"/>
<title>Surat Peringatan {{ $sp->jenis_sp }}</title>
<style>
    @page {
        size: A4 portrait;
        margin: 0;
    }

    * { margin: 0; padding: 0; box-sizing: border-box; }

    html, body {
        width: 210mm;
        height: 297mm;
    }

    body {
        font-family: 'Times New Roman', Times, serif;
        font-size: 12pt;
        color: #1a1a1a;
        line-height: 1.5;
        background: #ffffff;
    }

    .page {
        width: 210mm;
        height: 297mm;
        padding: 20mm 25mm 25mm 25mm;
        position: relative;
        overflow: hidden;
    }

    /* ─── KOP SURAT ─── */
    .kop {
        display: flex;
        align-items: center;
        gap: 18px;
        padding-bottom: 10px;
        border-bottom: 3px solid #1E5631;
        margin-bottom: 2px;
    }

    .kop-logo {
        width: 75px;
        height: 75px;
        flex-shrink: 0;
        object-fit: contain;
    }

    /* Fallback placeholder jika $sekolah['logo'] tidak tersedia */
    .kop-logo-placeholder {
        width: 75px;
        height: 75px;
        border: 2px solid #1E5631;
        border-radius: 50%;
        display: flex;
        align-items: center;
        justify-content: center;
        flex-shrink: 0;
        text-align: center;
    }

    .kop-logo-text {
        font-size: 9pt;
        font-weight: bold;
        color: #1E5631;
        line-height: 1.2;
    }

    .kop-text {
        flex: 1;
        text-align: center;
    }

    .kop-instansi {
        font-size: 11pt;
        color: #333;
        margin-bottom: 1px;
        text-transform: uppercase;
    }

    .kop-nama-sekolah {
        font-size: 19pt;
        font-weight: bold;
        color: #1E5631;
        line-height: 1.25;
        margin: 2px 0 3px;
        letter-spacing: 0.5px;
    }

    .kop-detail {
        font-size: 9.5pt;
        color: #555;
        line-height: 1.4;
        font-family: Arial, sans-serif;
    }

    .kop-garis2 {
        border-bottom: 1px solid #1E5631;
        margin-bottom: 18px;
    }

    /* ─── JUDUL SURAT ─── */
    .judul-wrap {
        text-align: center;
        margin: 0 0 18px;
    }

    .judul-sp {
        display: inline-block;
        font-size: 15pt;
        font-weight: bold;
        color: #ffffff;
        letter-spacing: 2px;
        padding: 9px 45px;
        border-radius: 4px;
        font-family: Arial, sans-serif;
    }

    .judul-sp-1 { background-color: #D97706; }
    .judul-sp-2 { background-color: #EA580C; }
    .judul-sp-3 { background-color: #DC2626; }

    .nomor-surat {
        font-size: 10.5pt;
        color: #555;
        margin-top: 8px;
        font-family: Arial, sans-serif;
    }

    /* ─── BADAN SURAT ─── */
    .body-surat {
        margin-bottom: 14px;
    }

    .body-surat p {
        margin-bottom: 11px;
        text-align: justify;
    }

    /* ─── TABEL IDENTITAS SISWA ─── */
    .tabel-identitas {
        width: 100%;
        border-collapse: collapse;
        margin: 14px 0 16px;
        font-size: 12pt;
    }

    .tabel-identitas td {
        padding: 3px 6px;
        vertical-align: top;
    }

    .tabel-identitas td:first-child {
        width: 170px;
        font-weight: bold;
        color: #333;
    }

    .tabel-identitas td:nth-child(2) {
        width: 12px;
        text-align: center;
    }

    /* ─── BOX POIN ─── */
    .box-poin {
        border: 2px solid;
        border-radius: 6px;
        padding: 12px 18px;
        margin: 16px 0;
        display: flex;
        align-items: center;
        gap: 18px;
        font-family: Arial, sans-serif;
    }

    .box-poin-sp1 { border-color: #D97706; background-color: #FFFBEB; }
    .box-poin-sp2 { border-color: #EA580C; background-color: #FFF7ED; }
    .box-poin-sp3 { border-color: #DC2626; background-color: #FEF2F2; }

    .poin-angka {
        font-size: 30pt;
        font-weight: bold;
        line-height: 1;
        min-width: 70px;
        text-align: center;
    }

    .poin-angka-sp1 { color: #D97706; }
    .poin-angka-sp2 { color: #EA580C; }
    .poin-angka-sp3 { color: #DC2626; }

    .poin-label {
        font-size: 10pt;
        color: #555;
        line-height: 1.3;
    }

    .poin-keterangan {
        font-size: 9.5pt;
        font-weight: bold;
        margin-top: 3px;
    }

    .poin-keterangan-sp1 { color: #D97706; }
    .poin-keterangan-sp2 { color: #EA580C; }
    .poin-keterangan-sp3 { color: #DC2626; }

    /* ─── KONSEKUENSI ─── */
    .box-konsekuensi {
        background-color: #F8FAFC;
        border-left: 4px solid;
        padding: 12px 16px;
        margin: 14px 0;
        border-radius: 0 4px 4px 0;
        font-family: Arial, sans-serif;
    }

    .box-konsekuensi-sp1 { border-color: #D97706; }
    .box-konsekuensi-sp2 { border-color: #EA580C; }
    .box-konsekuensi-sp3 { border-color: #DC2626; }

    .box-konsekuensi p {
        font-size: 10.5pt;
        font-weight: bold;
        color: #333;
        margin-bottom: 5px;
    }

    .box-konsekuensi ul {
        margin: 0;
        padding-left: 18px;
        font-size: 10.5pt;
        color: #444;
    }

    .box-konsekuensi ul li {
        margin-bottom: 3px;
    }

    /* ─── TANDA TANGAN ─── */
    .ttd-wrap {
        margin-top: 32px;
        display: table;
        width: 100%;
    }

    .ttd-kiri, .ttd-kanan {
        display: table-cell;
        width: 50%;
        text-align: center;
        vertical-align: top;
        font-size: 11pt;
    }

    .ttd-kiri {
        text-align: left;
        padding-left: 10px;
    }

    .ttd-kanan {
        text-align: right;
        padding-right: 10px;
    }

    .ttd-label {
        font-size: 11pt;
        color: #333;
        margin-bottom: 2px;
    }

    .ttd-tanggal {
        font-size: 11pt;
        margin-bottom: 60px;
        color: #333;
    }

    .ttd-nama {
        font-weight: bold;
        font-size: 12pt;
        border-top: 1px solid #333;
        padding-top: 4px;
        display: inline-block;
        min-width: 180px;
        text-align: center;
    }

    .ttd-jabatan {
        font-size: 10pt;
        color: #555;
    }

    /* ─── FOOTER ─── */
    .footer-surat {
        position: absolute;
        bottom: 12mm;
        left: 25mm;
        right: 25mm;
        border-top: 1px solid #ccc;
        padding-top: 5px;
        font-size: 8pt;
        color: #888;
        display: flex;
        justify-content: space-between;
        align-items: center;
        font-family: Arial, sans-serif;
    }

    .badge-sp {
        display: inline-block;
        font-size: 9pt;
        font-weight: bold;
        padding: 2px 10px;
        border-radius: 3px;
        color: white;
    }

    .badge-sp-1 { background-color: #D97706; }
    .badge-sp-2 { background-color: #EA580C; }
    .badge-sp-3 { background-color: #DC2626; }
</style>
</head>
<body>
<div class="page">

    {{-- ═══════════════════════════════════════════════
         KOP SURAT
    ═══════════════════════════════════════════════ --}}
    <div class="kop">
        @if(!empty($sekolah['logo']))
            <img class="kop-logo" src="{{ $sekolah['logo'] }}" alt="Logo Sekolah">
        @else
            <div class="kop-logo-placeholder">
                <div class="kop-logo-text">LOGO<br>MTs</div>
            </div>
        @endif

        <div class="kop-text">
            <div class="kop-instansi">PEMERINTAH KABUPATEN TASIKMALAYA</div>
            <div class="kop-instansi">KEMENTERIAN AGAMA REPUBLIK INDONESIA</div>
            <div class="kop-nama-sekolah">{{ strtoupper($sekolah['nama']) }}</div>
            <div class="kop-detail">
                {{ $sekolah['alamat'] }} &middot; Telp. {{ $sekolah['telp'] }}<br>
                NSS: {{ $sekolah['nss'] }} | NPSN: {{ $sekolah['npsn'] }} | Email: {{ $sekolah['email'] }}
            </div>
        </div>

        @if(!empty($sekolah['logo']))
            <img class="kop-logo" src="{{ $sekolah['logo'] }}" alt="" style="visibility:hidden;">
        @else
            <div class="kop-logo-placeholder" style="visibility:hidden;"></div>
        @endif
    </div>
    <div class="kop-garis2"></div>

    {{-- ═══════════════════════════════════════════════
         JUDUL SURAT
    ═══════════════════════════════════════════════ --}}
    <div class="judul-wrap">
        <div>
            <span class="judul-sp judul-sp-{{ $sp->jenis_sp === 'SP1' ? '1' : ($sp->jenis_sp === 'SP2' ? '2' : '3') }}">
                SURAT PERINGATAN {{ $sp->jenis_sp }}
            </span>
        </div>
        <div class="nomor-surat">Nomor: {{ $nomorSurat }}</div>
    </div>

    {{-- ═══════════════════════════════════════════════
         PEMBUKA
    ═══════════════════════════════════════════════ --}}
    <div class="body-surat">
        <p>
            Yang bertanda tangan di bawah ini, Kepala {{ $sekolah['nama'] }}, dengan ini
            menyatakan bahwa siswa yang tersebut di bawah ini telah melakukan pelanggaran tata tertib
            sekolah yang mengakibatkan akumulasi poin pelanggaran mencapai batas
            <strong>Surat Peringatan {{ $sp->jenis_sp }}</strong>:
        </p>

        {{-- Tabel Identitas --}}
        <table class="tabel-identitas">
            <tr>
                <td>Nama Siswa</td>
                <td>:</td>
                <td><strong{{ $siswa->first()->nama ?? '-' }}</strong></td>
            </tr>
            <tr>
                <td>NIS</td>
                <td>:</td>
                <td>{{ $siswa->nis }}</td>
            </tr>
            <tr>
                <td>Kelas</td>
                <td>:</td>
                <td>{{ $siswa->kelas }}</td>
            </tr>
            <tr>
                <td>Jenis Kelamin</td>
                <td>:</td>
                <td>{{ $siswa->jenis_kelamin === 'L' ? 'Laki-laki' : 'Perempuan' }}</td>
            </tr>
            <tr>
                <td>Nama Orang Tua/Wali</td>
                <td>:</td>
                <td>{{ $siswa->nama_orang_tua }}</td>
            </tr>
            <tr>
                <td>Wali Kelas</td>
                <td>:</td>
                <td>{{ $waliKelas?->name ?? '-' }}</td>
            </tr>
        </table>

        {{-- Box Poin --}}
        <div class="box-poin box-poin-{{ strtolower($sp->jenis_sp) }}">
            <div>
                <div class="poin-angka poin-angka-{{ strtolower($sp->jenis_sp) }}">
                    {{ $sp->total_poin_saat_ini }}
                </div>
            </div>
            <div>
                <div class="poin-label">Akumulasi Poin Pelanggaran</div>
                <div class="poin-keterangan poin-keterangan-{{ strtolower($sp->jenis_sp) }}">
                    @if($sp->jenis_sp === 'SP1') Batas SP1: 50 poin &mdash; Peringatan Pertama
                    @elseif($sp->jenis_sp === 'SP2') Batas SP2: 75 poin &mdash; Peringatan Kedua
                    @else Batas SP3: 100 poin &mdash; Peringatan Terakhir / Terancam Dikeluarkan
                    @endif
                </div>
                <div class="poin-label" style="margin-top:4px;">
                    Diterbitkan: {{ \Carbon\Carbon::parse($sp->tanggal_terbit)->isoFormat('D MMMM Y') }}
                </div>
            </div>
        </div>

        {{-- Isi Surat sesuai jenis SP --}}
        @if($sp->jenis_sp === 'SP1')
        <p>
            Melalui surat ini, kami memberikan <strong>Peringatan Pertama (SP1)</strong> kepada
            siswa yang bersangkutan agar segera memperbaiki perilaku dan mematuhi seluruh
            tata tertib yang berlaku di {{ $sekolah['nama'] }}.
        </p>
        <p>
            Kami berharap siswa yang bersangkutan dapat mengambil hikmah dari surat ini
            dan tidak mengulangi pelanggaran serupa. Orang tua/wali murid dimohon untuk
            memberikan perhatian dan bimbingan yang lebih intensif.
        </p>
        @elseif($sp->jenis_sp === 'SP2')
        <p>
            Melalui surat ini, kami memberikan <strong>Peringatan Kedua (SP2)</strong> kepada
            siswa yang bersangkutan. Peringatan ini diberikan karena siswa <em>masih melakukan
            pelanggaran</em> meskipun telah mendapatkan Surat Peringatan Pertama (SP1) sebelumnya.
        </p>
        <p>
            Kami memperingatkan dengan tegas bahwa apabila siswa kembali melakukan pelanggaran
            hingga akumulasi poin mencapai 100 poin, maka akan diterbitkan Surat Peringatan Ketiga
            (SP3) yang dapat berujung pada tindakan lebih tegas, termasuk kemungkinan
            <strong>pengembalian siswa kepada orang tua/wali</strong>.
        </p>
        @else
        <p>
            Melalui surat ini, kami memberikan <strong>Peringatan Terakhir (SP3)</strong> kepada
            siswa yang bersangkutan. Surat ini merupakan peringatan terakhir dan paling serius.
            Apabila siswa kembali melakukan pelanggaran setelah menerima SP3 ini, maka pihak
            sekolah akan mengambil tindakan tegas berupa <strong>pemanggilan orang tua/wali
            dan kemungkinan pengembalian siswa</strong>.
        </p>
        <p>
            Kami mengharapkan kerja sama yang serius dari orang tua/wali murid untuk memberikan
            bimbingan dan pengawasan ekstra kepada putra/putrinya, serta berkoordinasi
            aktif dengan pihak sekolah.
        </p>
        @endif

        @if($sp->keterangan)
        <p style="font-style:italic; color:#555; font-size:11pt;">
            <strong>Catatan BK:</strong> {{ $sp->keterangan }}
        </p>
        @endif

        {{-- Box Konsekuensi --}}
        <div class="box-konsekuensi box-konsekuensi-{{ strtolower($sp->jenis_sp) }}">
            <p>
                @if($sp->jenis_sp === 'SP1') Konsekuensi SP1:
                @elseif($sp->jenis_sp === 'SP2') Konsekuensi SP2:
                @else Konsekuensi SP3:
                @endif
            </p>
            <ul>
                @if($sp->jenis_sp === 'SP1')
                <li>Siswa wajib mengikuti program pembinaan dari Guru BK</li>
                <li>Orang tua/wali murid dipanggil untuk klarifikasi</li>
                <li>Siswa diawasi ketat selama minimal 1 bulan ke depan</li>
                @elseif($sp->jenis_sp === 'SP2')
                <li>Siswa wajib mengikuti program pembinaan intensif dari Guru BK</li>
                <li>Orang tua/wali murid dipanggil dan menandatangani surat pernyataan</li>
                <li>Siswa mendapat pengawasan ketat dari wali kelas dan BK</li>
                <li>Jika poin mencapai 100, akan diterbitkan SP3 dan dipertimbangkan tindakan lanjut</li>
                @else
                <li>Orang tua/wali murid <strong>wajib hadir</strong> ke sekolah dalam waktu 3&times;24 jam</li>
                <li>Sidang kasus bersama kepala sekolah, BK, dan orang tua/wali</li>
                <li>Siswa mendapat pembinaan intensif dan pengawasan ketat</li>
                <li>Apabila masih melanggar, dapat diproses pengembalian kepada orang tua/wali</li>
                @endif
            </ul>
        </div>

        <p>
            Demikian surat peringatan ini dibuat untuk dipergunakan sebagaimana mestinya.
            Atas perhatian dan kerja samanya, kami ucapkan terima kasih.
        </p>
    </div>

    {{-- ═══════════════════════════════════════════════
         TANDA TANGAN
    ═══════════════════════════════════════════════ --}}
    <div class="ttd-wrap">
        <div class="ttd-kiri">
            <div class="ttd-label">Mengetahui,</div>
            <div class="ttd-label">Orang Tua / Wali Murid</div>
            <div class="ttd-tanggal">&nbsp;</div>
            <div class="ttd-nama">{{ $siswa->nama_orang_tua }}</div>
            <div class="ttd-jabatan">Orang Tua/Wali dari {{ $siswa->nama }}</div>
        </div>

        <div class="ttd-kanan">
            <div class="ttd-label">
                {{ $sekolah['kota'] }}, {{ \Carbon\Carbon::parse($sp->tanggal_terbit)->isoFormat('D MMMM Y') }}
            </div>
            <div class="ttd-label">Kepala Sekolah,</div>
            <div class="ttd-tanggal">&nbsp;</div>
            <div class="ttd-nama">{{ $sekolah['kepsek'] }}</div>
            <div class="ttd-jabatan">NIP. &mdash;</div>
        </div>
    </div>

    {{-- ═══════════════════════════════════════════════
         FOOTER
    ═══════════════════════════════════════════════ --}}
    <div class="footer-surat">
        <span>
            Dokumen ini diterbitkan secara otomatis oleh Sistem SiMON &middot; {{ $sekolah['nama'] }}
        </span>
        <span>
            <span class="badge-sp badge-sp-{{ $sp->jenis_sp === 'SP1' ? '1' : ($sp->jenis_sp === 'SP2' ? '2' : '3') }}">
                {{ $sp->jenis_sp }}
            </span>
            &nbsp;#{{ $sp->id }} &middot; {{ \Carbon\Carbon::parse($sp->tanggal_terbit)->isoFormat('D MMM Y') }}
        </span>
    </div>

</div>
</body>
</html>
