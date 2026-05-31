@extends('layouts.app')

@section('title', $seo['title'])

@section('meta_description', $seo['meta_description'])

@section('meta_keywords', $seo['meta_keywords'])

@section('og_title', $seo['og_title'])

@section('og_description', $seo['og_description'])

@section('canonical_url', $seo['canonical_url'])

@section('content')
<div class="container" style="max-width: 1200px; margin: 0 auto; padding: 0 20px;">

    <!-- Breadcrumb -->
    <div style="padding: 20px 0; font-size: 14px;">
        <nav aria-label="breadcrumb">
            <ol style="display: flex; gap: 8px; list-style: none; flex-wrap: wrap;">
                <li><a href="{{ url('/') }}" style="color: #3b82f6; text-decoration: none;">Home</a></li>
                <li><i class="fas fa-chevron-right" style="font-size: 10px; color: #64748b;"></i></li>
                <li><a href="{{ url('/sell-old-phone') }}" style="color: #3b82f6; text-decoration: none;">Sell Mobile</a></li>
                <li><i class="fas fa-chevron-right" style="font-size: 10px; color: #64748b;"></i></li>
                <li><a href="{{ url('/sell-old-phone/sell-' . ($brand->sef_url ?? Str::slug($brand->title ?? ''))) }}" style="color: #3b82f6; text-decoration: none;">{{ $brand->title ?? 'Brand' }}</a></li>
                <li><i class="fas fa-chevron-right" style="font-size: 10px; color: #64748b;"></i></li>
                <li style="color: #64748b;">{{ $model->title }}</li>
            </ol>
        </nav>
    </div>

   <!-- Main Content -->
<div class="main-content-grid" style="display: grid; grid-template-columns: 1fr 1fr; gap: 40px; margin-bottom: 50px; align-items: start;">
    
    <!-- Left Column - Model Image -->
    <div class="model-image-box" style="background: #111118; border: 1px solid #2a2a3a; border-radius: 24px; padding: 30px; text-align: center;">
        @if($model->model_img && file_exists(public_path('media/images/model/' . $model->model_img)))
            <img 
                src="{{ $model->model_img_url }}" 
                alt="{{ $model->title }}" 
                class="model-image"
                style="max-width: 100%; max-height: 280px; width: auto; height: auto; object-fit: contain;"
            >
        @else
            <i class="fas fa-mobile-alt" style="font-size: 120px; color: #3b82f6;"></i>
        @endif
    </div>
    
    <!-- Right Column - Model Details -->
    <div class="model-details">

        <!-- Mobile Only Image -->
        <div class="mobile-image-wrapper" style="display:none; text-align:center; margin-bottom:25px;">
            @if($model->model_img && file_exists(public_path('media/images/model/' . $model->model_img)))
                <img 
                    src="{{ $model->model_img_url }}" 
                    alt="{{ $model->title }}" 
                    class="mobile-model-image"
                    style="max-width: 220px; width:100%; height:auto; object-fit:contain;"
                >
            @else
                <i class="fas fa-mobile-alt" style="font-size: 100px; color: #3b82f6;"></i>
            @endif
        </div>

        <!-- Brand -->
        @if($brand)
        <div style="margin-bottom: 15px;">
            <span style="background: #1a1a2e; padding: 5px 12px; border-radius: 20px; font-size: 12px; color: #3b82f6;">
                {{ $brand->title }}
            </span>
        </div>
        @endif
        
        <!-- Model Title -->
        <h1 style="font-size: 32px; font-weight: 800; color: white; margin-bottom: 15px;">
            {{ $model->title }}
        </h1>
        
        <!-- Get Best Price Above Variant -->
        <div style="background: linear-gradient(135deg, #1e293b, #0f172a); border-radius: 16px; padding: 20px; margin-bottom: 25px;">
            <div style="display: flex; align-items: center; justify-content: center; text-align: center; width: 100%;">
                <div>
                    <i class="fas fa-rupee-sign" style="font-size: 28px; color: #3b82f6;"></i>
                    <div>
                        <p style="color: #94a3b8; font-size: 13px; margin-bottom: 8px;">Get Best Price</p>

                        <p style="color: white; font-size: 24px; font-weight: 800;">
                            Upto ₹ 
                            <span id="selectedPrice" style="color: #3b82f6;">
                                {{ number_format($selectedVariant['upto_price'] ?? 0) }}
                            </span>
                        </p>
                    </div>
                </div>
            </div>
        </div>

        <!-- Choose Variant Section -->
        <div style="margin-bottom: 20px;">
            <h3 style="font-size: 16px; font-weight: 600; color: white; margin-bottom: 12px;">
                Select Storage Variant
            </h3>

            <div id="variantContainer" style="display: flex; gap: 12px; flex-wrap: wrap;">
                @foreach($variants as $index => $variant)
                    <button type="button" 
                            class="variant-btn" 
                            data-index="{{ $index }}"
                            data-capacity="{{ $variant['capacity'] }}"
                            data-upto-price="{{ $variant['upto_price'] }}"
                            data-base-price="{{ $variant['base_price'] }}"
                            data-slug="{{ Str::slug($variant['capacity']) }}"
                            style="
                                background: {{ $index == 0 ? '#3b82f6' : '#1a1a2e' }};
                                color: {{ $index == 0 ? 'white' : '#cbd5e1' }};
                                padding: 10px 20px;
                                border-radius: 40px;
                                border: 1px solid {{ $index == 0 ? '#3b82f6' : '#2a2a3a' }};
                                font-weight: 600;
                                cursor: pointer;
                                transition: all 0.3s;
                                font-size: 14px;
                            ">
                        {{ $variant['capacity'] }}
                    </button>

                @endforeach
                 <button id="proceedBtn" 
                class="proceed-btn"
                data-model-id="{{ $model->id }}"
                data-model-slug="{{ $model->sef_url }}"
                style="width: 100%; margin-top: 20px; background: linear-gradient(135deg, #3b82f6, #8b5cf6); color: white; padding: 16px; border-radius: 50px; border: none; font-weight: 700; font-size: 18px; cursor: pointer; transition: all 0.3s;">
                    Proceed to Sell →
                </button>
            </div>
        </div>
    </div>
</div>
  <!-- Proceed Button -->
  

    <!-- Description Section with HTML -->
    <div style="margin-bottom: 40px;">
        <div style="background: #111118; border: 1px solid #2a2a3a; border-radius: 20px; padding: 30px;">
            <h3 style="color: white; font-size: 20px; margin-bottom: 20px;">About {{ $model->title }}</h3>
            <div class="description-content" style="color: #cbd5e1; line-height: 1.8; font-size: 15px;">
                {!! $model->description !!}
            </div>
        </div>
    </div>

    <!-- Description CSS Fix -->
    <style>
        .description-content {
            word-wrap: break-word;
            overflow-wrap: break-word;
        }
        .description-content p {
            margin-bottom: 15px;
        }
        .description-content a {
            color: #3b82f6;
            text-decoration: none;
        }
        .description-content a:hover {
            text-decoration: underline;
        }
        .description-content img {
            max-width: 100% !important;
            height: auto !important;
            border-radius: 12px;
            margin: 15px 0;
        }
        .description-content b, .description-content strong {
            color: white;
        }
    </style>

  

    <!-- Why Sell Section -->
    <div class="why-sell-grid" style="display: grid; grid-template-columns: repeat(4, 1fr); gap: 20px; margin-bottom: 60px;">
        <div style="text-align: center; padding: 20px; background: #111118; border-radius: 16px;">
            <i class="fas fa-rupee-sign" style="font-size: 30px; color: #3b82f6; margin-bottom: 10px;"></i>
            <h4 style="color: white; font-size: 14px;">Best Price</h4>
        </div>
        <div style="text-align: center; padding: 20px; background: #111118; border-radius: 16px;">
            <i class="fas fa-truck-fast" style="font-size: 30px; color: #3b82f6; margin-bottom: 10px;"></i>
            <h4 style="color: white; font-size: 14px;">Free Pickup</h4>
        </div>
        <div style="text-align: center; padding: 20px; background: #111118; border-radius: 16px;">
            <i class="fas fa-clock" style="font-size: 30px; color: #3b82f6; margin-bottom: 10px;"></i>
            <h4 style="color: white; font-size: 14px;">Instant Cash</h4>
        </div>
        <div style="text-align: center; padding: 20px; background: #111118; border-radius: 16px;">
            <i class="fas fa-shield-alt" style="font-size: 30px; color: #3b82f6; margin-bottom: 10px;"></i>
            <h4 style="color: white; font-size: 14px;">Secure Data</h4>
        </div>
    </div>
</div>

<style>
    .variant-btn {
        transition: all 0.3s ease;
    }
    
    .variant-btn:hover {
        transform: translateY(-2px);
        box-shadow: 0 5px 15px rgba(59,130,246,0.3);
    }
    
    .proceed-btn {
        transition: all 0.3s ease;
    }
    
    .proceed-btn:hover {
        transform: translateY(-2px);
        box-shadow: 0 10px 25px -5px rgba(59,130,246,0.4);
    }
    
    /* Responsive Design */
    @media (max-width: 992px) {
        .container > div:first-of-type {
            grid-template-columns: 1fr 1fr !important;
            gap: 30px;
        }
        
        .why-sell-grid {
            grid-template-columns: repeat(4, 1fr) !important;
        }
    }
    
    @media (max-width: 768px) {
        .container > div:first-of-type {
            grid-template-columns: 1fr !important;
            gap: 25px;
        }
        
        .why-sell-grid {
            grid-template-columns: repeat(2, 1fr) !important;
            gap: 15px;
        }
        
        h1 {
            font-size: 24px !important;
        }
        
        .variant-btn {
            padding: 8px 16px !important;
            font-size: 12px !important;
        }
        
        .description-content {
            font-size: 14px !important;
        }
        
        .description-content img {
            width: 100% !important;
            height: auto !important;
        }
    }
    
    @media (max-width: 480px) {
        .container {
            padding: 0 15px !important;
        }
        
        .why-sell-grid {
            grid-template-columns: 1fr !important;
        }
        
        .proceed-btn {
            padding: 14px !important;
            font-size: 16px !important;
        }
        
        .description-content {
            font-size: 13px !important;
            line-height: 1.6 !important;
        }
    }
</style>

<style>
    @media (max-width: 992px) {
    .main-content-grid {
        grid-template-columns: 1fr 1fr !important;
        gap: 30px;
    }

    .why-sell-grid {
        grid-template-columns: repeat(4, 1fr) !important;
    }
}

@media (max-width: 768px) {

    .main-content-grid {
        grid-template-columns: 1fr !important;
        gap: 25px;
    }

    /* Hide desktop image box */
    .model-image-box {
        display: none;
    }

    /* Show mobile image */
    .mobile-image-wrapper {
        display: block !important;
    }

    .why-sell-grid {
        grid-template-columns: repeat(2, 1fr) !important;
        gap: 15px;
    }

    h1 {
        font-size: 24px !important;
        text-align: center;
    }

    .model-details {
        text-align: center;
    }

    #variantContainer {
        justify-content: center;
    }

    .variant-btn {
        padding: 8px 16px !important;
        font-size: 12px !important;
    }

    .description-content {
        font-size: 14px !important;
    }

    .description-content img {
        width: 100% !important;
        height: auto !important;
    }
}

@media (max-width: 480px) {

    .container {
        padding: 0 15px !important;
    }

    .why-sell-grid {
        grid-template-columns: 1fr !important;
    }

    .proceed-btn {
        padding: 14px !important;
        font-size: 16px !important;
    }

    .description-content {
        font-size: 13px !important;
        line-height: 1.6 !important;
    }

    .mobile-model-image {
        max-width: 180px !important;
    }
}
</style>

<script>
    document.addEventListener('DOMContentLoaded', function() {
        const variantBtns = document.querySelectorAll('.variant-btn');
        const selectedPriceSpan = document.getElementById('selectedPrice');
        const proceedBtn = document.getElementById('proceedBtn');
        
        let currentVariant = null;
        let currentVariantSlug = '';
        
        // Parse variants data from PHP
        const variants = @json($variants);
        
        // Set initial variant (first one)
        if (variants.length > 0) {
            currentVariant = variants[0];
            currentVariantSlug = generateSlug(variants[0]['capacity']);
        }
        
        // Variant button click handler
        variantBtns.forEach(btn => {
            btn.addEventListener('click', function() {
                // Update button styles
                variantBtns.forEach(b => {
                    b.style.background = '#1a1a2e';
                    b.style.color = '#cbd5e1';
                    b.style.border = '1px solid #2a2a3a';
                });
                this.style.background = '#3b82f6';
                this.style.color = 'white';
                this.style.border = '1px solid #3b82f6';
                
                // Get variant data
                const index = parseInt(this.getAttribute('data-index'));
                currentVariant = variants[index];
                currentVariantSlug = this.getAttribute('data-slug');
                
                // Update UI
                selectedPriceSpan.textContent = formatNumber(currentVariant['upto_price']);
            });
        });
        
        // Proceed button click handler
        proceedBtn.addEventListener('click', function() {
            if (!currentVariant) {
                alert('Please select a variant');
                return;
            }
            
            const modelSlug = this.getAttribute('data-model-slug');
            const variantSlug = currentVariantSlug;
            
            // Redirect to evaluate page
            const url = `/sell-old-mobile-phone/evaluate/${modelSlug}/${variantSlug}`;
            window.location.href = url;
        });
        
        // Helper function to format numbers
        function formatNumber(num) {
            return num.toString().replace(/\B(?=(\d{3})+(?!\d))/g, ",");
        }
        
        // Helper function to generate slug
        function generateSlug(capacity) {
            return capacity.toLowerCase()
                .replace(/\//g, '-')
                .replace(/\\/g, '-')
                .replace(/\s+/g, '-')
                .replace(/[^\w\-]+/g, '')
                .replace(/\-\-+/g, '-')
                .replace(/^-+/, '')
                .replace(/-+$/, '');
        }
    });
</script>
@endsection