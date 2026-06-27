<?php

use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\DB;
use App\Http\Controllers\UserController;
use App\Http\Controllers\AdminController;

Route::get('/', function () {
    return view('home');
});


Route::get('/sell-old-phone', [UserController::class, 'allBrands'])->name('brands.all');
Route::get('/sell-old-phone/{slug}', [UserController::class, 'model'])->name('model.all');
Route::get('/search-models', [UserController::class, 'searchModels'])->name('search.models');
Route::get('/api/search-models', [UserController::class, 'searchModels'])->name('api.search.models');
Route::get('/sell-old-tablet', [UserController::class, 'tablet_brands'])->name('tablet.brands');
Route::get('/sell-old-tablet/sell-{brand}', [UserController::class, 'tablet_models'])->name('tablet.models');
Route::get('/sell-old-tablet/sell-{slug}', [UserController::class, 'tablet_models'])->name('tablet_models');// need to working on it 
Route::get('/sell-old-mobile-phone/used-{slug}', [UserController::class, 'particular_model'])->name('particular_model');
Route::get('/sell-old-mobile-phone/evaluate/{model_slug}/{variant_slug}', [UserController::class, 'evaluate_phone'])->name('evaluate_phone');
    Route::get('/buy-refurbished-mobile-phones/best-selling-phones', [AdminController::class, 'all_refubrished_phones'])->name('all_refubrished_phones');
Route::get('/buy-refurbished-mobile-phones/buy-{slug}/{order_id}', [AdminController::class, 'buy_refubrished_phones'])->name('buy_refubrished_phones');    

Route::post('/send-otp', [UserController::class, 'sendOtp'])->name('send.otp');
Route::post('/verify-otp', [UserController::class, 'verifyOtp'])->name('verify.otp');
Route::post('/logout', [UserController::class, 'logout'])->name('logout');

Route::get('/get_attributes', [UserController::class, 'get_attributes'])->name('get_attributes');

Route::get('/cart', [UserController::class, 'cart'])->name('cart');
Route::get('/get_user_details', [UserController::class, 'getUserDetails'])->name('get.user.details');
Route::post('/submit_sell_order', [UserController::class, 'submitSellOrder'])->name('submit.sell.order');
Route::post('/put_into_cart', [UserController::class, 'putIntoCart'])->name('put.into.cart');
Route::post('/processCheckout', [UserController::class, 'processCheckout'])->name('api.checkout.process');

Route::get('/check-session', function() {
    return response()->json([
        'session_id' => session()->getId(),
        'user_id' => session('user_id'),
        'user_name' => session('user_name'),
        'lifetime' => config('session.lifetime') . ' minutes'
    ]);
});

Route::view('/contact-us', 'contact_us')->name('contact-us');
Route::view('/about-us', 'about_us')->name('about-us');
Route::view('/privacy-policy', 'privacy_policy')->name('privacy-policy');
Route::view('/terms-and-conditions', 'terms_and_conditions')->name('terms-and-conditions');
Route::view('/faq', 'faq')->name('faq');
Route::view('/dealer-register', 'dealer-register')->name('dealer-register');


Route::get('/profile', [UserController::class, 'profile'])->name('profile');
Route::post('/profile/update', [UserController::class, 'updateProfile'])->name('profile.update');
Route::get('/my-cart', [UserController::class, 'my_cart'])->name('my-cart');
Route::get('/my-orders', [UserController::class, 'my_orders'])->name('my-orders');
Route::post('/cancel-order', [UserController::class, 'cancel_order'])->name('cancel.order');
Route::get('/buy-orders', [UserController::class, 'buy_orders'])->name('buy_orders');

Route::get('/service-repair', [UserController::class, 'service_repair'])->name('service_repair');
Route::post('/service-repair', [UserController::class, 'store_service_repair'])->name('store_service_repair');
Route::get('/my-service-repair', [UserController::class, 'my_service_repair'])->name('my_service_repair');

Route::get('/admin/login', [AdminController::class, 'showLogin'])->name('admin.login');
Route::post('/admin/login', [AdminController::class, 'login'])->name('admin.login.submit');

// Protected Admin Routes Group
Route::middleware([\App\Http\Middleware\AdminAuth::class])->prefix('admin')->group(function () {
    Route::get('/dashboard', [AdminController::class, 'dashboard'])->name('admin.dashboard');
    Route::post('/logout', [AdminController::class, 'logout'])->name('admin.logout');
    
    // Empty placeholders for the rest of your menus (to be built next)
    Route::get('/orders', function() { return view('admin.orders'); })->name('admin.orders');
    Route::get('/stock', function() { return view('admin.stock'); })->name('admin.stock');
    Route::get('/models', function() { return view('admin.models'); })->name('admin.models');

    Route::get('/orders', [AdminController::class, 'index'])->name('admin.orders');
    Route::post('/orders/update-status', [AdminController::class, 'updateStatus'])->name('admin.orders.updateStatus');
    Route::post('/orders/move-to-stock', [AdminController::class, 'moveToStock'])->name('admin.orders.moveToStock');

    // here stock section 

    Route::get('/stock', [AdminController::class, 'stock_index'])->name('admin.stock');
    Route::post('/stock/store', [AdminController::class, 'store'])->name('admin.stock.store');
    Route::post('/stock/update', [AdminController::class, 'update'])->name('admin.stock.update');
    Route::post('/stock/delete', [AdminController::class, 'destroy'])->name('admin.stock.delete');
    Route::post('/stock/update-assignment', [AdminController::class, 'updateAssignment'])->name('admin.stock.updateAssignment');
    // Quick model catalog search utility endpoint for Select2 dropdown
    Route::get('/api/search-models', [AdminController::class, 'searchModels']);

    //dealers section
   Route::get('/dealers', [AdminController::class, 'dealer_index'])->name('admin.dealers.index');
    Route::post('/dealers/save', [AdminController::class, 'dealer_storeOrUpdate'])->name('admin.dealers.save');
    Route::get('/dealers/edit/{id}', [AdminController::class, 'dealer_edit'])->name('admin.dealers.edit');
    Route::post('/dealers/toggle/{id}', [AdminController::class, 'dealer_toggleStatus'])->name('admin.dealers.toggle');

    //bidding section
   Route::get('/bidding', [AdminController::class, 'bidding_index'])->name('admin.bidding.index');
    Route::post('/bidding/save', [AdminController::class, 'bidding_storeOrUpdate'])->name('admin.bidding.save');
    Route::get('/bidding/edit/{id}', [AdminController::class, 'bidding_edit'])->name('admin.bidding.edit');
    Route::delete('/bidding/delete/{id}', [AdminController::class, 'bidding_destroy'])->name('admin.bidding.delete');

    Route::get('/dealer-stock', [AdminController::class, 'dealer_stock_index'])->name('admin.dealerStock.index');
    Route::post('/dealer-stock/save', [AdminController::class, 'dealer_stock_storeOrUpdate'])->name('admin.dealerStock.save');
    Route::get('/dealer-stock/edit/{id}', [AdminController::class, 'dealer_stock_edit'])->name('admin.dealerStock.edit');
    Route::post('/dealer-stock/approve/{id}', [AdminController::class, 'dealer_stock_approve'])->name('admin.dealerStock.approve');
    Route::delete('/dealer-stock/reject/{id}', [AdminController::class, 'dealer_stock_reject'])->name('admin.dealerStock.reject');
    
        Route::get('/buy-orders', [AdminController::class, 'buy_orders'])->name('admin.buy_orders');
        Route::get('/service-repairs', [AdminController::class, 'service_repairs'])->name('admin.service_repairs');


});

// http://127.0.0.1:8000/sell-old-phone/sell-oneplus