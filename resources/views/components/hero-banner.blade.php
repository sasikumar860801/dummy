<div class="premium-hero-banner">
    <!-- Ambient Animated Light Flares -->
    <div class="ambient-glow flare-1"></div>
    <div class="ambient-glow flare-2"></div>

    <!-- Content Split Layout -->
    <div class="hero-layout-grid">
        
        <!-- Left Column: Typography & Actions -->
        <div class="hero-text-content">
            <h2 class="hero-main-title">
                SELL OLD GADGETS.<br>
                <span class="hero-gradient-text">BUY SUPERB REFURBISHED</span>
            </h2>
            <p class="hero-tagline">Get instant price quote & free doorstep pickup</p>
            
            <div class="hero-action-cluster">
                <button class="hero-btn-primary" onclick="window.location.href='{{ url('/sell-old-phone') }}'">
                    Sell Now <span class="btn-arrow">→</span>
                </button>
                <button class="hero-btn-secondary">
                    Buy Now <span class="btn-arrow">→</span>
                </button>
            </div>
        </div>

        <!-- Right Column: Premium High-Tech CSS Art Backdrop -->
        <div class="hero-visual-graphic">
            <div class="glass-dashboard-canvas">
                <div class="floating-glass-card card-main">
                    <span class="pulse-indicator"></span>
                    <div class="skeleton-line short"></div>
                    <div class="skeleton-line long"></div>
                </div>
                <div class="floating-glass-card card-sub">
                    <div class="chart-mockup-bars">
                        <span class="bar bar-1"></span>
                        <span class="bar bar-2"></span>
                        <span class="bar bar-3"></span>
                    </div>
                </div>
            </div>
        </div>

    </div>
</div>

<style>
    /* Main Hero Banner Housing Block */
    .premium-hero-banner {
        background: radial-gradient(circle at 0% 0%, #1e1b4b 0%, #0f172a 100%);
        border: 1px solid rgba(255, 255, 255, 0.05);
        border-radius: 32px; 
        margin: 50px 0; 
        padding: 60px 80px; 
        position: relative; 
        overflow: hidden;
        box-sizing: border-box;
    }

    /* Moving Ambient Gradient Lighting Mesh */
    .ambient-glow {
        position: absolute;
        border-radius: 50%;
        filter: blur(80px);
        pointer-events: none;
        opacity: 0.4;
        z-index: 1;
    }
    .flare-1 {
        top: -10%;
        right: 10%;
        width: 300px;
        height: 300px;
        background: #3b82f6;
        animation: ambientFloat 8s ease-in-out infinite alternate;
    }
    .flare-2 {
        bottom: -20%;
        left: 30%;
        width: 250px;
        height: 250px;
        background: #8b5cf6;
        animation: ambientFloat 12s ease-in-out infinite alternate-reverse;
    }

    /* Grid Layout Split Architecture */
    .hero-layout-grid {
        display: grid;
        grid-template-columns: 1.2fr 0.8fr;
        align-items: center;
        gap: 40px;
        position: relative;
        z-index: 2;
    }

    /* Text Components System */
    .hero-text-content {
        text-align: left;
    }

    .hero-main-title {
        color: white; 
        font-size: 44px; 
        font-weight: 800; 
        line-height: 1.2;
        margin: 0 0 16px 0;
        letter-spacing: -0.02em;
    }

    .hero-gradient-text {
        background: linear-gradient(90deg, #60a5fa, #a78bfa);
        -webkit-background-clip: text;
        -webkit-text-fill-color: transparent;
    }

    .hero-tagline {
        color: #94a3b8; 
        font-size: 17px; 
        margin: 0 0 35px 0;
        font-weight: 400;
    }

    /* Button Action Systems Styling */
    .hero-action-cluster {
        display: flex; 
        gap: 16px;
    }

    .hero-btn-primary, .hero-btn-secondary {
        padding: 14px 36px; 
        border-radius: 50px; 
        font-size: 15px;
        font-weight: 600; 
        cursor: pointer;
        display: inline-flex;
        align-items: center;
        gap: 8px;
        box-sizing: border-box;
        transition: transform 0.3s cubic-bezier(0.25, 0.8, 0.25, 1),
                    background 0.3s ease,
                    border-color 0.3s ease,
                    box-shadow 0.3s ease;
    }

    .hero-btn-primary {
        background: #ffffff; 
        color: #0f172a; 
        border: none;
        box-shadow: 0 4px 15px rgba(255, 255, 255, 0.1);
    }

    .hero-btn-secondary {
        background: transparent; 
        border: 2px solid rgba(255, 255, 255, 0.2); 
        color: white; 
    }

    /* Button Micro Interactions Hover States */
    .btn-arrow {
        transition: transform 0.3s cubic-bezier(0.25, 0.8, 0.25, 1);
    }
    
    .hero-btn-primary:hover {
        background: #f8fafc;
        transform: translateY(-2px);
        box-shadow: 0 10px 25px rgba(255, 255, 255, 0.15);
    }
    
    .hero-btn-secondary:hover {
        border-color: #ffffff;
        background: rgba(255, 255, 255, 0.05);
        transform: translateY(-2px);
    }

    .hero-btn-primary:hover .btn-arrow, .hero-btn-secondary:hover .btn-arrow {
        transform: translateX(4px);
    }

    /* Pure CSS Decorative Vector System */
    .hero-visual-graphic {
        display: flex;
        justify-content: center;
        align-items: center;
        width: 100%;
        height: 100%;
    }

    .glass-dashboard-canvas {
        position: relative;
        width: 260px;
        height: 200px;
    }

    .floating-glass-card {
        position: absolute;
        background: rgba(255, 255, 255, 0.03);
        backdrop-filter: blur(12px);
        -webkit-backdrop-filter: blur(12px);
        border: 1px solid rgba(255, 255, 255, 0.08);
        border-radius: 16px;
        padding: 16px;
        box-sizing: border-box;
    }

    .card-main {
        width: 180px;
        height: 110px;
        top: 10px;
        left: 10px;
        z-index: 3;
        animation: shapeFloat 5s ease-in-out infinite alternate;
    }

    .card-sub {
        width: 140px;
        height: 100px;
        bottom: 10px;
        right: 10px;
        z-index: 2;
        animation: shapeFloat 7s ease-in-out infinite alternate-reverse;
    }

    /* Inside card decoration details elements */
    .pulse-indicator {
        display: block;
        width: 10px;
        height: 10px;
        background: #10b981;
        border-radius: 50%;
        margin-bottom: 12px;
        box-shadow: 0 0 8px #10b981;
    }

    .skeleton-line {
        height: 6px;
        background: rgba(255, 255, 255, 0.1);
        border-radius: 4px;
        margin-bottom: 8px;
    }
    .skeleton-line.short { width: 40%; }
    .skeleton-line.long { width: 85%; margin-bottom: 0; }

    .chart-mockup-bars {
        display: flex;
        align-items: flex-end;
        gap: 8px;
        height: 100%;
        padding-top: 15px;
        box-sizing: border-box;
    }

    .chart-mockup-bars .bar {
        flex: 1;
        border-radius: 4px 4px 0 0;
        background: rgba(59, 130, 246, 0.3);
    }
    .chart-mockup-bars .bar-1 { height: 40%; }
    .chart-mockup-bars .bar-2 { height: 85%; background: linear-gradient(to top, #3b82f6, #60a5fa); }
    .chart-mockup-bars .bar-3 { height: 60%; }

    /* Keyframes Animations Sequences Matrix */
    @keyframes ambientFloat {
        0% { transform: translateY(0) scale(1); }
        100% { transform: translateY(-20px) scale(1.1); }
    }

    @keyframes shapeFloat {
        0% { transform: translateY(0); }
        100% { transform: translateY(-12px); }
    }

    /* --- Responsive Viewports Framework Adaptations --- */

    /* Tablet/Medium Screen adjustments overrides layout */
    @media (max-width: 1024px) {
        .premium-hero-banner {
            padding: 45px 50px;
        }
        .hero-main-title {
            font-size: 36px;
        }
        .glass-dashboard-canvas {
            transform: scale(0.85);
        }
    }

    /* Responsive Mobile Screen Transformations Blueprint */
    @media (max-width: 768px) {
        .premium-hero-banner {
            padding: 40px 30px;
            border-radius: 24px;
            margin: 30px 0;
        }
        
        .hero-layout-grid {
            grid-template-columns: 1fr; /* Stack columns vertically down */
            gap: 30px;
            text-align: center;
        }

        .hero-text-content {
            text-align: center;
            display: flex;
            flex-direction: column;
            align-items: center;
        }

        .hero-main-title {
            font-size: 30px;
        }

        .hero-tagline {
            font-size: 15px;
            margin-bottom: 25px;
        }

        .hero-action-cluster {
            width: 100%;
            flex-direction: column; /* Stack functional paths up row buttons */
            gap: 12px;
        }

        .hero-btn-primary, .hero-btn-secondary {
            width: 100%;
            justify-content: center;
            padding: 12px 24px;
        }

        /* Hide the floating CSS graphics module frame on mobile layout viewports to preserve clean spacing */
        .hero-visual-graphic {
            display: none;
        }
    }
</style>


