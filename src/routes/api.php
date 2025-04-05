<?php

use GuzzleHttp\Promise\Is;
use Illuminate\Http\Request;
use App\Http\Middleware\IsUserAuth;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\ProductController;
use App\Http\Middleware\IsAdmin;
use App\Models\Product;

// PUBLIC ROUTES
Route::post('login', [AuthController::class, 'login']);
Route::post('register', [AuthController::class, 'register']);

Route::middleware([IsUserAuth::class])->group(function () {
    Route::controller(AuthController::class)->group(function () {
        Route::get('me', 'user', 'getUser');
        Route::post('logout', 'logout');
    });

    Route::get('products', [ProductController::class, 'getProducts']);
});


Route::middleware([IsAdmin::class])->group(function () {
    Route::controller(ProductController::class)->group(function () {
        Route::get('products', 'getProducts');
        Route::post('product', 'addProduct');
        Route::get('/product/{id}', 'getProduct');
        Route::put('/product/{id}', 'updateProduct');
        Route::delete('/product/{id}', 'deleteProduct');
    });
});