@extends('layouts.app')

@section('title', 'My Cart - RevoDevice')

@push('styles')
<style>
    /* Sidebar Navigation Structural Theme Hooks */
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

    /* Cart Layout Component Specifics */
    .profile-layout {
        display: grid;
        grid-template-columns: 280px 1fr;
        gap: 30px;
        padding: 40px 0;
    }
    .cart-item-card {
        background: #111118;
        border: 1px solid #1e1e2a;
        border-radius: 24px;
        padding: 24px;
        margin-bottom: 20px;
        transition: transform 0.3s, border-color 0.3s;
    }
    .cart-item-card:hover {
        border-color: #2a2a3a;
    }
    
    /* Popups and Form Styling Rules */
    .modal-backdrop {
        display: none;
        position: fixed;
        top: 0;
        left: 0;
        width: 100%;
        height: 100%;
        background: rgba(5, 5, 8, 0.85);
        z-index: 99999;
        justify-content: center;
        align-items: center;
        backdrop-filter: blur(8px);
    }
    .modal-content-area {
        background: #111118;
        border-radius: 24px;
        max-width: 650px;
        width: 90%;
        max-height: 85vh;
        overflow-y: auto;
        border: 1px solid #1e1e2a;
        position: relative;
    }
    .form-control-dark {
        width: 100%;
        padding: 12px 16px;
        background: #1a1a2e;
        border: 1px solid #2a2a3a;
        border-radius: 12px;
        color: #e2e8f0;
        font-size: 14px;
        outline: none;
        transition: border-color 0.2s;
    }
    .form-control-dark:focus {
        border-color: #3b82f6;
    }
    .form-control-dark:disabled {
        background: #0f0f15;
        color: #64748b;
        border-color: #1e1e2a;
        cursor: not-allowed;
    }

    /* Summary Detail List Decorators */
    .summary-section { margin-bottom: 25px; }
    .summary-section h4 {
        color: #3b82f6;
        margin-bottom: 14px;
        font-size: 15px;
        font-weight: 600;
        border-left: 3px solid #3b82f6;
        padding-left: 12px;
    }
    .summary-item {
        padding: 10px 0;
        border-bottom: 1px solid #1e1e2a;
        font-size: 14px;
        display: flex;
        align-items: center;
        gap: 12px;
    }
    .summary-item.yes { color: #10b981; }
    .summary-item.no { color: #ef4444; }
    .defect-tag {
        display: inline-block;
        background: rgba(239, 68, 68, 0.15);
        color: #f87171;
        border: 1px solid rgba(239, 68, 68, 0.25);
        padding: 6px 14px;
        border-radius: 20px;
        font-size: 12px;
        margin: 4px;
    }

    /* Mobile Responsive Hooks */
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
    .sidebar-wrapper { transition: left 0.3s cubic-bezier(0.4, 0, 0.2, 1); }

    @media (max-width: 991px) {
        .profile-layout { grid-template-columns: 1fr; padding: 20px 0; }
        .mobile-nav-toggle { display: flex; }
        .sidebar-wrapper {
            position: fixed; top: 0; left: -100%; width: 280px; height: 100%;
            background: #0a0a0f; z-index: 99999; padding: 20px;
            box-shadow: 25px 0 50px -12px rgba(0,0,0,0.5);
        }
        .sidebar-wrapper.open { left: 0; }
        .sidebar-overlay {
            display: none; position: fixed; top: 0; left: 0; width: 100%; height: 100%;
            background: rgba(0,0,0,0.6); z-index: 99998; backdrop-filter: blur(4px);
        }
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
                    <a href="{{ route('my-cart') }}" class="sidebar-link active">
                        <i class="fas fa-shopping-cart" style="width: 20px;"></i> My Cart
                    </a>
                    <a href="{{ route('my-orders') }}" class="sidebar-link" style="opacity: 0.5; cursor: not-allowed;">
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
            <h2 class="gradient-text" style="font-size: 24px; font-weight: 700; margin-bottom: 6px;">My Cart</h2>
            <p style="color: #64748b; font-size: 14px; margin-bottom: 30px;">Manage pending estimations or complete your sale request orders.</p>

            @if(count($cartItems) > 0)
                @foreach($cartItems as $item)
                    <div class="cart-item-card">
                        <div style="display: flex; flex-wrap: wrap; gap: 24px; align-items: center;">
                            
                            <div style="flex: 0 0 100px; text-align: center;">
                                @if($item->model_img)
                                    <img src="{{ $item->model_img }}" alt="{{ $item->model_title }}" style="width: 85px; height: 85px; object-fit: contain;">
                                @else
                                    <div style="width: 85px; height: 85px; background: #1a1a2e; border-radius: 16px; display: flex; align-items: center; justify-content: center; margin: 0 auto;">
                                        <i class="fas fa-mobile-alt" style="font-size: 36px; color: #3b82f6;"></i>
                                    </div>
                                @endif
                            </div>

                            <div style="flex: 1; min-width: 200px;">
                                <h3 style="color: white; font-size: 19px; font-weight: 600; margin-bottom: 6px;">
                                    {{ $item->title }} {{ $item->model_title }}
                                </h3>
                                <div style="display: flex; flex-wrap: wrap; gap: 15px; font-size: 13px; color: #94a3b8;">
                                    <span><strong style="color: #64748b;">Storage:</strong> {{ $item->capacity ?? 'N/A' }}</span>
                                    <span><strong style="color: #64748b;">Reference ID:</strong> {{ $item->order_id }}</span>
                                </div>
                            </div>

                            <div style="min-width: 120px;">
                                <p style="color: #64748b; font-size: 12px; margin-bottom: 2px;">Estimated Value</p>
                                <h4 style="color: #3b82f6; font-size: 24px; font-weight: 800;">₹{{ number_format((float)$item->price) }}</h4>
                            </div>

                            <div style="display: flex; flex-direction: column; gap: 8px; min-width: 160px;">
                                <button class="view-summary-trigger" data-order-id="{{ $item->order_id }}" style="background: transparent; border: 1px solid #3b82f6; color: #3b82f6; padding: 10px 20px; border-radius: 30px; font-weight: 600; font-size: 13px; cursor: pointer; transition: 0.2s;">
                                    View Summary <i class="fas fa-eye" style="margin-left: 4px;"></i>
                                </button>
                                
                                <button class="reevaluate-trigger" data-slug="{{ $item->model_slug }}" style="background: transparent; border: 1px solid #f59e0b; color: #f59e0b; padding: 10px 20px; border-radius: 30px; font-weight: 600; font-size: 13px; cursor: pointer; transition: 0.2s;">
                                    Reevaluate <i class="fas fa-sync-alt" style="margin-left: 4px;"></i>
                                </button>

                                <button class="sell-now-trigger" 
                                        data-item-id="{{ $item->id }}" 
                                        data-model-id="{{ $item->model_id }}" 
                                        data-order-id="{{ $item->order_id }}" 
                                        style="background: linear-gradient(135deg, #3b82f6, #8b5cf6); color: white; padding: 10px 20px; border-radius: 30px; border: none; font-weight: 600; font-size: 13px; cursor: pointer; transition: 0.2s;">
                                    Sell Now <i class="fas fa-arrow-right" style="margin-left: 4px;"></i>
                                </button>
                            </div>

                        </div>
                    </div>
                @endforeach
            @else
                <div style="text-align: center; padding: 60px 20px; background: #111118; border: 1px solid #1e1e2a; border-radius: 24px;">
                    <div style="width: 70px; height: 70px; background: #1a1a2e; border-radius: 50%; display: flex; align-items: center; justify-content: center; margin: 0 auto 20px;">
                        <i class="fas fa-shopping-basket" style="font-size: 28px; color: #64748b;"></i>
                    </div>
                    <h3 style="color: white; font-size: 18px; font-weight: 600; margin-bottom: 6px;">Your cart is empty</h3>
                    <p style="color: #64748b; font-size: 14px; max-width: 300px; margin: 0 auto 20px;">You do not have any pending gadget sale evaluations in progress right now.</p>
                    <a href="{{ url('/') }}" class="btn-gradient" style="display: inline-block; padding: 12px 30px; border-radius: 30px; color: white; text-decoration: none; font-weight: 600; font-size: 14px;">Sell Device Now</a>
                </div>
            @endif
        </div>
    </div>
</div>

<div id="summaryModal" class="modal-backdrop">
    <div class="modal-content-area" style="max-width: 600px;">
        <div style="display: flex; justify-content: space-between; align-items: center; padding: 20px 25px; border-bottom: 1px solid #1e1e2a; position: sticky; top: 0; background: #111118; z-index: 10;">
            <h3 style="color: white; font-size: 18px; font-weight: 600; margin: 0;">Order #<span id="summaryOrderId"></span> - Condition Summary</h3>
            <button class="close-modal-btn" style="background: none; border: none; color: #94a3b8; font-size: 26px; cursor: pointer;">&times;</button>
        </div>
        <div id="summaryContent" style="padding: 25px;">
            </div>
    </div>
</div>

<div id="sellModal" class="modal-backdrop">
    <div class="modal-content-area" style="max-width: 580px;">
        <div style="display: flex; justify-content: space-between; align-items: center; padding: 20px 25px; border-bottom: 1px solid #1e1e2a; position: sticky; top: 0; background: #111118; z-index: 10;">
            <h3 style="color: white; font-size: 18px; font-weight: 600; margin: 0;">Complete Your Sale Request</h3>
            <button class="close-modal-btn" style="background: none; border: none; color: #94a3b8; font-size: 26px; cursor: pointer;">&times;</button>
        </div>
        <div style="padding: 25px;">
            <form id="sellForm">
                <input type="hidden" id="partial_order_item_id" name="partial_order_item_id">
                <input type="hidden" id="model_id" name="model_id">
                <input type="hidden" id="order_id" name="order_id">
                
                <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 15px; margin-bottom: 15px;">
                    <div>
                        <label style="color: #94a3b8; display: block; margin-bottom: 6px; font-size: 13px;">Full Name *</label>
                        <input type="text" id="name" name="name" class="form-control-dark" value="{{ $user->name }}" required>
                    </div>
                    <div>
                        <label style="color: #94a3b8; display: block; margin-bottom: 6px; font-size: 13px;">Primary Phone (Read-Only)</label>
                        <input type="tel" id="mobile_no" name="mobile_no" class="form-control-dark" value="{{ $user->phone }}" readonly>
                    </div>
                </div>

                <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 15px; margin-bottom: 15px;">
                    <div>
                        <label style="color: #94a3b8; display: block; margin-bottom: 6px; font-size: 13px;">Alternate Phone</label>
                        <input type="tel" id="alternate_mob_no" name="alternate_mob_no" maxlength="10" class="form-control-dark" value="{{ $user->alternate_mob_no }}">
                    </div>
                    <div>
                        <label style="color: #94a3b8; display: block; margin-bottom: 6px; font-size: 13px;">Email Address *</label>
                        <input type="email" id="email" name="email" class="form-control-dark" value="{{ $user->email }}" required>
                    </div>
                </div>

                <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 15px; margin-bottom: 15px;">
                    <div>
                        <label style="color: #94a3b8; display: block; margin-bottom: 6px; font-size: 13px;">Address Type *</label>
                        <select id="address_type" name="address_type" class="form-control-dark">
                            <option value="home">Home / Residence</option>
                            <option value="office">Office / Workplace</option>
                            <option value="other">Other Location</option>
                        </select>
                    </div>
                    <div>
                        <label style="color: #94a3b8; display: block; margin-bottom: 6px; font-size: 13px;">Landmark</label>
                        <input type="text" id="landmark" name="landmark" class="form-control-dark" value="{{ $user->landmark }}">
                    </div>
                </div>

                <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 15px; margin-bottom: 15px;">
                    <div>
                        <label style="color: #94a3b8; display: block; margin-bottom: 6px; font-size: 13px;">State *</label>
                        <input type="text" id="state" name="state" class="form-control-dark" value="{{ $user->state }}" required>
                    </div>
                    <div>
                        <label style="color: #94a3b8; display: block; margin-bottom: 6px; font-size: 13px;">Pincode *</label>
                        <input type="text" id="pincode" name="pincode" maxlength="6" class="form-control-dark" value="{{ $user->pincode }}" required>
                    </div>
                </div>

                <div style="margin-bottom: 15px;">
                    <label style="color: #94a3b8; display: block; margin-bottom: 6px; font-size: 13px;">Pickup Pickup Address Details *</label>
                    <textarea id="address" name="address" rows="2" class="form-control-dark" style="resize: none;" required>{{ $user->address }}</textarea>
                </div>

                <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 15px; margin-bottom: 20px;">
                    <div>
                        <label style="color: #94a3b8; display: block; margin-bottom: 6px; font-size: 13px;">Pickup Date Selection *</label>
                        <input type="date" id="pickup_date" name="pickup_date" class="form-control-dark" required>
                    </div>
                    <div>
                        <label style="color: #94a3b8; display: block; margin-bottom: 6px; font-size: 13px;">Preferred Timing Slot *</label>
                        <select id="pickup_time" name="pickup_time" class="form-control-dark" required>
                            <option value="7AM-1PM">7 AM - 1 PM</option>
                            <option value="1PM-5PM">1 PM - 5 PM</option>
                            <option value="5PM-10PM">5 PM - 10 PM</option>
                        </select>
                    </div>
                </div>

                <div style="margin-bottom: 25px;">
                    <label style="color: #94a3b8; display: block; margin-bottom: 6px; font-size: 13px;">Preferred Mode of Payment *</label>
                    <select id="payment_method" name="payment_method" class="form-control-dark" required>
                        <option value="cash">Instant Cash on Pickup</option>
                        <option value="upi">Direct UPI Transfer</option>
                        <option value="bank">Direct IMPS / Bank Wire</option>
                    </select>
                </div>

                <button type="submit" id="submitSellBtn" class="btn-gradient" style="width: 100%; color: white; padding: 14px; border-radius: 30px; font-weight: 600; border: none; cursor: pointer; font-size: 15px;">
                    Submit Sale Request <i class="fas fa-check-circle" style="margin-left: 4px;"></i>
                </button>
            </form>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script src="https://code.jquery.com/jquery-3.7.1.min.js"></script>
<script>
$(document).ready(function() {
    
    // 1. Mobile Dropdown Sidebar Hooks
    $('#openMobileSidebar').on('click', function() {
        $('#sidebarMenu').addClass('open');
        $('#sidebarOverlay').addClass('show');
    });
    $('#closeMobileSidebar, #sidebarOverlay, .close-modal-btn').on('click', function() {
        $('#sidebarMenu').removeClass('open');
        $('#sidebarOverlay').removeClass('show');
        $('.modal-backdrop').css('display', 'none');
    });

    // 2. Action - View Summary Trigger Logic
    $('.view-summary-trigger').on('click', function() {
        const orderId = $(this).data('order-id');
        $('#summaryOrderId').text(orderId);
        $('#summaryModal').css('display', 'flex');
        
        $('#summaryContent').html(`
            <div style="text-align: center; padding: 30px;">
                <i class="fas fa-spinner fa-spin" style="font-size: 32px; color: #3b82f6;"></i>
                <p style="color: #64748b; margin-top: 12px; font-size: 14px;">Fetching device parameters...</p>
            </div>
        `);

        $.ajax({
            url: '/api/view_summary/' + orderId,
            method: 'GET',
            dataType: 'json',
            success: function(response) {
                if (response.status === true) {
                    let html = '';
                    
                    // Condition Assessment Mapping Layer
                    html += '<div class="summary-section">';
                    html += '<h4>📋 Condition Assessment</h4>';
                    if (response.question && response.question.length > 0) {
                        response.question.forEach(function(q) {
                            const isYes = q.toLowerCase().startsWith('yes');
                            const statusClass = isYes ? 'yes' : 'no';
                            const statusIcon = isYes ? '✅' : '❌';
                            const cleanText = q.replace(/^(yes|no)\s/i, '');
                            
                            html += `<div class="summary-item ${statusClass}">
                                <span style="font-size: 14px;">${statusIcon}</span>
                                <span><strong>${isYes ? 'Yes' : 'No'}</strong> - ${escapeHtml(cleanText)}</span>
                            </div>`;
                        });
                    }
                    html += '</div>';
                    
                    // Defects / Assessment Mapping Layer
                    html += '<div class="summary-section">';
                    html += '<h4>⚠️ Reported Issues / Conditions</h4>';
                    html += '<div style="margin-top: 5px;">';
                    if (response.defects && response.defects.length > 0) {
                        response.defects.forEach(function(defect) {
                            html += `<span class="defect-tag">${escapeHtml(defect)}</span>`;
                        });
                    } else {
                        html += '<span class="defect-tag" style="background:rgba(16,185,129,0.15); color:#34d399; border-color:rgba(16,185,129,0.25);">No structural issues reported</span>';
                    }
                    html += '</div></div>';
                    
                    $('#summaryContent').html(html);
                } else {
                    renderSummaryError();
                }
            },
            error: function() {
                renderSummaryError();
            }
        });
    });

    function renderSummaryError() {
        $('#summaryContent').html(`
            <div style="text-align: center; padding: 20px; color: #ef4444;">
                <i class="fas fa-exclamation-triangle" style="font-size: 32px;"></i>
                <p style="margin-top: 10px; font-size: 14px;">Failed to gather order summary information components.</p>
            </div>
        `);
    }

    // 3. Action - Reevaluate Redirect Hook
    $('.reevaluate-trigger').on('click', function() {
        const slug = $(this).data('slug');
        if (slug) {
            window.location.href = '/sell-old-mobile-phone/used-' + slug;
        } else {
            alert('Unable to identify exact target route slug variant.');
        }
    });

    // 4. Action - Checkout / Sell Modal Modal Builder
    $('.sell-now-trigger').on('click', function() {
        // Hydrate configuration properties right out of individual dataset arrays
        $('#partial_order_item_id').val($(this).data('item-id'));
        $('#model_id').val($(this).data('model-id'));
        $('#order_id').val($(this).data('order-id'));

        // Establish pickup date offsets safely (+2 days constraint rule)
        const dateTarget = new Date();
        dateTarget.setDate(dateTarget.getDate() + 2);
        $('#pickup_date').attr('min', dateTarget.toISOString().split('T')[0]);

        $('#sellModal').css('display', 'flex');
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

    // 5. Action - Finalize Order Checkout API Submission
    $('#sellForm').on('submit', function(e) {
        e.preventDefault();
        
        const $btn = $('#submitSellBtn');
        $btn.prop('disabled', true).html('<i class="fas fa-spinner fa-spin"></i> finalising sale routing...');

        $.ajax({
            url: "{{ route('submit.sell.order') }}",
            method: 'POST',
            headers: {
                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
            },
            data: $(this).serialize(),
            success: function(response) {
                if (response.success) {
                    alert('Order executed successfully! ID: ' + response.order_id);
                    window.location.href = '/order-success/' + response.order_id;
                } else {
                    alert('Execution Interrupted: ' + response.message);
                    resetSubmitButton($btn);
                }
            },
            error: function(xhr) {
                alert('Connection failure: ' + (xhr.responseJSON?.message || 'Transaction submission error.'));
                resetSubmitButton($btn);
            }
        });
    });

    function resetSubmitButton($el) {
        $el.prop('disabled', false).html('Submit Sale Request <i class="fas fa-check-circle" style="margin-left: 4px;"></i>');
    }

    function escapeHtml(str) {
        if (!str) return '';
        return str.replace(/&/g, '&amp;').replace(/</g, '&lt;').replace(/>/g, '&gt;').replace(/"/g, '&quot;').replace(/'/g, '&#39;');
    }
});
</script>
@endpush