<div class="sub-header" style="background: #0f0f15; border-bottom: 1px solid #1e1e2a;  top: 72px; z-index: 999; width: 100%;">
    <div class="container" style="max-width: 1280px; margin: 0 auto; padding: 0 20px; width: 100%;">
        <div style="display: flex; align-items: center; justify-content: space-between; gap: 15px; padding: 12px 0;">
            
            <!-- Navigation Links -->
            <div style="display: flex; gap: 20px; align-items: center; flex-wrap: wrap; width: 100%;">
                
                <!-- Buy Dropdown -->
                <div class="dropdown" style="position: relative; z-index: 100;">
                    <a href="#" class="dropdown-toggle" style="color: #cbd5e1; text-decoration: none; display: flex; align-items: center; gap: 5px; font-size: 14px; white-space: nowrap; cursor: pointer;">
                        Buy Devices <i class="fas fa-angle-down"></i>
                    </a>
                    <div class="dropdown-content" style="display: none; position: absolute; top: 100%; left: 0; background: #1a1a2e; min-width: 180px; border-radius: 12px; padding: 8px 0; border: 1px solid #2a2a3a; z-index: 1000; box-shadow: 0 10px 25px -5px rgba(0,0,0,0.3);">
                        <a href="{{ route('all_refubrished_phones') }}" style="display: block; padding: 10px 20px; color: #cbd5e1; text-decoration: none; font-size: 13px; white-space: nowrap;">Mobiles</a>
                        <a href="#" onclick="alert('Laptop buying service will be available soon. Stay tuned!'); return false;" style="display: block; padding: 10px 20px; color: #cbd5e1; text-decoration: none; font-size: 13px; white-space: nowrap;">Laptops</a>
                        <a href="#" onclick="alert('Tablet buying service will be available soon. Stay tuned!'); return false;" style="display: block; padding: 10px 20px; color: #cbd5e1; text-decoration: none; font-size: 13px; white-space: nowrap;">Tablets</a>
                    </div>
                </div>
                
                <!-- Sell Dropdown -->
                <div class="dropdown" style="position: relative; z-index: 100;">
                    <a href="#" class="dropdown-toggle" style="color: #cbd5e1; text-decoration: none; display: flex; align-items: center; gap: 5px; font-size: 14px; white-space: nowrap; cursor: pointer;">
                        Sell Devices <i class="fas fa-angle-down"></i>
                    </a>
                    <div class="dropdown-content" style="display: none; position: absolute; top: 100%; left: 0; background: #1a1a2e; min-width: 180px; border-radius: 12px; padding: 8px 0; border: 1px solid #2a2a3a; z-index: 1000; box-shadow: 0 10px 25px -5px rgba(0,0,0,0.3);">
                        <a href="{{ route('brands.all') }}" style="display: block; padding: 10px 20px; color: #cbd5e1; text-decoration: none; font-size: 13px; white-space: nowrap;">Sell Mobile</a>
                        <a href="#" onclick="alert('Laptop selling service will be available soon. Stay tuned!'); return false;" style="display: block; padding: 10px 20px; color: #cbd5e1; text-decoration: none; font-size: 13px; white-space: nowrap;">Sell Laptop</a>
                        <a href="{{ route('tablet.brands') }}" style="display: block; padding: 10px 20px; color: #cbd5e1; text-decoration: none; font-size: 13px; white-space: nowrap;">Sell Tablet</a>
                    </div>
                </div>
                
                <!-- More Dropdown -->
                <div class="dropdown" style="position: relative; z-index: 100;">
                    <a href="#" class="dropdown-toggle" style="color: #cbd5e1; text-decoration: none; display: flex; align-items: center; gap: 5px; font-size: 14px; white-space: nowrap; cursor: pointer;">
                        More <i class="fas fa-angle-down"></i>
                    </a>
                    <div class="dropdown-content" style="display: none; position: absolute; top: 100%; left: 0; background: #1a1a2e; min-width: 200px; border-radius: 12px; padding: 8px 0; border: 1px solid #2a2a3a; z-index: 1000; box-shadow: 0 10px 25px -5px rgba(0,0,0,0.3);">
                        <a href="{{ route('about-us') }}" style="display: block; padding: 10px 20px; color: #cbd5e1; text-decoration: none; font-size: 13px; white-space: nowrap;">About Us</a>
                        <a href="{{ route('contact-us') }}" style="display: block; padding: 10px 20px; color: #cbd5e1; text-decoration: none; font-size: 13px; white-space: nowrap;">Contact Us</a>
                        <a href="{{ route('privacy-policy') }}" style="display: block; padding: 10px 20px; color: #cbd5e1; text-decoration: none; font-size: 13px; white-space: nowrap;">Privacy Policy</a>
                        <a href="{{ route('terms-and-conditions') }}" style="display: block; padding: 10px 20px; color: #cbd5e1; text-decoration: none; font-size: 13px; white-space: nowrap;">Terms &amp; Conditions</a>
                        <a href="{{ route('faq') }}" style="display: block; padding: 10px 20px; color: #cbd5e1; text-decoration: none; font-size: 13px; white-space: nowrap;">FAQ</a>
                    </div>
                </div>
                
                <a href="{{ route('dealer-register') }}" style="color: #cbd5e1; text-decoration: none; font-size: 14px; white-space: nowrap;">Partner with us</a>
            </div>
            
        </div>
    </div>
</div>

<style>
    .sub-header {
        overflow: visible !important;
    }
    
    .sub-header .container {
        overflow: visible !important;
    }
    
    .sub-header .container > div {
        overflow: visible !important;
    }
    
    .sub-header .container > div > div {
        overflow: visible !important;
    }
    
    .dropdown-content a:hover {
        background: #2a2a3a;
        color: #ffffff !important;
    }
    
    /* Desktop hover */
    @media (min-width: 769px) {
        .dropdown:hover .dropdown-content {
            display: block !important;
        }
    }
    
    /* Mobile click */
    @media (max-width: 768px) {
        .sub-header {
            overflow: visible !important;
            top: 60px;
        }
        
        .sub-header .container {
            padding: 0 12px !important;
            overflow: visible !important;
        }
        
        .sub-header .container > div {
            padding: 10px 0 !important;
            overflow: visible !important;
        }
        
        .sub-header .container > div > div {
            gap: 12px !important;
            flex-wrap: wrap !important;
            overflow: visible !important;
        }
        
        .dropdown {
            position: relative !important;
        }
        
        .dropdown a {
            font-size: 13px !important;
        }
        
        .dropdown-content {
            min-width: 160px !important;
            left: -10px !important;
            position: absolute !important;
            top: 100% !important;
        }
        
        .dropdown-content a {
            font-size: 12px !important;
            padding: 8px 16px !important;
            white-space: nowrap !important;
        }
        
        .sub-header a[href="{{ route('dealer-register') }}"] {
            font-size: 13px !important;
        }
    }
    
    @media (max-width: 480px) {
        .sub-header .container {
            padding: 0 10px !important;
        }
        
        .sub-header .container > div > div {
            gap: 8px !important;
        }
        
        .dropdown a {
            font-size: 12px !important;
        }
        
        .dropdown-content {
            min-width: 140px !important;
            left: -8px !important;
        }
        
        .dropdown-content a {
            font-size: 11px !important;
            padding: 6px 14px !important;
        }
    }
</style>

<!-- JavaScript for mobile click support -->
<script>
document.addEventListener('DOMContentLoaded', function() {
    // Get all dropdown toggles
    var toggles = document.querySelectorAll('.dropdown-toggle');
    
    toggles.forEach(function(toggle) {
        toggle.addEventListener('click', function(e) {
            e.preventDefault();
            e.stopPropagation();
            
            // Get the parent dropdown
            var dropdown = this.closest('.dropdown');
            var content = dropdown.querySelector('.dropdown-content');
            
            // Close all other dropdowns
            var allContents = document.querySelectorAll('.dropdown-content');
            allContents.forEach(function(item) {
                if (item !== content) {
                    item.style.display = 'none';
                }
            });
            
            // Toggle this dropdown
            if (content.style.display === 'block') {
                content.style.display = 'none';
            } else {
                content.style.display = 'block';
            }
        });
    });
    
    // Close dropdowns when clicking outside
    document.addEventListener('click', function(e) {
        if (!e.target.closest('.dropdown')) {
            var allContents = document.querySelectorAll('.dropdown-content');
            allContents.forEach(function(item) {
                item.style.display = 'none';
            });
        }
    });
});
</script>