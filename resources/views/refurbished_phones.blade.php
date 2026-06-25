@extends('layouts.app')

@section('content')
<div style="background-color: #050508; min-height: 100vh; padding: 25px 15px; color: #ffffff; font-family: system-ui, -apple-system, sans-serif;">
    <div style="max-width: 1200px; margin: 0 auto;">
        
        <div style="display: flex; justify-content: space-between; align-items: flex-start; margin-bottom: 25px; flex-wrap: wrap; gap: 15px;">
            <div>
                <h1 class="page-main-title">Best Selling Devices</h1>
                <p style="color: #64748b; font-size: 14px; margin: 5px 0 0 0; display: flex; align-items: center; gap: 6px;">
                    <i class="fas fa-check-circle" style="color: #3b82f6;"></i> Fully inspected, certified stock.
                </p>
            </div>
            <div style="background: rgba(30, 30, 42, 0.4); border: 1px solid #1e1e2a; padding: 8px 16px; border-radius: 10px; font-size: 13px; color: #94a3b8;">
                Showing <span id="visible_counter" style="color: #3b82f6; font-weight: 700;">{{ $products->count() }}</span> items
            </div>
        </div>

        <div style="margin-bottom: 25px;">
    <div style="position: relative; margin-bottom: 15px;">
        <i class="fas fa-search" style="position: absolute; left: 16px; top: 50%; transform: translateY(-50%); color: #64748b; font-size: 14px;"></i>
        <input type="text" id="catalog_search_input" onkeyup="applyFilters(currentBrand, null)" placeholder="Search by phone model (e.g., Vivo, iPhone...)" 
               style="width: 100%; padding: 14px 16px 14px 45px; background: #111118; border: 1px solid #1e1e2a; border-radius: 14px; color: white; font-size: 14px; outline: none; transition: border-color 0.2s;"
               onfocus="this.style.borderColor='#3b82f6'" onblur="this.style.borderColor='#1e1e2a'">
    </div>

    <div class="filter-dashboard-wrapper">
        <div class="brand-horizontal-scroller">
            <button class="brand-filter-pill active" onclick="applyFilters('all', this)">All Brands</button>
            @foreach($brands as $brand)
                <button class="brand-filter-pill" onclick="applyFilters('{{ Str::slug($brand) }}', this)">{{ $brand }}</button>
            @endforeach
        </div>

        <div class="sort-action-container">
            <select id="price_sort_trigger" class="sort-select-input" onchange="applyFilters(currentBrand, null)">
                <option value="default">Sort Valuation</option>
                <option value="low_high">Price: Low to High</option>
                <option value="high_low">Price: High to Low</option>
            </select>
        </div>
    </div>
</div>

     

        <div id="catalog_grid_matrix" class="products-catalog-grid">
            @forelse($products as $product)
                @php
                    $displayPrice = $product->sell_price > 0 ? $product->sell_price : $product->buy_price;
                    $lowerTitle = Str::lower($product->model_title);
                    $iconType = Str::contains($lowerTitle, ['macbook', 'laptop']) ? 'laptop' : (Str::contains($lowerTitle, ['ipad', 'tablet']) ? 'tablet-alt' : 'mobile-alt');
                @endphp
                
                <div class="catalog-product-card" data-brand="{{ Str::slug($product->brand_title) }}" data-price="{{ $displayPrice }}">
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
                        <div class="catalog-status-badge">Certified</div>
                    </div>
                    
                    <div class="card-details-body">
                        <div>
                            <h3 class="catalog-title-header">
                                {{ $product->model_title }}
                                <span class="catalog-title-capacity">({{ $product->capacity }})</span>
                            </h3>
                            <p class="catalog-price-tag">₹{{ number_format($displayPrice, 0) }}</p>
                            
                            <div class="catalog-meta-specs-row">
                                <span class="spec-item"><i class="fas fa-shield-alt"></i> {{ Str::title($product->warranty) }}</span>
                            </div>
                        </div>
                        
                        <div style="margin-top: 12px;">
                            <a href="{{ route('buy_refubrished_phones', ['slug' => $product->sef_url, 'order_id' => $product->order_id]) }}" 
                            class="catalog-action-buy-btn" 
                            style="display: block; text-align: center; text-decoration: none; box-sizing: border-box;">
                                Buy Now
                            </a>
                        </div>
                    </div>
                </div>
            @empty
                <div id="empty_state_node" style="grid-column: span 4; background: #111118; border: 1px solid #1e1e2a; border-radius: 24px; padding: 60px 20px; text-align: center; color: #64748b;">
                    <i class="fas fa-box-open" style="font-size: 40px; margin-bottom: 15px;"></i>
                    <h3 style="color: white; font-size: 16px; margin: 0;">No Active Stock Tracked</h3>
                </div>
            @endforelse
        </div>
        
        <div id="js_empty_state" style="display:none; background: #111118; border: 1px solid #1e1e2a; border-radius: 24px; padding: 60px 20px; text-align: center; color: #64748b; margin-top:20px;">
            <i class="fas fa-search" style="font-size: 40px; margin-bottom: 15px;"></i>
            <h3 style="color: white; font-size: 16px; margin: 0;">No matching items match your filters</h3>
        </div>

    </div>
</div>
<style>
    /* ... Keep all your premium CSS styles here exactly the same ... */
    
    /* Headings */
    .page-main-title { font-size: 28px; font-weight: 800; margin: 0; letter-spacing: -0.02em; }

    /* SMART FILTERS PLATFORM BAR */
    .filter-dashboard-wrapper {
        display: flex;
        justify-content: space-between;
        align-items: center;
        gap: 15px;
        margin-bottom: 25px;
        background: #111118;
        border: 1px solid #1e1e2a;
        padding: 12px 16px;
        border-radius: 16px;
    }
    .brand-horizontal-scroller {
        display: flex;
        gap: 8px;
        overflow-x: auto;
        white-space: nowrap;
        scrollbar-width: none;
        -webkit-overflow-scrolling: touch;
        flex: 1;
    }
    .brand-horizontal-scroller::-webkit-scrollbar { display: none; }
    
    .brand-filter-pill {
        background: #1a1a26;
        border: 1px solid #2a2a3a;
        color: #94a3b8;
        padding: 8px 16px;
        border-radius: 30px;
        font-size: 13px;
        font-weight: 600;
        cursor: pointer;
        transition: all 0.2s ease;
    }
    .brand-filter-pill:hover { border-color: #3b82f6; color: white; }
    .brand-filter-pill.active { background: #3b82f6; border-color: #3b82f6; color: white; }

    .sort-action-container { display: flex; align-items: center; }
    .sort-select-input {
        background: #1a1a26;
        border: 1px solid #2a2a3a;
        color: white;
        padding: 8px 14px;
        border-radius: 10px;
        font-size: 13px;
        outline: none;
        cursor: pointer;
    }

    /* THE CORE GRID INFRASTRUCTURE */
    .products-catalog-grid {
        display: grid;
        grid-template-columns: repeat(4, 1fr);
        gap: 20px;
    }

    .catalog-product-card {
        background: #111118;
        border: 1px solid #1e1e2a;
        border-radius: 18px;
        overflow: hidden;
        display: flex;
        flex-direction: column;
        transition: transform 0.3s ease, border-color 0.3s ease;
    }
    .catalog-product-card:hover { transform: translateY(-4px); border-color: #3b82f6; }

    .catalog-image-frame {
        background: linear-gradient(135deg, #16143c, #0f172a);
        height: 180px;
        display: flex;
        align-items: center;
        justify-content: center;
        position: relative;
    }
    .catalog-core-img { max-width: 70%; max-height: 70%; object-fit: contain; }
    .catalog-vector-fallback { font-size: 50px; color: white; opacity: 0.6; }
    
    .catalog-status-badge {
        position: absolute; top: 10px; right: 10px; background: #10b981;
        color: white; padding: 3px 10px; border-radius: 30px; font-size: 10px; font-weight: 700;
    }

    .card-details-body { padding: 16px; display: flex; flex-direction: column; justify-content: space-between; flex: 1; }
    .catalog-title-header { font-size: 15px; font-weight: 700; color: #ffffff; margin: 0 0 4px 0; line-height: 1.2; }
    .catalog-title-capacity { font-size: 11px; color: #64748b; font-weight: 400; display: block; margin-top: 2px; }
    .catalog-price-tag { color: #3b82f6; font-weight: 800; font-size: 18px; margin: 8px 0; }
    
    .catalog-meta-specs-row { display: flex; gap: 8px; font-size: 11px; color: #94a3b8; }
    .spec-item { background: rgba(30, 30, 42, 0.5); padding: 3px 8px; border-radius: 6px; }

    .catalog-action-buy-btn {
        width: 100%; padding: 10px; background: #3b82f6; border: none; border-radius: 10px;
        color: white; font-weight: 700; font-size: 13px; cursor: pointer; transition: background 0.2s;
    }
    .catalog-action-buy-btn:hover { background: #2563eb; }

    @media (max-width: 1024px) {
        .products-catalog-grid { grid-template-columns: repeat(3, 1fr); gap: 15px; }
    }
    @media (max-width: 768px) {
        .filter-dashboard-wrapper { flex-direction: column; align-items: stretch; gap: 12px; }
        .sort-action-container { width: 100%; }
        .sort-select-input { width: 100%; text-align: center; }
        .products-catalog-grid { grid-template-columns: repeat(2, 1fr); gap: 12px; }
    }
    @media (max-width: 480px) {
        .page-main-title { font-size: 22px; }
        .products-catalog-grid { 
            grid-template-columns: repeat(2, 1fr) !important;
            gap: 10px !important;
        }
        .catalog-image-frame { height: 130px; }
        .card-details-body { padding: 10px; }
        .catalog-title-header { font-size: 13px; }
        .catalog-price-tag { font-size: 15px; margin: 5px 0; }
        .catalog-action-buy-btn { padding: 8px; font-size: 12px; border-radius: 8px; }
        .catalog-status-badge { font-size: 9px; padding: 2px 6px; }
    }
</style>

<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>

<script>
  let currentBrand = 'all';

function applyFilters(brandSlug, element) {
    if(element) {
        // Switch active visual tab styles cleanly
        $('.brand-filter-pill').removeClass('active');
        $(element).addClass('active');
        currentBrand = brandSlug;
    }

    let sortType = $('#price_sort_trigger').val();
    let searchQuery = $('#catalog_search_input').val().toLowerCase().trim(); // Get search text
    let $grid = $('#catalog_grid_matrix');
    let $cards = $grid.find('.catalog-product-card');

    let visibleCount = 0;
    
    $cards.each(function() {
        let cardBrand = $(this).data('brand');
        // Finds the title text inside this specific card
        let cardTitle = $(this).find('.catalog-title-header').text().toLowerCase(); 

        // Match Brand Filter AND Search Input query together
        let matchesBrand = (currentBrand === 'all' || cardBrand === currentBrand);
        let matchesSearch = (searchQuery === '' || cardTitle.includes(searchQuery));

        if(matchesBrand && matchesSearch) {
            $(this).show();
            visibleCount++;
        } else {
            $(this).hide();
        }
    });

    // Flipkart price sorting system handler
    if (sortType === 'low_high' || sortType === 'high_low') {
        let sortedCards = $cards.get().sort(function(a, b) {
            let valA = parseFloat($(a).data('price'));
            let valB = parseFloat($(b).data('price'));
            
            return sortType === 'low_high' ? valA - valB : valB - valA;
        });
        $grid.append(sortedCards);
    }

    // Live counter adjustment
    $('#visible_counter').text(visibleCount);

    // Toggle empty state warning message panel
    if(visibleCount === 0) {
        $('#js_empty_state').show();
    } else {
        $('#js_empty_state').hide();
    }
}
</script>
@endsection