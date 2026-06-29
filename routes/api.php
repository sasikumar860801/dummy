<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\UserController;
use App\Http\Controllers\dealerController;
use SebastianBergmann\CodeCoverage\Report\Html\Dashboard;

Route::get('/user', function (Request $request) {
    return $request->user();
})->middleware('auth:sanctum');

Route::post('/get_price', [UserController::class, 'getPrice'])->name('get.price');
Route::get('/view_summary/{order_id}', [UserController::class, 'view_summary'])->name('view_summary');
Route::get('/search-devices', [UserController::class, 'searchDevices'])->name('api.search.devices');




Route::post('/get-otp', [dealerController::class, 'getOtp']);
Route::post('/verify-otp', [dealerController::class, 'verifyOtp']);

Route::middleware('dealer.auth')->group(function () {
    Route::post('/update-mpin', [dealerController::class, 'updateMpin']);
        Route::post('/verify-mpin', [dealerController::class, 'verify_mpin']);
    Route::post('/update-firebase', [dealerController::class, 'updateFirebase']);
    Route::get('/new-leads', [dealerController::class, 'newLeads']);
    Route::get('/live-leads', [dealerController::class, 'liveLeads']);
    Route::get('/history-leads', [dealerController::class, 'history_leads']);
    Route::get('/profile', [dealerController::class, 'profile']);
    Route::post('/placeBid', [dealerController::class, 'placeBid']);
Route::post('/create-dealer-stock', [dealerController::class, 'createDealerStock']);
Route::post('/edit-dealer-stock', [dealerController::class, 'editDealerStock']);
    Route::post('/delete-dealer-stock', [dealerController::class, 'deleteDealerStock']);
    Route::get('/list-dealer-stock', [dealerController::class, 'listDealerStock']);
        Route::post('/dashboard', [dealerController::class, 'dashboard']);
        Route::post('/cancel-lead-request', [dealerController::class, 'cancel_lead_request']);
        Route::post('/account-balance-history', [dealerController::class, 'account_balance_history']);
    Route::get('/buy-orders', [dealerController::class, 'buy_orders']);
    Route::get('/service-repairs', [dealerController::class, 'service_repairs']);

});

// Route::middleware('dealer.auth')->get('/dealer-products', function (Illuminate\Http\Request $request) {

//     $dealer = $request->attributes->get('dealer');

//     return response()->json([
//         'success' => true,
//         'message' => 'Dealer authenticated successfully',
//         'dealer_id' => $dealer->id,
//         'name' => $dealer->name,
//         'shop_name' => $dealer->shop_name
//     ]);
// });


// 3. Dashboard
// 4. canel req leads 
// 4. buy order list / pending , complleted
// 5./srvice repair list / pending , completed , cancelled
// 6. account balance history   