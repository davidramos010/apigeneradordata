<?php

use GuzzleHttp\Promise\Is;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

use App\Http\Controllers\AuthController;
use App\Http\Controllers\ProductController;
use App\Http\Controllers\GenerateDocumentController;
use App\Http\Controllers\GenerateFinancialController;
use App\Http\Controllers\GenerateCupsController;

use App\Http\Middleware\IsAdmin;
use App\Http\Middleware\IsUserAuth;
use App\Http\Middleware\EnforceUserDailyLimit;
use App\Http\Middleware\EnforceIpDailyLimit;


// PUBLIC ROUTES
Route::post('login', [AuthController::class, 'login'])->middleware([EnforceUserDailyLimit::class]);
Route::post('register', [AuthController::class, 'register'])->middleware([EnforceIpDailyLimit::class]);

/**
 * |--------------------------------------------------------------------------
 * | USER ROUTES
 * |--------------------------------------------------------------------------
 * | These routes are protected by the IsUserAuth middleware, which checks if the
 * | user is authenticated. Only authenticated users can access these routes.          
 * | The AuthController handles user-related actions such as retrieving user information and logging out.
 * | The ProductController allows authenticated users to view the list of products.
 * | The GenerateDocumentController provides endpoints for generating various types of documents (DNI, CIF, NIE, NIF, SSN) for authenticated users.
 * | EnforceUserDailyLimit applies each user's daily request quota (1000/day by default for role 'user', unlimited for 'admin', with per-user overrides).
 * |--------------------------------------------------------------------------
 * @authenticated
 */
Route::middleware([IsUserAuth::class, EnforceUserDailyLimit::class])->group(function () {
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
    Route::get('generate-cif-by-type', [GenerateDocumentController::class, 'generateCifByType']);
    Route::get('validate-document', [GenerateDocumentController::class, 'validateDocument']);

    Route::get('generate-iban',    [GenerateFinancialController::class, 'generateIban']);
    Route::get('validate-iban',    [GenerateFinancialController::class, 'validateIban']);
    Route::get('generate-cuenta',  [GenerateFinancialController::class, 'generateCuenta']);
    Route::get('generate-tarjeta', [GenerateFinancialController::class, 'generateTarjeta']);

    Route::prefix('cups')->group(function () {
        Route::post('generate', [GenerateCupsController::class, 'generate']);
        Route::post('validate', [GenerateCupsController::class, 'validate']);
    });
});

/**
 * |--------------------------------------------------------------------------
 * | ADMIN ROUTES
 * |--------------------------------------------------------------------------
 * | These routes are protected by the IsAdmin middleware, which checks if the
 * | authenticated user has admin privileges. Only users with the 'admin' role can access these routes.
 * | The ProductController handles CRUD operations for products, allowing admins to manage the product catalog.
 * | EnforceUserDailyLimit is also applied here for consistency, but admins are always unlimited (no-op for them).
 * |--------------------------------------------------------------------------
 * @authenticated
 */
Route::middleware([IsAdmin::class, EnforceUserDailyLimit::class])->group(function () {
    Route::controller(ProductController::class)->group(function () {
        Route::get('products', 'getProducts');
        Route::post('product', 'addProduct');
        Route::get('/product/{id}', 'getProduct');
        Route::put('/product/{id}', 'updateProduct');
        Route::delete('/product/{id}', 'deleteProduct');
    });
});
