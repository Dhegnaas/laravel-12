<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\UserController;
use App\Http\Controllers\ProductController;
use App\Http\Controllers\AddressController;
use App\Http\Controllers\JobPostsController;


// Public routes
Route::post('/register', [UserController::class, 'save']);
Route::post('/login', [AuthController::class, 'login']);

// Protected routes
Route::middleware('auth:sanctum')->group(function () {
    // Auth routes
    Route::post('/logout', [AuthController::class, 'logout']);
    Route::get('/user', [AuthController::class, 'user']);
    
    // User routes
    Route::post('/users', [UserController::class, 'save']);
    Route::get('/users', [UserController::class, 'list']);
    Route::get('/users/pagination', [UserController::class, 'pagination']);
    Route::get('/users/filtration', [UserController::class, 'filtration']);
    Route::get('/users/{user}', [UserController::class, 'show']);
    Route::put('/users/{user}', [UserController::class, 'update']);
    // Route::patch('/users/{user}', [UserController::class, 'update']);
    Route::delete('/users/{user}', [UserController::class, 'destroy']);
    
    // User status management
    Route::post('/users/{user}/submit', [UserController::class, 'submit']);
    Route::post('/users/{user}/cancel', [UserController::class, 'cancel']);
    
    // Product routes
    Route::apiResource('products', ProductController::class);
    // Address routes (Si gaar ah loo qoray, sidaad codsatay)
    Route::get('/address', [AddressController::class, 'index']);
    Route::post('/address', [AddressController::class, 'store']);
    Route::get('/address/{address}', [AddressController::class, 'show']);
    Route::put('/address/{address}', [AddressController::class, 'update']);
    Route::patch('/address/{address}', [AddressController::class, 'update']);
    Route::delete('/address/{address}', [AddressController::class, 'destroy']);

    // Address Status management
    Route::post('/address/{address}/submit', [AddressController::class, 'submit']);
    Route::post('/address/{address}/cancel', [AddressController::class, 'cancel']);
    // Address filtering
    Route::get('/address/filters', [AddressController::class, 'filters']);
    Route::get('/address/pagination', [AddressController::class, 'pagination']);

    // Job routes
    route::get('/jobs', [JobPostsController::class, 'index']);
    route::post('/jobs', [JobPostsController::class, 'store']);
    route::get('/jobs/pagination', [JobPostsController::class, 'pagination']);
    route::get('/jobs/filtration', [JobPostsController::class, 'filtration']);
    route::get('/jobs/{jobs}', [JobPostsController::class, 'show']);
    route::put('/jobs/{jobs}', [JobPostsController::class, 'update']);
    route::delete('/jobs/{jobs}', [JobPostsController::class, 'destroy']);
    route::post('/jobs/{jobs}/submit', [JobPostsController::class, 'submit']);
    route::post('/jobs/{jobs}/cancel', [JobPostsController::class, 'cancel']);
});
