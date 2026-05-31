@extends('layouts.app')

@section('title', 'Your Cart - RevoDevice')

@section('content')
<div class="container" style="max-width: 1200px; margin: 0 auto; padding: 40px 20px;">
    
    <h1 style="color: white; font-size: 32px; margin-bottom: 30px;">Your Cart</h1>
    
    @if($cartData && $model)
    <div style="background: #111118; border: 1px solid #2a2a3a; border-radius: 20px; padding: 30px;">
        <div style="display: flex; flex-wrap: wrap; gap: 30px; align-items: center;">
            <!-- Model Image -->
            <div style="flex: 0 0 150px; text-align: center;">
                @if($modelImg)
                    <img src="{{ $modelImg }}" alt="{{ $model->title }}" style="width: 120px; height: 120px; object-fit: contain;">
                @else
                    <i class="fas fa-mobile-alt" style="font-size: 80px; color: #3b82f6;"></i>
                @endif
            </div>
            
            <!-- Model Details -->
            <div style="flex: 2;">
                <h2 style="color: white; font-size: 22px; margin-bottom: 10px;">
                    {{ $brand->title ?? '' }} {{ $model->title }}
                </h2>
                <p style="color: #94a3b8; margin-bottom: 8px;">
                    <strong>Storage:</strong> {{ $cartData['capacity'] ?? 'N/A' }}
                </p>
                <p style="color: #94a3b8; margin-bottom: 8px;">
                    <strong>Order ID:</strong> {{ $cartData['order_id'] ?? 'N/A' }}
                </p>
            </div>
            
            <!-- Price -->
            <div style="text-align: right;">
                <p style="color: #94a3b8; font-size: 14px;">Final Price</p>
                <h3 style="color: #3b82f6; font-size: 32px; font-weight: 800;">
                    ₹{{ number_format($cartData['final_price'] ?? 0) }}
                </h3>
            </div>
        </div>
        
        <hr style="border-color: #2a2a3a; margin: 25px 0;">
        
        <!-- Buttons -->
        <div style="display: flex; gap: 20px; justify-content: center; flex-wrap: wrap;">
            <button id="viewSummaryBtn" style="background: transparent; border: 1px solid #3b82f6; color: #3b82f6; padding: 12px 30px; border-radius: 50px; font-weight: 600; cursor: pointer;">
                View Summary <i class="fas fa-eye"></i>
            </button>
            <button id="reevaluateBtn" style="background: transparent; border: 1px solid #f59e0b; color: #f59e0b; padding: 12px 30px; border-radius: 50px; font-weight: 600; cursor: pointer;">
                Reevaluate <i class="fas fa-sync-alt"></i>
            </button>
            <button id="sellNowBtn" style="background: linear-gradient(135deg, #3b82f6, #8b5cf6); color: white; padding: 12px 30px; border-radius: 50px; border: none; font-weight: 600; cursor: pointer;">
                Sell Now <i class="fas fa-arrow-right"></i>
            </button>
        </div>
    </div>
    @else
    <div style="text-align: center; padding: 60px; background: #111118; border-radius: 20px;">
        <i class="fas fa-shopping-cart" style="font-size: 60px; color: #64748b; margin-bottom: 20px;"></i>
        <h3 style="color: white;">Your cart is empty</h3>
        <p style="color: #94a3b8;">Go back to sell your device</p>
        <a href="{{ url('/sell-old-phone') }}" style="display: inline-block; background: linear-gradient(135deg, #3b82f6, #8b5cf6); color: white; padding: 12px 30px; border-radius: 40px; text-decoration: none; margin-top: 20px;">
            Browse Devices
        </a>
    </div>
    @endif
</div>

<!-- View Summary Modal -->
<div id="summaryModal" class="modal" style="display: none; position: fixed; top: 0; left: 0; width: 100%; height: 100%; background: rgba(0,0,0,0.9); z-index: 100000; justify-content: center; align-items: center;">
    <div style="background: #111118; border-radius: 24px; max-width: 650px; width: 90%; max-height: 85vh; overflow-y: auto; border: 1px solid #2a2a3a; position: relative; box-shadow: 0 25px 50px -12px rgba(0,0,0,0.5);">
        <div style="display: flex; justify-content: space-between; align-items: center; padding: 20px 25px; border-bottom: 1px solid #2a2a3a; position: sticky; top: 0; background: #111118; z-index: 10;">
            <h3 style="color: white; font-size: 20px; margin: 0;">Order #<span id="summaryOrderId"></span> - Condition Summary</h3>
            <button class="closeModal" style="background: none; border: none; color: #94a3b8; font-size: 28px; cursor: pointer; line-height: 1;">&times;</button>
        </div>
        <div id="summaryContent" style="padding: 25px;"></div>
    </div>
</div>

<!-- Sell Now Modal -->
<div id="sellModal" class="modal" style="display: none; position: fixed; top: 0; left: 0; width: 100%; height: 100%; background: rgba(0,0,0,0.9); z-index: 100000; justify-content: center; align-items: center; overflow-y: auto;">
    <div style="background: #111118; border-radius: 24px; max-width: 600px; width: 90%; max-height: 90vh; overflow-y: auto; border: 1px solid #2a2a3a; position: relative; box-shadow: 0 25px 50px -12px rgba(0,0,0,0.5); margin: 20px;">
        <div style="display: flex; justify-content: space-between; align-items: center; padding: 20px 25px; border-bottom: 1px solid #2a2a3a; position: sticky; top: 0; background: #111118; z-index: 10;">
            <h3 style="color: white; font-size: 20px; margin: 0;">Complete Your Sale</h3>
            <button class="closeModal" style="background: none; border: none; color: #94a3b8; font-size: 28px; cursor: pointer; line-height: 1;">&times;</button>
        </div>
        <div style="padding: 25px;">
            <form id="sellForm">
                <input type="hidden" id="partial_order_item_id" value="{{ $cartData['id'] ?? '' }}">
                <input type="hidden" id="model_id" value="{{ $model->id ?? '' }}">
                <input type="hidden" id="order_id" value="{{ $cartData['order_id'] ?? '' }}">
                
                <div style="margin-bottom: 18px;">
                    <label style="color: white; display: block; margin-bottom: 6px; font-weight: 500;">Full Name *</label>
                    <input type="text" id="name" class="form-input" style="width: 100%; padding: 12px; background: #1a1a2e; border: 1px solid #2a2a3a; border-radius: 10px; color: white; font-size: 14px;">
                </div>
                
                <div style="margin-bottom: 18px;">
                    <label style="color: white; display: block; margin-bottom: 6px; font-weight: 500;">Mobile Number * (Primary - Not Editable)</label>
                    <input type="tel" id="mobile_no" class="form-input" style="width: 100%; padding: 12px; background: #2a2a3a; border: 1px solid #2a2a3a; border-radius: 10px; color: #94a3b8; font-size: 14px;" readonly>
                </div>
                
                <div style="margin-bottom: 18px;">
                    <label style="color: white; display: block; margin-bottom: 6px; font-weight: 500;">Alternate Mobile Number</label>
                    <input type="tel" id="alternate_mob_no" class="form-input" style="width: 100%; padding: 12px; background: #1a1a2e; border: 1px solid #2a2a3a; border-radius: 10px; color: white; font-size: 14px;">
                </div>
                
                <div style="margin-bottom: 18px;">
                    <label style="color: white; display: block; margin-bottom: 6px; font-weight: 500;">Email *</label>
                    <input type="email" id="email" class="form-input" style="width: 100%; padding: 12px; background: #1a1a2e; border: 1px solid #2a2a3a; border-radius: 10px; color: white; font-size: 14px;">
                </div>
                
                <div style="margin-bottom: 18px;">
                    <label style="color: white; display: block; margin-bottom: 6px; font-weight: 500;">Address Type *</label>
                    <select id="address_type" class="form-input" style="width: 100%; padding: 12px; background: #1a1a2e; border: 1px solid #2a2a3a; border-radius: 10px; color: white;">
                        <option value="home">Home</option>
                        <option value="office">Office</option>
                        <option value="other">Other</option>
                    </select>
                </div>
                
                <div style="margin-bottom: 18px;">
                    <label style="color: white; display: block; margin-bottom: 6px; font-weight: 500;">Address *</label>
                    <textarea id="address" rows="2" class="form-input" style="width: 100%; padding: 12px; background: #1a1a2e; border: 1px solid #2a2a3a; border-radius: 10px; color: white; font-size: 14px; resize: vertical;"></textarea>
                </div>
                
                <div style="margin-bottom: 18px;">
                    <label style="color: white; display: block; margin-bottom: 6px; font-weight: 500;">Landmark</label>
                    <input type="text" id="landmark" class="form-input" style="width: 100%; padding: 12px; background: #1a1a2e; border: 1px solid #2a2a3a; border-radius: 10px; color: white;">
                </div>
                
                <div style="margin-bottom: 18px;">
                    <label style="color: white; display: block; margin-bottom: 6px; font-weight: 500;">State *</label>
                    <input type="text" id="state" class="form-input" style="width: 100%; padding: 12px; background: #1a1a2e; border: 1px solid #2a2a3a; border-radius: 10px; color: white;">
                </div>
                
                <div style="margin-bottom: 18px;">
                    <label style="color: white; display: block; margin-bottom: 6px; font-weight: 500;">Pincode *</label>
                    <input type="text" id="pincode" class="form-input" style="width: 100%; padding: 12px; background: #1a1a2e; border: 1px solid #2a2a3a; border-radius: 10px; color: white;">
                </div>
                
                <div style="margin-bottom: 18px;">
                    <label style="color: white; display: block; margin-bottom: 6px; font-weight: 500;">Pickup Date *</label>
                    <input type="date" id="pickup_date" class="form-input" style="width: 100%; padding: 12px; background: #1a1a2e; border: 1px solid #2a2a3a; border-radius: 10px; color: white;">
                </div>
                
                <div style="margin-bottom: 18px;">
                    <label style="color: white; display: block; margin-bottom: 6px; font-weight: 500;">Pickup Time Slot *</label>
                    <select id="pickup_time" class="form-input" style="width: 100%; padding: 12px; background: #1a1a2e; border: 1px solid #2a2a3a; border-radius: 10px; color: white;">
                        <option value="7AM-1PM">7 AM - 1 PM</option>
                        <option value="1PM-5PM">1 PM - 5 PM</option>
                        <option value="5PM-10PM">5 PM - 10 PM</option>
                    </select>
                </div>
                
                <div style="margin-bottom: 18px;">
                    <label style="color: white; display: block; margin-bottom: 6px; font-weight: 500;">Payment Method *</label>
                    <select id="payment_method" class="form-input" style="width: 100%; padding: 12px; background: #1a1a2e; border: 1px solid #2a2a3a; border-radius: 10px; color: white;">
                        <option value="cash">Cash on Pickup</option>
                        <option value="upi">UPI Transfer</option>
                        <option value="bank">Bank Transfer</option>
                    </select>
                </div>
                
                <div style="margin-top: 25px;">
                    <button type="submit" id="submitSellBtn" style="width: 100%; background: linear-gradient(135deg, #10b981, #059669); color: white; padding: 14px; border-radius: 50px; border: none; font-weight: 600; cursor: pointer; font-size: 16px;">
                        Submit Sale <i class="fas fa-check-circle"></i>
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

<style>
    .modal {
        animation: fadeIn 0.3s ease;
    }
    @keyframes fadeIn {
        from { opacity: 0; }
        to { opacity: 1; }
    }
    .form-input:focus {
        outline: none;
        border-color: #3b82f6;
    }
    .summary-section {
        margin-bottom: 25px;
    }
    .summary-section h4 {
        color: #3b82f6;
        margin-bottom: 12px;
        font-size: 16px;
        font-weight: 600;
        border-left: 3px solid #3b82f6;
        padding-left: 12px;
    }
    .summary-item {
        padding: 8px 0;
        border-bottom: 1px solid #2a2a3a;
        color: #94a3b8;
        font-size: 14px;
        display: flex;
        align-items: center;
        gap: 10px;
    }
    .summary-item.yes {
        color: #10b981;
    }
    .summary-item.no {
        color: #ef4444;
    }
    .summary-icon {
        width: 20px;
        font-size: 14px;
    }
    .issue-tag {
        display: inline-block;
        background: #ef4444;
        color: white;
        padding: 5px 12px;
        border-radius: 20px;
        font-size: 12px;
        margin: 4px;
    }
    .issue-tag.green {
        background: #10b981;
    }
    .price-row {
        display: flex;
        justify-content: space-between;
        padding: 8px 0;
        border-bottom: 1px solid #2a2a3a;
    }
    .price-row.total {
        border-top: 2px solid #3b82f6;
        border-bottom: none;
        margin-top: 10px;
        padding-top: 12px;
        font-weight: bold;
    }
</style>
@endsection

@push('scripts')
<script src="https://code.jquery.com/jquery-3.7.1.min.js" integrity="sha256-/JqT3SQfawRcv/BIHPThkBvs0OEvtFFmqPF/lYI/Cxo=" crossorigin="anonymous"></script>
<script>
$(document).ready(function() {
    const csrfToken = $('meta[name="csrf-token"]').attr('content');
    const orderId = '{{ $cartData['order_id'] ?? '' }}';
    const partialOrderItemId = '{{ $cartData['id'] ?? '' }}';
    const modelId = '{{ $model->id ?? '' }}';
    const modelSlug = '{{ $model->sef_url ?? '' }}';
    
    // Get the evaluation data directly from PHP (already decoded)
    const evaluationData = @json($cartData['item_name'] ?? []);
    console.log('Evaluation Data:', evaluationData);
    
    // View Summary Button
    $('#viewSummaryBtn').on('click', function() {
        $('#summaryModal').show();
        
        if (evaluationData && Object.keys(evaluationData).length > 0) {
            displaySummaryModal(evaluationData, orderId);
        } else {
            $('#summaryContent').html('<div style="text-align: center; padding: 40px;"><p style="color: #ef4444;">No evaluation data found</p></div>');
        }
    });
    
    // Display summary modal
    function displaySummaryModal(data, orderId) {
        $('#summaryOrderId').text(orderId);
        
        let qaHtml = '<div class="summary-section"><h4>📋 Condition Assessment</h4>';
        
        // Process QA details
        if (data.qa_details && data.qa_details.length > 0) {
            data.qa_details.forEach(function(qa) {
                const statusClass = qa.processed_answer === 'yes' ? 'yes' : 'no';
                const statusIcon = qa.processed_answer === 'yes' ? '✅' : '❌';
                const answerText = qa.processed_answer === 'yes' ? 'Yes' : 'No';
                qaHtml += '<div class="summary-item ' + statusClass + '">';
                qaHtml += '<span class="summary-icon">' + statusIcon + '</span>';
                qaHtml += '<span><strong>' + answerText + '</strong> - ' + qa.question_name + '</span>';
                qaHtml += '</div>';
            });
        } else {
            qaHtml += '<div class="summary-item">No condition data available</div>';
        }
        qaHtml += '</div>';
        
        // Process issues from selected attributes
        let issues = [];
        let goodConditions = [];
        
        if (data.yes_question_details && data.yes_question_details.length > 0) {
            data.yes_question_details.forEach(function(item) {
                if (item.label) {
                    if (Array.isArray(item.label)) {
                        item.label.forEach(function(label) {
                            if (label.toLowerCase().includes('no') || label.toLowerCase().includes('good') || label.toLowerCase().includes('working')) {
                                goodConditions.push(label);
                            } else {
                                issues.push(label);
                            }
                        });
                    } else {
                        if (item.label.toLowerCase().includes('no') || item.label.toLowerCase().includes('good') || item.label.toLowerCase().includes('working')) {
                            goodConditions.push(item.label);
                        } else {
                            issues.push(item.label);
                        }
                    }
                }
            });
        }
        
        let issuesHtml = '';
        if (issues.length > 0) {
            issuesHtml = '<div class="summary-section"><h4>⚠️ Reported Issues / Conditions</h4><div>';
            issues.forEach(function(issue) {
                issuesHtml += '<span class="issue-tag">' + issue + '</span> ';
            });
            issuesHtml += '</div></div>';
        }
        
        let goodHtml = '';
        if (goodConditions.length > 0) {
            goodHtml = '<div class="summary-section"><h4>✅ Good Conditions</h4><div>';
            goodConditions.forEach(function(condition) {
                goodHtml += '<span class="issue-tag green">' + condition + '</span> ';
            });
            goodHtml += '</div></div>';
        }
        
        // Price Details
        const finalPrice = data.final_price || '{{ $cartData['final_price'] ?? 0 }}';
        const basePrice = data.base_price || 0;
        const deductedAmount = data.deducted_amount_two || 0;
        
        const priceHtml = `
            <div class="summary-section">
                <h4>💰 Price Details</h4>
                <div class="price-row"><span>Base Price</span><span>₹${formatNumber(basePrice)}</span></div>
                <div class="price-row"><span>Deductions</span><span>- ₹${formatNumber(Math.abs(deductedAmount))}</span></div>
                <div class="price-row total"><span>Final Price</span><span style="color: #3b82f6; font-size: 18px;">₹${formatNumber(finalPrice)}</span></div>
            </div>
        `;
        
        $('#summaryContent').html(qaHtml + goodHtml + issuesHtml + priceHtml);
    }
    
    // Format number
    function formatNumber(num) {
        return parseFloat(num).toLocaleString('en-IN');
    }
    
    // Reevaluate Button
    $('#reevaluateBtn').on('click', function() {
        if (modelSlug) {
            window.location.href = '/sell-old-mobile-phone/used-' + modelSlug;
        } else {
            alert('Unable to reevaluate. Model not found.');
        }
    });
    
    // Sell Now Button
    $('#sellNowBtn').on('click', function() {
        fetchUserDetails();
    });
    
    // Close modals
    $('.closeModal').on('click', function() {
        $('.modal').hide();
    });
    
    $(window).on('click', function(e) {
        if ($(e.target).hasClass('modal')) {
            $('.modal').hide();
        }
    });
    
    // Fetch user details for sell modal
    function fetchUserDetails() {
        $.ajax({
            url: '/get_user_details',
            method: 'GET',
            headers: { 'X-CSRF-TOKEN': csrfToken },
            success: function(response) {
                if (response.success) {
                    const user = response.data;
                    $('#name').val(user.name || '');
                    $('#mobile_no').val(user.phone || '');
                    $('#email').val(user.email || '');
                    $('#alternate_mob_no').val(user.other_phone || '');
                    
                    if (user.address) {
                        $('#address_type').val(user.address.address_type || 'home');
                        $('#address').val(user.address.address || '');
                        $('#landmark').val(user.address.landmark || '');
                        $('#state').val(user.address.state || '');
                        $('#pincode').val(user.address.pincode || '');
                    }
                    
                    // Set min date for pickup (today + 2 days)
                    const today = new Date();
                    const minDate = new Date();
                    minDate.setDate(today.getDate() + 2);
                    const minDateStr = minDate.toISOString().split('T')[0];
                    $('#pickup_date').attr('min', minDateStr);
                    
                    $('#sellModal').show();
                } else {
                    alert('Error fetching user details: ' + (response.message || 'Unknown error'));
                }
            },
            error: function(xhr, status, error) {
                console.error('AJAX Error:', error);
                alert('Failed to fetch user details. Please try again.');
            }
        });
    }
    
    // Sell Form Submit
    $('#sellForm').on('submit', function(e) {
        e.preventDefault();
        
        const formData = {
            partial_order_item_id: $('#partial_order_item_id').val(),
            model_id: $('#model_id').val(),
            order_id: $('#order_id').val(),
            name: $('#name').val(),
            mobile_no: $('#mobile_no').val(),
            email: $('#email').val(),
            alternate_mob_no: $('#alternate_mob_no').val(),
            address_type: $('#address_type').val(),
            state: $('#state').val(),
            pincode: $('#pincode').val(),
            address: $('#address').val(),
            landmark: $('#landmark').val(),
            pickup_date: $('#pickup_date').val(),
            pickup_time: $('#pickup_time').val(),
            payment_method: $('#payment_method').val()
        };
        
        // Validation
        if (!formData.name || !formData.mobile_no || !formData.email || !formData.address || !formData.state || !formData.pincode || !formData.pickup_date || !formData.pickup_time) {
            alert('Please fill all required fields');
            return;
        }
        
        $('#submitSellBtn').prop('disabled', true);
        $('#submitSellBtn').html('<i class="fas fa-spinner fa-spin"></i> Submitting...');
        
        $.ajax({
            url: '/submit_sell_order',
            method: 'POST',
            headers: { 'X-CSRF-TOKEN': csrfToken },
            data: formData,
            success: function(response) {
                if (response.success) {
                    alert('Order placed successfully! Order ID: ' + response.order_id);
                    window.location.href = '/order-success/' + response.order_id;
                } else {
                    alert('Error: ' + response.message);
                    $('#submitSellBtn').prop('disabled', false);
                    $('#submitSellBtn').html('Submit Sale <i class="fas fa-check-circle"></i>');
                }
            },
            error: function(xhr) {
                const error = xhr.responseJSON;
                alert('Error: ' + (error?.message || 'Something went wrong'));
                $('#submitSellBtn').prop('disabled', false);
                $('#submitSellBtn').html('Submit Sale <i class="fas fa-check-circle"></i>');
            }
        });
    });
});
</script>
@endpush