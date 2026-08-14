<?php

use App\Http\Controllers\Admin\Auth\LoginController;
use App\Http\Controllers\Admin\AppReleaseController;
use App\Http\Controllers\Admin\BannerController;
use App\Http\Controllers\Admin\BusinessSettingController;
use App\Http\Controllers\Admin\DashboardController;
use App\Http\Controllers\Admin\GeneralSettingController;
use App\Http\Controllers\Admin\PushNotificationController;
use App\Http\Controllers\Admin\RestrictionSettingController;
use Illuminate\Support\Facades\Route;

Route::get('{path}', function () {
    return redirect()->route('admin.login');
})->where('path', 'admin|login');

Route::view('privacy-policy', 'pages.privacy-policy')->name('privacy-policy');
Route::view('terms-and-conditions', 'pages.terms-and-conditions')->name('terms-and-conditions');

Route::prefix('admin')->name('admin.')->group(function () {
    Route::middleware('guest')->group(function () {
        Route::get('login', [LoginController::class, 'showLoginForm'])->name('login');
        Route::post('login', [LoginController::class, 'login']);
    });

    Route::middleware(['auth', 'admin.maintenance'])->group(function () {
        Route::get('dashboard', [DashboardController::class, 'index'])->name('dashboard');
        Route::post('change-password', [LoginController::class, 'changePassword'])->name('password.change');
        Route::post('logout', [LoginController::class, 'logout'])->name('logout');

        Route::prefix('banners')->group(function () {
            Route::get('all', [BannerController::class, 'index'])->name('banners');
            Route::get('add', [BannerController::class, 'create']);
            Route::post('add', [BannerController::class, 'store']);
            Route::get('edit/{id}', [BannerController::class, 'edit']);
            Route::post('edit/{id}', [BannerController::class, 'update']);
            Route::delete('delete/{id}', [BannerController::class, 'destroy']);
            Route::post('change-status', [BannerController::class, 'changeStatus']);
        });

        Route::prefix('push_notifications')->group(function () {
            Route::get('all', [PushNotificationController::class, 'index'])->name('notification');
            Route::get('add', [PushNotificationController::class, 'create']);
            Route::post('add', [PushNotificationController::class, 'store']);
            Route::get('edit/{id}', [PushNotificationController::class, 'edit']);
            Route::post('edit/{id}', [PushNotificationController::class, 'update']);
            Route::delete('delete/{id}', [PushNotificationController::class, 'destroy']);
            Route::post('send/{id}', [PushNotificationController::class, 'sendPushNotification']);
            Route::post('change-status', [PushNotificationController::class, 'changeStatus']);
        });

        Route::prefix('app_releases')->group(function () {
            Route::get('all', [AppReleaseController::class, 'index'])->name('app_releases');
            Route::get('add', [AppReleaseController::class, 'create']);
            Route::post('add', [AppReleaseController::class, 'store']);
            Route::get('edit/{id}', [AppReleaseController::class, 'edit']);
            Route::post('edit/{id}', [AppReleaseController::class, 'update']);
            Route::delete('delete/{id}', [AppReleaseController::class, 'destroy']);
        });

        Route::prefix('restriction_settings')->group(function () {
            Route::get('all', [RestrictionSettingController::class, 'index'])->name('restriction_settings');
            Route::get('edit/{id}', [RestrictionSettingController::class, 'edit']);
            Route::post('edit/{id}', [RestrictionSettingController::class, 'update']);
        });

        Route::prefix('general_settings')->group(function () {
            Route::get('edit/{id}', [GeneralSettingController::class, 'edit']);
            Route::post('edit/{id}', [GeneralSettingController::class, 'update']);
        });

        Route::prefix('business_settings')->group(function () {
            Route::get('edit', [BusinessSettingController::class, 'edit']);
            Route::post('update', [BusinessSettingController::class, 'update']);
        });
    });
});
