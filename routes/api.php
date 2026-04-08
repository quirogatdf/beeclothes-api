<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

use App\Http\Controllers\colorController;
use App\Http\Controllers\sizeController;
use App\Http\Controllers\productController;
use App\Http\Controllers\categoryController;
use App\Http\Controllers\supplierController;
use App\Http\Controllers\orderController;
use App\Http\Controllers\OrderDetailController;
use App\Http\Controllers\SaleController;
use App\Http\Controllers\variantController;
use App\Http\Controllers\Api\AuthController;
use App\Http\Controllers\SiteSettingController;


/*Route::get('/user', function (Request $request) {
    return $request->user();
})->middleware('auth:sanctum');*/

Route::post('/login', [AuthController::class, 'login']);
Route::get('/products', [productController::class, 'index']);

// Public category endpoints
Route::get('/categories', [categoryController::class, 'index']);
Route::get('/categories/tree', [categoryController::class, 'tree']);
Route::get('/categories/{id}/products', [categoryController::class, 'products']);

Route::middleware('auth:sanctum')->prefix('admin')->group(function () {

    // Colors API route
    Route::apiResource('/colors', colorController::class);

    // Sizes API route
    Route::apiResource('/sizes', sizeController::class);

    // Categories API route
    Route::apiResource('/categories', categoryController::class);

    //Products API route
    Route::apiResource('/products', productController::class);

    //Variant API route
    Route::apiResource('/variants', variantController::class);

    //Suppliers API route
    Route::apiResource('/suppliers', supplierController::class);

    //Orders API route
    Route::apiResource('/orders', orderController::class);

    //OrderDetails API route
    Route::apiResource('/order-details', OrderDetailController::class);

    //Sales API route
    Route::apiResource('/sales', SaleController::class);

    // Site Settings & Menu
    Route::get('/config', [SiteSettingController::class, 'getConfig']);
    Route::put('/config', [SiteSettingController::class, 'updateConfig']);
    Route::put('/menu', [SiteSettingController::class, 'saveMenu']);
});

// Public menu endpoint (no auth required)
Route::get('/menu', [SiteSettingController::class, 'getMenu']);
