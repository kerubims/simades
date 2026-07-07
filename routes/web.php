<?php

use App\Http\Controllers\Admin\DashboardController as AdminDashboardController;
use App\Http\Controllers\Admin\MeteranController;
use App\Http\Controllers\Admin\PelangganController;
use App\Http\Controllers\Admin\QrisController;
use App\Http\Controllers\Admin\TagihanController;
use App\Http\Controllers\Admin\TarifController;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\Warga\DashboardController as WargaDashboardController;
use App\Http\Controllers\Warga\RiwayatController;
use App\Http\Controllers\Warga\SlipController;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| Web Routes — SIMADES
|--------------------------------------------------------------------------
*/

// Root → landing page
Route::get('/', fn () => view('welcome'))->name('home');

// Auth
Route::get('/login', [AuthController::class, 'showLogin'])->name('login');
Route::post('/login', [AuthController::class, 'login'])->name('login.post');
Route::get('/register', [AuthController::class, 'showRegister'])->name('register');
Route::post('/register', [AuthController::class, 'register'])->name('register.post');
Route::post('/logout', [AuthController::class, 'logout'])->name('logout');

// ── Admin Routes ────────────────────────────────────────────────────────────
Route::prefix('admin')->name('admin.')->middleware(['role:admin'])->group(function () {

    // Dashboard
    Route::get('/dashboard', [AdminDashboardController::class, 'index'])->name('dashboard');

    // Pelanggan CRUD
    Route::resource('pelanggan', PelangganController::class)->except(['show']);

    // Catat Meteran
    Route::get('/meteran', [MeteranController::class, 'index'])->name('meteran.index');
    Route::post('/meteran', [MeteranController::class, 'store'])->name('meteran.store');
    Route::post('/meteran/massal', [MeteranController::class, 'storeMassal'])->name('meteran.massal');
    Route::put('/meteran/{idTagihan}', [MeteranController::class, 'update'])->name('meteran.update');

    // Tagihan & Pembayaran
    Route::get('/tagihan', [TagihanController::class, 'index'])->name('tagihan.index');
    Route::patch('/tagihan/{idTagihan}/lunas', [TagihanController::class, 'tandaiLunas'])->name('tagihan.lunas');
    Route::post('/tagihan/{idTagihan}/tolak', [TagihanController::class, 'tolakPembayaran'])->name('tagihan.tolak');

    // Tarif
    Route::get('/tarif', [TarifController::class, 'index'])->name('tarif.index');
    Route::put('/tarif', [TarifController::class, 'update'])->name('tarif.update');

    // QRIS Pembayaran
    Route::get('/qris', [QrisController::class, 'index'])->name('qris.index');
    Route::post('/qris', [QrisController::class, 'store'])->name('qris.store');
});

// ── Warga Routes ─────────────────────────────────────────────────────────────
Route::prefix('warga')->name('warga.')->middleware(['role:warga'])->group(function () {

    // Dashboard tagihan bulan ini
    Route::get('/dashboard', [WargaDashboardController::class, 'index'])->name('dashboard');

    // Riwayat pemakaian
    Route::get('/riwayat', [RiwayatController::class, 'index'])->name('riwayat');

    // Download slip PDF
    Route::get('/slip/{idTagihan}/download', [SlipController::class, 'download'])->name('slip.download');

    // Upload Bukti Pembayaran
    Route::post('/tagihan/{idTagihan}/upload-bukti', [WargaDashboardController::class, 'uploadBukti'])->name('tagihan.upload');
});
