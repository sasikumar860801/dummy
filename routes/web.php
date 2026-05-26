<?php

use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\DB;
use App\Http\Controllers\UserController;


Route::get('/', function () {
    return view('home');
});

Route::get('/brands', [UserController::class, 'allBrands'])->name('brands.all');
Route::get('/sell-old-phone/{slug}', [UserController::class, 'model'])->name('model.all');
Route::get('/search-models', [UserController::class, 'searchModels'])->name('search.models');
Route::get('/api/search-models', [UserController::class, 'searchModels'])->name('api.search.models');
