<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\UserController;

Route::get('/user', function (Request $request) {
    return $request->user();
})->middleware('auth:sanctum');

Route::post('/get_price', [UserController::class, 'getPrice'])->name('get.price');
Route::post('/put_into_cart', [UserController::class, 'putIntoCart'])->name('put.into.cart');