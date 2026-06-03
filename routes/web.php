<?php

use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\DB;
use App\Http\Controllers\UserController;


Route::get('/', function () {
    return view('home');
});

Route::get('/sell-old-phone', [UserController::class, 'allBrands'])->name('brands.all');
Route::get('/sell-old-phone/{slug}', [UserController::class, 'model'])->name('model.all');
Route::get('/search-models', [UserController::class, 'searchModels'])->name('search.models');
Route::get('/api/search-models', [UserController::class, 'searchModels'])->name('api.search.models');
Route::get('/sell-old-tablet', [UserController::class, 'tablet_brands'])->name('tablet.brands');
Route::get('/sell-old-tablet/sell-{brand}', [UserController::class, 'tablet_models'])->name('tablet.models');
Route::get('/sell-old-tablet/sell-{slug}', [UserController::class, 'tablet_models'])->name('tablet.models');
Route::get('/sell-old-mobile-phone/used-{slug}', [UserController::class, 'particular_model'])->name('particular_model');
Route::get('/sell-old-mobile-phone/evaluate/{model_slug}/{variant_slug}', [UserController::class, 'evaluate_phone'])->name('evaluate_phone');

Route::post('/send-otp', [UserController::class, 'sendOtp'])->name('send.otp');
Route::post('/verify-otp', [UserController::class, 'verifyOtp'])->name('verify.otp');
Route::post('/logout', [UserController::class, 'logout'])->name('logout');

Route::get('/get_attributes', [UserController::class, 'get_attributes'])->name('get_attributes');

Route::get('/cart', [UserController::class, 'cart'])->name('cart');
Route::get('/get_user_details', [UserController::class, 'getUserDetails'])->name('get.user.details');
Route::post('/submit_sell_order', [UserController::class, 'submitSellOrder'])->name('submit.sell.order');
Route::post('/put_into_cart', [UserController::class, 'putIntoCart'])->name('put.into.cart');

Route::get('/check-session', function() {
    return response()->json([
        'session_id' => session()->getId(),
        'user_id' => session('user_id'),
        'user_name' => session('user_name'),
        'lifetime' => config('session.lifetime') . ' minutes'
    ]);
});


Route::get('/profile', [UserController::class, 'profile'])->name('profile');
Route::post('/profile/update', [UserController::class, 'updateProfile'])->name('profile.update');
Route::get('/my-cart', [UserController::class, 'my_cart'])->name('my-cart');
Route::get('/my-orders', [UserController::class, 'my_orders'])->name('my-orders');
Route::post('/cancel-order', [UserController::class, 'cancel_order'])->name('cancel.order');


// http://127.0.0.1:8000/sell-old-phone/sell-oneplus