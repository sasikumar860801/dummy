@extends('layouts.app')

@section('title', 'Partner with Us - RevoDevice Dealers')

@push('styles')
<style>
    .dealer-container { 
        max-width: 1100px; 
        margin: 40px auto; 
        padding: 0 20px; 
    }
    
    .dealer-layout { 
        display: grid; 
        grid-template-columns: 1fr 1.2fr; 
        gap: 40px; 
        align-items: center; 
    }
    
    @media (max-width: 991px) { 
        .dealer-layout { grid-template-columns: 1fr; } 
    }

    /* Left Side: Pitch */
    .dealer-pitch h1 { 
        color: #ffffff; 
        font-size: 36px; 
        font-weight: 700; 
        margin-bottom: 20px; 
        line-height: 1.2; 
    }
    .dealer-pitch h1 span { color: #3b82f6; }
    .dealer-pitch > p { 
        color: #94a3b8; 
        font-size: 16px; 
        line-height: 1.6; 
        margin-bottom: 30px; 
    }

    .benefit-item { 
        display: flex; 
        gap: 15px; 
        margin-bottom: 25px; 
    }
    .benefit-icon { 
        background: rgba(59, 130, 246, 0.1); 
        color: #3b82f6; 
        width: 50px; 
        height: 50px; 
        border-radius: 12px; 
        display: flex; 
        align-items: center; 
        justify-content: center; 
        font-size: 20px; 
        flex-shrink: 0;
    }
    .benefit-text h4 { color: #e2e8f0; margin: 0 0 5px 0; font-size: 18px; }
    .benefit-text p { color: #64748b; margin: 0; font-size: 14px; line-height: 1.5; }

    /* Right Side: Form */
    .dealer-form-card { 
        background: #111118; 
        border: 1px solid #1e1e2a; 
        border-radius: 20px; 
        padding: 40px; 
        box-shadow: 0 10px 30px rgba(0,0,0,0.5);
    }
    .dealer-form-card h3 { color: #fff; margin-top: 0; margin-bottom: 25px; font-size: 22px; }
    
    .form-row { display: grid; grid-template-columns: 1fr 1fr; gap: 15px; }
    @media (max-width: 600px) { .form-row { grid-template-columns: 1fr; } }

    .form-group { margin-bottom: 20px; }
    .form-group label { display: block; color: #94a3b8; margin-bottom: 8px; font-size: 13px; font-weight: 600; text-transform: uppercase; letter-spacing: 0.5px; }
    .form-control { 
        width: 100%; 
        background: #0a0a0f; 
        border: 1px solid #2a2a3a; 
        color: #fff; 
        padding: 12px 15px; 
        border-radius: 8px; 
        transition: 0.3s; 
    }
    .form-control:focus { outline: none; border-color: #3b82f6; box-shadow: 0 0 0 2px rgba(59,130,246,0.2); }
    
    .submit-btn { 
        background: #3b82f6; 
        color: white; 
        border: none; 
        padding: 14px 24px; 
        border-radius: 8px; 
        font-weight: 600; 
        font-size: 16px;
        cursor: pointer; 
        width: 100%; 
        transition: 0.3s; 
        margin-top: 10px;
    }
    .submit-btn:hover { background: #2563eb; transform: translateY(-2px); }
</style>
@endpush

@section('content')
<div class="dealer-container">
    <div class="dealer-layout">
        
        <div class="dealer-pitch">
            <h1>Grow your business with <span>RevoDevice</span></h1>
            <p>Join our network of verified electronics dealers. Whether you want to liquidate dead stock, source premium refurbished devices, or become an authorized service partner, we provide the platform to scale your operations.</p>
            
            <div class="benefit-item">
                <div class="benefit-icon"><i class="fas fa-box-open"></i></div>
                <div class="benefit-text">
                    <h4>Instant Stock Liquidation</h4>
                    <p>Sell us your second-hand inventory instantly. We offer competitive B2B rates and immediate payouts.</p>
                </div>
            </div>
            
            <div class="benefit-item">
                <div class="benefit-icon"><i class="fas fa-tags"></i></div>
                <div class="benefit-text">
                    <h4>Bulk Buying Discounts</h4>
                    <p>Get exclusive access to our wholesale inventory with special margin structures designed for retailers.</p>
                </div>
            </div>

            <div class="benefit-item">
                <div class="benefit-icon"><i class="fas fa-chart-line"></i></div>
                <div class="benefit-text">
                    <h4>Dedicated Dashboard</h4>
                    <p>Manage your bulk orders, track payouts, and raise service tickets easily through our dedicated dealer portal.</p>
                </div>
            </div>
        </div>

        <div class="dealer-form-card">
            <h3>Dealer Registration</h3>
            
            @if(session('success'))
                <div style="color: #10b981; background: rgba(16,185,129,0.1); padding: 12px; border-radius: 8px; margin-bottom: 20px; border: 1px solid rgba(16,185,129,0.2);">
                    <i class="fas fa-check-circle" style="margin-right: 8px;"></i> {{ session('success') }}
                </div>
            @endif

            <form action="" method="POST">
                @csrf
                
                <div class="form-group">
                    <label>Shop / Business Name *</label>
                    <input type="text" name="shop_name" class="form-control" placeholder="e.g., Mobile Tech Solutions" required>
                </div>

                <div class="form-row">
                    <div class="form-group">
                        <label>Owner Name *</label>
                        <input type="text" name="owner_name" class="form-control" required>
                    </div>
                    <div class="form-group">
                        <label>Phone Number *</label>
                        <input type="tel" name="phone" class="form-control" required>
                    </div>
                </div>

                <div class="form-row">
                    <div class="form-group">
                        <label>Business Email *</label>
                        <input type="email" name="email" class="form-control" required>
                    </div>
                    <div class="form-group">
                        <label>GSTIN Number (Optional)</label>
                        <input type="text" name="gst_number" class="form-control" placeholder="For B2B billing">
                    </div>
                </div>

                <div class="form-group">
                    <label>Primary Interest *</label>
                    <select name="partnership_type" class="form-control" required>
                        <option value="">Select an option...</option>
                        <option value="selling">Selling inventory to RevoDevice</option>
                        <option value="buying">Buying bulk inventory from RevoDevice</option>
                        <option value="repair">Becoming a Repair & Service Partner</option>
                        <option value="both">Both Buying & Selling</option>
                    </select>
                </div>

                <div class="form-group">
                    <label>City & Pincode *</label>
                    <input type="text" name="location" class="form-control" placeholder="e.g., Chennai, 600001" required>
                </div>

                <button type="submit" class="submit-btn">Apply for Partnership <i class="fas fa-arrow-right" style="margin-left: 8px;"></i></button>
                <p style="text-align: center; color: #64748b; font-size: 12px; margin-top: 15px;">Our team will verify your details and contact you within 24 hours.</p>
            </form>
        </div>

    </div>
</div>
@endsection