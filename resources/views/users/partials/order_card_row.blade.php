<div class="order-item-card">
    <div style="display: flex; flex-wrap: wrap; gap: 24px; align-items: center;">
        
        <div style="flex: 0 0 90px; text-align: center;">
            @if($item->model_img)
                <img src="{{ $item->model_img }}" alt="{{ $item->model_title }}" style="width: 75px; height: 75px; object-fit: contain;">
            @else
                <div style="width: 75px; height: 75px; background: #1a1a2e; border-radius: 12px; display: flex; align-items: center; justify-content: center; margin: 0 auto;">
                    <i class="fas fa-mobile-alt" style="font-size: 32px; color: #3b82f6;"></i>
                </div>
            @endif
        </div>

        <div style="flex: 1; min-width: 200px;">
            <h3 style="color: white; font-size: 18px; font-weight: 600; margin-bottom: 6px;">
                {{ $item->title }} {{ $item->model_title }}
            </h3>
            <div style="display: flex; flex-wrap: wrap; gap: 15px; font-size: 13px; color: #94a3b8;">
                <span><strong style="color: #64748b;">Storage:</strong> {{ $item->capacity ?? 'N/A' }}</span>
                <span><strong style="color: #64748b;">Order ID:</strong> {{ $item->order_id }}</span>
                @if($type === 'pending')
                    <span><strong style="color: #64748b;">Scheduled Pickup:</strong> {{ \Carbon\Carbon::parse($item->shipping_pickup_date)->format('d M Y') }} ({{ $item->shipping_pickup_time }})</span>
                @endif
            </div>
        </div>

        <div style="min-width: 120px;">
            <p style="color: #64748b; font-size: 12px; margin-bottom: 2px;">Finalized Quote</p>
            <h4 style="color: #3b82f6; font-size: 22px; font-weight: 800;">₹{{ number_format((float)$item->price) }}</h4>
        </div>

        <div style="display: flex; flex-direction: column; gap: 8px; min-width: 160px;">
            <button class="view-summary-trigger" data-order-id="{{ $item->order_id }}" style="background: transparent; border: 1px solid #3b82f6; color: #3b82f6; padding: 9px 18px; border-radius: 30px; font-weight: 600; font-size: 13px; cursor: pointer;">
                View Summary <i class="fas fa-eye" style="margin-left: 4px;"></i>
            </button>
            
            @if($type === 'pending')
                <button class="reevaluate-trigger" data-slug="{{ $item->model_slug }}" style="background: transparent; border: 1px solid #f59e0b; color: #f59e0b; padding: 9px 18px; border-radius: 30px; font-weight: 600; font-size: 13px; cursor: pointer;">
                    Reevaluate <i class="fas fa-sync-alt" style="margin-left: 4px;"></i>
                </button>

                <button class="cancel-order-trigger" data-order-id="{{ $item->order_id }}" style="background: transparent; border: 1px solid #ef4444; color: #ef4444; padding: 9px 18px; border-radius: 30px; font-weight: 600; font-size: 13px; cursor: pointer;">
                    Cancel Sale <i class="fas fa-times-circle" style="margin-left: 4px;"></i>
                </button>
            @endif
        </div>

    </div>
</div>