<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Session;
use Carbon\Carbon;
class UserController extends Controller
{


    // Other existing methods...
    
    /**
     * Display all brands page with SEO
     */
    public function allBrands()
    {
        // Get all published brands ordered by ordering
        $brands = DB::table('brand')
            ->where('published', 1)
            ->orderBy('id', 'asc')
            ->select('id', 'title', 'sef_url', 'image', 'description', 
                    'meta_title', 'meta_desc', 'meta_keywords', 'meta_canonical_url', 
                    'custom_schema')
            ->get();
        
        // Add full image URL to each brand
        foreach ($brands as $brand) {
            $brand->image_url = $brand->image 
                ? asset('media/images/brand/' . $brand->image)
                : asset('media/images/brand/default-brand.webp');
        }
        
        // SEO Data (can be customized or taken from database settings)
        $seo = [
            'title' => 'All Brands | RevoDevice - Best Refurbished Mobile, Laptop & Tablet Brands in India',
            'meta_description' => 'Explore top brands like Apple, Samsung, Google, Xiaomi, Lenovo, Asus and more. Buy certified refurbished devices from India\'s most trusted brands at best prices.',
            'meta_keywords' => 'Apple refurbished, Samsung refurbished, Google Pixel, Xiaomi phones, Lenovo laptops, Asus, Vivo, LG, refurbished brands India',
            'og_title' => 'All Brands | Shop Refurbished Devices from Top Brands | RevoDevice',
            'og_description' => 'Find certified refurbished devices from Apple, Samsung, Google, Xiaomi, Lenovo, Asus and 50+ brands. 12 months warranty, free delivery, instant cash on selling.',
            'canonical_url' => url()->current()
        ];
        return view('brands.all-brands', compact('brands', 'seo'));
    }

      public function model($slug)
{
     $slug = str_replace('sell-', '', $slug);
    // Get brand by slug
    $brand = DB::table('brand')
        ->where('sef_url', $slug)
        ->where('published', 1)
        ->first();
    
    if (!$brand) {
        abort(404, 'Brand not found');
    }
    
    // Get all models for this brand with cat_id = 83
    $models = DB::table('model')
        ->where('brand_id', $brand->id)
        ->where('cat_id', 83)
        ->where('published', 1)
        ->orderBy('ordering', 'asc')
        ->get();
    
    // Add image URL to each model
    foreach ($models as $model) {
        $model->model_img_url = $model->model_img 
            ? asset('media/images/model/' . $model->model_img)
            : asset('media/images/model/default-model.webp');
    }
    
    // Get unique model_series_id from the fetched models
    $modelSeriesIds = $models->pluck('model_series_id')->unique()->filter()->toArray();
    
    // Get only model series that have models for this brand
    $modelSeries = DB::table('model_series')
        ->whereIn('id', $modelSeriesIds)
        ->where('published', 1)
        ->orderBy('ordering', 'asc')
        ->get();
    
    // SEO Data
    $seo = [
        'title' => "Sell {$brand->title} Phone | Best Price for Used {$brand->title} Mobile | RevoDevice",
        'meta_description' => "Sell your old {$brand->title} phone instantly at best price. Free doorstep pickup, instant cash, secure data wiping. Get quote for {$brand->title} Galaxy, Note, A series and more.",
        'meta_keywords' => "sell {$brand->title} phone, sell used {$brand->title}, {$brand->title} exchange, old {$brand->title} sell, best price for {$brand->title}",
        'og_title' => "Sell {$brand->title} Phone - Get Instant Quote | RevoDevice",
        'og_description' => "Sell your {$brand->title} phone at best price. Free pickup, instant payment, 100% secure. Check price now!",
        'canonical_url' => url()->current()
    ];
    
    return view('sell-old-phone', compact('brand', 'models', 'modelSeries', 'seo'));
}
    
    /**
     * Search models API endpoint
     */
    public function searchModels(Request $request)
    {
        $query = $request->get('q');
        $brandId = $request->get('brand_id');
        $seriesId = $request->get('series_id');
        
        $modelsQuery = DB::table('model')
            ->where('published', 1)
            ->where('cat_id', 83);
        
        if ($brandId) {
            $modelsQuery->where('brand_id', $brandId);
        }
        
        if ($seriesId && $seriesId != 'all') {
            $modelsQuery->where('model_series_id', $seriesId);
        }
        
        if ($query) {
            $modelsQuery->where(function($q) use ($query) {
                $q->where('title', 'like', "%{$query}%")
                  ->orWhere('searchable_words', 'like', "%{$query}%");
            });
        }
        
        $models = $modelsQuery->orderBy('ordering', 'asc')->limit(50)->get();
        
        foreach ($models as $model) {
            $model->model_img_url = $model->model_img 
                ? asset('media/images/model/' . $model->model_img)
                : asset('media/images/model/default-model.webp');
        }
        
        return response()->json($models);
    }

      public function tablet_brands()
{
    // Get brands that have tablet models (cat_id = 28)
    $brands = DB::table('brand')
        ->where('published', 1)
        ->whereExists(function($query) {
            $query->select(DB::raw(1))
                ->from('model')
                ->whereColumn('model.brand_id', 'brand.id')
                ->where('model.cat_id', 28)
                ->where('model.published', 1);
        })
        ->orderBy('ordering', 'asc')
        ->get(['id', 'title', 'sef_url', 'image', 'meta_title', 'meta_desc', 'meta_keywords']);
    
    // Add image URL to each brand
    foreach ($brands as $brand) {
        $brand->image_url = $brand->image 
            ? asset('media/images/brand/' . $brand->image)
            : asset('media/images/brand/default-brand.webp');
    }
    
    // Get total tablet models count for search placeholder
    $totalTablets = DB::table('model')
        ->where('cat_id', 28)
        ->where('published', 1)
        ->count();
    
    // SEO Data
    $seo = [
        'title' => 'Sell Old Tablet | Best Price for Used Tablets Online | RevoDevice',
        'meta_description' => 'Sell your old tablet instantly at best price. Sell iPad, Samsung Tab, Lenovo Tab and more. Free doorstep pickup, instant cash, secure data wiping.',
        'meta_keywords' => 'sell old tablet, sell used tablet, tablet exchange, sell iPad, sell Samsung Tab, best price for tablet',
        'og_title' => 'Sell Old Tablet - Get Instant Quote | RevoDevice',
        'og_description' => 'Sell your tablet at best price. Free pickup, instant payment, 100% secure. Check price now!',
        'canonical_url' => url()->current()
    ];
    
    return view('sell-old-tablet', compact('brands', 'totalTablets', 'seo'));
}

public function tablet_models($slug)
{
    // Remove 'sell-' prefix from slug if present
    $cleanSlug = str_replace('sell-', '', $slug);
    
    // Get brand by sef_url
    $brand = DB::table('brand')
        ->where('sef_url', $cleanSlug)
        ->where('published', 1)
        ->first();
    
    if (!$brand) {
        abort(404, 'Brand not found');
    }
    
    // Get all tablet models for this brand (cat_id = 28)
    $models = DB::table('model')
        ->where('brand_id', $brand->id)
        ->where('cat_id', 28)
        ->where('published', 1)
        ->orderBy('ordering', 'asc')
        ->get();
    
    // Add image URL to each model
    foreach ($models as $model) {
        $model->model_img_url = $model->model_img 
            ? asset('media/images/model/' . $model->model_img)
            : asset('media/images/model/default-model.webp');
    }
    
    // Get unique model_series_id from the fetched models
    $modelSeriesIds = $models->pluck('model_series_id')->unique()->filter()->toArray();
    
    // Get only model series that have tablet models for this brand
    $modelSeries = DB::table('model_series')
        ->whereIn('id', $modelSeriesIds)
        ->where('published', 1)
        ->orderBy('ordering', 'asc')
        ->get();
    
    // SEO Data
    $seo = [
        'title' => "Sell {$brand->title} Tablet | Best Price for Used {$brand->title} Tablet | RevoDevice",
        'meta_description' => "Sell your old {$brand->title} tablet instantly at best price. Free doorstep pickup, instant cash, secure data wiping. Get quote for all {$brand->title} tablet models.",
        'meta_keywords' => "sell {$brand->title} tablet, sell used {$brand->title} tablet, {$brand->title} tablet exchange, old {$brand->title} tablet sell, best price for {$brand->title} tablet",
        'og_title' => "Sell {$brand->title} Tablet - Get Instant Quote | RevoDevice",
        'og_description' => "Sell your {$brand->title} tablet at best price. Free pickup, instant payment, 100% secure. Check price now!",
        'canonical_url' => url()->current()
    ];
    
    return view('sell-old-tablet-models', compact('brand', 'models', 'modelSeries', 'seo'));
}

  public function sendOtp(Request $request)
    {
        $request->validate([
            'mob_no' => 'required|digits:10'
        ]);

        $phone = $request->input('mob_no');
        // Generate random 4-digit OTP
        $otp = rand(1000, 9999);
        
        // Delete old OTP for this phone
        DB::table('phone_otp')->where('phone', $phone)->delete();
        
        // Store new OTP
        DB::table('phone_otp')->insert([
            'phone' => $phone,
            'otp' => $otp,
            'created_at' => Carbon::now(),
            'updated_at' => Carbon::now()
        ]);
        
        // In production, send SMS here
        // For testing, log OTP or return in response
        // \Log::info("OTP for {$phone}: {$otp}");
        
        return response()->json([
            'success' => true,
            'message' => 'OTP sent successfully',
            'otp' => $otp // Remove in production, only for testing
        ]);
    }
    
    /**
     * Verify OTP and login user
     */
    public function verifyOtp(Request $request)
    {
        $request->validate([
            'phone' => 'required|digits:10',
            'otp' => 'required|digits:4'
        ]);
        
        $phone = $request->phone;
        $otp = $request->otp;
        
        // Check OTP in database
        $otpRecord = DB::table('phone_otp')
            ->where('phone', $phone)
            ->where('otp', $otp)
            ->first();

        
        if (!$otpRecord) {
            return response()->json([
                'success' => false,
                'message' => 'Invalid or expired OTP'
            ], 400);
        }
        
        // Check if user exists
        $user = DB::table('users')->where('phone', $phone)->first();
        
        if (!$user) {
            // Insert new user as guest
            $userId = DB::table('users')->insertGetId([
                'phone' => $phone,
                'name' => 'Guest',
                'created_at' => Carbon::now(),
                'updated_at' => Carbon::now()
            ]);
            
            $user = DB::table('users')->where('id', $userId)->first();
        }
        
        // Set session
        Session::put('user_id', $user->id);
        Session::put('user_name', $user->name);
        Session::put('user_phone', $user->phone);
        Session::put('is_permanent', true);
        
        // Delete used OTP
        DB::table('phone_otp')->where('phone', $phone)->delete();
        
        return response()->json([
            'success' => true,
            'message' => 'Login successful',
            'user' => [
                'id' => $user->id,
                'name' => $user->name,
                'phone' => $user->phone
            ]
        ]);
    }
    
    /**
     * Logout user
     */
   public function logout(Request $request)
{
    Session::flush();
    
    if ($request->ajax()) {
        return response()->json([
            'success' => true,
            'message' => 'Logged out successfully',
            'redirect' => url('/')
        ]);
    }
    
    return redirect('/');
}

public function particular_model($slug)
{
    // Get model by sef_url
    $model = DB::table('model')
        ->where('sef_url', $slug)
        ->where('published', 1)
        ->first();
    
    if (!$model) {
        abort(404, 'Model not found');
    }
    
    // Get brand details
    $brand = DB::table('brand')
        ->where('id', $model->brand_id)
        ->where('published', 1)
        ->first();
    
    // Parse capacity JSON
    $variants = json_decode($model->capacity, true);
    
    // Add image URL
    $model->model_img_url = $model->model_img 
        ? asset('media/images/model/' . $model->model_img)
        : asset('media/images/model/default-model.webp');
    
    // Default selected variant (first one)
    $selectedVariant = !empty($variants) ? $variants[0] : null;
    
    // SEO Data
    $seo = [
        'title' => $model->meta_title ?: "Sell {$model->title} | Best Price for Used {$model->title} | RevoDevice",
        'meta_description' => $model->meta_desc ?: "Sell your {$model->title} at best price. Free doorstep pickup, instant cash, secure data wiping. Get instant quote for all variants.",
        'meta_keywords' => $model->meta_keywords ?: "sell {$model->title}, used {$model->title} price, {$model->title} exchange, sell old {$model->title}",
        'og_title' => "Sell {$model->title} - Get Instant Quote | RevoDevice",
        'og_description' => "Sell your {$model->title} at best price. Free pickup, instant payment. Check price now!",
        'canonical_url' => $model->meta_canonical_url ?: url()->current()
    ];
    
    return view('sell-old-mobile-particular', compact('model', 'brand', 'variants', 'selectedVariant', 'seo'));
}

    public function get_attributes(Request $request)
    {
        try {
            // Get selected_id from GET or POST
            $selectedId = $request->get('selected_id');
            
            if (!$selectedId) {
                // Try to get from JSON body (POST request)
                $input = $request->json()->all();
                $selectedId = $input['selected_id'] ?? null;
            }
            
            // Convert to array if comma separated string
            if ($selectedId && is_string($selectedId)) {
                $selectedId = explode(',', $selectedId);
            }
            
            // Validate
            if (!is_array($selectedId) || empty($selectedId)) {
                return response()->json([
                    'status' => 'error',
                    'message' => 'Invalid payload'
                ], 400);
            }
            
            // Sanitize IDs (convert to integers)
            $ids = array_map('intval', $selectedId);
            $modelId = $request->get('model_id') ? intval($request->get('model_id')) : 0;
            
            if (!$modelId) {
                // Try to get from JSON body
                if ($request->isJson()) {
                    $modelId = $input['model_id'] ?? 0;
                }
            }
            
            // ------------------------------------
            // GET WARRANTY STATUS
            // ------------------------------------
            $warranty = 'on'; // default
            
            if ($modelId) {
                $modelData = DB::table('model')
                    ->where('id', $modelId)
                    ->select('warranty')
                    ->first();
                
                if ($modelData && isset($modelData->warranty)) {
                    $warranty = strtolower(trim($modelData->warranty));
                }
            }
            
            // ------------------------------------
            // MAIN QUERY
            // ------------------------------------
            $idList = implode(',', $ids);
            
            $query = DB::table('product_fields as pf')
                ->join('product_options as po', 'po.product_field_id', '=', 'pf.id')
                ->select('pf.*', 'po.*')
                ->whereRaw("pf.question_no_id IN ($idList)")
                ->where('pf.product_id', $modelId);
            
            $results = $query->get();
            
            if ($results->isEmpty()) {
                return response()->json([
                    'status' => 'success',
                    'data' => []
                ]);
            }
            
            // Base icon URL - Use your local base URL
            $baseIconUrl = url('/media/images/custom_fields/');
            $data = [];
            
            foreach ($results as $row) {
                // Convert stdClass to array for manipulation
                $rowArray = (array) $row;
                
                // ------------------------------------
                // EXCLUDE "Age of the mobile" IF WARRANTY = OFF
                // ------------------------------------
                if (
                    $warranty == 'off' &&
                    isset($rowArray['name']) && 
                    trim($rowArray['name']) == 'Age of the mobile'
                ) {
                    continue;
                }
                
                // Convert ID to string
                if (isset($rowArray['id'])) {
                    $rowArray['id'] = (string) $rowArray['id'];
                }
                
                // Convert product_field_id to string if exists
                if (isset($rowArray['product_field_id'])) {
                    $rowArray['product_field_id'] = (string) $rowArray['product_field_id'];
                }
                
                // Convert question_no_id to string if exists
                if (isset($rowArray['question_no_id'])) {
                    $rowArray['question_no_id'] = (string) $rowArray['question_no_id'];
                }
                
                // Add full icon URL using local base URL
                if (!empty($rowArray['icon'])) {
                    $rowArray['icon_url'] = $baseIconUrl . '/' . $rowArray['icon'];
                } else {
                    $rowArray['icon_url'] = "";
                }
                
                $data[] = $rowArray;
            }
            
            // ------------------------------------
            // OUTPUT JSON
            // ------------------------------------
            return response()->json([
                'status' => 'success',
                'data' => $data
            ]);
            
        } catch (\Exception $e) {
            
            return response()->json([
                'status' => 'error',
                'message' => $e->getMessage()
            ], 500);
        }
    }

 public function evaluate_phone($model_slug, $variant_slug)
    {
        
        // Get model data
        $model = DB::table('model')
            ->where('sef_url', $model_slug)
            ->where('published', 1)
            ->first();
        
        if (!$model) {
            abort(404, 'Model not found');
        }
        
        // Get QA questions based on model's cat_id
        $qaQuestions = DB::table('qa')
            ->where('category_id', $model->cat_id)
            ->orderBy('id', 'asc')
            ->get(['id', 'name', 'description']);
        
        // Get brand details
        $brand = DB::table('brand')
            ->where('id', $model->brand_id)
            ->first();
        // Get capacity from variant slug
        $capacity = str_replace('-', '/', $variant_slug);
        
        // Get model image
        $model->model_img_url = $model->model_img 
            ? asset('media/images/model/' . $model->model_img)
            : asset('media/images/model/default-model.webp');
        
        // SEO Data
        $seo = [
            'title' => "Evaluate {$brand->title} {$model->title} - Get Best Price | RevoDevice",
            'meta_description' => "Answer few questions about your {$brand->title} {$model->title} and get instant best price. Free doorstep pickup, instant cash payment.",
            'meta_keywords' => "evaluate {$brand->title} {$model->title}, sell {$brand->title} {$model->title}, phone evaluation",
            'canonical_url' => url()->current()
        ];

        return view('evaluate-page', compact('model', 'brand', 'qaQuestions', 'capacity', 'variant_slug', 'seo'));
    }

 public function getPrice(Request $request)
{
    try {
        $data = $request->json()->all();
        
        // Extract data from payload
        $model_id = $data['model_id'] ?? null;
        $qa_answers = $data['answers'] ?? [];
        $selected_attributes = $data['selected_attributes'] ?? [];
        
        if (!$model_id) {
            return response()->json([
                'success' => false,
                'error' => 'Model ID is required'
            ], 400);
        }
        
        // Get model details
        $model = DB::table('model')->where('id', $model_id)->first();
        
        if (!$model) {
            return response()->json([
                'success' => false,
                'error' => 'Model not found'
            ], 404);
        }
        
        // Get the capacity from variant_slug
        $capacity = $data['variant_slug'] ?? '';
        
        // Normalize the input capacity for matching
        // Convert "8gb128gb" to "8GB/128GB"
        $normalized_capacity = $this->normalizeCapacity($capacity);
        
        // Get base price from model capacity
        $base_price = 0;
        $model_capacities = json_decode($model->capacity, true);
        
        if (is_array($model_capacities)) {
            foreach ($model_capacities as $model_cap) {
                // Normalize the database capacity for comparison
                $normalized_db_capacity = $this->normalizeCapacity($model_cap['capacity']);
                
                if ($normalized_db_capacity == $normalized_capacity) {
                    $base_price = floatval($model_cap['base_price']);
                    break;
                }
            }
        }
        
        // If capacity not found, use model price
        if ($base_price == 0) {
            $base_price = floatval($model->price);
        }
        
        // Process QA answers - find first 'no' answer
        $first_no_index = -1;
        $processed_qa = [];
        $qa_details = [];
        $found_no = false;
        
        ksort($qa_answers);
        
        foreach ($qa_answers as $question_id => $answer) {
            if ($found_no) {
                $processed_qa[$question_id] = 'no';
            } elseif (strtolower($answer) === 'no') {
                $processed_qa[$question_id] = 'no';
                $found_no = true;
                if ($first_no_index == -1) {
                    $first_no_index = $question_id;
                }
            } else {
                $processed_qa[$question_id] = 'yes';
            }
            
            // Get question name
            $question = DB::table('qa')->where('id', $question_id)->first();
            $qa_details[] = [
                'question_id' => $question_id,
                'question_name' => $question->name ?? 'Question ' . $question_id,
                'original_answer' => $answer,
                'processed_answer' => $processed_qa[$question_id]
            ];
        }
        
        // Check if first answer is 'no'
        $first_question_id = array_key_first($qa_answers);
        $first_answer = $qa_answers[$first_question_id] ?? null;
        
        if (strtolower($first_answer) === 'no') {
            $final_price = floatval($model->least_price ?? 0);
            
            return response()->json([
                'success' => true,
                'data' => [
                    'base_price' => $base_price,
                    'model_id' => $model_id,
                    'deducted_amount_first' => 0,
                    'deducted_amount_two' => 0,
                    'final_price' => $final_price,
                    'qa_details' => $qa_details,
                    'no_question_details' => [],
                    'yes_question_details' => [],
                    'processed_answers' => $processed_qa,
                    'skip_calculation_reason' => 'First QA answer is "no"',
                    'first_question_id' => $first_question_id,
                    'first_answer' => $first_answer,
                    'model_least_price' => floatval($model->least_price ?? 0)
                ]
            ]);
        }
        
        // Calculate deductions from selected attributes
        $deducted_amount_two = 0;
        $yes_question_details = [];
        
        // Get all product fields and options for the selected attributes
        if (!empty($selected_attributes)) {
            foreach ($selected_attributes as $field_id => $option_id) {
                // Get product field details
                $product_field = DB::table('product_fields')
                    ->where('id', $field_id)
                    ->first();
                
                // Get option details
                $option = DB::table('product_options')
                    ->where('id', $option_id)
                    ->first();
                
                if ($product_field && $option) {
                    // Calculate amount based on price_type
                    $amount = 0;
                    if ($option->price_type == 1) {
                        // Fixed amount
                        $amount = floatval($option->price);
                    } else {
                        // Percentage of base price
                        $percentage = floatval($option->price);
                        $amount = ($base_price * $percentage / 100);
                    }
                    
                    // Check add_sub
                    $add_sub = trim($option->add_sub ?? '+');
                    if ($add_sub === '-') {
                        $deducted_amount_two -= $amount;
                    } else {
                        $deducted_amount_two += $amount;
                    }
                    
                    // Find which question this field belongs to
                    $question_id = $product_field->question_no_id ?? null;
                    
                    $yes_question_details[] = [
                        'question_id' => $question_id,
                        'field_id' => $field_id,
                        'option_id' => $option_id,
                        'label' => $option->label,
                        'add_sub' => $add_sub,
                        'calculated_amount' => $amount
                    ];
                }
            }
        }
        
        // Calculate final price
        $final_price = $base_price + $deducted_amount_two;
        $final_price = max(0, $final_price);
        
        return response()->json([
            'success' => true,
            'data' => [
                'base_price' => $base_price,
                'model_id' => $model_id,
                'deducted_amount_first' => 0,
                'deducted_amount_two' => $deducted_amount_two,
                'final_price' => $final_price,
                'qa_details' => $qa_details,
                'no_question_details' => [],
                'yes_question_details' => $yes_question_details,
                'processed_answers' => $processed_qa,
                'capacity' => $capacity,
                'normalized_capacity' => $normalized_capacity,
                'model_price' => floatval($model->price)
            ]
        ]);
        
    } catch (\Exception $e) {
        return response()->json([
            'success' => false,
            'error' => $e->getMessage()
        ], 500);
    }
}

/**
 * Normalize capacity string for comparison
 * Converts "8gb128gb" to "8GB/128GB" and "6GB/64GB" to "6GB/64GB" etc.
 */
private function normalizeCapacity($capacity)
{
    // Remove spaces and convert to uppercase
    $normalized = strtoupper(preg_replace('/\s+/', '', $capacity));
    
    // Check if it already has the format "XGB/XGB"
    if (strpos($normalized, '/') !== false) {
        return $normalized;
    }
    
    // Convert "8GB128GB" to "8GB/128GB"
    if (preg_match('/(\d+GB)(\d+GB)/i', $normalized, $matches)) {
        return $matches[1] . '/' . $matches[2];
    }
    
    // Convert "8gb128gb" to "8GB/128GB"
    if (preg_match('/(\d+)GB(\d+)GB/i', $normalized, $matches)) {
        return $matches[1] . 'GB/' . $matches[2] . 'GB';
    }
    
    // Convert "8gb 128gb" with space to "8GB/128GB"
    $normalized = str_replace(' ', '/', $normalized);
    
    return $normalized;
}
    /**
     * Calculate price logic
     */
    private function calculatePrice($payload)
    {
        $device_id = $payload['device_id'] ?? null;
        $form_data = $payload['form_data'] ?? [];
        $qa_answers = $form_data['qa_answers'] ?? [];
        $capacity = $payload['capacity'] ?? null;
        $model_id = $form_data['model_id'] ?? $payload['model_id'] ?? null;
        
        // Parse field_answers from new structure (grouped by numbers)
        $field_answers_flat = [];
        if (isset($form_data['field_answers'])) {
            foreach ($form_data['field_answers'] as $group => $fields) {
                foreach ($fields as $field_id => $field_data) {
                    $field_answers_flat[$field_id] = $field_data;
                }
            }
        }
        
        // STEP 1: Set base price
        $base_price = 0;
        $model = null;
        
        if ($model_id) {
            $model = DB::table('model')->where('id', $model_id)->first();
            
            if ($model) {
                // Check if capacity is provided
                $capacity_to_use = $capacity ?: ($form_data['capacity'] ?? null);
                
                if (!empty($capacity_to_use)) {
                    // Decode capacity JSON from model
                    $model_capacities = json_decode($model->capacity, true);
                    
                    if (is_array($model_capacities)) {
                        // Find matching capacity
                        $found = false;
                        $normalized_input = strtoupper(preg_replace('/\s+/', '', $capacity_to_use));
                        
                        foreach ($model_capacities as $model_cap) {
                            $normalized_model_cap = strtoupper(preg_replace('/\s+/', '', $model_cap['capacity']));
                            
                            if ($normalized_model_cap == $normalized_input) {
                                $base_price = floatval($model_cap['base_price']);
                                $found = true;
                                break;
                            }
                        }
                        
                        if (!$found) {
                            $base_price = floatval($model->price);
                        }
                    } else {
                        $base_price = floatval($model->price);
                    }
                } else {
                    $base_price = floatval($model->price);
                }
            }
        }
        
        // NEW CONDITION: Check if first QA answer is "no"
        $skip_calculation = false;
        if (!empty($qa_answers)) {
            // Sort questions by key (question number) to get the first one
            ksort($qa_answers);
            
            // Get the first question ID and its answer
            $first_question_id = array_key_first($qa_answers);
            $first_answer = $qa_answers[$first_question_id] ?? null;
            
            // If the first answer is "no", set final price to model's least_price
            if (strtolower($first_answer) === 'no') {
                $skip_calculation = true;
                $final_price = floatval($model->least_price ?? 0);
                
                return [
                    'base_price' => $base_price,
                    'model_id' => $model_id,
                    'deducted_amount_first' => 0,
                    'deducted_amount_two' => 0,
                    'final_price' => $final_price,
                    'qa_details' => [],
                    'no_question_details' => [],
                    'yes_question_details' => [],
                    'processed_answers' => $qa_answers,
                    'skip_calculation_reason' => 'First QA answer is "no"',
                    'first_question_id' => $first_question_id,
                    'first_answer' => $first_answer,
                    'model_least_price' => floatval($model->least_price ?? 0),
                    'field_answers_structure' => 'grouped'
                ];
            }
        }
        
        // STEP 2: Process QA answers - if any answer is "no", set all subsequent answers to "no"
        $processed_qa_answers = [];
        $qa_details = [];
        $found_no = false;
        
        ksort($qa_answers);
        
        foreach ($qa_answers as $question_id => $answer) {
            if ($found_no) {
                $processed_qa_answers[$question_id] = 'no';
            } elseif (strtolower($answer) === 'no') {
                $processed_qa_answers[$question_id] = 'no';
                $found_no = true;
            } else {
                $processed_qa_answers[$question_id] = 'yes';
            }
        }
        
        // Get QA question names from database
        $qa_names = [];
        if (!empty($qa_answers)) {
            $question_ids = array_keys($qa_answers);
            $qa_names_data = DB::table('qa')
                ->whereIn('id', $question_ids)
                ->select('id', 'name')
                ->get();
            
            foreach ($qa_names_data as $qa) {
                $qa_names[$qa->id] = $qa->name;
            }
            
            // Prepare QA details with names
            foreach ($qa_answers as $question_id => $answer) {
                $qa_details[] = [
                    'question_id' => $question_id,
                    'question_name' => $qa_names[$question_id] ?? 'Question ' . $question_id,
                    'original_answer' => $answer,
                    'processed_answer' => $processed_qa_answers[$question_id] ?? $answer
                ];
            }
        }
        
        // Separate yes and no questions from PROCESSED answers
        $no_questions = [];
        $yes_questions = [];
        
        foreach ($processed_qa_answers as $question_id => $answer) {
            if (strtolower($answer) === 'no') {
                $no_questions[] = $question_id;
            } elseif (strtolower($answer) === 'yes') {
                $yes_questions[] = $question_id;
            }
        }
        
        // STEP 3: Calculate deducted_amount_first from NO questions
        $deducted_amount_first = 0;
        $addition_amount_step3 = 0;
        $sub_amount_step3 = 0;
        $no_question_details = [];
        
        if (!empty($no_questions)) {
            // Get product fields for no questions
            $no_fields = DB::table('product_fields as pf')
                ->select('pf.question_no_id', 'pf.no_question_operator', 'pf.no_question_type', 'pf.no_question_value')
                ->whereIn('pf.question_no_id', $no_questions)
                ->where('pf.product_id', $model_id)
                ->whereNotNull('pf.no_question_operator')
                ->whereNotNull('pf.no_question_type')
                ->groupBy('pf.question_no_id')
                ->get();
            
            foreach ($no_fields as $field) {
                $question_id = $field->question_no_id;
                $operator = trim($field->no_question_operator ?? '+');
                $type = strtolower(trim($field->no_question_type ?? 'baseprice'));
                $value = floatval($field->no_question_value ?? 0);
                
                if ($value == 0) continue;
                
                $amount = 0;
                if ($type === 'baseprice') {
                    $amount = ($base_price * $value) / 100;
                } elseif ($type === 'fixed') {
                    $amount = $value;
                }
                
                // Accumulate based on operator
                if ($operator === '+') {
                    $addition_amount_step3 += $amount;
                } elseif ($operator === '-') {
                    $sub_amount_step3 += $amount;
                }
                
                // Store details
                $no_question_details[] = [
                    'question_id' => $question_id,
                    'question_name' => $qa_names[$question_id] ?? 'Question ' . $question_id,
                    'operator' => $operator,
                    'type' => $type,
                    'value' => $value,
                    'base_price' => $base_price,
                    'calculated_amount' => $amount,
                    'applied_as' => ($operator === '+' ? 'addition' : 'subtraction')
                ];
            }
            
            // CORRECT CALCULATION: Net effect = additions - subtractions
            $deducted_amount_first = $addition_amount_step3 - $sub_amount_step3;
        }
        
        // STEP 4: Calculate deducted_amount_two from YES questions
        $deducted_amount_two = 0;
        $addition_amount_step4 = 0;
        $sub_amount_step4 = 0;
        $yes_question_details = [];
        
        if (!empty($yes_questions)) {
            foreach ($yes_questions as $question_no_id) {
                // Find all product fields for this question_no_id
                $field_ids_for_question = DB::table('product_fields')
                    ->where('question_no_id', $question_no_id)
                    ->where('product_id', $model_id)
                    ->pluck('id')
                    ->toArray();
                
                // Initialize question details
                $question_detail = [
                    'question_id' => $question_no_id,
                    'question_name' => $qa_names[$question_no_id] ?? 'Question ' . $question_no_id,
                    'selected_options' => [],
                    'fields_processed' => []
                ];
                
                // Calculate amount for each field
                foreach ($field_ids_for_question as $field_id) {
                    if (isset($field_answers_flat[$field_id])) {
                        $field_data = $field_answers_flat[$field_id];
                        
                        // Handle both 'value' and 'values' from payload
                        $selected_values = [];
                        
                        if (isset($field_data['value'])) {
                            $selected_values[] = $field_data['value'];
                        } elseif (isset($field_data['values'])) {
                            if (is_array($field_data['values'])) {
                                $selected_values = $field_data['values'];
                            } else {
                                $selected_values = [$field_data['values']];
                            }
                        }
                        
                        $question_detail['fields_processed'][] = [
                            'field_id' => $field_id,
                            'has_data' => true,
                            'selected_values_count' => count($selected_values),
                            'selected_values' => $selected_values
                        ];
                        
                        if (!empty($selected_values)) {
                            // Get options from database
                            $options = DB::table('product_options')
                                ->where('product_field_id', $field_id)
                                ->whereIn('label', $selected_values)
                                ->select('id', 'label', 'price_type', 'price', 'add_sub')
                                ->get();
                            
                            foreach ($options as $option) {
                                $amount = 0;
                                if ($option->price_type == 1) {
                                    // Fixed amount
                                    $amount = floatval($option->price);
                                } else {
                                    // Percentage of base price
                                    $percentage = floatval($option->price);
                                    $amount = ($base_price * $percentage / 100);
                                }
                                
                                // Check add_sub column
                                $add_sub = trim($option->add_sub ?? '');
                                if ($add_sub === '+') {
                                    $addition_amount_step4 += $amount;
                                } elseif ($add_sub === '-') {
                                    $sub_amount_step4 += $amount;
                                } else {
                                    $addition_amount_step4 += $amount;
                                }
                                
                                // Store selected option details
                                $question_detail['selected_options'][] = [
                                    'field_id' => $field_id,
                                    'option_id' => $option->id,
                                    'label' => $option->label,
                                    'price_type' => $option->price_type,
                                    'price' => $option->price,
                                    'add_sub' => $add_sub,
                                    'calculated_amount' => $amount
                                ];
                            }
                            
                            if ($options->isEmpty()) {
                                $question_detail['fields_processed'][] = [
                                    'field_id' => $field_id,
                                    'warning' => 'No matching options found in database',
                                    'selected_values' => $selected_values
                                ];
                            }
                        } else {
                            $question_detail['fields_processed'][] = [
                                'field_id' => $field_id,
                                'warning' => 'No values found in payload for this field',
                                'field_data' => $field_data
                            ];
                        }
                    } else {
                        $question_detail['fields_processed'][] = [
                            'field_id' => $field_id,
                            'warning' => 'Field not found in payload'
                        ];
                    }
                }
                
                $yes_question_details[] = $question_detail;
            }
            
            // Calculate deducted_amount_two
            $deducted_amount_two = $addition_amount_step4 - $sub_amount_step4;
        }
        
        // Calculate final price
        $final_price = $base_price + $deducted_amount_first + $deducted_amount_two;
        
        // Ensure price doesn't go below 0
        $final_price = max(0, $final_price);
        
        return [
            'base_price' => $base_price,
            'model_id' => $model_id,
            'deducted_amount_first' => $deducted_amount_first,
            'deducted_amount_two' => $deducted_amount_two,
            'final_price' => $final_price,
            'step3_details' => [
                'addition_amount' => $addition_amount_step3,
                'sub_amount' => $sub_amount_step3
            ],
            'step4_details' => [
                'addition_amount' => $addition_amount_step4,
                'sub_amount' => $sub_amount_step4
            ],
            'qa_details' => $qa_details,
            'no_question_details' => $no_question_details,
            'yes_question_details' => $yes_question_details,
            'processed_answers' => $processed_qa_answers,
            'field_answers_structure' => 'grouped',
            'field_answers_flat_count' => count($field_answers_flat),
            'field_answers_groups' => isset($form_data['field_answers']) ? array_keys($form_data['field_answers']) : []
        ];
    }

      public function putIntoCart(Request $request)
    {
        try {
            // Check if user is logged in
            if (!Session::has('user_id')) {
                return response()->json([
                    'success' => false,
                    'message' => 'User not logged in'
                ], 401);
            }
            
            $user_id = Session::get('user_id');
            
            // Get raw POST payload
            $payload = $request->getContent();
            
            if (empty($payload)) {
                return response()->json([
                    'success' => false,
                    'message' => 'No data received'
                ], 400);
            }
            
            // Decode JSON
            $data = json_decode($payload, true);
            
            if (json_last_error() !== JSON_ERROR_NONE) {
                return response()->json([
                    'success' => false,
                    'message' => 'Invalid JSON format'
                ], 400);
            }
            
            // Required fields
            if (!isset($data['model_id']) || !isset($data['final_price'])) {
                return response()->json([
                    'success' => false,
                    'message' => 'Missing model_id or final_price'
                ], 400);
            }
            
            $model_id = (int)$data['model_id'];
            $final_price = (float)$data['final_price'];
            
            // Extract capacity correctly - full string like "4GB/64GB"
            $capacity = null;
            if (isset($data['capacity']) && is_string($data['capacity']) && trim($data['capacity']) !== '') {
                $capacity = trim($data['capacity']);
            }
            
            // Store full JSON payload in item_name column
            $item_name_json = $payload;
            
            // Begin transaction
            DB::beginTransaction();
            
            try {
                // Check if a pending item already exists for this user + model_id
                $existingItem = DB::table('partial_order_items')
                    ->where('user_id', $user_id)
                    ->where('model_id', $model_id)
                    ->where('status', 'pending')
                    ->select('id', 'order_id', DB::raw("'partial' as source"))
                    ->first();
                
                if (!$existingItem) {
                    $existingItem = DB::table('order_items')
                        ->where('user_id', $user_id)
                        ->where('model_id', $model_id)
                        ->where('status', 'pending')
                        ->select('id', 'order_id', DB::raw("'order' as source"))
                        ->first();
                }
                
                if ($existingItem) {
                    // UPDATE existing record
                    $table = $existingItem->source === 'partial' ? 'partial_order_items' : 'order_items';
                    $existing_id = $existingItem->id;
                    $existing_order_id = $existingItem->order_id;
                    
                    DB::table($table)
                        ->where('id', $existing_id)
                        ->update([
                            'item_name' => $item_name_json,
                            'price' => $final_price,
                            'quantity_price' => $final_price,
                            'capacity' => $capacity,
                            'updated_at' => now()
                        ]);
                    
                    DB::commit();
                    
                    return response()->json([
                        'success' => true,
                        'message' => 'Cart updated successfully',
                        'order_id' => $existing_order_id,
                        'action' => 'updated',
                        'table' => $table
                    ]);
                    
                } else {
                    // INSERT new record into partial_order_items
                    $order_id = str_pad(mt_rand(1, 999999999999), 12, '0', STR_PAD_LEFT);
                    Session::put('current_order_id', $order_id);
                    
                    DB::table('partial_order_items')->insert([
                        'order_id' => $order_id,
                        'user_id' => $user_id,
                        'model_id' => $model_id,
                        'item_name' => $item_name_json,
                        'price' => $final_price,
                        'quantity_price' => $final_price,
                        'capacity' => $capacity,
                        'status' => 'pending',
                        'created_at' => now(),
                        'updated_at' => now()
                    ]);
                    
                    DB::commit();
                    
                    return response()->json([
                        'success' => true,
                        'message' => 'Item added to cart successfully',
                        'order_id' => $order_id,
                        'action' => 'inserted',
                        'table' => 'partial_order_items'
                    ]);
                }
                
            } catch (\Exception $e) {
                DB::rollBack();
                throw $e;
            }
            
        } catch (\Exception $e) {
            
            return response()->json([
                'success' => false,
                'message' => $e->getMessage()
            ], 500);
        }
    }

   public function cart()
{
    // Get cart data from session or database
    $cartData = session()->get('cart_data');

    if (!$cartData) {
        $userId = Session::get('user_id');

        if ($userId) {
            $cartItem = DB::table('partial_order_items')
                ->where('user_id', $userId)
                ->where('status', 'pending')
                ->orderBy('id', 'desc')
                ->first();

            if ($cartItem) {

                $itemNameData = json_decode($cartItem->item_name, true);

                // Generate readable assessment text
                $formattedItemName = "Condition Assessment\n\n";

                if (!empty($itemNameData['qa_details'])) {
                    foreach ($itemNameData['qa_details'] as $qa) {
                        $formattedItemName .= ucfirst($qa['processed_answer']) . "\n";
                        $formattedItemName .= $qa['question_name'] . "\n\n";
                    }
                }

                $formattedItemName .= "Reported Issues / Conditions\n\n";

                if (!empty($itemNameData['yes_question_details'])) {
                    foreach ($itemNameData['yes_question_details'] as $issue) {
                        $formattedItemName .= $issue['label'] . "\n";
                    }
                }

                $cartData = [
                    'id' => $cartItem->id,
                    'model_id' => $cartItem->model_id,
                    'final_price' => $cartItem->price,
                    'capacity' => $cartItem->capacity,
                    'order_id' => $cartItem->order_id,
                    'item_name' => trim($formattedItemName),
                    'item_name_json' => $itemNameData,
                    'item_name_raw' => $cartItem->item_name
                ];
            }
        }
    }

    // Get model details
    $model = null;
    $brand = null;
    $modelImg = null;

    if ($cartData && isset($cartData['model_id'])) {
        $model = DB::table('model')
            ->where('id', $cartData['model_id'])
            ->first();

        if ($model) {
            $brand = DB::table('brand')
                ->where('id', $model->brand_id)
                ->first();

            $modelImg = $model->model_img
                ? asset('media/images/model/' . $model->model_img)
                : null;
        }
    }
    return view('cart', compact('cartData', 'model', 'brand', 'modelImg'));
}

public function getUserDetails()
{
    try {
        if (!Session::has('user_id')) {
            return response()->json([
                'success' => false,
                'message' => 'User not logged in'
            ], 401);
        }
        
        $userId = Session::get('user_id');
        
        $user = DB::table('users')
            ->select('id', 'name', 'phone', 'email', 'alternate_mob_no')
            ->where('id', $userId)
            ->first();
        
        $address = DB::table('address')
            ->select('address_type', 'address', 'landmark', 'state', 'pincode')
            ->where('user_id', $userId)
           
            ->first();
        
        return response()->json([
            'success' => true,
            'data' => [
                'name' => $user->name ?? '',
                'phone' => $user->phone ?? '',
                'email' => $user->email ?? '',
                'other_phone' => $user->other_phone ?? '',
                'address' => $address
            ]
        ]);
        
    } catch (\Exception $e) {
        return response()->json([
            'success' => false,
            'message' => $e->getMessage()
        ], 500);
    }
}

/**
 * Submit sell order
 */
public function submitSellOrder(Request $request)
{
    try {
        if (!Session::has('user_id')) {
            return response()->json([
                'success' => false,
                'message' => 'User not logged in'
            ], 401);
        }
        
        $userId = Session::get('user_id');
        
        // Validate required fields
        $required = ['partial_order_item_id', 'model_id', 'order_id', 'name', 'mobile_no', 'email', 
                     'address_type', 'state', 'pincode', 'address', 'pickup_date', 'pickup_time', 'payment_method'];
        
        foreach ($required as $field) {
            if (empty($request->input($field))) {
                return response()->json([
                    'success' => false,
                    'message' => "Missing field: $field"
                ], 400);
            }
        }
        
        $partialOrderItemId = $request->input('partial_order_item_id');
        $modelId = $request->input('model_id');
        $orderId = $request->input('order_id');
        
        // Verify partial order item
        $partialItem = DB::table('partial_order_items')
            ->where('id', $partialOrderItemId)
            ->where('user_id', $userId)
            ->where('model_id', $modelId)
            ->where('order_id', $orderId)
            ->where('status', 'pending')
            ->first();
        
        if (!$partialItem) {
            return response()->json([
                'success' => false,
                'message' => 'Invalid order item'
            ], 400);
        }
        
        $itemNameJson = $partialItem->item_name;
        $price = floatval($partialItem->price);
        $quantityPrice = floatval($partialItem->quantity_price);
        $capacity = $partialItem->capacity;
        
        // Update user
        DB::table('users')
            ->where('id', $userId)
            ->update([
                'name' => $request->input('name'),
                'email' => $request->input('email'),
                'other_phone' => $request->input('alternate_mob_no'),
                'updated_at' => now()
            ]);
        
        // Handle address
        $addressType = $request->input('address_type');
        $existingAddress = DB::table('address')
            ->where('user_id', $userId)
            ->where('address_type', $addressType)
            ->first();
        
        if ($existingAddress) {
            DB::table('address')
                ->where('id', $existingAddress->id)
                ->update([
                    'name' => $request->input('name'),
                    'mobile_no' => $request->input('mobile_no'),
                    'alternate_mob_no' => $request->input('alternate_mob_no'),
                    'landmark' => $request->input('landmark'),
                    'state' => $request->input('state'),
                    'pincode' => $request->input('pincode'),
                    'address' => $request->input('address'),
                    'updated_at' => now()
                ]);
            $addressId = $existingAddress->id;
        } else {
            $addressId = DB::table('address')->insertGetId([
                'user_id' => $userId,
                'name' => $request->input('name'),
                'mobile_no' => $request->input('mobile_no'),
                'alternate_mob_no' => $request->input('alternate_mob_no'),
                'landmark' => $request->input('landmark'),
                'address_type' => $addressType,
                'state' => $request->input('state'),
                'pincode' => $request->input('pincode'),
                'address' => $request->input('address'),
                'created_at' => now()
            ]);
        }
        
        // Create order
        $allDetailsJson = json_encode($request->all());
        $paymentMethod = $request->input('payment_method');
        
        $orderData = [
            'user_id' => $userId,
            'order_id' => $orderId,
            'payment_method' => $paymentMethod,
            'shipping_pickup_date' => $request->input('pickup_date'),
            'status' => 'pending',
            'sales_pack' => 'we_come_for_you',
            'address_id' => $addressId,
            'shipping_pickup_time' => $request->input('pickup_time'),
            'email_while_add_to_cart' => $request->input('email'),
            'all_details' => $allDetailsJson,
            'created_at' => now()
        ];
        
        $orderMainId = DB::table('orders')->insertGetId($orderData);
        
        // Insert order items
        DB::table('order_items')->insert([
            'order_id' => $orderId,
            'user_id' => $userId,
            'model_id' => $modelId,
            'item_name' => $itemNameJson,
            'price' => $price,
            'quantity_price' => $quantityPrice,
            'capacity' => $capacity,
            'status' => 'pending',
            'created_at' => now(),
            'updated_at' => now()
        ]);
        
        // Update partial order item status
        DB::table('partial_order_items')
            ->where('id', $partialOrderItemId)
            ->update([
                'status' => 'completed',
                'updated_at' => now()
            ]);
        
      DB::commit();
        
        // Trigger webhook (optional)
        // $this->triggerNewOrderWebhook($orderId);
        
        return response()->json([
            'success' => true,
            'order_id' => $orderId,
            'order_main_id' => $orderMainId
        ]);
        
    } catch (\Exception $e) {
        DB::rollBack();
        
        return response()->json([
            'success' => false,
            'message' => $e->getMessage()
        ], 500);
    }
}

}