<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Session;
use Carbon\Carbon;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;


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
            'dealer_id'           => DB::table('order_items')->where('order_id', $orderId)->value('vendor_id'),
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
            ->where(function($q) { $q->whereNull('stocks.dealer_id')->orWhere('stocks.dealer_id', 0); })
            ->orderBy('stocks.id', 'desc')->get();

        $assignedStock = (clone $baseQuery)
            ->where('stocks.status', 'pending')
            ->whereNotNull('stocks.user_id')
            ->where('stocks.user_id', '!=', 0)
            ->where(function($q) { $q->whereNull('stocks.dealer_id')->orWhere('stocks.dealer_id', 0); })
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
            'media.*'    => 'nullable|file|mimes:jpeg,jpg,png,webp,mp4,mov,avi|max:20480',
        ]);

        $buyPrice = (float)$request->input('buy_price');
        $pUserPercent = (int)($request->input('profit_percent_user') ?? 20);
        $profit = ($buyPrice * $pUserPercent) / 100;

        // Fetch model information to engineer premium SEO properties
        $model = DB::table('model')->where('id', $request->input('model_id'))->first();
        if (!$model) {
            return response()->json(['success' => false, 'message' => 'Target phone model not found.'], 404);
        }

        // Build dynamic programmatic smart SEO configurations
        $modelTitle = $model->title;
        $variantInfo = $request->input('capacity') . ' (' . Str::title($request->input('color')) . ')';
        
        $metaTitle = "Best Refurbished " . $modelTitle . " " . $variantInfo . " | Certified Pre-Owned";
        $metaDescription = "Buy a 100% certified refurbished " . $modelTitle . " " . $variantInfo . " with " . $request->input('warranty') . " warranty. Fully tested, verified, and ready to dispatch today at a fraction of retail pricing!";
        $metaKeywords = "refurbished " . $modelTitle . ", buy second hand " . $modelTitle . ", " . $modelTitle . " " . $request->input('capacity') . ", certified preowned phones, discount smart phones";
        $canonicalUrl = url('/refurbished-phones/' . Str::slug($modelTitle . '-' . $variantInfo . '-' . $request->input('imei_no_1')));

        $schemaData = [
            "@context"    => "https://schema.org/",
            "@type"       => "Product",
            "name"        => "Certified Refurbished " . $modelTitle . " " . $variantInfo,
            "description" => $metaDescription,
            "offers"      => [
                "@type"         => "Offer",
                "priceCurrency" => "INR",
                "price"         => $buyPrice,
                "itemCondition" => "https://schema.org/RefurbishedCondition",
                "availability"  => "https://schema.org/InStock"
            ]
        ];

        // Process File Media Stream Array Uploads
        $mediaPaths = [];
        if ($request->hasFile('media')) {
            foreach ($request->file('media') as $file) {
                if ($file->isValid()) {
                    $filename = time() . '_' . uniqid() . '.' . $file->getClientOriginalExtension();
                    $file->move(public_path('media/images/stock'), $filename);
                    $mediaPaths[] = 'media/images/stock/' . $filename;
                }
            }
        }
        $orderId = 'ORD' . random_int(1000000000, 9999999999);

        DB::table('stocks')->insert([
            'order_id'            => $orderId,
            'user_id'             => 0,
            'dealer_id'           => 0,
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
            
            // Automated SEO Mapping
            'meta_title'          => $metaTitle,
            'meta_description'    => $metaDescription,
            'meta_keywords'       => $metaKeywords,
            'canonical_url'       => $canonicalUrl,
            'schema_data'         => json_encode($schemaData),
            'meta_robots'         => 'index, follow',
            
            'media'               => json_encode($mediaPaths),
            'purchase_date'       => Carbon::now()->toDateString(),
            'created_at'          => Carbon::now(),
            'updated_at'          => Carbon::now()
        ]);

        return response()->json(['success' => true, 'message' => 'Manual asset entry saved successfully with automated SEO.']);
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

        // Fetch existing asset dataset row to preserve media and get the model_id
        $currentStock = DB::table('stocks')->where('id', $id)->first();
        if (!$currentStock) {
            return response()->json(['success' => false, 'message' => 'Stock record not found.'], 404);
        }

        $existingMedia = $currentStock->media ? json_decode($currentStock->media, true) : [];

        // Fetch model to regenerate SEO based on potential capacity/color updates
        $model = DB::table('model')->where('id', $currentStock->model_id)->first();
        $modelTitle = $model ? $model->title : 'Premium Smartphone';
        $variantInfo = $request->input('capacity') . ' (' . Str::title($request->input('color')) . ')';

        $metaTitle = "Best Refurbished " . $modelTitle . " " . $variantInfo . " | Certified Pre-Owned";
        $metaDescription = "Buy a 100% certified refurbished " . $modelTitle . " " . $variantInfo . " with " . $request->input('warranty') . " warranty. Fully tested, verified, and ready to dispatch today!";
        $metaKeywords = "refurbished " . $modelTitle . ", buy second hand " . $modelTitle . ", " . $modelTitle . " " . $request->input('capacity');
        $canonicalUrl = url('/refurbished-phones/' . Str::slug($modelTitle . '-' . $variantInfo . '-' . $request->input('imei_no_1')));

        $schemaData = [
            "@context"    => "https://schema.org/",
            "@type"       => "Product",
            "name"        => "Certified Refurbished " . $modelTitle . " " . $variantInfo,
            "description" => $metaDescription,
            "offers"      => [
                "@type"         => "Offer",
                "priceCurrency" => "INR",
                "price"         => $buyPrice,
                "itemCondition" => "https://schema.org/RefurbishedCondition",
                "availability"  => "https://schema.org/InStock"
            ]
        ];

        // Append newly captured file uploads
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
            
            // Re-mapped SEO values to keep them accurate after an update
            'meta_title'          => $metaTitle,
            'meta_description'    => $metaDescription,
            'meta_keywords'       => $metaKeywords,
            'canonical_url'       => $canonicalUrl,
            'schema_data'         => json_encode($schemaData),
            
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
                'dealer_id' => 0,
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
    // 1. Fetch your target approved inventory products
    $products = DB::table('stocks')
        ->join('model', 'stocks.model_id', '=', 'model.id')
        ->join('brand', 'model.brand_id', '=', 'brand.id')
        ->select(
            'stocks.id',
            'stocks.buy_price',
            'stocks.order_id',          // NEW: Required for unique URL
            'model.sef_url',            // NEW: Required for the SEO slug
            'stocks.sell_price',
            'stocks.warranty',
            'stocks.capacity',
            'stocks.color',
            'model.title as model_title',
            'model.model_img',
            'brand.title as brand_title'
        )
        ->where('stocks.status', 'pending')
        ->where('stocks.is_approved', 1)
        ->orderBy('stocks.buy_price', 'desc')
        ->get(); // Using get() for full instant catalog client-side filtering

    // 2. Dynamically extract unique active brand options for the tab row
    $brands = $products->pluck('brand_title')->unique()->toArray();

    return view('refurbished_phones', compact('products', 'brands'));
}

  public function buy_refubrished_phones($slug, $order_id)
{
    // 1. Fetch the exact stock item using order_id (which is truly unique)
    $stock = DB::table('stocks')
        ->join('model', 'stocks.model_id', '=', 'model.id')
        ->select(
            'stocks.*', 
            'model.title as model_title', 
            'model.model_img', 
            'model.sef_url'
        )
        ->where('stocks.order_id', $order_id)
        ->where('stocks.status', 'pending')
        ->first();

    // Verify stock exists AND the URL slug strictly matches the database to prevent URL spoofing
    if (!$stock || $stock->sef_url !== $slug) {
        abort(404, 'Refurbished Device Not Found or Already Sold.');
    }

    // 2. Normalize and check model_img base path syntax
    if (!empty($stock->model_img)) {
        if (!str_contains($stock->model_img, '/')) {
            $stock->model_img = 'media/images/model/' . $stock->model_img;
        }
    }

    // 3. Parse the custom stock media collection gallery
    $mediaGallery = [];
    if (!empty($stock->media)) {
        $parsedMedia = is_string($stock->media) ? json_decode($stock->media, true) : $stock->media;
        
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

    // 4. Recommendation Engine (+/- 10k RS Price Match)
    $targetPrice = $stock->buy_price;
    $minPrice = $targetPrice - 10000;
    $maxPrice = $targetPrice + 10000;

    // Fetch up to 20 nearest stocks sorting by the absolute closest price difference
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
    ->whereBetween('stocks.buy_price', [$minPrice, $maxPrice])
    ->orderByRaw('ABS(stocks.buy_price - ?)', [$targetPrice])
    ->limit(20)
    ->get();

// If fewer than 5 related stocks found, show 20 random stocks instead
if ($relatedStocks->count() < 5) {
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
        ->limit(20)
        ->get();
}

// Sanitize image paths
foreach ($relatedStocks as $rel) {
    if (!empty($rel->model_img) && !str_contains($rel->model_img, '/')) {
        $rel->model_img = 'media/images/model/' . $rel->model_img;
    }
}

    return view('buy_product', compact('stock', 'mediaGallery', 'relatedStocks'));
}

public function dealer_index(Request $request)
    {
       if ($request->ajax()) {
            $dealers = DB::table('dealers')->orderBy('id', 'desc')->get();
            return response()->json(['dealers' => $dealers]);
        }

        $districts = DB::table('districts')->select('id', 'district_name')->get();
        return view('admin.dealers.index', compact('districts'));
    }

    // Secure Data Storage / Mutation Wrapper
    public function dealer_storeOrUpdate(Request $request)
    {
       $request->validate([
            'name' => 'required|string|max:255',
            'shop_name' => 'required|string|max:255',
            'phone' => 'required|string|max:20',
            'district' => 'required|array',
        ]);

        $id = $request->dealer_id;
        
        $data = [
            'name' => $request->name,
            'shop_name' => $request->shop_name,
            'phone' => $request->phone,
            'alternate_phone' => $request->alternate_phone,
            'shop_address' => $request->shop_address,
            'district' => json_encode($request->district),
            'pan_no' => $request->pan_no,
            'updated_at' => now(),
        ];

        if (!empty($request->mpin)) {
            $data['mpin'] = Hash::make($request->mpin);
        }

        $fileFields = ['dealer_photo', 'shop_photo', 'proof_front', 'proof_back'];
        foreach ($fileFields as $field) {
            if ($request->hasFile($field)) {
                if ($id) {
                    $oldFile = DB::table('dealers')->where('id', $id)->value($field);
                    if (!empty($oldFile) && file_exists(public_path($oldFile))) {
                        @unlink(public_path($oldFile));
                    }
                }

                $file = $request->file($field);
                $filename = time() . '_' . uniqid() . '.' . $file->getClientOriginalExtension();
                $file->move(public_path('uploads/dealers'), $filename);
                $data[$field] = 'uploads/dealers/' . $filename;
            }
        }

        if ($id) {
            DB::table('dealers')->where('id', $id)->update($data);
            return response()->json(['success' => 'Dealer configuration saved successfully.']);
        } else {
            $data['status'] = 1; 
            $data['created_at'] = now();
            DB::table('dealers')->insert($data);
            return response()->json(['success' => 'Dealer registered successfully.']);
        }
    }

    // Fetch singular entry for dynamic modal structural injection
    public function dealer_edit($id)
    {
       $dealer = DB::table('dealers')->where('id', $id)->first();
        if (!$dealer) return response()->json(['error' => 'Record not found'], 404);
        
        $dealer->district = json_decode($dealer->district, true) ?? [];
        return response()->json(['dealer' => $dealer]);
    }

    // Inline Status Change Operator
    public function dealer_toggleStatus($id)
    {
      $dealer = DB::table('dealers')->where('id', $id)->first();
        if (!$dealer) return response()->json(['error' => 'Record missing'], 404);

        $newStatus = ($dealer->status == 1) ? 0 : 1;
        DB::table('dealers')->where('id', $id)->update(['status' => $newStatus, 'updated_at' => now()]);

        return response()->json(['success' => 'Status changed successfully.']);
    }

    public function bidding_index(Request $request)
    {
        if ($request->ajax()) {
            $bids = DB::table('bidding')->orderBy('id', 'desc')->get();
            return response()->json(['bids' => $bids]);
        }

        // Fetch valid districts for our Select2 form dropdown
        $districts = DB::table('districts')->select('id', 'district_name')->get();
        return view('admin.bidding.index', compact('districts'));
    }

    public function bidding_storeOrUpdate(Request $request)
    {
        $request->validate([
            'district_id' => 'required|integer',
            'bid_time_start' => 'required',
            'bid_time_end' => 'required',
            'bid_min_perc' => 'required|numeric',
            'bid_max_perc' => 'required|numeric',
        ]);

        // Resolve structural district name from ID mapped via request
        $district = DB::table('districts')->where('id', $request->district_id)->first();
        if (!$district) {
            return response()->json(['error' => 'Invalid district parameter tracking data.'], 422);
        }

        $id = $request->bid_id;

        $data = [
            'district_id' => $request->district_id,
            'district_name' => $district->district_name,
            'bid_time_start' => $request->bid_time_start,
            'bid_time_end' => $request->bid_time_end,
            'bid_min_perc' => $request->bid_min_perc,
            'bid_max_perc' => $request->bid_max_perc,
            'updated_at' => now(),
        ];

        if ($id) {
            DB::table('bidding')->where('id', $id)->update($data);
            return response()->json(['success' => 'Bidding profile configuration synchronized.']);
        } else {
            $data['created_at'] = now();
            DB::table('bidding')->insert($data);
            return response()->json(['success' => 'Bidding configuration mapped successfully.']);
        }
    }

    public function bidding_edit($id)
    {
        $bid = DB::table('bidding')->where('id', $id)->first();
        if (!$bid) return response()->json(['error' => 'Record profile missing.'], 404);
        return response()->json(['bid' => $bid]);
    }

    public function bidding_destroy($id)
    {
        DB::table('bidding')->where('id', $id)->delete();
        return response()->json(['success' => 'Bidding parameter dropped from matrix vaults.']);
    }

    public function dealer_stock_index(Request $request) {
     $stocks = DB::table('stocks')
        ->join('dealers', 'stocks.dealer_id', '=', 'dealers.id')
        ->leftJoin('model', 'stocks.model_id', '=', 'model.id')
        ->where('stocks.dealer_id', '!=', 0)
        ->select(
            'stocks.*', 
            'dealers.name as dealer_name', 
            'dealers.phone as dealer_phone', 
            'dealers.shop_name',
            'dealers.district as dealer_district_json', // Grab the JSON column here
            'model.title'
        )
        ->orderBy('stocks.id', 'desc')
        ->get();

    // 2. Map the district names directly onto each stock record
    foreach ($stocks as $stock) {
        $districtIds = json_decode($stock->dealer_district_json, true) ?? [];

        $namesArray = DB::table('districts')
            ->whereIn('id', $districtIds)
            ->pluck('district_name')
            ->toArray();

        // Attach it securely so $row->district_names becomes available in Blade
        $stock->district_names = !empty($namesArray) ? implode(', ', $namesArray) : 'No District';
    }

    // 3. Keep these for your modal dropdown fields
    $dealers = DB::table('dealers')->where('status', 1)->get();
    $models = DB::table('model')->get();

    return view('admin.dealer_stock.index', compact('stocks', 'dealers', 'models'));
}

      
    public function dealer_stock_storeOrUpdate(Request $request) {
       $id = $request->stock_id;
        
        $data = [
            'dealer_id' => $request->dealer_id,
            'model_id' => $request->model_id,
            'capacity' => $request->capacity,
            'buy_price' => $request->buy_price,
            'sell_price' => $request->sell_price ?? $request->buy_price,
            'color' => $request->color,
            'imei_no_1' => $request->imei_no_1,
            'imei_no_2' => $request->imei_no_2,
            'warranty' => $request->warranty,
            'updated_at' => now(),
        ];

        if ($request->hasFile('media_files')) {
            $uploadedFiles = [];
            foreach ($request->file('media_files') as $file) {
                $filename = time() . '_' . uniqid() . '.' . $file->getClientOriginalExtension();
                $file->move(public_path('media/images/stock'), $filename);
                $uploadedFiles[] = 'media/images/stock/' . $filename;
            }
            $data['media'] = json_encode($uploadedFiles);
        }

        if ($id) {
            DB::table('stocks')->where('id', $id)->update($data);
            return response()->json(['success' => 'Stock parameters updated successfully.']);
        } else {
            $data['order_id'] = 'ORD' . rand(100000, 999999);
            $data['is_approved'] = 0; 
            $data['purchase_date'] = now();
            $data['created_at'] = now();
            
            DB::table('stocks')->insert($data);
            return response()->json(['success' => 'Stock provisioned successfully.']);
        }
    }

    public function dealer_stock_edit($id) {
     $stock = DB::table('stocks')->where('id', $id)->first();
        return response()->json(['stock' => $stock]);
    }

    public function dealer_stock_approve($id) {
       DB::table('stocks')->where('id', $id)->update([
            'is_approved' => 1,
            'updated_at' => now()
        ]);
        return response()->json(['success' => 'Stock approved successfully.']);
    }

    public function dealer_stock_reject($id) {
       $stock = DB::table('stocks')->where('id', $id)->first();
        if ($stock) {
            // Decodes media array and physically unlinks existing files from storage
            $mediaPaths = json_decode($stock->media, true) ?? [];
            foreach ($mediaPaths as $path) {
                if (!empty($path) && file_exists(public_path($path))) {
                    @unlink(public_path($path));
                }
            }
            DB::table('stocks')->where('id', $id)->delete();
        }
        return response()->json(['success' => 'Stock record and associated media removed successfully.']);
    }

}