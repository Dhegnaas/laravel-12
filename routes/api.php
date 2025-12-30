<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\UserController;
use App\Http\Controllers\ProductController;
use App\Http\Controllers\AddressController;


// Public routes
Route::post('/register', [UserController::class, 'save']);
Route::post('/login', [AuthController::class, 'login']);

// Protected routes
Route::middleware('auth:sanctum')->group(function () {
    // Auth routes
    Route::post('/logout', [AuthController::class, 'logout']);
    Route::get('/user', [AuthController::class, 'user']);
    Route::put('/user', [UserController::class, 'updateProfile']);
    Route::patch('/user', [UserController::class, 'updateProfile']);
    Route::delete('/user', [UserController::class, 'deleteProfile']);
    
    // User routes
    Route::post('/user/save', [UserController::class, 'save']);
    Route::get('/users', [UserController::class, 'list']);
    Route::get('/users/{id}', [UserController::class, 'show']);
    Route::put('/users/{id}', [UserController::class, 'update']);
    Route::patch('/users/{id}', [UserController::class, 'update']);
    Route::delete('/users/{id}', [UserController::class, 'destroy']);
    
    // Product routes
    Route::apiResource('products', ProductController::class);
    // Address routes
Route::get('/address', [AddressController::class, 'index']); // GET all addresses
Route::post('/address', [AddressController::class, 'store']); // POST create new address
Route::get('/address/{address}', [AddressController::class, 'show']); // GET single address
Route::put('/address/{address}', [AddressController::class, 'update']); // PUT update address
Route::patch('/address/{address}', [AddressController::class, 'update']); // PATCH update address
Route::delete('/address/{address}', [AddressController::class, 'destroy']); // DELETE address   
});
