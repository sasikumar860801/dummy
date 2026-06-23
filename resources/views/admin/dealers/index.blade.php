
@extends('admin.layout')

@section('title', 'Dealers Management Portal')

@push('admin-styles')
<link href="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css" rel="stylesheet" />
<style>
    .dealer-card-grid { background: #111118; border: 1px solid #1e1e2a; border-radius: 20px; padding: 24px; }
    
    /* Select2 Theme Custom Overrides */
    .select2-container--default .select2-selection--multiple { background-color: #1a1a2e !important; border: 1px solid #2a2a3a !important; border-radius: 10px; padding: 6px; min-height: 45px; }
    .select2-container--default .select2-selection--multiple .select2-selection__choice { background-color: #ec4899 !important; color: white !important; border: none !important; border-radius: 6px; padding: 2px 10px; font-weight: 600; }
    .select2-container--default .select2-selection--multiple .select2-selection__choice__remove { color: white !important; margin-right: 5px; }
    .select2-dropdown { background-color: #111118 !important; border-color: #2a2a3a !important; color: white !important; }
    .select2-container--default .select2-results__option--highlighted[aria-selected] { background-color: #ec4899 !important; color: white !important; }
    
    .preview-thumbnail { height: 45px; width: 45px; object-fit: cover; border-radius: 50%; border: 2px solid #1e1e2a; }
    .district-pill { display: inline-block; background: rgba(59, 130, 246, 0.15); color: #60a5fa; border: 1px solid rgba(59, 130, 246, 0.25); padding: 2px 10px; border-radius: 12px; font-size: 11px; font-weight: 600; margin: 2px; }

    /* Ported Navigation Filter Buttons from orders layout reference */
    .tab-trigger-btn { background: none; border: none; color: #64748b; padding: 12px 24px; font-size: 14px; font-weight: 600; cursor: pointer; transition: 0.2s; display: flex; align-items: center; gap: 8px; border-bottom: 2px solid transparent; }
    .tab-trigger-btn:hover { color: #cbd5e1; }
    .tab-trigger-btn.active { color: #ec4899; border-color: #ec4899; }
    .tab-badge-count { font-size: 11px; font-weight: 700; color: white; padding: 2px 8px; border-radius: 20px; }

    /* Orders PopUp Scaffolding Structural Layouts */
    .dashboard-popup-scaffolding { position: fixed; top: 0; left: 0; width: 100vw; height: 100vh; background: rgba(5,5,8,0.85); backdrop-filter: blur(8px); z-index: 99999; display: none; justify-content: center; align-items: center; padding: 20px; }
    .popup-modal-box { background: #111118; border: 1px solid #2a2a3a; width: 100%; border-radius: 24px; overflow: hidden; display: flex; flex-direction: column; box-shadow: 0 25px 50px -12px rgba(0,0,0,0.5); }
    .popup-modal-header { padding: 20px 24px; border-bottom: 1px solid #1e1e2a; display: flex; align-items: center; justify-content: space-between; }
    .popup-modal-body { padding: 24px; max-height: 75vh; overflow-y: auto; }
    .popup-modal-footer { padding: 16px 24px; background: #0f0f15; border-top: 1px solid #1e1e2a; display: flex; justify-content: flex-end; gap: 12px; }
    
    .input-form-row { margin-bottom: 18px; }
    .input-form-row label { display: block; font-size: 13px; font-weight: 600; color: #94a3b8; margin-bottom: 6px; text-transform: uppercase; letter-spacing: 0.5px; }
    .input-form-row input, .input-form-row textarea { width: 100%; padding: 12px; background: #1a1a2e; border: 1px solid #2a2a3a; border-radius: 10px; color: white; outline: none; font-size: 14px; }
</style>
@endpush

@section('admin-content')
<div class="container-fluid p-0">
    
    <div style="margin-bottom: 30px; display: flex; justify-content: space-between; align-items: center; flex-wrap: wrap; gap: 15px;">
        <div>
            <h2 class="gradient-text" style="font-size: 28px; font-weight: 700; margin-bottom: 5px;">Dealers Hub Terminal</h2>
            <p style="color: #64748b; font-size: 14px;">Onboard regional operators, maintain structural credentials, and scale service networks.</p>
        </div>
        <button class="btn" style="background: linear-gradient(135deg, #f472b6, #c084fc); border: none; color: white; padding: 12px 24px; font-weight: 600; border-radius: 10px; cursor: pointer; display: flex; align-items: center; gap: 8px;" onclick="openDealerModal('create')">
            <i class="fas fa-plus-circle"></i> Onboard New Dealer
        </button>
    </div>

    <div id="district_filter_strip" style="display: flex; gap: 10px; border-bottom: 1px solid #1e1e2a; padding-bottom: 1px; margin-bottom: 30px; flex-wrap: wrap;">
        </div>

    <div class="dealer-card-grid">
        <div class="table-responsive">
            <table class="table table-dark table-hover align-middle border-0 mb-0" style="--bs-table-bg: transparent;">
                <thead>
                    <tr class="text-muted border-bottom border-secondary" style="font-size: 12px; text-transform: uppercase; letter-spacing: 0.5px;">
                        <th style="padding: 15px 10px;">ID</th>
                        <th>Avatar</th>
                        <th>Dealer Identity</th>
                        <th>Shop Details</th>
                        <th>Physical Address</th>
                        <th>Contact info</th>
                        <th>Mapped Districts</th>
                        <th>Status</th>
                        <th class="text-center">Execution Controls</th>
                    </tr>
                </thead>
                <tbody id="dealers_table_body">
                    </tbody>
            </table>
        </div>
    </div>
</div>

<div id="dealerFormModal" class="dashboard-popup-scaffolding">
    <div class="popup-modal-box" style="max-width: 800px;">
        <div class="popup-modal-header">
            <h4 style="color: white; font-size: 18px; font-weight: 600;"><i class="fas fa-users-cog" style="color: #ec4899; margin-right: 8px;"></i> <span id="modal_display_title">Register Entity</span></h4>
            <button onclick="closeDealerModal()" style="background: none; border: none; color: #94a3b8; font-size: 28px; cursor: pointer;">&times;</button>
        </div>
        <form id="dealer_master_form" method="POST" enctype="multipart/form-data">
            @csrf
            <input type="hidden" name="dealer_id" id="dealer_id">
            
            <div class="popup-modal-body">
                <div class="row">
                    <div class="col-md-6 input-form-row">
                        <label>Dealer Profile Name *</label>
                        <input type="text" name="name" id="name" required placeholder="e.g. Sasi Kumar">
                    </div>
                    <div class="col-md-6 input-form-row">
                        <label>Shop Firm Name *</label>
                        <input type="text" name="shop_name" id="shop_name" required placeholder="e.g. Delta Enterprises">
                    </div>
                </div>

                <div class="row">
                    <div class="col-md-6 input-form-row">
                        <label>Primary Phone *</label>
                        <input type="text" name="phone" id="phone" required placeholder="e.g. +91 9876543210">
                    </div>
                    <div class="col-md-6 input-form-row">
                        <label>Secondary / Alternate Phone</label>
                        <input type="text" name="alternate_phone" id="alternate_phone" placeholder="Optional backup line">
                    </div>
                </div>

                <div class="row">
                    <div class="col-md-6 input-form-row">
                        <label>PAN Registry ID</label>
                        <input type="text" name="pan_no" id="pan_no" placeholder="Tax account identity index">
                    </div>
                    <div class="col-md-6 input-form-row">
                        <label>Security MPIN <span id="mpin_hint" style="color:#f472b6;">*</span></label>
                        <input type="password" name="mpin" id="mpin" placeholder="Access code sequence">
                    </div>
                </div>

                <div class="input-form-row">
                    <label>Assigned Territorial Coverage Districts *</label>
                    <select name="district[]" id="district" class="form-control select2-multiple" multiple="multiple" style="width:100%;" required>
                        @foreach($districts as $dist)
                            <option value="{{ $dist->id }}">{{ $dist->district_name }}</option>
                        @endforeach
                    </select>
                </div>

                <div class="input-form-row">
                    <label>Physical Street Address Framework</label>
                    <textarea name="shop_address" id="shop_address" rows="2" placeholder="Full operational warehouse details..."></textarea>
                </div>

                <div class="row g-2">
                    <div class="col-md-3 input-form-row">
                        <label style="font-size:11px;">Avatar File</label>
                        <input type="file" name="dealer_photo" style="padding: 8px; font-size:12px;">
                    </div>
                    <div class="col-md-3 input-form-row">
                        <label style="font-size:11px;">Shop Frame</label>
                        <input type="file" name="shop_photo" style="padding: 8px; font-size:12px;">
                    </div>
                    <div class="col-md-3 input-form-row">
                        <label style="font-size:11px;">ID Front</label>
                        <input type="file" name="proof_front" style="padding: 8px; font-size:12px;">
                    </div>
                    <div class="col-md-3 input-form-row">
                        <label style="font-size:11px;">ID Back</label>
                        <input type="file" name="proof_back" style="padding: 8px; font-size:12px;">
                    </div>
                </div>
            </div>

            <div class="popup-modal-footer">
                <button type="button" class="btn" style="background: #1e1e2a; color: #94a3b8; padding: 10px 20px; border-radius: 10px; font-weight:600;" onclick="closeDealerModal()">Cancel</button>
                <button type="submit" class="btn" style="background: linear-gradient(135deg, #10b981, #059669); color: white; padding: 10px 24px; border-radius: 10px; font-weight:600;">Save Record</button>
            </div>
        </form>
    </div>
</div>
@endsection

@push('admin-scripts')
<script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js"></script>
<script>
    // System lookup dataset to map ID integers back to strings cleanly
    const operationalDistrictMap = {
        @foreach($districts as $dist)
            "{{ $dist->id }}": "{{ $dist->district_name }}",
        @endforeach
    };

    let globalDealersArray = []; // Store the data globally to make dynamic filtering fast

    $(document).ready(function() {
        $('.select2-multiple').select2({
            dropdownParent: $('#dealerFormModal'),
            placeholder: "Select regional district parameters",
            allowClear: true
        });

        fetchDealersData();

        // AJAX commit save operations
        $('#dealer_master_form').on('submit', function(e) {
            e.preventDefault();
            let formData = new FormData(this);

            $.ajax({
                url: "{{ route('admin.dealers.save') }}",
                type: "POST",
                data: formData,
                processData: false,
                contentType: false,
                success: function(response) {
                    alert(response.success);
                    closeDealerModal();
                    fetchDealersData();
                },
                error: function() {
                    alert("Execution failed. Verify fields schema layout mappings.");
                }
            });
        });
    });

    function fetchDealersData() {
        $.ajax({
            url: "{{ route('admin.dealers.index') }}",
            type: "GET",
            dataType: "json",
            success: function(data) {
                globalDealersArray = data.dealers;
                buildDistrictFilterTabs();
                renderTableRows(globalDealersArray);
            }
        });
    }

    // Dynamic Filter Button Grid Generator
    function buildDistrictFilterTabs() {
        let activeDistricts = new Set();
        let countsMap = {}; // Tracks active assignments to build tab layout labels

        globalDealersArray.forEach(function(dealer) {
            let ids = [];
            try { ids = JSON.parse(dealer.district) || []; } catch(e) { ids = []; }
            ids.forEach(id => {
                if(operationalDistrictMap[id]) {
                    activeDistricts.add(id);
                    countsMap[id] = (countsMap[id] || 0) + 1;
                }
            });
        });

        // Add 'Show All Dealers' component to head position
        let filterHtml = `
            <button class="tab-trigger-btn active" data-district-id="all" onclick="filterDealersContext(event, 'all')">
                All Districts <span class="tab-badge-count" style="background: #3b82f6;">${globalDealersArray.length}</span>
            </button>
        `;

        // Loop over parsed parameters to render additional filtering metrics
        activeDistricts.forEach(function(dId) {
            filterHtml += `
                <button class="tab-trigger-btn" data-district-id="${dId}" onclick="filterDealersContext(event, '${dId}')">
                    ${operationalDistrictMap[dId]} <span class="tab-badge-count" style="background: #ec4899;">${countsMap[dId]}</span>
                </button>
            `;
        });

        $('#district_filter_strip').html(filterHtml);
    }

    // Dynamic DOM filtering operation
    function filterDealersContext(event, targetDistrictId) {
        $('.tab-trigger-btn').removeClass('active');
        $(event.currentTarget).addClass('active');

        if(targetDistrictId === 'all') {
            renderTableRows(globalDealersArray);
        } else {
            let filtered = globalDealersArray.filter(function(dealer) {
                let ids = [];
                try { ids = JSON.parse(dealer.district) || []; } catch(e) { ids = []; }
                return ids.includes(targetDistrictId.toString()) || ids.includes(parseInt(targetDistrictId));
            });
            renderTableRows(filtered);
        }
    }

    function renderTableRows(dealersList) {
        let html = '';
        if(dealersList.length > 0) {
            dealersList.forEach(function(row) {
                let photo = row.dealer_photo ? `/${row.dealer_photo}` : 'https://via.placeholder.com/150';
                let statusBadge = row.status == 1 ? '<span style="background:#10b981; color:white; padding:4px 10px; font-size:12px; font-weight:700; border-radius:6px;">Active</span>' : '<span style="background:#ef4444; color:white; padding:4px 10px; font-size:12px; font-weight:700; border-radius:6px;">Inactive</span>';
                let toggleIcon = row.status == 1 ? '<i class="fas fa-toggle-on text-success fa-lg" style="color:#10b981 !important; cursor:pointer;"></i>' : '<i class="fas fa-toggle-off text-muted fa-lg" style="cursor:pointer;"></i>';
                
                let districtIds = [];
                try { districtIds = JSON.parse(row.district) || []; } catch(e) { districtIds = []; }
                let districtBadges = '';
                districtIds.forEach(function(dId) {
                    if(operationalDistrictMap[dId]) {
                        districtBadges += `<span class="district-pill">${operationalDistrictMap[dId]}</span>`;
                    }
                });
                if(!districtBadges) districtBadges = '<span class="text-muted small">None</span>';

                html += `
                    <tr class="border-bottom border-dark" style="border-color: #1e1e2a !important;">
                        <td style="color: #475569; font-weight:700; padding:15px 10px;">${row.id}</td>
                        <td><img src="${photo}" class="preview-thumbnail"></td>
                        <td><div class="fw-bold text-white" style="font-size:14px;">${row.name}</div><small class="text-muted">PAN: ${row.pan_no ? row.pan_no : 'N/A'}</small></td>
                        <td style="color: #cbd5e1; font-size:14px;">${row.shop_name}</td>
                        <td style="color: #94a3b8; font-size:13px; max-width:220px; white-space:normal; line-height:1.4;">${row.shop_address ? row.shop_address : '<span class="text-muted small">No custom address logged</span>'}</td>
                        <td style="color: #cbd5e1; font-size:13px; line-height:1.4;">
                            <div><i class="fas fa-phone-alt text-muted" style="font-size:11px;"></i> ${row.phone}</div>
                            ${row.alternate_phone ? `<div><i class="fas fa-phone-square text-muted" style="font-size:11px;"></i> ${row.alternate_phone}</div>` : ''}
                        </td>
                        <td style="max-width: 200px; white-space: normal;">${districtBadges}</td>
                        <td>${statusBadge}</td>
                        <td class="text-center">
                            <div class="d-flex justify-content-center align-items-center gap-3">
                                <a href="javascript:void(0)" onclick="toggleDealerMatrix(${row.id})" title="Toggle Access status">
                                    ${toggleIcon}
                                </a>
                                <button class="btn btn-sm" style="border: 1px solid #3b82f6; color:#3b82f6; background:transparent; font-weight:600; border-radius:6px; padding: 4px 10px;" onclick="openDealerModal('edit', ${row.id})">
                                    <i class="fas fa-edit"></i> Edit
                                </button>
                            </div>
                        </td>
                    </tr>
                `;
            });
        } else {
            html = '<tr><td colspan="9" class="text-center text-muted py-5">No structural operational dealer nodes registered matching selection criteria.</td></tr>';
        }
        $('#dealers_table_body').html(html);
    }

    function openDealerModal(mode, id = null) {
        $('#dealer_master_form')[0].reset();
        $('#dealer_id').val('');
        $('#district').val(null).trigger('change');

        if(mode === 'create') {
            $('#modal_display_title').text('Register New Dealer Entity');
            $('#mpin_hint').text('*');
            $('#mpin').attr('required', true);
            $('#dealerFormModal').css('display', 'flex');
        } else {
            $('#modal_display_title').text('Modify Dealer Base Configuration Settings');
            $('#mpin_hint').text('(Leave blank to maintain original)');
            $('#mpin').removeAttr('required');
            
            $.ajax({
                url: `/admin/dealers/edit/${id}`,
                type: "GET",
                success: function(res) {
                    $('#dealer_id').val(res.dealer.id);
                    $('#name').val(res.dealer.name);
                    $('#shop_name').val(res.dealer.shop_name);
                    $('#phone').val(res.dealer.phone);
                    $('#alternate_phone').val(res.dealer.alternate_phone);
                    $('#pan_no').val(res.dealer.pan_no);
                    $('#shop_address').val(res.dealer.shop_address);
                    
                    $('#district').val(res.dealer.district).trigger('change');
                    $('#dealerFormModal').css('display', 'flex');
                }
            });
        }
    }

    function closeDealerModal() {
        $('#dealerFormModal').hide();
    }

    function toggleDealerMatrix(id) {
        $.ajax({
            url: `/admin/dealers/toggle/${id}`,
            type: "POST",
            data: { _token: "{{ csrf_token() }}" },
            success: function() {
                fetchDealersData();
            }
        });
    }
</script>
@endpush