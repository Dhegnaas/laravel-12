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
    Route::delete('/users/{user}', [UserController::class, 'delete']);
    
    // User status management
    Route::post('/users/{user}/submit', [UserController::class, 'submit']);
    Route::post('/users/{user}/cancel', [UserController::class, 'cancel']);
    
    // Product routes
    route::get('/products', [ProductController::class, 'list']);
    route::post('/products', [ProductController::class, 'save']);
    route::get('/products/pagination', [ProductController::class, 'pagination']);
    route::get('/products/filtration', [ProductController::class, 'filtration']);
    route::get('/products/{product}', [ProductController::class, 'show']);
    Route::put('/products/{product}', [ProductController::class, 'update']);
    route::delete('/products/{product}', [ProductController::class, 'delete']);
    route::post('/products/{product}/submit', [ProductController::class, 'submit']);
    route::post('/products/{product}/cancel', [ProductController::class, 'cancel']);    



    // Address routes (Si gaar ah loo qoray, sidaad codsatay)
    route::get('/addresses', [AddressController::class, 'list']);
    route::post('/addresses', [AddressController::class, 'save']);
    route::get('/addresses/pagination', [AddressController::class, 'pagination']);
    route::get('/addresses/filtration', [AddressController::class, 'filtration']);
    route::get('/addresses/{address}', [AddressController::class, 'show']);
    Route::put('/addresses/{address}', [AddressController::class, 'update']);
    route::delete('/addresses/{address}', [AddressController::class, 'delete']);
    route::post('/addresses/{address}/submit', [AddressController::class, 'submit']);
    route::post('/addresses/{address}/cancel', [AddressController::class, 'cancel']);



    // Job routes
    route::get('/jobs', [JobPostsController::class, 'list']);
    route::post('/jobs', [JobPostsController::class, 'save']);
    route::get('/jobs/pagination', [JobPostsController::class, 'pagination']);
    route::get('/jobs/filtration', [JobPostsController::class, 'filtration']);
    route::get('/jobs/{JobPosts}', [JobPostsController::class, 'show']);
    Route::put('/jobs/{JobPosts}', [JobPostsController::class, 'update']);
    route::delete('/jobs/{JobPosts}', [JobPostsController::class, 'delete']);
    route::post('/jobs/{JobPosts}/submit', [JobPostsController::class, 'submit']);
    route::post('/jobs/{JobPosts}/cancel', [JobPostsController::class, 'cancel']);
});
