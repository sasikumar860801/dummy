@if($collection->isEmpty())
    <div style="background: #111118; border: 1px solid #1e1e2a; border-radius: 20px; padding: 40px; text-align: center; color: #64748b;">
        <i class="fas fa-boxes" style="font-size: 40px; margin-bottom: 15px; color: #1e1e2a;"></i>
        <p style="font-size: 14px;">No hardware assets cataloged inside this classification pool track container.</p>
    </div>
@else
    @foreach($collection as $item)
        <div class="order-item-record-card" style="grid-template-columns: 1.2fr 2fr 1.2fr;">
            
            <div class="sub-panel-column">
                <div class="profile-card-title"><i class="fas fa-barcode" style="color: #3b82f6;"></i> Resource Registry</div>
                <div style="font-size: 12px; color:#64748b; margin-bottom:10px; font-family: monospace;">
                    ORDER REF: <span style="color:#cbd5e1;">{{ $item->order_id }}</span><br>
                    STOCK ID: <span style="color:#ec4899;">#{{ $item->id }}</span>
                </div>
                <div style="display: flex; gap: 12px; align-items: center;">
                    @if($item->model_img)
                        <img src="{{ url('media/images/model/' . $item->model_img) }}" style="width: 50px; height: 50px; object-fit: contain; background: #1a1a2e; border-radius: 10px; padding: 4px; border:1px solid #2a2a3a;">
                    @endif
                    <div style="font-weight: 600; color: white; font-size: 14px; line-height: 1.3;">{{ $item->model_title }}</div>
                </div>
            </div>

            <div class="sub-panel-column" style="display: grid; grid-template-columns: 1fr 1fr; gap: 10px 20px;">
                <div>
                    <div class="profile-card-title" style="margin-bottom:4px; font-size:11px;">Hardware Configuration</div>
                    <div class="meta-data-line">Capacity: <span style="color:white; font-weight:600;">{{ $item->capacity }}</span></div>
                    <div class="meta-data-line">Colorway: <span style="color:white;">{{ $item->color }}</span></div>
                    <div class="meta-data-line">Warranty: <span style="color:#a78bfa; font-weight:500;">{{ $item->warranty }}</span></div>
                </div>
                <div>
                    <div class="profile-card-title" style="margin-bottom:4px; font-size:11px;">Financial Profiles</div>
                    <div class="meta-data-line">Cost Price: <span style="color:#10b981; font-weight:600;">${{ number_format($item->buy_price, 2) }}</span></div>
                    <div class="meta-data-line">User Profit: <span style="color:#64748b;">{{ $item->profit_percent_user }}% (${{ number_format($item->profit, 2) }})</span></div>
                    <div class="meta-data-line">Vendor Cut: <span style="color:#64748b;">{{ $item->profit_perc_vendor }}%</span></div>
                </div>
                <div style="grid-column: span 2; border-top: 1px solid #1a1a2e; padding-top: 6px; font-size: 12px; color: #94a3b8; font-family:monospace;">
                    <i class="fas fa-fingerprint" style="font-size:10px; color:#64748b;"></i> IMEI 1: {{ $item->imei_no_1 }} @if($item->imei_no_2) | IMEI 2: {{ $item->imei_no_2 }} @endif
                </div>
            </div>

            <div style="display: flex; flex-direction: column; gap: 8px; justify-content: center;">
                
                @if($tab === 'new')
                    <button class="action-control-btn" style="background: #1a1a2e; color: #3b82f6; border: 1px solid rgba(59,130,246,0.2);" 
                            onclick="launchEditModal({
                                id: '{{ $item->id }}', capacity: '{{ $item->capacity }}', buy_price: '{{ $item->buy_price }}', 
                                color: '{{ $item->color }}', imei_no_1: '{{ $item->imei_no_1 }}', imei_no_2: '{{ $item->imei_no_2 }}', 
                                warranty: '{{ $item->warranty }}', profit_percent_user: '{{ $item->profit_percent_user }}', profit_perc_vendor: '{{ $item->profit_perc_vendor }}'
                            })">
                        <i class="fas fa-edit"></i> Edit Properties
                    </button>
                    <button class="action-control-btn" style="background: rgba(239,68,68,0.1); color: #f87171; border: 1px solid rgba(239,68,68,0.15);" onclick="fireDeleteAction('{{ $item->id }}')">
                        <i class="fas fa-trash-alt"></i> Purge From Vault
                    </button>
                @endif

                @if($tab === 'assigned')
                    <button class="action-control-btn" style="background: #1a1a2e; color: #3b82f6; border: 1px solid rgba(59,130,246,0.2);" 
                            onclick="launchEditModal({
                                id: '{{ $item->id }}', capacity: '{{ $item->capacity }}', buy_price: '{{ $item->buy_price }}', 
                                color: '{{ $item->color }}', imei_no_1: '{{ $item->imei_no_1 }}', imei_no_2: '{{ $item->imei_no_2 }}', 
                                warranty: '{{ $item->warranty }}', profit_percent_user: '{{ $item->profit_percent_user }}', profit_perc_vendor: '{{ $item->profit_perc_vendor }}'
                            })">
                        <i class="fas fa-edit"></i> Edit Properties
                    </button>
                    <button class="action-control-btn" style="background: linear-gradient(135deg, #eab308, #ca8a04); color: black;" onclick="fireAssignmentAction('{{ $item->id }}', 'unassign')">
                        <i class="fas fa-undo-alt"></i> Move to New Stock
                    </button>
                    <button class="action-control-btn" style="background: linear-gradient(135deg, #10b981, #059669); color: white;" onclick="fireAssignmentAction('{{ $item->id }}', 'complete')">
                        <i class="fas fa-check-double"></i> Mark Completed
                    </button>
                @endif

                @if($tab === 'completed')
                    <div style="text-align: center; padding: 10px; background: rgba(16,185,129,0.08); border: 1px solid rgba(16,185,129,0.15); border-radius: 10px; color: #34d399; font-size: 12px; font-weight: 600;">
                        <i class="fas fa-archive" style="margin-right: 5px;"></i> Archived In Vaults
                    </div>
                @endif

            </div>
        </div>
    @endforeach
@endif