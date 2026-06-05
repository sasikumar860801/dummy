<style>
    .admin-sidebar { width: 280px; background: #0f0f15; border-right: 1px solid #1e1e2a; height: 100vh; position: fixed; top: 0; left: 0; z-index: 1000; padding: 24px 20px; display: flex; flex-direction: column; transition: transform 0.3s ease; }
    .nav-header { display: flex; align-items: center; justify-content: space-between; margin-bottom: 35px; }
    .nav-links-stack { display: flex; flex-direction: column; gap: 6px; flex: 1; }
    .nav-item-link { display: flex; align-items: center; gap: 14px; padding: 14px 18px; color: #94a3b8; text-decoration: none; border-radius: 12px; font-weight: 500; font-size: 14px; transition: 0.2s; }
    .nav-item-link:hover, .nav-item-link.active { background: #1a1a2e; color: #ec4899; }
    .nav-item-link.active { background: linear-gradient(90deg, #1d1225 0%, #0f0f15 100%); border-left: 4px solid #ec4899; font-weight: 600; }
    
    .sidebar-overlay-layer { display: none; position: fixed; top: 0; left: 0; width: 100vw; height: 100vh; background: rgba(0,0,0,0.6); backdrop-filter: blur(4px); z-index: 999; }

    @media (max-width: 991px) {
        .admin-sidebar { transform: translateX(-100%); }
        .admin-sidebar.open-mobile { transform: translateX(0); }
        .sidebar-overlay-layer.show-overlay { display: block; }
    }
</style>

<div class="sidebar-overlay-layer" id="adminSidebarOverlay"></div>

<aside class="admin-sidebar" id="sidebarAdminContainer">
    <div class="nav-header">
        <div style="display: flex; align-items: center; gap: 10px;">
            <div style="width: 35px; height: 35px; background: linear-gradient(135deg, #ec4899, #8b5cf6); border-radius: 10px; display: flex; align-items: center; justify-content: center;">
                <i class="fas fa-terminal" style="color: white; font-size: 16px;"></i>
            </div>
            <span class="gradient-text" style="font-weight: 800; font-size: 20px;">RevoAdmin</span>
        </div>
        <button id="closeSidebarBtn" style="background:none; border:none; color:#64748b; font-size:20px; cursor:pointer;" class="d-lg-none">
            <i class="fas fa-times"></i>
        </button>
    </div>

    <nav class="nav-links-stack">
        <a href="{{ route('admin.dashboard') }}" class="nav-item-link {{ Route::is('admin.dashboard') ? 'active' : '' }}">
            <i class="fas fa-chart-pie" style="width: 20px;"></i> Dashboard
        </a>
        <a href="{{ route('admin.orders') }}" class="nav-item-link {{ Route::is('admin.orders') ? 'active' : '' }}">
            <i class="fas fa-shopping-bag" style="width: 20px;"></i> Orders
        </a>
        <a href="{{ route('admin.stock') }}" class="nav-item-link {{ Route::is('admin.stock') ? 'active' : '' }}">
            <i class="fas fa-boxes" style="width: 20px;"></i> Stock
        </a>
        <a href="{{ route('admin.models') }}" class="nav-item-link {{ Route::is('admin.models') ? 'active' : '' }}">
            <i class="fas fa-mobile" style="width: 20px;"></i> Models
        </a>
    </nav>

    <div style="border-top: 1px solid #1e1e2a; padding-top: 15px;">
        <a href="#" class="nav-item-link" style="color: #ef4444;" onclick="event.preventDefault(); document.getElementById('adminLogoutForm').submit();">
            <i class="fas fa-sign-out-alt" style="width: 20px;"></i> Terminate Session
        </a>
        <form id="adminLogoutForm" action="{{ route('admin.logout') }}" method="POST" style="display: none;">
            @csrf
        </form>
    </div>
</aside>