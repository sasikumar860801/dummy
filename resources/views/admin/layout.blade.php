@php
use Illuminate\Support\Facades\DB;

$dealerStockRequests = DB::table('stocks')
    ->where('is_approved', 0)
    ->count();

$sellOrders = DB::table('orders')
    ->where('status', 'pending')
    ->count();

$buyOrders = DB::table('stocks')
    ->where('is_approved', 1)
    ->where('user_id', '!=', 0)
    ->count();

$serviceRepairs = DB::table('service_repairs')
    ->where('status', 'pending')
    ->count();

    $userCartCount = DB::table('partial_order_items')
    ->whereNotIn('partial_order_items.order_id', function ($query) {
        $query->select(DB::raw('order_id COLLATE utf8mb4_unicode_ci'))
              ->from('order_items')
              ->whereNotNull('order_id');
    })
    ->count();
@endphp

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>@yield('title', 'Control Desk Console') - RevoDevice</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <style>
        * { box-sizing: border-box; margin: 0; padding: 0; font-family: 'Segoe UI', system-ui, sans-serif; }
        body { background: #07070a; color: #e2e8f0; display: flex; min-height: 100vh; overflow-x: hidden; }
        
        .main-workspace { flex-1: 1; width: 100%; min-height: 100vh; transition: all 0.3s; padding-left: 280px; }
        .top-navbar { height: 70px; background: #0f0f15; border-bottom: 1px solid #1e1e2a; display: flex; align-items: center; justify-content: space-between; padding: 0 30px; position: sticky; top: 0; z-index: 99; }
        .view-body { padding: 40px; max-width: 1400px; margin: 0 auto; width: 100%; }
        .gradient-text { background: linear-gradient(135deg, #f472b6, #c084fc); -webkit-background-clip: text; background-clip: text; color: transparent; }
        
        .hamburger-menu { display: none; background: none; border: none; color: white; font-size: 22px; cursor: pointer; }

        @media (max-width: 991px) {
            .main-workspace { padding-left: 0; }
            .hamburger-menu { display: block; }
        }
    </style>
    @stack('admin-styles')
</head>
<body>

    @include('admin.sidebar')

    <div class="main-workspace" id="workspaceContainer">
        
<header class="top-navbar">

    <button class="hamburger-menu" id="toggleAdminSidebar">
        <i class="fas fa-bars"></i>
    </button>

    <div style="font-weight:600;font-size:15px;color:#94a3b8;">
        Role:
        <span style="color:#ec4899;">System Operators</span>
    </div>

    <div style="display:flex;align-items:center;gap:18px;">

      <div onclick="window.location='{{ url('/admin/dealer-stock') }}'"
     title="Dealer Stock Requests"
     style="display:flex;align-items:center;gap:6px;background:#1a1a2e;padding:8px 12px;border-radius:10px;cursor:pointer;transition:.2s;">
    <i class="fas fa-box-open" style="color:#f59e0b;"></i>
    <span style="font-size:13px;font-weight:600;">{{ $dealerStockRequests }}</span>
</div>

       <div onclick="window.location='{{ url('/admin/orders') }}'"
     title="Pending Sell Orders"
     style="display:flex;align-items:center;gap:6px;background:#1a1a2e;padding:8px 12px;border-radius:10px;cursor:pointer;transition:.2s;">
    <i class="fas fa-arrow-up" style="color:#ef4444;"></i>
    <span style="font-size:13px;font-weight:600;">{{ $sellOrders }}</span>
</div>

     <div onclick="window.location='{{ url('/admin/buy-orders') }}'"
     title="Buy Orders"
     style="display:flex;align-items:center;gap:6px;background:#1a1a2e;padding:8px 12px;border-radius:10px;cursor:pointer;transition:.2s;">
    <i class="fas fa-arrow-down" style="color:#10b981;"></i>
    <span style="font-size:13px;font-weight:600;">{{ $buyOrders }}</span>
</div>

<div class="top-counter"
     onclick="window.location='{{ url('/admin/user-list') }}'"
     title="Active User Carts"
     style="display:flex;align-items:center;gap:6px;background:#1a1a2e;padding:8px 12px;border-radius:10px;cursor:pointer;transition:.2s;">
    <i class="fas fa-cart-shopping" style="color:#f97316;"></i>
    <span style="font-size:13px;font-weight:600;">{{ $userCartCount }}</span>
</div>

       <div onclick="window.location='{{ url('/admin/service-repairs') }}'"
     title="Service & Repair Requests"
     style="display:flex;align-items:center;gap:6px;background:#1a1a2e;padding:8px 12px;border-radius:10px;cursor:pointer;transition:.2s;">
    <i class="fas fa-screwdriver-wrench" style="color:#3b82f6;"></i>
    <span style="font-size:13px;font-weight:600;">{{ $serviceRepairs }}</span>
</div>

        <div style="display:flex;align-items:center;gap:10px;">
            <div style="width:32px;height:32px;background:#1a1a2e;border-radius:50%;display:flex;align-items:center;justify-content:center;border:1px solid #ec4899;">
                <i class="fas fa-user-crown" style="font-size:13px;color:#ec4899;"></i>
            </div>

            <span style="font-size:14px;font-weight:500;color:white;">
                {{ Session::get('admin_name') }}
            </span>
        </div>

    </div>

</header>

        <main class="view-body">
            @yield('admin-content')
        </main>
    </div>

<script src="https://code.jquery.com/jquery-3.7.1.min.js"></script>
<script>
    $(document).ready(function() {
        $('#toggleAdminSidebar, #closeSidebarBtn, #adminSidebarOverlay').on('click', function() {
            $('#sidebarAdminContainer').toggleClass('open-mobile');
            $('#adminSidebarOverlay').toggleClass('show-overlay');
        });
    });
</script>
@stack('admin-scripts')
</body>
</html>