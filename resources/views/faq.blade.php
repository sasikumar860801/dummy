@extends('layouts.app')

@section('title', 'Frequently Asked Questions - RevoDevice')

@push('styles')
<style>
    .static-container { 
        max-width: 900px; 
        margin: 40px auto; 
        padding: 0 20px; 
    }
    .page-header { 
        text-align: center; 
        margin-bottom: 50px; 
    }
    .page-title { 
        color: #ffffff; 
        font-size: 32px; 
        font-weight: 700; 
        margin-bottom: 15px; 
    }
    .page-subtitle { 
        color: #94a3b8; 
        font-size: 16px; 
    }

    /* Category Headers */
    .faq-category {
        color: #3b82f6;
        font-size: 20px;
        font-weight: 600;
        margin: 40px 0 20px 0;
        border-bottom: 1px solid #1e1e2a;
        padding-bottom: 10px;
    }

    /* Accordion Styles */
    .faq-item {
        background: #111118;
        border: 1px solid #1e1e2a;
        border-radius: 12px;
        margin-bottom: 15px;
        transition: all 0.3s ease;
        overflow: hidden;
    }
    .faq-item:hover {
        border-color: #2a2a3a;
        box-shadow: 0 4px 12px rgba(0,0,0,0.1);
    }
    
    .faq-question {
        padding: 22px 25px;
        display: flex;
        justify-content: space-between;
        align-items: center;
        cursor: pointer;
        color: #e2e8f0;
        font-weight: 600;
        font-size: 16px;
        user-select: none;
        transition: color 0.3s ease;
    }
    
    .faq-icon {
        color: #64748b;
        font-size: 14px;
        transition: transform 0.3s ease, color 0.3s ease;
    }

    .faq-answer {
        max-height: 0;
        padding: 0 25px;
        color: #94a3b8;
        font-size: 15px;
        line-height: 1.7;
        opacity: 0;
        transition: all 0.3s ease;
    }

    /* Active State */
    .faq-item.active {
        border-color: #3b82f6;
        background: #151520;
    }
    .faq-item.active .faq-question {
        color: #3b82f6;
    }
    .faq-item.active .faq-icon {
        transform: rotate(180deg);
        color: #3b82f6;
    }
    .faq-item.active .faq-answer {
        max-height: 500px; /* High enough to fit content */
        padding: 0 25px 25px 25px;
        opacity: 1;
    }

    /* Mobile Responsive Adjustments */
    @media (max-width: 768px) {
        .static-container { margin: 20px auto; }
        .page-title { font-size: 26px; }
        .faq-category { font-size: 18px; margin: 30px 0 15px 0; }
        .faq-question { padding: 18px 20px; font-size: 15px; gap: 15px; }
        .faq-answer { font-size: 14px; }
        .faq-item.active .faq-answer { padding: 0 20px 20px 20px; }
    }
</style>
@endpush

@section('content')
<div class="static-container">
    <div class="page-header">
        <h1 class="page-title">Frequently Asked Questions</h1>
        <p class="page-subtitle">Find answers to common questions about buying, selling, and repairs.</p>
    </div>

    <h2 class="faq-category"><i class="fas fa-shopping-bag" style="margin-right: 10px;"></i> Buying Devices</h2>
    
    <div class="faq-item">
        <div class="faq-question">
            <span>Are the refurbished devices tested?</span>
            <i class="fas fa-chevron-down faq-icon"></i>
        </div>
        <div class="faq-answer">
            Yes. Every device sold on RevoDevice goes through a rigorous multi-point hardware and software inspection to ensure it functions perfectly before it is listed on our platform.
        </div>
    </div>

    <div class="faq-item">
        <div class="faq-question">
            <span>Do purchased devices come with a warranty?</span>
            <i class="fas fa-chevron-down faq-icon"></i>
        </div>
        <div class="faq-answer">
            Absolutely. We offer a standard warranty on all devices purchased through our platform. The specific duration (e.g., 30, 60, or 90 days) will be clearly indicated on the product page.
        </div>
    </div>

    <h2 class="faq-category"><i class="fas fa-hand-holding-usd" style="margin-right: 10px;"></i> Selling Your Device</h2>

    <div class="faq-item">
        <div class="faq-question">
            <span>How do I get paid for my old device?</span>
            <i class="fas fa-chevron-down faq-icon"></i>
        </div>
        <div class="faq-answer">
            Once we receive and inspect your device to verify its condition, your payment is processed immediately. You can choose to be paid via bank transfer or preferred UPI methods.
        </div>
    </div>

    <div class="faq-item">
        <div class="faq-question">
            <span>Do I need to wipe my data before selling?</span>
            <i class="fas fa-chevron-down faq-icon"></i>
        </div>
        <div class="faq-answer">
            We highly recommend signing out of your iCloud/Google accounts and performing a factory reset before handing it over. However, our technical team performs a certified secure data wipe on every received device as a secondary precaution.
        </div>
    </div>

    <h2 class="faq-category"><i class="fas fa-tools" style="margin-right: 10px;"></i> Repair Services</h2>

    <div class="faq-item">
        <div class="faq-question">
            <span>How can I track my repair status?</span>
            <i class="fas fa-chevron-down faq-icon"></i>
        </div>
        <div class="faq-answer">
            You can track your repair in real-time by logging into your account and navigating to the "Repair & Services" section. It will show whether your device is pending, in progress, or completed.
        </div>
    </div>

    <div class="faq-item">
        <div class="faq-question">
            <span>Is my personal data safe during a repair?</span>
            <i class="fas fa-chevron-down faq-icon"></i>
        </div>
        <div class="faq-answer">
            Yes. Our technicians only access the necessary default apps (like the camera or microphone) required to test the hardware. Your photos, messages, and personal files remain strictly private.
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
    document.addEventListener('DOMContentLoaded', function() {
        const faqItems = document.querySelectorAll('.faq-item');

        faqItems.forEach(item => {
            const question = item.querySelector('.faq-question');
            
            question.addEventListener('click', () => {
                // Check if this item is currently active
                const isActive = item.classList.contains('active');
                
                // Optional: Close all other accordions when one opens
                faqItems.forEach(otherItem => {
                    otherItem.classList.remove('active');
                });

                // If it wasn't active, open it (toggle behavior)
                if (!isActive) {
                    item.classList.add('active');
                }
            });
        });
    });
</script>
@endpush