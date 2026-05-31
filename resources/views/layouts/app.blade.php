<!DOCTYPE html>
<html lang="en">
<head>
    <!-- Basic SEO -->
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0, user-scalable=yes">
    <meta name="description" content="@yield('meta_description', 'RevoDevice - India\'s most trusted platform to buy and sell certified refurbished mobiles, laptops, tablets. Best prices, 12 months warranty, free pickup.')">
    <meta name="keywords" content="@yield('meta_keywords', 'refurbished phones, sell old phone, buy refurbished laptop, second hand mobile, certified refurbished, RevoDevice, cashify alternative')">
    <meta name="author" content="RevoDevice">
    <meta name="robots" content="index, follow">
    <meta name="language" content="English">
    <meta name="revisit-after" content="7 days">
    <meta name="csrf-token" content="{{ csrf_token() }}">

    
    <!-- Open Graph / Social Media Meta Tags -->
    <meta property="og:title" content="@yield('og_title', 'RevoDevice - Buy & Sell Refurbished Devices')">
    <meta property="og:description" content="@yield('og_description', 'Best platform to buy certified refurbished devices and sell old gadgets. Instant cash, free pickup, 12 months warranty.')">
    <meta property="og:image" content="@yield('og_image', asset('images/og-image.jpg'))">
    <meta property="og:url" content="@yield('og_url', url()->current())">
    <meta property="og:type" content="website">
    <meta property="og:site_name" content="RevoDevice">
    
    <!-- Twitter Card -->
    <meta name="twitter:card" content="summary_large_image">
    <meta name="twitter:title" content="@yield('twitter_title', 'RevoDevice - Buy & Sell Refurbished Devices')">
    <meta name="twitter:description" content="@yield('twitter_description', 'Sell old gadgets instantly or buy certified refurbished devices with warranty. Best prices guaranteed.')">
    <meta name="twitter:image" content="@yield('twitter_image', asset('images/twitter-card.jpg'))">
    
    <!-- Canonical URL -->
    <link rel="canonical" href="@yield('canonical_url', url()->current())">
    
    <!-- Title -->
    <title>@yield('title', 'RevoDevice - Buy & Sell Certified Refurbished Devices | Best Prices')</title>
    
    <!-- Preconnect for faster loading -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    
    <!-- Google Fonts -->
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700;800&display=swap" rel="stylesheet">
    
    <!-- Font Awesome -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    
    <!-- JSON-LD Structured Data for Organization -->
     @verbatim

    <script type="application/ld+json">
    {
        "@context": "https://schema.org",
        "@type": "Organization",
        "name": "RevoDevice",
        "url": "https://yourdomain.com",
        "logo": "https://yourdomain.com/images/logo.png",
        "description": "India's most trusted platform for buying and selling certified refurbished devices",
        "address": {
            "@type": "PostalAddress",
            "streetAddress": "701, Phoenix Tower, Andheri East",
            "addressLocality": "Mumbai",
            "addressRegion": "MH",
            "postalCode": "400093",
            "addressCountry": "IN"
        },
        "contactPoint": {
            "@type": "ContactPoint",
            "telephone": "+91-98765-43210",
            "contactType": "customer service",
            "availableLanguage": ["English", "Hindi"]
        },
        "sameAs": [
            "https://www.facebook.com/revodevice",
            "https://www.instagram.com/revodevice",
            "https://twitter.com/revodevice"
        ]
    }
    </script>
    @endverbatim
    
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        body {
            font-family: 'Inter', sans-serif;
            background: #0a0a0f;
            color: #e2e8f0;
        }

        .container {
            max-width: 1280px;
            margin: 0 auto;
            padding: 0 40px;
        }

        ::-webkit-scrollbar {
            width: 8px;
        }
        ::-webkit-scrollbar-track {
            background: #1a1a2e;
        }
        ::-webkit-scrollbar-thumb {
            background: #3b3b5c;
            border-radius: 10px;
        }
        ::-webkit-scrollbar-thumb:hover {
            background: #4a4a6e;
        }

        .card-dark {
            background: #111118;
            border: 1px solid #1e1e2a;
            border-radius: 20px;
            transition: all 0.3s;
        }

        .card-dark:hover {
            transform: translateY(-5px);
            border-color: #3b82f6;
            box-shadow: 0 20px 35px -10px rgba(0,0,0,0.3);
        }

        .gradient-text {
            background: linear-gradient(135deg, #60a5fa, #a78bfa);
            -webkit-background-clip: text;
            background-clip: text;
            color: transparent;
        }

        .btn-gradient {
            background: linear-gradient(135deg, #3b82f6, #8b5cf6);
            border: none;
            transition: all 0.3s;
        }
        .btn-gradient:hover {
            transform: translateY(-2px);
            box-shadow: 0 10px 25px -5px rgba(59,130,246,0.4);
        }

        @media (max-width: 768px) {
            .container {
                padding: 0 20px;
            }
        }
    </style>
    
   <style>
    * {
        margin: 0;
        padding: 0;
        box-sizing: border-box;
    }

    body {
        font-family: 'Inter', sans-serif;
        background: #0a0a0f;
        color: #e2e8f0;
        overflow-x: hidden;
        width: 100%;
        position: relative;
    }

    .container {
        max-width: 1280px;
        margin: 0 auto;
        padding: 0 20px;
        width: 100%;
        overflow-x: hidden;
    }

    /* Fix for all sections */
    main {
        overflow-x: hidden;
        width: 100%;
    }

    /* Fix for all grids */
    [class*="grid"] {
        width: 100%;
    }

    /* Ensure no overflow */
    img, iframe, video {
        max-width: 100%;
        height: auto;
    }

    @media (max-width: 768px) {
        .container {
            padding: 0 15px;
        }
    }
</style>

    @stack('styles')
</head>
<body>
    @include('components.whatsapp-float')
    @include('components.header')
    @include('components.sub-header')
    
    <main>
        @yield('content')
    </main>
    
    @include('components.footer')
    
    @stack('scripts')
</body>
</html>