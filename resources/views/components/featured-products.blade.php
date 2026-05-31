<div style="margin-bottom: 60px;">
    <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 25px; flex-wrap: wrap; gap: 10px;">
        <h3 style="font-size: 24px; font-weight: 700;">Premium Refurbished Devices</h3>
        <a href="#" style="color: #3b82f6; text-decoration: none;">View all →</a>
    </div>
    
    <div class="products-grid">
        @php
        $products = [
            ['name' => 'iPhone 13', 'price' => '$13,600', 'icon' => 'mobile-alt', 'warranty' => '12 Months', 'quality' => '4.5'],
            ['name' => 'MacBook Air M1', 'price' => '$95,500', 'icon' => 'laptop', 'warranty' => '12 Months', 'quality' => '4.8'],
            ['name' => 'iPad Pro', 'price' => '$45,000', 'icon' => 'tablet-alt', 'warranty' => '12 Months', 'quality' => '4.7']
        ];
        @endphp
        @foreach($products as $product)
        <div class="product-card">
            <div class="product-image" style="background: linear-gradient(135deg, #1e1b4b, #0f172a); height: 200px; display: flex; align-items: center; justify-content: center; position: relative;">
                <i class="fas fa-{{ $product['icon'] }}" style="font-size: 80px; color: white; opacity: 0.8;"></i>
                <div class="product-badge" style="position: absolute; top: 15px; right: 15px; background: #10b981; color: white; padding: 4px 12px; border-radius: 20px; font-size: 11px; font-weight: 600;">Refurbished</div>
            </div>
            <div style="padding: 20px;">
                <h4 style="font-size: 18px; font-weight: 700;">{{ $product['name'] }}</h4>
                <p style="color: #3b82f6; font-weight: 800; font-size: 20px; margin: 10px 0;">{{ $product['price'] }}</p>
                <div style="display: flex; gap: 15px; font-size: 12px; color: #94a3b8; margin: 12px 0;">
                    <span><i class="fas fa-shield-alt"></i> {{ $product['warranty'] }} Warranty</span>
                    <span><i class="fas fa-star"></i> Quality {{ $product['quality'] }}</span>
                </div>
                <div style="display: flex; gap: 10px;">
                    <button style="flex: 1; padding: 10px; background: #3b82f6; border: none; border-radius: 30px; color: white; cursor: pointer; transition: all 0.3s;">Buy Now</button>
                </div>
            </div>
        </div>
        @endforeach
    </div>
</div>

<style>
    .products-grid {
        display: grid;
        grid-template-columns: repeat(3, 1fr);
        gap: 25px;
    }
    
    .product-card {
        background: #111118;
        border: 1px solid #1e1e2a;
        border-radius: 20px;
        overflow: hidden;
        transition: all 0.3s;
    }
    
    .product-card:hover {
        transform: translateY(-8px);
        border-color: #3b82f6;
        box-shadow: 0 20px 35px -10px rgba(59,130,246,0.2);
    }
    
    .product-card button:hover {
        transform: translateY(-2px);
        box-shadow: 0 5px 15px rgba(59,130,246,0.4);
    }
    
    /* Laptop / Desktop - 3 products */
    @media (min-width: 1025px) {
        .products-grid {
            grid-template-columns: repeat(3, 1fr);
        }
    }
    
    /* Tablet - 2 products */
    @media (min-width: 769px) and (max-width: 1024px) {
        .products-grid {
            grid-template-columns: repeat(2, 1fr);
            gap: 20px;
        }
    }
    
    /* Mobile - 1 product */
    @media (max-width: 768px) {
        .products-grid {
            grid-template-columns: 1fr;
            gap: 20px;
        }
        
        .product-image {
            height: 180px;
        }
        
        .product-image i {
            font-size: 60px !important;
        }
    }
    
    /* Small Mobile */
    @media (max-width: 480px) {
        .products-grid {
            gap: 15px;
        }
        
        .product-image {
            height: 160px;
        }
        
        .product-image i {
            font-size: 50px !important;
        }
        
        .product-card > div:last-child {
            padding: 15px;
        }
        
        .product-card h4 {
            font-size: 16px;
        }
        
        .product-card p {
            font-size: 18px;
        }
    }
</style>