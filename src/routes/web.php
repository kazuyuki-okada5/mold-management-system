<?php

use App\Http\Controllers\DashboardController;
use App\Http\Controllers\MoldController;
use App\Http\Controllers\ReservationController;
use App\Http\Controllers\UsageController;
use App\Http\Controllers\UsageLogController;
use App\Http\Controllers\Admin\DashboardController as AdminDashboardController;
use App\Http\Controllers\Admin\MoldController as AdminMoldController;
use App\Http\Controllers\Admin\ReservationController as AdminReservationController;
use App\Http\Controllers\Admin\UserController as AdminUserController;
use Illuminate\Support\Facades\Route;

// トップはログインへリダイレクト
Route::get('/', function() { return redirect()->route('login');});

// 認証必須（全ユーザー共通）
Route::middleware('auth')->group(function () {

    // ダッシュボード（一般ユーザー）
    Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard');

    // 金型一覧・詳細（全ユーザー）
    Route::get('/molds', [MoldController::class, 'index'])->name('molds.index');
    Route::get('/molds/{mold}', [MoldController::class, 'show'])->name('molds.show');
    // 使用開始・終了
    Route::post('/molds/{mold}/use-start', [UsageController::class, 'start'])->name('molds.use-start');
    Route::post('/molds/{mold}/use-end', [UsageController::class, 'end'])->name('molds.use-end');

    // 予約（一覧・申請・詳細・キャンセル）
    Route::get('/reservations', [ReservationController::class, 'index'])->name('reservations.index');
    Route::get('/reservations/create', [ReservationController::class, 'create'])->name('reservations.create');
    Route::post('/reservations', [ReservationController::class, 'store'])->name('reservations.store');
    Route::get('/reservations/{reservation}', [ReservationController::class, 'show'])->name('reservations.show');
    Route::post('/reservations/{reservation}/cancel', [ReservationController::class, 'cancel'])->name('reservations.cancel');

    // 使用履歴
    Route::get('/usage-logs', [UsageLogController::class, 'index'])->name('usage-logs.index');
});

// 管理者専用
Route::middleware(['auth', 'admin'])->prefix('admin')->name('admin.')->group(function () {

    // 管理ダッシュボード
    Route::get('/dashboard', [AdminDashboardController::class, 'index'])->name('dashboard');

    // 金型登録・編集・削除・統計（adminのみ）
    Route::get('/molds/stats', [AdminMoldController::class, 'stats'])->name('molds.stats');
    Route::get('/molds/create', [AdminMoldController::class, 'create'])->name('molds.create');
    Route::post('/molds', [AdminMoldController::class, 'store'])->name('molds.store');
    Route::get('/molds/{mold}/edit', [AdminMoldController::class, 'edit'])->name('molds.edit');
    Route::put('/molds/{mold}', [AdminMoldController::class, 'update'])->name('molds.update');
    Route::delete('/molds/{mold}', [AdminMoldController::class, 'destroy'])->name('molds.destroy');

    // 予約承認・否認
    Route::post('/reservations/{reservation}/approve', [AdminReservationController::class, 'approve'])->name('reservations.approve');
    Route::post('/reservations/{reservation}/reject', [AdminReservationController::class, 'reject'])->name('reservations.reject');

    // ユーザー管理
    Route::get('/users', [AdminUserController::class, 'index'])->name('users.index');

});

require __DIR__.'/auth.php';
