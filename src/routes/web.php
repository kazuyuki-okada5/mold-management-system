<?php

use App\Http\Controllers\Admin;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\MoldController;
use App\Http\Controllers\ReservationController;
use App\Http\Controllers\UsageController;
use App\Http\Controllers\UsageLogController;
use Illuminate\Support\Facades\Route;

// コントローラーが未作成なのでクロージャ（使い捨て関数）で仮置き

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
    Route::get('/dashboard', [Admin\DashboardController::class, 'index'])->name('dashboard');

    // 金型登録・編集・削除・統計（adminのみ）
    Route::get('/molds/stats', [Admin\MoldController::class, 'stats'])->name('molds.stats');
    Route::get('/molds/create', [Admin\MoldController::class, 'create'])->name('molds.create');
    Route::post('/molds', [Admin\MoldController::class, 'store'])->name('molds.store');
    Route::get('/molds/{mold}/edit', [Admin\MoldController::class, 'edit'])->name('molds.edit');
    Route::put('/molds/{mold}', [Admin\MoldController::class, 'update'])->name('molds.update');
    Route::delete('/molds/{mold}', [Admin\MoldController::class, 'destroy'])->name('molds.destroy');

    // 予約承認・否認
    Route::post('/reservations/{reservation}/approve', [Admin\ReservationController::class, 'approve'])->name('reservations.approve');
    Route::post('/reservations/{reservation}/reject', [Admin\ReservationController::class, 'reject'])->name('reservations.reject');


    // ユーザー管理
    Route::get('/users', [Admin\UserController::class, 'index'])->name('users.index');

});

require __DIR__.'/auth.php';
