<div style="display: flex; gap: 20px; margin-bottom: 50px; flex-wrap: wrap;">

    <!-- Mobiles -->
    <a href="{{ url('/sell-old-phone') }}"
       style="flex: 1; text-decoration: none; color: inherit;">

        <div class="card-dark"
             style="padding: 30px; text-align: center; cursor: pointer;">

            <i class="fas fa-mobile-alt"
               style="font-size: 40px; color: #3b82f6; margin-bottom: 15px;"></i>

            <h4 style="font-size: 18px; margin-bottom: 5px;">
                Mobiles
            </h4>

            <p style="font-size: 12px; color: #94a3b8;">
                iPhone · Samsung · Pixel
            </p>

        </div>
    </a>

    <!-- Tablets -->
    <a href="{{ url('/sell-old-tablet') }}"
       style="flex: 1; text-decoration: none; color: inherit;">

        <div class="card-dark"
             style="padding: 30px; text-align: center; cursor: pointer;">

            <i class="fas fa-tablet-alt"
               style="font-size: 40px; color: #8b5cf6; margin-bottom: 15px;"></i>

            <h4 style="font-size: 18px; margin-bottom: 5px;">
                Tablets
            </h4>

            <p style="font-size: 12px; color: #94a3b8;">
                iPad · Samsung Tab
            </p>

        </div>
    </a>

    <!-- Laptops -->
    <div class="card-dark"
         onclick="alert('Future update: Laptop & PC selling will be available soon!')"
         style="flex: 1; padding: 30px; text-align: center; cursor: pointer;">

        <i class="fas fa-laptop"
           style="font-size: 40px; color: #10b981; margin-bottom: 15px;"></i>

        <h4 style="font-size: 18px; margin-bottom: 5px;">
            Laptops
        </h4>

        <p style="font-size: 12px; color: #94a3b8;">
            MacBook · Dell · HP
        </p>

    </div>

</div>