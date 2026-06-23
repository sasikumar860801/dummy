@extends('admin.layout')

@section('title', 'Bidding Configuration Console')

@push('admin-styles')
<link href="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css" rel="stylesheet" />
<style>
    .bidding-card-grid { background: #111118; border: 1px solid #1e1e2a; border-radius: 20px; padding: 24px; }
    
    /* Theme Matching Select2 Dropdown Overrides */
    .select2-container--default .select2-selection--single { background-color: #1a1a2e !important; border: 1px solid #2a2a3a !important; border-radius: 10px; height: 45px !important; display: flex !important; align-items: center !important; }
    .select2-container--default .select2-selection--single .select2-selection__rendered { color: white !important; padding-left: 12px !important; }
    .select2-container--default .select2-selection--single .select2-selection__arrow { height: 45px !important; }
    .select2-dropdown { background-color: #111118 !important; border-color: #2a2a3a !important; color: white !important; }
    .select2-container--default .select2-results__option { padding: 10px 15px; color: #cbd5e1; }
    .select2-container--default .select2-results__option--highlighted[aria-selected] { background-color: #ec4899 !important; color: white !important; }
    
    .percentage-pill { display: inline-block; background: rgba(16, 185, 129, 0.1); color: #34d399; border: 1px solid rgba(16, 185, 129, 0.2); padding: 4px 12px; border-radius: 8px; font-weight: 700; font-size: 13px; }

    /* Modular Popup Scaffolding Ported from orders reference layout */
    .dashboard-popup-scaffolding { position: fixed; top: 0; left: 0; width: 100vw; height: 100vh; background: rgba(5,5,8,0.85); backdrop-filter: blur(8px); z-index: 99999; display: none; justify-content: center; align-items: center; padding: 20px; }
    .popup-modal-box { background: #111118; border: 1px solid #2a2a3a; width: 100%; border-radius: 24px; overflow: hidden; display: flex; flex-direction: column; box-shadow: 0 25px 50px -12px rgba(0,0,0,0.5); }
    .popup-modal-header { padding: 20px 24px; border-bottom: 1px solid #1e1e2a; display: flex; align-items: center; justify-content: space-between; }
    .popup-modal-body { padding: 24px; max-height: 75vh; overflow-y: auto; }
    .popup-modal-footer { padding: 16px 24px; background: #0f0f15; border-top: 1px solid #1e1e2a; display: flex; justify-content: flex-end; gap: 12px; }
    
    .input-form-row { margin-bottom: 18px; }
    .input-form-row label { display: block; font-size: 13px; font-weight: 600; color: #94a3b8; margin-bottom: 6px; text-transform: uppercase; letter-spacing: 0.5px; }
    .input-form-row input { width: 100%; padding: 12px; background: #1a1a2e; border: 1px solid #2a2a3a; border-radius: 10px; color: white; outline: none; font-size: 14px; }
    .input-form-row input:focus { border-color: #ec4899; }
</style>
@endpush

@section('admin-content')
<div class="container-fluid p-0">
    
    <div style="margin-bottom: 30px; display: flex; justify-content: space-between; align-items: center; flex-wrap: wrap; gap: 15px;">
        <div>
            <h2 class="gradient-text" style="font-size: 28px; font-weight: 700; margin-bottom: 5px;">Bidding Constraints Dashboard</h2>
            <p style="color: #64748b; font-size: 14px;">Establish algorithmic margins, configure operating hours, and assign regional limits.</p>
        </div>
        <button class="btn" style="background: linear-gradient(135deg, #f472b6, #c084fc); border: none; color: white; padding: 12px 24px; font-weight: 600; border-radius: 10px; cursor: pointer; display: flex; align-items: center; gap: 8px;" onclick="openBiddingModal('create')">
            <i class="fas fa-sliders-h"></i> Create Bidding Rule
        </button>
    </div>

    <div class="bidding-card-grid">
        <div class="table-responsive">
            <table class="table table-dark table-hover align-middle border-0 mb-0" style="--bs-table-bg: transparent;">
                <thead>
                    <tr class="text-muted border-bottom border-secondary" style="font-size: 12px; text-transform: uppercase; letter-spacing: 0.5px;">
                        <th style="padding: 15px 10px;">ID</th>
                        <th> District Name Bid</th>
                        <th>Bidding Start Time</th>
                        <th>BiddingEnd Time</th>
                        <th>Min Percent Value</th>
                        <th>Max Percent Value</th>
                        <th class="text-center">Execution Controls</th>
                    </tr>
                </thead>
                <tbody id="bidding_table_body">
                    </tbody>
            </table>
        </div>
    </div>
</div>

<div id="biddingFormModal" class="dashboard-popup-scaffolding">
    <div class="popup-modal-box" style="max-width: 650px;">
        <div class="popup-modal-header">
            <h4 style="color: white; font-size: 18px; font-weight: 600;"><i class="fas fa-clock" style="color: #ec4899; margin-right: 8px;"></i> <span id="modal_display_title">Configure Parameters</span></h4>
            <button onclick="closeBiddingModal()" style="background: none; border: none; color: #94a3b8; font-size: 28px; cursor: pointer;">&times;</button>
        </div>
        <form id="bidding_master_form" method="POST">
            @csrf
            <input type="hidden" name="bid_id" id="bid_id">
            
            <div class="popup-modal-body">
                <div class="input-form-row">
                    <label>Target Operational District *</label>
                    <select name="district_id" id="district_id" class="form-control select2-single" style="width:100%;" required>
                        <option value="">-- Choose Target District --</option>
                        @foreach($districts as $dist)
                            <option value="{{ $dist->id }}">{{ $dist->district_name }}</option>
                        @endforeach
                    </select>
                </div>

                <div class="row">
                    <div class="col-md-6 input-form-row">
                        <label>Window Start Time *</label>
                        <input type="time" name="bid_time_start" id="bid_time_start" required>
                    </div>
                    <div class="col-md-6 input-form-row">
                        <label>Window End Time *</label>
                        <input type="time" name="bid_time_end" id="bid_time_end" required>
                    </div>
                </div>

                <div class="row">
                    <div class="col-md-6 input-form-row">
                        <label>Minimum Percentage Cap (%) *</label>
                        <input type="number" step="0.01" name="bid_min_perc" id="bid_min_perc" required placeholder="e.g. 5.50">
                    </div>
                    <div class="col-md-6 input-form-row">
                        <label>Maximum Percentage Cap (%) *</label>
                        <input type="number" step="0.01" name="bid_max_perc" id="bid_max_perc" required placeholder="e.g. 25.00">
                    </div>
                </div>
            </div>

            <div class="popup-modal-footer">
                <button type="button" class="btn" style="background: #1e1e2a; color: #94a3b8; padding: 10px 20px; border-radius: 10px; font-weight:600;" onclick="closeBiddingModal()">Cancel</button>
                <button type="submit" class="btn" style="background: linear-gradient(135deg, #10b981, #059669); color: white; padding: 10px 24px; border-radius: 10px; font-weight:600;">Commit Rule</button>
            </div>
        </form>
    </div>
</div>
@endsection

@push('admin-scripts')
<script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js"></script>
<script>
    $(document).ready(function() {
        // Init Select2 configuration context inside our layout wrapper boundaries
        $('.select2-single').select2({
            dropdownParent: $('#biddingFormModal'),
            placeholder: "-- Choose Target District --",
            allowClear: true
        });

        // Pull baseline records directly
        fetchBiddingData();

        // Handle AJAX Form Submissions
        $('#bidding_master_form').on('submit', function(e) {
            e.preventDefault();
            
            $.ajax({
                url: "{{ route('admin.bidding.save') }}",
                type: "POST",
                data: $(this).serialize(),
                success: function(response) {
                    alert(response.success);
                    closeBiddingModal();
                    fetchBiddingData();
                },
                error: function() {
                    alert("Operation failed. Ensure parameters match logical numeric constraints.");
                }
            });
        });
    });

    function fetchBiddingData() {
        $.ajax({
            url: "{{ route('admin.bidding.index') }}",
            type: "GET",
            dataType: "json",
            success: function(data) {
                let html = '';
                if(data.bids.length > 0) {
                    data.bids.forEach(function(row) {
                        html += `
                            <tr class="border-bottom border-dark" style="border-color: #1e1e2a !important;">
                                <td style="color: #475569; font-weight:700; padding:15px 10px;">${row.id}</td>
                                <td class="fw-bold text-white"><i class="fas fa-map-marker-alt text-muted small me-2" ></i>${row.district_name}</td>
                                <td style="color: #cbd5e1;"><i class="far fa-clock text-muted me-2 padding-left: 25px"></i>${row.bid_time_start}</td>
                                <td style="color: #cbd5e1;"><i class="far fa-clock text-muted me-2"></i>${row.bid_time_end}</td>
                                <td><span class="percentage-pill" style="background: rgba(59, 130, 246, 0.1); color: #60a5fa; border-color: rgba(59, 130, 246, 0.2);">${row.bid_min_perc}%</span></td>
                                <td><span class="percentage-pill">${row.bid_max_perc}%</span></td>
                                <td class="text-center">
                                    <div class="d-flex justify-content-center align-items-center gap-2">
                                        <button class="btn btn-sm" style="border: 1px solid #3b82f6; color:#3b82f6; background:transparent; font-weight:600; border-radius:6px; padding: 4px 12px;" onclick="openBiddingModal('edit', ${row.id})">
                                            <i class="fas fa-edit"></i> Edit
                                        </button>
                                        <button class="btn btn-sm" style="border: 1px solid #ef4444; color:#ef4444; background:transparent; font-weight:600; border-radius:6px; padding: 4px 12px;" onclick="deleteBiddingNode(${row.id})">
                                            <i class="fas fa-trash-alt"></i> Delete
                                        </button>
                                    </div>
                                </td>
                            </tr>
                        `;
                    });
                } else {
                    html = '<tr><td colspan="7" class="text-center text-muted py-5">No localized bidding rule matrix structures registered yet.</td></tr>';
                }
                $('#bidding_table_body').html(html);
            }
        });
    }

    function openBiddingModal(mode, id = null) {
        $('#bidding_master_form')[0].reset();
        $('#bid_id').val('');
        $('#district_id').val(null).trigger('change');

        if(mode === 'create') {
            $('#modal_display_title').text('Establish Dynamic Bidding Rule Parameters');
            $('#biddingFormModal').css('display', 'flex');
        } else {
            $('#modal_display_title').text('Modify System Bidding Configurations');
            
            $.ajax({
                url: `/admin/bidding/edit/${id}`,
                type: "GET",
                success: function(res) {
                    $('#bid_id').val(res.bid.id);
                    $('#district_id').val(res.bid.district_id).trigger('change');
                    $('#bid_time_start').val(res.bid.bid_time_start);
                    $('#bid_time_end').val(res.bid.bid_time_end);
                    $('#bid_min_perc').val(res.bid.bid_min_perc);
                    $('#bid_max_perc').val(res.bid.bid_max_perc);
                    
                    $('#biddingFormModal').css('display', 'flex');
                }
            });
        }
    }

    function closeBiddingModal() {
        $('#biddingFormModal').hide();
    }

    function deleteBiddingNode(id) {
        if(confirm("Are you sure you want to completely clear this bidding rule constraint configuration frame?")) {
            $.ajax({
                url: `/admin/bidding/delete/${id}`,
                type: "POST",
                data: {
                    _token: "{{ csrf_token() }}",
                    _method: "DELETE"
                },
                success: function(response) {
                    alert(response.success);
                    fetchBiddingData();
                },
                error: function() {
                    alert("Failed to truncate index resource profile.");
                }
            });
        }
    }
</script>
@endpush