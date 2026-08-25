<!DOCTYPE html>
<html lang="id">
<head>
<meta charset="UTF-8">
<meta http-equiv="Content-Type" content="text/html; charset=utf-8"/>
<style>
    /* Reset & Page Setup untuk A4 */
    * { margin: 0; padding: 0; box-sizing: border-box; }

    page {
        background: white;
        display: block;
        margin: 0 auto;
        margin-bottom: 0.5in;
    }
    
    @page {
        size: A4;
        margin: 0;
    }

    body {
        font-family: "Times New Roman", Times, serif;
        font-size: 12pt;
        color: #000000;
        line-height: 1.15;
        background: #525659;
    }

    .page {
        width: 210mm;
        min-height: 297mm;
        /* Padding seimbang kanan-kiri (25mm) agar posisi pas di tengah A4 */
        padding: 20mm 25mm 20mm 25mm;
        margin: 0 auto;
        background: white;
        position: relative;
        box-shadow: 0 0 10px rgba(0,0,0,0.3);
    }

    @media print {
        body {
            background: transparent;
        }
        .page {
            box-shadow: none;
            margin: 0;
            padding: 20mm 25mm 20mm 25mm;
            width: 100%;
            min-height: 100%;
        }
    }

    /* ─── KOP SURAT RESMI ─── */
    .kop {
        display: flex;
        align-items: center;
        gap: 16px;
        padding-bottom: 8px;
        border-bottom: 4px double #000000;
        margin-bottom: 4px;
    }

    .kop-logo-placeholder {
        width: 75px;
        height: 75px;
        border: 1px solid #000000;
        display: flex;
        align-items: center;
        justify-content: center;
        flex-shrink: 0;
    }

    .kop-logo-text {
        font-size: 9pt;
        font-weight: bold;
        color: #000000;
        text-align: center;
    }

    .kop-text {
        flex: 1;
        text-align: center;
    }

    .kop-instansi {
        font-size: 11pt;
        font-weight: bold;
        text-transform: uppercase;
        color: #000000;
        margin-bottom: 1px;
    }

    .kop-nama-sekolah {
        font-size: 15pt;
        font-weight: bold;
        text-transform: uppercase;
        color: #000000;
        line-height: 1.2;
        margin-bottom: 2px;
    }

    .kop-detail {
        font-size: 10pt;
        color: #000000;
        line-height: 1.3;
    }

    /* ─── JUDUL SURAT ─── */
    .judul-wrap {
        text-align: center;
        margin: 20px 0 16px;
    }

    .judul-sp {
        display: inline-block;
        font-size: 13pt;
        font-weight: bold;
        color: #000000;
        text-transform: uppercase;
        letter-spacing: 1px;
        padding: 0 0 2px 0;
        border-bottom: 1.5px solid #000000;
    }

    .nomor-surat {
        font-size: 11pt;
        color: #000000;
        margin-top: 4px;
    }

    /* ─── BADAN SURAT ─── */
    .body-surat {
        margin-bottom: 14px;
    }

    .body-surat p {
        margin-bottom: 10px;
        text-align: justify;
        text-indent: 30px;
    }

    .body-surat p.no-indent {
        text-indent: 0;
    }

    /* ─── TABEL IDENTITAS SISWA ─── */
    .tabel-identitas {
        width: 100%;
        border-collapse: collapse;
        margin: 10px 0 14px 30px;
        font-size: 12pt;
    }

    .tabel-identitas td {
        padding: 3px 4px;
        vertical-align: top;
    }

    .tabel-identitas td:first-child {
        width: 160px;
        font-weight: normal;
        color: #000000;
    }

    .tabel-identitas td:nth-child(2) {
        width: 15px;
        text-align: center;
    }

    /* ─── BOX POIN ─── */
    .box-poin {
        border: 1px solid #000000;
        background-color: #FFFFFF !important;
        padding: 10px 14px;
        margin: 14px 0;
        display: flex;
        align-items: center;
        gap: 16px;
    }

    .poin-angka {
        font-size: 24pt;
        font-weight: bold;
        line-height: 1;
        color: #000000 !important;
    }

    .poin-label {
        font-size: 11pt;
        color: #000000;
        line-height: 1.3;
    }

    .poin-keterangan {
        font-size: 10.5pt;
        font-weight: bold;
        margin-top: 2px;
        color: #000000 !important;
    }

    /* ─── KONSEKUENSI ─── */
    .box-konsekuensi {
        background-color: #FFFFFF !important;
        border: 1px solid #000000;
        border-left: 4px solid #000000 !important;
        padding: 10px 14px;
        margin: 12px 0;
    }

    .box-konsekuensi p {
        font-size: 11pt;
        font-weight: bold;
        color: #000000;
        margin-bottom: 4px;
        text-indent: 0 !important;
    }

    .box-konsekuensi ul {
        margin: 0;
        padding-left: 18px;
        font-size: 11pt;
        color: #000000;
    }

    .box-konsekuensi ul li {
        margin-bottom: 2px;
    }

    /* ─── TANDA TANGAN ─── */
    .ttd-wrap {
        margin-top: 24px;
        display: table;
        width: 100%;
        page-break-inside: avoid;
    }

    .ttd-kiri, .ttd-kanan {
        display: table-cell;
        width: 50%;
        text-align: center;
        vertical-align: top;
        font-size: 12pt;
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
        font-size: 12pt;
        color: #000000;
        margin-bottom: 2px;
    }

    .ttd-tanggal {
        font-size: 12pt;
        margin-bottom: 65px;
        color: #000000;
    }

    .ttd-nama {
        font-weight: bold;
        font-size: 12pt;
        border-bottom: 1px solid #000000;
        padding-bottom: 1px;
        display: inline-block;
        min-width: 180px;
        text-align: center;
    }

    .ttd-jabatan {
        font-size: 11pt;
        color: #000000;
        margin-top: 2px;
    }

    /* ─── FOOTER ─── */
    .footer-surat {
        position: absolute;
        bottom: 14mm;
        left: 25mm;
        right: 25mm;
        border-top: 1px solid #999;
        padding-top: 5px;
        font-size: 8pt;
        color: #555;
        display: flex;
        justify-content: space-between;
    }

    .badge-sp {
        display: inline-block;
        font-size: 8pt;
        font-weight: bold;
        padding: 1px 6px;
        border: 1px solid #000;
        background-color: #eee !important;
        color: #000 !important;
    }
</style>
</head>
<body>
<div class="page">

    {{-- ═══════════════════════════════════════════════
         KOP SURAT
    ═══════════════════════════════════════════════ --}}
    <div class="kop">
        {{-- Logo placeholder (bisa diganti dengan <img> jika ada logo) --}}
        <div class="kop-logo-placeholder">
            <div class="kop-logo-text">LOGO<br>MTs</div>
        </div>

        <div class="kop-text">
            <div class="kop-instansi">PEMERINTAH KABUPATEN TASIKMALAYA</div>
            <div class="kop-instansi">KEMENTERIAN AGAMA REPUBLIK INDONESIA</div>
            <div class="kop-nama-sekolah">{{ strtoupper($sekolah['nama']) }}</div>
            <div class="kop-detail">
                {{ $sekolah['alamat'] }} · Telp. {{ $sekolah['telp'] }}<br>
                NSS: {{ $sekolah['nss'] }} | NPSN: {{ $sekolah['npsn'] }} | Email: {{ $sekolah['email'] }}
            </div>
        </div>
    </div>

    {{-- ═══════════════════════════════════════════════
         JUDUL SURAT
    ═══════════════════════════════════════════════ --}}
    <div class="judul-wrap">
        <div>
            <span class="judul-sp">
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
                <td><strong>{{ $siswa->nama }}</strong></td>
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
        <div class="box-poin">
            <div>
                <div class="poin-angka">
                    {{ $sp->total_poin_saat_ini }}
                </div>
            </div>
            <div>
                <div class="poin-label">Akumulasi Poin Pelanggaran</div>
                <div class="poin-keterangan">
                    @if($sp->jenis_sp === 'SP1') Batas SP1: 50 poin — Peringatan Pertama
                    @elseif($sp->jenis_sp === 'SP2') Batas SP2: 75 poin — Peringatan Kedua
                    @else Batas SP3: 100 poin — Peringatan Terakhir / Terancam Dikeluarkan
                    @endif
                </div>
                <div class="poin-label" style="margin-top:2px; font-size:10pt;">
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
        <p class="no-indent" style="font-style:italic; font-size:11pt; margin-top: 8px;">
            <strong>Catatan BK:</strong> {{ $sp->keterangan }}
        </p>
        @endif

        {{-- Box Konsekuensi --}}
        <div class="box-konsekuensi">
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
                <li>Orang tua/wali murid <strong>wajib hadir</strong> ke sekolah dalam waktu 3×24 jam</li>
                <li>Sidang kasus bersama kepala sekolah, BK, dan orang tua/wali</li>
                <li>Siswa mendapat pembinaan intensif dan pengawasan ketat</li>
                <li>Apabila masih melanggar, dapat diproses pengembalian kepada orang tua/wali</li>
                @endif
            </ul>
        </div>

        <p style="margin-top: 12px;">
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
            <div class="ttd-jabatan">NIP. —</div>
        </div>
    </div>

    {{-- ═══════════════════════════════════════════════
         FOOTER
    ═══════════════════════════════════════════════ --}}
    <div class="footer-surat">
        <span>
            Dokumen ini diterbitkan secara otomatis oleh Sistem SiMON · {{ $sekolah['nama'] }}
        </span>
        <span>
            <span class="badge-sp">
                {{ $sp->jenis_sp }}
            </span>
            &nbsp;#{{ $sp->id }} · {{ \Carbon\Carbon::parse($sp->tanggal_terbit)->isoFormat('D MMM Y') }}
        </span>
    </div>

</div>
</body>
</html>