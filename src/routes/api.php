<?php

use GuzzleHttp\Promise\Is;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

use App\Http\Controllers\AuthController;
use App\Http\Controllers\ProductController;
use App\Http\Controllers\GenerateDocumentController;

use App\Http\Middleware\IsAdmin;
use App\Http\Middleware\IsUserAuth;


// PUBLIC ROUTES
Route::post('login', [AuthController::class, 'login']);
Route::post('register', [AuthController::class, 'register']);

// PRIVATE ROUTES
Route::middleware([IsUserAuth::class])->group(function () {
    // Agroup routes for AuthController
    Route::controller(AuthController::class)->group(function () {
        Route::get('me', 'getUser');
        Route::post('logout', 'logout');
    });

    Route::get('products', [ProductController::class, 'getProducts']);

    Route::get('generate-dni', [GenerateDocumentController::class, 'generateDni']);
    Route::get('generate-cif', [GenerateDocumentController::class, 'generateCif']);
    Route::get('generate-nie', [GenerateDocumentController::class, 'generateNie']);
    Route::get('generate-nif', [GenerateDocumentController::class, 'generateNif']);
    Route::get('generate-ssn', [GenerateDocumentController::class, 'generateSsn']);

    Route::post('validate-dni', [GenerateDocumentController::class, 'validateDni']);
    Route::post('validate-cif', [GenerateDocumentController::class, 'validateCif']);
    Route::post('validate-nie', [GenerateDocumentController::class, 'validateNie']);
    Route::post('validate-nif', [GenerateDocumentController::class, 'validateNif']);
    Route::post('validate-ssn', [GenerateDocumentController::class, 'validateSsn']);
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
