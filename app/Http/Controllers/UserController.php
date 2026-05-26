<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

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
    // Get brand by slug
    $brand = DB::table('brand')
        ->where('title', $slug)
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
}