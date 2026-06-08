@extends('layouts.app') {{-- Change this to your main layout file if different --}}

@section('content')
<div style="background-color: #050508; min-height: 100vh; padding: 40px 20px; color: #ffffff;">
    <div style="max-width: 1200px; margin: 0 auto;">
        
        <div style="display: flex; justify-content: space-between; align-items: flex-start; margin-bottom: 40px; flex-wrap: wrap; gap: 15px;">
            <div>
                <h1 style="font-size: 32px; font-weight: 800; margin: 0 0 6px 0; letter-spacing: -0.02em;">Best Selling Refurbished Devices</h1>
                <p style="color: #64748b; font-size: 15px; margin: 0; display: flex; align-items: center; gap: 8px;">
                    <i class="fas fa-shield-check" style="color: #3b82f6;"></i> Fully inspected, certified, and ready for dispatch.
                </p>
            </div>
            <div style="background: rgba(30, 30, 42, 0.4); border: 1px solid #1e1e2a; padding: 10px 20px; border-radius: 14px; font-size: 14px; color: #94a3b8;">
                Showing <span style="color: #3b82f6; font-weight: 700;">{{ $products->count() }}</span> live stock offers
            </div>
        </div>

        <div class="products-catalog-grid">
            @forelse($products as $product)
                @php
                    // Display Price calculation mechanism (sell_price fallback target to buy_price)
                    $displayPrice = $product->sell_price > 0 ? $product->sell_price : $product->buy_price;
                    
                    // Smart FontAwesome vector fallback framework trackers
                    $lowerTitle = Str::lower($product->model_title);
                    if (Str::contains($lowerTitle, ['macbook', 'laptop'])) {
                        $iconType = 'laptop';
                    } elseif (Str::contains($lowerTitle, ['ipad', 'tablet'])) {
                        $iconType = 'tablet-alt';
                    } else {
                        $iconType = 'mobile-alt';
                    }
                @endphp
                
                <div class="catalog-product-card">
                    <div class="catalog-image-frame">
                        @if(!empty($product->model_img))
                            <img src="{{ url('media/images/model/' . $product->model_img) }}" 
                                 alt="{{ $product->model_title }}" 
                                 class="catalog-core-img"
                                 onerror="this.style.display='none'; document.getElementById('catalog-fallback-{{ $product->id }}').style.display='block';">
                            
                            <i id="catalog-fallback-{{ $product->id }}" class="fas fa-{{ $iconType }} catalog-vector-fallback" style="display: none;"></i>
                        @else
                            <i class="fas fa-{{ $iconType }} catalog-vector-fallback"></i>
                        @endif
                        
                        <div class="catalog-status-badge">Certified Refurbished</div>
                    </div>
                    
                    <div style="padding: 24px; display: flex; flex-direction: column; justify-content: space-between; flex: 1;">
                        <div>
                            <h3 class="catalog-title-header">
                                {{ $product->model_title }}
                                <span class="catalog-title-capacity">({{ $product->capacity }})</span>
                            </h3>
                            
                            <p class="catalog-price-tag">
                                ₹{{ number_format($displayPrice, 2) }}
                            </p>
                            
                            <div class="catalog-meta-specs-row">
                                <span class="spec-item">
                                    <i class="fas fa-shield-alt"></i> {{ Str::title($product->warranty) }} Warranty
                                </span>
                                <span class="spec-item">
                                    <i class="fas fa-star" style="color: #eab308;"></i> Quality 4.5
                                </span>
                            </div>
                        </div>
                        
                        <div style="margin-top: 15px;">
                            <button class="catalog-action-buy-btn">
                                Buy Now <i class="fas fa-arrow-right" style="font-size: 11px;"></i>
                            </button>
                        </div>
                    </div>
                </div>
            @empty
                <div style="grid-column: span 4; background: #111118; border: 1px solid #1e1e2a; border-radius: 24px; padding: 60px 20px; text-align: center; color: #64748b;">
                    <i class="fas fa-box-open" style="font-size: 50px; color: #1e1e2a; margin-bottom: 20px;"></i>
                    <h3 style="color: white; font-size: 18px; margin: 0 0 8px 0;">No Premium Stocks Found</h3>
                    <p style="margin: 0; font-size: 14px;">We are currently updating our vault inventory listing. Check back soon!</p>
                </div>
            @endforelse
        </div>

        @if($products->hasPages())
            <div class="catalog-pagination-wrapper">
                {{ $products->links() }}
            </div>
        @endif

    </div>
</div>

<style>
    /* Framework Grid Layout rules structure definition */
    .products-catalog-grid {
        display: grid;
        grid-template-columns: repeat(4, 1fr);
        gap: 25px;
    }

    /* Core Product Card Premium Architecture */
    .catalog-product-card {
        background: #111118;
        border: 1px solid #1e1e2a;
        border-radius: 24px;
        overflow: hidden;
        display: flex;
        flex-direction: column;
        transition: transform 0.4s cubic-bezier(0.25, 0.8, 0.25, 1),
                    border-color 0.4s cubic-bezier(0.25, 0.8, 0.25, 1),
                    box-shadow 0.4s cubic-bezier(0.25, 0.8, 0.25, 1);
    }

    .catalog-product-card:hover {
        transform: translateY(-8px);
        border-color: #3b82f6;
        box-shadow: 0 25px 40px -15px rgba(59,130,246,0.25);
    }

    /* Top Image Module Block Framework */
    .catalog-image-frame {
        background: linear-gradient(135deg, #16143c, #0f172a);
        height: 220px;
        display: flex;
        align-items: center;
        justify-content: center;
        position: relative;
        overflow: hidden;
    }

    .catalog-core-img {
        max-width: 75%;
        max-height: 75%;
        object-fit: contain;
        transition: transform 0.4s cubic-bezier(0.25, 0.8, 0.25, 1);
    }

    .catalog-product-card:hover .catalog-core-img {
        transform: scale(1.06);
    }

    .catalog-vector-fallback {
        font-size: 80px; 
        color: white; 
        opacity: 0.7;
    }

    /* Floating Status Pill Tags */
    .catalog-status-badge {
        position: absolute;
        top: 15px;
        right: 15px;
        background: #10b981;
        color: white;
        padding: 5px 14px;
        border-radius: 30px;
        font-size: 11px;
        font-weight: 700;
        letter-spacing: 0.02em;
        box-shadow: 0 4px 10px rgba(16, 185, 129, 0.2);
    }

    /* Content Data Typography styles */
    .catalog-title-header {
        font-size: 18px; 
        font-weight: 700; 
        color: #ffffff;
        margin: 0 0 8px 0;
        line-height: 1.3;
    }

    .catalog-title-capacity {
        font-size: 13px;
        color: #64748b;
        font-weight: 400;
        display: inline-block;
        margin-left: 4px;
    }

    .catalog-price-tag {
        color: #3b82f6; 
        font-weight: 800; 
        font-size: 22px; 
        margin: 0 0 15px 0;
    }

    /* Metadata Specification Badging Systems Row */
    .catalog-meta-specs-row {
        display: flex; 
        gap: 12px; 
        font-size: 12px; 
        color: #94a3b8; 
        margin-bottom: 5px;
        flex-wrap: wrap;
    }

    .spec-item {
        display: inline-flex;
        align-items: center;
        gap: 6px;
        background: rgba(30, 30, 42, 0.5);
        padding: 4px 10px;
        border-radius: 8px;
        border: 1px solid rgba(255,255,255,0.02);
    }

    /* Interactive Functional Call-To-Action Link Button */
    .catalog-action-buy-btn {
        width: 100%; 
        padding: 12px; 
        background: #3b82f6; 
        border: none; 
        border-radius: 12px; 
        color: white; 
        font-weight: 700;
        font-size: 14px;
        cursor: pointer; 
        display: flex;
        align-items: center;
        justify-content: center;
        gap: 8px;
        transition: background 0.2s, transform 0.2s, box-shadow 0.2s;
    }

    .catalog-action-buy-btn:hover {
        background: #2563eb;
        transform: translateY(-1px);
        box-shadow: 0 8px 15px rgba(59,130,246,0.3);
    }

    /* Laravel Pagination Style Wrapper */
    .catalog-pagination-wrapper {
        margin-top: 50px;
        display: flex;
        justify-content: center;
    }
    
    /* Clean up framework default pagination styles for dark templates */
    .catalog-pagination-wrapper nav svg { width: 20px; }
    .catalog-pagination-wrapper nav div:first-child { display: none; }

    /* --- Media Responsive Viewports Breakthrough Settings --- */

    /* Standard Desktop screens */
    @media (max-width: 1200px) {
        .products-catalog-grid {
            grid-template-columns: repeat(3, 1fr);
            gap: 20px;
        }
    }

    /* Tablets / Medium horizontal displays */
    @media (max-width: 900px) {
        .products-catalog-grid {
            grid-template-columns: repeat(2, 1fr);
            gap: 16px;
        }
        .catalog-image-frame { height: 190px; }
        .catalog-title-header { font-size: 16px; }
        .catalog-price-tag { font-size: 20px; }
    }

    /* Small handheld mobile forms tracking */
    @media (max-width: 550px) {
        .products-catalog-grid {
            grid-template-columns: 1fr; /* Drop execution parameters down to a single card row stream */
            gap: 20px;
        }
        .catalog-image-frame { height: 210px; }
        h1 { font-size: 26px !important; }
    }
</style>
@endsection