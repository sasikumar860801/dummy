<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Session;
use Carbon\Carbon;
class AdminController extends Controller
{
    public function showLogin()
    {
        if (Session::has('admin_id')) {
            return redirect()->route('admin.dashboard');
        }
        return view('admin.login');
    }

    public function login(Request $request)
    {
        $request->validate([
            'username' => 'required',
            'password' => 'required'
        ]);

        $username = $request->input('username');
        $password = $request->input('password');

        // Look up plain-text credentials natively matching structural configurations
        $admin = DB::table('admin')
            ->where('username', $username)
            ->where('password', $password)
            ->first();

        if ($admin) {
            Session::put('admin_id', $admin->id);
            // Dynamic check if column name maps to 'name' or fallback to 'username' string values
            Session::put('admin_name', $admin->name ?? $admin->username);
            
            return redirect()->route('admin.dashboard')->with('success', 'Welcome to Control Hub Console Management.');
        }

        return back()->withInput()->with('error', 'Invalid Management Credentials Provided.');
    }

    public function dashboard()
    {
        return view('admin.dashboard');
    }

    public function logout()
    {
        Session::forget(['admin_id', 'admin_name']);
        return redirect()->route('admin.login')->with('success', 'Logged out safely.');
    }

    public function index()
    {
        
        // 1. Compile Live Header Status Aggregates via Scalar Queries
        $counts = [
            'pending'   => DB::table('orders')->where('status', 'pending')->count(),
            'reject'    => DB::table('orders')->where('status', 'reject')->count(),
            'cancelled' => DB::table('orders')->where('status', 'cancelled')->count(),
            'completed' => DB::table('orders')->where('status', 'completed')->count(),
        ];

        // 2. Load Core Unified Matrix dataset with clean inner left join relationships
        $orders = DB::table('orders')
            ->join('order_items', 'orders.order_id', '=', 'order_items.order_id')
            ->join('model', 'orders.model_id', '=', 'model.id')
            ->select(
                'orders.*',
                'order_items.price as item_price',
                'order_items.capacity as item_capacity',
                'order_items.item_name',
                'model.model_img',
                'model.title as model_title',
                // Subquery to verify if a stock matching this order_id has already been provisionsed
                DB::raw('(SELECT COUNT(1) FROM stocks WHERE stocks.order_id = orders.order_id) as exists_in_stock')
            )
            ->orderBy('orders.id', 'desc')
            ->get()
            ->groupBy('status');

        return view('admin.orders', compact('orders', 'counts'));
    }

    public function updateStatus(Request $request)
    {
        $request->validate([
            'order_id' => 'required',
            'status'   => 'required|in:completed,cancelled'
        ]);

        $orderId = $request->input('order_id');
        $status = $request->input('status');

        DB::transaction(function () use ($orderId, $status) {
            DB::table('orders')->where('order_id', $orderId)->update(['status' => $status]);
            DB::table('order_items')->where('order_id', $orderId)->update(['status' => $status, 'order_status' => $status]);
        });

        return response()->json(['success' => true, 'message' => "Order state marked as {$status} securely."]);
    }

    public function moveToStock(Request $request)
    {
        $request->validate([
            'order_id' => 'required',
            'model_id' => 'required',
            'capacity' => 'required',
            'buy_price' => 'required|numeric',
            'color'    => 'required',
            'imei_no_1'=> 'required',
            'warranty' => 'required'
        ]);

        $orderId = $request->input('order_id');

        // Check if stock record exists for this order
        $exists = DB::table('stocks')->where('order_id', $orderId)->exists();
        if ($exists) {
            return response()->json(['success' => false, 'message' => 'This device asset is already indexed inside stock vaults.'], 422);
        }

        // Calculate profit based on buy_price
        $buyPrice = (float)$request->input('buy_price');
        $profitUser = ($buyPrice * 20) / 100;

        DB::table('stocks')->insert([
            'order_id'            => $orderId,
            'user_id'             => DB::table('orders')->where('order_id', $orderId)->value('user_id'),
            'vendor_id'           => DB::table('order_items')->where('order_id', $orderId)->value('vendor_id'),
            'model_id'            => $request->input('model_id'),
            'capacity'            => $request->input('capacity'),
            'buy_price'           => $buyPrice,
            'sell_price'          => 0.00,
            'color'               => $request->input('color'),
            'imei_no_1'           => $request->input('imei_no_1'),
            'imei_no_2'           => $request->input('imei_no_2'),
            'warranty'            => $request->input('warranty'),
            'profit_percent_user' => 20,
            'profit_perc_vendor'  => 5,
            'profit'              => $profitUser,
            'status'              => 'pending',
            'payment_status'      => 'pending',
            'purchase_date'       => Carbon::now()->toDateString(),
            'created_at'          => Carbon::now(),
            'updated_at'          => Carbon::now()
        ]);

        return response()->json(['success' => true, 'message' => 'Asset inventory parameters securely provisioned inside global stock record sheets.']);
    }

    public function stock_index()
    {
        // Live base query with product schema properties
        $baseQuery = DB::table('stocks')
            ->join('model', 'stocks.model_id', '=', 'model.id')
            ->select('stocks.*', 'model.title as model_title', 'model.model_img');

        // Segment collections according to your precise rules
        $newStock = (clone $baseQuery)
            ->where('stocks.status', 'pending')
            ->where(function($q) { $q->whereNull('stocks.user_id')->orWhere('stocks.user_id', 0); })
            ->where(function($q) { $q->whereNull('stocks.vendor_id')->orWhere('stocks.vendor_id', 0); })
            ->orderBy('stocks.id', 'desc')->get();

        $assignedStock = (clone $baseQuery)
            ->where('stocks.status', 'pending')
            ->whereNotNull('stocks.user_id')
            ->where('stocks.user_id', '!=', 0)
            ->where(function($q) { $q->whereNull('stocks.vendor_id')->orWhere('stocks.vendor_id', 0); })
            ->orderBy('stocks.id', 'desc')->get();

        $completedStock = (clone $baseQuery)
            ->where('stocks.status', 'completed')
            ->orderBy('stocks.id', 'desc')->get();

        return view('admin.stock', compact('newStock', 'assignedStock', 'completedStock'));
    }

    public function searchModels(Request $request)
    {
        $term = $request->get('q');
        $models = DB::table('model')
            ->where('title', 'LIKE', '%' . $term . '%')
            ->select('id', 'title as text')
            ->limit(20)
            ->get();
            
        return response()->json(['results' => $models]);
    }

    public function store(Request $request)
    {
        $request->validate([
            'order_id' => 'required',
            'model_id' => 'required',
            'capacity' => 'required',
            'buy_price' => 'required|numeric',
            'color'    => 'required',
            'imei_no_1'=> 'required',
            'warranty' => 'required'
        ]);

        $buyPrice = (float)$request->input('buy_price');
        $pUserPercent = (int)($request->input('profit_percent_user') ?? 20);
        $profit = ($buyPrice * $pUserPercent) / 100;

        DB::table('stocks')->insert([
            'order_id'            => $request->input('order_id'),
            'user_id'             => 0,
            'vendor_id'           => 0,
            'model_id'            => $request->input('model_id'),
            'capacity'            => $request->input('capacity'),
            'buy_price'           => $buyPrice,
            'sell_price'          => 0.00,
            'color'               => $request->input('color'),
            'imei_no_1'           => $request->input('imei_no_1'),
            'imei_no_2'           => $request->input('imei_no_2'),
            'warranty'            => $request->input('warranty'),
            'profit_percent_user' => $pUserPercent,
            'profit_perc_vendor'  => (int)($request->input('profit_perc_vendor') ?? 5),
            'profit'              => $profit,
            'status'              => 'pending',
            'payment_status'      => 'pending',
            'purchase_date'       => Carbon::now()->toDateString(),
            'created_at'          => Carbon::now(),
            'updated_at'          => Carbon::now()
        ]);

        return response()->json(['success' => true, 'message' => 'Manual asset entry saved successfully.']);
    }

    public function update(Request $request)
    {
        $request->validate([
            'id' => 'required',
            'capacity' => 'required',
            'buy_price' => 'required|numeric',
            'color' => 'required',
            'imei_no_1' => 'required',
            'warranty' => 'required'
        ]);

        $id = $request->input('id');
        $buyPrice = (float)$request->input('buy_price');
        $pUserPercent = (int)$request->input('profit_percent_user');
        $profit = ($buyPrice * $pUserPercent) / 100;

        DB::table('stocks')->where('id', $id)->update([
            'capacity' => $request->input('capacity'),
            'buy_price' => $buyPrice,
            'color' => $request->input('color'),
            'imei_no_1' => $request->input('imei_no_1'),
            'imei_no_2' => $request->input('imei_no_2'),
            'warranty' => $request->input('warranty'),
            'profit_percent_user' => $pUserPercent,
            'profit_perc_vendor' => (int)$request->input('profit_perc_vendor'),
            'profit' => $profit,
            'updated_at' => Carbon::now()
        ]);

        return response()->json(['success' => true, 'message' => 'Stock metadata updated successfully.']);
    }

    public function destroy(Request $request)
    {
        DB::table('stocks')->where('id', $request->input('id'))->delete();
        return response()->json(['success' => true, 'message' => 'Stock item permanently purged from system logs.']);
    }

    public function updateAssignment(Request $request)
    {
        $id = $request->input('id');
        $action = $request->input('action'); // 'unassign' or 'complete'

        if ($action === 'unassign') {
            DB::table('stocks')->where('id', $id)->update([
                'user_id' => 0,
                'vendor_id' => 0,
                'updated_at' => Carbon::now()
            ]);
            return response()->json(['success' => true, 'message' => 'Asset unassigned and moved to New Stock.']);
        } elseif ($action === 'complete') {
            DB::table('stocks')->where('id', $id)->update([
                'status' => 'completed',
                'updated_at' => Carbon::now()
            ]);
            return response()->json(['success' => true, 'message' => 'Asset moved to Completed Storage pools.']);
        }

        return response()->json(['success' => false, 'message' => 'Invalid structural route action execution request.'], 400);
    }
}