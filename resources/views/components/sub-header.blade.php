<div class="sub-header" style="background: #0f0f15; border-bottom: 1px solid #1e1e2a;  top: 72px; z-index: 99; width: 100%; overflow: visible;">
    <div class="container" style="max-width: 1280px; margin: 0 auto; padding: 0 20px; width: 100%; overflow: visible;">
        <div style="display: flex; align-items: center; justify-content: space-between; flex-wrap: wrap; gap: 15px; padding: 12px 0; overflow: visible;">
            
            <!-- Navigation Links -->
            <div style="display: flex; gap: 20px; align-items: center; flex-wrap: wrap; overflow: visible;">
                
                <!-- Buy Dropdown -->
                <div class="dropdown" style="position: relative; z-index: 100;">
                    <a href="#" style="color: #cbd5e1; text-decoration: none; display: flex; align-items: center; gap: 5px; font-size: 14px;">
                        Buy Devices <i class="fas fa-angle-down"></i>
                    </a>
                    <div class="dropdown-content" style="position: absolute; top: 100%; left: 0; background: #1a1a2e; min-width: 180px; border-radius: 12px; padding: 8px 0; opacity: 0; visibility: hidden; transition: all 0.2s; border: 1px solid #2a2a3a; z-index: 1000; box-shadow: 0 10px 25px -5px rgba(0,0,0,0.3);">
                        <a href="#" style="display: block; padding: 10px 20px; color: #cbd5e1; text-decoration: none; font-size: 13px;">Mobiles</a>
                        <a href="#" style="display: block; padding: 10px 20px; color: #cbd5e1; text-decoration: none; font-size: 13px;">Laptops</a>
                        <a href="#" style="display: block; padding: 10px 20px; color: #cbd5e1; text-decoration: none; font-size: 13px;">Tablets</a>
                    </div>
                </div>
                
                <!-- Sell Dropdown -->
                <div class="dropdown" style="position: relative; z-index: 100;">
                    <a href="#" style="color: #cbd5e1; text-decoration: none; display: flex; align-items: center; gap: 5px; font-size: 14px;">
                        Sell Devices <i class="fas fa-angle-down"></i>
                    </a>
                    <div class="dropdown-content" style="position: absolute; top: 100%; left: 0; background: #1a1a2e; min-width: 180px; border-radius: 12px; padding: 8px 0; opacity: 0; visibility: hidden; transition: all 0.2s; border: 1px solid #2a2a3a; z-index: 1000; box-shadow: 0 10px 25px -5px rgba(0,0,0,0.3);">
                        <a href="#" style="display: block; padding: 10px 20px; color: #cbd5e1; text-decoration: none; font-size: 13px;">Sell Mobile</a>
                        <a href="#" style="display: block; padding: 10px 20px; color: #cbd5e1; text-decoration: none; font-size: 13px;">Sell Laptop</a>
                        <a href="#" style="display: block; padding: 10px 20px; color: #cbd5e1; text-decoration: none; font-size: 13px;">Sell Tablet</a>
                    </div>
                </div>
                
                <!-- More Dropdown -->
                <div class="dropdown" style="position: relative; z-index: 100;">
                    <a href="#" style="color: #cbd5e1; text-decoration: none; display: flex; align-items: center; gap: 5px; font-size: 14px;">
                        More <i class="fas fa-angle-down"></i>
                    </a>
                    <div class="dropdown-content" style="position: absolute; top: 100%; left: 0; background: #1a1a2e; min-width: 200px; border-radius: 12px; padding: 8px 0; opacity: 0; visibility: hidden; transition: all 0.2s; border: 1px solid #2a2a3a; z-index: 1000; box-shadow: 0 10px 25px -5px rgba(0,0,0,0.3);">
                        <a href="#" style="display: block; padding: 10px 20px; color: #cbd5e1; text-decoration: none; font-size: 13px;">Contact Us</a>
                        <a href="#" style="display: block; padding: 10px 20px; color: #cbd5e1; text-decoration: none; font-size: 13px;">Privacy Policy</a>
                        <a href="#" style="display: block; padding: 10px 20px; color: #cbd5e1; text-decoration: none; font-size: 13px;">About Us</a>
                        <a href="#" style="display: block; padding: 10px 20px; color: #cbd5e1; text-decoration: none; font-size: 13px;">FAQ</a>
                    </div>
                </div>
                
                <a href="#" style="color: #cbd5e1; text-decoration: none; font-size: 14px;">Get Franchise</a>
            </div>
            
            
        </div>
    </div>
</div>

<style>
    .sub-header {
        overflow: visible !important;
    }
    
    .dropdown:hover .dropdown-content {
        opacity: 1 !important;
        visibility: visible !important;
    }
    
    .dropdown-content a:hover {
        background: #2a2a3a;
    }
    
    /* Desktop dropdown fix */
    @media (min-width: 769px) {
        .dropdown-content {
            position: absolute;
            top: 100%;
            left: 0;
        }
    }
    
    /* Mobile dropdown fix */
    @media (max-width: 768px) {
        .dropdown {
            position: relative;
        }
        
        .dropdown-content {
            position: absolute;
            top: 100%;
            left: 0;
            right: auto;
            min-width: 200px;
        }
    }
</style>