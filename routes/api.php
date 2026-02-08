<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api\ProductController as ApiProductController;
use App\Http\Controllers\Api\CartController as ApiCartController;
use App\Http\Controllers\Api\CheckoutController as ApiCheckoutController;

// GET API - All products with multiple images
Route::get('/products', [ApiProductController::class, 'index']);

// Cart APIs (user_id hardcoded to 1 in controller)
Route::post('/cart', [ApiCartController::class, 'store']);
Route::get('/cart', [ApiCartController::class, 'index']);
Route::get('/cart/user/{user_id}', [ApiCartController::class, 'show']);
Route::put('/cart/{id}', [ApiCartController::class, 'update']);
Route::delete('/cart/{id}', [ApiCartController::class, 'destroy']);

// Checkout API (Stripe payment gateway integration)
Route::post('/checkout', [ApiCheckoutController::class, 'store']);
