@extends('layouts.app')

@section('title', 'My Service Requests - RevoDevice')

@push('styles')
<style>
    /* Absolute Sidebar Theme Syncing */
    .profile-sidebar {
        background: #111118;
        border: 1px solid #1e1e2a;
        border-radius: 20px;
        padding: 24px 16px;
    }
    .sidebar-link {
        display: flex;
        align-items: center;
        gap: 12px;
        padding: 14px 20px;
        color: #94a3b8;
        text-decoration: none;
        border-radius: 12px;
        font-weight: 500;
        transition: all 0.2s ease;
        margin-bottom: 8px;
    }
    .sidebar-link:hover, .sidebar-link.active {
        background: #1a1a2e;
        color: #3b82f6;
    }
    .sidebar-link.active {
        border-left: 4px solid #8b5cf6;
        background: linear-gradient(90deg, #16162a 0%, #111118 100%);
    }

    /* Mobile Quick Navigation Toggle Layout styling rules */
    .mobile-nav-toggle {
        display: none;
        background: #111118;
        border: 1px solid #1e1e2a;
        color: #e2e8f0;
        padding: 12px 20px;
        width: 100%;
        border-radius: 12px;
        align-items: center;
        justify-content: space-between;
        margin-bottom: 20px;
        cursor: pointer;
    }

    /* Complete Layout Grid distribution rules */
    .profile-layout {
        display: grid;
        grid-template-columns: 280px 1fr;
        gap: 30px;
        padding: 40px 0;
    }
    
    .card-dark {
        background: #111118;
        border: 1px solid #1e1e2a;
        border-radius: 20px;
    }

    /* Services Specific Dark Theme Styles */
    .tabs { 
        display: flex; 
        gap: 10px; 
        margin-bottom: 25px; 
        border-bottom: 2px solid #1e1e2a; 
    }
    .tab-btn { 
        padding: 12px 24px; 
        border: none; 
        background: transparent; 
        cursor: pointer; 
        font-weight: 600; 
        color: #64748b; 
        border-bottom: 2px solid transparent; 
        margin-bottom: -2px;
        transition: 0.3s;
    }
    .tab-btn:hover {
        color: #e2e8f0;
    }
    .tab-btn.active { 
        color: #3b82f6; 
        border-color: #3b82f6; 
    }
    .tab-content { display: none; }
    .tab-content.active { display: block; }
    
    .service-card { 
        background: #1a1a2e; 
        border: 1px solid #2a2a3a; 
        border-radius: 12px; 
        padding: 20px; 
        margin-bottom: 15px; 
        display: flex; 
        justify-content: space-between; 
        align-items: center; 
        transition: transform 0.2s, box-shadow 0.2s;
    }
    .service-card:hover {
        transform: translateY(-2px);
        box-shadow: 0 5px 15px rgba(0,0,0,0.3);
        border-color: #3b82f6;
    }
    
    .status-badge { 
        padding: 6px 14px; 
        border-radius: 20px; 
        font-size: 12px; 
        font-weight: 700; 
        text-transform: uppercase; 
        letter-spacing: 0.5px;
    }
    .status-pending { 
        background: rgba(217, 119, 6, 0.15); 
        color: #fbbf24; 
        border: 1px solid rgba(217, 119, 6, 0.3);
    }
    .status-completed { 
        background: rgba(16, 185, 129, 0.15); 
        color: #34d399; 
        border: 1px solid rgba(16, 185, 129, 0.3);
    }
    .empty-state {
        color: #64748b;
        text-align: center;
        padding: 40px 20px;
        background: #1a1a2e;
        border-radius: 12px;
        border: 1px dashed #2a2a3a;
    }

    /* Mobile Sidebar Media Queries */
    @media (max-width: 991px) {
        .profile-layout {
            grid-template-columns: 1fr;
            padding: 20px 0;
        }
        .mobile-nav-toggle {
            display: flex;
        }
        .sidebar-wrapper {
            position: fixed;
            top: 0;
            left: -100%;
            width: 280px;
            height: 100%;
            background: #0a0a0f;
            z-index: 99999;
            transition: left 0.3s cubic-bezier(0.4, 0, 0.2, 1);
            box-shadow: 25px 0 50px -12px rgba(0,0,0,0.5);
            padding: 20px;
        }
        .sidebar-wrapper.open {
            left: 0;
        }
        .sidebar-overlay {
            display: none;
            position: fixed;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            background: rgba(0,0,0,0.6);
            z-index: 99998;
            backdrop-filter: blur(4px);
        }
        .sidebar-overlay.show {
            display: block;
        }
    }
</style>
@endpush

@section('content')
<div class="container">
    
    <br>
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

                      <a href="{{ route('buy_orders') }}" class="sidebar-link ">
                        <i class="fas fa-shopping-bag" style="width: 20px;"></i> Buy Orders
                    </a>

                      <a href="{{ route('my_service_repair') }}" class="sidebar-link active">
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

        <div class="card-dark" style="padding: 30px;">
            <h2 style="font-size: 22px; font-weight: 700; margin-bottom: 6px; color: white;">My Service & Repair Requests</h2>
            <p style="color: #64748b; font-size: 14px; margin-bottom: 25px;">Track the status of your device repairs.</p>

            @if(session('success'))
                <div style="background: rgba(16, 185, 129, 0.1); color: #34d399; padding: 15px 20px; border-radius: 8px; border: 1px solid rgba(16, 185, 129, 0.2); margin-bottom: 20px;">
                    <i class="fas fa-check-circle" style="margin-right: 8px;"></i> {{ session('success') }}
                </div>
            @endif

            <div class="tabs">
                <button class="tab-btn active" onclick="switchUserTab('pending', event)">Pending</button>
                <button class="tab-btn" onclick="switchUserTab('completed', event)">Completed</button>
            </div>

            <div id="pending" class="tab-content active">
                @forelse($pendingServices as $service)
                    <div class="service-card">
                        <div style="display: flex; align-items: center; gap: 15px;">
                            @if($service->model_img)
                                <img src="{{ asset('media/images/model/' . $service->model_img) }}" alt="{{ $service->model_title }}" style="width: 60px; height: 60px; object-fit: contain; border-radius: 8px; background: #fff; padding: 5px;">
                            @else
                                <div style="width: 60px; height: 60px; background: #2a2a3a; border-radius: 8px; display: flex; align-items: center; justify-content: center;">
                                    <i class="fas fa-mobile-alt" style="color: #64748b; font-size: 24px;"></i>
                                </div>
                            @endif
                            
                            <div>
                                <h4 style="margin: 0 0 8px 0; color: #e2e8f0; font-size: 18px;">{{ $service->model_title ?? 'Unknown Model' }}</h4>
                                <p style="margin: 0; color: #94a3b8; font-size: 14px;"><i class="fas fa-exclamation-circle" style="font-size: 12px; margin-right: 5px; color: #64748b;"></i> Issue: {{ $service->category_name }} - {{ $service->subcategory_name }}</p>
                                <p style="margin: 8px 0 0 0; font-size: 12px; color: #64748b;">ID: #{{ $service->service_id }} &bull; Date: {{ \Carbon\Carbon::parse($service->created_at)->format('M d, Y') }}</p>
                            </div>
                        </div>
                        <div>
                            <span class="status-badge status-pending">Pending</span>
                        </div>
                    </div>
                @empty
                    <div class="empty-state">
                        <i class="fas fa-box-open" style="font-size: 30px; margin-bottom: 10px; color: #33334d;"></i>
                        <p>No pending service requests found.</p>
                    </div>
                @endforelse
            </div>

            <div id="completed" class="tab-content">
                @forelse($completedServices as $service)
                    <div class="service-card">
                        <div style="display: flex; align-items: center; gap: 15px;">
                            @if($service->model_img)
                                <img src="{{ asset('media/images/model/' . $service->model_img) }}" alt="{{ $service->model_title }}" style="width: 60px; height: 60px; object-fit: contain; border-radius: 8px; background: #fff; padding: 5px;">
                            @else
                                <div style="width: 60px; height: 60px; background: #2a2a3a; border-radius: 8px; display: flex; align-items: center; justify-content: center;">
                                    <i class="fas fa-mobile-alt" style="color: #64748b; font-size: 24px;"></i>
                                </div>
                            @endif

                            <div>
                                <h4 style="margin: 0 0 8px 0; color: #e2e8f0; font-size: 18px;">{{ $service->model_title ?? 'Unknown Model' }}</h4>
                                <p style="margin: 0; color: #94a3b8; font-size: 14px;"><i class="fas fa-check-circle" style="font-size: 12px; margin-right: 5px; color: #64748b;"></i> Issue: {{ $service->category_name }} - {{ $service->subcategory_name }}</p>
                                <p style="margin: 8px 0 0 0; font-size: 12px; color: #64748b;">ID: #{{ $service->service_id }} &bull; Date: {{ \Carbon\Carbon::parse($service->created_at)->format('M d, Y') }}</p>
                            </div>
                        </div>
                        <div>
                            <span class="status-badge status-completed">Completed</span>
                        </div>
                    </div>
                @empty
                    <div class="empty-state">
                        <i class="fas fa-clipboard-check" style="font-size: 30px; margin-bottom: 10px; color: #33334d;"></i>
                        <p>No completed service requests yet.</p>
                    </div>
                @endforelse
            </div>
        </div>

    </div>
</div>
@endsection

@push('scripts')
<script>
    // Tab Switching Logic
    function switchUserTab(tabId, event) {
        document.querySelectorAll('.tab-btn').forEach(btn => btn.classList.remove('active'));
        document.querySelectorAll('.tab-content').forEach(content => content.classList.remove('active'));
        
        event.target.classList.add('active');
        document.getElementById(tabId).classList.add('active');
    }

    // Mobile Sidebar Toggle Logic
    document.addEventListener('DOMContentLoaded', function() {
        const openBtn = document.getElementById('openMobileSidebar');
        const closeBtn = document.getElementById('closeMobileSidebar');
        const sidebar = document.getElementById('sidebarMenu');
        const overlay = document.getElementById('sidebarOverlay');

        if(openBtn && closeBtn && sidebar && overlay) {
            openBtn.addEventListener('click', () => {
                sidebar.classList.add('open');
                overlay.classList.add('show');
            });

            closeBtn.addEventListener('click', () => {
                sidebar.classList.remove('open');
                overlay.classList.remove('show');
            });

            overlay.addEventListener('click', () => {
                sidebar.classList.remove('open');
                overlay.classList.remove('show');
            });
        }
    });
</script>
@endpush