<?php

use App\Http\Controllers\Auth\AuthController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\Admin\UserController;
use App\Http\Controllers\Admin\SiswaController;
use App\Http\Controllers\Admin\PelanggaranController      as AdminPelanggaranController;
use App\Http\Controllers\Admin\JenisPelanggaranController;
use App\Http\Controllers\Admin\SuratPeringatanController;
use App\Http\Controllers\Admin\LaporanController;
use App\Http\Controllers\WaliKelas\SiswaWaliKelasController;
use App\Http\Controllers\WaliKelas\PelanggaranController  as WaliKelasPelanggaranController;
use Illuminate\Support\Facades\Route;

// ══════════════════════════════════════════════════════════════════
// PUBLIK
// ══════════════════════════════════════════════════════════════════
Route::middleware('guest')->group(function () {
    Route::get('/login',  [AuthController::class, 'showLogin'])->name('login');
    Route::post('/login', [AuthController::class, 'login'])->name('login.post');
});

// ══════════════════════════════════════════════════════════════════
// TERPROTEKSI
// ══════════════════════════════════════════════════════════════════
Route::middleware(['auth', 'must.change.password'])->group(function () {

    Route::post('/logout',         [AuthController::class, 'logout'])->name('logout');
    Route::get('/',                [DashboardController::class, 'index'])->name('dashboard');
    Route::get('/dashboard',       [DashboardController::class, 'index'])->name('dashboard.index');
    Route::get('/ganti-password',  [AuthController::class, 'showChangePassword'])->name('password.change');
    Route::post('/ganti-password', [AuthController::class, 'updatePassword'])->name('password.change.update');

    // ─── ADMIN ────────────────────────────────────────────────────
    Route::prefix('admin')->name('admin.')->middleware('role:admin')->group(function () {

        // Akun & Siswa
        Route::resource('users', UserController::class);
        Route::resource('siswa', SiswaController::class);

        // Jenis Pelanggaran
        Route::resource('jenis-pelanggaran', JenisPelanggaranController::class)->except(['show']);
        Route::patch('jenis-pelanggaran/{jenisPelanggaran}/toggle-status',
            [JenisPelanggaranController::class, 'toggleStatus'])
            ->name('jenis-pelanggaran.toggle-status');

        // Pelanggaran
        Route::get('pelanggaran',                                [AdminPelanggaranController::class, 'index'])->name('pelanggaran.index');
        Route::get('pelanggaran/{pelanggaran}',                  [AdminPelanggaranController::class, 'show'])->name('pelanggaran.show');
        Route::post('pelanggaran/{pelanggaran}/konfirmasi',      [AdminPelanggaranController::class, 'konfirmasi'])->name('pelanggaran.konfirmasi');
        Route::post('pelanggaran/{pelanggaran}/tolak',           [AdminPelanggaranController::class, 'tolak'])->name('pelanggaran.tolak');
        Route::post('pelanggaran/{pelanggaran}/batal-konfirmasi',[AdminPelanggaranController::class, 'batalKonfirmasi'])->name('pelanggaran.batal-konfirmasi');
        Route::post('pelanggaran/konfirmasi-bulk',               [AdminPelanggaranController::class, 'konfirmasiBulk'])->name('pelanggaran.konfirmasi-bulk');

        // Surat Peringatan
        Route::get('surat-peringatan',                                     [SuratPeringatanController::class, 'index'])->name('surat-peringatan.index');
        Route::get('surat-peringatan/create',                              [SuratPeringatanController::class, 'create'])->name('surat-peringatan.create');
        Route::post('surat-peringatan',                                    [SuratPeringatanController::class, 'store'])->name('surat-peringatan.store');
        Route::get('surat-peringatan/{suratPeringatan}',                   [SuratPeringatanController::class, 'show'])->name('surat-peringatan.show');
        Route::get('surat-peringatan/{suratPeringatan}/download',          [SuratPeringatanController::class, 'download'])->name('surat-peringatan.download');
        Route::get('surat-peringatan/{suratPeringatan}/preview',           [SuratPeringatanController::class, 'preview'])->name('surat-peringatan.preview');
        Route::patch('surat-peringatan/{suratPeringatan}/regenerate-pdf',  [SuratPeringatanController::class, 'regeneratePdf'])->name('surat-peringatan.regenerate-pdf');
        Route::patch('surat-peringatan/{suratPeringatan}/toggle-status',   [SuratPeringatanController::class, 'toggleStatus'])->name('surat-peringatan.toggle-status');
        Route::delete('surat-peringatan/{suratPeringatan}',                [SuratPeringatanController::class, 'destroy'])->name('surat-peringatan.destroy');

        // Laporan & Export
        Route::get('laporan',                       [LaporanController::class, 'index'])->name('laporan.index');
        Route::get('laporan/siswa',                 [LaporanController::class, 'siswa'])->name('laporan.siswa');
        Route::get('laporan/chart-data',            [LaporanController::class, 'chartData'])->name('laporan.chart-data');
        Route::get('laporan/export-pelanggaran',    [LaporanController::class, 'exportPelanggaranExcel'])->name('laporan.export-pelanggaran-excel');
        Route::get('laporan/export-rekap-excel',    [LaporanController::class, 'exportRekapExcel'])->name('laporan.export-rekap-excel');
    });

    // ─── WALI KELAS ───────────────────────────────────────────────
    Route::prefix('wali-kelas')->name('wali-kelas.')->middleware('role:wali_kelas')->group(function () {
        Route::resource('siswa',       SiswaWaliKelasController::class)->only(['index', 'show']);
        Route::resource('pelanggaran', WaliKelasPelanggaranController::class);
    });
});

Route::redirect('/', '/dashboard');
