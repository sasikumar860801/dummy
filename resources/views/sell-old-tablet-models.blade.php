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
                <li><a href="{{ url('/sell-old-tablet') }}" style="color: #3b82f6; text-decoration: none;">Sell Old Tablet</a></li>
                <li><i class="fas fa-chevron-right" style="font-size: 10px; color: #64748b;"></i></li>
                <li style="color: #64748b;">{{ $brand->title }}</li>
            </ol>
        </nav>
    </div>

    <!-- Page Header -->
    <div style="text-align: center; margin-bottom: 40px;">
        <div style="display: flex; align-items: center; justify-content: center; gap: 15px; margin-bottom: 16px;">
            @if($brand->image && file_exists(public_path('media/images/brand/' . $brand->image)))
                <img src="{{ asset('media/images/brand/' . $brand->image) }}" alt="{{ $brand->title }}" style="width: 60px; height: 60px; object-fit: contain;">
            @else
                <i class="fas fa-tablet-alt" style="font-size: 50px; color: #3b82f6;"></i>
            @endif
            <h1 style="font-size: 36px; font-weight: 800; background: linear-gradient(135deg, #60a5fa, #a78bfa); -webkit-background-clip: text; background-clip: text; color: transparent;">
                Sell {{ $brand->title }} Tablet
            </h1>
        </div>
        <p style="color: #94a3b8; max-width: 600px; margin: 0 auto;">
            Get instant best price for your used {{ $brand->title }} tablet. Free doorstep pickup, instant cash payment.
        </p>
    </div>

    <!-- Search Box -->
    <div style="max-width: 500px; margin: 0 auto 40px;">
        <div style="position: relative;">
            <i class="fas fa-search" style="position: absolute; left: 18px; top: 50%; transform: translateY(-50%); color: #64748b;"></i>
            <input type="text" 
                   id="searchModel" 
                   placeholder="Search your {{ $brand->title }} tablet model" 
                   style="width: 100%; padding: 14px 20px 14px 45px; background: #1a1a2e; border: 1px solid #2a2a3a; border-radius: 30px; color: white; outline: none; font-size: 14px;">
        </div>
    </div>

    <!-- Choose by Series -->
    @if($modelSeries->count() > 0)
    <div style="margin-bottom: 50px;">
        <h3 style="font-size: 20px; font-weight: 700; margin-bottom: 20px; text-align: center;">Choose by Series</h3>
        <div style="display: flex; gap: 12px; flex-wrap: wrap; justify-content: center;">
            <button class="series-btn active" data-series="all" style="background: #3b82f6; color: white; padding: 10px 24px; border-radius: 40px; border: none; font-weight: 600; cursor: pointer; transition: all 0.2s;">ALL</button>
            @foreach($modelSeries as $series)
            <button class="series-btn" data-series="{{ $series->id }}" style="background: #111118; color: #e2e8f0; padding: 10px 24px; border-radius: 40px; border: 1px solid #2a2a3a; font-weight: 600; cursor: pointer; transition: all 0.2s;">
                {{ $series->title }}
            </button>
            @endforeach
        </div>
    </div>
    @endif

    <!-- Choose Model - 5 per row -->
    <div>
        <h3 style="font-size: 20px; font-weight: 700; margin-bottom: 20px;">Select Your Tablet Model</h3>
        <div id="modelsGrid" style="display: grid; grid-template-columns: repeat(5, 1fr); gap: 25px;">
            @foreach($models as $model)
            <div class="model-card" data-series-id="{{ $model->model_series_id }}" data-model-title="{{ strtolower($model->title) }}">
                <div style="background: #111118; border: 1px solid #2a2a3a; border-radius: 20px; padding: 25px 15px; text-align: center; transition: all 0.3s; cursor: pointer; height: 100%;">
                    <!-- Model Image -->
                    <div style="width: 120px; height: 120px; margin: 0 auto 15px; display: flex; align-items: center; justify-content: center;">
                        @if($model->model_img && file_exists(public_path('media/images/model/' . $model->model_img)))
                            <img src="{{ $model->model_img_url }}" alt="{{ $model->title }}" style="max-width: 100px; max-height: 100px; object-fit: contain;">
                        @else
                            <i class="fas fa-tablet-alt" style="font-size: 60px; color: #3b82f6;"></i>
                        @endif
                    </div>
                    
                    <!-- Model Title -->
                    <h4 style="font-size: 16px; font-weight: 600; margin-bottom: 10px; color: white;">{{ $model->title }}</h4>
                    
                    <!-- Price -->
                    @if($model->sell_price)
                    <p style="font-size: 20px; font-weight: 700; color: #3b82f6;">₹{{ number_format($model->sell_price) }}</p>
                    @endif
                    
                    <!-- Sell Button -->
                    <button class="sell-now-btn" data-model="{{ $model->title }}" data-brand="{{ $brand->title }}" style="margin-top: 15px; background: linear-gradient(135deg, #3b82f6, #8b5cf6); color: white; padding: 10px 20px; border-radius: 30px; border: none; font-weight: 600; cursor: pointer; width: 100%; transition: all 0.3s;">
                        Sell Now
                    </button>
                </div>
            </div>
            @endforeach
        </div>
        
        <!-- No results message -->
        <div id="noResults" style="display: none; text-align: center; padding: 60px; background: #111118; border-radius: 20px; margin-top: 30px;">
            <i class="fas fa-tablet-alt" style="font-size: 50px; color: #64748b; margin-bottom: 20px;"></i>
            <h3 style="color: white;">No tablet models found</h3>
            <p style="color: #94a3b8;">Try searching with a different keyword</p>
        </div>
    </div>

    <!-- Why Sell Section -->
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
    .model-card:hover {
        transform: translateY(-8px);
    }
    .model-card:hover > div {
        border-color: #3b82f6;
        box-shadow: 0 20px 35px -10px rgba(59,130,246,0.2);
    }
    .series-btn:hover:not(.active) {
        background: #2a2a3a;
        transform: translateY(-2px);
    }
    .sell-now-btn:hover {
        transform: translateY(-2px);
        box-shadow: 0 5px 15px rgba(59,130,246,0.4);
    }
    
    @media (max-width: 1024px) {
        #modelsGrid {
            grid-template-columns: repeat(4, 1fr) !important;
            gap: 20px;
        }
    }
    @media (max-width: 768px) {
        #modelsGrid {
            grid-template-columns: repeat(3, 1fr) !important;
            gap: 15px;
        }
    }
    @media (max-width: 550px) {
        #modelsGrid {
            grid-template-columns: repeat(2, 1fr) !important;
        }
    }
    @media (max-width: 350px) {
        #modelsGrid {
            grid-template-columns: 1fr !important;
        }
    }
</style>

<script>
    // Series filtering
    let currentSeries = 'all';
    let currentSearch = '';
    
    const seriesBtns = document.querySelectorAll('.series-btn');
    const modelsGrid = document.getElementById('modelsGrid');
    const noResults = document.getElementById('noResults');
    const searchInput = document.getElementById('searchModel');
    
    function filterModels() {
        const modelCards = document.querySelectorAll('.model-card');
        let visibleCount = 0;
        
        modelCards.forEach(card => {
            const seriesId = card.getAttribute('data-series-id');
            const modelTitle = card.getAttribute('data-model-title');
            
            let seriesMatch = currentSeries === 'all' || seriesId == currentSeries;
            let searchMatch = !currentSearch || modelTitle.includes(currentSearch.toLowerCase());
            
            if (seriesMatch && searchMatch) {
                card.style.display = 'block';
                visibleCount++;
            } else {
                card.style.display = 'none';
            }
        });
        
        // Show/hide no results
        if (visibleCount === 0) {
            modelsGrid.style.display = 'none';
            noResults.style.display = 'block';
        } else {
            modelsGrid.style.display = 'grid';
            noResults.style.display = 'none';
        }
    }
    
    // Series button click
    if (seriesBtns.length > 0) {
        seriesBtns.forEach(btn => {
            btn.addEventListener('click', function() {
                seriesBtns.forEach(b => {
                    b.classList.remove('active');
                    b.style.background = '#111118';
                    b.style.color = '#e2e8f0';
                    b.style.border = '1px solid #2a2a3a';
                });
                
                this.classList.add('active');
                this.style.background = '#3b82f6';
                this.style.color = 'white';
                this.style.border = 'none';
                
                currentSeries = this.getAttribute('data-series');
                filterModels();
            });
        });
    }
    
    // Search functionality
    searchInput.addEventListener('input', function() {
        currentSearch = this.value;
        filterModels();
    });
    
    // Sell button click
    document.querySelectorAll('.sell-now-btn').forEach(btn => {
        btn.addEventListener('click', function(e) {
            e.stopPropagation();
            const modelName = this.getAttribute('data-model');
            const brandName = this.getAttribute('data-brand');
            alert(`Sell ${brandName} ${modelName}\n\nThis will open the selling form. You'll get best price with free doorstep pickup!`);
            // window.location.href = `/sell/${brandName.toLowerCase()}/${modelName.toLowerCase().replace(/\s+/g, '-')}`;
        });
    });
</script>
@endsection