@extends('layouts.app')

@section('title', 'Contact Us - RevoDevice')

@push('styles')
<style>
    .page-container { padding: 50px 0; max-width: 1000px; margin: 0 auto; }
    .header-section { text-align: center; margin-bottom: 50px; }
    .header-section h1 { color: #e2e8f0; font-weight: 800; font-size: 32px; margin-bottom: 15px; }
    .header-section p { color: #94a3b8; font-size: 16px; max-width: 600px; margin: 0 auto; }
    
    .contact-grid { display: grid; grid-template-columns: 1fr 1.5fr; gap: 30px; }
    
    .info-card { background: #111118; border: 1px solid #1e1e2a; border-radius: 20px; padding: 30px; }
    .info-item { display: flex; align-items: flex-start; gap: 15px; margin-bottom: 25px; }
    .info-icon { width: 45px; height: 45px; background: rgba(59, 130, 246, 0.1); color: #3b82f6; border-radius: 12px; display: flex; align-items: center; justify-content: center; font-size: 20px; flex-shrink: 0; }
    .info-text h4 { color: #e2e8f0; font-size: 16px; margin: 0 0 5px 0; font-weight: 600; }
    .info-text p { color: #94a3b8; font-size: 14px; margin: 0; line-height: 1.5; }

    .form-card { background: #111118; border: 1px solid #1e1e2a; border-radius: 20px; padding: 30px; }
    .form-group { margin-bottom: 20px; }
    .form-group label { display: block; color: #cbd5e1; font-size: 14px; margin-bottom: 8px; font-weight: 500; }
    .form-control { width: 100%; background: #0a0a0f; border: 1px solid #2a2a3a; color: white; padding: 12px 15px; border-radius: 10px; transition: 0.3s; }
    .form-control:focus { border-color: #3b82f6; outline: none; box-shadow: 0 0 0 3px rgba(59,130,246,0.1); }
    textarea.form-control { min-height: 120px; resize: vertical; }
    .submit-btn { background: #3b82f6; color: white; border: none; padding: 12px 25px; border-radius: 10px; font-weight: 600; width: 100%; cursor: pointer; transition: 0.3s; }
    .submit-btn:hover { background: #2563eb; }

    @media (max-width: 768px) {
        .contact-grid { grid-template-columns: 1fr; }
    }
</style>
@endpush

@section('content')
<div class="container page-container">
    <div class="header-section">
        <h1>Get in Touch</h1>
        <p>Have questions about a repair, a device you want to buy, or looking to sell? We're here to help you out.</p>
    </div>

    <div class="contact-grid">
        <!-- Contact Information -->
        <div class="info-card">
            <h3 style="color: white; margin-top: 0; margin-bottom: 25px;">Contact Information</h3>
            
            <div class="info-item">
                <div class="info-icon"><i class="fas fa-map-marker-alt"></i></div>
                <div class="info-text">
                    <h4>Our Location</h4>
                    <p>RevoDevice HQ<br>123 Tech Street, Digital City<br>ZIP 12345</p>
                </div>
            </div>
            
            <div class="info-item">
                <div class="info-icon"><i class="fas fa-envelope"></i></div>
                <div class="info-text">
                    <h4>Email Us</h4>
                    <p>support@revodevice.com<br>sales@revodevice.com</p>
                </div>
            </div>
            
            <div class="info-item">
                <div class="info-icon"><i class="fas fa-phone-alt"></i></div>
                <div class="info-text">
                    <h4>Call Us</h4>
                    <p>+1 (555) 123-4567<br>Mon-Fri, 9am - 6pm</p>
                </div>
            </div>
        </div>

        <!-- Contact Form -->
        <div class="form-card">
            <h3 style="color: white; margin-top: 0; margin-bottom: 25px;">Send us a Message</h3>
            
            @if(session('success'))
                <div style="background: rgba(16, 185, 129, 0.1); color: #34d399; padding: 15px; border-radius: 8px; margin-bottom: 20px; border: 1px solid rgba(16, 185, 129, 0.2);">
                    <i class="fas fa-check-circle"></i> {{ session('success') }}
                </div>
            @endif

            <form action="#" method="POST">
                @csrf
                <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 15px;">
                    <div class="form-group">
                        <label>Your Name</label>
                        <input type="text" name="name" class="form-control" required placeholder="John Doe">
                    </div>
                    <div class="form-group">
                        <label>Your Email</label>
                        <input type="email" name="email" class="form-control" required placeholder="john@example.com">
                    </div>
                </div>
                
                <div class="form-group">
                    <label>Subject</label>
                    <select name="subject" class="form-control" required>
                        <option value="">Select an option...</option>
                        <option value="buy">Buying a Device</option>
                        <option value="sell">Selling a Device</option>
                        <option value="repair">Repair Service Status</option>
                        <option value="other">Other Inquiry</option>
                    </select>
                </div>
                
                <div class="form-group">
                    <label>Message</label>
                    <textarea name="message" class="form-control" required placeholder="How can we help you?"></textarea>
                </div>
                
                <button type="submit" class="submit-btn"><i class="fas fa-paper-plane" style="margin-right: 8px;"></i> Send Message</button>
            </form>
        </div>
    </div>
</div>
@endsection