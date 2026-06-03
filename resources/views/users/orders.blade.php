@extends('layouts.app')

@section('title', 'My Orders - RevoDevice')

@push('styles')
<style>
    /* Global Layout Component Rules */
    .profile-layout { display: grid; grid-template-columns: 280px 1fr; gap: 30px; padding: 40px 0; }
    .profile-sidebar { background: #111118; border: 1px solid #1e1e2a; border-radius: 20px; padding: 24px 16px; }
    .sidebar-link {
        display: flex; align-items: center; gap: 12px; padding: 14px 20px; color: #94a3b8;
        text-decoration: none; border-radius: 12px; font-weight: 500; transition: all 0.2s; margin-bottom: 8px;
    }
    .sidebar-link:hover, .sidebar-link.active { background: #1a1a2e; color: #3b82f6; }
    .sidebar-link.active { border-left: 4px solid #8b5cf6; background: linear-gradient(90deg, #16162a 0%, #111118 100%); }

    /* Orders Filter Tabs UI Wrapper Setup */
    .orders-tabs-nav {
        display: flex; gap: 10px; border-bottom: 1px solid #1e1e2a; margin-bottom: 25px; padding-bottom: 10px;
    }
    .tab-trigger-btn {
        background: transparent; border: none; color: #64748b; font-weight: 600; font-size: 15px;
        padding: 10px 24px; cursor: pointer; transition: 0.2s; border-radius: 30px;
    }
    .tab-trigger-btn:hover { color: #e2e8f0; }
    .tab-trigger-btn.active { background: #1a1a2e; color: #3b82f6; }

    /* List Content Card Blocks Layout Vibe */
    .order-item-card { background: #111118; border: 1px solid #1e1e2a; border-radius: 24px; padding: 24px; margin-bottom: 20px; }
    .modal-backdrop {
        display: none; position: fixed; top: 0; left: 0; width: 100%; height: 100%;
        background: rgba(5, 5, 8, 0.85); z-index: 99999; justify-content: center; align-items: center; backdrop-filter: blur(8px);
    }
    .modal-content-area { background: #111118; border-radius: 24px; max-width: 600px; width: 90%; max-height: 85vh; overflow-y: auto; border: 1px solid #1e1e2a; }
    
    /* Summary Modal Element Overrides */
    .summary-section { margin-bottom: 25px; }
    .summary-section h4 { color: #3b82f6; margin-bottom: 14px; font-size: 15px; font-weight: 600; border-left: 3px solid #3b82f6; padding-left: 12px; }
    .summary-item { padding: 10px 0; border-bottom: 1px solid #1e1e2a; font-size: 14px; display: flex; align-items: center; gap: 12px; }
    .summary-item.yes { color: #10b981; }
    .summary-item.no { color: #ef4444; }
    .defect-tag { display: inline-block; background: rgba(239, 68, 68, 0.15); color: #f87171; border: 1px solid rgba(239, 68, 68, 0.25); padding: 6px 14px; border-radius: 20px; font-size: 12px; margin: 4px; }

    /* Mobile Responsive Logic */
    .mobile-nav-toggle { display: none; background: #111118; border: 1px solid #1e1e2a; color: #e2e8f0; padding: 12px 20px; width: 100%; border-radius: 12px; align-items: center; justify-content: space-between; margin-bottom: 20px; cursor: pointer; }
    @media (max-width: 991px) {
        .profile-layout { grid-template-columns: 1fr; padding: 20px 0; }
        .mobile-nav-toggle { display: flex; }
        .sidebar-wrapper { position: fixed; top: 0; left: -100%; width: 280px; height: 100%; background: #0a0a0f; z-index: 99999; padding: 20px; box-shadow: 25px 0 50px -12px rgba(0,0,0,0.5); }
        .sidebar-wrapper.open { left: 0; }
        .sidebar-overlay { display: none; position: fixed; top: 0; left: 0; width: 100%; height: 100%; background: rgba(0,0,0,0.6); z-index: 99998; backdrop-filter: blur(4px); }
        .sidebar-overlay.show { display: block; }
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
                    <h4 style="color: white; font-size: 16px; font-weight: 600;">{{ $user->name }}</h4>
                    <p style="color: #64748b; font-size: 12px; margin-top: 4px;">{{ $user->phone }}</p>
                </div>

                <nav>
                    <a href="{{ route('profile') }}" class="sidebar-link">
                        <i class="fas fa-user" style="width: 20px;"></i> My Profile
                    </a>
                    <a href="{{ route('my-cart') }}" class="sidebar-link">
                        <i class="fas fa-shopping-cart" style="width: 20px;"></i> My Cart
                    </a>
                    <a href="{{ route('my-orders') }}" class="sidebar-link active">
                        <i class="fas fa-box-open" style="width: 20px;"></i> My Order
                    </a>
                    <hr style="border: none; border-top: 1px solid #1e1e2a; margin: 15px 0;">
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
            <h2 class="gradient-text" style="font-size: 24px; font-weight: 700; margin-bottom: 6px;">My Orders</h2>
            <p style="color: #64748b; font-size: 14px; margin-bottom: 30px;">Track historical asset sell orders and processing status codes instantly.</p>

            <div class="orders-tabs-nav">
                <button class="tab-trigger-btn active" data-target="pendingTab">Pending Allocation</button>
                <button class="tab-trigger-btn" data-target="completedTab">Completed Sales</button>
                <button class="tab-trigger-btn" data-target="cancelledTab">Cancelled Requests</button>
            </div>

            <div id="pendingTab" class="tab-content-panel">
                @if(count($pendingOrders) > 0)
                    @foreach($pendingOrders as $order)
                        @include('users.partials.order_card_row', ['item' => $order, 'type' => 'pending'])
                    @endforeach
                @else
                    @include('users.partials.order_empty_state', ['msg' => 'No active pending orders processing right now.'])
                @endif
            </div>

            <div id="completedTab" class="tab-content-panel" style="display: none;">
                @if(count($completedOrders) > 0)
                    @foreach($completedOrders as $order)
                        @include('users.partials.order_card_row', ['item' => $order, 'type' => 'completed'])
                    @endforeach
                @else
                    @include('users.partials.order_empty_state', ['msg' => 'No finalized pickup evaluations found.'])
                @endif
            </div>

            <div id="cancelledTab" class="tab-content-panel" style="display: none;">
                @if(count($cancelledOrders) > 0)
                    @foreach($cancelledOrders as $order)
                        @include('users.partials.order_card_row', ['item' => $order, 'type' => 'cancelled'])
                    @endforeach
                @else
                    @include('users.partials.order_empty_state', ['msg' => 'No rejected or dropped orders recorded.'])
                @endif
            </div>

        </div>
    </div>
</div>

<div id="summaryModal" class="modal-backdrop">
    <div class="modal-content-area">
        <div style="display: flex; justify-content: space-between; align-items: center; padding: 20px 25px; border-bottom: 1px solid #1e1e2a; position: sticky; top: 0; background: #111118; z-index: 10;">
            <h3 style="color: white; font-size: 18px; font-weight: 600; margin: 0;">Order #<span id="summaryOrderId"></span> - Assessment Details</h3>
            <button class="close-modal-btn" style="background: none; border: none; color: #94a3b8; font-size: 26px; cursor: pointer;">&times;</button>
        </div>
        <div id="summaryContent" style="padding: 25px;"></div>
    </div>
</div>
@endsection

@push('scripts')
<script src="https://code.jquery.com/jquery-3.7.1.min.js"></script>
<script>
$(document).ready(function() {
    
    // 1. Mobile Layout Dropdown Controls Integration
    $('#openMobileSidebar').on('click', function() {
        $('#sidebarMenu').addClass('open'); $('#sidebarOverlay').addClass('show');
    });
    $('#closeMobileSidebar, #sidebarOverlay, .close-modal-btn').on('click', function() {
        $('#sidebarMenu').removeClass('open'); $('#sidebarOverlay').removeClass('show');
        $('.modal-backdrop').css('display', 'none');
    });

    // 2. Tab Engine Panel Display Mapping Logic Layout Layers
    $('.tab-trigger-btn').on('click', function() {
        $('.tab-trigger-btn').removeClass('active');
        $(this).addClass('active');
        
        const destination = $(this).data('target');
        $('.tab-content-panel').hide();
        $('#' + destination).fadeIn(200);
    });

     $('#logoutForm').on('submit', function(e) {
        e.preventDefault();

        $.ajax({
            url: $(this).attr('action'),
            type: 'POST',
            data: $(this).serialize(),
            success: function(response) {
                console.log(response);

                if (response.success) {
                    window.location.href = "/";
                }
            }
        });
    });

    // 3. View Summary Modal Event Loop Engine
    $('.view-summary-trigger').on('click', function() {
        const orderId = $(this).data('order-id');
        $('#summaryOrderId').text(orderId);
        $('#summaryModal').css('display', 'flex');
        
        $('#summaryContent').html(`
            <div style="text-align: center; padding: 30px;">
                <i class="fas fa-spinner fa-spin" style="font-size: 32px; color: #3b82f6;"></i>
                <p style="color: #64748b; margin-top: 12px; font-size: 14px;">Reading assessment values...</p>
            </div>
        `);

        $.ajax({
            url: '/api/view_summary/' + orderId,
            method: 'GET',
            dataType: 'json',
            success: function(response) {
                if (response.status === true) {
                    let html = '<div class="summary-section"><h4>📋 Condition Assessment</h4>';
                    if (response.question && response.question.length > 0) {
                        response.question.forEach(function(q) {
                            const isYes = q.toLowerCase().startsWith('yes');
                            html += `<div class="summary-item ${isYes ? 'yes' : 'no'}">
                                <span>${isYes ? '✅' : '❌'}</span>
                                <span><strong>${isYes ? 'Yes' : 'No'}</strong> - ${escapeHtml(q.replace(/^(yes|no)\s/i, ''))}</span>
                            </div>`;
                        });
                    }
                    html += '</div><div class="summary-section"><h4>⚠️ Reported Issues / Conditions</h4><div style="margin-top: 5px;">';
                    if (response.defects && response.defects.length > 0) {
                        response.defects.forEach(function(defect) {
                            html += `<span class="defect-tag">${escapeHtml(defect)}</span>`;
                        });
                    } else {
                        html += '<span class="defect-tag" style="background:rgba(16,185,129,0.15); color:#34d399; border-color:rgba(16,185,129,0.25);">Perfect parameters observed</span>';
                    }
                    html += '</div></div>';
                    $('#summaryContent').html(html);
                } else { $('#summaryContent').html('<p style="color:#ef4444; text-align:center;">Failed to trace summary entries.</p>'); }
            },
            error: function() { $('#summaryContent').html('<p style="color:#ef4444; text-align:center;">API Connectivity Error layer failed.</p>'); }
        });
    });

    // 4. Reevaluate Route Redirects Hook Injection
    $('.reevaluate-trigger').on('click', function() {
        const slug = $(this).data('slug');
        if (slug) window.location.href = '/sell-old-mobile-phone/used-' + slug;
        else alert('Unable to configure explicit model redirect values.');
    });

    // 5. AJAX Cancel Order Action Handler 
    $('.cancel-order-trigger').on('click', function() {
        const orderId = $(this).data('order-id');
        const $btn = $(this);

        if (confirm('Are you absolutely certain you want to reject and cancel Order #' + orderId + '?')) {
            $btn.prop('disabled', true).html('<i class="fas fa-spinner fa-spin"></i> Processing...');
            
            $.ajax({
                url: "{{ route('cancel.order') }}",
                method: "POST",
                headers: { 'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content') },
                data: { order_id: orderId },
                success: function(res) {
                    if (res.success) {
                        alert(res.message);
                        window.location.reload(); // Hard refresh updates collections automatically across all panels
                    } else {
                        alert('Error execution aborted: ' + res.message);
                        $btn.prop('disabled', false).html('Cancel Sale <i class="fas fa-times-circle" style="margin-left:4px;"></i>');
                    }
                },
                error: function() {
                    alert('Server validation termination sequence error.');
                    $btn.prop('disabled', false).html('Cancel Sale <i class="fas fa-times-circle" style="margin-left:4px;"></i>');
                }
            });
        }
    });

    function escapeHtml(str) {
        if (!str) return '';
        return str.replace(/&/g, '&amp;').replace(/</g, '&lt;').replace(/>/g, '&gt;').replace(/"/g, '&quot;').replace(/'/g, '&#39;');
    }
});
</script>
@endpush