@extends('layouts.app')

{{-- Inject Dynamic SEO Meta tags into Layout Hooks --}}
@section('title', $stock->meta_title ?? $stock->model_title . ' (' . $stock->capacity . ') - Buy Refurbished')
@section('meta_description', $stock->meta_description ?? 'Buy a high-quality refurbished ' . $stock->model_title . ' at an unbeatable price.')
@section('meta_keywords', $stock->meta_keywords ?? 'refurbished phone, second hand ' . $stock->model_title . ', RevoDevice')
@section('canonical_url', !empty($stock->canonical_url) ? $stock->canonical_url : url()->current())

@push('styles')
<style>
    .product-container { 
        max-width: 1200px; 
        margin: 40px auto; 
        display: grid; 
        grid-template-columns: 1.2fr 1fr; 
        gap: 50px; 
        background: #111118; 
        border: 1px solid #1e1e2a; 
        border-radius: 24px; 
        padding: 40px; 
    }
    
    /* Left Side Thumbnail Stack Configuration */
    .media-gallery-showcase { display: flex; gap: 20px; height: 500px; }
    .thumbnail-track-column { display: flex; flex-direction: column; gap: 12px; overflow-y: auto; width: 85px; padding-right: 4px; }
    .thumbnail-track-column::-webkit-scrollbar { width: 4px; }
    .thumbnail-track-column::-webkit-scrollbar-thumb { background: #2a2a3a; border-radius: 4px; }
    
    .thumb-nav-card { 
        width: 75px; 
        height: 75px; 
        object-fit: cover; 
        border-radius: 10px; 
        border: 2px solid #1e1e2a; 
        background: #0c0c14; 
        cursor: pointer; 
        transition: 0.2s; 
        flex-shrink: 0; 
    }
    .thumb-nav-card:hover, .thumb-nav-card.active { border-color: #ec4899; }
    
    .main-display-viewport { 
        flex: 1; 
        background: #0c0c14; 
        border: 1px solid #1e1e2a; 
        border-radius: 16px; 
        display: flex; 
        align-items: center; 
        justify-content: center; 
        position: relative; 
        overflow: hidden; 
    }
    .main-display-viewport img, .main-display-viewport video { max-width: 100%; max-height: 100%; object-fit: contain; }

    /* Right Side Information Column Specs */
    .product-details-column { display: flex; flex-direction: column; justify-content: center; text-align: left; }
    .product-title { font-size: 32px; font-weight: 700; color: white; margin: 0 0 10px 0; }
    .spec-badge-row { display: flex; gap: 10px; margin-bottom: 25px; flex-wrap: wrap; }
    .spec-badge { background: #1a1a2e; border: 1px solid #2a2a3a; color: #94a3b8; padding: 6px 14px; border-radius: 20px; font-size: 13px; font-weight: 600; }
    .spec-badge span { color: #ec4899; }
    
    .pricing-bracket { margin-bottom: 30px; border-bottom: 1px solid #1e1e2a; padding-bottom: 20px; }
    .selling-price { font-size: 38px; font-weight: 800; color: white; display: flex; align-items: center; gap: 10px; }
    .selling-price small { font-size: 14px; color: #64748b; font-weight: 400; text-decoration: line-through; }
    
    .hardware-grid-specs { display: grid; grid-template-columns: 1fr 1fr; gap: 20px; margin-bottom: 35px; }
    .spec-info-box { background: rgba(30, 30, 45, 0.3); border: 1px dashed #2a2a3a; padding: 14px 18px; border-radius: 12px; }
    .spec-info-box label { display: block; font-size: 12px; color: #64748b; font-weight: 700; text-transform: uppercase; margin-bottom: 4px; }
    .spec-info-box p { margin: 0; font-size: 15px; color: #cbd5e1; font-weight: 600; }

    .buy-now-action-btn { 
        background: linear-gradient(135deg, #ec4899, #8b5cf6); 
        color: white; 
        border: none; 
        padding: 16px 32px; 
        font-size: 18px; 
        font-weight: 700; 
        border-radius: 12px; 
        cursor: pointer; 
        transition: 0.2s; 
        display: inline-flex; 
        align-items: center; 
        justify-content: center; 
        gap: 10px; 
        box-shadow: 0 10px 20px -5px rgba(236, 72, 153, 0.4); 
    }
    .buy-now-action-btn:hover { opacity: 0.95; transform: translateY(-2px); }

    /* Recommendation Showcase Engine Footer CSS */
 /* Recommendation Showcase Engine Footer CSS */
    .recommendations-section { max-width: 1200px; margin: 60px auto; text-align: left; }
    .section-header-title { font-size: 22px; font-weight: 700; color: white; margin-bottom: 25px; display: flex; align-items: center; gap: 10px; }
    
    /* Updated Grid Layout for Desktop/Tablet */
    .recommendations-grid { 
        display: grid; 
        grid-template-columns: repeat(auto-fill, minmax(220px, 1fr)); 
        gap: 20px; 
    }
    
    .rec-card { background: #111118; border: 1px solid #1e1e2a; border-radius: 18px; padding: 18px; transition: 0.2s; display: flex; flex-direction: column; justify-content: space-between; text-decoration: none; color: inherit; }
    .rec-card:hover { border-color: #3b82f6; transform: translateY(-3px); }
    .rec-img-holder { width: 100%; height: 180px; background: #0c0c14; border-radius: 12px; display: flex; align-items: center; justify-content: center; margin-bottom: 15px; overflow: hidden; }
    .rec-img-holder img { max-width: 90%; max-height: 90%; object-fit: contain; }
    .rec-title { font-size: 16px; font-weight: 600; color: white; margin: 0 0 6px 0; }
    .rec-capacity { color: #64748b; font-size: 13px; margin-bottom: 12px; }
    .rec-footer { display: flex; justify-content: space-between; align-items: center; margin-top: auto; }
    .rec-price { font-size: 18px; font-weight: 700; color: white; }
    .rec-btn { background: #1a1a2e; border: 1px solid #2a2a3a; color: #cbd5e1; padding: 8px 14px; border-radius: 8px; font-size: 12px; font-weight: 600; transition: 0.2s; }
    .rec-card:hover .rec-btn { background: #ec4899; color: white; border-color: #ec4899; }

    /* Media Queries for Responsive Views */
    @media (max-width: 992px) {
        .product-container { grid-template-columns: 1fr; padding: 20px; margin: 20px auto; }
        .media-gallery-showcase { height: 400px; }
    }

    /* FIX: Force exactly 2 columns inline on mobile view ports */
    @media (max-width: 576px) {
        .recommendations-grid {
            grid-template-columns: repeat(2, 1fr) !important; /* Force a strict 2-column structure */
            gap: 12px; /* Tighten up spacing layout slightly for mobile */
        }
        .rec-card {
            padding: 12px; /* Reduce internal padding to save viewport space */
            border-radius: 12px;
        }
        .rec-img-holder {
            height: 130px; /* Decrease height so items don't stretch vertically on mobile */
        }
        .rec-title {
            font-size: 14px; /* Slightly smaller typography fitment */
        }
        .rec-capacity {
            font-size: 11px;
        }
        .rec-price {
            font-size: 15px;
        }
        .rec-btn {
            display: none; /* Hide the textual view button to keep mobile screens clean and compact */
        }
    }
</style>
@endpush

@section('content')
<div class="container">
    <div class="product-container">
        
        <div class="media-gallery-showcase">
            <div class="thumbnail-track-column">
                @foreach($mediaGallery as $index => $mediaPath)
                    @if(preg_match('/\.(mp4|webm|avi|mov)$/i', $mediaPath))
                        <video src="{{ asset($mediaPath) }}" class="thumb-nav-card {{ $index === 0 ? 'active' : '' }}" onclick="updateStageMedia(this, true)" muted></video>
                    @else
                        <img src="{{ asset($mediaPath) }}" class="thumb-nav-card {{ $index === 0 ? 'active' : '' }}" onclick="updateStageMedia(this, false)">
                    @endif
                @endforeach
            </div>
            
            <div class="main-display-viewport" id="stage_viewport_frame">
                @if(preg_match('/\.(mp4|webm|avi|mov)$/i', $mediaGallery[0] ?? ''))
                    <video src="{{ asset($mediaGallery[0] ?? '') }}" id="stage_media_element" controls autoplay muted></video>
                @else
                    <img src="{{ asset($mediaGallery[0] ?? '') }}" id="stage_media_element">
                @endif
            </div>
        </div>

        <div class="product-details-column">
            <h1 class="product-title">{{ $stock->model_title }}</h1>
            
            <div class="spec-badge-row">
                <div class="spec-badge"><i class="fas fa-microchip"></i> Config: <span>{{ $stock->capacity }}</span></div>
                <div class="spec-badge"><i class="fas fa-palette"></i> Color: <span>{{ $stock->color }}</span></div>
            </div>

            <div class="pricing-bracket">
                @php
                    $sellingPrice = $stock->buy_price + ($stock->buy_price * ($stock->profit_percent_user / 100));
                    $originalPrice = $stock->buy_price * 1.4;
                @endphp
                <div class="selling-price">
                    ₹{{ number_format($sellingPrice, 2) }}
                    <small>₹{{ number_format($originalPrice, 2) }}</small>
                </div>
                <p style="color: #10b981; font-size: 13px; font-weight: 600; margin: 6px 0 0 0;"><i class="fas fa-check-circle"></i> In Stock & Ready to Ship</p>
            </div>

            <div class="hardware-grid-specs">
                <div class="spec-info-box">
                    <label>Warranty Period</label>
                    <p>{{ ucwords($stock->warranty) }}</p>
                </div>
                <div class="spec-info-box">
                    <label>Quality Status</label>
                    <p>{{ ucwords($stock->quality ?? 'Excellent') }}</p>
                </div>
            </div>

            <button class="buy-now-action-btn" onclick="executeInstantCheckout('{{ $stock->id }}')">
                <i class="fas fa-bolt"></i> Secure Instant Buy Now
            </button>
        </div>
    </div>

    <div class="recommendations-section">
        <div class="section-header-title">
            <i class="fas fa-sparkles" style="color: #eab308;"></i> You May Also Like
        </div>
        <div class="recommendations-grid">
            @forelse($relatedStocks as $rel)
                @php 
                    $thumbSrc = !empty($rel->model_img) ? $rel->model_img : 'placeholder.png';
                    $calculatedPrice = $rel->buy_price + ($rel->buy_price * ($rel->profit_percent_user / 100));
                @endphp
                <a href="/buy-refurbished-mobile-phones/buy-{{ $rel->sef_url }}" class="rec-card">
                    <div>
                        <div class="rec-img-holder">
                            <img src="{{ asset($thumbSrc) }}" alt="{{ $rel->model_title }}">
                        </div>
                        <h3 class="rec-title">{{ $rel->model_title }}</h3>
                        <div class="rec-capacity">{{ $rel->capacity }} • {{ $rel->color }}</div>
                    </div>
                    <div class="rec-footer">
                        <div class="rec-price">₹{{ number_format($calculatedPrice, 2) }}</div>
                        <div class="rec-btn">View Details</div>
                    </div>
                </a>
            @empty
                <p style="color:#64748b; font-size:14px;">No complementary alternative hardware setups found at this time.</p>
            @endforelse
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
    function updateStageMedia(element, isVideo) {
        document.querySelectorAll('.thumb-nav-card').forEach(el => el.classList.remove('active'));
        element.classList.add('active');
        
        var sourceUrl = element.getAttribute('src');
        var viewport = document.getElementById('stage_viewport_frame');
        
        if (isVideo) {
            viewport.innerHTML = `<video src="${sourceUrl}" id="stage_media_element" controls autoplay muted></video>`;
        } else {
            viewport.innerHTML = `<img src="${sourceUrl}" id="stage_media_element">`;
        }
    }

    function executeInstantCheckout(stockId) {
        alert("Proceeding to checkout module for Stock ID: " + stockId + ". This payload route logic will be hooked up in the next phase!");
    }
</script>
@endpush