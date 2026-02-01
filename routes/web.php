<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\HomeController;
use App\Http\Controllers\SuratMasukController;
use App\Http\Controllers\SuratKeluarController;
use App\Http\Controllers\Admin\UserController as AdminUserController;
use App\Http\Controllers\Admin\RoleController as AdminRoleController;
use App\Http\Controllers\Admin\PermissionController as AdminPermissionController;
use App\Http\Controllers\Admin\ArchiveController as AdminArchiveController;
use App\Http\Controllers\Admin\ActivityLogController as AdminActivityLogController;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider and all of them will
| be assigned to the "web" middleware group. Make something great!
|
*/

// Guest-only authentication routes
Route::middleware('guest')->group(function () {
    Route::get('/login', [AuthController::class, 'showLogin'])->name('login');
    Route::post('/login', [AuthController::class, 'login'])->name('login.attempt');
});

// Authenticated routes
Route::middleware('auth')->group(function () {
    Route::post('/logout', [AuthController::class, 'logout'])->name('logout');

    // Home (User non-admin landing)
    Route::get('/', [HomeController::class, 'index'])->name('home');

    // Admin routes
    Route::prefix('admin')->group(function () {
        // Dashboard
        Route::get('/', [DashboardController::class, 'index'])->name('dashboard');
        Route::resource('users', AdminUserController::class)
            ->only(['index', 'store', 'show', 'update', 'destroy']);

        Route::resource('roles', AdminRoleController::class)
            ->only(['index', 'store', 'show', 'update', 'destroy']);

        Route::resource('permissions', AdminPermissionController::class)
            ->only(['index', 'store', 'show', 'update', 'destroy']);

        // Arsip (Global)
        Route::get('/archive', [AdminArchiveController::class, 'index'])->name('admin.arsip.index');
        Route::get('/archive/{id}', [AdminArchiveController::class, 'show'])->name('admin.arsip.show');
        Route::post('/archive/{id}/restore', [AdminArchiveController::class, 'restore'])->name('admin.arsip.restore');
        Route::delete('/archive/{id}', [AdminArchiveController::class, 'destroy'])->name('admin.arsip.destroy');

        // Activity Logs
        Route::get('/activity-logs', [AdminActivityLogController::class, 'index'])->name('admin.activity-logs.index');
        Route::get('/activity-logs/{id}', [AdminActivityLogController::class, 'show'])->name('admin.activity-logs.show');
    });

    // Non-admin: Surat Masuk
    Route::prefix('surat-masuk')->group(function () {
        Route::get('/', [SuratMasukController::class, 'index'])->name('surat-masuk.index');
        Route::post('/', [SuratMasukController::class, 'store'])->name('surat-masuk.store');
        Route::get('/{id}', [SuratMasukController::class, 'show'])->name('surat-masuk.show');
        Route::put('/{id}', [SuratMasukController::class, 'update'])->name('surat-masuk.update');
        Route::post('/{id}/verify', [SuratMasukController::class, 'verify'])->name('surat-masuk.verify');
        Route::post('/{id}/disposisi', [SuratMasukController::class, 'distribute'])->name('surat-masuk.distribute');
    });

    // Non-admin: Surat Keluar
    Route::prefix('surat-keluar')->group(function () {
        Route::get('/', [SuratKeluarController::class, 'index'])->name('surat-keluar.index');
        Route::post('/', [SuratKeluarController::class, 'store'])->name('surat-keluar.store');
        Route::get('/{id}', [SuratKeluarController::class, 'show'])->name('surat-keluar.show');
        Route::put('/{id}', [SuratKeluarController::class, 'update'])->name('surat-keluar.update');
        Route::post('/{id}/send', [SuratKeluarController::class, 'send'])->name('surat-keluar.send');
    });
});
