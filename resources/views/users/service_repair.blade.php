@extends('layouts.app') {{-- Adjust to your user layout --}}

@section('title', 'Book a Service/Repair')

@push('styles')
<link href="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css" rel="stylesheet" />
<style>
    /* ===== RESET & BASE ===== */
    .repair-container { 
        max-width: 800px; 
        margin: 30px auto; 
        padding: 0 16px; 
    }
    
    .form-card { 
        background: #111118; 
        padding: 25px 20px; 
        border-radius: 12px; 
        box-shadow: 0 4px 15px rgba(0,0,0,0.3); 
        border: 1px solid #1e1e2a; 
    }
    
    .form-card h2 {
        color: white;
        font-size: 22px;
        margin-bottom: 20px;
    }
    
    .form-card h4 {
        color: white;
        font-size: 18px;
        margin-bottom: 16px;
    }

    /* ===== FORM ELEMENTS ===== */
    .form-group { 
        margin-bottom: 18px; 
        width: 100%;
    }
    
    .form-group label { 
        font-weight: 600; 
        margin-bottom: 6px; 
        display: block; 
        color: #cbd5e1; 
        font-size: 14px;
    }
    
    .form-control { 
        width: 100%; 
        padding: 10px 14px; 
        background: #1a1a2e;
        color: white;
        border: 1px solid #2a2a3a; 
        border-radius: 8px; 
        transition: 0.3s ease;
        font-size: 14px;
        box-sizing: border-box;
    }
    
    .form-control:focus {
        outline: none;
        border-color: #3b82f6;
        box-shadow: 0 0 0 3px rgba(59, 130, 246, 0.15);
    }
    
    textarea.form-control {
        resize: vertical;
        min-height: 80px;
        font-family: inherit;
    }
    
    /* ===== ROW LAYOUT ===== */
    .form-row {
        display: flex;
        gap: 16px;
        flex-wrap: wrap;
    }
    
    .form-row .form-group {
        flex: 1;
        min-width: 180px;
    }
    
    .form-row .form-group.flex-2 {
        flex: 2;
        min-width: 200px;
    }
    
    .form-row .form-group.flex-1 {
        flex: 1;
        min-width: 140px;
    }

    /* ===== SELECT2 DARK MODE ===== */
    .select2-container--default .select2-selection--single {
        background-color: #1a1a2e;
        border: 1px solid #2a2a3a;
        border-radius: 8px;
        height: 42px;
        display: flex;
        align-items: center;
    }
    
    .select2-container--default .select2-selection--single .select2-selection__rendered {
        color: white;
        padding-left: 14px;
        font-size: 14px;
    }
    
    .select2-container--default .select2-selection--single .select2-selection__arrow {
        height: 40px;
        right: 8px;
    }
    
    .select2-container--default .select2-selection--single .select2-selection__arrow b {
        border-color: #94a3b8 transparent transparent transparent;
    }
    
    .select2-dropdown {
        background-color: #111118;
        border: 1px solid #2a2a3a;
        border-radius: 8px;
    }
    
    .select2-search--dropdown .select2-search__field {
        background-color: #1a1a2e;
        color: white;
        border: 1px solid #2a2a3a;
        border-radius: 6px;
        padding: 8px 12px;
    }
    
    .select2-search--dropdown .select2-search__field:focus {
        border-color: #3b82f6;
        outline: none;
    }
    
    .select2-container--default .select2-results__option {
        color: #cbd5e1;
        padding: 8px 14px;
        font-size: 14px;
    }
    
    .select2-container--default .select2-results__option[aria-selected=true] {
        background-color: #1e1e2a;
    }
    
    .select2-container--default .select2-results__option--highlighted[aria-selected] {
        background-color: #3b82f6;
        color: white;
    }
    
    .select2-container--default .select2-results__option--highlighted[aria-selected] .select2-results__option {
        color: white;
    }

    /* ===== BUTTON ===== */
    .btn-submit { 
        background: #3b82f6; 
        color: white; 
        padding: 13px 24px; 
        border: none; 
        border-radius: 8px; 
        font-weight: 600; 
        font-size: 16px;
        cursor: pointer; 
        width: 100%; 
        transition: 0.3s;
    }
    
    .btn-submit:hover { 
        background: #2563eb; 
    }
    
    .btn-submit:active {
        transform: scale(0.98);
    }
    
    /* ===== DIVIDER ===== */
    hr.dark-divider {
        margin: 25px 0; 
        border: none;
        border-top: 1px solid #1e1e2a;
    }
    
    /* ===== REQUIRED STAR ===== */
    .form-group label .required {
        color: #ef4444;
        margin-left: 2px;
    }

    /* ======================================== */
    /* ===== RESPONSIVE BREAKPOINTS ===== */
    /* ======================================== */

    /* Tablets & Small Laptops */
    @media (max-width: 768px) {
        .repair-container {
            margin: 20px auto;
            padding: 0 12px;
        }
        
        .form-card {
            padding: 20px 16px;
        }
        
        .form-card h2 {
            font-size: 20px;
        }
        
        .form-card h4 {
            font-size: 16px;
        }
        
        .form-row {
            gap: 12px;
        }
        
        .form-row .form-group {
            min-width: 150px;
        }
        
        .form-group label {
            font-size: 13px;
        }
        
        .form-control {
            font-size: 13px;
            padding: 9px 12px;
        }
        
        .btn-submit {
            font-size: 15px;
            padding: 12px 20px;
        }
    }

    /* Mobile Phones */
    @media (max-width: 576px) {
        .repair-container {
            margin: 12px auto;
            padding: 0 10px;
        }
        
        .form-card {
            padding: 16px 12px;
            border-radius: 10px;
        }
        
        .form-card h2 {
            font-size: 18px;
            margin-bottom: 16px;
        }
        
        .form-card h2 i {
            font-size: 16px;
        }
        
        .form-card h4 {
            font-size: 15px;
            margin-bottom: 14px;
        }
        
        .form-group {
            margin-bottom: 14px;
        }
        
        .form-group label {
            font-size: 12px;
            margin-bottom: 4px;
        }
        
        .form-control {
            font-size: 13px;
            padding: 8px 12px;
            border-radius: 6px;
        }
        
        .form-row {
            flex-direction: column !important;
            gap: 0;
        }
        
        .form-row .form-group {
            flex: 1 1 100% !important;
            min-width: 100% !important;
            width: 100% !important;
        }
        
        .form-row .form-group.flex-2,
        .form-row .form-group.flex-1 {
            flex: 1 1 100% !important;
            min-width: 100% !important;
        }
        
        textarea.form-control {
            min-height: 70px;
        }
        
        .btn-submit {
            font-size: 14px;
            padding: 11px 16px;
        }
        
        hr.dark-divider {
            margin: 18px 0;
        }
        
        /* Select2 mobile fix */
        .select2-container--default .select2-selection--single {
            height: 38px;
        }
        
        .select2-container--default .select2-selection--single .select2-selection__rendered {
            font-size: 13px;
            padding-left: 12px;
        }
        
        .select2-container--default .select2-selection--single .select2-selection__arrow {
            height: 36px;
        }
        
        .select2-container--default .select2-results__option {
            font-size: 13px;
            padding: 7px 12px;
        }
    }

    /* Very Small Phones */
    @media (max-width: 380px) {
        .repair-container {
            padding: 0 6px;
        }
        
        .form-card {
            padding: 12px 10px;
            border-radius: 8px;
        }
        
        .form-card h2 {
            font-size: 16px;
        }
        
        .form-card h4 {
            font-size: 14px;
        }
        
        .form-control {
            font-size: 12px;
            padding: 7px 10px;
        }
        
        .form-group label {
            font-size: 11px;
        }
        
        .btn-submit {
            font-size: 13px;
            padding: 10px 14px;
        }
        
        .select2-container--default .select2-selection--single {
            height: 35px;
        }
        
        .select2-container--default .select2-selection--single .select2-selection__rendered {
            font-size: 12px;
            padding-left: 10px;
        }
        
        .select2-container--default .select2-selection--single .select2-selection__arrow {
            height: 33px;
        }
    }

    /* ===== TOUCH FRIENDLY ===== */
    @media (hover: none) and (pointer: coarse) {
        .form-control,
        .btn-submit,
        .select2-container--default .select2-selection--single {
            min-height: 44px;
        }
        
        .select2-container--default .select2-selection--single {
            height: 44px;
        }
        
        .select2-container--default .select2-selection--single .select2-selection__arrow {
            height: 42px;
        }
    }
</style>
@endpush

@section('content')
<div class="repair-container">
    <div class="form-card">
        <h2><i class="fas fa-tools" style="color: #3b82f6; margin-right: 10px;"></i> Book Mobile Repair</h2>
        
        <form action="{{ route('store_service_repair') }}" method="POST">
            @csrf
            
            <!-- Mobile Model -->
            <div class="form-group">
                <label>Select Mobile Model <span class="required">*</span></label>
                <select name="model_id" class="form-control select2-model" required>
                    <option value="">-- Search for your model --</option>
                    @foreach($models as $model)
                        <option value="{{ $model->id }}">{{ $model->title }}</option>
                    @endforeach
                </select>
            </div>

            <!-- Issue Category & Subcategory -->
            <div class="form-row">
                <div class="form-group">
                    <label>Issue Category <span class="required">*</span></label>
                    <select name="category_name" id="issue_category" class="form-control" required>
                        <option value="">-- Select Category --</option>
                        <option value="Display">Display &amp; Touch</option>
                        <option value="Battery">Battery &amp; Power</option>
                        <option value="Camera">Camera</option>
                        <option value="Audio">Audio &amp; Speakers</option>
                        <option value="Other">Other Issues</option>
                    </select>
                </div>

                <div class="form-group">
                    <label>Specific Issue <span class="required">*</span></label>
                    <select name="subcategory_name" id="issue_subcategory" class="form-control" required>
                        <option value="">-- Select Category First --</option>
                    </select>
                </div>
            </div>

            <hr class="dark-divider">
            <h4>Customer Details</h4>

            <!-- Name, Phone, Alt Phone -->
            <div class="form-row">
                <div class="form-group">
                    <label>Full Name <span class="required">*</span></label>
                    <input type="text" name="name" class="form-control" required>
                </div>
                <div class="form-group">
                    <label>Phone Number <span class="required">*</span></label>
                    <input type="tel" name="phone" class="form-control" required>
                </div>
                <div class="form-group">
                    <label>Alt Phone</label>
                    <input type="tel" name="alt_phone" class="form-control">
                </div>
            </div>

            <!-- Address -->
            <div class="form-group">
                <label>Complete Address <span class="required">*</span></label>
                <textarea name="address" class="form-control" rows="3" required></textarea>
            </div>

            <!-- Landmark & Pincode -->
            <div class="form-row">
                <div class="form-group flex-2">
                    <label>Landmark</label>
                    <input type="text" name="landmark" class="form-control">
                </div>
                <div class="form-group flex-1">
                    <label>Pincode <span class="required">*</span></label>
                    <input type="text" name="pincode" class="form-control" required>
                </div>
            </div>

            <!-- Remarks -->
            <div class="form-group">
                <label>Additional Remarks <span style="color: #64748b; font-weight: 400; font-size: 12px;">(Optional)</span></label>
                <textarea name="remarks" class="form-control" rows="2"></textarea>
            </div>

            <button type="submit" class="btn-submit"><i class="fas fa-check-circle" style="margin-right: 6px;"></i> Submit Service Request</button>
        </form>
    </div>
</div>
@endsection

@push('scripts')
<script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js"></script>
<script>
    $(document).ready(function() {
        // Initialize Select2
        $('.select2-model').select2({
            width: '100%',
            placeholder: '-- Search for your model --',
            allowClear: true
        });

        // Subcategories mapping
        const subcategories = {
            'Display': ['Screen Cracked', 'Touch Not Working', 'Display Lines', 'Blank Screen'],
            'Battery': ['Battery Draining Fast', 'Device Not Turning On', 'Swollen Battery', 'Charging Port Issue'],
            'Camera': ['Back Camera Blur', 'Front Camera Not Working', 'Camera Glass Broken'],
            'Audio': ['Earpiece No Sound', 'Loudspeaker Issue', 'Mic Not Working'],
            'Other': ['Software Issue', 'Network Issue', 'Water Damage', 'Other (Explain in remarks)']
        };

        // Update subcategory on category change
        $('#issue_category').on('change', function() {
            let cat = $(this).val();
            let subcatSelect = $('#issue_subcategory');
            subcatSelect.empty();
            
            if (cat && subcategories[cat]) {
                subcatSelect.append('<option value="">-- Select Specific Issue --</option>');
                subcategories[cat].forEach(function(issue) {
                    subcatSelect.append('<option value="'+issue+'">'+issue+'</option>');
                });
            } else {
                subcatSelect.append('<option value="">-- Select Category First --</option>');
            }
        });
    });
</script>
@endpush