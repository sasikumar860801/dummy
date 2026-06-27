@extends('layouts.app')

@section('title', 'Buy Orders - RevoDevice')

@push('styles')
<style>
    /* ---------------- Global Layout Component Rules ---------------- */
    .profile-layout { display: grid; grid-template-columns: 280px 1fr; gap: 30px; padding: 40px 0; }
    .profile-sidebar { background: #111118; border: 1px solid #1e1e2a; border-radius: 20px; padding: 24px 16px; }
    
    .sidebar-link {
        display: flex; align-items: center; gap: 12px; padding: 14px 20px; color: #94a3b8;
        text-decoration: none; border-radius: 12px; font-weight: 500; transition: all 0.2s; margin-bottom: 8px;
    }
    .sidebar-link:hover, .sidebar-link.active { background: #1a1a2e; color: #3b82f6; }
    .sidebar-link.active { border-left: 4px solid #8b5cf6; background: linear-gradient(90deg, #16162a 0%, #111118 100%); }

    /* ---------------- Buy Orders Specific Styles ---------------- */
    .tabs-header {
        display: flex; gap: 15px; margin-bottom: 25px; border-bottom: 1px solid #1e1e2a; padding-bottom: 10px;
    }
    .tab-btn {
        background: transparent; border: none; color: #64748b; font-size: 16px; font-weight: 600;
        padding: 10px 20px; cursor: pointer; border-radius: 8px; transition: 0.3s;
    }
    .tab-btn:hover { color: #cbd5e1; background: #1a1a2e; }
    .tab-btn.active { color: white; background: #1a1a2e; border: 1px solid #2a2a3a; }
    
    .tab-content { display: none; }
    .tab-content.active { display: block; animation: fadeIn 0.3s ease; }
    @keyframes fadeIn { from { opacity: 0; transform: translateY(5px); } to { opacity: 1; transform: translateY(0); } }

    .order-card {
        background: #111118; border: 1px solid #1e1e2a; border-radius: 16px; padding: 20px;
        display: flex; gap: 20px; margin-bottom: 20px; align-items: center; transition: 0.2s;
    }
    .order-card:hover { border-color: #2a2a3a; }
    
    .order-img {
        width: 100px; height: 100px; background: #0c0c14; border-radius: 12px;
        display: flex; align-items: center; justify-content: center; flex-shrink: 0; padding: 10px;
    }
    .order-img img { max-width: 100%; max-height: 100%; object-fit: contain; }
    
    .order-details { flex: 1; }
    .order-title { font-size: 18px; font-weight: 700; color: white; margin: 0 0 8px 0; }
    .order-specs { font-size: 13px; color: #94a3b8; display: flex; gap: 15px; margin-bottom: 8px; flex-wrap: wrap; }
    .order-specs span { background: #1a1a2e; padding: 4px 10px; border-radius: 6px; }
    .order-meta { font-size: 12px; color: #64748b; margin: 0; }
    .order-meta strong { color: #cbd5e1; }

    .order-actions { text-align: right; display: flex; flex-direction: column; align-items: flex-end; gap: 12px; min-width: 150px; }
    .order-price { font-size: 20px; font-weight: 800; color: white; }
    
    .badge { padding: 6px 12px; border-radius: 20px; font-size: 12px; font-weight: 700; text-transform: uppercase; }
    .badge-pending { background: rgba(234, 179, 8, 0.1); color: #eab308; border: 1px solid rgba(234, 179, 8, 0.2); }
    .badge-completed { background: rgba(16, 185, 129, 0.1); color: #10b981; border: 1px solid rgba(16, 185, 129, 0.2); }

    .pay-now-btn {
        background: linear-gradient(135deg, #3b82f6, #8b5cf6); color: white; border: none; padding: 10px 20px;
        font-size: 14px; font-weight: 600; border-radius: 8px; cursor: pointer; text-decoration: none; display: inline-block;
    }
    
    .empty-state { text-align: center; padding: 50px 20px; color: #64748b; }

    /* ---------------- Mobile Responsive Logic ---------------- */
    .mobile-nav-toggle { display: none; background: #111118; border: 1px solid #1e1e2a; color: #e2e8f0; padding: 12px 20px; width: 100%; border-radius: 12px; align-items: center; justify-content: space-between; margin-bottom: 20px; cursor: pointer; }
    @media (max-width: 991px) {
        .profile-layout { grid-template-columns: 1fr; padding: 20px 0; }
        .mobile-nav-toggle { display: flex; }
        .sidebar-wrapper { position: fixed; top: 0; left: -100%; width: 280px; height: 100%; background: #0a0a0f; z-index: 99999; padding: 20px; box-shadow: 25px 0 50px -12px rgba(0,0,0,0.5); transition: left 0.3s; }
        .sidebar-wrapper.open { left: 0; }
        .sidebar-overlay { display: none; position: fixed; top: 0; left: 0; width: 100%; height: 100%; background: rgba(0,0,0,0.6); z-index: 99998; backdrop-filter: blur(4px); }
        .sidebar-overlay.show { display: block; }
        
        .order-card { flex-direction: column; align-items: flex-start; }
        .order-actions { text-align: left; align-items: flex-start; width: 100%; margin-top: 10px; flex-direction: row; justify-content: space-between; }
    }
</style>
@endpush

@section('content')
<div class="container">
    
    <div class="mobile-nav-toggle" id="openMobileSidebar">
        <span><i class="fas fa-bars" style="margin-right: 10px; color: #3b82f6;"></i> Account Menu</span>
        <i class="fas fa-chevron-right" style="font-size: 12px; color: #64748b;"></i>
    </div>
    <div class="sidebar-overlay" id="sidebarOverlay"></div>

    <div class="profile-layout">
        
        <div class="sidebar-wrapper" id="sidebarMenu">
            <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 20px;" class="d-lg-none">
                <span class="gradient-text" style="font-weight: 800; font-size: 18px;">Account Menu</span>
                <button id="closeMobileSidebar" style="background: none; border: none; color: #94a3b8; font-size: 22px; cursor: pointer;">&times;</button>
            </div>
            
            <div class="profile-sidebar">
                <div style="text-align: center; padding-bottom: 20px; margin-bottom: 20px; border-bottom: 1px solid #1e1e2a;">
                    <div style="width: 65px; height: 65px; background: linear-gradient(135deg, #3b82f6, #8b5cf6); border-radius: 50%; display: flex; align-items: center; justify-content: center; margin: 0 auto 12px;">
                        <i class="fas fa-user-shield" style="color: white; font-size: 26px;"></i>
                    </div>
                    <h4 style="color: white; font-size: 16px; font-weight: 600;">{{ $user->name ?? 'User' }}</h4>
                    <p style="color: #64748b; font-size: 12px; margin-top: 4px;">{{ $user->phone ?? 'N/A' }}</p>
                </div>

                      <nav>
                    <a href="{{ route('profile') }}" class="sidebar-link ">
                        <i class="fas fa-user" style="width: 20px;"></i> My Profile
                    </a>
                    <a href="my-cart" class="sidebar-link" style="opacity: 0.5; cursor: not-allowed;">
                        <i class="fas fa-shopping-cart" style="width: 20px;"></i>  Cart
                    </a>
                    <a href="my-orders" class="sidebar-link " style="opacity: 0.5; cursor: not-allowed;">
                        <i class="fas fa-box-open" style="width: 20px;"></i> Sell Order
                    </a>

                      <a href="{{ route('buy_orders') }}" class="sidebar-link active">
                        <i class="fas fa-shopping-bag" style="width: 20px;"></i> Buy Orders
                    </a>

                      <a href="{{ route('my_service_repair') }}" class="sidebar-link ">
                        <i class="fas fa-tools" style="width: 20px;"></i> Repair & Services
                    </a>
                    
                    <hr style="border: none; border-top: 1px solid #1e1e2a; my: 15px; margin: 15px 0;">
                    
                    <form id="logoutForm" method="POST" action="{{ route('logout') }}">
                        @csrf
                        <button type="submit" class="sidebar-link" style="color: #ef4444; border:none; background:none;">
                            <i class="fas fa-sign-out-alt" style="width: 20px;"></i> Logout
                        </button>
                    </form>
                </nav>
            </div>
        </div>

        <div>
            <h2 class="gradient-text" style="font-size: 24px; font-weight: 700; margin-bottom: 6px;">Buy Orders</h2>
            <p style="color: #64748b; font-size: 14px; margin-bottom: 30px;">Track your purchased devices and complete pending payments.</p>

            <div class="tabs-header">
                <button class="tab-btn active" onclick="switchTab('pending')">Pending ({{ $pendingOrders->count() }})</button>
                <button class="tab-btn" onclick="switchTab('completed')">Completed ({{ $completedOrders->count() }})</button>
            </div>

            <div id="pending" class="tab-content active">
                @forelse($pendingOrders as $order)
                    @php 
                        $calculatedPrice = $order->buy_price + ($order->buy_price * ($order->profit_percent_user / 100));
                        $thumbSrc = !empty($order->model_img) ? $order->model_img : 'placeholder.png';
                    @endphp
                    <div class="order-card">
                        <div class="order-img">
                            <img src="{{ asset('media/images/model/' . $order->model_img) }}" alt="{{ $order->title }}">
                            
                        </div>
                        
                        <div class="order-details">
                            <h3 class="order-title">{{ $order->title }}</h3>
                            <div class="order-specs">
                                <span><i class="fas fa-microchip"></i> {{ $order->capacity }}</span>
                                <span><i class="fas fa-palette"></i> {{ $order->color }}</span>
                                <span><i class="fas fa-shield-alt"></i> {{ ucwords($order->warranty) }}</span>
                            </div>
                            <p class="order-meta">
                                Order ID: <strong>{{ $order->order_id ?? 'N/A' }}</strong> • 
                                Dealer: <strong>{{ $order->shop_name ?? 'RevoDevice Direct' }}</strong>
                            </p>
                        </div>
                        
                        <div class="order-actions">
                            <div class="order-price">₹{{ number_format($calculatedPrice, 2) }}</div>
                            <span class="badge badge-pending">Order Pending</span>
                            
                            @if(strtolower($order->payment_status) === 'pending')
                                {{-- Replace this route with your actual payment gateway route --}}
                                <a href="#" class="pay-now-btn">
                                    <i class="fas fa-credit-card"></i> Pay Now
                                </a>
                            @endif
                        </div>
                    </div>
                @empty
                    <div class="empty-state">
                        <i class="fas fa-shopping-basket" style="font-size: 40px; margin-bottom: 15px; opacity: 0.5;"></i>
                        <p>You have no pending buy orders.</p>
                    </div>
                @endforelse
            </div>

            <div id="completed" class="tab-content">
                @forelse($completedOrders as $order)
                    @php 
                        $calculatedPrice = $order->buy_price + ($order->buy_price * ($order->profit_percent_user / 100));
                        $thumbSrc = !empty($order->model_img) ? $order->model_img : 'placeholder.png';
                    @endphp
                    <div class="order-card">
                        <div class="order-img">
                            <img src="{{ asset('media/images/model/' . $order->model_img) }}" alt="{{ $order->title }}">
                        </div>
                        
                        <div class="order-details">
                            <h3 class="order-title">{{ $order->title }}</h3>
                            <div class="order-specs">
                                <span><i class="fas fa-microchip"></i> {{ $order->capacity }}</span>
                                <span><i class="fas fa-palette"></i> {{ $order->color }}</span>
                                <span><i class="fas fa-shield-alt"></i> {{ ucwords($order->warranty) }}</span>
                            </div>
                            <p class="order-meta">
                                Order ID: <strong>{{ $order->order_id ?? 'N/A' }}</strong> • 
                                Dealer: <strong>{{ $order->shop_name ?? 'RevoDevice Direct' }}</strong>
                            </p>
                        </div>
                        
                        <div class="order-actions">
                            <div class="order-price">₹{{ number_format($calculatedPrice, 2) }}</div>
                            <span class="badge badge-completed">Completed</span>
                        </div>
                    </div>
                @empty
                    <div class="empty-state">
                        <i class="fas fa-check-circle" style="font-size: 40px; margin-bottom: 15px; opacity: 0.5;"></i>
                        <p>You have no completed orders yet.</p>
                    </div>
                @endforelse
            </div>
            
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script src="https://code.jquery.com/jquery-3.7.1.min.js"></script>
<script>
    // Tab switching logic for Buy Orders
    function switchTab(tabId) {
        document.querySelectorAll('.tab-btn').forEach(btn => btn.classList.remove('active'));
        document.querySelectorAll('.tab-content').forEach(content => content.classList.remove('active'));
        
        event.target.classList.add('active');
        document.getElementById(tabId).classList.add('active');
    }

    $(document).ready(function() {
        // Mobile Layout Dropdown Controls Integration
        $('#openMobileSidebar').on('click', function() {
            $('#sidebarMenu').addClass('open'); 
            $('#sidebarOverlay').addClass('show');
        });
        
        $('#closeMobileSidebar, #sidebarOverlay').on('click', function() {
            $('#sidebarMenu').removeClass('open'); 
            $('#sidebarOverlay').removeClass('show');
        });

        // Logout Script
        $('#logoutForm').on('submit', function(e) {
            e.preventDefault();
            $.ajax({
                url: $(this).attr('action'),
                type: 'POST',
                data: $(this).serialize(),
                success: function(response) {
                    if (response.success) {
                        window.location.href = "/";
                    }
                }
            });
        });
    });
</script>
@endpush