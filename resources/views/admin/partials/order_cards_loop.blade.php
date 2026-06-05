@if($collection->isEmpty())
    <div style="background: #111118; border: 1px solid #1e1e2a; border-radius: 20px; padding: 40px; text-align: center; color: #64748b;">
        <i class="fas fa-folder-open" style="font-size: 40px; margin-bottom: 15px; color: #1e1e2a;"></i>
        <p style="font-size: 14px;">No tracking entry arrays listed within this system context group.</p>
    </div>
@else
    @foreach($collection as $order)
        @php
        
            // Programmatic JSON Fallback Parser Engine
            $displayName = $order->item_name;
            $displayPrice = $order->item_price;
            $displayCapacity = $order->item_capacity;

            // Inspect string signature arrays to strip nested raw string payloads out
            if (is_string($order->item_name) && str_starts_with($order->item_name, '{')) {
                $nestedData = json_decode($order->item_name, true);
                if (json_last_error() === JSON_ERROR_NONE) {
                    $displayName = $nestedData['item_name'] ?? $order->name;
                    // If the item_name key inside JSON is another JSON string wrapper, parse it again
                    if (is_string($displayName) && str_starts_with($displayName, '{')) {
                         $deepData = json_decode($displayName, true);
                         if (json_last_error() === JSON_ERROR_NONE) {
                             // Fallback chain down to master values
                             $displayPrice = $deepData['final_price'] ?? ($deepData['base_price'] ?? $displayPrice);
                             $displayCapacity = $deepData['normalized_capacity'] ?? ($deepData['capacity'] ?? $displayCapacity);
                         }
                    }
                    // Extract name from the parent model relationship table as ultimate backup
                    $displayName = $order->model_title ?? ($nestedData['normalized_capacity'] ?? 'Evaluated Smart Device');
                }
            }
        @endphp

        <div class="order-item-record-card">
            
            <div class="sub-panel-column">
                <div class="profile-card-title"><i class="fas fa-user-circle" style="color: #3b82f6;"></i> Identity & Destination</div>
                <div class="meta-data-line" style="font-weight: 600; color: white;">{{ $order->name }}</div>
                <div class="meta-data-line"><i class="fas fa-phone-alt" style="font-size: 12px; margin-right: 6px; color: #64748b;"></i>{{ $order->mobile_no }}</div>
                @if($order->alternate_mob_no)
                    <div class="meta-data-line" style="font-size:12px; color: #64748b; padding-left:18px;">Alt: {{ $order->alternate_mob_no }}</div>
                @endif
                <div class="meta-data-line"><i class="fas fa-envelope" style="font-size: 12px; margin-right: 6px; color: #64748b;"></i>{{ $order->email }}</div>
                <div class="meta-data-line" style="font-size: 13px; color: #94a3b8; margin-top: 10px; line-height: 1.4;">
                    <span style="display:block; font-size:11px; text-transform:uppercase; font-weight:700;">Address ({{ $order->address_type }}):</span>
                    {{ $order->address }}, Near {{ $order->landmark }}, {{ $order->state }} - {{ $order->pincode }}
                </div>
            </div>

            <div class="sub-panel-column">
                <div class="profile-card-title"><i class="fas fa-mobile-alt" style="color: #a78bfa;"></i> Device Specification Profile</div>
                <div style="display: flex; gap: 15px; align-items: flex-start;">
                    @if($order->model_img)
                        <img src="{{ url('media/images/model/' . $order->model_img) }}" style="width: 65px; height: 65px; object-fit: contain; background: #1a1a2e; border-radius: 12px; padding: 5px; border: 1px solid #2a2a3a;">
                    @endif
                    <div>
                        <div style="font-weight: 600; color: white; font-size: 16px; margin-bottom: 4px;">{{ $displayName }}</div>
                        <div class="meta-data-line">Capacity variant: <strong style="color:#f472b6; text-transform: uppercase;">{{ $displayCapacity }}</strong></div>
                        <div class="meta-data-line">Evaluated Valuation: <strong style="color:#10b981;">${{ number_format((float)$displayPrice, 2) }}</strong></div>
                    </div>
                </div>
                <div style="margin-top: 15px; padding-top: 12px; border-top: 1px solid #1a1a2e; display: flex; gap: 20px; font-size: 13px;">
                    <div><span style="color:#64748b;">Pickup:</span> <strong style="color:white;">{{ $order->shipping_pickup_date }}</strong></div>
                    <div><span style="color:#64748b;">Window:</span> <strong style="color:white;">{{ $order->shipping_pickup_time }}</strong></div>
                </div>
            </div>

            <div style="display: flex; flex-direction: column; gap: 10px; justify-content: center;">
                <div style="font-size: 11px; color:#64748b; margin-bottom:5px;">
                    ID: <span style="color:#cbd5e1; font-family:monospace;">{{ $order->order_id }}</span><br>
                    Logged: <span style="color:#cbd5e1;">{{ \Carbon\Carbon::parse($order->created_at)->format('Y-m-d H:i') }}</span>
                </div>

                <button class="action-control-btn view-summary-trigger" style="background: #1a1a2e; color: #cbd5e1;" data-order-id="{{ $order->order_id }}">
                    <i class="fas fa-eye"></i> View Summary
                </button>

                @if($tab === 'new')
                    <button class="action-control-btn" style="background: linear-gradient(135deg, #10b981, #059669); color: white;" onclick="fireStatusMutationAction('{{ $order->order_id }}', 'completed')">
                        <i class="fas fa-check"></i> Complete Order
                    </button>
                    <button class="action-control-btn" style="background: linear-gradient(135deg, #ef4444, #dc2626); color: white;" onclick="fireStatusMutationAction('{{ $order->order_id }}', 'cancelled')">
                        <i class="fas fa-times"></i> Cancel Order
                    </button>
                @endif

                @if($tab === 'rejected')
                    <button class="action-control-btn" style="background: linear-gradient(135deg, #ef4444, #dc2626); color: white;" onclick="fireStatusMutationAction('{{ $order->order_id }}', 'cancelled')">
                        <i class="fas fa-times"></i> Cancel Order
                    </button>
                @endif

                @if($tab === 'completed')
                    @if((int)$order->exists_in_stock > 0)
                        <button class="action-control-btn disabled-btn" disabled>
                            <i class="fas fa-cubes"></i> Inside Inventory
                        </button>
                    @else
                        <button class="action-control-btn" style="background: linear-gradient(135deg, #3b82f6, #2563eb); color: white;" 
                                data-id="{{ $order->order_id }}" 
                                data-model="{{ $order->model_id }}" 
                                data-capacity="{{ $displayCapacity }}" 
                                data-price="{{ $displayPrice }}" 
                                onclick="configureStockModal(this)">
                            <i class="fas fa-boxes"></i> Move To Stock
                        </button>
                    @endif
                @endif

            </div>
        </div>
    @endforeach
@endif