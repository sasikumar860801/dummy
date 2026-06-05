@extends('admin.layout')

@section('title', 'Global Stock Control Room')

@push('admin-styles')
<link href="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css" rel="stylesheet" />
<style>
    /* Direct UI Select2 Theme Overrides */
    .select2-container--default .select2-selection--single { background-color: #1a1a2e !important; border: 1px solid #2a2a3a !important; border-radius: 10px !important; height: 45px !important; display: flex !important; align-items: center !important; }
    .select2-container--default .select2-selection--single .select2-selection__rendered { color: white !important; padding-left: 12px !important; }
    .select2-container--default .select2-selection--single .select2-selection__arrow { height: 43px !important; }
    .select2-dropdown { background-color: #111118 !important; border: 1px solid #2a2a3a !important; color: white !important; z-index: 9999999 !important; }
    .select2-container--default .select2-search--dropdown .select2-search__field { background-color: #1a1a2e !important; border: 1px solid #2a2a3a !important; color: white !important; border-radius: 6px !important; }
    .select2-container--default .select2-results__option--highlighted[aria-selected] { background-color: #ec4899 !important; }
    
    /* Interactive Navigation Matrix Buttons Styles */
    .tab-trigger-btn { background: none; border: none; color: #64748b; padding: 12px 24px; font-size: 15px; font-weight: 600; cursor: pointer; transition: 0.2s; display: inline-flex; align-items: center; gap: 8px; border-bottom: 2px solid transparent; }
    .tab-trigger-btn:hover { color: #cbd5e1; }
    .tab-trigger-btn.active { color: #ec4899; border-color: #ec4899; }
    .tab-badge-count { font-size: 11px; font-weight: 700; color: white; padding: 2px 8px; border-radius: 20px; }
    
    /* Segment Visibility Controls */
    .tab-view-panel { display: none; }
    .tab-view-panel.active { display: block; }

    /* Core Element Cards CSS Architecture */
    .order-item-record-card { background: #111118; border: 1px solid #1e1e2a; border-radius: 20px; padding: 24px; margin-bottom: 20px; display: grid; gap: 30px; text-align: left; }
    .sub-panel-column { border-right: 1px solid #1e1e2a; padding-right: 15px; }
    .sub-panel-column:last-child { border-right: none; padding-right: 0; }
    .profile-card-title { font-size: 12px; font-weight: 700; color: #64748b; text-transform: uppercase; letter-spacing: 0.5px; margin-bottom: 12px; display: flex; align-items: center; gap: 6px; }
    .meta-data-line { font-size: 14px; margin-bottom: 8px; color: #cbd5e1; }
    .meta-data-line span { color: #64748b; }

    /* Buttons Layout Controls */
    .action-control-btn { padding: 10px 16px; border: none; border-radius: 10px; font-size: 13px; font-weight: 600; cursor: pointer; display: inline-flex; align-items: center; gap: 8px; transition: 0.2s; text-decoration: none; justify-content: center; }
    .action-control-btn:hover { opacity: 0.9; transform: translateY(-1px); }

    /* Structured Custom Form Row CSS Frameworks */
    .input-form-row { margin-bottom: 16px; text-align: left; }
    .input-form-row label { display: block; font-size: 13px; font-weight: 500; color: #94a3b8; margin-bottom: 6px; }
    .input-form-row input, .input-form-row select { width: 100%; padding: 12px; background: #1a1a2e; border: 1px solid #2a2a3a; border-radius: 10px; color: white; outline: none; font-size: 14px; box-sizing: border-box; }
</style>
@endpush

@section('admin-content')
<div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 25px; gap: 20px; flex-wrap: wrap;">
    <div style="text-align: left;">
        <h2 class="gradient-text" style="font-size: 28px; font-weight: 700; margin-bottom: 5px; color: white;">Inventory Vaults Control</h2>
        <p style="color: #64748b; font-size: 14px;">Track physical hardware catalog distribution lanes, update pricing, and audit asset specifications.</p>
    </div>
    <button class="action-control-btn" style="background: linear-gradient(135deg, #ec4899, #8b5cf6); color: white; padding: 12px 22px; font-size: 14px;" onclick="openModalWindow('addStockModal')">
        <i class="fas fa-plus-circle"></i> Onboard Asset Manually
    </button>
</div>

<div style="display: flex; gap: 10px; border-bottom: 1px solid #1e1e2a; padding-bottom: 1px; margin-bottom: 30px; flex-wrap: wrap;">
    <button class="tab-trigger-btn active" onclick="switchContextTab(event, 'new_stock')">
        New Stock <span class="tab-badge-count" style="background: #3b82f6;">{{ $newStock->count() }}</span>
    </button>
    <button class="tab-trigger-btn" onclick="switchContextTab(event, 'assigned_stock')">
        Assigned <span class="tab-badge-count" style="background: #eab308;">{{ $assignedStock->count() }}</span>
    </button>
    <button class="tab-trigger-btn" onclick="switchContextTab(event, 'completed_stock')">
        Completed <span class="tab-badge-count" style="background: #10b981;">{{ $completedStock->count() }}</span>
    </button>
</div>

<div id="new_stock" class="tab-view-panel active">
    @include('admin.partials.stock_cards_loop', ['collection' => $newStock, 'tab' => 'new'])
</div>

<div id="assigned_stock" class="tab-view-panel">
    @include('admin.partials.stock_cards_loop', ['collection' => $assignedStock, 'tab' => 'assigned'])
</div>

<div id="completed_stock" class="tab-view-panel">
    @include('admin.partials.stock_cards_loop', ['collection' => $completedStock, 'tab' => 'completed'])
</div>

<div id="addStockModal" style="position: fixed; top: 0; left: 0; width: 100vw; height: 100vh; background: rgba(5,5,8,0.85); backdrop-filter: blur(8px); z-index: 99999; display: none; justify-content: center; align-items: center; padding: 20px; box-sizing: border-box;">
    <div style="background: #111118; border: 1px solid #2a2a3a; width: 100%; max-width: 550px; border-radius: 24px; overflow: hidden; display: flex; flex-direction: column; box-shadow: 0 25px 50px -12px rgba(0,0,0,0.5);">
        <div style="padding: 20px 24px; border-bottom: 1px solid #1e1e2a; display: flex; align-items: center; justify-content: space-between;">
            <h4 style="color: white; font-size: 18px; font-weight: 600; margin: 0;"><i class="fas fa-plus" style="color: #ec4899; margin-right: 8px;"></i> Onboard System Hardware Asset</h4>
            <button onclick="closeModalWindow('addStockModal')" style="background: none; border: none; color: #94a3b8; font-size: 24px; cursor: pointer;">&times;</button>
        </div>
        <form id="addStockForm">
            @csrf
            <div style="padding: 24px; max-height: 70vh; overflow-y: auto;">
                <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 15px;">
                    <div class="input-form-row">
                        <label>Order Reference Code</label>
                        <input type="text" name="order_id" placeholder="e.g. MAN-9871" required>
                    </div>
                    <div class="input-form-row">
                        <label>Storage Size Spec</label>
                        <input type="text" name="capacity" placeholder="e.g. 8GB/128GB" value="8GB/128GB" required>
                    </div>
                </div>
                <div class="input-form-row">
                    <label>Select Device Catalog Profile Model</label>
                    <select id="model_search_dropdown" name="model_id" style="width: 100%;" required></select>
                </div>
                <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 15px;">
                    <div class="input-form-row">
                        <label>Assessed Cost Price ($)</label>
                        <input type="number" step="0.01" name="buy_price" required>
                    </div>
                    <div class="input-form-row">
                        <label>Device Color Spec</label>
                        <input type="text" name="color" placeholder="Midnight Black" required>
                    </div>
                </div>
                <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 15px;">
                    <div class="input-form-row">
                        <label>Primary IMEI Number</label>
                        <input type="text" name="imei_no_1" required>
                    </div>
                    <div class="input-form-row">
                        <label>Secondary IMEI Number</label>
                        <input type="text" name="imei_no_2">
                    </div>
                </div>
                <div class="input-form-row">
                    <label>Warranty Terms</label>
                    <select name="warranty" required>
                        <option value="no warranty">No Warranty Cover Framework</option>
                        <option value="3 month">3 Months Coverage Warranty</option>
                        <option value="6 month">6 Months Coverage Warranty</option>
                        <option value="1 year">1 Full Calendar Year Warranty</option>
                    </select>
                </div>
                <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 15px;">
                    <div class="input-form-row">
                        <label>Profit User Margin Allocation (%)</label>
                        <input type="number" name="profit_percent_user" value="20" required>
                    </div>
                    <div class="input-form-row">
                        <label>Vendor Margin Split (%)</label>
                        <input type="number" name="profit_perc_vendor" value="5" required>
                    </div>
                </div>
            </div>
            <div style="padding: 16px 24px; background: #0f0f15; border-top: 1px solid #1e1e2a; display: flex; justify-content: flex-end; gap: 12px;">
                <button type="button" class="action-control-btn" style="background: #1a1a2e; color: white;" onclick="closeModalWindow('addStockModal')">Abort</button>
                <button type="submit" class="action-control-btn" style="background: linear-gradient(135deg, #ec4899, #8b5cf6); color: white;">Save Hardware Entry</button>
            </div>
        </form>
    </div>
</div>

<div id="editStockModal" style="position: fixed; top: 0; left: 0; width: 100vw; height: 100vh; background: rgba(5,5,8,0.85); backdrop-filter: blur(8px); z-index: 99999; display: none; justify-content: center; align-items: center; padding: 20px; box-sizing: border-box;">
    <div style="background: #111118; border: 1px solid #2a2a3a; width: 100%; max-width: 550px; border-radius: 24px; overflow: hidden; display: flex; flex-direction: column; box-shadow: 0 25px 50px -12px rgba(0,0,0,0.5);">
        <div style="padding: 20px 24px; border-bottom: 1px solid #1e1e2a; display: flex; align-items: center; justify-content: space-between;">
            <h4 style="color: white; font-size: 18px; font-weight: 600; margin: 0;"><i class="fas fa-edit" style="color: #3b82f6; margin-right: 8px;"></i> Edit Stock Inventory Specifications</h4>
            <button onclick="closeModalWindow('editStockModal')" style="background: none; border: none; color: #94a3b8; font-size: 24px; cursor: pointer;">&times;</button>
        </div>
        <form id="editStockForm">
            @csrf
            <input type="hidden" id="edit_stock_id" name="id">
            <div style="padding: 24px; max-height: 70vh; overflow-y: auto;">
                <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 15px;">
                    <div class="input-form-row">
                        <label>Storage Size Spec</label>
                        <input type="text" id="edit_capacity" name="capacity" required>
                    </div>
                    <div class="input-form-row">
                        <label>Assessed Cost Price ($)</label>
                        <input type="number" step="0.01" id="edit_buy_price" name="buy_price" required>
                    </div>
                </div>
                <div class="input-form-row">
                    <label>Device Color Spec</label>
                    <input type="text" id="edit_color" name="color" required>
                </div>
                <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 15px;">
                    <div class="input-form-row">
                        <label>Primary IMEI Number</label>
                        <input type="text" id="edit_imei_1" name="imei_no_1" required>
                    </div>
                    <div class="input-form-row">
                        <label>Secondary IMEI Number</label>
                        <input type="text" id="edit_imei_2" name="imei_no_2">
                    </div>
                </div>
                <div class="input-form-row">
                    <label>Warranty Terms</label>
                    <select id="edit_warranty" name="warranty" required>
                        <option value="no warranty">No Warranty Cover Framework</option>
                        <option value="3 month">3 Months Coverage Warranty</option>
                        <option value="6 month">6 Months Coverage Warranty</option>
                        <option value="1 year">1 Full Calendar Year Warranty</option>
                    </select>
                </div>
                <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 15px;">
                    <div class="input-form-row">
                        <label>Profit User Margin Allocation (%)</label>
                        <input type="number" id="edit_profit_percent_user" name="profit_percent_user" required>
                    </div>
                    <div class="input-form-row">
                        <label>Vendor Margin Split (%)</label>
                        <input type="number" id="edit_profit_perc_vendor" name="profit_perc_vendor" required>
                    </div>
                </div>
            </div>
            <div style="padding: 16px 24px; background: #0f0f15; border-top: 1px solid #1e1e2a; display: flex; justify-content: flex-end; gap: 12px;">
                <button type="button" class="action-control-btn" style="background: #1a1a2e; color: white;" onclick="closeModalWindow('editStockModal')">Cancel</button>
                <button type="submit" class="action-control-btn" style="background: linear-gradient(135deg, #3b82f6, #2563eb); color: white;">Save Changes</button>
            </div>
        </form>
    </div>
</div>
@endsection

@push('admin-scripts')
<script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js"></script>
<script>
    // Tab switching architecture engine fix
    function switchContextTab(event, tabId) {
        // Hide all template tab blocks explicitly
        $('.tab-view-panel').removeClass('active').hide();
        $('.tab-trigger-btn').removeClass('active');
        
        // Show matching content element
        $('#' + tabId).addClass('active').show();
        $(event.currentTarget).addClass('active');
    }

    // Modal popup triggers
    function openModalWindow(modalId) { 
        $('#' + modalId).css('display', 'flex'); 
        
        if(modalId === 'addStockModal') {
            // Re-mount the catalog lookup autocomplete select2 tool safely inside container
            $('#model_search_dropdown').select2({
                dropdownParent: $('#addStockModal'),
                ajax: {
                    url: '/admin/api/search-models',
                    dataType: 'json',
                    delay: 250,
                    data: function (params) {
                        return { q: params.term };
                    },
                    processResults: function (data) { 
                        return { results: data.results }; 
                    },
                    cache: true
                },
                placeholder: 'Type to query device database name...',
                minimumInputLength: 1
            });
        }
    }
    
    function closeModalWindow(modalId) { 
        $('#' + modalId).css('display', 'none'); 
    }

    // Mount properties safely inside javascript modal layout
    function launchEditModal(dataset) {
        $('#edit_stock_id').val(dataset.id);
        $('#edit_capacity').val(dataset.capacity);
        $('#edit_buy_price').val(dataset.buy_price);
        $('#edit_color').val(dataset.color);
        $('#edit_imei_1').val(dataset.imei_no_1);
        $('#edit_imei_2').val(dataset.imei_no_2);
        $('#edit_warranty').val(dataset.warranty);
        $('#edit_profit_percent_user').val(dataset.profit_percent_user);
        $('#edit_profit_perc_vendor').val(dataset.profit_perc_vendor);
        openModalWindow('editStockModal');
    }

    // Process Form Requests via Core AJAX Operations
    $('#addStockForm').on('submit', function(e) {
        e.preventDefault();
        $.ajax({
            url: "{{ route('admin.stock.store') }}",
            method: "POST",
            data: $(this).serialize(),
            success: function(res) { 
                if(res.success) { 
                    alert(res.message); 
                    window.location.reload(); 
                } 
            },
            error: function() { alert("Failed to log inventory entry."); }
        });
    });

    $('#editStockForm').on('submit', function(e) {
        e.preventDefault();
        $.ajax({
            url: "{{ route('admin.stock.update') }}",
            method: "POST",
            data: $(this).serialize(),
            success: function(res) { 
                if(res.success) { 
                    alert(res.message); 
                    window.location.reload(); 
                } 
            },
            error: function() { alert("Failed to commit modification changes."); }
        });
    });

    function fireDeleteAction(id) {
        if(!confirm("Are you sure you want to permanently erase this inventory record?")) return;
        $.ajax({
            url: "{{ route('admin.stock.delete') }}",
            method: "POST",
            data: { _token: "{{ csrf_token() }}", id: id },
            success: function(res) { 
                if(res.success) { 
                    alert(res.message); 
                    window.location.reload(); 
                } 
            }
        });
    }

    function fireAssignmentAction(id, actionType) {
        if(!confirm(`Are you sure you want to proceed with this inventory pool move?`)) return;
        $.ajax({
            url: "{{ route('admin.stock.updateAssignment') }}",
            method: "POST",
            data: { _token: "{{ csrf_token() }}", id: id, action: actionType },
            success: function(res) { 
                if(res.success) { 
                    alert(res.message); 
                    window.location.reload(); 
                } 
            }
        });
    }

    // Run tab configuration defaults inside document on page load
    $(document).ready(function() {
        $('.tab-view-panel').hide();
        $('.tab-view-panel.active').show();
    });
</script>
@endpush