<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Str;
class dealerController extends Controller
{
     public function getOtp(Request $request)
    {
        $phone = trim($request->phone);

        if (!$phone) {
            return response()->json([
                'success' => false,
                'message' => 'Phone number is required'
            ]);
        }

        $dealer = DB::table('dealers')
            ->where('phone', $phone)
            ->first();

        if (!$dealer) {
            return response()->json([
                'success' => false,
                'message' => 'No dealer registered with this mobile number'
            ]);
        }

        $otp = rand(1000, 9999);

        DB::table('phone_otp')->updateOrInsert(
            ['phone' => $phone],
            [
                'otp' => $otp,
                'updated_at' => now()
            ]
        );

        return response()->json([
            'success' => true,
            'message' => 'OTP generated successfully',
            'otp' => $otp // remove in production
        ]);
    }

    public function verifyOtp(Request $request)
    {
        $phone = trim($request->phone);
        $otp = trim($request->otp);

        $otpRow = DB::table('phone_otp')
            ->where('phone', $phone)
            ->where('otp', $otp)
            ->first();

        if (!$otpRow) {
            return response()->json([
                'success' => false,
                'message' => 'Invalid OTP'
            ]);
        }

        $dealer = DB::table('dealers')
            ->where('phone', $phone)
            ->first();

        if (!$dealer) {
            return response()->json([
                'success' => false,
                'message' => 'Dealer not found'
            ]);
        }

        $token = hash('sha256', uniqid() . time() . rand());

        DB::table('dealers')
            ->where('id', $dealer->id)
            ->update([
                'auth_token' => $token
            ]);

        DB::table('phone_otp')
            ->where('phone', $phone)
            ->delete();

        return response()->json([
            'success' => true,
            'message' => 'Login successful',
            'auth_token' => $token,
            'dealer' => [
                'id' => $dealer->id,
                'name' => $dealer->name,
                'phone' => $dealer->phone
            ]
        ]);
    }

    public function updateMpin(Request $request)
    {
        $mpin = trim($request->mpin);

        if (!$mpin) {
            return response()->json([
                'success' => false,
                'message' => 'MPIN is required'
            ]);
        }

        DB::table('dealers')
            ->where('id', $request->dealer_id)
            ->update([
                'mpin' => $mpin
            ]);

        return response()->json([
            'success' => true,
            'message' => 'MPIN updated successfully'
        ]);
    }

    public function updateFirebase(Request $request)
    {
        $fcmToken = trim($request->fcm_token);

        if (!$fcmToken) {
            return response()->json([
                'success' => false,
                'message' => 'FCM token is required'
            ]);
        }

        DB::table('dealers')
            ->where('id', $request->dealer_id)
            ->update([
                'fcm_token' => $fcmToken
            ]);

        return response()->json([
            'success' => true,
            'message' => 'Firebase token updated successfully'
        ]);
    }

    public function profile(Request $request)
{
    $dealer = DB::table('dealers')
        ->where('id', $request->dealer_id)
        ->first();

    if (!$dealer) {
        return response()->json([
            'success' => false,
            'message' => 'Dealer not found'
        ]);
    }
$baseUrl = "http://127.0.0.1:8000/";
    return response()->json([
        'success' => true,
        'data' => [
            'id' => $dealer->id,
            'name' => $dealer->name,
            'shop_name' => $dealer->shop_name,
            'shop_address' => $dealer->shop_address,
            'dealer_photo' => $dealer->dealer_photo ? $baseUrl . $dealer->dealer_photo : null,
        'shop_photo' => $dealer->shop_photo ? $baseUrl . $dealer->shop_photo : null,
        'proof_front' => $dealer->proof_front ? $baseUrl . $dealer->proof_front : null,
        'proof_back' => $dealer->proof_back ? $baseUrl . $dealer->proof_back : null,
            'mpin_set' => !empty($dealer->mpin),
            'status' => $dealer->status,
            'district' => json_decode($dealer->district, true),
            'pan_no' => $dealer->pan_no,
            'created_at' => $dealer->created_at
        ]
    ]);
}

   public function newLeads(Request $request)
{
    $dealer = DB::table('dealers')
        ->where('id', $request->dealer_id)
        ->first();

    if (!$dealer) {
        return response()->json([
            'success' => false,
            'message' => 'Dealer not found'
        ]);
    }

    $districtIds = json_decode($dealer->district, true);

    if (!is_array($districtIds) || empty($districtIds)) {
        return response()->json([
            'success' => true,
            'count' => 0,
            'data' => []
        ]);
    }

    $orders = DB::table('orders as o')
        ->join('order_items as oi', 'o.order_id', '=', 'oi.order_id')
        ->join('model as m', 'm.id', '=', 'oi.model_id')
        ->join('users as u', 'u.id', '=', 'oi.user_id')
        ->where('o.bidding_status', 'pending')
        ->where('o.status', 'pending')
        ->whereIn('o.district_id', $districtIds)
        ->select(
            'u.name as customer_name',
            'm.title as model_name',
            'o.order_id',
            'o.mobile_no',
            'o.alternate_mob_no',
            'o.address',
            'o.landmark',
            'o.pincode',
            'o.area_name',
            'o.state',
            'o.status',
            'o.bidding_status',
            'o.shipping_pickup_date',
            'o.shipping_pickup_time',
            'oi.capacity',
            'oi.price',
            'oi.item_name'
        )
        ->orderByDesc('o.id')
        ->get();

    $response = [];

    foreach ($orders as $order) {

        $qa = [];
        $reportedIssues = [];

        // First decode
        $rootData = json_decode($order->item_name, true);

        // Second decode (actual QA data)
        $itemData = [];

        if (
            is_array($rootData) &&
            isset($rootData['item_name'])
        ) {
            $itemData = json_decode($rootData['item_name'], true);
        }

        // QA Details
        if (!empty($itemData['qa_details'])) {

            foreach ($itemData['qa_details'] as $q) {

                $qa[] = [
                    'question' => $q['question_name'] ?? '',
                    'answer'   => $q['original_answer'] ?? ''
                ];
            }
        }

        // Reported Issues
        if (!empty($itemData['yes_question_details'])) {

            foreach ($itemData['yes_question_details'] as $issue) {

                if (!empty($issue['label'])) {
                    $reportedIssues[] = $issue['label'];
                }
            }
        }

        $response[] = [
            'order_id' => $order->order_id,
            'customer_name' => $order->customer_name,
            'model_name' => $order->model_name,
            'mobile_no' => $order->mobile_no,
            'alternate_mob_no' => $order->alternate_mob_no,
            'address' => $order->address,
            'landmark' => $order->landmark,
            'pincode' => $order->pincode,
            'area_name' => $order->area_name,
            'state' => $order->state,
            'status' => $order->status,
            'bidding_status' => $order->bidding_status,
            'shipping_pickup_date' => $order->shipping_pickup_date,
            'shipping_pickup_time' => $order->shipping_pickup_time,
            'capacity' => $order->capacity,
            'price' => $order->price,

            'qa' => $qa,
            'reported_issues' => $reportedIssues
        ];
    }

    return response()->json([
        'success' => true,
        'count' => count($response),
        'data' => $response
    ]);
}

public function liveLeads(Request $request)
{
    $dealer = DB::table('dealers')
        ->where('id', $request->dealer_id)
        ->first();

    if (!$dealer) {
        return response()->json([
            'success' => false,
            'message' => 'Dealer not found'
        ]);
    }

    $orders = DB::table('orders as o')
        ->join('order_items as oi', 'o.order_id', '=', 'oi.order_id')
        ->join('model as m', 'm.id', '=', 'oi.model_id')
        ->join('users as u', 'u.id', '=', 'oi.user_id')
        ->where('o.bidding_status', 'completed')
        ->where('o.status', 'pending')
        ->where('o.dealer_id', $request->dealer_id)
        ->select(
            'u.name as customer_name',
            'm.title as model_name',
            'o.order_id',
            'o.mobile_no',
            'o.alternate_mob_no',
            'o.address',
            'o.landmark',
            'o.pincode',
            'o.area_name',
            'o.state',
            'o.status',
            'o.bidding_status',
            'o.shipping_pickup_date',
            'o.shipping_pickup_time',
            'oi.capacity',
            'oi.price',
            'oi.item_name'
        )
        ->orderByDesc('o.id')
        ->get();

    $response = [];

    foreach ($orders as $order) {

        $qa = [];
        $reportedIssues = [];

        $rootData = json_decode($order->item_name, true);

        $itemData = [];

        if (
            is_array($rootData) &&
            isset($rootData['item_name'])
        ) {
            $itemData = json_decode($rootData['item_name'], true);
        }

        if (!empty($itemData['qa_details'])) {
            foreach ($itemData['qa_details'] as $q) {
                $qa[] = [
                    'question' => $q['question_name'] ?? '',
                    'answer'   => $q['original_answer'] ?? ''
                ];
            }
        }

        if (!empty($itemData['yes_question_details'])) {
            foreach ($itemData['yes_question_details'] as $issue) {
                if (!empty($issue['label'])) {
                    $reportedIssues[] = $issue['label'];
                }
            }
        }

        $response[] = [
            'order_id' => $order->order_id,
            'customer_name' => $order->customer_name,
            'model_name' => $order->model_name,
            'mobile_no' => $order->mobile_no,
            'alternate_mob_no' => $order->alternate_mob_no,
            'address' => $order->address,
            'landmark' => $order->landmark,
            'pincode' => $order->pincode,
            'area_name' => $order->area_name,
            'state' => $order->state,
            'status' => $order->status,
            'bidding_status' => $order->bidding_status,
            'shipping_pickup_date' => $order->shipping_pickup_date,
            'shipping_pickup_time' => $order->shipping_pickup_time,
            'capacity' => $order->capacity,
            'price' => $order->price,
            'qa' => $qa,
            'reported_issues' => $reportedIssues
        ];
    }

    return response()->json([
        'success' => true,
        'count' => count($response),
        'data' => $response
    ]);
}

public function history_leads(Request $request)
{
    $dealer = DB::table('dealers')
        ->where('id', $request->dealer_id)
        ->first();

    if (!$dealer) {
        return response()->json([
            'success' => false,
            'message' => 'Dealer not found'
        ]);
    }

    $orders = DB::table('orders as o')
        ->join('order_items as oi', 'o.order_id', '=', 'oi.order_id')
        ->join('model as m', 'm.id', '=', 'oi.model_id')
        ->join('users as u', 'u.id', '=', 'oi.user_id')
        ->where('o.bidding_status', 'completed')
        ->whereIn('o.status', ['completed', 'cancelled'])
        ->where('o.dealer_id', $request->dealer_id)
        ->select(
            'u.name as customer_name',
            'm.title as model_name',
            'o.order_id',
            'o.mobile_no',
            'o.alternate_mob_no',
            'o.address',
            'o.landmark',
            'o.pincode',
            'o.area_name',
            'o.state',
            'o.status',
            'o.bidding_status',
            'o.shipping_pickup_date',
            'o.shipping_pickup_time',
            'oi.capacity',
            'oi.price',
            'oi.item_name'
        )
        ->orderByDesc('o.id')
        ->get();

    $response = [];

    foreach ($orders as $order) {

        $qa = [];
        $reportedIssues = [];

        $rootData = json_decode($order->item_name, true);

        $itemData = [];

        if (
            is_array($rootData) &&
            isset($rootData['item_name'])
        ) {
            $itemData = json_decode($rootData['item_name'], true);
        }

        if (!empty($itemData['qa_details'])) {
            foreach ($itemData['qa_details'] as $q) {
                $qa[] = [
                    'question' => $q['question_name'] ?? '',
                    'answer'   => $q['original_answer'] ?? ''
                ];
            }
        }

        if (!empty($itemData['yes_question_details'])) {
            foreach ($itemData['yes_question_details'] as $issue) {
                if (!empty($issue['label'])) {
                    $reportedIssues[] = $issue['label'];
                }
            }
        }

        $response[] = [
            'order_id' => $order->order_id,
            'customer_name' => $order->customer_name,
            'model_name' => $order->model_name,
            'mobile_no' => $order->mobile_no,
            'alternate_mob_no' => $order->alternate_mob_no,
            'address' => $order->address,
            'landmark' => $order->landmark,
            'pincode' => $order->pincode,
            'area_name' => $order->area_name,
            'state' => $order->state,
            'status' => $order->status,
            'bidding_status' => $order->bidding_status,
            'shipping_pickup_date' => $order->shipping_pickup_date,
            'shipping_pickup_time' => $order->shipping_pickup_time,
            'capacity' => $order->capacity,
            'price' => $order->price,
            'qa' => $qa,
            'reported_issues' => $reportedIssues
        ];
    }

    return response()->json([
        'success' => true,
        'count' => count($response),
        'data' => $response
    ]);
}

public function placeBid(Request $request)
{
    $orderId = $request->order_id;
    $dealerId = $request->dealer_id; // from middleware
    $percentage = (float)$request->percentage;

    if (!$orderId || !$percentage) {
        return response()->json([
            'success' => false,
            'message' => 'Order ID and percentage are required'
        ]);
    }

    DB::beginTransaction();

    try {

        // Dealer
        $dealer = DB::table('dealers')
            ->where('id', $dealerId)
            ->first();

        if (!$dealer) {
            return response()->json([
                'success' => false,
                'message' => 'Dealer not found'
            ]);
        }

        // Order
        $order = DB::table('orders')
            ->where('order_id', $orderId)
            ->first();

        if (!$order) {
            return response()->json([
                'success' => false,
                'message' => 'Invalid order'
            ]);
        }

        // District Validation
        $dealerDistricts = json_decode($dealer->district, true);

        if (!in_array($order->district_id, $dealerDistricts)) {
            return response()->json([
                'success' => false,
                'message' => 'This lead does not belong to your district'
            ]);
        }

        // Bidding Config
        $biddingConfig = DB::table('bidding')
            ->where('district_id', $order->district_id)
            ->first();

        if (!$biddingConfig) {
            return response()->json([
                'success' => false,
                'message' => 'Bidding configuration not found'
            ]);
        }

        // Time Validation
        $currentTime = date('H:i:s');

        if (
            $currentTime < $biddingConfig->bid_time_start ||
            $currentTime > $biddingConfig->bid_time_end
        ) {
            return response()->json([
                'success' => false,
                'message' => 'Bidding time expired'
            ]);
        }

        // Percentage Validation
        if (
            $percentage < $biddingConfig->bid_min_perc ||
            $percentage > $biddingConfig->bid_max_perc
        ) {
            return response()->json([
                'success' => false,
                'message' => 'Bid percentage must be between '
                    . $biddingConfig->bid_min_perc . '% and '
                    . $biddingConfig->bid_max_perc . '%'
            ]);
        }

        // Order Price
        $orderItem = DB::table('order_items')
            ->where('order_id', $orderId)
            ->first();

        if (!$orderItem) {
            return response()->json([
                'success' => false,
                'message' => 'Order item not found'
            ]);
        }

        $biddingData = [];

        if (!empty($order->bidding)) {
            $biddingData = json_decode($order->bidding, true);
        }

        // Current Highest Bid
        $currentHighest = 0;
        $currentWinner = null;

        foreach ($biddingData as $bidDealerId => $bidInfo) {

            if (($bidInfo['percent'] ?? 0) > $currentHighest) {
                $currentHighest = $bidInfo['percent'];
                $currentWinner = $bidDealerId;
            }
        }

        // Must be greater than current highest
        if ($percentage <= $currentHighest) {
            return response()->json([
                'success' => false,
                'message' => 'Bid must be greater than current highest bid (' . $currentHighest . '%)'
            ]);
        }

        // ---------------------------------
        // REFUND PREVIOUS WINNER
        // ---------------------------------
        if ($currentWinner) {

            $refundAmount = round(
                ($orderItem->price * $currentHighest) / 100,
                2
            );

            $winnerWallet = DB::table('dealer_accounts')
                ->where('dealer_id', $currentWinner)
                ->orderByDesc('id')
                ->first();

            $winnerBalance = $winnerWallet->current_balance ?? 0;

            DB::table('dealer_accounts')->insert([
                'dealer_id' => $currentWinner,
                'order_id' => $orderId,
                'amount' => $refundAmount,
                'current_balance' => $winnerBalance + $refundAmount,
                'credit_or_debit' => 'credit',
                'remarks' => 'Refund for order bidding ' . $orderId,
                'created_at' => now(),
                'updated_at' => now()
            ]);
        }

        // ---------------------------------
        // CHARGE NEW WINNER
        // ---------------------------------
        $commission = round(
            ($orderItem->price * $percentage) / 100,
            2
        );

        $dealerWallet = DB::table('dealer_accounts')
            ->where('dealer_id', $dealerId)
            ->orderByDesc('id')
            ->first();

        $dealerBalance = $dealerWallet->current_balance ?? 0;

        if ($dealerBalance < $commission) {
            return response()->json([
                'success' => false,
                'message' => 'Insufficient wallet balance'
            ]);
        }

        $newBalance = $dealerBalance - $commission;

        DB::table('dealer_accounts')->insert([
            'dealer_id' => $dealerId,
            'order_id' => $orderId,
            'amount' => $commission,
            'current_balance' => $newBalance,
            'credit_or_debit' => 'debit',
            'remarks' => 'Order bidding ' . $orderId,
            'created_at' => now(),
            'updated_at' => now()
        ]);

        // ---------------------------------
        // UPDATE BIDDING JSON
        // ---------------------------------
        foreach ($biddingData as $id => $bid) {
            $biddingData[$id]['color'] = 'red';
        }

        $biddingData[$dealerId] = [
            'percent' => $percentage,
            'color' => 'green'
        ];

        DB::table('orders')
            ->where('order_id', $orderId)
            ->update([
                'dealer_id' => $dealerId,
                'bidding' => json_encode($biddingData),
                'bidding_status' => 'completed',
                'updated_at' => now()
            ]);

        DB::commit();

        return response()->json([
            'success' => true,
            'message' => 'Bid placed successfully',
            'data' => [
                'order_id' => $orderId,
                'percentage' => $percentage,
                'commission' => $commission,
                'current_balance' => $newBalance
            ]
        ]);

    } catch (\Exception $e) {

        DB::rollBack();

        return response()->json([
            'success' => false,
            'message' => $e->getMessage()
        ]);
    }
}

public function createDealerStock(Request $request) 
    {
        
        // 1. Validate incoming parameters cleanly
        $validator = Validator::make($request->all(), [
            'model_id'     => 'required|exists:model,id',
            'capacity'     => 'required|string|max:100',
            'buy_price'    => 'required|numeric|min:0',
            'color'        => 'required|string|max:100',
            'imei_no_1'    => 'required|string|max:50|unique:stocks,imei_no_1',
            'imei_no_2'    => 'nullable|string|max:50',
            'warranty'     => 'required|string|max:255',
            'media'        => 'required|array|min:1',
            'media.*'      => 'file|mimes:jpeg,png,jpg,gif,webp,mp4,mov,avi,webm|max:20480' // Max 20MB supporting videos
        ]);

        if ($validator->fails()) {
            return response()->json([
                'status'  => 'error',
                'message' => $validator->errors()->first(),
                'errors'  => $validator->errors()
            ], 422);
        }

        // 2. Extract context attributes safely injected by your middleware layer
        $dealerId = $request->input('dealer_id');
        if (!$dealerId) {
            return response()->json(['status' => 'error', 'message' => 'Unauthorized dealer context mapping.'], 401);
        }

        // 3. Retrieve model information to engineer premium SEO properties
        $model = DB::table('model')->where('id', $request->model_id)->first();
        if (!$model) {
            return response()->json(['status' => 'error', 'message' => 'Target phone model variants not found.'], 404);
        }

        try {
            // 4. File uploads collection execution loop pipeline framework
            $uploadedMediaPaths = [];
            if ($request->hasFile('media')) {
                foreach ($request->file('media') as $file) {
                    $uniquePrefix = time() . '_' . uniqid();
                    $fileName = $uniquePrefix . '.' . $file->getClientOriginalExtension();
                    
                    // Moves resource to public/media/images/stock/ structure path rules
                    $file->move(public_path('media/images/stock'), $fileName);
                    
                    // Match requested structured slash output format string path arrays
                    $uploadedMediaPaths[] = "media/images/stock/" . $fileName;
                }
            }

            // 5. Build dynamic programmatic smart SEO configurations framework 
            $modelTitle = $model->title;
            $variantInfo = $request->capacity . ' (' . Str::title($request->color) . ')';
            
            $metaTitle = "Best Refurbished " . $modelTitle . " " . $variantInfo . " | Certified Pre-Owned";
            $metaDescription = "Buy a 100% certified refurbished " . $modelTitle . " " . $variantInfo . " with " . $request->warranty . " warranty. Fully tested, secure diagnostic logs verified, and ready to dispatch today at a fraction of retail pricing!";
            $metaKeywords = "refurbished " . $modelTitle . ", buy second hand " . $modelTitle . ", " . $modelTitle . " " . $request->capacity . ", certified preowned phones, discount smart phones";
            $canonicalUrl = url('/refurbished-phones/' . Str::slug($modelTitle . '-' . $variantInfo . '-' . $request->imei_no_1));

            // Generate clean structural rich product schema markdown properties
            $schemaData = [
                "@context"    => "https://schema.org/",
                "@type"       => "Product",
                "name"        => "Certified Refurbished " . $modelTitle . " " . $variantInfo,
                "description" => $metaDescription,
                "offers"      => [
                    "@type"         => "Offer",
                    "priceCurrency" => "INR",
                    "price"         => $request->buy_price,
                    "itemCondition" => "https://schema.org/RefurbishedCondition",
                    "availability"  => "https://schema.org/InStock"
                ]
            ];

            // Unique tracking receipt generation identifier logic matrix parameters
            $orderId = 'ORD' . random_int(1000000000, 9999999999);

            // 6. DB Core payload creation operation tracking layout blocks
            DB::table('stocks')->insert([
                'order_id'            => $orderId,
                'user_id'             => 0, // Fallback placeholder defaults
                'dealer_id'           => $dealerId,
                'bidding'             => json_encode([]),
                'model_id'            => $request->model_id,
                'capacity'            => $request->capacity,
                'buy_price'           => $request->buy_price,
                'sell_price'          => 0.00, // Left tracking zero for admin to set and override
                'color'               => $request->color,
                'imei_no_1'           => $request->imei_no_1,
                'imei_no_2'           => $request->imei_no_2,
                'warranty'            => $request->warranty,
                'profit_percent_user' => 0,
                'profit_perc_vendor'  => 0,
                'profit'              => 0.00,
                'status'              => 'pending',
                'payment_status'      => 'unpaid',
                'is_approved'         => 0, // Pending validation by default parameters
                'purchase_date'       => now()->toDateString(),
                'meta_title'          => $metaTitle,
                'meta_description'    => $metaDescription,
                'meta_keywords'       => $metaKeywords,
                'canonical_url'       => $canonicalUrl,
                'schema_data'         => json_encode($schemaData),
                'media'               => json_encode($uploadedMediaPaths), // JSON String conversion outputs array list
                'meta_robots'         => 'index, follow',
                'created_at'          => now(),
                'updated_at'          => now()
            ]);

            return response()->json([
                'status'  => 'success',
                'message' => 'Stock inventory entry successfully captured! Awaiting administrator approval routing.',
                'data'    => [
                    'order_id' => $orderId
                ]
            ], 201);

        } catch (\Exception $e) {
            return response()->json([
                'status'  => 'error',
                'message' => 'System execution tracking fault failure processing creation payload.',
                'error'   => $e->getMessage()
            ], 500);
        }
    }

    public function editDealerStock(Request $request) 
    {
        $validator = Validator::make($request->all(), [
            'order_id'   => 'required|string|exists:stocks,order_id',
            'model_id'   => 'required|exists:model,id',
            'capacity'   => 'required|string|max:100',
            'buy_price'  => 'required|numeric|min:0',
            'color'      => 'required|string|max:100',
            'imei_no_1'  => 'required|string|max:50',
            'imei_no_2'  => 'nullable|string|max:50',
            'warranty'   => 'required|string|max:255',
            'media'      => 'nullable|array', // Optional: only send if updating media
            'media.*'    => 'file|mimes:jpeg,png,jpg,gif,webp,mp4,mov,avi,webm|max:20480'
        ]);

        if ($validator->fails()) {
            return response()->json(['status' => 'error', 'message' => $validator->errors()->first()], 422);
        }

        $dealerId = $request->input('dealer_id');

        // Locate the target stock row matching both order_id and dealer_id
        $stock = DB::table('stocks')
            ->where('order_id', $request->order_id)
            ->where('dealer_id', $dealerId)
            ->first();

        if (!$stock) {
            return response()->json(['status' => 'error', 'message' => 'Stock record not found or unauthorized.'], 404);
        }

        // CRITICAL CHECK: Block updates if already approved by admin
        if ((int)$stock->is_approved === 1) {
            return response()->json(['status' => 'error', 'message' => 'Approved stock records are locked and cannot be edited.'], 403);
        }

        // Ensure IMEI is unique to other rows (excluding itself)
        $imeiCheck = DB::table('stocks')
            ->where('imei_no_1', $request->imei_no_1)
            ->where('id', '!=', $stock->id)
            ->exists();
            
        if ($imeiCheck) {
            return response()->json(['status' => 'error', 'message' => 'The IMEI number 1 has already been registered.'], 422);
        }

        try {
            $model = DB::table('model')->where('id', $request->model_id)->first();
            
            // Handle media update if new files are provided
            if ($request->hasFile('media')) {
                // Delete older files from local directory storage
                $oldMedia = json_decode($stock->media, true) ?? [];
                foreach ($oldMedia as $oldPath) {
                    $absolutePath = public_path($oldPath);
                    if (File::exists($absolutePath)) {
                        File::delete($absolutePath);
                    }
                }

                // Upload new incoming media replacement assets
                $uploadedMediaPaths = [];
                foreach ($request->file('media') as $file) {
                    $fileName = time() . '_' . uniqid() . '.' . $file->getClientOriginalExtension();
                    $file->move(public_path('media/images/stock'), $fileName);
                    $uploadedMediaPaths[] = "media/images/stock/" . $fileName;
                }
                $mediaPayload = json_encode($uploadedMediaPaths);
            } else {
                // Keep the existing media files if nothing new was uploaded
                $mediaPayload = $stock->media;
            }

            // Regenerate the smart optimized SEO properties
            $variantInfo = $request->capacity . ' (' . Str::title($request->color) . ')';
            $metaTitle = "Best Refurbished " . $model->title . " " . $variantInfo . " | Certified Pre-Owned";
            $metaDescription = "Buy a 100% certified refurbished " . $model->title . " " . $variantInfo . " with " . $request->warranty . " warranty.";

            // Save updates to database row
            DB::table('stocks')
                ->where('id', $stock->id)
                ->update([
                    'model_id'         => $request->model_id,
                    'capacity'         => $request->capacity,
                    'buy_price'        => $request->buy_price,
                    'color'            => $request->color,
                    'imei_no_1'        => $request->imei_no_1,
                    'imei_no_2'        => $request->imei_no_2,
                    'warranty'         => $request->warranty,
                    'media'            => $mediaPayload,
                    'meta_title'       => $metaTitle,
                    'meta_description' => $metaDescription,
                    'updated_at'       => now()
                ]);

            return response()->json(['status' => 'success', 'message' => 'Stock record successfully updated!']);

        } catch (\Exception $e) {
            return response()->json(['status' => 'error', 'message' => $e->getMessage()], 500);
        }
    }

    // ==========================================
    // API: DELETE DEALER STOCK (With Local Files)
    // ==========================================
    public function deleteDealerStock(Request $request) 
    {
        $validator = Validator::make($request->all(), [
            'order_id' => 'required|string|exists:stocks,order_id'
        ]);

        if ($validator->fails()) {
            return response()->json(['status' => 'error', 'message' => $validator->errors()->first()], 422);
        }

        $dealerId = $request->input('dealer_id');

        // Locate and confirm row context ownership
        $stock = DB::table('stocks')
            ->where('order_id', $request->order_id)
            ->where('dealer_id', $dealerId)
            ->first();

        if (!$stock) {
            return response()->json(['status' => 'error', 'message' => 'Stock record not found or unauthorized.'], 404);
        }

        try {
            // 1. Loop through and delete physical media files from server directory storage
            $mediaFiles = json_decode($stock->media, true) ?? [];
            foreach ($mediaFiles as $filePath) {
                $absolutePath = public_path($filePath);
                if (File::exists($absolutePath)) {
                    File::delete($absolutePath);
                }
            }

            // 2. Clear database row entry
            DB::table('stocks')->where('id', $stock->id)->delete();

            return response()->json(['status' => 'success', 'message' => 'Stock listing and all related media permanently deleted.']);

        } catch (\Exception $e) {
            return response()->json(['status' => 'error', 'message' => $e->getMessage()], 500);
        }
    }

    public function listDealerStock(Request $request) 
    {
        $dealerId = $request->input('dealer_id');

        try {
            // Fetch stocks with pagination and join model/brand names
            $stocks = DB::table('stocks')
                ->join('model', 'stocks.model_id', '=', 'model.id')
                ->leftJoin('brand', 'model.brand_id', '=', 'brand.id')
                ->where('stocks.dealer_id', $dealerId)
                ->select(
                    'stocks.id',
                    'stocks.order_id',
                    'stocks.model_id',
                    'brand.title as brand_title',
                    'model.title as model_title',
                    'stocks.capacity',
                    'stocks.color',
                    'stocks.buy_price',
                    'stocks.sell_price',
                    'stocks.imei_no_1',
                    'stocks.imei_no_2',
                    'stocks.warranty',
                    'stocks.status',
                    'stocks.is_approved',
                    'stocks.media',
                    'stocks.created_at'
                )
                ->orderBy('stocks.id', 'desc')
                ->paginate(15); // Adjust this number based on your frontend needs

            // Decode the media JSON string back into a workable array for the frontend
            foreach ($stocks->items() as $stock) {
                $stock->media = json_decode($stock->media, true) ?? [];
            }

            return response()->json([
                'status'  => 'success',
                'message' => 'Dealer stock inventory retrieved successfully.',
                'data'    => $stocks
            ], 200);

        } catch (\Exception $e) {
            return response()->json([
                'status'  => 'error',
                'message' => 'Failed to retrieve stock listings.',
                'error'   => $e->getMessage()
            ], 500);
        }
    }
}