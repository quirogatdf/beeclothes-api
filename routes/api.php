<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

use App\Http\Controllers\colorController;
use App\Http\Controllers\sizeController;
use App\Http\Controllers\productController;
use App\Http\Controllers\categoryController;
use App\Http\Controllers\supplierController;
use App\Http\Controllers\Api\AuthController;


/*Route::get('/user', function (Request $request) {
    return $request->user();
})->middleware('auth:sanctum');*/

Route::post('/login', [AuthController::class, 'login']);
Route::get('/products', [productController::class, 'index']);

Route::middleware('auth:sanctum')->prefix('admin')->name('admin')->group(function () {

    // Colors API route
    Route::apiResource('/colors', colorController::class);

    // Sizes API route
    Route::apiResource('/sizes', sizeController::class);

    // Categories API route
    Route::apiResource('/categories', categoryController::class);

    //Products API route
    Route::apiResource('/products', productController::class);

    //Variant API route
    Route::apiResource('/variants', productController::class);

    //Suppliers API route
    Route::apiResource('/suppliers', supplierController::class);
});
