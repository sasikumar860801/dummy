<?php

namespace App\Http\Controllers\Customer;

use App\Models\Order;
use App\Models\OrderDetail;
use App\Models\PaymentRequest;
use App\Models\User;
use App\Utils\Helpers;
use App\Http\Controllers\Controller;
use App\Library\Payer;
// use App\Library\Payment as PaymentInfo;
use App\Library\Receiver;
use App\Models\ShippingAddress;
use App\Models\ShippingType;
use App\Models\BusinessSetting;
use App\Models\Cart;
use App\Models\EmiTransaction;
use App\Models\Payment;

use App\Models\CartShipping;
use App\Models\Currency;
// use App\Traits\Payment;
use App\Utils\CartManager;
use App\Utils\Convert;
use App\Utils\OrderManager;
use Brian2694\Toastr\Facades\Toastr;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Routing\Redirector;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Http;
use Carbon\Carbon;
use Illuminate\Support\Str;

class PaymentController extends Controller
{
   // use Payment;
   
  public function webhook_payu(Request $request)
{
    // Log::info($request->all());
    // Log::info('This is webhook');
    
    $transactionId = $request->mihpayid;
    $paymode = $request->mode;
    $payment_status = $request->status;
    $unmappedstatus = $request->unmappedstatus;
    $merchant_orderId = $request->txnid;
    $amount = $request->amount;
    $bank_ref_num = $request->bank_ref_num;
    $name = $request->firstname;
    
    $data = [
        "customer_name" => $request->firstname,
        "TransactionId" => $transactionId
    ];

    if ($payment_status == 'success' && $unmappedstatus == 'captured') {
        // Get the emi_transaction first before updating
        $emi_transaction = DB::table('emi_transactions')
            ->where('txn_id', $merchant_orderId)
            ->first();

        if ($emi_transaction) {
            // Assuming emi_id is an array or json, so decode it
            $emi_ids = json_decode($emi_transaction->emi_id);

            // Update the emi_transactions table
            $updated = DB::table('emi_transactions')
                ->where('txn_id', $merchant_orderId)
                ->update([
                    'status' => 'paid',
                    'updated_at' => now(),
                ]);

            if ($updated) {
                // Retrieve the emi_id of the updated transaction
                $emi_id = DB::table('emi_transactions')
                    ->where('txn_id', $merchant_orderId)
                    ->value('emi_id');

                // Ensure emi_id is not null or empty
                if ($emi_id) {
                    // Update the payments table using the retrieved emi_id
                    DB::table('payments')
                        ->where('id', $emi_id)
                        ->update([
                            'emi_status' => 'paid',
                            'transaction_id' => $transactionId,
                            'app_name' => 'web',
                            'paid_date' => now(),
                            'paid_datetime' => now(),
                            'updated_at' => now(),
                        ]);
                } else {
                    // Log::error("emi_id is missing for txn_id: " . $merchant_orderId);
                }
            }

            // Retrieve customer_id from emi_transaction
            $customer_id = $emi_transaction->customer_id;

            // Make an external API call to update available credit
            $response = Http::get('https://onecredit.in/api/v3/seller/update_available_credit', [
                'id' => $customer_id,
            ]);

            // Log any errors if the external request fails
            if ($response->failed()) {
                Log::error("Failed to update available credit for customer_id: " . $customer_id);
            }

        } else {
            Log::error("Emi transaction not found for txn_id: " . $merchant_orderId);
        }
    }

    return response()->json([
        'success' => 'ok',
    ], 200);
}

public function billdesk_success(Request $request){
    // log::info('this is billdesk webhooks');
    // log::info($request->all());
    
}

  public function showSuccessPage(Request $request)
{
    $data = $request->all(); // You now have all the data passed from the form

    // Optional: format or manipulate data before view
    return view('show_test')->with('data', $data);
}


    public function payment(Request $request): JsonResponse|Redirector|RedirectResponse
    {
        $user = Helpers::getCustomerInformation($request);
        $orderAdditionalData = [];
        $validator = Validator::make($request->all(), [
            'payment_method' => 'required',
            'payment_platform' => 'required',
        ]);

        $validator->sometimes('customer_id', 'required', function ($input) {
            return in_array($input->payment_request_from, ['app']);
        });
        $validator->sometimes('is_guest', 'required', function ($input) {
            return in_array($input->payment_request_from, ['app']);
        });

        if ($validator->fails()) { //api
            $errors = Helpers::validationErrorProcessor($validator);
            if (in_array($request['payment_request_from'], ['app'])) {
                return response()->json(['errors' => Helpers::validationErrorProcessor($validator)], 403);
            } else {
                foreach ($errors as $value) {
                    Toastr::error(translate($value['message']));
                }
                return back();
            }
        }

        $cartGroupIds = CartManager::get_cart_group_ids(request: $request, type: 'checked');
        $carts = Cart::whereHas('product', function ($query) {
            return $query->active();
        })->whereIn('cart_group_id', $cartGroupIds)->where(['is_checked' => 1])->get();
        $productStockCheck = CartManager::product_stock_check($carts);
        if (!$productStockCheck && in_array($request['payment_request_from'], ['app'])) {
            return response()->json(['errors' => ['code' => 'product-stock', 'message' => 'The following items in your cart are currently out of stock']], 403);
        } elseif (!$productStockCheck) {
            Toastr::error(translate('the_following_items_in_your_cart_are_currently_out_of_stock'));
            return redirect()->route('shop-cart');
        }

        $verifyStatus = OrderManager::verifyCartListMinimumOrderAmount($request);
        if ($verifyStatus['status'] == 0 && in_array($request['payment_request_from'], ['app'])) {
            return response()->json(['errors' => ['code' => 'Check the minimum order amount requirement']], 403);
        } elseif ($verifyStatus['status'] == 0) {
            Toastr::info('Check the minimum order amount requirement');
            return redirect()->route('shop-cart');
        }

        if (in_array($request['payment_request_from'], ['app'])) {
            $shippingMethod = getWebConfig(name: 'shipping_method');
            $physicalProductExist = false;
            foreach ($carts as $cart) {
                if ($cart->product_type == 'physical') {
                    $physicalProductExist = true;
                }

                if ($shippingMethod == 'inhouse_shipping') {
                    $adminShipping = ShippingType::where('seller_id', 0)->first();
                    $getShippingType = isset($adminShipping) == true ? $adminShipping->shipping_type : 'order_wise';
                } else {
                    if ($cart->seller_is == 'admin') {
                        $adminShipping = ShippingType::where('seller_id', 0)->first();
                        $getShippingType = isset($adminShipping) == true ? $adminShipping->shipping_type : 'order_wise';
                    } else {
                        $seller_shipping = ShippingType::where('seller_id', $cart->seller_id)->first();
                        $getShippingType = isset($seller_shipping) == true ? $seller_shipping->shipping_type : 'order_wise';
                    }
                }

                if ($getShippingType == 'order_wise') {
                    $cartShipping = CartShipping::where('cart_group_id', $cart->cart_group_id)->first();
                    if (!isset($cartShipping) && $physicalProductExist) {
                        return response()->json(['errors' => ['code' => 'shipping-method', 'message' => 'Data not found']], 403);
                    }
                }
            }

            if (($user == 'offline' && $request['is_check_create_account'])) {
                $getAPIProcess = self::getRegisterNewCustomerAPIProcess($request);
                if ($getAPIProcess['status'] == 0) {
                    return response()->json(['message' => translate('Already_registered ')], 403);
                }
                $orderAdditionalData += [
                    'new_customer_info' => $getAPIProcess['data'],
                ];
            }
        }

        $redirectLink = $this->getCustomerPaymentRequest($request, $orderAdditionalData);

        if (in_array($request['payment_request_from'], ['app'])) {
            return response()->json([
                'redirect_link' => $redirectLink,
                'new_user' => isset($orderAdditionalData['new_customer_info']) && $orderAdditionalData['new_customer_info'] != null ? 1 : 0,
            ], 200);
        } else {
            return redirect($redirectLink);
        }
    }

    function getRegisterNewCustomerAPIProcess($request)
    {
        $newCustomerRegister = [];
        $shippingAddress = ShippingAddress::where(['customer_id' => $request['guest_id'], 'is_guest' => 1, 'id' => $request->input('address_id')])->first();
        if ($request->has('address_id') && $request['address_id'] && $shippingAddress) {
            if (User::where(['email' => $shippingAddress['email']])->orWhere(['phone' => $shippingAddress['phone']])->first()) {
                return ['status' => 0];
            } else {
                $newCustomerRegister = [
                    'status' => 1,
                    'data' => self::getRegisterNewCustomer(
                        request: $request,
                        address: $shippingAddress,
                        shippingId: $request['address_id'],
                        billingId: $request->has('billing_address_id') && $request['billing_address_id'] ? $request['billing_address_id'] : null
                    )
                ];
            }
        }

        $billingAddress = ShippingAddress::where(['customer_id' => $request['guest_id'], 'is_guest' => 1, 'id' => $request->input('billing_address_id')])->first();
        if ($request['address_id'] == null && $request->has('billing_address_id') && $request['billing_address_id'] && $billingAddress) {
            if (User::where(['email' => $billingAddress['email']])->orWhere(['phone' => $billingAddress['phone']])->first()) {
                return ['status' => 0];
            } else {
                $newCustomerRegister = [
                    'status' => 1,
                    'data' => self::getRegisterNewCustomer(
                        request: $request,
                        address: $billingAddress,
                        shippingId: null,
                        billingId: $request['billing_address_id'],
                    )
                ];
            }
        }

        return $newCustomerRegister;
    }


    function getRegisterNewCustomer($request, $address, $shippingId = null, $billingId = null): array
    {
        return [
            'name' => $address['contact_person_name'],
            'f_name' => $address['contact_person_name'],
            'l_name' => '',
            'email' => $address['email'],
            'phone' => $address['phone'],
            'is_active' => 1,
            'password' => $request['password'],
            'referral_code' => Helpers::generate_referer_code(),
            'shipping_id' => $shippingId,
            'billing_id' => $billingId,
        ];
    }

  

    public function web_payment_success(Request $request)
    {
        if($request->flag == 'success') {
            if (session()->has('payment_mode') && session('payment_mode') == 'app') {
                return response()->json(['message' => 'Payment succeeded'], 200);
            } else {
                Toastr::success(translate('Payment_success'));
                $isNewCustomerInSession = session('newCustomerRegister');
                session()->forget('newCustomerRegister');
                return view(VIEW_FILE_NAMES['order_complete'], compact('isNewCustomerInSession'));
            }
        }else{
            if(session()->has('payment_mode') && session('payment_mode') == 'app'){
                return response()->json(['message' => 'Payment failed'], 403);
            }else{
                Toastr::error(translate('Payment_failed').'!');
                return redirect(url('/'));
            }
        }

    }

    public function getCustomerPaymentRequest(Request $request, $orderAdditionalData = []): mixed
    {
        $additionalData = [
            'business_name' => getWebConfig(name: 'company_name'),
            'business_logo' => getStorageImages(path: getWebConfig('company_web_logo'), type:'shop'),
            'payment_mode' => $request->has('payment_platform') ? $request['payment_platform'] : 'web',
        ];

        $user = Helpers::getCustomerInformation($request);

        $getGuestId = $request['is_guest'] ? $request['guest_id'] : (session('guest_id') ?? 0);
        $isGuestUser = ($user == 'offline') ? 1 : 0;
        $getCustomerID = null;
        $isGuestUserInOrder = $isGuestUser;
        if ($user == 'offline' && session('newCustomerRegister')) {
            $additionalData['new_customer_info'] = session('newCustomerRegister') ?? null;
            $additionalData['customer_id'] = $getGuestId;
            $additionalData['address_id'] = session('newCustomerRegister')['address_id'] ?? null;
            $additionalData['billing_address_id'] = session('newCustomerRegister')['billing_address_id'] ?? null;
            $getCustomerID = $getGuestId;
            $isGuestUserInOrder = 0;
        } elseif ($user == 'offline' && !session('newCustomerRegister') && isset($orderAdditionalData['new_customer_info'])) {
            $additionalData['new_customer_info'] = $orderAdditionalData['new_customer_info'];
            $getCustomerID = $getGuestId;
            $isGuestUserInOrder = 0;
        } elseif ($user != 'offline') {
            $getCustomerID = 0;
            $isGuestUserInOrder = 0;
        }

        $additionalData['is_guest'] = $isGuestUser;
        if (in_array($request['payment_request_from'], ['app'])) {
            $additionalData['customer_id'] = $request['customer_id'];
            $additionalData['is_guest'] = $request['is_guest'];
            $additionalData['order_note'] = $request['order_note'];
            $additionalData['address_id'] = $request['address_id'];
            $additionalData['billing_address_id'] = $request['billing_address_id'];
            $additionalData['coupon_code'] = $request['coupon_code'];
            $additionalData['coupon_discount'] = $request['coupon_discount'];
            $additionalData['payment_request_from'] = $request['payment_request_from'];
        } else {
            $additionalData['customer_id'] = $user != 'offline' ? $user->id : $getCustomerID;
            $additionalData['order_note'] = session('order_note') ?? null;
            $additionalData['address_id'] = session('address_id') ?? 0;
            $additionalData['billing_address_id'] = session('billing_address_id') ?? 0;

            $additionalData['coupon_code'] = session('coupon_code') ?? null;
            $additionalData['coupon_discount'] = session('coupon_discount') ?? 0;
            $additionalData['payment_request_from'] = $request['payment_mode'] ?? 'web';
        }
        $additionalData['new_customer_id'] = $getCustomerID;
        $additionalData['is_guest_in_order'] = $isGuestUserInOrder;

        $currency_model = getWebConfig(name: 'currency_model');
        if ($currency_model == 'multi_currency') {
            $currency_code = 'USD';
        } else {
            $default = getWebConfig(name: 'system_default_currency');
            $currency_code = Currency::find($default)->code;
        }

        if (in_array($request['payment_request_from'], ['app'])) {
            $cart_group_ids = CartManager::get_cart_group_ids(request: $request, type: 'checked');
            $cart_amount = 0;
            $shippingCostSaved = 0;
            foreach ($cart_group_ids as $group_id) {
                $cart_amount += CartManager::api_cart_grand_total($request, $group_id);
                $shippingCostSaved += CartManager::get_shipping_cost_saved_for_free_delivery(groupId: $group_id, type: 'checked');
            }
            $paymentAmount = $cart_amount - $request['coupon_discount'] - $shippingCostSaved;
        } else {
            $discount = session()->has('coupon_discount') ? session('coupon_discount') : 0;
            $orderWiseShippingDiscount = CartManager::order_wise_shipping_discount();
            $shippingCostSaved = CartManager::get_shipping_cost_saved_for_free_delivery(type: 'checked');
            $paymentAmount = CartManager::cart_grand_total(type: 'checked') - $discount - $orderWiseShippingDiscount - $shippingCostSaved;
        }

        $customer = Helpers::getCustomerInformation($request);

        if ($customer == 'offline') {
            $address = ShippingAddress::where(['customer_id' => $request['customer_id'], 'is_guest' => 1])->latest()->first();
            if ($address) {
                $payer = new Payer(
                    $address->contact_person_name,
                    $address->email,
                    $address->phone,
                    ''
                );
            } else {
                $payer = new Payer(
                    'Contact person name',
                    '',
                    '',
                    ''
                );
            }
        } else {
            $payer = new Payer(
                $customer['f_name'] . ' ' . $customer['l_name'],
                $customer['email'],
                $customer['phone'],
                ''
            );
            if (empty($customer['phone'])) {
                Toastr::error(translate('please_update_your_phone_number'));
                return route('checkout-payment');
            }
        }

        $paymentInfo = new PaymentInfo(
            success_hook: 'digital_payment_success',
            failure_hook: 'digital_payment_fail',
            currency_code: $currency_code,
            payment_method: $request['payment_method'],
            payment_platform: $request['payment_platform'],
            payer_id: $customer == 'offline' ? $request['customer_id'] : $customer['id'],
            receiver_id: '100',
            additional_data: $additionalData,
            payment_amount: $paymentAmount,
            external_redirect_link: $request['payment_platform'] == 'web' ? $request['external_redirect_link'] : null,
            attribute: 'order',
            attribute_id: idate("U")
        );

        $receiverInfo = new Receiver('receiver_name', 'example.png');
        return $this->generate_link($payer, $paymentInfo, $receiverInfo);
    }

    public function customer_add_to_fund_request(Request $request): JsonResponse|Redirector|RedirectResponse
    {
        if (getWebConfig(name: 'add_funds_to_wallet') != 1) {
            if (in_array($request['payment_request_from'], ['app'])) {
                return response()->json(['message' => 'Add funds to wallet is deactivated'], 403);
            }
            Toastr::error(translate('add_funds_to_wallet_is_deactivated'));
            return back();
        }

        $validator = Validator::make($request->all(), [
            'amount' => 'required',
            'payment_method' => 'required',
            'payment_platform' => 'required',
        ]);

        if ($validator->fails()) {
            $errors = Helpers::validationErrorProcessor($validator);
            if (in_array($request->payment_request_from, ['app'])) {
                return response()->json(['errors' => $errors]);
            } else {
                foreach ($errors as $value) {
                    Toastr::error(translate($value['message']));
                }
                return back();
            }
        }

        $currency_model = getWebConfig(name: 'currency_model');
        if ($currency_model == 'multi_currency') {
            $default_currency = Currency::find(getWebConfig(name: 'system_default_currency'));
            $currency_code = $default_currency['code'];
            $currentCurrency = $request->current_currency_code ?? session('currency_code');
        } else {
            $default = BusinessSetting::where(['type' => 'system_default_currency'])->first()->value;
            $currency_code = Currency::find($default)->code;
            $currentCurrency = $currency_code;
        }


        $minimumAddFundAmount = getWebConfig(name: 'minimum_add_fund_amount') ?? 0;
        $maximumAddFundAmount = getWebConfig(name: 'maximum_add_fund_amount') ?? 0;

        if (!(Convert::usdPaymentModule($request->amount, $currentCurrency) >= Convert::usdPaymentModule($minimumAddFundAmount, 'USD')) || !(Convert::usdPaymentModule($request->amount, $currentCurrency) <= Convert::usdPaymentModule($maximumAddFundAmount, 'USD'))) {
            $errors = [
                'minimum_amount' => $minimumAddFundAmount ?? 0,
                'maximum_amount' => $maximumAddFundAmount ?? 1000,
            ];
            if (in_array($request->payment_request_from, ['app'])) {
                return response()->json($errors, 202);
            } else {
                Toastr::error(translate('the_amount_needs_to_be_between') . ' ' . webCurrencyConverter($minimumAddFundAmount) . ' - ' . webCurrencyConverter($maximumAddFundAmount));
                return back();
            }
        }

        $additional_data = [
            'business_name' => BusinessSetting::where(['type' => 'company_name'])->first()->value,
            'business_logo' => getWebConfig('company_web_logo')['path'],
            'payment_mode' => $request->has('payment_platform') ? $request->payment_platform : 'web',
        ];

        $customer = Helpers::getCustomerInformation($request);

        if (in_array($request->payment_request_from, ['app'])) {
            $additional_data['customer_id'] = $customer->id;
            $additional_data['payment_request_from'] = $request->payment_request_from;
        }

        $payer = new Payer(
            $customer->f_name . ' ' . $customer->l_name,
            $customer['email'],
            $customer->phone,
            ''
        );

        $payment_info = new PaymentInfo(
            success_hook: 'add_fund_to_wallet_success',
            failure_hook: 'add_fund_to_wallet_fail',
            currency_code: getWebConfig(name: 'currency_model') == 'multi_currency' ? 'USD' : $currency_code,
            payment_method: $request->payment_method,
            payment_platform: $request->payment_platform,
            payer_id: $customer->id,
            receiver_id: '100',
            additional_data: $additional_data,
            payment_amount: Convert::usdPaymentModule($request->amount, $currentCurrency),
            external_redirect_link: $request->payment_platform == 'web' ? $request->external_redirect_link : null,
            attribute: 'add_funds_to_wallet',
            attribute_id: idate("U")
        );

        $receiver_info = new Receiver('receiver_name', 'example.png');

        $redirect_link = Payment::generate_link($payer, $payment_info, $receiver_info);

        if (in_array($request['payment_request_from'], ['app'])) {
            return response()->json(['redirect_link' => $redirect_link], 200);
        } else {
            return redirect($redirect_link);
        }
    }
  
    public function get_payment_gateway(Request $request){
        $x=DB::table('business_settings')->where('type','payment_gateway_select')->value('value');
         return response()->json(['data'=>$x],200);
    }
   
     public function initiatePayment(Request $request)
    {
       
        // log::info($request->all());
        $x=DB::table('business_settings')->where('type','payment_gateway_select')->value('value');
        //  $x='billl';
        if($x=='billdesk'){

            $name=$request->name;
            $email=$request->email;
            // $amount = number_format($request->amount, 2);
            // dd($amount);
            $amount = $request->amount;
            $mobile=$request->mobile;
            $customer_id=$request->id;
            $paymentIds=$request->payids;
            $loan_id=$request->loan_id;
            $emi_date=$request->emi_date;
             $app=$request->app;
            //  dd($app);
           $pids = explode(',', $paymentIds);
            
            $merchant_OrderId=uniqid();

            $data=DB::table('emi_transactions')->insert([
                'customer_id'=>$customer_id,
                'loan_id'=>$loan_id,
                'emi_id' => json_encode($pids), // Store as JSON array
                //'emi_id' => $paymentIds,  // Store the array as JSON
                'txn_id'=>$merchant_OrderId,
                'name'=>$name,
                'email'=>$email,
                'mobile'=>$mobile,
                'emi_date'=>$emi_date,
                'amount'=>$amount,
                'status'=>'pending',
                'created_at'=>now(),
                'updated_at'=>now(),
            ]);
        
            $jwtToken = $this->generateJwsToken($amount,$merchant_OrderId,$name,$email,$mobile,$app);
            //             log::info('this is jsw');
            // log::info($jwtToken);
            

      
            $orderData = $this->createOrder($jwtToken);

            // log::info('this is create order response');
            // log::info($orderData);
            // dd($orderData);
            $rdata = $orderData['links'][1]['parameters']['rdata'];
            $merchantid = $orderData['links'][1]['parameters']['mercid'];
            $bdorderid = $orderData['links'][1]['parameters']['bdorderid'];
            
           
        
            if ($orderData && isset($rdata, $merchantid, $bdorderid)) {
              
                return view('test2', [
                    'bdorderid' => $bdorderid,
                    'merchantid' => $merchantid,
                    'rdata' => $rdata
                ]);
            } else {
                // Handle failure, return an error message or appropriate response
                return response()->json(['error' => 'Payment initiation failed.'], 500);
            }
        }
        
        else{
            
        
    //     $validator = Validator::make($request->all(), [
    //         'amount' => 'required|string',
    //         'name' => 'required|string',
    //         'email' => 'required|string',
    //         'mobile'=>'required|string',
    //         'id' => 'required|numeric',

    //     ]);

    //     if ($validator->fails()) {
    //         return back()->withErrors($validator)->withInput();
    //     }
    //     $name=$request->name;
    //     $email=$request->email;
    //     $amount=$request->amount;
    //     $mobile=$request->mobile;
    //     $customer_id=$request->id;
    //      $paymentIds=$request->payids;
    //   // $pids=explode(',',$paymentIds);
        
    //     $merchant_OrderId=uniqid();
    //   // $merchant_OrderId="6724643f489b9";
    //   // dd($merchant_OrderId);
    //     $productinfo="Test product";
    //     $key = "urF7C4";
    //     $salt = "lFjeqQcImS5tLa1HnS0qIz7nBMRuAYO9";
    //     //return uniqid();
       
    //     $transarray=array(
    //       "payment_id"=>$paymentIds,
    //         "customer_id"=>$request->id,
    //         "amount"=>$amount,
    //         "merchant_transid"=>$merchant_OrderId,
    //         "payment_status"=>"PAYMENT_PENDING"
    //         );
    //       $insert=EmiTransaction::insert($transarray); 
       
    //      $input = $key . "|" . $merchant_OrderId . "|" . $amount . "|" . $productinfo . "|" . $name. "|" . $email . "|".$customer_id."||||||||||" . $salt;
    //      $hashkey= strtolower(hash("sha512", $input));
       
    //     $payuData = [
    //         'key' =>$key,
    //         'txnid' => $merchant_OrderId,
    //         'amount' => $amount,
    //         'productinfo' => $productinfo,
    //         'firstname' => $name,
    //         'mobile' => $mobile,
    //         'email' => $email,
    //         "customer_id"=>$customer_id,
    //         'hash' => $hashkey
    //     ];

    //     return  view(VIEW_FILE_NAMES['payu'], compact('payuData'));
    
     $validator = Validator::make($request->all(), [
            'amount' => 'required|string',
            'name' => 'required|string',
            'email' => 'required|string',
            'mobile'=>'required|string',
            'id' => 'required|numeric',
            'payids'=>'required',
            'loan_id'=>'required',
            // 'emi_date'=>'required',

        ]);

        if ($validator->fails()) {
            return back()->withErrors($validator)->withInput();
        }
        $name=$request->name;
        $email=$request->email;
        $amount=$request->amount;
        $mobile=$request->mobile;
        $customer_id=$request->id;
        $paymentIds=$request->payids;
        $loan_id=$request->loan_id;
        $emi_date=$request->emi_date;
      // $pids=explode(',',$paymentIds);
        
        $merchant_OrderId=uniqid();
        // dd($merchant_OrderId);
       
        $productinfo="Test product";
        $test="test test";
        // $key = "urF7C4";
        // $salt = "lFjeqQcImS5tLa1HnS0qIz7nBMRuAYO9";
        
         //live
        $key="ciYMKr";
        $salt="6cD4eqlQHawZsn1IHH6EGvJ9IBOL9xSL";
                // $salt="MIIEwAIBADANBgkqhkiG9w0BAQEFAASCBKowggSmAgEAAoIBAQD3yIEBRrP4QbY3ynsBqot1DRxLgYN3evbz+hM7E8qqjH7j4Hg7GmMDS8pghAgYN6eGk2XOs/RmXtLq72jD7cP0o4xp1OO7lQMQGDp4LOhyhOjXBTPIi+caQtLeqHO1eE0ZYwUm01q1qPL0l0IJyPQ0yTpAjg5Tgpp0OCgNdfyRjvnVO6UAhcMnDVRINhWcu/TvCLhh4O4VYc5I2gyiDIQ+Y1pQvjJ+PXCrlPb0mneh19N/+B9MOs7NeJnli0nFlCSo/00VuPQRPPtwtAzql+ejAi8jI9Tjmwzr5L2J1Y1OyLqEJfCCJLhZ+yncBIdodgnp85VHesYXYNP9SQUn9e31AgMBAAECggEBALCYIfk63sEsdCXHFWvWlJXTxjq6D2x0ItU3gcU1EdgDUdwu+wGEiNSsi4vGDc7Uu3zaSFDNSH49Tq1J+6zIJESS21wB8lyakxhBbEqCFxinSRsWBhYEP60juw4dmHnZR3m0bNODBr85rg5MTzCSHBoS4IVpuSQjjNkPvNv7HwrNKJj/VFysqM8vDyPWQe6Uw/Ci+GLfLPrdZDP0gAraNZMLPD6RV7eAH4w+EsU43uITiiTr6Sc+YP5i67sW1vol4K9bSSayVRGinUJYxu/fuuNXdt0esBM8XH6GgYm+fMJcRQeBffO+hGEnFMghZSWsrdu1ibLRVH34Y2bR4O88ifECgYEA/B5biyUZRUaQdwtT5tAROCxydV+DkcMny5jEMu5nQR+ihKbr7N0hNQeJOp2vrCnWpLN8Ie5ySAIIBl52hFz4lpEzEyhqFHqiSQvcyZciqjIw4DdyGNJ+R4QiumIpKFin9j4E0jZMGi+9I/n1muIQb4SrnxLUj+lqXSesJRRXP+8CgYEA+5kPVqw3j3XaVUC2ZFqVohfw3aAKW/stdBckwiM5OJynttnebU5fQhWUFT4wgsEmXR7W5RRuF09PSO7f4WZB1CzpAkvd4Zr9nxiUeicocvBgkNz5oxBG4zSSdcTzuDM5FUhDmI54C14Sfo3RBu0ITKzG2M3r3b6FZcKp9wY9DFsCgYEA0D5y9rep6+KhKPMeViO+VVvBHtnJ3vgHQs/oHvl0KAJtRoxpirgL2bVj0Bq3I8lbFad6/LvrgTbMUhZsZmA0pIlCWqyjEk9JBHTb5VcEtvfGDy08/OvAimnGFZVG0aCI+4e2i7t1mJud6r1n9IqNcM9wwm/XSxdNV6yRL/9hIdkCgYEAgKZAQJppApNJpAQl/2SOVMcXI8wc3/GsyUq4QgjOzpLT81yuLog/j0QHZ2FYXtOy8TS+v35V2Nd1/B4hHlkyWfLo5oKynxSokPx5l4iEV1lwl0JW57l/9dfA+DVQRiTzEF3WKqDW40EkBdAwTPFRKBvExcFt1QIBpgoG7Mgk2TcCgYEAt4WpZsM6kOszOchHs3HgfJFbd3sjqbZXlWvSh/6NowtRs8LJIwp1L4eaQ2CSUPDwUUZmse0guXObk6jcn5GNBHJREJ0f/VoegYPEQaa1oUt754pRx4/eEH8TPm7XycA7h61txKj27hKD8Po83P5870+1maHDyxWn7+YGqzH34I8=";

        
        // dd($customer_id,$loan_id,$paymentIds,$merchant_OrderId,$name,$email,$mobile,$emi_date,$amount);
        //return uniqid();
        $data=DB::table('emi_transactions')->insert([
            'customer_id'=>$customer_id,
            'loan_id'=>$loan_id,
            'emi_id'=>$paymentIds,
            // 'emi_id' => json_encode($paymentIds), 
            'txn_id'=>$merchant_OrderId,
            'name'=>$name,
            'email'=>$email,
            'mobile'=>$mobile,
            'emi_date'=>$emi_date,
            'amount'=>$amount,
            'status'=>'pending',
            'created_at'=>now(),
            'updated_at'=>now(),
        ]);

       
        // $transarray=array(
        //    "payment_id"=>$paymentIds,
        //     "customer_id"=>$request->id,
        //     "amount"=>$amount,
        //     "merchant_transid"=>$merchant_OrderId,
        //     "payment_status"=>"PAYMENT_PENDING",
        //     "response_msg"=>"success",
        //     "providerReferenceId"=>"1234",
        //     "merchantOrderId"=>$merchant_OrderId,
        //     "checksum"=>"123",
        //     );
        //   $insert=EmiTransaction::insert($transarray); 
       
         $input = $key . "|" . $merchant_OrderId . "|" . $amount . "|" . $productinfo . "|" . $name. "|" . $email . "|".$customer_id."||||||||||" . $salt;
         $hashkey= strtolower(hash("sha512", $input));
       
        $payuData = [
            'key' =>$key,
            'txnid' => $merchant_OrderId,
            'amount' => $amount,
            'productinfo' => $productinfo,
            'firstname' => $name,
            'mobile' => $mobile,
            'email' => $email,
            "customer_id"=>$customer_id,
            "loan_id"=>$loan_id,
            "emi_date"=>$emi_date,
            "payment_ids"=>$paymentIds,
            'hash' => $hashkey
        ];
        
        return  view(VIEW_FILE_NAMES['payu'], compact('payuData'));
        }
    }

 public static function generateJwsToken($amount,$merchant_OrderId, $name, $email, $mobile,$app)
    {
        $currentTimestamp = Carbon::now()->format('YmdHis');

        $date = Carbon::createFromFormat('YmdHis', $currentTimestamp);
        
        $formattedDateWithTimezone = $date->setTimezone('Asia/Kolkata')->toIso8601String(); 
        $timestamp = now()->format('YmdHis');
        //test
        // $secretKey='kCQhF9iLB5HhkcV7IIXfg9biJqpQ5BI4';
        // $clientId='bduat2k447';
        // $merchantId='BDUAT2K447';
        
         $secretKey='0HlP8brfArOj00YI8Ue5cPYozEf8Gspu';
        $clientId='onecredit';
        $merchantId='ONECREDIT';
      
        // $orderId = Str::random(8); 
        $orderId=$merchant_OrderId;
        
    $formattedAmount = number_format($amount, 2, '.', '');
    $amount = str_replace(',', '', $formattedAmount);

        $orderDate=$formattedDateWithTimezone;
        $currency='356';
        $ru='https://onecredit.in/success_billdesk';

        // JWS Header
        $header = [
            "alg" => "HS256",
            "clientid" =>$clientId,
        ];
        $payload = [
            "mercid" => $merchantId,
            "orderid" => $orderId,
            "amount" => $amount,
            "order_date" => $orderDate,
            "currency" => $currency,
            "ru" => $ru,
            "additional_info"=>[
              "additional_info1" => $name,
                "additional_info2" => $email,
                "additional_info3" => $mobile,
                "additional_info4" => $app,
                "additional_info5" => "NA",
                "additional_info6" => "NA",
                "additional_info7" => "NA",

            ],
            "itemcode"=>"DIRECT",
            "device" => [
                "init_channel" => "internet",
                "ip"=> "82.112.239.19",
                "user_agent"=> "Mozilla/5.0(WindowsNT10.0;WOW64;rv:51.0)Gecko/20100101Firefox/51.0",
                "accept_header"=>"text/html",
                "browser_tz" => "-330"
            ],
        ];
        $headerEncoded = self::base64urlEncode(json_encode($header));
        $payloadEncoded = self::base64urlEncode(json_encode($payload));
        $message = $headerEncoded . '.' . $payloadEncoded;
        $signature = hash_hmac('sha256', $message, $secretKey, true);
        
        $signatureEncoded = self::base64urlEncode($signature);
        $jwsToken = $headerEncoded . '.' . $payloadEncoded . '.' . $signatureEncoded;
        // dd($jwsToken);
        return $jwsToken;
    }
    
     public function createOrder($jwt)
{
    // Define the API URL
    // $url = 'https://uat1.billdesk.com/u2/payments/ve1_2/orders/create';
    $url = 'https://api.billdesk.com/payments/ve1_2/orders/create';

    // Get current timestamp and generate trace ID
    $timestamp = now()->format('YmdHis');
    $traceid = Str::random(18);
    
    log::info($traceid);
    log::info($timestamp);
    log::info($jwt);

    // Set headers for the API request
    $headers = [
        'content-type: application/jose',
        'accept: application/jose',
        'bd-timestamp: ' . $timestamp,
        'bd-traceid: ' . $traceid,
    ];

    // Initialize cURL session
    $ch = curl_init();

    // Set cURL options
    curl_setopt_array($ch, [
        CURLOPT_URL => $url,
        CURLOPT_RETURNTRANSFER => true,
        CURLOPT_ENCODING => '',
        CURLOPT_MAXREDIRS => 10,
        CURLOPT_TIMEOUT => 30, // You can adjust this timeout as needed
        CURLOPT_FOLLOWLOCATION => true,
        CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
        CURLOPT_CUSTOMREQUEST => 'POST',
        CURLOPT_POSTFIELDS => $jwt, // JWT token as POST data
        CURLOPT_HTTPHEADER => $headers, // Headers
    ]);

    // Execute cURL request
    $response = curl_exec($ch);
  
    // $response_static='eyJhbGciOiJIUzI1NiIsImNsaWVudGlkIjoiYmR1YXQyazQ0NyIsImtpZCI6IkhNQUMifQ.eyJvYmplY3RpZCI6Im9yZGVyIiwib3JkZXJpZCI6IkgwSnhVUmhsIiwiYmRvcmRlcmlkIjoiT0FUVklJS0tDVFpFWFRaIiwibWVyY2lkIjoiQkRVQVQySzQ0NyIsIm9yZGVyX2RhdGUiOiIyMDI1LTAyLTI1VDE1OjQxOjAwKzA1OjMwIiwiYW1vdW50IjoiMTAwMCIsImN1cnJlbmN5IjoiMzU2IiwicnUiOiJodHRwczovL29uZWNyZWRpdC5pbi9wYXltZW50U3VjY2VzcyIsImFkZGl0aW9uYWxfaW5mbyI6eyJhZGRpdGlvbmFsX2luZm8xIjoiVGVzdCBQYXltZW50IiwiYWRkaXRpb25hbF9pbmZvMiI6IkluZm8yIiwiYWRkaXRpb25hbF9pbmZvMyI6Ik9rIiwiYWRkaXRpb25hbF9pbmZvNCI6Ik5BIiwiYWRkaXRpb25hbF9pbmZvNSI6Ik5BIiwiYWRkaXRpb25hbF9pbmZvNiI6Ik5BIiwiYWRkaXRpb25hbF9pbmZvNyI6Ik5BIiwiYWRkaXRpb25hbF9pbmZvOCI6Ik5BIiwiYWRkaXRpb25hbF9pbmZvOSI6Ik5BIiwiYWRkaXRpb25hbF9pbmZvMTAiOiJOQSJ9LCJpdGVtY29kZSI6IkRJUkVDVCIsImNyZWF0ZWRvbiI6IjIwMjUtMDItMjVUMTU6NDM6MzIrMDU6MzAiLCJuZXh0X3N0ZXAiOiJyZWRpcmVjdCIsImxpbmtzIjpbeyJocmVmIjoiaHR0cHM6Ly93d3cuYmlsbGRlc2suY29tL3BnaS92ZTFfMi9vcmRlcnMvSDBKeFVSaGwiLCJyZWwiOiJzZWxmIiwibWV0aG9kIjoiR0VUIn0seyJocmVmIjoiaHR0cHM6Ly91YXQxLmJpbGxkZXNrLmNvbS91Mi93ZWIvdjFfMi9lbWJlZGRlZHNkayIsInJlbCI6InJlZGlyZWN0IiwibWV0aG9kIjoiUE9TVCIsInBhcmFtZXRlcnMiOnsibWVyY2lkIjoiQkRVQVQySzQ0NyIsImJkb3JkZXJpZCI6Ik9BVFZJSUtLQ1RaRVhUWiIsInJkYXRhIjoiZDJiNTUwZGE0NzBiMzExNDUyYjM1NWM3OTc2YTQwMDgyOWQ5ZGMyZmMwYzZlMGQ1ZTE1NThjZDA3NjMyZTRmNzc0MzM3NjY0NTNjMjU3ZDgzYmVhMDA4Njk1MTE3OTkwNWFjYzQzMWI0ZTllZWU3NTFhMzk4MmFkYmE3ZmM5MTYzY2E4LjQxNDU1MzVmNTU0MTU0MzEifSwidmFsaWRfZGF0ZSI6IjIwMjUtMDItMjVUMTY6MTM6MzIrMDU6MzAiLCJoZWFkZXJzIjp7ImF1dGhvcml6YXRpb24iOiJPVG9rZW4gZWRiOWExMTU2ODVjZjUzODliY2UyOGMzYzc1YzQ0MDZkNDg1NzMwMzUwZGNhY2U0Yzg5ZjExMmQwNGZkNTg4NjcyOTg0YjdiYjQwOWYwYTE3NGM3OTc1MmExNWRmMTg5OGQwNzYwZDYzMTcwZTYzMDczMTk4ZDhjYzI3MjRkMzRlZjc4MmEzMzYwYWIwZThlOGMwNDE1ODVmYTVlMmYzNjliMmJlMzYyN2EyZGVlN2Q2MDNjNzhhZjNjMjViNTVjZTZjZGYwMzk4ZjkzZDZmZTQ0YmM3NmU3OGFkMjcyNGY2YTZiYWRhMGY3MjllOWQyOTAwYzc1YmU0ODhhMDZiM2UwYTc3N2E1ZDcyZWFkYTcyYjEyZGZiMWM2MWU0ZTU0Yzg3ZmY2ODlhOTZmYTZmZDcxMWIwYi40MTQ1NTM1ZjU1NDE1NDMxIn19XSwic3RhdHVzIjoiQUNUSVZFIn0.TVxlBnP3VYSQy_BBnELJ0nOBzQ_zXNs92LClNORYLzY';
  log::info($response);
    $responseData=$this->decode_jwt($response);
       log::info($responseData);
    return $responseData;
  
}

 public static function base64urlEncode($data){
        // echo "test";
        // $data='test5';
        return rtrim(strtr(base64_encode($data), '+/', '-_'), '=');
    }
    
    private function decode_jwt($jwt) {
    

    list($headerEncoded, $payloadEncoded, $signatureEncoded) = explode('.', $jwt);

    $headerDecoded = json_decode($this->base64urlDecode($headerEncoded), true);
    $payloadDecoded = json_decode($this->base64urlDecode($payloadEncoded), true);

    return $payloadDecoded;
   
}

private function base64urlDecode($data) {
    // Add padding if necessary
    $padding = strlen($data) % 4;
    if ($padding > 0) {
        $data .= str_repeat('=', 4 - $padding);
    }
    
    // Decode from base64url to base64
    return base64_decode(strtr($data, '-_', '+/'));
}

    
    
    //  public function initiatePayment(Request $request)
    // {
    //     log::info($request->all());
    // //     $validator = Validator::make($request->all(), [
    // //         'amount' => 'required|string',
    // //         'name' => 'required|string',
    // //         'email' => 'required|string',
    // //         'mobile'=>'required|string',
    // //         'id' => 'required|numeric',

    // //     ]);

    // //     if ($validator->fails()) {
    // //         return back()->withErrors($validator)->withInput();
    // //     }
    // //     $name=$request->name;
    // //     $email=$request->email;
    // //     $amount=$request->amount;
    // //     $mobile=$request->mobile;
    // //     $customer_id=$request->id;
    // //      $paymentIds=$request->payids;
    // //   // $pids=explode(',',$paymentIds);
        
    // //     $merchant_OrderId=uniqid();
    // //   // $merchant_OrderId="6724643f489b9";
    // //   // dd($merchant_OrderId);
    // //     $productinfo="Test product";
    // //     $key = "urF7C4";
    // //     $salt = "lFjeqQcImS5tLa1HnS0qIz7nBMRuAYO9";
    // //     //return uniqid();
       
    // //     $transarray=array(
    // //       "payment_id"=>$paymentIds,
    // //         "customer_id"=>$request->id,
    // //         "amount"=>$amount,
    // //         "merchant_transid"=>$merchant_OrderId,
    // //         "payment_status"=>"PAYMENT_PENDING"
    // //         );
    // //       $insert=EmiTransaction::insert($transarray); 
       
    // //      $input = $key . "|" . $merchant_OrderId . "|" . $amount . "|" . $productinfo . "|" . $name. "|" . $email . "|".$customer_id."||||||||||" . $salt;
    // //      $hashkey= strtolower(hash("sha512", $input));
       
    // //     $payuData = [
    // //         'key' =>$key,
    // //         'txnid' => $merchant_OrderId,
    // //         'amount' => $amount,
    // //         'productinfo' => $productinfo,
    // //         'firstname' => $name,
    // //         'mobile' => $mobile,
    // //         'email' => $email,
    // //         "customer_id"=>$customer_id,
    // //         'hash' => $hashkey
    // //     ];

    // //     return  view(VIEW_FILE_NAMES['payu'], compact('payuData'));
    
    //  $validator = Validator::make($request->all(), [
    //         'amount' => 'required|string',
    //         'name' => 'required|string',
    //         'email' => 'required|string',
    //         'mobile'=>'required|string',
    //         'id' => 'required|numeric',
    //         'payids'=>'required',
    //         'loan_id'=>'required',
    //         // 'emi_date'=>'required',

    //     ]);

    //     if ($validator->fails()) {
    //         return back()->withErrors($validator)->withInput();
    //     }
    //     $name=$request->name;
    //     $email=$request->email;
    //     $amount=$request->amount;
    //     $mobile=$request->mobile;
    //     $customer_id=$request->id;
    //     $paymentIds=$request->payids;
    //     $loan_id=$request->loan_id;
    //     $emi_date=$request->emi_date;
    //   // $pids=explode(',',$paymentIds);
        
    //     $merchant_OrderId=uniqid();
    //     // dd($merchant_OrderId);
       
    //     $productinfo="Test product";
    //     $test="test test";
    //     // $key = "urF7C4";
    //     // $salt = "lFjeqQcImS5tLa1HnS0qIz7nBMRuAYO9";
        
    //      //live
    //     $key="ciYMKr";
    //     $salt="6cD4eqlQHawZsn1IHH6EGvJ9IBOL9xSL";
    //             // $salt="MIIEwAIBADANBgkqhkiG9w0BAQEFAASCBKowggSmAgEAAoIBAQD3yIEBRrP4QbY3ynsBqot1DRxLgYN3evbz+hM7E8qqjH7j4Hg7GmMDS8pghAgYN6eGk2XOs/RmXtLq72jD7cP0o4xp1OO7lQMQGDp4LOhyhOjXBTPIi+caQtLeqHO1eE0ZYwUm01q1qPL0l0IJyPQ0yTpAjg5Tgpp0OCgNdfyRjvnVO6UAhcMnDVRINhWcu/TvCLhh4O4VYc5I2gyiDIQ+Y1pQvjJ+PXCrlPb0mneh19N/+B9MOs7NeJnli0nFlCSo/00VuPQRPPtwtAzql+ejAi8jI9Tjmwzr5L2J1Y1OyLqEJfCCJLhZ+yncBIdodgnp85VHesYXYNP9SQUn9e31AgMBAAECggEBALCYIfk63sEsdCXHFWvWlJXTxjq6D2x0ItU3gcU1EdgDUdwu+wGEiNSsi4vGDc7Uu3zaSFDNSH49Tq1J+6zIJESS21wB8lyakxhBbEqCFxinSRsWBhYEP60juw4dmHnZR3m0bNODBr85rg5MTzCSHBoS4IVpuSQjjNkPvNv7HwrNKJj/VFysqM8vDyPWQe6Uw/Ci+GLfLPrdZDP0gAraNZMLPD6RV7eAH4w+EsU43uITiiTr6Sc+YP5i67sW1vol4K9bSSayVRGinUJYxu/fuuNXdt0esBM8XH6GgYm+fMJcRQeBffO+hGEnFMghZSWsrdu1ibLRVH34Y2bR4O88ifECgYEA/B5biyUZRUaQdwtT5tAROCxydV+DkcMny5jEMu5nQR+ihKbr7N0hNQeJOp2vrCnWpLN8Ie5ySAIIBl52hFz4lpEzEyhqFHqiSQvcyZciqjIw4DdyGNJ+R4QiumIpKFin9j4E0jZMGi+9I/n1muIQb4SrnxLUj+lqXSesJRRXP+8CgYEA+5kPVqw3j3XaVUC2ZFqVohfw3aAKW/stdBckwiM5OJynttnebU5fQhWUFT4wgsEmXR7W5RRuF09PSO7f4WZB1CzpAkvd4Zr9nxiUeicocvBgkNz5oxBG4zSSdcTzuDM5FUhDmI54C14Sfo3RBu0ITKzG2M3r3b6FZcKp9wY9DFsCgYEA0D5y9rep6+KhKPMeViO+VVvBHtnJ3vgHQs/oHvl0KAJtRoxpirgL2bVj0Bq3I8lbFad6/LvrgTbMUhZsZmA0pIlCWqyjEk9JBHTb5VcEtvfGDy08/OvAimnGFZVG0aCI+4e2i7t1mJud6r1n9IqNcM9wwm/XSxdNV6yRL/9hIdkCgYEAgKZAQJppApNJpAQl/2SOVMcXI8wc3/GsyUq4QgjOzpLT81yuLog/j0QHZ2FYXtOy8TS+v35V2Nd1/B4hHlkyWfLo5oKynxSokPx5l4iEV1lwl0JW57l/9dfA+DVQRiTzEF3WKqDW40EkBdAwTPFRKBvExcFt1QIBpgoG7Mgk2TcCgYEAt4WpZsM6kOszOchHs3HgfJFbd3sjqbZXlWvSh/6NowtRs8LJIwp1L4eaQ2CSUPDwUUZmse0guXObk6jcn5GNBHJREJ0f/VoegYPEQaa1oUt754pRx4/eEH8TPm7XycA7h61txKj27hKD8Po83P5870+1maHDyxWn7+YGqzH34I8=";

        
    //     // dd($customer_id,$loan_id,$paymentIds,$merchant_OrderId,$name,$email,$mobile,$emi_date,$amount);
    //     //return uniqid();
    //     $data=DB::table('emi_transactions')->insert([
    //         'customer_id'=>$customer_id,
    //         'loan_id'=>$loan_id,
    //         'emi_id'=>$paymentIds,
    //         // 'emi_id' => json_encode($paymentIds), 
    //         'txn_id'=>$merchant_OrderId,
    //         'name'=>$name,
    //         'email'=>$email,
    //         'mobile'=>$mobile,
    //         'emi_date'=>$emi_date,
    //         'amount'=>$amount,
    //         'status'=>'pending',
    //         'created_at'=>now(),
    //         'updated_at'=>now(),
    //     ]);

       
    //     // $transarray=array(
    //     //    "payment_id"=>$paymentIds,
    //     //     "customer_id"=>$request->id,
    //     //     "amount"=>$amount,
    //     //     "merchant_transid"=>$merchant_OrderId,
    //     //     "payment_status"=>"PAYMENT_PENDING",
    //     //     "response_msg"=>"success",
    //     //     "providerReferenceId"=>"1234",
    //     //     "merchantOrderId"=>$merchant_OrderId,
    //     //     "checksum"=>"123",
    //     //     );
    //     //   $insert=EmiTransaction::insert($transarray); 
       
    //      $input = $key . "|" . $merchant_OrderId . "|" . $amount . "|" . $productinfo . "|" . $name. "|" . $email . "|".$customer_id."||||||||||" . $salt;
    //      $hashkey= strtolower(hash("sha512", $input));
       
    //     $payuData = [
    //         'key' =>$key,
    //         'txnid' => $merchant_OrderId,
    //         'amount' => $amount,
    //         'productinfo' => $productinfo,
    //         'firstname' => $name,
    //         'mobile' => $mobile,
    //         'email' => $email,
    //         "customer_id"=>$customer_id,
    //         "loan_id"=>$loan_id,
    //         "emi_date"=>$emi_date,
    //         "payment_ids"=>$paymentIds,
    //         'hash' => $hashkey
    //     ];
        
    //     return  view(VIEW_FILE_NAMES['payu'], compact('payuData'));
    // }

    private function generateHash(Request $request)
    {
    $input = $key . '|' . $txnid . '|' . $amount . '|' . $productinfo . '|' . $firstname . '|' . $email . '|||||||||||' . $salt;
    return hash('sha512', $input);  
    }
  public function success(Request $request)
    {
        Log::channel('custom_log')->info('This is a custom log message.');
        
            Log::channel('custom_log')->info($request->all());

            //   dd($request->all());

        // dd('test2');
       // dd($request);
        // $transactionId=$request->mihpayid;
        // $paymode=$request->mode;
        // $payment_status=$request->status;
        // $merchant_orderId=$request->txnid;
        // $amount=$request->amount;
        // $bank_ref_num=$request->bank_ref_num;
        // $name=$request->firstname;
        // $data=array("customer_name"=>$request->firstname,
        // "TransactionId"=>$transactionId);
        // $sucessparams=array(
        //     "transaction_id"=>$transactionId,
        //     "payment_status"=>$payment_status,
        //     "PaymentType"=>$paymode,
        //     "response_msg"=>""
        //     );
        
        // $update=EmiTransaction::where('merchant_transid',$merchant_orderId)->update($sucessparams);
        // $getpayment=EmiTransaction::where('merchant_transid',$merchant_orderId)->first();
        // $paymentid=explode(',',$getpayment->payment_id);
        
        //  foreach($paymentid as $key => $value)
        //  {
        //      $uparr=array("status" => "1");
        //   $upd=Payment::where('id',$value)->update($uparr);
   
        //  }
        // return  view(VIEW_FILE_NAMES['paymentsucess'], compact('data'));

       // return  view(VIEW_FILE_NAMES['paymentsuccess']);
       
        $transactionId = $request->mihpayid;
    $paymode = $request->mode;
    $payment_status = $request->status;
    $unmappedstatus = $request->unmappedstatus;
    $merchant_orderId = $request->txnid;
    $amount = $request->amount;
    $bank_ref_num = $request->bank_ref_num;
    $name = $request->firstname;
    $data = array("customer_name" => $request->firstname, "TransactionId" => $transactionId);

    
    if ($payment_status == 'success' && $unmappedstatus == 'captured') {
        // Get the emi_transaction first before updating
        $emi_transaction = DB::table('emi_transactions')->where('txn_id', $merchant_orderId)->first();
        if ($emi_transaction) {
            // $emi_id = $emi_transaction->emi_id;
             $emi_ids = json_decode($emi_transaction->emi_id);  
    
            // Update the emi_transactions table
            $updated=DB::table('emi_transactions')->where('txn_id', $merchant_orderId)->update([
                'status' => 'paid',
                'updated_at' => now(),
            ]);
    
            // Update the payments table
            // $update_payments = DB::table('payments')->where('id', $emi_id)->update([
            //     'emi_status' => 'paid',
            //     'transaction_id' => $transactionId,
            //     'paid_date' => now(),
            //   'paid_datetime' => now(),
            //     'updated_at' => now(),
            // ]);
            
            if ($updated) {
    // Retrieve the emi_id of the updated transaction
    $emi_id = DB::table('emi_transactions')
        ->where('txn_id', $merchant_orderId)
        ->value('emi_id');

    // Update the payments table using the retrieved emi_id
    DB::table('payments')->where('id', $emi_id)->update([
        'emi_status' => 'paid',
        'transaction_id' => $transactionId,
        'app_name'=>'web',
        'paid_date' => now(),
        'paid_datetime' => now(),
        'updated_at' => now(),
    ]);
}
            // $emi_ids = explode(',', $emi_ids[0]);
            
            //   foreach ($emi_ids as $emi_id) {
            //     // Update the payments table for each emi_id
            //     DB::table('payments')->where('id', $emi_id)->update([
            //         'emi_status' => 'paid',
            //         'transaction_id' => $transactionId,
            //         'paid_date' => now(),
            //         'paid_datetime' => now(),
            //         'updated_at' => now(),
            //     ]);
                
            //     $customer_id=$emi_transaction->customer_id;

            // $response = Http::get('https://onecredit.in/api/v3/seller/update_available_credit', [
            //     'id' => $customer_id,
            // ]);
            
            // }
            
               $customer_id=$emi_transaction->customer_id;

            $response = Http::get('https://onecredit.in/api/v3/seller/update_available_credit', [
                'id' => $customer_id,
            ]);
            
        } else {
            Log::error("Emi transaction not found for txn_id: " . $merchant_orderId);
        }
    }
            return  view(VIEW_FILE_NAMES['paymentsucess'], compact('data'));


    }

    public function fail(): JsonResponse
    {
        return response()->json(['message' => 'Payment failed'], 403);
    }
    
    public function testbilldesk(){
        

$curl = curl_init();

curl_setopt_array($curl, array(
  CURLOPT_URL => 'https://uat1.billdesk.com/u2/payments/ve1_2/orders/create',
  CURLOPT_RETURNTRANSFER => true,
  CURLOPT_ENCODING => '',
  CURLOPT_MAXREDIRS => 10,
  CURLOPT_TIMEOUT => 0,
  CURLOPT_FOLLOWLOCATION => true,
  CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
  CURLOPT_CUSTOMREQUEST => 'POST',
  CURLOPT_POSTFIELDS =>'eyJhbGciOiJIUzI1NiIsImNsaWVudGlkIjoiYmR1YXQyazQ0NyJ9.eyJtZXJjaWQiOiJCRFVBVDJLNDQ3Iiwib3JkZXJpZCI6ImVzeFJHMWtIIiwiYW1vdW50IjoxMDAwLCJvcmRlcl9kYXRlIjoiMjAyNS0wMi0yNVQxNjoxNDoxNyswNTozMCIsImN1cnJlbmN5IjoiMzU2IiwicnUiOiJodHRwczpcL1wvb25lY3JlZGl0LmluXC9wYXltZW50U3VjY2VzcyIsImFkZGl0aW9uYWxfaW5mbyI6eyJhZGRpdGlvbmFsX2luZm8xIjoiVGVzdCBQYXltZW50IiwiYWRkaXRpb25hbF9pbmZvMiI6IkluZm8yIiwiYWRkaXRpb25hbF9pbmZvMyI6Ik9rIiwiYWRkaXRpb25hbF9pbmZvNCI6Ik5BIiwiYWRkaXRpb25hbF9pbmZvNSI6Ik5BIiwiYWRkaXRpb25hbF9pbmZvNiI6Ik5BIiwiYWRkaXRpb25hbF9pbmZvNyI6Ik5BIn0sIml0ZW1jb2RlIjoiRElSRUNUIiwiZGV2aWNlIjp7ImluaXRfY2hhbm5lbCI6ImludGVybmV0IiwiaXAiOiIxOTIuMTY4LjI5LjMyIiwidXNlcl9hZ2VudCI6Ik1vemlsbGFcLzUuMChXaW5kb3dzTlQxMC4wO1dPVzY0O3J2OjUxLjApR2Vja29cLzIwMTAwMTAxRmlyZWZveFwvNTEuMCIsImFjY2VwdF9oZWFkZXIiOiJ0ZXh0XC9odG1sIiwiYnJvd3Nlcl90eiI6Ii0zMzAifX0.LapKEZKW8xH59NLES5Polsn3Ocf-UE7XtrsoJ8FDSu8',
  CURLOPT_HTTPHEADER => array(
    'content-type:  application/jose',
    'accept: application/jose',
    'bd-timestamp: 20250225161544',
    'bd-traceid: UHNwKXZNTN5cEoVE64',
    'Cookie: ; TS01d1e959=018ba61b74f92bf3d67cf83d1c7765bb74fdef8e36ab9a136e6de708e39e644ff32ec96f7ecc340f73d0230f5b53db84b805eb3273'
  ),
));

$response = curl_exec($curl);

curl_close($curl);
echo $response;

    }
    
//     public function testbill(){
//         // <?php

// $curl = curl_init();

// curl_setopt_array($curl, array(
//   CURLOPT_URL => 'https://api.billdesk.com/payments/ve1_2/transactions/get',
//   CURLOPT_RETURNTRANSFER => true,
//   CURLOPT_ENCODING => '',
//   CURLOPT_MAXREDIRS => 10,
//   CURLOPT_TIMEOUT => 0,
//   CURLOPT_FOLLOWLOCATION => true,
//   CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
//   CURLOPT_CUSTOMREQUEST => 'POST',
//   CURLOPT_POSTFIELDS =>'eyJhbGciOiJIUzI1NiIsImNsaWVudGlkIjoib25lY3JlZGl0In0.eyJtZXJjaWQiOiJPTkVDUkVESVQiLCJvcmRlcmlkIjoiNjdlZmRiNDQzZTcyNCJ9.qTNoKuK5i3H67lvx9ADtYvryAlyPRkvhaLrutmha46Y',
//   CURLOPT_HTTPHEADER => array(
//     'content-type: application/jose',
//     'accept: application/json',
//     'bd-traceid: 20200817132207LOU5A',
//     'bd-timestamp: 20250504114534',
//     'Cookie: TS01871f2a=01eb63c73042a24e4c8ac21640f0834a8112fe98761a8c0f4ddd2d146f7cdd2e930121833f816af53c37872ed52293871598963d7d'
//   ),
// ));

// $response = curl_exec($curl);

// curl_close($curl);
// echo $response;
        
//     }

public function cron_retrive_billdesk(){
    $data = DB::table('emi_transactions')
        ->where('status', 'pending')
        ->where(function ($query) {
            $query->whereDate('created_at', '=', today())
                  ->orWhereDate('created_at', '=', Carbon::now()->subDay());
        })
        ->get();
        
        // dd($data);
        
    // Iterate through each transaction and make a cURL request
    foreach ($data as $transaction) {
        
           $traceid = Str::random(18);
    $timestamp = now()->format('YmdHis');
    
        // Retrieve the orderId (txn_id) from the transaction
        $orderId = $transaction->txn_id;
        
        // Get the token for the current orderId
        $token = $this->get_token_for_retrive_billdesk($orderId);
        
        // Prepare the cURL request
        $curl = curl_init();
        
        curl_setopt_array($curl, array(
            CURLOPT_URL => 'https://api.billdesk.com/payments/ve1_2/transactions/get',
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_ENCODING => '',
            CURLOPT_MAXREDIRS => 10,
            CURLOPT_TIMEOUT => 0,
            CURLOPT_FOLLOWLOCATION => true,
            CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
            CURLOPT_CUSTOMREQUEST => 'POST',
            CURLOPT_POSTFIELDS => $token, // Pass the dynamically generated token here
            CURLOPT_HTTPHEADER => array(
                'content-type: application/jose',
                'accept: application/json',
                'bd-traceid: ' . $traceid, // Dynamic trace ID
                'bd-timestamp: ' . $timestamp, // Dynamic timestamp
                'Cookie: TS01871f2a=01eb63c73042a24e4c8ac21640f0834a8112fe98761a8c0f4ddd2d146f7cdd2e930121833f816af53c37872ed52293871598963d7d'
            ),
        ));
        
        // Execute the cURL request and capture the response
        $response = curl_exec($curl);
        
        // Close the cURL session
        curl_close($curl);
        
        // Optionally, log the response or process further
        echo "Response for txn_id {$orderId}: " . $response . "\n";
        
        $this->sendResponseToSuccessBilldesk($response);
    
    }
}

public function sendResponseToSuccessBilldesk($response)
{
    // Send the response to the success_billdesk route as a POST request
    $response = Http::post('https://onecredit.in/success_billdesk', [
        'transaction_response' => $response
    ]);
}

    private function get_token_for_retrive_billdesk($orderId){
        
          $currentTimestamp = Carbon::now()->format('YmdHis');

        $date = Carbon::createFromFormat('YmdHis', $currentTimestamp);
        
        $formattedDateWithTimezone = $date->setTimezone('Asia/Kolkata')->toIso8601String(); 
        $timestamp = now()->format('YmdHis');
        
        $secretKey='0HlP8brfArOj00YI8Ue5cPYozEf8Gspu';
        $clientId='onecredit';
        $merchantId='ONECREDIT';
        $orderId=$orderId;
        $amount='1';
        $orderDate=$formattedDateWithTimezone;
        $currency='356';
        $ru='https://onecredit.in/paymentSuccess';

        // JWS Header
        $header = [
            "alg" => "HS256",
            "clientid" =>$clientId,
        ];
        $payload = [
            "mercid" => $merchantId,
            "orderid" => $orderId,          
        ];
        $headerEncoded = self::base64urlEncode(json_encode($header));
        $payloadEncoded = self::base64urlEncode(json_encode($payload));
        $message = $headerEncoded . '.' . $payloadEncoded;
        $signature = hash_hmac('sha256', $message, $secretKey, true);
        
        $signatureEncoded = self::base64urlEncode($signature);
        $jwsToken = $headerEncoded . '.' . $payloadEncoded . '.' . $signatureEncoded;
        return $jwsToken;
        
    }
    
    public function retrive(){
        

$curl = curl_init();

curl_setopt_array($curl, array(
  CURLOPT_URL => 'https://uat1.billdesk.com/u2/payments/ve1_2/transactions/get',
  CURLOPT_RETURNTRANSFER => true,
  CURLOPT_ENCODING => '',
  CURLOPT_MAXREDIRS => 10,
  CURLOPT_TIMEOUT => 0,
  CURLOPT_FOLLOWLOCATION => true,
  CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
  CURLOPT_CUSTOMREQUEST => 'POST',
  CURLOPT_POSTFIELDS =>'eyJhbGciOiJIUzI1NiIsImNsaWVudGlkIjoib25lY3JlZGl0In0.eyJtZXJjaWQiOiJPTkVDUkVESVQiLCJvcmRlcmlkIjoiNjdlZmRiNDQzZTcyNCJ9.qTNoKuK5i3H67lvx9ADtYvryAlyPRkvhaLrutmha46Y',
  CURLOPT_HTTPHEADER => array(
    'content-type: application/jose',
    'accept: application/json',
    'bd-traceid: 20200817132207LOU21',
    'bd-timestamp: 20250213033434',
    'Cookie: ; TS01d1e959=018ba61b74031855773e7eb2666de2a35b9b1eea04a5067ec5299f2b768439b98d0d2b8269013474393f3cf78f2af592b89c41df67'
  ),
));

$response = curl_exec($curl);

curl_close($curl);
echo $response;

    }
    // public function success(Request $request)
    // {
    //     // Handle success response from PayU
    //     return view('payment.success', ['data' => $request->all()]);
    // }

    // public function failure(Request $request)
    // {
    //     // Handle failure response from PayU
    //     return view('payment.failure', ['data' => $request->all()]);
    // }
    
//     public function success_billdesk(Request $request)
// {
//       $response=$request->transaction_response;
 
//     // Decode the JWT
//     $data = $this->decode_jwt($response);
    
//     // dd($data);
//     // log::info($data);
    
//     // Check if the transaction is successful
//     if ($data['auth_status'] == '0300') {
//         // Step 1: Find the EMI transaction from the emi_transactions table
//         $emiTransaction = DB::table('emi_transactions')->where('txn_id', $data['orderid'])->first();

//         if ($emiTransaction) {
//             $emi_id = $emiTransaction->emi_id;
            
//             $txn_id=$emiTransaction->txn_id;

//             // Step 2: Find the corresponding payment record from the payments table
//             $payment = DB::table('payments')->where('id', $emi_id)->first();
            
//             // 'payment_method_type' => 'upi',
            
//             $additional_info4 = $data['additional_info']['additional_info4'];
//             $payment_method_type=$data['payment_method_type'];

//             $app = "BD - "  . $additional_info4 . " (" . $payment_method_type . ")";

//             if ($payment) {
//                 // Step 3: Update the payment details
//                 DB::table('payments')->where('id', $emi_id)->update([
//                     'emi_status' => 'paid',
//                     'paid_date' => Carbon::today()->toDateString(), // Today's date
//                     'paid_datetime' => Carbon::now(), // Current datetime
//                     'transaction_id' => $data['transactionid'],
//                     'total_amount' => $data['charge_amount'],
//                     'app_name'=>$app,
//                 ]);
                
//                  DB::table('emi_transactions')->where('txn_id', $txn_id)->update([
//                     'status' => 'paid',
//                 ]);

//                 // Step 4: Redirect to the view test2 with $data
//                 return view('test3')->with('data', $data);
//             } else {
//                 // Handle case where payment with that emi_id is not found
//                 return redirect()->route('errorView')->with('message', 'Payment not found');
//             }
//         } else {
//             // Handle case where emi_transaction with that txn_id is not found
//             return redirect()->route('errorView')->with('message', 'EMI Transaction not found');
//         }
//     } 
    
//      else if ($data['auth_status'] == '0399') {
//         return view('test3')->with('data', $data);
//     }
    
//     else {
//         // If auth_status is not successful
//         return redirect()->route('errorView')->with('message', 'Transaction failed');
//     }
// }

public function success_billdesk(Request $request)
{
    $response = $request->transaction_response;

    // Decode the JWT
    $data = $this->decode_jwt($response);
  
   Log::info('this is billdesk successs');
    Log::info($data);

    // Check if the transaction is successful
    if ($data['auth_status'] == '0300') {
        // Step 1: Find the EMI transaction from the emi_transactions table
        $emiTransaction = DB::table('emi_transactions')->where('txn_id', $data['orderid'])->first();

        if ($emiTransaction) {
            // Get the emi_id field (can be a JSON array or comma-separated string)
            // Assuming it's a JSON array like: ["15053", "15054"]
            $emi_ids = json_decode($emiTransaction->emi_id, true);
            
            // Fallback if not JSON (i.e. comma-separated string like "15053,15054")
            if (!is_array($emi_ids)) {
                $emi_ids = explode(',', $emiTransaction->emi_id);
            }

            $txn_id = $emiTransaction->txn_id;

            $additional_info4 = $data['additional_info']['additional_info4'] ?? '';
            $payment_method_type = $data['payment_method_type'] ?? '';
            $app = "BD - " . $additional_info4 . " (" . $payment_method_type . ")";

            // Step 2: Loop through each EMI ID and update payment record
            foreach ($emi_ids as $emi_id) {
                $payment = DB::table('payments')->where('id', $emi_id)->first();

                if ($payment) {
                    DB::table('payments')->where('id', $emi_id)->update([
                        'emi_status' => 'paid',
                        'paid_date' => Carbon::today()->toDateString(), // Today's date
                        'paid_datetime' => Carbon::now(), // Current datetime
                        'transaction_id' => $data['transactionid'],
                        'total_amount' => $data['charge_amount'],
                        'app_name' => $app,
                    ]);
                }
            }

            // Step 3: Update the emi_transactions table
            DB::table('emi_transactions')->where('txn_id', $txn_id)->update([
                'status' => 'paid',
            ]);

            // Step 4: Redirect to the view with $data
            return view('test3')->with('data', $data);

        } else {
            // Handle case where emi_transaction with that txn_id is not found
            return redirect()->route('errorView')->with('message', 'EMI Transaction not found');
        }

    } else if ($data['auth_status'] == '0399') {
        return view('test3')->with('data', $data);
    } else {
        // If auth_status is not successful
        return redirect()->route('errorView')->with('message', 'Transaction failed');
    }
}


public function retrive_billdesk(Request $request){
    // dd('test');

$curl = curl_init();

curl_setopt_array($curl, array(
  CURLOPT_URL => 'https://uat1.billdesk.com/u2/payments/ve1_2/transactions/get',
  CURLOPT_RETURNTRANSFER => true,
  CURLOPT_ENCODING => '',
  CURLOPT_MAXREDIRS => 10,
  CURLOPT_TIMEOUT => 0,
  CURLOPT_FOLLOWLOCATION => true,
  CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
  CURLOPT_CUSTOMREQUEST => 'POST',
  CURLOPT_POSTFIELDS =>'eyJhbGciOiJIUzI1NiIsImNsaWVudGlkIjoiYmR1YXQyazQ0NyJ9.eyJtZXJjaWQiOiJCRFVBVDJLNDQ3Iiwib3JkZXJpZCI6IjY3YzAyYTc4YTIyZGUifQ.8SQSIk4W_YTfZKoHoGxOwwglHBZXjlRnNU1Ja3HwjRU',
  CURLOPT_HTTPHEADER => array(
    'content-type: application/jose',
    'accept: application/json',
     'bd-traceid: 20200817132207LOU31',
    'bd-timestamp: 20250227150734',
    'Cookie: ; TS01d1e959=018ba61b74d9c5160540e1553a208773fc7761941ba3a865a799c9fc5fd7d0c0272f6cee7d1d8af0a6b6e15156b801da32285dd7c3'
  ),
));

$response = curl_exec($curl);

curl_close($curl);
echo $response;

}
  
  public function initiateUPIMandate(Request $request)
{
  //  $customer_id = $request->id;
   // $loan_id = $request->loan_id;
    
      $customer_id = 4;
    $loan_id = 'ONC10';
    $subscription_refid = 'SUB_' . $loan_id . '_' . $customer_id;
    $customer_refid = 'CUST_' . $customer_id;
    
    // Generate unique mandate token ID
    $mandate_tokenid = uniqid('MANDATE');
    
    
    // Store mandate initiation data
    DB::table('mandate_registrations')->insert([
        'customer_id' => $customer_id,
        'loan_id' => $loan_id,
        'mandate_tokenid' => $mandate_tokenid,
        'subscription_refid' => $subscription_refid,
        'customer_refid' => $customer_refid,
        'status' => 'initiated',
        'created_at' => now(),
        'updated_at' => now(),
    ]);
    
    // Generate JWS token for mandate
    $jwtToken = $this->generateMandateJwsToken($customer_refid, $subscription_refid, $mandate_tokenid);
        Log::info('initiateUPIMandate function : Original encoded Create Mandate API request string: ' . $jwtToken);

    
    // Create mandate token
    $mandateData = $this->createMandateToken($jwtToken);
    
  
    
 if ($mandateData && isset($mandateData['links'][1]['parameters'])) {
    $rdata = $mandateData['links'][1]['parameters']['rdata'];
    $mercid = $mandateData['links'][1]['parameters']['mercid'];
    $mandate_tokenid = $mandateData['links'][1]['parameters']['mandate_tokenid']; // ✅ fixed key
        
        // Update with actual token ID from BillDesk
         // DB::table('mandate_registrations')
        //     ->where('mandate_tokenid', $mandate_tokenid)
        //     ->update(['bd_mandate_tokenid' => $mandate_tokenid]);
        
        return view('mandate_setup', [
            'mandatetokenid' => $mandate_tokenid,
            'merchantid' => $mercid,
            'rdata' => $rdata
        ]);
    } else {
        return response()->json(['error' => 'Mandate setup failed.'], 500);
    }
}

public static function generateMandateJwsToken($customer_refid, $subscription_refid, $mandate_tokenid)
{
    $secretKey = 'kCQhF9iLB5HhkcV7IIXfg9biJqpQ5BI4';
    $clientId = 'bduat2k447';
    $merchantId = 'BDUAT2K447';
    
    $currentTimestamp = Carbon::now()->format('YmdHis');
    $date = Carbon::createFromFormat('YmdHis', $currentTimestamp);
    $formattedDateWithTimezone = $date->setTimezone('Asia/Kolkata')->toIso8601String();
    
    // JWS Header
    $header = [
        "alg" => "HS256",
        "clientid" => $clientId,
    ];
    
    // Mandate payload as per Section 6
    $payload = [
        "mercid" => $merchantId,
        "customer_refid" => $customer_refid,
        "subscription_refid" => $subscription_refid,
        "subscription_desc" => "Weekly Loan EMI",
        "currency" => "356",
        "frequency" => "week", // Weekly payments
        "amount_type" => "max",
        "amount" => "2000.00", // Maximum limit for weekly debit
        "start_date" => date('Y-m-d'), // Today's date
        "end_date" => date('Y-m-d', strtotime('+1 year')), // 1 year validity
        "recurrence_rule" => "after", // For UPI
        "debit_day" => "1", // For UPI
        "ru" => "https://onecredit.in/mandate-response", // Your mandate response URL
        "customer" => [
            "first_name" => "sasi", // Get from your DB
            "last_name" => "kumar",
            "mobile" => "9800000000", // Get from your DB
            "email" => "customer@email.com" // Get from your DB
        ],
        "additional_info" => [
            "additional_info1" => "sasi kumar ",
            "additional_info2" => "6383562660",
            "additional_info3" => "sasikumar80738@gmail.com",
            "additional_info4" => "NA",
            "additional_info5" => "NA",
            "additional_info6" => "NA",
            "additional_info7" => "NA",
        ],
        "device" => [
            "init_channel" => "internet",
            "ip" => request()->ip(),
            "user_agent" => request()->userAgent(),
            "accept_header" => request()->header('Accept'),
            "browser_tz" => "-330",
            "browser_color_depth" => "32",
            "browser_java_enabled" => "false",
            "browser_screen_height" => "601",
            "browser_screen_width" => "657",
            "browser_language" => "en-US",
            "browser_javascript_enabled" => "true"
        ],
    ];
    
    $headerEncoded = self::base64urlEncode(json_encode($header));
    $payloadEncoded = self::base64urlEncode(json_encode($payload));
    $message = $headerEncoded . '.' . $payloadEncoded;
    $signature = hash_hmac('sha256', $message, $secretKey, true);
    $signatureEncoded = self::base64urlEncode($signature);
    
    return $headerEncoded . '.' . $payloadEncoded . '.' . $signatureEncoded;
}

public function createMandateToken($jwt)
{
    $url = 'https://uat1.billdesk.com/u2/pgsi/ve1_2/mandatetokens/create';
     


    
    $timestamp = now()->format('YmdHis');
    $traceid = Str::random(18);
Log::info('createMandateToken timestamp: ' . $timestamp . ' traceid: ' . $traceid);
  
    $headers = [
        'content-type: application/jose',
        'accept: application/jose',
        'bd-timestamp: ' . $timestamp,
        'bd-traceid: ' . $traceid,
    ];
    
   
    $ch = curl_init();
    curl_setopt_array($ch, [
        CURLOPT_URL => $url,
        CURLOPT_RETURNTRANSFER => true,
        CURLOPT_ENCODING => '',
        CURLOPT_MAXREDIRS => 10,
        CURLOPT_TIMEOUT => 30,
        CURLOPT_FOLLOWLOCATION => true,
        CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
        CURLOPT_CUSTOMREQUEST => 'POST',
        CURLOPT_POSTFIELDS => $jwt,
        CURLOPT_HTTPHEADER => $headers,
    ]);
    
    $response = curl_exec($ch);
    curl_close($ch);
        Log::info('createMandateToken function : Original encoded Create Mandate API response string: ' . $response);

    $responseData = $this->decode_jwt($response);
 
    
    return $responseData;
}

public function handleMandateResponse(Request $request)
{
    // BillDesk will post response to your RU URL
    $mandate_response = $request->get('mandate_response');
    $mercid = $request->get('mercid');
    $mandate_tokenid = $request->get('mandate_tokenid');
    
    if ($mandate_response) {
        // Decode the mandate response
        $decodedResponse = $this->decode_jwt($mandate_response);
        
        Log::info('Mandate Response: ' . json_encode($decodedResponse));
        
        if ($decodedResponse && isset($decodedResponse['status']) && $decodedResponse['status'] === 'active') {
            // Mandate setup successful
            DB::table('mandate_registrations')
                ->where('bd_mandate_tokenid', $mandate_tokenid)
                ->update([
                    'status' => 'active',
                    'mandateid' => $decodedResponse['mandateid'],
                    'payment_method' => $decodedResponse['payment_method_type'],
                    'updated_at' => now()
                ]);
            
            return redirect('/mandate-success');
        } else {
            // Mandate setup failed
            DB::table('mandate_registrations')
                ->where('bd_mandate_tokenid', $mandate_tokenid)
                ->update([
                    'status' => 'failed',
                    'failure_reason' => $decodedResponse['verification_error_desc'] ?? 'Unknown error',
                    'updated_at' => now()
                ]);
            
            return redirect('/mandate-failed');
        }
    }
    
    return response()->json(['error' => 'Invalid mandate response'], 400);
}

public function createInvoice($mandateId)
{
    try {
        // Generate JWS token for invoice creation
        $jwtToken = $this->generateInvoiceJwsToken($mandateId);
      
           Log::info('JWT Response: ' . $jwtToken);

        // Create invoice via BillDesk API
        $invoiceData = $this->createInvoiceAPI($jwtToken);
        
         Log::info('Invoice Creation Response: ' . json_encode($invoiceData));
        
        if ($invoiceData && isset($invoiceData['invoice_id']) && $invoiceData['status'] === 'unpaid') {
            // Store invoice details in database
            DB::table('invoice_records')->insert([
                'mandate_id' => $mandateId,
                'invoice_id' => $invoiceData['invoice_id'],
                'invoice_number' => $invoiceData['invoice_number'],
                'merchant_id' => $invoiceData['mercid'],
                'customer_refid' => $invoiceData['customer_refid'],
                'subscription_refid' => $invoiceData['subscription_refid'],
                'amount' => $invoiceData['amount'],
                'due_date' => $invoiceData['duedate'],
                'debit_date' => $invoiceData['debit_date'],
                'status' => $invoiceData['status'],
                'created_at' => now(),
                'updated_at' => now(),
            ]);
            
            return [
                'success' => true,
                'invoice_id' => $invoiceData['invoice_id'],
                'invoice_number' => $invoiceData['invoice_number'],
                'status' => $invoiceData['status'],
                'debit_date' => $invoiceData['debit_date']
            ];
        } else {
            Log::error('Invoice creation failed: ' . json_encode($invoiceData));
            return [
                'success' => false,
                'error' => $invoiceData['verification_error_desc'] ?? 'Invoice creation failed'
            ];
        }
        
    } catch (\Exception $e) {
        Log::error('Invoice Creation Error: ' . $e->getMessage());
        return [
            'success' => false,
            'error' => $e->getMessage()
        ];
    }
}

public function generateInvoiceJwsToken($mandateId)
{
    $secretKey = 'kCQhF9iLB5HhkcV7IIXfg9biJqpQ5BI4'; // Your secret key
    $clientId = 'bduat2k447';
    $merchantId = 'BDUAT2K447';
      
    // Get mandate details from database
    $mandate = DB::table('mandate_registrations')->where('mandate_tokenid', $mandateId)->first();
    
    if (!$mandate) {
        throw new \Exception('Mandate not found');
    }
    
    // Generate unique invoice number
    $invoiceNumber = 'INV_' . $mandate->loan_id . '_' . time();
    $invoiceDisplayNumber = 'EMI-' . date('Ymd') . '-' . $mandate->loan_id;
    
    // Calculate dates (2 days before actual debit as per regulatory requirement)
    $debitDate = date('Y-m-d', strtotime('+2 days')); // Actual debit date
    $dueDate = date('Y-m-d', strtotime('+5 days')); // Due date
    $invoiceDate = date('Y-m-d'); // Invoice generation date
    
    // JWS Header
    $header = [
        "alg" => "HS256",
        "clientid" => $clientId,
    ];
    // Invoice payload as per Section 8.1.2
    $payload = [
        "mandateid" => $mandateId,
        "mercid" => $merchantId,
        "customer_refid" => $mandate->customer_refid,
        "subscription_refid" => $mandate->subscription_refid,
        "invoice_number" => $invoiceNumber,
        "invoice_display_number" => $invoiceDisplayNumber,
        "invoice_date" => $invoiceDate,
        "duedate" => $dueDate,
        "debit_date" => $debitDate,
        "amount" => "1000.00", // EMI amount - get from loan details
        "net_amount" => "1000.00", // Same as amount if no discounts
        "currency" => "356",
        "description" => "Loan EMI for " . date('F Y')

    ];
    
    // Optional fields - include if applicable
    // if (isset($loanDetails['early_payment_discount'])) {
    //     $payload["early_payment_duedate"] = date('Y-m-d', strtotime('+1 day'));
    //     $payload["early_payment_discount"] = $loanDetails['early_payment_discount'];
    //     $payload["early_payment_amount"] = $payload["amount"] - $loanDetails['early_payment_discount'];
    // }
    
    // if (isset($loanDetails['late_payment_charges'])) {
    //     $payload["late_payment_charges"] = $loanDetails['late_payment_charges'];
    //     $payload["late_payment_amount"] = $payload["amount"] + $loanDetails['late_payment_charges'];
    // }
    
    $headerEncoded = $this->base64urlEncode(json_encode($header));
    $payloadEncoded = $this->base64urlEncode(json_encode($payload));
    $message = $headerEncoded . '.' . $payloadEncoded;
    $signature = hash_hmac('sha256', $message, $secretKey, true);
    $signatureEncoded = $this->base64urlEncode($signature);
    
    return $headerEncoded . '.' . $payloadEncoded . '.' . $signatureEncoded;
}

public function createInvoiceAPI($jwt)
{
  
    $url = 'https://uat1.billdesk.com/u2/pgsi/ve1_2/invoices/create';
    
    $timestamp = now()->format('YmdHis');
    $traceid = Str::random(18);
    
    $headers = [
        'content-type: application/jose',
        'accept: application/jose',
        'bd-timestamp: ' . $timestamp,
        'bd-traceid: ' . $traceid,
    ];
  
    
    Log::info('Invoice Creation Request JWT: ' . $jwt);
    Log::info('Invoice timestamp: ' . $timestamp);
    Log::info('Invoice traceid: ' . $traceid);
    
    $ch = curl_init();
    curl_setopt_array($ch, [
        CURLOPT_URL => $url,
        CURLOPT_RETURNTRANSFER => true,
        CURLOPT_ENCODING => '',
        CURLOPT_MAXREDIRS => 10,
        CURLOPT_TIMEOUT => 30,
        CURLOPT_FOLLOWLOCATION => true,
        CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
        CURLOPT_CUSTOMREQUEST => 'POST',
        CURLOPT_POSTFIELDS => $jwt,
        CURLOPT_HTTPHEADER => $headers,
    ]);
    
    $response = curl_exec($ch);

    $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
    curl_close($ch);
  
    
    if ($httpCode !== 200) {
        Log::error('Invoice API HTTP Error: ' . $httpCode . ' - ' . $response);
        return null;
    }
    
    $responseData = $this->decode_jwt($response);
    return $responseData;
}


// Controller method to trigger invoice creation
public function createLoanInvoice(Request $request)
{
    $mandateId = $request->mandate_id;
    // $loanId = $request->loan_id;
    
    // // Get loan details from database
    // $loanDetails = DB::table('loans')->where('id', $loanId)->first();
    
    // if (!$loanDetails) {
    //     return response()->json(['error' => 'Loan not found'], 404);
    // }
    
    // $invoiceResult = $this->createInvoice($mandateId, [
    //     'emi_amount' => $loanDetails->emi_amount,
    //     'early_payment_discount' => $loanDetails->early_payment_discount ?? null,
    //     'late_payment_charges' => $loanDetails->late_payment_charges ?? null,
    // ]);
     $invoiceResult = $this->createInvoice($mandateId, [
        'emi_amount' => '2000',
        'early_payment_discount' => $loanDetails->early_payment_discount ?? null,
        'late_payment_charges' => $loanDetails->late_payment_charges ?? null,
    ]);
    
    if ($invoiceResult['success']) {
        return response()->json([
            'success' => true,
            'message' => 'Invoice created successfully',
            'invoice_id' => $invoiceResult['invoice_id'],
            'invoice_number' => $invoiceResult['invoice_number'],
            'debit_date' => $invoiceResult['debit_date']
        ]);
    } else {
        return response()->json([
            'success' => false,
            'error' => $invoiceResult['error']
        ], 500);
    }
}

// Retrieve Invoice (Optional - as per Section 8.2)
public function getInvoice($merchantId, $invoiceNumber)
{
    $jwtToken = $this->generateGetInvoiceJwsToken($merchantId, $invoiceNumber);
    
    $url = 'https://uat1.billdesk.com/u2/pgsi/ve1_2/invoices/get';
    
    $timestamp = now()->format('YmdHis');
    $traceid = Str::random(18);
    
    $headers = [
        'content-type: application/jose',
        'accept: application/jose',
        'bd-timestamp: ' . $timestamp,
        'bd-traceid: ' . $traceid,
    ];
    
    $ch = curl_init();
    curl_setopt_array($ch, [
        CURLOPT_URL => $url,
        CURLOPT_RETURNTRANSFER => true,
        CURLOPT_CUSTOMREQUEST => 'POST',
        CURLOPT_POSTFIELDS => $jwtToken,
        CURLOPT_HTTPHEADER => $headers,
    ]);
    
    $response = curl_exec($ch);
    curl_close($ch);
    
    return $this->decode_jwt($response);
}

private function generateGetInvoiceJwsToken($merchantId, $invoiceNumber)
{
    $secretKey = 'kCQhF9iLB5HhkcV7IIXfg9biJqpQ5BI4';
    $clientId = 'bduat2k447';
    
    $header = [
        "alg" => "HS256",
        "clientid" => $clientId,
    ];
    
    $payload = [
        "mercid" => $merchantId,
        "invoice_number" => $invoiceNumber
    ];
    
    $headerEncoded = $this->base64urlEncode(json_encode($header));
    $payloadEncoded = $this->base64urlEncode(json_encode($payload));
    $message = $headerEncoded . '.' . $payloadEncoded;
    $signature = hash_hmac('sha256', $message, $secretKey, true);
    $signatureEncoded = $this->base64urlEncode($signature);
    
    return $headerEncoded . '.' . $payloadEncoded . '.' . $signatureEncoded;
}
  
  public function createRecurringTransaction(Request $request)
{
    try {
        $mandateId = $request->mandate_id;
        $invoiceId = $request->invoice_id;
        $amount = $request->amount;
      
          
        
        // Get mandate details from database
        $mandate = DB::table('mandate_registrations')
            ->where('mandate_tokenid', $mandateId)
            ->first();
        
        if (!$mandate) {
            return response()->json(['error' => 'Mandate not found'], 404);
        }
        
        // Generate JWS token for recurring transaction
        $jwtToken = $this->generateRecurringTransactionJwsToken(
            $mandateId,
            $invoiceId,
            $amount,
            $mandate
        );
      
        
        // Create recurring transaction via BillDesk API
        $transactionData = $this->createRecurringTransactionAPI($jwtToken);
      
        
        Log::info('Recurring Transaction Response: ' . json_encode($transactionData));
              
        if ($transactionData && isset($transactionData['transactionid'])) {
            // Store transaction details in database
            DB::table('recurring_transactions')->insert([
                'mandate_id' => $mandateId,
                'invoice_id' => $invoiceId,
                'transaction_id' => $transactionData['transactionid'],
                'order_id' => $transactionData['orderid'],
                'merchant_id' => $transactionData['mercid'],
                'amount' => $transactionData['amount'],
                'charge_amount' => $transactionData['charge_amount'] ?? $transactionData['amount'],
                'currency' => $transactionData['currency'],
                'auth_status' => $transactionData['auth_status'],
                'transaction_status' => $transactionData['transaction_error_type'] ?? 'pending',
                'bank_ref_no' => $transactionData['bank_ref_no'] ?? null,
                'transaction_date' => $transactionData['transaction_date'],
                'payment_method_type' => $transactionData['payment_method_type'] ?? 'upi',
                'created_at' => now(),
                'updated_at' => now(),
            ]);
            
            return [
                'success' => true,
                'transaction_id' => $transactionData['transactionid'],
                'order_id' => $transactionData['orderid'],
                'auth_status' => $transactionData['auth_status'],
                'transaction_status' => $transactionData['transaction_error_type'] ?? 'pending',
                'transaction_error_desc' => $transactionData['transaction_error_desc'] ?? 'Transaction Successful',
                'amount' => $transactionData['amount']
            ];
        } else {
            Log::error('Recurring transaction failed: ' . json_encode($transactionData));
            return [
                'success' => false,
                'error' => $transactionData['verification_error_desc'] ?? $transactionData['transaction_error_desc'] ?? 'Recurring transaction failed'
            ];
        }
        
    } catch (\Exception $e) {
        Log::error('Recurring Transaction Error: ' . $e->getMessage());
        return [
            'success' => false,
            'error' => $e->getMessage()
        ];
    }
}
  
  public function generateRecurringTransactionJwsToken($mandateId, $invoiceId, $amount, $mandate)
{
    $secretKey = 'kCQhF9iLB5HhkcV7IIXfg9biJqpQ5BI4'; // Your secret key
    $clientId = 'bduat2k447';
    $merchantId = 'BDUAT2K447';
    
    // Generate unique order ID
    $orderId = 'REC_' . $mandate->loan_id . '_' . time();
    
    // JWS Header
    $header = [
        "alg" => "HS256",
        "clientid" => $clientId,
    ];
    // Recurring transaction payload as per Section 8.3
    $payload = [
        "mercid" => $merchantId,
        "orderid" => $orderId,
        "amount" => number_format($amount, 2, '.', ''),
        "currency" => "356",
        "txn_process_type" => "si", // SI for Standing Instruction
        "mandateid" => $mandateId,
        "subscription_refid" => $mandate->subscription_refid,
        "invoice_id" => $invoiceId,
        "itemcode" => "DIRECT",
        "additional_info" => [
            "additional_info1" => "Loan EMI Debit",
            "additional_info2" => "Recurring Payment",
          "additional_info3" => "sasi kumar ",
            "additional_info4" => "6383562660",
            "additional_info5" => "sasikumar80738@gmail.com",
            "additional_info6" => "NA",
            "additional_info7" => "NA",
        ]
    ];
    
    // Add device object for UPI/eNACH (as per documentation)
    if ($mandate->payment_method == 'upi') {
        $payload["device"] = [
            "init_channel" => "internet",
            "ip" => request()->ip(),
            "user_agent" => request()->userAgent(),
            "accept_header" => request()->header('Accept'),
            "browser_tz" => "-330",
            "browser_color_depth" => "32",
            "browser_java_enabled" => "false",
            "browser_screen_height" => "601",
            "browser_screen_width" => "657",
            "browser_language" => "en-US",
            "browser_javascript_enabled" => "true"
        ];
        
        // Add customer object for UPI
        $payload["customer"] = [
            "first_name" => "Customer", // Get from DB
            "last_name" => "Name",
            "mobile" => "9800000000",
            "email" => "customer@email.com"
        ];
    }
    
    // Add bankid for eNACH
    if ($mandate->payment_method == 'enach') {
        $payload["bankid"] = "ENY"; // Static value for eNACH as per documentation
        $payload["device"] = [
            "init_channel" => "internet",
            "ip" => request()->ip(),
            "user_agent" => request()->userAgent(),
            "accept_header" => request()->header('Accept'),
            "browser_tz" => "-330",
            "browser_color_depth" => "32",
            "browser_java_enabled" => "false",
            "browser_screen_height" => "601",
            "browser_screen_width" => "657",
            "browser_language" => "en-US",
            "browser_javascript_enabled" => "true"
        ];
    }
    
    $headerEncoded = $this->base64urlEncode(json_encode($header));
    $payloadEncoded = $this->base64urlEncode(json_encode($payload));
    $message = $headerEncoded . '.' . $payloadEncoded;
    $signature = hash_hmac('sha256', $message, $secretKey, true);
    $signatureEncoded = $this->base64urlEncode($signature);
    
    return $headerEncoded . '.' . $payloadEncoded . '.' . $signatureEncoded;
}

public function createRecurringTransactionAPI($jwt)
{
    $url = 'https://uat1.billdesk.com/u2/payments/ve1_2/transactions/create';
    
    $timestamp = now()->format('YmdHis');
    $traceid = Str::random(18);
    Log::info('createRecurringTransactionAPI timestamp: ' . $timestamp . ' traceid: ' . $traceid);

    $headers = [
        'content-type: application/jose',
        'accept: application/jose',
        'bd-timestamp: ' . $timestamp,
        'bd-traceid: ' . $traceid,
    ];
    
        Log::info('createRecurringTransaction function : Original encoded Create Recurring Transaction API request string with
        BD-TraceID=' . $traceid . ' & BD-Timestamp=' . $timestamp . ': ' . $jwt);

    
    $ch = curl_init();
    curl_setopt_array($ch, [
        CURLOPT_URL => $url,
        CURLOPT_RETURNTRANSFER => true,
        CURLOPT_ENCODING => '',
        CURLOPT_MAXREDIRS => 10,
        CURLOPT_TIMEOUT => 30,
        CURLOPT_FOLLOWLOCATION => true,
        CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
        CURLOPT_CUSTOMREQUEST => 'POST',
        CURLOPT_POSTFIELDS => $jwt,
        CURLOPT_HTTPHEADER => $headers,
    ]);
    
    $response = curl_exec($ch);
    $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
    curl_close($ch);
    dd($response);
    if ($httpCode !== 200) {
        Log::error('Recurring Transaction API HTTP Error: ' . $httpCode . ' - ' . $response);
        return null;
    }
       Log::info('createRecurringTransactionAPI function : Original encoded Create Recurring Transaction API response string: ' . $response);
    $responseData = $this->decode_jwt($response);
    return $responseData;
}


// Controller method to trigger recurring transaction
public function triggerRecurringDebit(Request $request)
{
    $mandateId = $request->mandate_id;
    $invoiceId = $request->invoice_id;
    $amount = $request->amount;
    
    $transactionResult = $this->createRecurringTransaction($mandateId, $invoiceId, $amount);
    
    if ($transactionResult['success']) {
        return response()->json([
            'success' => true,
            'message' => 'Recurring transaction initiated successfully',
            'transaction_id' => $transactionResult['transaction_id'],
            'order_id' => $transactionResult['order_id'],
            'auth_status' => $transactionResult['auth_status'],
            'status' => $transactionResult['transaction_status'],
            'amount' => $transactionResult['amount']
        ]);
    } else {
        return response()->json([
            'success' => false,
            'error' => $transactionResult['error']
        ], 500);
    }
}

/**
 * 4. RETRIEVE MANDATE (Section 9.3)
 */
public function retrieveMandate($mandateId)
{
    try {
        $jwtToken = $this->generateRetrieveMandateJwsToken($mandateId);
        
        $mandateData = $this->retrieveMandateAPI($jwtToken);
        
        Log::info('Retrieve Mandate Response: ' . json_encode($mandateData));
        
        if ($mandateData && isset($mandateData['mandateid'])) {
            // Update mandate details in database
            DB::table('mandate_registrations')
                ->where('mandate_tokenid', $mandateId)
                ->update([
                    'status' => $mandateData['status'],
                    'amount' => $mandateData['amount'],
                    'amount_type' => $mandateData['amount_type'],
                    'start_date' => $mandateData['start_date'],
                    'end_date' => $mandateData['end_date'],
                    'frequency' => $mandateData['frequency'],
                    'payment_method' => $mandateData['payment_method_type'],
                    'bank_unrm' => $mandateData['bank_unrm'] ?? null,
                    'updated_at' => now()
                ]);
            
            return [
                'success' => true,
                'mandate_id' => $mandateData['mandateid'],
                'status' => $mandateData['status'],
                'customer_refid' => $mandateData['customer_refid'],
                'subscription_refid' => $mandateData['subscription_refid'],
                'amount' => $mandateData['amount'],
                'amount_type' => $mandateData['amount_type'],
                'start_date' => $mandateData['start_date'],
                'end_date' => $mandateData['end_date'],
                'frequency' => $mandateData['frequency'],
                'payment_method' => $mandateData['payment_method_type'],
                'createdon' => $mandateData['createdon']
            ];
        } else {
            Log::error('Retrieve mandate failed: ' . json_encode($mandateData));
            return [
                'success' => false,
                'error' => $mandateData['verification_error_desc'] ?? 'Mandate retrieval failed'
            ];
        }
        
    } catch (\Exception $e) {
        Log::error('Retrieve Mandate Error: ' . $e->getMessage());
        return [
            'success' => false,
            'error' => $e->getMessage()
        ];
    }
}

public function generateRetrieveMandateJwsToken($mandateId)
{
    $secretKey = 'kCQhF9iLB5HhkcV7IIXfg9biJqpQ5BI4';
    $clientId = 'bduat2k447';
    
    // JWS Header
    $header = [
        "alg" => "HS256",
        "clientid" => $clientId,
    ];
    
    // Retrieve mandate payload as per Section 9.3
    $payload = [
        "mandateid" => $mandateId
    ];
    
    $headerEncoded = $this->base64urlEncode(json_encode($header));
    $payloadEncoded = $this->base64urlEncode(json_encode($payload));
    $message = $headerEncoded . '.' . $payloadEncoded;
    $signature = hash_hmac('sha256', $message, $secretKey, true);
    $signatureEncoded = $this->base64urlEncode($signature);
    
    return $headerEncoded . '.' . $payloadEncoded . '.' . $signatureEncoded;
}

public function retrieveMandateAPI($jwt)
{
    $url = 'https://uat1.billdesk.com/u2/pgsi/ve1_2/mandates/get';
    
    $timestamp = now()->format('YmdHis');
    $traceid = Str::random(18);
    
    $headers = [
        'content-type: application/jose',
        'accept: application/jose',
        'bd-timestamp: ' . $timestamp,
        'bd-traceid: ' . $traceid,
    ];
    
        Log::info('retrieveMandate function : Original encoded Retrieve Mandate API request string with 
        BD-TraceID=' . $traceid . ' & BD-Timestamp=' . $timestamp . ': ' . $jwt);

    
    $ch = curl_init();
    curl_setopt_array($ch, [
        CURLOPT_URL => $url,
        CURLOPT_RETURNTRANSFER => true,
        CURLOPT_ENCODING => '',
        CURLOPT_MAXREDIRS => 10,
        CURLOPT_TIMEOUT => 30,
        CURLOPT_FOLLOWLOCATION => true,
        CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
        CURLOPT_CUSTOMREQUEST => 'POST',
        CURLOPT_POSTFIELDS => $jwt,
        CURLOPT_HTTPHEADER => $headers,
    ]);
    
    $response = curl_exec($ch);
    $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
    curl_close($ch);
    
    if ($httpCode !== 200) {
        Log::error('Retrieve Mandate API HTTP Error: ' . $httpCode . ' - ' . $response);
        return null;
    }
    
    $responseData = $this->decode_jwt($response);
      Log::info('retrieveMandateAPI function : Original encoded Retrieve Mandate API response string: ' . $response);

    return $responseData;
}

// Controller method to retrieve mandate
public function getMandateDetails(Request $request)
{
    $mandateId = $request->mandate_id;
    
    $mandateResult = $this->retrieveMandate($mandateId);
    
    if ($mandateResult['success']) {
        return response()->json([
            'success' => true,
            'message' => 'Mandate retrieved successfully',
            'mandate' => $mandateResult
        ]);
    } else {
        return response()->json([
            'success' => false,
            'error' => $mandateResult['error']
        ], 500);
    }
}

/**
 * 5. RETRIEVE TRANSACTION (Section 9.5)
 */
public function retrieveTransaction(Request $request)
{

    $orderId = $request->orderId;
        $transactionId = $request->transactionId;
          $includeRefundDetails = $request->includeRefundDetails;

    try {
        if (!$orderId && !$transactionId) {
            throw new \Exception('Either orderId or transactionId is required');
        }
        
        $jwtToken = $this->generateRetrieveTransactionJwsToken($orderId, $transactionId, $includeRefundDetails);
        
        $transactionData = $this->retrieveTransactionAPI($jwtToken);
        
        if ($transactionData && isset($transactionData['transactionid'])) {
            // Update transaction details in database
            $updateData = [
                'auth_status' => $transactionData['auth_status'],
                'transaction_status' => $transactionData['transaction_error_type'] ?? 'success',
                'transaction_error_desc' => $transactionData['transaction_error_desc'] ?? null,
                'bank_ref_no' => $transactionData['bank_ref_no'] ?? null,
                'surcharge' => $transactionData['surcharge'] ?? '0.00',
                'discount' => $transactionData['discount'] ?? '0.00',
                'charge_amount' => $transactionData['charge_amount'],
                'updated_at' => now()
            ];
            
            // Add refund info if requested and available
            if ($includeRefundDetails && isset($transactionData['refundInfo'])) {
                $updateData['refund_info'] = json_encode($transactionData['refundInfo']);
            }
            
            if ($orderId) {
                DB::table('recurring_transactions')
                    ->where('order_id', $orderId)
                    ->update($updateData);
            } else {
                DB::table('recurring_transactions')
                    ->where('transaction_id', $transactionId)
                    ->update($updateData);
            }
            
            $response = [
                'success' => true,
                'transaction_id' => $transactionData['transactionid'],
                'order_id' => $transactionData['orderid'],
                'merchant_id' => $transactionData['mercid'],
                'amount' => $transactionData['amount'],
                'charge_amount' => $transactionData['charge_amount'],
                'currency' => $transactionData['currency'],
                'auth_status' => $transactionData['auth_status'],
                'transaction_status' => $transactionData['transaction_error_type'] ?? 'success',
                'transaction_error_desc' => $transactionData['transaction_error_desc'] ?? 'Transaction Successful',
                'bank_ref_no' => $transactionData['bank_ref_no'] ?? null,
                'transaction_date' => $transactionData['transaction_date'],
                'payment_method_type' => $transactionData['payment_method_type'] ?? 'upi',
                'mandateid' => $transactionData['mandateid'] ?? null
            ];
            
            // Add refund info if available
            if ($includeRefundDetails && isset($transactionData['refundInfo'])) {
                $response['refund_info'] = $transactionData['refundInfo'];
            }
            
            return $response;
        } else {
            Log::error('Retrieve transaction failed: ' . json_encode($transactionData));
            return [
                'success' => false,
                'error' => $transactionData['verification_error_desc'] ?? $transactionData['message'] ?? 'Transaction retrieval failed'
            ];
        }
        
    } catch (\Exception $e) {
        Log::error('Retrieve Transaction Error: ' . $e->getMessage());
        return [
            'success' => false,
            'error' => $e->getMessage()
        ];
    }
}

public function generateRetrieveTransactionJwsToken($orderId, $transactionId, $includeRefundDetails = false)
{
    $secretKey = 'kCQhF9iLB5HhkcV7IIXfg9biJqpQ5BI4';
    $clientId = 'bduat2k447';
    $merchantId = 'BDUAT2K447';
    
    // JWS Header
    $header = [
        "alg" => "HS256",
        "clientid" => $clientId,
    ];
    
    // Retrieve transaction payload as per Section 9.5
    $payload = [
        "mercid" => $merchantId,
          "refund_details" => "true"
    ];
  
    
    if ($orderId) {
        $payload["orderid"] = $orderId;
    }
    
    if ($transactionId) {
        $payload["transactionid"] = $transactionId;
    }
    
    if ($includeRefundDetails) {
        $payload["refund_details"] = "true";
    }
    
    $headerEncoded = $this->base64urlEncode(json_encode($header));
    $payloadEncoded = $this->base64urlEncode(json_encode($payload));
    $message = $headerEncoded . '.' . $payloadEncoded;
    $signature = hash_hmac('sha256', $message, $secretKey, true);
    $signatureEncoded = $this->base64urlEncode($signature);
    
    return $headerEncoded . '.' . $payloadEncoded . '.' . $signatureEncoded;
}

public function retrieveTransactionAPI($jwt)
{
    $url = 'https://uat1.billdesk.com/u2/payments/ve1_2/transactions/get';
    
    $timestamp = now()->format('YmdHis');
    $traceid = Str::random(18);
        Log::info('retrieveTransactionAPI timestamp: ' . $timestamp . ' traceid: ' . $traceid);

    $headers = [
        'content-type: application/jose',
        'accept: application/jose',
        'bd-timestamp: ' . $timestamp,
        'bd-traceid: ' . $traceid,
    ];
    
       Log::info('retrieveTransaction function : Original encoded Retrieve Transaction API request string with 
       BD-TraceID=' . $traceid . ' & BD-Timestamp=' . $timestamp . ': ' . $jwt);

    
    $ch = curl_init();
    curl_setopt_array($ch, [
        CURLOPT_URL => $url,
        CURLOPT_RETURNTRANSFER => true,
        CURLOPT_ENCODING => '',
        CURLOPT_MAXREDIRS => 10,
        CURLOPT_TIMEOUT => 30,
        CURLOPT_FOLLOWLOCATION => true,
        CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
        CURLOPT_CUSTOMREQUEST => 'POST',
        CURLOPT_POSTFIELDS => $jwt,
        CURLOPT_HTTPHEADER => $headers,
    ]);
    
    $response = curl_exec($ch);
    $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
    curl_close($ch);
    
    if ($httpCode !== 200) {
        Log::error('Retrieve Transaction API HTTP Error: ' . $httpCode . ' - ' . $response);
        return null;
    }
    
      Log::info('retrieveTransactionAPI function : Original encoded Retrieve Transaction API response string: ' . $response);

    $responseData = $this->decode_jwt($response);
    return $responseData;
}

// Controller method to retrieve transaction
public function getTransactionDetails(Request $request)
{
    $orderId = $request->order_id;
    $transactionId = $request->transaction_id;
    $includeRefundDetails = $request->boolean('include_refund', false);
    
        $transactionResult = $this->retrieveTransaction(
        $request->order_id,
        $request->transaction_id,
        $request->boolean('include_refund', false)
        );

    if ($transactionResult['success']) {
        return response()->json([
            'success' => true,
            'message' => 'Transaction retrieved successfully',
            'transaction' => $transactionResult
        ]);
    } else {
        return response()->json([
            'success' => false,
            'error' => $transactionResult['error']
        ], 500);
    }
}


}



  

