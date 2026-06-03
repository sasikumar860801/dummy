@extends('layouts.app')

@section('title', 'My Profile - RevoDevice')

@push('styles')
<link href="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css" rel="stylesheet" />
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

    /* Custom Form Layout styling rules */
    .form-group-custom {
        margin-bottom: 20px;
    }
    .form-group-custom label {
        display: block;
        color: #94a3b8;
        font-size: 13px;
        font-weight: 500;
        margin-bottom: 8px;
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

    /* Target Select2 Overrides to natively blend with your custom Dark Vibe */
    .select2-container--default .select2-selection--single {
        background-color: #1a1a2e !important;
        border: 1px solid #2a2a3a !important;
        border-radius: 12px !important;
        height: 46px !important;
        display: flex !important;
        align-items: center !important;
    }
    .select2-container--default .select2-selection--single .select2-selection__rendered {
        color: #e2e8f0 !important;
        padding-left: 16px !important;
    }
    .select2-container--default .select2-selection--single .select2-selection__arrow {
        height: 44px !important;
        right: 10px !important;
    }
    .select2-dropdown {
        background-color: #1a1a2e !important;
        border: 1px solid #2a2a3a !important;
        border-radius: 12px !important;
        color: #e2e8f0 !important;
        overflow: hidden;
    }
    .select2-container--default .select2-results__option--highlighted[aria-selected] {
        background-color: #3b82f6 !important;
    }
    .select2-container--default .select2-results__option[aria-selected=true] {
        background-color: #8b5cf6 !important;
    }
    .select2-search__field {
        background-color: #111118 !important;
        border: 1px solid #2a2a3a !important;
        color: white !important;
        border-radius: 6px !important;
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
        grid-template-columns: 280px 18fr;
        gap: 30px;
        padding: 40px 0;
    }

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
    
    <div class="mobile-nav-toggle" id="openMobileSidebar">
        <span><i class="fas fa-bars" style="margin-right: 10px; color: #3b82f6;"></i> Profile Menu</span>
        <i class="fas fa-chevron-right style-nav" style="font-size: 12px; color: #64748b;"></i>
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
                    <h4 style="color: white; font-size: 16px; font-weight: 600;" id="sidebarUserName">{{ $user->name }}</h4>
                    <p style="color: #64748b; font-size: 12px; margin-top: 4px;">{{ $user->phone }}</p>
                </div>

                <nav>
                    <a href="{{ route('profile') }}" class="sidebar-link active">
                        <i class="fas fa-user" style="width: 20px;"></i> My Profile
                    </a>
                    <a href="my-cart" class="sidebar-link" style="opacity: 0.5; cursor: not-allowed;">
                        <i class="fas fa-shopping-cart" style="width: 20px;"></i> My Cart
                    </a>
                    <a href="my-orders" class="sidebar-link" style="opacity: 0.5; cursor: not-allowed;">
                        <i class="fas fa-box-open" style="width: 20px;"></i> My Order
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

        <div class="card-dark" style="padding: 30px; position: relative;">
            <h2 class="gradient-text" style="font-size: 22px; font-weight: 700; margin-bottom: 6px;">Profile Settings</h2>
            <p style="color: #64748b; font-size: 14px; margin-bottom: 30px;">View and manage your core primary setup configurations.</p>

            <form id="profileUpdateForm">
                @csrf
                <div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(280px, 1fr)); gap: 20px;">
                    
                    <div class="form-group-custom">
                        <label for="name">Full Name *</label>
                        <input type="text" id="name" name="name" class="form-control-dark" value="{{ $user->name }}" required>
                    </div>

                    <div class="form-group-custom">
                        <label for="phone">Primary Phone Number (Read-Only)</label>
                        <input type="text" id="phone" class="form-control-dark" value="{{ $user->phone }}" disabled>
                    </div>

                    <div class="form-group-custom">
                        <label for="email">Email Address</label>
                        <input type="email" id="email" name="email" class="form-control-dark" value="{{ $user->email }}">
                    </div>

                    <div class="form-group-custom">
                        <label for="alternate_mob_no">Alternate Mobile Number</label>
                        <input type="tel" id="alternate_mob_no" name="alternate_mob_no" maxlength="10" class="form-control-dark" value="{{ $user->alternate_mob_no }}">
                    </div>
                </div>

                <div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(200px, 1fr)); gap: 20px; margin-top: 10px;">
                    <div class="form-group-custom">
                        <label for="landmark">Landmark</label>
                        <input type="text" id="landmark" name="landmark" class="form-control-dark" value="{{ $user->landmark }}">
                    </div>

                    <div class="form-group-custom">
                        <label for="state">State</label>
                        <select id="state" name="state" class="form-control-dark" style="width: 100%;">
                            <option value="">Select State</option>
                            @foreach($states as $stateName)
                                <option value="{{ $stateName }}" {{ $user->state == $stateName ? 'selected' : '' }}>{{ $stateName }}</option>
                            @endforeach
                        </select>
                    </div>

                    <div class="form-group-custom">
                        <label for="pincode">Pincode</label>
                        <input type="text" id="pincode" name="pincode" maxlength="6" class="form-control-dark" value="{{ $user->pincode }}">
                    </div>
                </div>

                <div class="form-group-custom" style="margin-top: 10px;">
                    <label for="address">Complete Street Address</label>
                    <textarea id="address" name="address" class="form-control-dark" rows="4" style="resize: none;">{{ $user->address }}</textarea>
                </div>

                <div id="formAlert" style="display: none; padding: 12px; border-radius: 10px; font-size: 14px; margin-bottom: 20px;"></div>

                <div style="margin-top: 30px;">
                    <button type="submit" id="saveProfileBtn" class="btn-gradient" style="color: white; padding: 14px 35px; border-radius: 30px; font-weight: 600; cursor: pointer; font-size: 15px;">
                        Update Profile
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js"></script>
<script>
$(document).ready(function() {
    // 1. Initialize Select2 Dynamic UI
    $('#state').select2({
        placeholder: "Select State",
        allowClear: true
    });

    // 2. Mobile Off-Canvas Menu Logic
    const $sidebarMenu = $('#sidebarMenu');
    const $sidebarOverlay = $('#sidebarOverlay');

    $('#openMobileSidebar').on('click', function() {
        $sidebarMenu.addClass('open');
        $sidebarOverlay.addClass('show');
    });

    $('#closeMobileSidebar, #sidebarOverlay').on('click', function() {
        $sidebarMenu.removeClass('open');
        $sidebarOverlay.removeClass('show');
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

    // 3. Dynamic AJAX Profile Form submission
    $('#profileUpdateForm').on('submit', function(e) {
        e.preventDefault();
        
        const $btn = $('#saveProfileBtn');
        const $alert = $('#formAlert');
        
        $btn.prop('disabled', true).text('Updating Content...');
        $alert.hide().removeClass('alert-success alert-danger');

        $.ajax({
            url: "{{ route('profile.update') }}",
            method: "POST",
            data: $(this).serialize(),
            dataType: "json",
            success: function(response) {
                if(response.success) {
                    $alert.css({ 'background': 'rgba(16, 185, 129, 0.1)', 'color': '#10b981', 'border': '1px solid #10b981' })
                           .text(response.message).fadeIn();
                    
                    // Live match the user layout fields seamlessly without reloading
                    $('#sidebarUserName').text($('#name').val());
                    if ($('#userNameSpan').length) {
                        $('#userNameSpan').text($('#name').val());
                    }
                } else {
                    $alert.css({ 'background': 'rgba(239, 68, 68, 0.1)', 'color': '#ef4444', 'border': '1px solid #ef4444' })
                           .text(response.message || 'Something went wrong.').fadeIn();
                }
            },
            error: function(xhr) {
                let errorMsg = 'Validation Error occurred.';
                if(xhr.responseJSON && xhr.responseJSON.message) {
                    errorMsg = xhr.responseJSON.message;
                }
                $alert.css({ 'background': 'rgba(239, 68, 68, 0.1)', 'color': '#ef4444', 'border': '1px solid #ef4444' })
                       .text(errorMsg).fadeIn();
            },
            complete: function() {
                $btn.prop('disabled', false).text('Update Profile');
            }
        });
    });
});
</script>
@endpush