<?php

\Illuminate\Support\Facades\Route::get('{path}', function () {
    return redirect()->route('admin.login');
})->where('path', 'admin|login');

\Illuminate\Support\Facades\Route::view('privacy-policy', 'pages.privacy-policy')->name('privacy-policy');
\Illuminate\Support\Facades\Route::view('terms-and-conditions', 'pages.terms-and-conditions')->name('terms-and-conditions');

\Illuminate\Support\Facades\Route::prefix('admin')->name('admin.')->group(function () {
    \Illuminate\Support\Facades\Route::middleware('guest')->group(function () {
        \Illuminate\Support\Facades\Route::get('login', [\App\Http\Controllers\Admin\Auth\LoginController::class, 'showLoginForm'])->name('login');
        \Illuminate\Support\Facades\Route::post('login', [\App\Http\Controllers\Admin\Auth\LoginController::class, 'login']);
    });

    \Illuminate\Support\Facades\Route::middleware(['auth', 'admin.maintenance'])->group(function () {
        \Illuminate\Support\Facades\Route::get('dashboard', [\App\Http\Controllers\Admin\DashboardController::class, 'index'])->name('dashboard');
        \Illuminate\Support\Facades\Route::post('change-password', [\App\Http\Controllers\Admin\Auth\LoginController::class, 'changePassword'])->name('password.change');
        \Illuminate\Support\Facades\Route::post('logout', [\App\Http\Controllers\Admin\Auth\LoginController::class, 'logout'])->name('logout');

        \Illuminate\Support\Facades\Route::prefix('banners')->group(function () {
            \Illuminate\Support\Facades\Route::get('all', [\App\Http\Controllers\Admin\BannerController::class, 'index'])->name('banners');
            \Illuminate\Support\Facades\Route::get('add', [\App\Http\Controllers\Admin\BannerController::class, 'create']);
            \Illuminate\Support\Facades\Route::post('add', [\App\Http\Controllers\Admin\BannerController::class, 'store']);
            \Illuminate\Support\Facades\Route::get('edit/{id}', [\App\Http\Controllers\Admin\BannerController::class, 'edit']);
            \Illuminate\Support\Facades\Route::post('edit/{id}', [\App\Http\Controllers\Admin\BannerController::class, 'update']);
            \Illuminate\Support\Facades\Route::get('delete/{id}', [\App\Http\Controllers\Admin\BannerController::class, 'destroy']);
            \Illuminate\Support\Facades\Route::post('change-status', [\App\Http\Controllers\Admin\BannerController::class, 'changeStatus']);
        });

        \Illuminate\Support\Facades\Route::prefix('push_notifications')->group(function () {
            \Illuminate\Support\Facades\Route::get('all', [\App\Http\Controllers\Admin\PushNotificationController::class, 'index'])->name('notification');
            \Illuminate\Support\Facades\Route::get('add', [\App\Http\Controllers\Admin\PushNotificationController::class, 'create']);
            \Illuminate\Support\Facades\Route::post('add', [\App\Http\Controllers\Admin\PushNotificationController::class, 'store']);
            \Illuminate\Support\Facades\Route::get('edit/{id}', [\App\Http\Controllers\Admin\PushNotificationController::class, 'edit']);
            \Illuminate\Support\Facades\Route::post('edit/{id}', [\App\Http\Controllers\Admin\PushNotificationController::class, 'update']);
            \Illuminate\Support\Facades\Route::get('delete/{id}', [\App\Http\Controllers\Admin\PushNotificationController::class, 'destroy']);
            \Illuminate\Support\Facades\Route::post('send/{id}', [\App\Http\Controllers\Admin\PushNotificationController::class, 'sendPushNotification']);
        });

        \Illuminate\Support\Facades\Route::prefix('restriction_settings')->group(function () {
            \Illuminate\Support\Facades\Route::get('all', [\App\Http\Controllers\Admin\RestrictionSettingController::class, 'index'])->name('restriction');
            \Illuminate\Support\Facades\Route::get('edit/{id}', [\App\Http\Controllers\Admin\RestrictionSettingController::class, 'edit']);
            \Illuminate\Support\Facades\Route::post('edit/{id}', [\App\Http\Controllers\Admin\RestrictionSettingController::class, 'update']);
        });

        \Illuminate\Support\Facades\Route::prefix('general_settings')->group(function () {
            \Illuminate\Support\Facades\Route::get('edit/{id}', [\App\Http\Controllers\Admin\GeneralSettingController::class, 'edit']);
            \Illuminate\Support\Facades\Route::post('edit/{id}', [\App\Http\Controllers\Admin\GeneralSettingController::class, 'update']);
        });

        \Illuminate\Support\Facades\Route::prefix('business_settings')->group(function () {
            \Illuminate\Support\Facades\Route::get('edit', [\App\Http\Controllers\Admin\BusinessSettingController::class, 'edit']);
            \Illuminate\Support\Facades\Route::post('update', [\App\Http\Controllers\Admin\BusinessSettingController::class, 'update']);
        });
    });
});
