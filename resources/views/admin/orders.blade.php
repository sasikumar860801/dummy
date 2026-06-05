@extends('admin.layout')

@section('title', 'Order Management Terminal')

@section('admin-content')
<div style="margin-bottom: 25px;">
    <h2 class="gradient-text" style="font-size: 28px; font-weight: 700; margin-bottom: 5px;">Order Fulfillment Control Desk</h2>
    <p style="color: #64748b; font-size: 14px;">Process inbound collection pipelines, inspect user evaluations, and convert assets to storage tracks.</p>
</div>

<div style="display: flex; gap: 10px; border-bottom: 1px solid #1e1e2a; padding-bottom: 1px; margin-bottom: 30px; flex-wrap: wrap;">
    <button class="tab-trigger-btn active" onclick="switchContextTab(event, 'new_orders')">
        New Orders <span class="tab-badge-count" style="background: #3b82f6;">{{ $counts['pending'] }}</span>
    </button>
    <button class="tab-trigger-btn" onclick="switchContextTab(event, 'rejected_orders')">
        Rejected <span class="tab-badge-count" style="background: #ef4444;">{{ $counts['reject'] }}</span>
    </button>
    <button class="tab-trigger-btn" onclick="switchContextTab(event, 'cancelled_orders')">
        Cancelled <span class="tab-badge-count" style="background: #64748b;">{{ $counts['cancelled'] }}</span>
    </button>
    <button class="tab-trigger-btn" onclick="switchContextTab(event, 'completed_orders')">
        Completed <span class="tab-badge-count" style="background: #10b981;">{{ $counts['completed'] }}</span>
    </button>
</div>

<div id="new_orders" class="tab-view-panel active">
    @include('admin.partials.order_cards_loop', ['collection' => $orders->get('pending', collect([])), 'tab' => 'new'])
</div>

<div id="rejected_orders" class="tab-view-panel">
    @include('admin.partials.order_cards_loop', ['collection' => $orders->get('reject', collect([])), 'tab' => 'rejected'])
</div>

<div id="cancelled_orders" class="tab-view-panel">
    @include('admin.partials.order_cards_loop', ['collection' => $orders->get('cancelled', collect([])), 'tab' => 'cancelled'])
</div>

<div id="completed_orders" class="tab-view-panel">
    @include('admin.partials.order_cards_loop', ['collection' => $orders->get('completed', collect([])), 'tab' => 'completed'])
</div>

<div id="summaryModal" class="dashboard-popup-scaffolding">
    <div class="popup-modal-box" style="max-width: 600px;">
        <div style="display: flex; justify-content: space-between; align-items: center; padding: 20px 25px; border-bottom: 1px solid #1e1e2a; position: sticky; top: 0; background: #111118; z-index: 10;">
            <h3 style="color: white; font-size: 17px; font-weight: 600; margin: 0;">Order #<span id="summaryOrderId" style="color: #ec4899;"></span> - Condition Summary</h3>
            <button onclick="closeModalWindow('summaryModal')" style="background: none; border: none; color: #94a3b8; font-size: 26px; cursor: pointer;">&times;</button>
        </div>
        <div id="summaryContent" style="padding: 25px; max-height: 75vh; overflow-y: auto;">
            </div>
    </div>
</div>

<div id="stockProvisionModal" class="dashboard-popup-scaffolding">
    <div class="popup-modal-box" style="max-width: 500px;">
        <div class="popup-modal-header">
            <h4 style="color: white; font-size: 18px; font-weight: 600;"><i class="fas fa-boxes" style="color: #10b981; margin-right: 8px;"></i> Ingest Device Into Storage Vaults</h4>
            <button onclick="closeModalWindow('stockProvisionModal')" style="background: none; border: none; color: #94a3b8; font-size: 24px; cursor: pointer;">&times;</button>
        </div>
        <form id="stockIngestForm">
            @csrf
            <div class="popup-modal-body">
                <div class="input-form-row">
                    <label>Order Reference Tracking ID</label>
                    <input type="text" id="stock_order_id" name="order_id" readonly style="background: #111118; color: #64748b; cursor: not-allowed;">
                </div>
                <div class="input-form-row">
                    <label>Assessed Cost Price ($)</label>
                    <input type="number" step="0.01" id="stock_buy_price" name="buy_price" required>
                </div>
                <div class="input-form-row">
                    <label>Device Colorway Spec</label>
                    <input type="text" id="stock_color" name="color" placeholder="e.g. Cosmic Black, Cosmic White" required>
                </div>
                <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 15px;">
                    <div class="input-form-row">
                        <label>Primary IMEI (Slot 1)</label>
                        <input type="text" id="stock_imei_1" name="imei_no_1" placeholder="15 Digit Array" required>
                    </div>
                    <div class="input-form-row">
                        <label>Secondary IMEI (Slot 2)</label>
                        <input type="text" id="stock_imei_2" name="imei_no_2" placeholder="Optional Field">
                    </div>
                </div>
                <div class="input-form-row">
                    <label>Warranty Terms</label>
                    <select id="stock_warranty" name="warranty" required>
                        <option value="no warranty">No Warranty Cover Framework</option>
                        <option value="3 month">3 Months Coverage Warranty</option>
                        <option value="6 month">6 Months Coverage Warranty</option>
                        <option value="1 year">1 Full Calendar Year Warranty</option>
                    </select>
                </div>
                <input type="hidden" id="stock_model_id" name="model_id">
                <input type="hidden" id="stock_capacity" name="capacity">
            </div>
            <div class="popup-modal-footer">
                <button type="button" class="action-control-btn" style="background: #1a1a2e; color: white;" onclick="closeModalWindow('stockProvisionModal')">Abort</button>
                <button type="submit" class="action-control-btn" style="background: linear-gradient(135deg, #10b981, #059669); color: white;">Confirm Log Entry</button>
            </div>
        </form>
    </div>
</div>

<style>
    /* Content Layout Scaffolding Sheets */
    .tab-trigger-btn { background: none; border: none; color: #64748b; padding: 12px 24px; font-size: 15px; font-weight: 600; cursor: pointer; transition: 0.2s; display: flex; align-items: center; gap: 8px; border-bottom: 2px solid transparent; }
    .tab-trigger-btn:hover { color: #cbd5e1; }
    .tab-trigger-btn.active { color: #ec4899; border-color: #ec4899; }
    .tab-badge-count { font-size: 11px; font-weight: 700; color: white; padding: 2px 8px; border-radius: 20px; }
    
    .tab-view-panel { display: none; }
    .tab-view-panel.active { display: block; }

    /* Core Element Cards CSS Architecture */
    .order-item-record-card { background: #111118; border: 1px solid #1e1e2a; border-radius: 20px; padding: 24px; margin-bottom: 20px; display: grid; grid-template-columns: 1.5fr 2fr 1fr; gap: 30px; }
    @media(max-width: 991px) { .order-item-record-card { grid-template-columns: 1fr; gap: 20px; } }
    
    .sub-panel-column { border-right: 1px solid #1e1e2a; padding-right: 15px; }
    .sub-panel-column:last-child { border-right: none; padding-right: 0; }
    
    .profile-card-title { font-size: 12px; font-weight: 700; color: #64748b; text-transform: uppercase; letter-spacing: 0.5px; margin-bottom: 12px; display: flex; align-items: center; gap: 6px; }
    .meta-data-line { font-size: 14px; margin-bottom: 8px; color: #cbd5e1; }
    .meta-data-line span { color: #64748b; }

    /* Interactive Interface Buttons Styling Elements */
    .action-control-btn { padding: 10px 16px; border: none; border-radius: 10px; font-size: 13px; font-weight: 600; cursor: pointer; display: inline-flex; align-items: center; gap: 8px; transition: 0.2s; text-decoration: none; }
    .action-control-btn:hover { opacity: 0.9; transform: translateY(-1px); }
    .action-control-btn:disabled, .action-control-btn.disabled-btn { background: #1e1e2a !important; color: #475569 !important; cursor: not-allowed !important; transform: none !important; }

    /* Popup Modal Presentation Layers CSS Layouts */
    .dashboard-popup-scaffolding { position: fixed; top: 0; left: 0; width: 100vw; height: 100vh; background: rgba(5,5,8,0.85); backdrop-filter: blur(8px); z-index: 99999; display: none; justify-content: center; align-items: center; padding: 20px; }
    .popup-modal-box { background: #111118; border: 1px solid #2a2a3a; width: 100%; border-radius: 24px; overflow: hidden; display: flex; flex-direction: column; box-shadow: 0 25px 50px -12px rgba(0,0,0,0.5); }
    .popup-modal-header { padding: 20px 24px; border-bottom: 1px solid #1e1e2a; display: flex; align-items: center; justify-content: space-between; }
    .popup-modal-body { padding: 24px; max-height: 70vh; overflow-y: auto; }
    .popup-modal-footer { padding: 16px 24px; background: #0f0f15; border-top: 1px solid #1e1e2a; display: flex; justify-content: flex-end; gap: 12px; }

    /* Live Summary Engine Specific Custom Styling Elements */
    .summary-section { margin-bottom: 25px; }
    .summary-section h4 { color: #94a3b8; font-size: 13px; font-weight: 700; text-transform: uppercase; letter-spacing: 0.5px; margin-bottom: 12px; }
    .summary-item { background: #171725; border-radius: 10px; padding: 12px 16px; margin-bottom: 8px; display: flex; align-items: center; gap: 12px; font-size: 14px; border: 1px solid #222235; }
    .summary-item.yes { border-left: 4px solid #10b981; }
    .summary-item.no { border-left: 4px solid #ef4444; }
    .defect-tag { display: inline-block; background: rgba(239, 68, 68, 0.1); color: #f87171; border: 1px solid rgba(239, 68, 68, 0.2); padding: 6px 14px; border-radius: 20px; font-size: 13px; margin-right: 8px; margin-bottom: 8px; font-weight: 500; }

    .input-form-row { margin-bottom: 16px; }
    .input-form-row label { display: block; font-size: 13px; font-weight: 500; color: #94a3b8; margin-bottom: 6px; }
    .input-form-row input, .input-form-row select { width: 100%; padding: 12px; background: #1a1a2e; border: 1px solid #2a2a3a; border-radius: 10px; color: white; outline: none; font-size: 14px; }
</style>
@endsection

@push('admin-scripts')
<script>
    function switchContextTab(event, tabId) {
        $('.tab-view-panel').removeClass('active');
        $('.tab-trigger-btn').removeClass('active');
        $('#' + tabId).addClass('active');
        $(event.currentTarget).addClass('active');
    }

    function openModalWindow(modalId) { $('#' + modalId).css('display', 'flex'); }
    function closeModalWindow(modalId) { $('#' + modalId).hide(); }

    function escapeHtml(text) {
        return text ? text.replace(/&/g, "&amp;").replace(/</g, "&lt;").replace(/>/g, "&gt;").replace(/"/g, "&quot;").replace(/'/g, "&#039;") : '';
    }

    function renderSummaryError() {
        $('#summaryContent').html(`
            <div style="text-align: center; padding: 30px; color: #ef4444;">
                <i class="fas fa-exclamation-triangle" style="font-size: 32px; margin-bottom: 12px;"></i>
                <p style="font-size: 14px;">Failed to gather remote diagnostic data profile layers.</p>
            </div>
        `);
    }

    // Live AJAX API Engine Hook
    $('.view-summary-trigger').on('click', function() {
        const orderId = $(this).data('order-id');
        $('#summaryOrderId').text(orderId);
        openModalWindow('summaryModal');
        
        $('#summaryContent').html(`
            <div style="text-align: center; padding: 40px;">
                <i class="fas fa-spinner fa-spin" style="font-size: 32px; color: #ec4899;"></i>
                <p style="color: #64748b; margin-top: 12px; font-size: 14px;">Fetching device parameters dynamically...</p>
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
                                <span style="color: white;"><strong>${isYes ? 'Yes' : 'No'}</strong> - ${escapeHtml(cleanText)}</span>
                            </div>`;
                        });
                    } else {
                        html += '<p style="color:#64748b; font-size:14px; padding-left:5px;">No assessment questions answered.</p>';
                    }
                    html += '</div>';
                    
                    // Defects Layer
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

    function fireStatusMutationAction(orderId, nextState) {
        if(!confirm(`Are you sure you want to change this order status to ${nextState}?`)) return;

        $.ajax({
            url: "{{ route('admin.orders.updateStatus') }}",
            method: "POST",
            data: { _token: "{{ csrf_token() }}", order_id: orderId, status: nextState },
            success: function(response) {
                if(response.success) { alert(response.message); window.location.reload(); }
            },
            error: function() { alert("Transaction processing validation block error encountered."); }
        });
    }

    function configureStockModal(btn) {
        $('#stock_order_id').val($(btn).data('id'));
        $('#stock_model_id').val($(btn).data('model'));
        $('#stock_capacity').val($(btn).data('capacity'));
        $('#stock_buy_price').val($(btn).data('price'));
        
        $('#stock_color').val('');
        $('#stock_imei_1').val('');
        $('#stock_imei_2').val('');
        $('#stock_warranty').val('no warranty');
        openModalWindow('stockProvisionModal');
    }

    $('#stockIngestForm').on('submit', function(e) {
        e.preventDefault();
        $.ajax({
            url: "{{ route('admin.orders.moveToStock') }}",
            method: "POST",
            data: $(this).serialize(),
            success: function(response) {
                if(response.success) { alert(response.message); closeModalWindow('stockProvisionModal'); window.location.reload(); }
                else { alert(response.message); }
            },
            error: function(xhr) { alert(xhr.responseJSON?.message || "Storage provisioning runtime error."); }
        });
    });
</script>
@endpush