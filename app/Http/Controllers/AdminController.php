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
            'order_id'   => 'required',
            'model_id'   => 'required',
            'capacity'   => 'required',
            'buy_price'  => 'required|numeric',
            'color'      => 'required',
            'imei_no_1'  => 'required',
            'warranty'   => 'required',
            'media.*'    => 'nullable|file|mimes:jpeg,jpg,png,webp,mp4,mov,avi|max:20480', // 20MB Max per file
            'meta_title' => 'nullable|string|max:255',
        ]);

        $buyPrice = (float)$request->input('buy_price');
        $pUserPercent = (int)($request->input('profit_percent_user') ?? 20);
        $profit = ($buyPrice * $pUserPercent) / 100;

        // Process File Media Stream Array Uploads
        $mediaPaths = [];
        if ($request->hasFile('media')) {
            foreach ($request->file('media') as $file) {
                if ($file->isValid()) {
                    // Generate distinctive asset storage designation signature
                    $filename = time() . '_' . uniqid() . '.' . $file->getClientOriginalExtension();
                    // Move item straight to your custom destination directory paths layout
                    $file->move(public_path('media/images/stock'), $filename);
                    $mediaPaths[] = 'media/images/stock/' . $filename;
                }
            }
        }

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
            
            // SEO & Structured Web Crawl Properties Mapping
            'meta_title'          => $request->input('meta_title'),
            'meta_description'    => $request->input('meta_description'),
            'meta_keywords'       => $request->input('meta_keywords'),
            'canonical_url'       => $request->input('canonical_url'),
            'schema_data'         => $request->input('schema_data'),
            'meta_robots'         => $request->input('meta_robots') ?? 'index, follow',
            
            // Dynamic Assets Storage String Array
            'media'               => json_encode($mediaPaths),
            
            'purchase_date'       => Carbon::now()->toDateString(),
            'created_at'          => Carbon::now(),
            'updated_at'          => Carbon::now()
        ]);

        return response()->json(['success' => true, 'message' => 'Manual asset entry saved successfully.']);
    }
    
public function update(Request $request)
    {
        $request->validate([
            'id'         => 'required',
            'capacity'   => 'required',
            'buy_price'  => 'required|numeric',
            'color'      => 'required',
            'imei_no_1'  => 'required',
            'warranty'   => 'required',
            'media.*'    => 'nullable|file|mimes:jpeg,jpg,png,webp,mp4,mov,avi|max:20480',
        ]);

        $id = $request->input('id');
        $buyPrice = (float)$request->input('buy_price');
        $pUserPercent = (int)$request->input('profit_percent_user');
        $profit = ($buyPrice * $pUserPercent) / 100;

        // Fetch existing asset dataset row record to safely preserve old media references
        $currentStock = DB::table('stocks')->where('id', $id)->first();
        $existingMedia = $currentStock && $currentStock->media ? json_decode($currentStock->media, true) : [];

        // Append newly captured file uploads to the array loop structure
        if ($request->hasFile('media')) {
            foreach ($request->file('media') as $file) {
                if ($file->isValid()) {
                    $filename = time() . '_' . uniqid() . '.' . $file->getClientOriginalExtension();
                    $file->move(public_path('media/images/stock'), $filename);
                    $existingMedia[] = 'media/images/stock/' . $filename;
                }
            }
        }

        DB::table('stocks')->where('id', $id)->update([
            'capacity'            => $request->input('capacity'),
            'buy_price'           => $buyPrice,
            'color'               => $request->input('color'),
            'imei_no_1'           => $request->input('imei_no_1'),
            'imei_no_2'           => $request->input('imei_no_2'),
            'warranty'            => $request->input('warranty'),
            'profit_percent_user' => $pUserPercent,
            'profit_perc_vendor'  => (int)$request->input('profit_perc_vendor'),
            'profit'              => $profit,
            
            // Updated SEO Metadata values
            'meta_title'          => $request->input('meta_title'),
            'meta_description'    => $request->input('meta_description'),
            'meta_keywords'       => $request->input('meta_keywords'),
            'canonical_url'       => $request->input('canonical_url'),
            'schema_data'         => $request->input('schema_data'),
            'meta_robots'         => $request->input('meta_robots'),
            
            'media'               => json_encode($existingMedia),
            'updated_at'          => Carbon::now()
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

    public function all_refubrished_phones()
    {
        // Query live stocks and match with model data attributes
        $products = DB::table('stocks')
            ->join('model', 'stocks.model_id', '=', 'model.id')
            ->select(
                'stocks.id',
                'stocks.buy_price',
                'stocks.sell_price',
                'stocks.warranty',
                'stocks.capacity',
                'model.title as model_title',
                'model.model_img'
            )
            ->where('stocks.status', 'pending')
            ->orderBy('stocks.buy_price', 'desc')
            ->paginate(12); // Using pagination instead of get() for clean page loads

        // Return view path containing payload collection
        return view('refurbished_phones', compact('products'));
    }

    public function buy_refubrished_phones($slug)
{
    // 1. Fetch the primary stock item matching the model's SEF URL slug
    $stock = DB::table('stocks')
        ->join('model', 'stocks.model_id', '=', 'model.id')
        ->select(
            'stocks.*', 
            'model.title as model_title', 
            'model.model_img', 
            'model.sef_url'
        )
        ->where('model.sef_url', $slug)
        ->where('stocks.status', 'pending')
        ->first();

    if (!$stock) {
        abort(404, 'Refurbished Device Not Found or Already Sold.');
    }

    // 2. Normalize and check model_img base path syntax
    if (!empty($stock->model_img)) {
        // If it doesn't contain a slash, it's just a raw filename; append your folder structure
        if (!str_contains($stock->model_img, '/')) {
            $stock->model_img = 'media/images/model/' . $stock->model_img;
        }
    }

    // 3. Parse the custom stock media collection gallery
    $mediaGallery = [];
    if (!empty($stock->media)) {
        $parsedMedia = is_string($stock->media) ? json_decode($stock->media, true) : $stock->media;
        
        // Loop through stock media array elements to fix their image paths if needed
        foreach ($parsedMedia as $mediaPath) {
            if (!empty($mediaPath)) {
                if (!str_contains($mediaPath, '/')) {
                    $mediaGallery[] = 'media/images/model/' . $mediaPath;
                } else {
                    $mediaGallery[] = $mediaPath;
                }
            }
        }
    }

    // Force prepend the corrected model.model_img to be the absolute first element
    if (!empty($stock->model_img)) {
        array_unshift($mediaGallery, $stock->model_img);
    }

    // 4. Recommendation Engine ("You May Also Like")
    $relatedStocks = DB::table('stocks')
        ->join('model', 'stocks.model_id', '=', 'model.id')
        ->select(
            'stocks.*', 
            'model.title as model_title', 
            'model.model_img', 
            'model.sef_url'
        )
        ->where('stocks.status', 'pending')
        ->where('stocks.id', '!=', $stock->id)
        ->inRandomOrder()
        ->limit(4)
        ->get();

    // Map over related items to sanitize their individual model_img paths as well
    foreach ($relatedStocks as $rel) {
        if (!empty($rel->model_img) && !str_contains($rel->model_img, '/')) {
            $rel->model_img = 'media/images/model/' . $rel->model_img;
        }
    }

    return view('buy_product', compact('stock', 'mediaGallery', 'relatedStocks'));
}

}