<?php

\Illuminate\Support\Facades\Route::get('{path}', function () {
    return redirect()->route('admin.login');
})->where('path', 'admin|login');

\Illuminate\Support\Facades\Route::prefix('admin')->name('admin.')->group(function () {
    \Illuminate\Support\Facades\Route::middleware('guest')->group(function () {
        \Illuminate\Support\Facades\Route::get('login', [\App\Http\Controllers\Admin\Auth\LoginController::class, 'showLoginForm'])->name('login');
        \Illuminate\Support\Facades\Route::post('login', [\App\Http\Controllers\Admin\Auth\LoginController::class, 'login']);
    });

    \Illuminate\Support\Facades\Route::middleware('auth')->group(function () {
        \Illuminate\Support\Facades\Route::get('dashboard', [\App\Http\Controllers\Admin\DashboardController::class, 'index'])->name('dashboard');
        \Illuminate\Support\Facades\Route::post('change-password', [\App\Http\Controllers\Admin\Auth\LoginController::class, 'changePassword'])->name('password.change');
        \Illuminate\Support\Facades\Route::post('logout', [\App\Http\Controllers\Admin\Auth\LoginController::class, 'logout'])->name('logout');
    });
});
