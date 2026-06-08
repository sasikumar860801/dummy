
<div class="premium-categories-row">
    <a href="{{ url('/sell-old-phone') }}" class="category-link">
        <div class="premium-category-card mobile-accent">
            <div class="card-glow-layer"></div>
            
            <div class="category-media-frame">
                <div class="device-mockup phone-mock">
                    <span class="inner-screen-glow"></span>
                </div>
            </div>

            <h4 class="category-card-title">Mobiles</h4>
            <p class="category-card-subtitle">iPhone • Samsung • Pixel</p>
            <span class="action-arrow-tag">Explore <i class="fas fa-chevron-right"></i></span>
        </div>
    </a>

    <a href="{{ url('/sell-old-tablet') }}" class="category-link">
        <div class="premium-category-card tablet-accent">
            <div class="card-glow-layer"></div>
            
            <div class="category-media-frame">
                <div class="device-mockup tablet-mock">
                    <span class="inner-screen-glow"></span>
                </div>
            </div>

            <h4 class="category-card-title">Tablets</h4>
            <p class="category-card-subtitle">iPad • Samsung Tab</p>
            <span class="action-arrow-tag">Explore <i class="fas fa-chevron-right"></i></span>
        </div>
    </a>

    <div class="premium-category-card laptop-accent coming-soon-disabled" 
         onclick="alert('Future update: Laptop & PC selling will be available soon!')">
        <div class="card-glow-layer"></div>
        
        <div class="coming-soon-ribbon">Soon</div>
        
        <div class="category-media-frame">
            <div class="device-mockup laptop-mock">
                <span class="laptop-base"></span>
            </div>
        </div>

        <h4 class="category-card-title">Laptops</h4>
        <p class="category-card-subtitle">MacBook • Dell • HP</p>
        <span class="action-arrow-tag text-muted">Coming Soon</span>
    </div>

</div>

<style>
    /* Main Parent Container */
    .premium-categories-row {
        display: flex; 
        gap: 25px; 
        margin-bottom: 60px; 
        width: 100%;
        box-sizing: border-box;
    }

    .category-link {
        flex: 1; 
        text-decoration: none; 
        color: inherit;
        display: block;
    }

    /* Core Premium Isometric Card Architectural Blueprint */
    .premium-category-card {
        background: #111118;
        border: 1px solid #1e1e2a;
        border-radius: 24px;
        padding: 35px 25px;
        text-align: center;
        cursor: pointer;
        position: relative;
        overflow: hidden;
        display: flex;
        flex-direction: column;
        align-items: center;
        box-sizing: border-box;
        height: 100%;
        transition: transform 0.4s cubic-bezier(0.25, 0.8, 0.25, 1),
                    border-color 0.4s cubic-bezier(0.25, 0.8, 0.25, 1),
                    box-shadow 0.4s cubic-bezier(0.25, 0.8, 0.25, 1);
    }

    /* Coming Soon Ribbon Tag */
    .coming-soon-ribbon {
        position: absolute;
        top: 15px;
        left: 15px;
        background: rgba(16, 185, 129, 0.1);
        color: #10b981;
        font-size: 11px;
        font-weight: 700;
        padding: 3px 10px;
        border-radius: 20px;
        border: 1px solid rgba(16, 185, 129, 0.2);
    }

    /* Radial Backlighting Layer */
    .card-glow-layer {
        position: absolute;
        top: 0;
        left: 0;
        width: 100%;
        height: 100%;
        opacity: 0;
        pointer-events: none;
        transition: opacity 0.4s ease;
        z-index: 1;
    }
    .mobile-accent .card-glow-layer { background: radial-gradient(circle at top, rgba(59,130,246,0.12) 0%, rgba(0,0,0,0) 60%); }
    .tablet-accent .card-glow-layer { background: radial-gradient(circle at top, rgba(139,92,246,0.12) 0%, rgba(0,0,0,0) 60%); }
    .laptop-accent .card-glow-layer { background: radial-gradient(circle at top, rgba(16,185,129,0.08) 0%, rgba(0,0,0,0) 60%); }

    /* Pure CSS Premium Device Mockup Illustrations Engine */
    .category-media-frame {
        height: 90px;
        display: flex;
        align-items: center;
        justify-content: center;
        margin-bottom: 25px;
        position: relative;
        z-index: 2;
    }

    /* Base Architecture for Device Shapes */
    .device-mockup {
        position: relative;
        border-radius: 8px;
        transition: transform 0.4s cubic-bezier(0.25, 0.8, 0.25, 1), filter 0.4s ease;
        box-shadow: 0 10px 25px rgba(0,0,0,0.5);
    }

    /* 3D Phone styling */
    .phone-mock {
        width: 34px;
        height: 65px;
        border: 3px solid #3b82f6;
        background: #09090e;
        border-radius: 7px;
        transform: rotate(-10deg) skewX(5deg);
    }
    /* 3D Tablet styling */
    .tablet-mock {
        width: 58px;
        height: 44px;
        border: 3px solid #8b5cf6;
        background: #09090e;
        border-radius: 6px;
        transform: rotate(12deg) skewY(-4deg);
    }
    /* 3D Laptop styling */
    .laptop-mock {
        width: 65px;
        height: 40px;
        border: 3px solid #475569;
        background: #09090e;
        border-radius: 4px;
        transform: translateY(-5px);
    }
    .laptop-base {
        position: absolute;
        bottom: -6px;
        left: -10px;
        width: 85px;
        height: 4px;
        background: #64748b;
        border-radius: 2px;
    }

    /* Premium Neon Screen Core Lighting Glows */
    .inner-screen-glow {
        position: absolute;
        top: 10%; left: 10%; right: 10%; bottom: 10%;
        border-radius: 3px;
        opacity: 0.6;
        transition: opacity 0.4s ease;
    }
    .phone-mock .inner-screen-glow { background: linear-gradient(135deg, #3b82f6, transparent); }
    .tablet-mock .inner-screen-glow { background: linear-gradient(135deg, #8b5cf6, transparent); }

    /* Text Component Architecture Styles */
    .category-card-title {
        font-size: 19px;
        font-weight: 700;
        color: #ffffff;
        margin: 0 0 6px 0;
        position: relative;
        z-index: 2;
    }

    .category-card-subtitle {
        font-size: 13px;
        color: #64748b;
        margin: 0 0 20px 0;
        position: relative;
        z-index: 2;
        transition: color 0.4s ease;
    }

    /* Micro Interact Link Indicator Footer */
    .action-arrow-tag {
        font-size: 12px;
        font-weight: 700;
        color: #3b82f6;
        display: flex;
        align-items: center;
        gap: 4px;
        opacity: 0;
        transform: translateY(5px);
        transition: opacity 0.3s ease, transform 0.3s ease;
        position: relative;
        z-index: 2;
    }
    .tablet-accent .action-arrow-tag { color: #8b5cf6; }
    .text-muted { color: #475569 !important; opacity: 1 !important; transform: none !important; font-weight: 500; }

    /* Master Interactive Animation Hover Loops Trigger states */
    .category-link:hover .premium-category-card {
        transform: translateY(-8px);
        box-shadow: 0 25px 40px -15px rgba(0,0,0,0.7);
    }
    
    .category-link:hover .mobile-accent { border-color: rgba(59, 130, 246, 0.45); }
    .category-link:hover .tablet-accent { border-color: rgba(139, 92, 246, 0.45); }
    
    .category-link:hover .card-glow-layer { opacity: 1; }
    
    /* Device floating up animations on hover actions */
    .category-link:hover .phone-mock { transform: rotate(-5deg) scale(1.1) translateY(-4px); border-color: #60a5fa; }
    .category-link:hover .tablet-mock { transform: rotate(6deg) scale(1.08) translateY(-4px); border-color: #a78bfa; }
    .category-link:hover .inner-screen-glow { opacity: 1; }
    
    /* Reveal footer button on hover actions */
    .category-link:hover .action-arrow-tag { opacity: 1; transform: translateY(0); }
    .category-link:hover .category-card-subtitle { color: #94a3b8; }

    /* Disabled State styling configuration controls */
    .coming-soon-disabled {
        flex: 1;
        cursor: not-allowed;
        opacity: 0.65;
    }

    /* --- Media Queries Responsiveness Blocks Matrix --- */
    
    /* Tablet / Small Desktop Viewports */
    @media (max-width: 900px) {
        .premium-categories-row {
            gap: 16px;
        }
        .premium-category-card {
            padding: 25px 15px;
            border-radius: 20px;
        }
        .category-card-title { font-size: 17px; }
        .category-card-subtitle { font-size: 12px; }
        .action-arrow-tag { opacity: 1; transform: none; } /* Persist visible on touch screens */
    }

    /* Mobile Devices Viewports Stack matrix rules */
    @media (max-width: 650px) {
        .premium-categories-row {
            flex-direction: column;
            gap: 15px;
        }
        .category-link {
            width: 100%;
        }
        .premium-category-card {
            flex-direction: row;
            text-align: left;
            padding: 20px;
            justify-content: flex-start;
            gap: 20px;
        }
        .category-media-frame {
            margin-bottom: 0;
            height: 50px;
            width: 60px;
        }
        /* Straighten vector rotations for flat row alignment lists inside small mobile views */
        .phone-mock { transform: scale(0.85); }
        .tablet-mock { transform: scale(0.75); }
        .laptop-mock { transform: scale(0.75) translateX(-5px); }
        
        .category-card-subtitle {
            margin-bottom: 0;
        }
        .action-arrow-tag {
            margin-left: auto; /* Push explicit target label right-aligned inside lists row */
        }
        
        .category-link:hover .phone-mock { transform: scale(0.9) translateY(-2px); }
        .category-link:hover .tablet-mock { transform: scale(0.8) translateY(-2px); }
        .coming-soon-ribbon {
            position: relative;
            top: unset; left: unset;
            margin-left: auto;
            order: 4;
        }
    }
</style>