<div class="card-dark" style="padding: 35px;">
    <h3 style="font-size: 24px; margin-bottom: 20px;">Why Sell with Us?</h3>
    <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 20px;">
        @php
        $features = [
            ['icon' => 'dollar-sign', 'title' => 'Best Showroom', 'desc' => 'Price match'],
            ['icon' => 'clock', 'title' => 'Instant Cash', 'desc' => 'Same day payment'],
            ['icon' => 'truck-fast', 'title' => 'Free Pickup', 'desc' => 'Doorstep service'],
            ['icon' => 'gift', 'title' => 'Self Service Bonus', 'desc' => 'Get extra ₹300 when you courier your device to us']
        ];
        @endphp
        @foreach($features as $feature)
        <div style="text-align: center; padding: 15px; background: #1a1a2e; border-radius: 16px;">
            <i class="fas fa-{{ $feature['icon'] }}" style="font-size: 28px; color: #8b5cf6; margin-bottom: 10px;"></i>
            <h4 style="font-size: 14px; margin-bottom: 5px;">{{ $feature['title'] }}</h4>
            <p style="font-size: 12px; color: #94a3b8;">{{ $feature['desc'] }}</p>
        </div>
        @endforeach
    </div>
</div>