<?php

use Illuminate\Support\Facades\Route;

// API-only application - web routes are minimal
Route::get('/', function () {
    return response()->json([
        'message' => 'Laravel API',
        'version' => '1.0.0',
    ]);
});
