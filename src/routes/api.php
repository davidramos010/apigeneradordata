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

/**
 * |--------------------------------------------------------------------------
 * | USER ROUTES
 * |--------------------------------------------------------------------------
 * | These routes are protected by the IsUserAuth middleware, which checks if the
 * | user is authenticated. Only authenticated users can access these routes.          
 * | The AuthController handles user-related actions such as retrieving user information and logging out.
 * | The ProductController allows authenticated users to view the list of products.
 * | The GenerateDocumentController provides endpoints for generating various types of documents (DNI, CIF, NIE, NIF, SSN) for authenticated users.
 * |--------------------------------------------------------------------------
 * @authenticated
 */
Route::middleware([IsUserAuth::class])->group(function () {
    Route::controller(AuthController::class)->group(function () {
        Route::get('me', 'user', 'getUser');
        Route::post('logout', 'logout');
    });

    Route::get('products', [ProductController::class, 'getProducts']);

    Route::get('generate-dni', [GenerateDocumentController::class, 'generateDni']);
    Route::get('generate-cif', [GenerateDocumentController::class, 'generateCif']);
    Route::get('generate-nie', [GenerateDocumentController::class, 'generateNie']);
    Route::get('generate-nif', [GenerateDocumentController::class, 'generateNif']);
    Route::get('generate-ssn', [GenerateDocumentController::class, 'generateSsn']);
    Route::get('generate-cif-by-type/{strType}', [GenerateDocumentController::class, 'generateCifByType']);
});

/**
 * |--------------------------------------------------------------------------
 * | ADMIN ROUTES
 * |--------------------------------------------------------------------------
 * | These routes are protected by the IsAdmin middleware, which checks if the
 * | authenticated user has admin privileges. Only users with the 'admin' role can access these routes.
 * | The ProductController handles CRUD operations for products, allowing admins to manage the product catalog.
 * |--------------------------------------------------------------------------
 * @authenticated
 */
Route::middleware([IsAdmin::class])->group(function () {
    Route::controller(ProductController::class)->group(function () {
        Route::get('products', 'getProducts');
        Route::post('product', 'addProduct');
        Route::get('/product/{id}', 'getProduct');
        Route::put('/product/{id}', 'updateProduct');
        Route::delete('/product/{id}', 'deleteProduct');
    });
});
