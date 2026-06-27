@extends('layouts.app')

@section('title', 'Terms and Conditions - RevoDevice')

@push('styles')
<style>
    .legal-container { padding: 50px 0; max-width: 800px; margin: 0 auto; }
    .legal-header { margin-bottom: 40px; border-bottom: 1px solid #1e1e2a; padding-bottom: 20px; }
    .legal-header h1 { color: white; font-weight: 800; font-size: 32px; margin-bottom: 10px; }
    .legal-header p { color: #64748b; font-size: 14px; margin: 0; }
    
    .legal-content { background: #111118; border: 1px solid #1e1e2a; border-radius: 20px; padding: 40px; }
    .legal-content h2 { color: #e2e8f0; font-size: 20px; margin-top: 30px; margin-bottom: 15px; border-left: 3px solid #8b5cf6; padding-left: 15px; }
    .legal-content h2:first-child { margin-top: 0; }
    .legal-content p { color: #cbd5e1; line-height: 1.8; margin-bottom: 20px; font-size: 15px; }
</style>
@endpush

@section('content')
<div class="container legal-container">
    <div class="legal-header">
        <h1>Terms and Conditions</h1>
        <p>Last Updated: {{ date('F d, Y') }}</p>
    </div>

    <div class="legal-content">
        <h2>1. Acceptance of Terms</h2>
        <p>By accessing and using RevoDevice, you accept and agree to be bound by the terms and provision of this agreement. If you do not agree to abide by these terms, please do not use our services.</p>

        <h2>2. User Accounts</h2>
        <p>To use certain features of the platform (buying, selling, repairs), you must register for an account. You are responsible for maintaining the confidentiality of your account information and for all activities that occur under your account.</p>

        <h2>3. Buying Devices</h2>
        <p>All devices listed for sale undergo quality checks. However, availability and prices are subject to change without notice. We reserve the right to refuse or cancel any order for any reason, including limitations on quantities available for purchase.</p>

        <h2>4. Selling Devices</h2>
        <p>When you submit a device to sell, you represent that you are the lawful owner. Quotes provided online are estimates based on the condition you report. Final payment is subject to physical inspection. Devices reported lost or stolen will be rejected and reported to authorities.</p>

        <h2>5. Repair Services</h2>
        <p>By submitting a device for repair, you authorize RevoDevice technicians to perform the necessary services. We are not responsible for any data loss; it is the user's responsibility to back up data prior to sending the device for repair.</p>

        <h2>6. Warranties and Returns</h2>
        <p>Purchased devices and repair services come with a limited warranty as specified on the receipt. Physical damage or water damage occurring after purchase or repair voids this warranty.</p>
    </div>
</div>
@endsection