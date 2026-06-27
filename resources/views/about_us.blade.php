@extends('layouts.app')

@section('title', 'About Us - RevoDevice')

@push('styles')
<style>
    .static-container { max-width: 1000px; margin: 40px auto; padding: 0 20px; }
    .page-header { text-align: center; margin-bottom: 40px; }
    .page-title { color: #ffffff; font-size: 32px; font-weight: 700; margin-bottom: 15px; }
    .page-subtitle { color: #94a3b8; font-size: 16px; max-width: 600px; margin: 0 auto; }
    
    .content-card { background: #111118; border: 1px solid #1e1e2a; border-radius: 20px; padding: 40px; margin-bottom: 30px; }
    .content-card h2 { color: #e2e8f0; font-size: 22px; margin-bottom: 15px; border-bottom: 1px solid #1e1e2a; padding-bottom: 10px; }
    .content-card p { color: #94a3b8; font-size: 15px; line-height: 1.7; margin-bottom: 20px; }
    
    .features-grid { display: grid; grid-template-columns: repeat(auto-fit, minmax(250px, 1fr)); gap: 20px; margin-top: 30px; }
    .feature-box { background: #1a1a2e; padding: 25px; border-radius: 12px; text-align: center; border: 1px solid #2a2a3a; }
    .feature-box i { font-size: 30px; color: #3b82f6; margin-bottom: 15px; }
    .feature-box h3 { color: #e2e8f0; font-size: 18px; margin-bottom: 10px; }
    .feature-box p { color: #64748b; font-size: 14px; margin: 0; }
</style>
@endpush

@section('content')
<div class="static-container">
    <div class="page-header">
        <h1 class="page-title">About RevoDevice</h1>
        <p class="page-subtitle">Your trusted partner for buying, selling, and repairing premium smart devices.</p>
    </div>

    <div class="content-card">
        <h2>Our Mission</h2>
        <p>At RevoDevice, we believe that technology should be accessible, sustainable, and reliable. We bridge the gap in the electronics market by providing a secure platform where everyday users can buy certified refurbished devices, sell their old gadgets for a fair price, and get professional repair services.</p>
        
        <h2>Why Choose Us?</h2>
        <p>We take the uncertainty out of the second-hand market. Every device sold on our platform goes through strict quality checks. When you hand your phone to us for repair, we treat your data with the utmost privacy and use only high-quality replacement parts.</p>
        
        <div class="features-grid">
            <div class="feature-box">
                <i class="fas fa-shield-alt"></i>
                <h3>Verified Devices</h3>
                <p>Every device is thoroughly tested to ensure you get exactly what you pay for.</p>
            </div>
            <div class="feature-box">
                <i class="fas fa-tools"></i>
                <h3>Expert Repairs</h3>
                <p>From screen replacements to complex motherboard issues, our technicians handle it all.</p>
            </div>
            <div class="feature-box">
                <i class="fas fa-handshake"></i>
                <h3>Fair Pricing</h3>
                <p>No hidden fees. Just transparent, competitive pricing for buying, selling, and repairs.</p>
            </div>
        </div>
    </div>
</div>
@endsection