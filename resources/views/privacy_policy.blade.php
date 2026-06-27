@extends('layouts.app')

@section('title', 'Privacy Policy - RevoDevice')

@push('styles')
<style>
    .legal-container { padding: 50px 0; max-width: 800px; margin: 0 auto; }
    .legal-header { margin-bottom: 40px; border-bottom: 1px solid #1e1e2a; padding-bottom: 20px; }
    .legal-header h1 { color: white; font-weight: 800; font-size: 32px; margin-bottom: 10px; }
    .legal-header p { color: #64748b; font-size: 14px; margin: 0; }
    
    .legal-content { background: #111118; border: 1px solid #1e1e2a; border-radius: 20px; padding: 40px; }
    .legal-content h2 { color: #e2e8f0; font-size: 20px; margin-top: 30px; margin-bottom: 15px; border-left: 3px solid #3b82f6; padding-left: 15px; }
    .legal-content h2:first-child { margin-top: 0; }
    .legal-content p { color: #cbd5e1; line-height: 1.8; margin-bottom: 20px; font-size: 15px; }
    .legal-content ul { color: #cbd5e1; line-height: 1.8; margin-bottom: 20px; padding-left: 20px; font-size: 15px; }
    .legal-content li { margin-bottom: 10px; }
</style>
@endpush

@section('content')
<div class="container legal-container">
    <div class="legal-header">
        <h1>Privacy Policy</h1>
        <p>Last Updated: {{ date('F d, Y') }}</p>
    </div>

    <div class="legal-content">
        <h2>1. Information We Collect</h2>
        <p>When you use RevoDevice to buy, sell, or repair devices, we collect information that you provide directly to us. This includes your name, email address, phone number, shipping address, and payment information. We also collect device specifics (IMEI, model, condition) when you submit a repair or sell request.</p>

        <h2>2. How We Use Your Information</h2>
        <p>We use the information we collect to:</p>
        <ul>
            <li>Process your transactions (buy, sell, or repair orders).</li>
            <li>Communicate with you regarding the status of your orders.</li>
            <li>Provide customer support and resolve disputes.</li>
            <li>Improve our platform, prevent fraud, and maintain security.</li>
        </ul>

        <h2>3. Information Sharing</h2>
        <p>RevoDevice does not sell your personal data to third parties. We may share necessary information with trusted service providers (such as shipping partners or payment gateways) strictly for the purpose of fulfilling your requests.</p>

        <h2>4. Data Security</h2>
        <p>We implement strict security measures to protect your personal information. However, no electronic transmission over the internet or information storage technology can be guaranteed to be 100% secure.</p>

        <h2>5. Your Rights</h2>
        <p>You have the right to access, update, or delete your personal information stored on our platform. You can manage this directly from your Account Dashboard or by contacting our support team.</p>
    </div>
</div>
@endsection