<?php

use App\Http\Controllers\Admin\ActivityLogController;
use Illuminate\Support\Facades\Route;

Route::prefix('admin')->name('admin.')->middleware(['auth', 'admin.activity'])->group(function () {
    Route::prefix('activity_logs')->group(function () {
        Route::get('all', [ActivityLogController::class, 'index'])->name('activity_logs');
        Route::get('all-data', [ActivityLogController::class, 'data']);
    });
});
