<?php

use App\Http\Controllers\Api\CategoryController;
use App\Http\Controllers\Api\AuthController;
use App\Http\Controllers\Api\CustomerController;
use App\Http\Controllers\Api\ItemController;
use App\Models\Customer;
use App\Models\Item;
use Illuminate\Support\Facades\Route;

// auth:api -> Passport checks the Bearer token        (401 if missing/invalid)
// ->can()  -> the model's Policy checks the user's role (403 if not allowed)

Route::post('/auth/login', [AuthController::class, 'login'])
    ->middleware('throttle:10,1'); // max 10 login attempts per minute per IP

Route::middleware('auth:api')->group(function () {
    Route::post('/auth/logout', [AuthController::class, 'logout']);
    Route::get('/auth/me', [AuthController::class, 'me']);
    Route::get('/categories', [CategoryController::class, 'index']);

    Route::get('/items', [ItemController::class, 'index'])->can('viewAny', Item::class);
    Route::post('/items', [ItemController::class, 'store'])->can('create', Item::class);
    Route::get('/items/{item}', [ItemController::class, 'show'])->can('view', 'item');
    Route::match(['put', 'patch'], '/items/{item}', [ItemController::class, 'update'])->can('update', 'item');
    Route::delete('/items/{item}', [ItemController::class, 'destroy'])->can('delete', 'item');

    Route::get('/customers', [CustomerController::class, 'index'])->can('viewAny', Customer::class);
    Route::post('/customers', [CustomerController::class, 'store'])->can('create', Customer::class);
    Route::get('/customers/{customer}', [CustomerController::class, 'show'])->can('view', 'customer');
    Route::match(['put', 'patch'], '/customers/{customer}', [CustomerController::class, 'update'])->can('update', 'customer');
    Route::delete('/customers/{customer}', [CustomerController::class, 'destroy'])->can('delete', 'customer');
});