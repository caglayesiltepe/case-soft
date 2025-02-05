<?php

use App\Http\Controllers\CustomerController;
use App\Http\Controllers\DiscountController;
use App\Http\Controllers\ProductController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\OrderController;


/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider and all of them will
| be assigned to the "api" middleware group. Make something great!
|
*/

Route::middleware('auth:sanctum')->get('/user', function (Request $request) {
    return $request->user();
});


Route::post('register', [AuthController::class, 'register']);
Route::post('login', [AuthController::class, 'login']);

Route::middleware('auth:sanctum')->post('logout', [AuthController::class, 'logout']);

Route::middleware('auth:sanctum')->post('order', [OrderController::class, 'store']);
Route::middleware('auth:sanctum')->get('order', [OrderController::class, 'index']);
Route::middleware('auth:sanctum')->delete('order/{id}', [OrderController::class, 'destroy'])->whereNumber('id');

Route::middleware('auth:sanctum')->get('customer', [CustomerController::class, 'index']);
Route::middleware('auth:sanctum')->post('customer', [CustomerController::class, 'store']);
Route::middleware('auth:sanctum')->delete('customer/{id}', [CustomerController::class, 'destroy'])->whereNumber('id');

Route::middleware('auth:sanctum')->get('product', [ProductController::class, 'index']);
Route::middleware('auth:sanctum')->post('product', [ProductController::class, 'store']);
Route::middleware('auth:sanctum')->delete('product/{id}', [ProductController::class, 'destroy'])->whereNumber('id');

Route::middleware('auth:sanctum')->get('discounted/{id}', [DiscountController::class, 'show'])->whereNumber('id');
