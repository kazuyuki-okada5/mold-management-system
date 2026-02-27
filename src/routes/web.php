<?php

use App\Http\Controllers\ProfileController;
use Illuminate\Support\Facades\Route;

// コントローラーが未作成なのでクロージャ（使い捨て関数）で仮置き

// トップはログインへリダイレクト
Route::get('/', function() { return redirect()->route('login');});

// 認証必須（全ユーザー共通）
Route::middleware('auth')->group(function () {

    // ダッシュボード（一般ユーザー）
    Route::get('/dashboard', function() { return view('dashboard'); })->name('dashboard');

    // プロフィール（Breeze標準）
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');

    // 金型（Controllerに差し替え要）
    Route::get('/molds', function() { return 'molds index'; })->name('molds.index');
    Route::get('/molds/{id}', function($id) { return 'molds show'; })->name('molds.show');
    Route::post('/molds/{id}/use-start', function($id) { return 'use start'; })->name('molds.use-start');
    Route::post('/molds/{id}/use-end', function($id) { return 'use end'; })->name('molds.use-end');

    // 予約（Controllerに差し替え要）
    Route::get('/reservations', function() { return 'reservations index'; })->name('reservations.index');
    Route::get('/reservations/create', function() { return 'reservations create'; })->name('reservations.create');
    Route::post('/reservations', function() { return 'reservations store'; })->name('reservations.store');
    Route::get('/reservations/{id}', function($id) { return 'reservations show'; })->name('reservations.show');
    Route::post('/reservations/{id}/cancel', function($id) { return 'cancel'; })->name('reservations.cancel');

    // 使用履歴（Controllerに差し替え要）
    Route::get('/usage-logs', function() { return 'usage logs'; })->name('usage-logs.index');
});

// 管理者専用
Route::middleware(['auth', 'admin'])->prefix('admin')->name('admin.')->group(function () {

    // 管理ダッシュボード
    Route::get('/dashboard', function() { return 'admin dashboard'; })->name('dashboard');

    // 金型管理（Controllerに差し替え要）
    Route::get('/molds/create', function() { return 'admin molds create'; })->name('molds.create');
    Route::post('/molds', function() { return 'admin molds store'; })->name('molds.store');
    Route::get('/molds/{id}/edit', function($id) { return 'admin molds edit'; })->name('molds.edit');
    Route::put('/molds/{id}', function($id) { return 'admin molds update'; })->name('molds.update');
    Route::delete('/molds/{id}', function($id) { return 'admin molds destroy'; })->name('molds.destroy');

    // 予約承認（Controllerに差し替え要）
    Route::post('/reservations/{id}/approve', function($id) { return 'approve'; })->name('reservations.approve');
    Route::post('/reservations/{id}/reject', function($id) { return 'reject'; })->name('reservations.reject');

    // ユーザー管理（Controllerに差し替え要）
    Route::get('/users', function() { return 'admin users'; })->name('users.index');

    // 稼働率統計（Controllerに差し替え要）
    Route::get('/molds/stats', function() { return 'admin stats'; })->name('molds.stats');
});

require __DIR__.'/auth.php';
