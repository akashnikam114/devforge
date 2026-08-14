<?php

use App\Http\Controllers\Admin\UserController;
use Illuminate\Support\Facades\Route;

Route::prefix('admin')->name('admin.')->middleware('auth')->group(function () {
    Route::prefix('users')->group(function () {
        Route::get('all', [UserController::class, 'index'])->name('users');
        Route::get('details/{id}', [UserController::class, 'show'])->name('users.details');
    });
});
