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

<div style="margin-bottom: 50px;">

    <!-- Heading -->
    <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 25px;">

        <h3 style="font-size: 24px; font-weight: 700;">
            Top Brands
        </h3>

        <a href="{{ url('/sell-old-phone') }}"
           style="color: #3b82f6; text-decoration: none; font-weight: 500;">
            View all →
        </a>

    </div>

    <!-- Brands Grid -->
    <div class="brands-grid">

        @foreach($topBrands as $brand)

            <a href="{{ url('/sell-old-phone/sell-' . ($brand->sef_url ?: Str::slug($brand->title))) }}"
               class="brand-link">

                <div class="brand-box">

                    <!-- Brand Image -->
                    <div class="brand-image-wrap">

                        @if(!empty($brand->image))
                            <img
                                src="{{ $brand->image_url }}"
                                alt="{{ $brand->title }}"
                                class="brand-image"
                            >
                        @else
                            <i class="fas fa-building brand-icon"></i>
                        @endif

                    </div>

                    <!-- Brand Name -->
                    <h4 class="brand-title">
                        {{ $brand->title }}
                    </h4>

                </div>

            </a>

        @endforeach

    </div>

</div>

<style>

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

    .brand-box {
        background: #111118;
        border: 1px solid #2a2a3a;
        border-radius: 16px;
        padding: 20px 15px;
        text-align: center;
        transition: all .3s ease;
        cursor: pointer;
        height: 100%;
    }

    .brand-box:hover {
        transform: translateY(-5px);
        border-color: #3b82f6;
        background: #1a1a2e;
        box-shadow: 0 8px 20px -5px rgba(59,130,246,0.25);
    }

    .brand-image-wrap {
        width: 70px;
        height: 70px;
        margin: 0 auto 12px;
        display: flex;
        align-items: center;
        justify-content: center;
    }

    .brand-image {
        max-width: 60px;
        max-height: 60px;
        object-fit: contain;
    }

    .brand-icon {
        font-size: 40px;
        color: #3b82f6;
    }

    .brand-title {
        font-size: 16px;
        font-weight: 600;
        margin: 0;
        color: #e2e8f0;
    }

    /* Laptop / Desktop - 5 brands */
    @media (min-width: 1025px) {
        .brands-grid {
            grid-template-columns: repeat(5, 1fr);
        }
    }

    /* Tablet - 3 brands */
    @media (min-width: 769px) and (max-width: 1024px) {
        .brands-grid {
            grid-template-columns: repeat(3, 1fr);
        }
    }

    /* Mobile - 2 brands (shows 2 per row) */
    @media (min-width: 481px) and (max-width: 768px) {
        .brands-grid {
            grid-template-columns: repeat(2, 1fr);
            gap: 15px;
        }
    }

    /* Small Mobile - Still 2 brands, just smaller padding */
    @media (max-width: 480px) {
        .brands-grid {
            grid-template-columns: repeat(2, 1fr);
            gap: 12px;
        }
        
        .brand-box {
            padding: 12px 10px;
        }
        
        .brand-image-wrap {
            width: 50px;
            height: 50px;
        }
        
        .brand-image {
            max-width: 40px;
            max-height: 40px;
        }
        
        .brand-icon {
            font-size: 30px;
        }
        
        .brand-title {
            font-size: 13px;
        }
    }

</style>

@endif