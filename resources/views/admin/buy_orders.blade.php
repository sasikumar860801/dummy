@extends('admin.layout')

@section('title', 'Manage Buy Orders - Admin Panel')

@push('admin-styles')
<style>
    .admin-orders-container {
        padding: 30px;
        background: #050508;
        min-height: 100vh;
    }
    .page-header {
        display: flex;
        justify-content: space-between;
        align-items: center;
        margin-bottom: 30px;
    }
    .page-title {
        font-size: 24px;
        font-weight: 700;
        color: white;
        margin: 0;
    }

    /* Tabs */
    .admin-tabs {
        display: flex;
        gap: 10px;
        margin-bottom: 25px;
        border-bottom: 1px solid #1e1e2a;
        padding-bottom: 15px;
    }
    .admin-tab-btn {
        background: #111118;
        border: 1px solid #1e1e2a;
        color: #64748b;
        padding: 10px 24px;
        border-radius: 8px;
        font-weight: 600;
        cursor: pointer;
        transition: 0.2s;
    }
    .admin-tab-btn:hover { background: #1a1a2e; color: white; }
    .admin-tab-btn.active { background: #3b82f6; color: white; border-color: #3b82f6; }
    
    .tab-content { display: none; }
    .tab-content.active { display: block; animation: fadeIn 0.3s ease; }
    @keyframes fadeIn { from { opacity: 0; transform: translateY(5px); } to { opacity: 1; transform: translateY(0); } }

    /* Admin Order Card */
    .admin-card {
        background: #111118;
        border: 1px solid #1e1e2a;
        border-radius: 12px;
        padding: 20px;
        margin-bottom: 15px;
        display: grid;
        grid-template-columns: auto 2fr 1.5fr auto;
        gap: 20px;
        align-items: center;
    }
    .admin-card:hover { border-color: #2a2a3a; }

    .device-img {
        width: 80px; height: 80px;
        background: #0a0a0f;
        border-radius: 8px;
        padding: 8px;
        display: flex; align-items: center; justify-content: center;
    }
    .device-img img { max-width: 100%; max-height: 100%; object-fit: contain; }

    .details-block h4 { color: white; margin: 0 0 8px 0; font-size: 16px; }
    .details-block p { color: #94a3b8; font-size: 13px; margin: 0 0 4px 0; }
    .tag-row { display: flex; gap: 10px; margin-top: 8px; }
    .tag-row span { background: #1a1a2e; color: #cbd5e1; font-size: 11px; padding: 4px 8px; border-radius: 4px; }

    /* Buyer Info Section */
    .buyer-info {
        background: rgba(59, 130, 246, 0.05);
        border: 1px solid rgba(59, 130, 246, 0.1);
        padding: 12px 15px;
        border-radius: 8px;
    }
    .buyer-info strong { display: block; color: #e2e8f0; font-size: 14px; margin-bottom: 4px; }
    .buyer-info span { display: block; color: #94a3b8; font-size: 12px; margin-bottom: 2px; }
    .buyer-info i { color: #3b82f6; width: 14px; margin-right: 4px; }

    .action-block { text-align: right; display: flex; flex-direction: column; gap: 10px; align-items: flex-end; }
    .price { font-size: 18px; font-weight: 700; color: #10b981; }
    
    .status-badge { padding: 5px 12px; border-radius: 20px; font-size: 11px; font-weight: 600; text-transform: uppercase; }
    .status-pending { background: rgba(234, 179, 8, 0.1); color: #eab308; border: 1px solid rgba(234, 179, 8, 0.2); }
    .status-completed { background: rgba(16, 185, 129, 0.1); color: #10b981; border: 1px solid rgba(16, 185, 129, 0.2); }

    .empty-state { text-align: center; padding: 60px 20px; color: #64748b; background: #111118; border-radius: 12px; border: 1px dashed #1e1e2a; }

    @media (max-width: 991px) {
        .admin-card { grid-template-columns: 1fr; gap: 15px; }
        .action-block { align-items: flex-start; text-align: left; flex-direction: row; justify-content: space-between; width: 100%; }
        .device-img { width: 100%; height: 150px; }
    }
</style>
@endpush

@section('admin-content')
<div class="admin-orders-container">
    <div class="page-header">
        <h1 class="page-title"><i class="fas fa-shopping-cart" style="color: #3b82f6; margin-right: 10px;"></i> Platform Buy Orders</h1>
    </div>

    <div class="admin-tabs">
        <button class="admin-tab-btn active" onclick="switchAdminTab('pending')">Pending Orders ({{ collect($pendingOrders)->count() }})</button>
        <button class="admin-tab-btn" onclick="switchAdminTab('completed')">Completed Sales ({{ collect($completedOrders)->count() }})</button>
    </div>

    <div id="pending" class="tab-content active">
        @forelse($pendingOrders as $order)
            @php 
                $calculatedPrice = $order->buy_price + ($order->buy_price * ($order->profit_percent_user / 100));
            @endphp
            <div class="admin-card">
                <div class="device-img">
                    @if($order->model_img)
                        <img src="{{ asset('media/images/model/' . $order->model_img) }}" alt="{{ $order->model_title }}">
                    @else
                        <i class="fas fa-mobile-alt" style="color: #64748b; font-size: 24px;"></i>
                    @endif
                </div>
                
                <div class="details-block">
                    <h4>{{ $order->model_title }}</h4>
                    <p>Order ID: <strong>{{ $order->order_id ?? 'N/A' }}</strong></p>
                    <p>Sourced from: <strong>{{ $order->shop_name ?? 'Direct User' }}</strong></p>
                    <div class="tag-row">
                        <span>{{ $order->capacity }}</span>
                        <span>{{ $order->color }}</span>
                        <span>{{ ucwords($order->warranty) }}</span>
                    </div>
                </div>

                <div class="buyer-info">
                    <strong><i class="fas fa-user"></i> {{ $order->buyer_name }}</strong>
                    <span><i class="fas fa-phone"></i> {{ $order->buyer_phone }}</span>
                    <span><i class="fas fa-envelope"></i> {{ $order->buyer_email }}</span>
                </div>

                <div class="action-block">
                    <div class="price">₹{{ number_format($calculatedPrice, 2) }}</div>
                    <span class="status-badge status-pending">Pending</span>
                </div>
            </div>
        @empty
            <div class="empty-state">
                <i class="fas fa-inbox" style="font-size: 48px; margin-bottom: 15px; opacity: 0.3;"></i>
                <h3>No Pending Orders</h3>
                <p>All purchased devices have been fully processed.</p>
            </div>
        @endforelse
    </div>

    <div id="completed" class="tab-content">
        @forelse($completedOrders as $order)
            @php 
                $calculatedPrice = $order->buy_price + ($order->buy_price * ($order->profit_percent_user / 100));
            @endphp
            <div class="admin-card" style="opacity: 0.8;">
                <div class="device-img">
                    @if($order->model_img)
                        <img src="{{ asset('media/images/model/' . $order->model_img) }}" alt="{{ $order->model_title }}">
                    @else
                        <i class="fas fa-mobile-alt" style="color: #64748b; font-size: 24px;"></i>
                    @endif
                </div>
                
                <div class="details-block">
                    <h4>{{ $order->model_title }}</h4>
                    <p>Order ID: <strong>{{ $order->order_id ?? 'N/A' }}</strong></p>
                    <p>Sourced from: <strong>{{ $order->shop_name ?? 'Direct User' }}</strong></p>
                    <div class="tag-row">
                        <span>{{ $order->capacity }}</span>
                        <span>{{ $order->color }}</span>
                        <span>{{ ucwords($order->warranty) }}</span>
                    </div>
                </div>

                <div class="buyer-info">
                    <strong><i class="fas fa-user"></i> {{ $order->buyer_name }}</strong>
                    <span><i class="fas fa-phone"></i> {{ $order->buyer_phone }}</span>
                </div>

                <div class="action-block">
                    <div class="price">₹{{ number_format($calculatedPrice, 2) }}</div>
                    <span class="status-badge status-completed">Completed</span>
                    <p style="font-size: 11px; color: #64748b; margin-top: 5px;">{{ \Carbon\Carbon::parse($order->updated_at)->format('M d, Y') }}</p>
                </div>
            </div>
        @empty
            <div class="empty-state">
                <i class="fas fa-check-double" style="font-size: 48px; margin-bottom: 15px; opacity: 0.3;"></i>
                <h3>No Completed Orders</h3>
                <p>There are no historical completed transactions yet.</p>
            </div>
        @endforelse
    </div>
</div>
@endsection

@push('admin-scripts')
<script>
    function switchAdminTab(tabId) {
        document.querySelectorAll('.admin-tab-btn').forEach(btn => btn.classList.remove('active'));
        document.querySelectorAll('.tab-content').forEach(content => content.classList.remove('active'));
        
        event.target.classList.add('active');
        document.getElementById(tabId).classList.add('active');
    }
</script>
@endpush