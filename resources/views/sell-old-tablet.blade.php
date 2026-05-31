@extends('layouts.app')

@section('title', $seo['title'])

@section('meta_description', $seo['meta_description'])

@section('meta_keywords', $seo['meta_keywords'])

@section('og_title', $seo['og_title'])

@section('og_description', $seo['og_description'])

@section('canonical_url', $seo['canonical_url'])

@section('content')
<div class="container">
    <!-- Breadcrumbs for SEO -->
    <div style="padding: 20px 0; font-size: 14px;">
        <nav aria-label="breadcrumb">
            <ol style="display: flex; gap: 8px; list-style: none; flex-wrap: wrap;">
                <li><a href="{{ url('/') }}" style="color: #3b82f6; text-decoration: none;">Home</a></li>
                <li><i class="fas fa-chevron-right" style="font-size: 10px; color: #64748b;"></i></li>
                <li style="color: #64748b;">Sell Old Tablet</li>
            </ol>
        </nav>
    </div>

    <!-- Page Header -->
    <div style="text-align: center; margin-bottom: 40px;">
        <h1 style="font-size: 36px; font-weight: 800; margin-bottom: 16px; background: linear-gradient(135deg, #60a5fa, #a78bfa); -webkit-background-clip: text; background-clip: text; color: transparent;">
            Sell Old Tablet
        </h1>
        <p style="color: #94a3b8; max-width: 600px; margin: 0 auto;">
            Get instant best price for your used tablet. Free doorstep pickup, instant cash payment, secure data wiping.
        </p>
    </div>

    <!-- Search Box -->
    <div style="max-width: 500px; margin: 0 auto 50px;">
        <div style="position: relative;">
            <i class="fas fa-search" style="position: absolute; left: 18px; top: 50%; transform: translateY(-50%); color: #64748b;"></i>
            <input type="text" 
                   id="searchTablet" 
                   placeholder="Search your tablet model (e.g., iPad Pro, Samsung Tab S9)" 
                   style="width: 100%; padding: 14px 20px 14px 45px; background: #1a1a2e; border: 1px solid #2a2a3a; border-radius: 30px; color: white; outline: none; font-size: 14px;">
            <span style="position: absolute; right: 18px; top: 50%; transform: translateY(-50%); color: #64748b; font-size: 12px;">{{ $totalTablets }}+ Models</span>
        </div>
    </div>

    <!-- Brands Grid - 5 brands per row on laptop -->
    <div>
        <h3 style="font-size: 20px; font-weight: 700; margin-bottom: 20px;">Select Your Tablet Brand</h3>
        <div id="brandsGrid" style="display: grid; grid-template-columns: repeat(5, 1fr); gap: 25px;">
            @foreach($brands as $brand)
            <a href="{{ url('/sell-old-tablet/sell-' . $brand->sef_url) }}" 
               class="brand-card"
               data-brand-title="{{ strtolower($brand->title) }}"
               style="text-decoration: none; display: block;">
                <div style="background: #111118; border: 1px solid #2a2a3a; border-radius: 20px; padding: 25px 15px; text-align: center; transition: all 0.3s; cursor: pointer;">
                    <!-- Brand Image -->
                    <div style="width: 100px; height: 100px; margin: 0 auto 15px; display: flex; align-items: center; justify-content: center;">
                        @if($brand->image && file_exists(public_path('media/images/brand/' . $brand->image)))
                            <img src="{{ $brand->image_url }}" alt="{{ $brand->title }}" style="max-width: 80px; max-height: 80px; object-fit: contain;">
                        @else
                            <i class="fas fa-tablet-alt" style="font-size: 50px; color: #3b82f6;"></i>
                        @endif
                    </div>
                    
                    <!-- Brand Title -->
                    <h4 style="font-size: 18px; font-weight: 700; margin-bottom: 8px; color: white;">{{ $brand->title }}</h4>
                    
                    <!-- View Models Button -->
                    <div style="margin-top: 15px; background: linear-gradient(135deg, #3b82f6, #8b5cf6); color: white; padding: 8px 16px; border-radius: 30px; font-size: 13px; font-weight: 500; display: inline-block;">
                        View Models →
                    </div>
                </div>
            </a>
            @endforeach
        </div>
        
        <!-- No results message -->
        <div id="noResults" style="display: none; text-align: center; padding: 60px; background: #111118; border-radius: 20px; margin-top: 30px;">
            <i class="fas fa-tablet-alt" style="font-size: 50px; color: #64748b; margin-bottom: 20px;"></i>
            <h3 style="color: white;">No tablet brands found</h3>
            <p style="color: #94a3b8;">Try searching with a different keyword</p>
        </div>
    </div>

    <!-- Why Sell Tablet Section -->
    <div style="display: grid; grid-template-columns: repeat(4, 1fr); gap: 25px; margin: 60px 0 40px;">
        <div style="background: #111118; border: 1px solid #2a2a3a; border-radius: 16px; padding: 25px; text-align: center;">
            <i class="fas fa-rupee-sign" style="font-size: 35px; color: #3b82f6; margin-bottom: 15px;"></i>
            <h4 style="color: white; margin-bottom: 8px;">Best Price</h4>
            <p style="color: #94a3b8; font-size: 13px;">Get highest resale value</p>
        </div>
        <div style="background: #111118; border: 1px solid #2a2a3a; border-radius: 16px; padding: 25px; text-align: center;">
            <i class="fas fa-truck-fast" style="font-size: 35px; color: #3b82f6; margin-bottom: 15px;"></i>
            <h4 style="color: white; margin-bottom: 8px;">Free Pickup</h4>
            <p style="color: #94a3b8; font-size: 13px;">Doorstep service</p>
        </div>
        <div style="background: #111118; border: 1px solid #2a2a3a; border-radius: 16px; padding: 25px; text-align: center;">
            <i class="fas fa-clock" style="font-size: 35px; color: #3b82f6; margin-bottom: 15px;"></i>
            <h4 style="color: white; margin-bottom: 8px;">Instant Cash</h4>
            <p style="color: #94a3b8; font-size: 13px;">Same day payment</p>
        </div>
        <div style="background: #111118; border: 1px solid #2a2a3a; border-radius: 16px; padding: 25px; text-align: center;">
            <i class="fas fa-shield-alt" style="font-size: 35px; color: #3b82f6; margin-bottom: 15px;"></i>
            <h4 style="color: white; margin-bottom: 8px;">Secure Data</h4>
            <p style="color: #94a3b8; font-size: 13px;">100% data wiping</p>
        </div>
    </div>
</div>

<style>
    .brand-card div:hover {
        transform: translateY(-8px);
        border-color: #3b82f6;
        box-shadow: 0 20px 35px -10px rgba(59,130,246,0.2);
    }
    
    @media (max-width: 1024px) {
        #brandsGrid {
            grid-template-columns: repeat(4, 1fr) !important;
            gap: 20px;
        }
    }
    
    @media (max-width: 768px) {
        #brandsGrid {
            grid-template-columns: repeat(3, 1fr) !important;
            gap: 15px;
        }
        .why-sell-grid {
            grid-template-columns: repeat(2, 1fr) !important;
        }
    }
    
    @media (max-width: 550px) {
        #brandsGrid {
            grid-template-columns: repeat(2, 1fr) !important;
        }
    }
    
    @media (max-width: 350px) {
        #brandsGrid {
            grid-template-columns: 1fr !important;
        }
        .why-sell-grid {
            grid-template-columns: 1fr !important;
        }
    }
</style>

<script>
    // Search functionality for brands
    const searchInput = document.getElementById('searchTablet');
    const brandsGrid = document.getElementById('brandsGrid');
    const noResults = document.getElementById('noResults');
    const brandCards = document.querySelectorAll('.brand-card');
    
    function filterBrands() {
        const searchTerm = searchInput.value.toLowerCase();
        let visibleCount = 0;
        
        brandCards.forEach(card => {
            const brandTitle = card.getAttribute('data-brand-title');
            
            if (!searchTerm || brandTitle.includes(searchTerm)) {
                card.style.display = 'block';
                visibleCount++;
            } else {
                card.style.display = 'none';
            }
        });
        
        // Show/hide no results
        if (visibleCount === 0) {
            brandsGrid.style.display = 'none';
            noResults.style.display = 'block';
        } else {
            brandsGrid.style.display = 'grid';
            noResults.style.display = 'none';
        }
    }
    
    searchInput.addEventListener('input', filterBrands);
</script>
@endsection