@php
    use Illuminate\Support\Facades\DB;
    use Illuminate\Support\Str;

    // Get top 10 published brands
    $topBrands = DB::table('brand')
        ->where('published', 1)
        ->orderBy('id', 'asc')
        ->limit(10)
        ->get(['id', 'title', 'sef_url', 'image']);

    // Add image URL
    foreach ($topBrands as $brand) {
        $brand->image_url = $brand->image
            ? asset('media/images/brand/' . $brand->image)
            : asset('media/images/brand/default-brand.webp');
    }
@endphp

@if($topBrands->count() > 0)

<div style="margin-bottom: 60px;">

    <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 30px; padding: 0 5px;">
        <div>
            <h3 style="font-size: 24px; font-weight: 700; color: white; margin: 0 0 4px 0;">Top Brands</h3>
            <div style="width: 40px; height: 3px; background: #3b82f6; border-radius: 2px;"></div>
        </div>

        <a href="{{ url('/sell-old-phone') }}" class="premium-view-all">
            View all <span>→</span>
        </a>
    </div>

    <div class="brands-grid">
        @foreach($topBrands as $brand)
            <a href="{{ url('/sell-old-phone/sell-' . ($brand->sef_url ?: Str::slug($brand->title))) }}" class="brand-link">
                <div class="brand-box">
                    
                    <div class="brand-glow-accent"></div>

                    <div class="brand-image-wrap">
                        @if(!empty($brand->image))
                            <img src="{{ $brand->image_url }}" alt="{{ $brand->title }}" class="brand-image">
                        @else
                            <i class="fas fa-building brand-icon"></i>
                        @endif
                    </div>

                    <h4 class="brand-title">{{ $brand->title }}</h4>
                    
                    <div class="brand-active-bar"></div>
                </div>
            </a>
        @endforeach
    </div>
</div>

<style>
    /* Premium Heading View All Animation */
    .premium-view-all {
        color: #64748b; 
        text-decoration: none; 
        font-weight: 600; 
        font-size: 14px;
        display: flex;
        align-items: center;
        gap: 6px;
        transition: color 0.3s cubic-bezier(0.25, 0.8, 0.25, 1);
    }
    .premium-view-all span {
        transition: transform 0.3s cubic-bezier(0.25, 0.8, 0.25, 1);
    }
    .premium-view-all:hover {
        color: #3b82f6;
    }
    .premium-view-all:hover span {
        transform: translateX(4px);
    }

    /* Core Layout Structure Layout Configuration */
    .brands-grid {
        display: grid;
        grid-template-columns: repeat(5, 1fr);
        gap: 20px;
    }

    .brand-link {
        text-decoration: none;
        color: inherit;
        display: block;
    }

    /* High-End Micro Interactive Brand Box Card Framework */
    .brand-box {
        background: #111118;
        border: 1px solid #1e1e2a;
        border-radius: 20px;
        padding: 28px 20px;
        text-align: center;
        position: relative;
        overflow: hidden;
        display: flex;
        flex-direction: column;
        align-items: center;
        justify-content: center;
        height: 100%;
        box-sizing: border-box;
        transition: background 0.4s cubic-bezier(0.25, 0.8, 0.25, 1),
                    border-color 0.4s cubic-bezier(0.25, 0.8, 0.25, 1),
                    transform 0.4s cubic-bezier(0.25, 0.8, 0.25, 1),
                    box-shadow 0.4s cubic-bezier(0.25, 0.8, 0.25, 1);
    }

    /* Background Corner Ambient Glow Light */
    .brand-glow-accent {
        position: absolute;
        top: -30px;
        right: -30px;
        width: 60px;
        height: 60px;
        background: radial-gradient(circle, rgba(59,130,246,0.15) 0%, rgba(0,0,0,0) 70%);
        border-radius: 50%;
        transition: transform 0.5s ease;
        pointer-events: none;
    }

    /* Animated Luxury Bottom Glow Border Underline */
    .brand-active-bar {
        position: absolute;
        bottom: 0;
        left: 50%;
        width: 0;
        height: 2.5px;
        background: linear-gradient(90deg, #3b82f6, #60a5fa);
        border-radius: 2px;
        transition: width 0.4s cubic-bezier(0.25, 0.8, 0.25, 1), 
                    left 0.4s cubic-bezier(0.25, 0.8, 0.25, 1);
    }

    /* Master Hover Animation Bindings */
    .brand-link:hover .brand-box {
        transform: translateY(-6px);
        background: linear-gradient(180deg, #161622 0%, #111118 100%);
        border-color: rgba(59, 130, 246, 0.4);
        box-shadow: 0 20px 30px -12px rgba(5, 5, 8, 0.7),
                    0 4px 15px -3px rgba(59, 130, 246, 0.15);
    }

    .brand-link:hover .brand-glow-accent {
        transform: scale(2.5);
    }

    .brand-link:hover .brand-active-bar {
        width: 40%;
        left: 30%;
    }

    /* Image Scaling Interaction Architecture */
    .brand-image-wrap {
        width: 75px;
        height: 75px;
        margin-bottom: 16px;
        display: flex;
        align-items: center;
        justify-content: center;
        position: relative;
        z-index: 1;
    }

    .brand-image {
        max-width: 65px;
        max-height: 65px;
        object-fit: contain;
        filter: grayscale(15%) brightness(95%);
        transition: transform 0.4s cubic-bezier(0.25, 0.8, 0.25, 1),
                    filter 0.4s cubic-bezier(0.25, 0.8, 0.25, 1);
    }

    .brand-link:hover .brand-image {
        transform: scale(1.08);
        filter: grayscale(0%) brightness(105%);
    }

    .brand-icon {
        font-size: 42px;
        color: #475569;
        transition: color 0.4s ease;
    }
    .brand-link:hover .brand-icon {
        color: #3b82f6;
    }

    /* Typography Clean Rules */
    .brand-title {
        font-size: 15px;
        font-weight: 600;
        margin: 0;
        color: #94a3b8;
        position: relative;
        z-index: 1;
        transition: color 0.4s ease;
    }
    .brand-link:hover .brand-title {
        color: #ffffff;
    }

    /* --- Media Queries and Responsiveness Matrix --- */
    
    /* Desktop Viewports */
    @media (min-width: 1025px) {
        .brands-grid {
            grid-template-columns: repeat(5, 1fr);
        }
    }

    /* Tablets and Medium Display Screens */
    @media (min-width: 769px) and (max-width: 1024px) {
        .brands-grid {
            grid-template-columns: repeat(3, 1fr);
            gap: 18px;
        }
        .brand-box {
            padding: 24px 15px;
        }
    }

    /* Standalone Mobile Devices Grid */
    @media (min-width: 481px) and (max-width: 768px) {
        .brands-grid {
            grid-template-columns: repeat(2, 1fr);
            gap: 16px;
        }
        .brand-box {
            padding: 22px 15px;
        }
    }

    /* Small Handheld Screen Forms optimization */
    @media (max-width: 480px) {
        .brands-grid {
            grid-template-columns: repeat(2, 1fr);
            gap: 12px;
        }
        
        .brand-box {
            padding: 18px 12px;
            border-radius: 16px;
        }
        
        .brand-image-wrap {
            width: 55px;
            height: 55px;
            margin-bottom: 10px;
        }
        
        .brand-image {
            max-width: 48px;
            max-height: 48px;
        }
        
        .brand-icon {
            font-size: 32px;
        }
        
        .brand-title {
            font-size: 13px;
        }

        .brand-link:hover .brand-active-bar {
            width: 50%;
            left: 25%;
        }
    }
</style>

@endif