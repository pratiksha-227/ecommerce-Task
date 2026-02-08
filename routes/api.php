<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api\AuthController as ApiAuthController;
use App\Http\Controllers\Api\ProductController as ApiProductController;
use App\Http\Controllers\Api\CartController as ApiCartController;
use App\Http\Controllers\Api\CheckoutController as ApiCheckoutController;

// Auth APIs
Route::post('/login', [ApiAuthController::class, 'login']);
Route::post('/register', [ApiAuthController::class, 'register']);

// Products
Route::get('/products', [ApiProductController::class, 'index']);
Route::get('/products/{id}', [ApiProductController::class, 'show']);
Route::post('/products', [ApiProductController::class, 'store']);
Route::put('/products/{id}', [ApiProductController::class, 'update']);
Route::patch('/products/{id}', [ApiProductController::class, 'update']);
// POST with form-data (PHP only parses POST body); use this when sending multipart/form-data + file
Route::post('/products/{id}', [ApiProductController::class, 'update']);
Route::delete('/products/{id}', [ApiProductController::class, 'destroy']);
Route::delete('/products/{productId}/images/{imageId}', [ApiProductController::class, 'destroyImage']);

// Cart
Route::post('/cart', [ApiCartController::class, 'store']);
Route::get('/cart', [ApiCartController::class, 'index']);
Route::get('/cart/user/{user_id}', [ApiCartController::class, 'show']);
Route::put('/cart/{id}', [ApiCartController::class, 'update']);
Route::delete('/cart/{id}', [ApiCartController::class, 'destroy']);

// Checkout
Route::post('/checkout', [ApiCheckoutController::class, 'store']);
