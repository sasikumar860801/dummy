<div class="card-dark" style="padding: 35px;">
    <h3 style="font-size: 24px; margin-bottom: 20px;">Why Buy with Us?</h3>
    <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 20px;">
        @php
        $features = [
            ['icon' => 'shield-alt', 'title' => '6–12 Months Seller Warranty', 'desc' => 'Excludes physical & water damage'],
            ['icon' => 'chart-line', 'title' => 'Quality 4.5+', 'desc' => 'Certified products'],
            ['icon' => 'exchange-alt', 'title' => '7 Days Replacement', 'desc' => 'Easy replacement support'],
            ['icon' => 'truck', 'title' => 'Hassle Free', 'desc' => 'Free delivery']
        ];
        @endphp
        @foreach($features as $feature)
        <div style="text-align: center; padding: 15px; background: #1a1a2e; border-radius: 16px;">
            <i class="fas fa-{{ $feature['icon'] }}" style="font-size: 28px; color: #3b82f6; margin-bottom: 10px;"></i>
            <h4 style="font-size: 14px; margin-bottom: 5px;">{{ $feature['title'] }}</h4>
            <p style="font-size: 12px; color: #94a3b8;">{{ $feature['desc'] }}</p>
        </div>
        @endforeach
    </div>
</div>