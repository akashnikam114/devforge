<?php

use App\Http\Controllers\Admin\AppReleaseController;
use Illuminate\Support\Facades\Route;

Route::prefix('admin')->name('admin.')->middleware('auth')->group(function () {
    Route::prefix('app_releases')->group(function () {
        Route::get('all', [AppReleaseController::class, 'index'])->name('app_releases');
        Route::get('add', [AppReleaseController::class, 'create']);
        Route::post('add', [AppReleaseController::class, 'store']);
        Route::get('edit/{id}', [AppReleaseController::class, 'edit']);
        Route::post('edit/{id}', [AppReleaseController::class, 'update']);
        Route::delete('delete/{id}', [AppReleaseController::class, 'destroy']);
    });
});
