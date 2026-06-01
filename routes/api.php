<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\UserController;

Route::get('/user', function (Request $request) {
    return $request->user();
})->middleware('auth:sanctum');

Route::post('/get_price', [UserController::class, 'getPrice'])->name('get.price');
Route::get('/view_summary/{order_id}', [UserController::class, 'view_summary'])->name('view_summary');
