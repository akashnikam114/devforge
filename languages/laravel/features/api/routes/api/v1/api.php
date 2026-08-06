<?php

use Illuminate\Support\Facades\Route;

Route::get('health', function () {
    return response()->json([
        'status' => true,
        'message' => 'API is running.',
    ]);
});
