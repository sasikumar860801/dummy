<footer style="background: #0a0a0f; border-top: 1px solid #1e1e2a; padding: 50px 0 30px; margin-top: 40px; width: 100%; overflow-x: hidden;">
    <div class="container" style="max-width: 1280px; margin: 0 auto; padding: 0 20px; width: 100%; overflow-x: hidden;">
        <div class="footer-grid" style="display: grid; grid-template-columns: repeat(auto-fit, minmax(200px, 1fr)); gap: 40px; margin-bottom: 40px; width: 100%;">
            
            <!-- Brand Section -->
            <div class="footer-brand">
                <h2 style="font-size: 24px; background: linear-gradient(135deg, #60a5fa, #a78bfa); -webkit-background-clip: text; background-clip: text; color: transparent; margin-bottom: 15px;">RevoDevice</h2>
                <p style="color: #94a3b8; font-size: 13px; line-height: 1.6; margin-bottom: 20px;">India's most trusted platform for buying and selling certified refurbished devices.</p>
                <div class="social-icons" style="display: flex; gap: 12px; flex-wrap: wrap;">
                    <a href="#" aria-label="Facebook" style="color: #64748b; font-size: 18px; transition: color 0.2s; display: inline-block; padding: 6px;"><i class="fab fa-facebook"></i></a>
                    <a href="#" aria-label="Instagram" style="color: #64748b; font-size: 18px; transition: color 0.2s; display: inline-block; padding: 6px;"><i class="fab fa-instagram"></i></a>
                    <a href="#" aria-label="YouTube" style="color: #64748b; font-size: 18px; transition: color 0.2s; display: inline-block; padding: 6px;"><i class="fab fa-youtube"></i></a>
                </div>
            </div>
            
            <!-- Company Links -->
            <div class="footer-links">
                <h4 style="color: white; margin-bottom: 15px; font-size: 16px;">Company</h4>
                <ul style="list-style: none; padding: 0; margin: 0;">
                    <li style="margin-bottom: 10px;"><a href="{{ route('about-us') }}" style="color: #94a3b8; text-decoration: none; font-size: 13px; transition: color 0.2s; display: inline-block; padding: 4px 0;">About Us</a></li>
                    <li style="margin-bottom: 10px;"><a href="{{ route('dealer-register') }}" style="color: #94a3b8; text-decoration: none; font-size: 13px; transition: color 0.2s; display: inline-block; padding: 4px 0;">Partner with us</a></li>
                </ul>
            </div>

            <!-- Support Links -->
            <div class="footer-links">
                <h4 style="color: white; margin-bottom: 15px; font-size: 16px;">Support</h4>
                <ul style="list-style: none; padding: 0; margin: 0;">
                    <li style="margin-bottom: 10px;"><a href="{{ route('contact-us') }}" style="color: #94a3b8; text-decoration: none; font-size: 13px; transition: color 0.2s; display: inline-block; padding: 4px 0;">Contact Us</a></li>
                    <li style="margin-bottom: 10px;"><a href="{{ route('privacy-policy') }}" style="color: #94a3b8; text-decoration: none; font-size: 13px; transition: color 0.2s; display: inline-block; padding: 4px 0;">Privacy Policy</a></li>
                    <li style="margin-bottom: 10px;"><a href="{{ route('terms-and-conditions') }}" style="color: #94a3b8; text-decoration: none; font-size: 13px; transition: color 0.2s; display: inline-block; padding: 4px 0;">Terms &amp; Conditions</a></li>
                    <li style="margin-bottom: 10px;"><a href="{{ route('faq') }}" style="color: #94a3b8; text-decoration: none; font-size: 13px; transition: color 0.2s; display: inline-block; padding: 4px 0;">FAQ</a></li>
                </ul>
            </div>
            
            <!-- Contact Info -->
            <div class="footer-contact">
                <h4 style="color: white; margin-bottom: 15px; font-size: 16px;">Contact Info</h4>
                <ul style="list-style: none; padding: 0; margin: 0;">
                    <li style="margin-bottom: 12px; display: flex; gap: 10px; font-size: 12px; color: #94a3b8; align-items: flex-start;">
                        <i class="fas fa-map-marker-alt" style="color: #3b82f6; margin-top: 2px; min-width: 16px;"></i>
                        <span style="line-height: 1.5;">701, Phoenix Tower, Andheri East, Mumbai</span>
                    </li>
                    <li style="margin-bottom: 12px; display: flex; gap: 10px; font-size: 12px; color: #94a3b8; align-items: center;">
                        <i class="fas fa-phone" style="color: #3b82f6; min-width: 16px;"></i>
                        <span>+91 98765 43210</span>
                    </li>
                    <li style="margin-bottom: 12px; display: flex; gap: 10px; font-size: 12px; color: #94a3b8; align-items: center;">
                        <i class="fas fa-envelope" style="color: #3b82f6; min-width: 16px;"></i>
                        <span>hello@revodevice.com</span>
                    </li>
                </ul>
            </div>
        </div>
        
        <!-- Bottom Bar -->
        <div style="text-align: center; padding-top: 25px; border-top: 1px solid #1e1e2a; font-size: 12px; color: #64748b;">
            <p style="margin: 0;">&copy; 2025 RevoDevice. All rights reserved. | Buy &amp; Sell Refurbished Devices</p>
        </div>
    </div>
</footer>

<style>
    /* Hover effect */
    footer a:hover {
        color: #ffffff !important;
    }
    
    /* Tablet */
    @media (max-width: 768px) {
        .footer-grid {
            grid-template-columns: repeat(2, 1fr) !important;
            gap: 30px !important;
        }
        
        footer .container {
            padding: 0 16px !important;
        }
    }
    
    /* Mobile */
    @media (max-width: 480px) {
        footer {
            padding: 40px 0 20px;
        }
        
        .footer-grid {
            grid-template-columns: 1fr !important;
            gap: 30px !important;
            text-align: center;
        }
        
        .footer-brand {
            text-align: center;
        }
        
        .footer-brand p {
            max-width: 300px;
            margin-left: auto;
            margin-right: auto;
        }
        
        .social-icons {
            justify-content: center !important;
        }
        
        .footer-contact ul li {
            justify-content: center !important;
            flex-direction: column !important;
            align-items: center !important;
            gap: 4px !important;
            text-align: center;
        }
        
        .footer-contact ul li i {
            margin-top: 0 !important;
        }
        
        .footer-links ul li a {
            padding: 6px 0 !important;
        }
    }
</style>