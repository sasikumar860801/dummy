@php
    use Illuminate\Support\Facades\DB;
    use Illuminate\Support\Str;

    // Fetch premium stocks ordered by highest purchase price
    $tickerStocks = DB::table('stocks')
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
        ->limit(10)
        ->get();
@endphp

<div style="margin-bottom: 60px; overflow: hidden; width: 100%;">
    <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 25px; padding: 0 10px;">
        <div>
            <h3 style="font-size: 25px; font-weight: 700; color: white; margin: 0 0 4px 0;">Premium Vault Inventory</h3>
            <span style="color: #64748b; font-size: 13px; display: flex; align-items: center; gap: 6px;">
                <i class="fas fa-circle" style="color: #10b981; font-size: 8px; animation: pulse 1.5s infinite;"></i> Live Stock Track
            </span>
        </div>
        <a href="#" style="color: #3b82f6; text-decoration: none; font-size: 14px; font-weight: 600; display: flex; align-items: center; gap: 6px; transition: color 0.2s;" onmouseover="this.style.color='#60a5fa'" onmouseout="this.style.color='#3b82f6'">
            View All <i class="fas fa-arrow-right" style="font-size: 12px;"></i>
        </a>
    </div>
    
    <div class="marquee-wrapper">
        @if($tickerStocks->isNotEmpty())
            <div class="marquee-track">
                @foreach([1, 2] as $loopIndex)
                    <div class="marquee-group">
                        @foreach($tickerStocks as $product)
                            @php
                                $displayPrice = $product->sell_price > 0 ? $product->sell_price : $product->buy_price;
                                $lowerTitle = Str::lower($product->model_title);
                                if (Str::contains($lowerTitle, ['macbook', 'laptop'])) {
                                    $iconType = 'laptop';
                                } elseif (Str::contains($lowerTitle, ['ipad', 'tablet'])) {
                                    $iconType = 'tablet-alt';
                                } else {
                                    $iconType = 'mobile-alt';
                                }
                            @endphp
                            
                            <div class="ticker-card">
                                <div class="ticker-image-box">
                                    @if(!empty($product->model_img))
                                        <img src="{{ url('media/images/model/' . $product->model_img) }}" 
                                             alt="{{ $product->model_title }}"
                                             onerror="this.style.display='none'; document.getElementById('ticker-fallback-{{ $loopIndex }}-{{ $product->id }}').style.display='block';">
                                        <i id="ticker-fallback-{{ $loopIndex }}-{{ $product->id }}" class="fas fa-{{ $iconType }}" style="font-size: 44px; color: white; opacity: 0.7; display: none;"></i>
                                    @else
                                        <i class="fas fa-{{ $iconType }}" style="font-size: 44px; color: white; opacity: 0.7;"></i>
                                    @endif
                                </div>
                                <div style="flex: 1; min-width: 0; display: flex; flex-direction: column; justify-content: space-between; height: 100%;">
                                    <div>
                                        <h4 class="ticker-title">{{ $product->model_title }}</h4>
                                        <div style="font-size: 12px; color: #64748b; margin-bottom: 6px;">{{ $product->capacity }}</div>
                                    </div>
                                    
                                    <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 10px;">
                                        <span class="ticker-price">${{ number_format($displayPrice, 2) }}</span>
                                        <span class="ticker-badge"><i class="fas fa-shield-alt" style="font-size: 9px;"></i> {{ Str::before($product->warranty, ' ') }}</span>
                                    </div>

                                    <button class="ticker-buy-btn">
                                        Buy Now <i class="fas fa-shopping-cart" style="font-size: 11px;"></i>
                                    </button>
                                </div>
                            </div>
                        @endforeach
                    </div>
                @endforeach
            </div>
        @else
            <div style="background: #111118; border: 1px solid #1e1e2a; border-radius: 20px; padding: 40px; text-align: center; color: #64748b; width: 100%;">
                <p style="margin: 0; font-size: 14px;">No active stocks available to stream.</p>
            </div>
        @endif
    </div>
</div>

<style>
    /* Marquee Track Container setup */
    .marquee-wrapper {
        position: relative;
        width: 100%;
        overflow-x: hidden;
        padding: 15px 0;
        background: rgba(17, 17, 24, 0.3);
        border-radius: 24px;
        border: 1px solid rgba(30, 30, 42, 0.6);
    }
    
    .marquee-wrapper::before, .marquee-wrapper::after {
        content: "";
        position: absolute;
        top: 0;
        width: 120px;
        height: 100%;
        z-index: 2;
        pointer-events: none;
    }
    .marquee-wrapper::before { left: 0; background: linear-gradient(to right, #050508 0%, rgba(5,5,8,0) 100%); }
    .marquee-wrapper::after { right: 0; background: linear-gradient(to left, #050508 0%, rgba(5,5,8,0) 100%); }

    .marquee-track {
        display: flex;
        width: max-content;
        animation: scrollLeftToRight 32s linear infinite;
    }

    .marquee-track:hover {
        animation-play-state: paused;
    }

    .marquee-group {
        display: flex;
        align-items: center;
        gap: 25px;
        padding-right: 25px;
    }

    /* Resized Individual Card Architecture */
    .ticker-card {
        background: #111118;
        border: 1px solid #1e1e2a;
        border-radius: 20px;
        width: 310px; /* Increased from 280px */
        height: 135px; /* Added structured height layout mapping */
        padding: 18px; /* Increased padding */
        display: flex;
        align-items: center;
        gap: 18px;
        transition: border-color 0.3s, transform 0.3s, box-shadow 0.3s;
        text-align: left;
    }
    
    .ticker-card:hover {
        border-color: #3b82f6;
        transform: translateY(-3px);
        box-shadow: 0 12px 20px -8px rgba(59,130,246,0.25);
    }

    .ticker-image-box {
        background: linear-gradient(135deg, #1e1b4b, #0f172a);
        width: 80px; /* Increased from 65px */
        height: 80px; /* Increased from 65px */
        border-radius: 14px;
        display: flex;
        align-items: center;
        justify-content: center;
        overflow: hidden;
        flex-shrink: 0;
    }

    .ticker-image-box img {
        max-width: 85%;
        max-height: 85%;
        object-fit: contain;
    }

    .ticker-title {
        font-size: 15px; /* Increased sizing values */
        font-weight: 700;
        color: white;
        margin: 0 0 2px 0;
        white-space: nowrap;
        overflow: hidden;
        text-overflow: ellipsis;
    }

    .ticker-price {
        color: #3b82f6;
        font-weight: 800;
        font-size: 16px;
    }

    .ticker-badge {
        background: rgba(16, 185, 129, 0.1);
        color: #10b981;
        font-size: 10px;
        font-weight: 600;
        padding: 2px 8px;
        border-radius: 12px;
        white-space: nowrap;
    }

    /* Core Inline Action Buy Control Button */
    .ticker-buy-btn {
        width: 100%;
        padding: 6px 12px;
        background: #3b82f6;
        border: none;
        border-radius: 8px;
        color: white;
        font-size: 12px;
        font-weight: 600;
        cursor: pointer;
        display: flex;
        align-items: center;
        justify-content: center;
        gap: 6px;
        transition: background 0.2s, transform 0.2s;
    }

    .ticker-buy-btn:hover {
        background: #2563eb;
        transform: translateY(-1px);
    }

    /* Animation Keyframes rules matrix */
    @keyframes scrollLeftToRight {
        0% { transform: translateX(0); }
        100% { transform: translateX(-50%); }
    }
    
    @keyframes pulse {
        0% { opacity: 0.4; }
        50% { opacity: 1; }
        100% { opacity: 0.4; }
    }

    /* Responsive Viewport Configurations overrides */
    @media (max-width: 768px) {
        .marquee-track {
            animation-duration: 22s; 
        }
        
        .ticker-card {
            width: 260px; 
            height: 120px;
            padding: 12px;
            gap: 12px;
        }

        .ticker-image-box {
            width: 65px;
            height: 65px;
        }

        .ticker-title {
            font-size: 14px;
        }

        .ticker-price {
            font-size: 14px;
        }

        .ticker-buy-btn {
            padding: 4px 10px;
            font-size: 11px;
        }
        
        .marquee-wrapper::before, .marquee-wrapper::after {
            width: 50px;
        }
    }
</style>