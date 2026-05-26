{{-- resources/views/home.blade.php --}}
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>RevoDevice - Buy & Sell Refurbished Devices</title>
    
    <!-- Google Fonts -->
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700;800&display=swap" rel="stylesheet">
    
    <!-- Font Awesome -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        body {
            font-family: 'Inter', sans-serif;
            background: #f8f9fc;
            color: #1a1a2e;
        }

        /* Container */
        .container {
            max-width: 1280px;
            margin: 0 auto;
            padding: 0 40px;
        }

        /* Header */
        .header {
            background: white;
            padding: 16px 0;
            border-bottom: 1px solid #e8ecf1;
            position: sticky;
            top: 0;
            z-index: 100;
            background: rgba(255,255,255,0.98);
            backdrop-filter: blur(10px);
        }

        .header-content {
            display: flex;
            align-items: center;
            justify-content: space-between;
            flex-wrap: wrap;
            gap: 20px;
        }

        .logo {
            display: flex;
            align-items: center;
            gap: 10px;
        }

        .logo-icon {
            width: 40px;
            height: 40px;
            background: linear-gradient(135deg, #2563eb, #7c3aed);
            border-radius: 12px;
            display: flex;
            align-items: center;
            justify-content: center;
        }

        .logo-icon i {
            font-size: 22px;
            color: white;
        }

        .logo h1 {
            font-size: 26px;
            font-weight: 800;
            background: linear-gradient(135deg, #2563eb, #7c3aed);
            -webkit-background-clip: text;
            background-clip: text;
            color: transparent;
        }

        /* Search Bar */
        .search-bar {
            flex: 1;
            max-width: 400px;
            position: relative;
        }

        .search-bar input {
            width: 100%;
            padding: 12px 20px 12px 45px;
            border: 1px solid #e2e8f0;
            border-radius: 30px;
            font-size: 14px;
            background: #f8fafc;
            transition: all 0.3s;
        }

        .search-bar input:focus {
            outline: none;
            border-color: #2563eb;
            box-shadow: 0 0 0 3px rgba(37,99,235,0.1);
            background: white;
        }

        .search-bar i {
            position: absolute;
            left: 18px;
            top: 50%;
            transform: translateY(-50%);
            color: #94a3b8;
        }

        /* Location & Login */
        .location-login {
            display: flex;
            align-items: center;
            gap: 20px;
        }

        .location {
            display: flex;
            align-items: center;
            gap: 8px;
            background: #f1f5f9;
            padding: 8px 16px;
            border-radius: 30px;
            cursor: pointer;
            transition: all 0.2s;
        }

        .location:hover {
            background: #e2e8f0;
        }

        .location i {
            color: #2563eb;
        }

        .login-btn {
            background: #1a1a2e;
            color: white;
            padding: 8px 24px;
            border-radius: 30px;
            border: none;
            font-weight: 600;
            cursor: pointer;
            transition: all 0.3s;
        }

        .login-btn:hover {
            background: #2563eb;
            transform: translateY(-2px);
        }

        /* Navigation */
        .nav {
            margin-top: 20px;
            display: flex;
            align-items: center;
            justify-content: space-between;
            flex-wrap: wrap;
            gap: 15px;
        }

        .nav-links {
            display: flex;
            gap: 30px;
            align-items: center;
            flex-wrap: wrap;
        }

        .nav-links a {
            text-decoration: none;
            color: #475569;
            font-weight: 500;
            font-size: 14px;
            transition: color 0.2s;
        }

        .nav-links a:hover {
            color: #2563eb;
        }

        .dropdown {
            position: relative;
        }

        .dropdown > a {
            display: flex;
            align-items: center;
            gap: 5px;
        }

        .dropdown-content {
            position: absolute;
            top: 100%;
            left: 0;
            background: white;
            min-width: 160px;
            box-shadow: 0 10px 40px rgba(0,0,0,0.1);
            border-radius: 12px;
            padding: 8px 0;
            opacity: 0;
            visibility: hidden;
            transition: all 0.2s;
            z-index: 10;
        }

        .dropdown:hover .dropdown-content {
            opacity: 1;
            visibility: visible;
        }

        .dropdown-content a {
            display: block;
            padding: 10px 20px;
            font-size: 13px;
        }

        .dropdown-content a:hover {
            background: #f1f5f9;
        }

        .sell-search {
            position: relative;
            width: 280px;
        }

        .sell-search input {
            width: 100%;
            padding: 8px 16px 8px 38px;
            border: 1px solid #e2e8f0;
            border-radius: 30px;
            font-size: 13px;
            background: #f8fafc;
        }

        .sell-search i {
            position: absolute;
            left: 14px;
            top: 50%;
            transform: translateY(-50%);
            color: #94a3b8;
            font-size: 12px;
        }

        /* Hero Section */
        .hero {
            background: linear-gradient(135deg, #0f172a 0%, #1e293b 100%);
            border-radius: 24px;
            margin: 40px 0;
            padding: 50px 60px;
            position: relative;
            overflow: hidden;
        }

        .hero h2 {
            color: white;
            font-size: 42px;
            font-weight: 800;
            margin-bottom: 16px;
        }

        .hero p {
            color: #cbd5e1;
            font-size: 16px;
            margin-bottom: 30px;
        }

        .hero-buttons {
            display: flex;
            gap: 15px;
        }

        .btn-sell, .btn-buy {
            padding: 12px 32px;
            border-radius: 40px;
            font-weight: 600;
            cursor: pointer;
            transition: all 0.3s;
            border: none;
        }

        .btn-sell {
            background: white;
            color: #0f172a;
        }

        .btn-sell:hover {
            transform: translateY(-2px);
            box-shadow: 0 10px 25px rgba(255,255,255,0.2);
        }

        .btn-buy {
            background: transparent;
            border: 2px solid white;
            color: white;
        }

        .btn-buy:hover {
            background: white;
            color: #0f172a;
        }

        /* Categories */
        .categories {
            display: flex;
            gap: 20px;
            margin-bottom: 50px;
            flex-wrap: wrap;
        }

        .category-card {
            flex: 1;
            background: white;
            padding: 30px;
            text-align: center;
            border-radius: 20px;
            cursor: pointer;
            transition: all 0.3s;
            border: 1px solid #eef2ff;
        }

        .category-card:hover {
            transform: translateY(-5px);
            box-shadow: 0 20px 35px -10px rgba(0,0,0,0.1);
            border-color: #2563eb;
        }

        .category-card i {
            font-size: 40px;
            color: #2563eb;
            margin-bottom: 15px;
        }

        .category-card h4 {
            font-size: 18px;
            margin-bottom: 5px;
        }

        .category-card p {
            font-size: 12px;
            color: #64748b;
        }

        /* Brands */
        .brands {
            margin-bottom: 50px;
        }

        .section-header {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 25px;
        }

        .section-header h3 {
            font-size: 24px;
            font-weight: 700;
        }

        .brand-list {
            display: flex;
            gap: 15px;
            flex-wrap: wrap;
        }

        .brand-item {
            padding: 10px 24px;
            background: white;
            border-radius: 40px;
            font-weight: 600;
            font-size: 14px;
            cursor: pointer;
            transition: all 0.2s;
            border: 1px solid #e2e8f0;
        }

        .brand-item:hover {
            background: #2563eb;
            color: white;
            border-color: #2563eb;
            transform: scale(0.95);
        }

        /* Featured Products */
        .featured-products {
            margin-bottom: 60px;
        }

        .products-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(280px, 1fr));
            gap: 25px;
            margin-top: 20px;
        }

        .product-card {
            background: white;
            border-radius: 20px;
            overflow: hidden;
            transition: all 0.3s;
            cursor: pointer;
            border: 1px solid #eef2ff;
        }

        .product-card:hover {
            transform: translateY(-8px);
            box-shadow: 0 25px 40px -12px rgba(0,0,0,0.15);
        }

        .product-image {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            height: 200px;
            display: flex;
            align-items: center;
            justify-content: center;
            position: relative;
        }

        .product-image i {
            font-size: 80px;
            color: white;
            opacity: 0.9;
        }

        .product-badge {
            position: absolute;
            top: 15px;
            right: 15px;
            background: #10b981;
            color: white;
            padding: 4px 12px;
            border-radius: 20px;
            font-size: 11px;
            font-weight: 600;
        }

        .product-info {
            padding: 20px;
        }

        .product-title {
            font-weight: 700;
            font-size: 18px;
            margin-bottom: 8px;
        }

        .product-price {
            color: #2563eb;
            font-weight: 800;
            font-size: 20px;
            margin: 10px 0;
        }

        .product-features {
            display: flex;
            gap: 15px;
            font-size: 12px;
            color: #64748b;
            margin: 12px 0;
        }

        .product-features span {
            display: flex;
            align-items: center;
            gap: 5px;
        }

        .product-actions {
            display: flex;
            gap: 10px;
            margin-top: 15px;
        }

        .btn-sell-product, .btn-buy-product {
            flex: 1;
            padding: 10px;
            border-radius: 30px;
            font-weight: 600;
            font-size: 13px;
            cursor: pointer;
            transition: all 0.2s;
            text-align: center;
        }

        .btn-sell-product {
            background: #f1f5f9;
            color: #475569;
            border: none;
        }

        .btn-buy-product {
            background: #2563eb;
            color: white;
            border: none;
        }

        .btn-sell-product:hover, .btn-buy-product:hover {
            transform: translateY(-2px);
        }

        /* Why Buy/Sell Section */
        .why-section {
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 30px;
            margin-bottom: 60px;
        }

        .why-card {
            background: white;
            padding: 35px;
            border-radius: 24px;
            transition: all 0.3s;
            border: 1px solid #eef2ff;
        }

        .why-card:hover {
            transform: translateY(-5px);
            box-shadow: 0 20px 35px -10px rgba(0,0,0,0.1);
        }

        .why-card h3 {
            font-size: 24px;
            margin-bottom: 20px;
        }

        .feature-grid {
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 20px;
            margin: 20px 0;
        }

        .feature-item {
            text-align: center;
            padding: 15px;
            background: #f8fafc;
            border-radius: 16px;
        }

        .feature-item i {
            font-size: 28px;
            color: #2563eb;
            margin-bottom: 10px;
        }

        .feature-item h4 {
            font-size: 14px;
            margin-bottom: 5px;
        }

        .feature-item p {
            font-size: 12px;
            color: #64748b;
        }

        /* Customer Feedback */
        .feedback-section {
            margin-bottom: 60px;
        }

        .feedback-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(300px, 1fr));
            gap: 25px;
            margin-top: 25px;
        }

        .feedback-card {
            background: white;
            padding: 25px;
            border-radius: 20px;
            transition: all 0.3s;
            border: 1px solid #eef2ff;
        }

        .feedback-card:hover {
            transform: translateY(-5px);
            box-shadow: 0 15px 30px -10px rgba(0,0,0,0.1);
        }

        .stars {
            color: #fbbf24;
            margin-bottom: 15px;
        }

        .feedback-text {
            color: #475569;
            line-height: 1.6;
            margin-bottom: 20px;
            font-style: italic;
        }

        .customer {
            display: flex;
            align-items: center;
            gap: 12px;
        }

        .customer-avatar {
            width: 45px;
            height: 45px;
            background: linear-gradient(135deg, #667eea, #764ba2);
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            color: white;
        }

        /* Footer */
        .footer {
            background: #0f172a;
            color: #94a3b8;
            padding: 50px 0 30px;
            margin-top: 40px;
        }

        .footer-content {
            display: grid;
            grid-template-columns: 2fr 1fr 1fr 1.5fr;
            gap: 40px;
            margin-bottom: 40px;
        }

        .footer-logo h2 {
            color: white;
            margin-bottom: 15px;
        }

        .social-icons {
            display: flex;
            gap: 15px;
            margin-top: 20px;
        }

        .social-icons a {
            color: #94a3b8;
            font-size: 20px;
            transition: color 0.2s;
        }

        .social-icons a:hover {
            color: white;
        }

        .footer-links h4 {
            color: white;
            margin-bottom: 20px;
            font-size: 16px;
        }

        .footer-links a {
            display: block;
            color: #94a3b8;
            text-decoration: none;
            margin-bottom: 12px;
            font-size: 14px;
            transition: color 0.2s;
        }

        .footer-links a:hover {
            color: white;
        }

        .contact-info p {
            margin-bottom: 12px;
            font-size: 14px;
            display: flex;
            align-items: center;
            gap: 10px;
        }

        .copyright {
            text-align: center;
            padding-top: 30px;
            border-top: 1px solid #1e293b;
            font-size: 13px;
        }

        /* Floating WhatsApp */
        .whatsapp-float {
            position: fixed;
            bottom: 30px;
            right: 30px;
            background: #25D366;
            width: 60px;
            height: 60px;
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 32px;
            color: white;
            box-shadow: 0 5px 20px rgba(37,211,102,0.4);
            transition: all 0.3s;
            z-index: 1000;
            cursor: pointer;
        }

        .whatsapp-float:hover {
            transform: scale(1.1);
        }

        @media (max-width: 768px) {
            .container {
                padding: 0 20px;
            }
            .hero h2 {
                font-size: 28px;
            }
            .why-section {
                grid-template-columns: 1fr;
            }
            .footer-content {
                grid-template-columns: 1fr;
            }
            .nav-links {
                gap: 15px;
            }
        }
    </style>
</head>
<body>

<!-- Floating WhatsApp -->
<div class="whatsapp-float">
    <i class="fab fa-whatsapp"></i>
</div>

<!-- Header -->
<header class="header">
    <div class="container">
        <div class="header-content">
            <div class="logo">
                <div class="logo-icon">
                    <i class="fas fa-mobile-alt"></i>
                </div>
                <h1>RevoDevice</h1>
            </div>

            <div class="search-bar">
                <i class="fas fa-search"></i>
                <input type="text" placeholder="Search devices for buy or sell">
            </div>

            <div class="location-login">
                <div class="location">
                    <i class="fas fa-map-marker-alt"></i>
                    <span>New Delhi</span>
                    <i class="fas fa-chevron-down"></i>
                </div>
                <button class="login-btn">Login</button>
            </div>
        </div>

        <div class="nav">
            <div class="nav-links">
                <div class="dropdown">
                    <a href="#">Buy Devices <i class="fas fa-angle-down"></i></a>
                    <div class="dropdown-content">
                        <a href="#">Mobiles</a>
                        <a href="#">Laptops</a>
                        <a href="#">Tablets</a>
                    </div>
                </div>
                <div class="dropdown">
                    <a href="#">Sell Devices <i class="fas fa-angle-down"></i></a>
                    <div class="dropdown-content">
                        <a href="#">Sell Mobile</a>
                        <a href="#">Sell Laptop</a>
                        <a href="#">Sell Tablet</a>
                    </div>
                </div>
                <div class="dropdown">
                    <a href="#">More <i class="fas fa-angle-down"></i></a>
                    <div class="dropdown-content">
                        <a href="#">Contact Us</a>
                        <a href="#">Privacy Policy</a>
                        <a href="#">About Us</a>
                        <a href="#">FAQ</a>
                    </div>
                </div>
                <a href="#">Get Franchise</a>
            </div>

            <div class="sell-search">
                <i class="fas fa-search"></i>
                <input type="text" placeholder="Search your devices for sell devices">
            </div>
        </div>
    </div>
</header>

<main>
    <div class="container">
        <!-- Hero Banner -->
        <div class="hero">
            <h2>SELL OLD GADGETS.<br>BUY SUPERB REFURBISHED</h2>
            <p>Get instant price quote & free doorstep pickup</p>
            <div class="hero-buttons">
                <button class="btn-sell">Sell Now →</button>
                <button class="btn-buy">Buy Now →</button>
            </div>
        </div>

        <!-- Categories -->
        <div class="categories">
            <div class="category-card">
                <i class="fas fa-mobile-alt"></i>
                <h4>Mobiles</h4>
                <p>iPhone · Samsung · Pixel</p>
            </div>
            <div class="category-card">
                <i class="fas fa-tablet-alt"></i>
                <h4>Tablets</h4>
                <p>iPad · Samsung Tab</p>
            </div>
            <div class="category-card">
                <i class="fas fa-laptop"></i>
                <h4>Laptops</h4>
                <p>MacBook · Dell · HP</p>
            </div>
        </div>

        <!-- Brands -->
        <div class="brands">
            <div class="section-header">
                <h3>Top Brands</h3>
                <a href="#" style="color: #2563eb; text-decoration: none;">View all →</a>
            </div>
            <div class="brand-list">
                <div class="brand-item">Apple</div>
                <div class="brand-item">Google</div>
                <div class="brand-item">Xiaomi</div>
                <div class="brand-item">Lenovo</div>
                <div class="brand-item">Asus</div>
                <div class="brand-item">Vivo</div>
                <div class="brand-item">LG</div>
                <div class="brand-item">Samsung</div>
            </div>
        </div>

        <!-- Featured Products -->
        <div class="featured-products">
            <div class="section-header">
                <h3>Featured Products</h3>
                <a href="#" style="color: #2563eb; text-decoration: none;">View all →</a>
            </div>
            <div class="products-grid">
                <div class="product-card">
                    <div class="product-image">
                        <i class="fas fa-mobile-alt"></i>
                        <div class="product-badge">Refurbished</div>
                    </div>
                    <div class="product-info">
                        <div class="product-title">iPhone 13</div>
                        <div class="product-price">$13,600</div>
                        <div class="product-features">
                            <span><i class="fas fa-shield-alt"></i> 12 Months Warranty</span>
                            <span><i class="fas fa-star"></i> Quality 4.5</span>
                        </div>
                        <div class="product-actions">
                            <button class="btn-sell-product">Sell</button>
                            <button class="btn-buy-product">Buy</button>
                        </div>
                    </div>
                </div>

                <div class="product-card">
                    <div class="product-image">
                        <i class="fas fa-laptop"></i>
                        <div class="product-badge">Like New</div>
                    </div>
                    <div class="product-info">
                        <div class="product-title">MacBook Air M1</div>
                        <div class="product-price">$95,500</div>
                        <div class="product-features">
                            <span><i class="fas fa-shield-alt"></i> 12 Months Warranty</span>
                            <span><i class="fas fa-star"></i> Quality 4.8</span>
                        </div>
                        <div class="product-actions">
                            <button class="btn-sell-product">Sell</button>
                            <button class="btn-buy-product">Buy</button>
                        </div>
                    </div>
                </div>

                <div class="product-card">
                    <div class="product-image">
                        <i class="fas fa-tablet-alt"></i>
                        <div class="product-badge">Certified</div>
                    </div>
                    <div class="product-info">
                        <div class="product-title">iPad Pro</div>
                        <div class="product-price">$45,000</div>
                        <div class="product-features">
                            <span><i class="fas fa-shield-alt"></i> 12 Months Warranty</span>
                            <span><i class="fas fa-star"></i> Quality 4.7</span>
                        </div>
                        <div class="product-actions">
                            <button class="btn-sell-product">Sell</button>
                            <button class="btn-buy-product">Buy</button>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Why Buy/Sell Section -->
        <div class="why-section">
            <div class="why-card">
                <h3>Why Buy with Us?</h3>
                <div class="feature-grid">
                    <div class="feature-item">
                        <i class="fas fa-shield-alt"></i>
                        <h4>12 Months Warranty</h4>
                        <p>On all devices</p>
                    </div>
                    <div class="feature-item">
                        <i class="fas fa-chart-line"></i>
                        <h4>Quality 4.5+</h4>
                        <p>Certified products</p>
                    </div>
                    <div class="feature-item">
                        <i class="fas fa-bolt"></i>
                        <h4>Instant Cash</h4>
                        <p>On selling</p>
                    </div>
                    <div class="feature-item">
                        <i class="fas fa-truck"></i>
                        <h4>Hassle Free</h4>
                        <p>Free delivery</p>
                    </div>
                </div>
            </div>

            <div class="why-card">
                <h3>Why Sell with Us?</h3>
                <div class="feature-grid">
                    <div class="feature-item">
                        <i class="fas fa-dollar-sign"></i>
                        <h4>Best Showroom</h4>
                        <p>Price match</p>
                    </div>
                    <div class="feature-item">
                        <i class="fas fa-clock"></i>
                        <h4>Instant Cash</h4>
                        <p>Same day payment</p>
                    </div>
                    <div class="feature-item">
                        <i class="fas fa-truck-fast"></i>
                        <h4>Free Pickup</h4>
                        <p>Doorstep service</p>
                    </div>
                    <div class="feature-item">
                        <i class="fas fa-lock"></i>
                        <h4>Secure & Safe</h4>
                        <p>Data wiping</p>
                    </div>
                </div>
            </div>
        </div>

        <!-- Customer Feedback -->
        <div class="feedback-section">
            <div class="section-header">
                <h3>Customer Feedback</h3>
            </div>
            <div class="feedback-grid">
                <div class="feedback-card">
                    <div class="stars">
                        <i class="fas fa-star"></i>
                        <i class="fas fa-star"></i>
                        <i class="fas fa-star"></i>
                        <i class="fas fa-star"></i>
                        <i class="fas fa-star"></i>
                    </div>
                    <p class="feedback-text">"Amazing experience! Got my iPhone 13 in perfect condition. The warranty gives me peace of mind."</p>
                    <div class="customer">
                        <div class="customer-avatar">
                            <i class="fas fa-user"></i>
                        </div>
                        <div>
                            <strong>Rahul Sharma</strong>
                            <p style="font-size: 12px; color: #64748b;">Verified Buyer</p>
                        </div>
                    </div>
                </div>

                <div class="feedback-card">
                    <div class="stars">
                        <i class="fas fa-star"></i>
                        <i class="fas fa-star"></i>
                        <i class="fas fa-star"></i>
                        <i class="fas fa-star"></i>
                        <i class="fas fa-star"></i>
                    </div>
                    <p class="feedback-text">"Sold my old laptop within hours! Best price compared to other platforms. Quick payment too."</p>
                    <div class="customer">
                        <div class="customer-avatar">
                            <i class="fas fa-user"></i>
                        </div>
                        <div>
                            <strong>Priya Patel</strong>
                            <p style="font-size: 12px; color: #64748b;">Verified Seller</p>
                        </div>
                    </div>
                </div>

                <div class="feedback-card">
                    <div class="stars">
                        <i class="fas fa-star"></i>
                        <i class="fas fa-star"></i>
                        <i class="fas fa-star"></i>
                        <i class="fas fa-star"></i>
                        <i class="fas fa-star-half-alt"></i>
                    </div>
                    <p class="feedback-text">"Excellent customer support. The MacBook I bought feels brand new. Highly recommended!"</p>
                    <div class="customer">
                        <div class="customer-avatar">
                            <i class="fas fa-user"></i>
                        </div>
                        <div>
                            <strong>Aditya Mehta</strong>
                            <p style="font-size: 12px; color: #64748b;">Verified Buyer</p>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</main>

<!-- Footer -->
<footer class="footer">
    <div class="container">
        <div class="footer-content">
            <div class="footer-logo">
                <h2 style="font-size: 28px;">RevoDevice</h2>
                <p style="margin-top: 10px;">India's most trusted platform for buying and selling refurbished devices.</p>
                <div class="social-icons">
                    <a href="#"><i class="fab fa-facebook"></i></a>
                    <a href="#"><i class="fab fa-instagram"></i></a>
                    <a href="#"><i class="fab fa-twitter"></i></a>
                    <a href="#"><i class="fab fa-linkedin"></i></a>
                </div>
            </div>
            <div class="footer-links">
                <h4>Company</h4>
                <a href="#">About Us</a>
                <a href="#">Careers</a>
                <a href="#">Press</a>
                <a href="#">Franchise</a>
            </div>
            <div class="footer-links">
                <h4>Support</h4>
                <a href="#">Contact Us</a>
                <a href="#">Privacy Policy</a>
                <a href="#">Terms & Conditions</a>
                <a href="#">FAQ</a>
            </div>
            <div class="contact-info">
                <h4>Contact Info</h4>
                <p><i class="fas fa-map-marker-alt"></i> 701, Phoenix Tower, Andheri East, Mumbai - 400093</p>
                <p><i class="fas fa-phone"></i> +91 98765 43210</p>
                <p><i class="fas fa-envelope"></i> hello@revodevice.com</p>
            </div>
        </div>
        <div class="copyright">
            <p>© 2025 RevoDevice. All rights reserved. | Buy & Sell Refurbished Devices</p>
        </div>
    </div>
</footer>

</body>
</html>