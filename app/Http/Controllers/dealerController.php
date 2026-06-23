<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

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
}