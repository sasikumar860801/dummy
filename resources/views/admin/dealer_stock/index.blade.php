@extends('admin.layout')

@section('title', 'Dealer Stock Terminal')

@push('admin-styles')
<link href="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css" rel="stylesheet" />
<style>
    .stock-card-grid { background: #111118; border: 1px solid #1e1e2a; border-radius: 20px; padding: 24px; }
    
    .tab-trigger-btn { background: none; border: none; color: #64748b; padding: 12px 24px; font-size: 15px; font-weight: 600; cursor: pointer; transition: 0.2s; display: flex; align-items: center; gap: 8px; border-bottom: 2px solid transparent; }
    .tab-trigger-btn:hover { color: #cbd5e1; }
    .tab-trigger-btn.active { color: #ec4899; border-color: #ec4899; }
    .tab-badge-count { font-size: 11px; font-weight: 700; color: white; padding: 2px 8px; border-radius: 20px; }
    
    .tab-view-panel { display: none; }
    .tab-view-panel.active { display: table-row-group; }
    
    .media-thumb { width: 50px; height: 50px; object-fit: cover; border-radius: 8px; border: 1px solid #2a2a3a; margin-right: 6px; }

    .dashboard-popup-scaffolding { position: fixed; top: 0; left: 0; width: 100vw; height: 100vh; background: rgba(5,5,8,0.85); backdrop-filter: blur(8px); z-index: 99999; display: none; justify-content: center; align-items: center; padding: 20px; }
    .popup-modal-box { background: #111118; border: 1px solid #2a2a3a; width: 100%; border-radius: 24px; overflow: hidden; display: flex; flex-direction: column; }
    .popup-modal-header { padding: 20px 24px; border-bottom: 1px solid #1e1e2a; display: flex; align-items: center; justify-content: space-between; }
    .popup-modal-body { padding: 24px; max-height: 75vh; overflow-y: auto; }
    .popup-modal-footer { padding: 16px 24px; background: #0f0f15; border-top: 1px solid #1e1e2a; display: flex; justify-content: flex-end; gap: 12px; }
    
    .input-form-row { margin-bottom: 16px; }
    .input-form-row label { display: block; font-size: 13px; font-weight: 500; color: #94a3b8; margin-bottom: 6px; }
    .input-form-row input, .input-form-row select { width: 100%; padding: 12px; background: #1a1a2e; border: 1px solid #2a2a3a; border-radius: 10px; color: white; outline: none; }
</style>
@endpush

@section('admin-content')

<style>
    /* Fix Cluttered Row Spacing and Structural Layout */
#pending_panel tr, #approved_panel tr {
    border-bottom: 1px solid #1e1e2a !important;
    transition: background 0.2s ease;
}
#pending_panel tr:hover, #approved_panel tr:hover {
    background: rgba(30, 30, 42, 0.4);
}
#pending_panel td, #approved_panel td {
    padding: 20px 14px !important; /* Fixed compact layout */
    vertical-align: middle;
}

/* Modernized Custom Button Layouts */
.action-btn-group {
    display: flex;
    align-items: center;
    gap: 8px;
    justify-content: center;
}
.btn-action-panel {
    border: none;
    padding: 8px 14px;
    font-size: 13px;
    font-weight: 600;
    border-radius: 8px;
    cursor: pointer;
    display: inline-flex;
    align-items: center;
    gap: 6px;
    transition: all 0.2s ease;
}
.btn-approve-style {
    background: rgba(16, 185, 129, 0.15);
    color: #10b981;
    border: 1px solid rgba(16, 185, 129, 0.3);
}
.btn-approve-style:hover {
    background: #10b981;
    color: white;
}
.btn-reject-style {
    background: rgba(239, 68, 68, 0.15);
    color: #ef4444;
    border: 1px solid rgba(239, 68, 68, 0.3);
}
.btn-reject-style:hover {
    background: #ef4444;
    color: white;
}
.btn-edit-style {
    background: rgba(59, 130, 246, 0.15);
    color: #3b82f6;
    border: 1px solid rgba(59, 130, 246, 0.3);
}
.btn-edit-style:hover {
    background: #3b82f6;
    color: white;
}

/* Clickable Interactive Media Thumbnail Styles */
.media-preview-container {
    display: flex;
    gap: 6px;
    flex-wrap: wrap;
}
.media-thumb-clickable {
    width: 52px;
    height: 52px;
    object-fit: cover;
    border-radius: 8px;
    border: 1px solid #2a2a3a;
    cursor: pointer;
    transition: transform 0.2s, border-color 0.2s;
}
.media-thumb-clickable:hover {
    transform: scale(1.08);
    border-color: #ec4899;
}

/* Lightbox Dynamic Media Viewer Overlay Scaffolding */
.media-lightbox-overlay {
    position: fixed;
    top: 0; left: 0; width: 100vw; height: 100vh;
    background: rgba(5, 5, 8, 0.9);
    backdrop-filter: blur(10px);
    z-index: 100000;
    display: none;
    justify-content: center;
    align-items: center;
}
.lightbox-content-box {
    position: relative;
    max-width: 85vw;
    max-height: 85vh;
}
.lightbox-content-box img, .lightbox-content-box video {
    max-width: 100%;
    max-height: 85vh;
    border-radius: 12px;
    box-shadow: 0 25px 50px -12px rgba(0,0,0,0.8);
}
.lightbox-close-trigger {
    position: absolute;
    top: -45px;
    right: 0;
    background: none;
    border: none;
    color: #94a3b8;
    font-size: 32px;
    cursor: pointer;
    transition: color 0.2s;
}
.lightbox-close-trigger:hover { color: #white; }
</style>
<div class="container-fluid p-0">
    
    <div style="margin-bottom: 25px; display: flex; justify-content: space-between; align-items: center; flex-wrap: wrap; gap: 15px;">
        <div>
            <h2 class="gradient-text" style="font-size: 28px; font-weight: 700; margin-bottom: 5px;">Dealer Ingest Control Desk</h2>
            <p style="color: #64748b; font-size: 14px;">Review pending pipeline stock requests and evaluate approved catalog records.</p>
        </div>
        <button class="btn" style="background: linear-gradient(135deg, #ec4899, #8b5cf6); border: none; color: white; padding: 12px 24px; font-weight: 600; border-radius: 10px;" onclick="openStockModal('create')">
            <i class="fas fa-plus"></i> Manual Add Stock
        </button>
    </div>

    <div style="display: flex; gap: 10px; border-bottom: 1px solid #1e1e2a; padding-bottom: 1px; margin-bottom: 30px;">
        <button class="tab-trigger-btn active" onclick="switchContextTab(event, 'pending_panel')">
            Pending Stock <span class="tab-badge-count" style="background: #3b82f6;">{{ $stocks->where('is_approved', 0)->count() }}</span>
        </button>
        <button class="tab-trigger-btn" onclick="switchContextTab(event, 'approved_panel')">
            Approved Stock <span class="tab-badge-count" style="background: #10b981;">{{ $stocks->where('is_approved', 1)->count() }}</span>
        </button>
    </div>

    <div class="bidding-card-grid">
        <div class="table-responsive">
            <table class="table table-dark table-hover align-middle border-0 mb-0" style="--bs-table-bg: transparent;">
                <thead>
                    <tr class="text-muted border-bottom border-secondary" style="font-size: 12px; text-transform: uppercase;">
                        <th style="padding: 15px 10px;">Order ID</th>
                        <th>Media</th>
                        <th>Model details</th>
                        <th>Dealer / Shop Name</th>
                        <th>Buy Price</th>
                        <th>IMEI References</th>
                        <th>Warranty</th>
                        <th class="text-center">Execution Controls</th>
                    </tr>
                </thead>
                
                <tbody id="pending_panel" class="tab-view-panel active">
                 @forelse($stocks->where('is_approved', 0) as $row)
                    <tr class="row-node-{{ $row->id }}">
                        <td style="color:#ec4899; font-weight:700; font-size:14px;">{{ $row->order_id }}</td>
                        <td>
                            <div class="media-preview-container">
                                @php $images = json_decode($row->media, true) ?? []; @endphp
                                @forelse($images as $img)
                                    @php $extension = pathinfo($img, PATHINFO_EXTENSION); @endphp
                                    @if(in_array(strtolower($extension), ['mp4', 'webm', 'ogg']))
                                        <video src="{{ asset($img) }}" class="media-thumb-clickable" onclick="launchLightbox('{{ asset($img) }}', 'video')"></video>
                                    @else
                                        <img src="{{ asset($img) }}" class="media-thumb-clickable" onclick="launchLightbox('{{ asset($img) }}', 'image')" onerror="this.src='https://via.placeholder.com/150'">
                                    @endif
                                @empty
                                    <span class="text-muted small">No Media</span>
                                @endforelse
                            </div>
                        </td>
                        <td>
                            <div class="text-white fw-bold" style="font-size: 14px;">{{ $row->title }}</div>
                            <div style="font-size: 12px; color: #94a3b8; margin-top: 2px;">{{ $row->capacity }} • <span class="text-capitalize">{{ $row->color }}</span></div>
                        </td>
                        <td>
    <div class="text-white fw-bold" style="font-size: 14px;">{{ $row->dealer_name }}</div>
    <div style="font-size: 12px; color: #64748b; margin-top: 2px; line-height: 1.4;">
        {{ $row->shop_name }} • <span style="color: #cbd5e1;">{{ $row->dealer_phone }}</span>
        <div style="color: #a855f7; font-size: 11px; text-transform: capitalize; margin-top: 2px;">
            <i class="fas fa-map-marker-alt" style="font-size: 10px; margin-right: 2px;"></i> 
            {{ strtolower($row->district_names) }}
        </div>
    </div>
</td>
                        <td>
                            <div style="color: #10b981; font-weight: 700; font-size: 14px;">${{ number_format($row->buy_price, 2) }}</div>
                        </td>
                        <td style="font-size:12px; color: #94a3b8; line-height: 1.5;">
                            <div><span class="text-muted">1:</span> {{ $row->imei_no_1 }}</div>
                            @if($row->imei_no_2) <div><span class="text-muted">2:</span> {{ $row->imei_no_2 }}</div> @endif
                        </td>
                        <td style="font-size:13px; color: #cbd5e1;">{{ $row->warranty ?? 'N/A' }}</td>
                        <td>
                            <div class="action-btn-group">
                                <button class="btn-action-panel btn-approve-style" onclick="approveAsset({{ $row->id }})">
                                    <i class="fas fa-check-circle"></i> Approve
                                </button>
                                <button class="btn-action-panel btn-reject-style" onclick="rejectOrDeleteAsset({{ $row->id }})">
                                    <i class="fas fa-times-circle"></i> Reject
                                </button>
                                <button class="btn-action-panel btn-edit-style" onclick="openStockModal('edit', {{ $row->id }})" title="Modify details">
                                    <i class="fas fa-edit"></i>
                                </button>
                            </div>
                        </td>
                    </tr>
                @empty
                    <tr><td colspan="8" class="text-center py-5 text-muted">No pending stock requests found.</td></tr>
                @endforelse
                </tbody>

                <tbody id="approved_panel" class="tab-view-panel">
                   @forelse($stocks->where('is_approved', 1) as $row)
                        <tr class="row-node-{{ $row->id }}">
                            <td style="color:#ec4899; font-weight:700; font-size:14px;">{{ $row->order_id }}</td>
                            <td>
                                <div class="media-preview-container">
                                    @php $images = json_decode($row->media, true) ?? []; @endphp
                                    @forelse($images as $img)
                                        @php $extension = pathinfo($img, PATHINFO_EXTENSION); @endphp
                                        @if(in_array(strtolower($extension), ['mp4', 'webm', 'ogg']))
                                            <video src="{{ asset($img) }}" class="media-thumb-clickable" onclick="launchLightbox('{{ asset($img) }}', 'video')"></video>
                                        @else
                                            <img src="{{ asset($img) }}" class="media-thumb-clickable" onclick="launchLightbox('{{ asset($img) }}', 'image')" onerror="this.src='https://via.placeholder.com/150'">
                                        @endif
                                    @empty
                                        <span class="text-muted small">No Media</span>
                                    @endforelse
                                </div>
                            </td>
                            <td>
                                <div class="text-white fw-bold" style="font-size: 14px;">{{ $row->title }}</div>
                                <div style="font-size: 12px; color: #94a3b8; margin-top: 2px;">{{ $row->capacity }} • <span class="text-capitalize">{{ $row->color }}</span></div>
                            </td>
                                            <td>
                                            <div class="text-white fw-bold" style="font-size: 14px;">{{ $row->dealer_name }}</div>
                                            <div style="font-size: 12px; color: #64748b; margin-top: 2px; line-height: 1.4;">
                                                {{ $row->shop_name }} • <span style="color: #cbd5e1;">{{ $row->dealer_phone }}</span>
                                                <div style="color: #a855f7; font-size: 11px; text-transform: capitalize; margin-top: 2px;">
                                                    <i class="fas fa-map-marker-alt" style="font-size: 10px; margin-right: 2px;"></i> 
                                                    {{ strtolower($row->district_names) }}
                                                </div>
                                            </div>
                                        </td>
                            <td>
                                <div style="color: #10b981; font-weight: 700; font-size: 14px;">${{ number_format($row->buy_price, 2) }}</div>
                            </td>
                            <td style="font-size:12px; color: #94a3b8; line-height: 1.5;">
                                <div><span class="text-muted">1:</span> {{ $row->imei_no_1 }}</div>
                                @if($row->imei_no_2) <div><span class="text-muted">2:</span> {{ $row->imei_no_2 }}</div> @endif
                            </td>
                            <td style="font-size:13px; color: #cbd5e1;">{{ $row->warranty ?? 'N/A' }}</td>
                            <td class="text-center">
                                <button class="btn-action-panel btn-reject-style" onclick="rejectOrDeleteAsset({{ $row->id }})">
                                    <i class="fas fa-trash-alt"></i> Delete Record
                                </button>
                            </td>
                        </tr>
                    @empty
                        <tr><td colspan="8" class="text-center py-5 text-muted">No approved catalog records tracked.</td></tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
</div>

<div id="stockFormModal" class="dashboard-popup-scaffolding">
    <div class="popup-modal-box" style="max-width: 700px;">
        <div class="popup-modal-header">
            <h4 style="color: white; font-size: 18px; font-weight: 600;"><i class="fas fa-box" style="color: #ec4899; margin-right: 8px;"></i> <span id="modal_display_title">Configure Stock Node</span></h4>
            <button onclick="closeStockModal()" style="background: none; border: none; color: #94a3b8; font-size: 26px; cursor: pointer;">&times;</button>
        </div>
        <form id="stock_master_form" method="POST" enctype="multipart/form-data">
            @csrf
            <input type="hidden" name="stock_id" id="stock_id">
            <div class="popup-modal-body">
                <div class="row">
                    <div class="col-md-6 input-form-row">
                        <label>Target Source Dealer *</label>
                        <select name="dealer_id" id="dealer_id" style="width: 100%;" required>
                            @foreach($dealers as $dealer)
                                <option value="{{ $dealer->id }}">{{ $dealer->name }} [{{ $dealer->shop_name }}]</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="col-md-6 input-form-row">
                        <label>Product Model Asset *</label>
                        <select name="model_id" id="model_id" style="width: 100%;">
                            @foreach($models as $model)
                                <option value="{{ $model->id }}">{{ $model->title }}</option>
                            @endforeach
                        </select>
                    </div>
                </div>
                <div class="row">
                    <div class="col-md-6 input-form-row">
                        <label>Capacity Allocation</label>
                        <input type="text" name="capacity" id="capacity" placeholder="e.g. 8GB/128GB">
                    </div>
                    <div class="col-md-6 input-form-row">
                        <label>Color Specification</label>
                        <input type="text" name="color" id="color" required>
                    </div>
                </div>
                <div class="row">
                    <div class="col-md-6 input-form-row">
                        <label>Buy Valuation Price *</label>
                        <input type="number" step="0.01" name="buy_price" id="buy_price" required>
                    </div>
                    <div class="col-md-6 input-form-row">
                        <label>Assigned Selling Target Price</label>
                        <input type="number" step="0.01" name="sell_price" id="sell_price">
                    </div>
                </div>
                <div class="row">
                    <div class="col-md-6 input-form-row">
                        <label>Primary IMEI Number (Slot 1) *</label>
                        <input type="text" name="imei_no_1" id="imei_no_1" required>
                    </div>
                    <div class="col-md-6 input-form-row">
                        <label>Secondary IMEI Number (Slot 2)</label>
                        <input type="text" name="imei_no_2" id="imei_no_2">
                    </div>
                </div>
                <div class="input-form-row">
                    <label>Warranty Scope Context</label>
                    <input type="text" name="warranty" id="warranty">
                </div>
                <div class="input-form-row">
                    <label>Media Inventory Uploads</label>
                    <input type="file" name="media_files[]" id="media_files" multiple style="padding: 8px;">
                </div>
            </div>
            <div class="popup-modal-footer">
                <button type="button" class="btn" style="background: #1a1a2e; color: white;" onclick="closeStockModal()">Abort</button>
                <button type="submit" class="btn" style="background: linear-gradient(135deg, #10b981, #059669); color: white;">Save Stock Asset</button>
            </div>
        </form>
    </div>
</div>

<div id="mediaLightboxModal" class="media-lightbox-overlay" onclick="closeLightbox()">
    <div class="lightbox-content-box" onclick="event.stopPropagation()">
        <button class="lightbox-close-trigger" onclick="closeLightbox()">&times;</button>
        <div id="lightbox_resource_render_hook"></div>
    </div>
</div>

@endsection

@push('admin-scripts')
<script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js"></script>
<script>
    $(document).ready(function() {
        $('#dealer_id, #model_id').select2({ dropdownParent: $('#stockFormModal') });

        $('#stock_master_form').on('submit', function(e) {
            e.preventDefault();
            let fd = new FormData(this);
            $.ajax({
                url: "{{ route('admin.dealerStock.save') }}",
                method: "POST",
                data: fd,
                processData: false,
                contentType: false,
                success: function(res) {
                    alert(res.success);
                    location.reload(); // Simple reload to refresh direct blade layout loop counters securely
                }
            });
        });
    });

    function switchContextTab(event, tabId) {
        $('.tab-view-panel').removeClass('active');
        $('.tab-trigger-btn').removeClass('active');
        $('#' + tabId).addClass('active');
        $(event.currentTarget).addClass('active');
    }

    function openStockModal(mode, id = null) {
        $('#stock_master_form')[0].reset();
        $('#stock_id').val('');
        $('#dealer_id, #model_id').val(null).trigger('change');

        if(mode === 'create') {
            $('#modal_display_title').text('Provision Manual Stock Record Block');
            $('#stockFormModal').css('display', 'flex');
        } else {
            $('#modal_display_title').text('Update Stock Matrix Context');
            $.ajax({
                url: `/admin/dealer-stock/edit/${id}`,
                method: "GET",
                success: function(res) {
                    $('#stock_id').val(res.stock.id);
                    $('#dealer_id').val(res.stock.dealer_id).trigger('change');
                    $('#model_id').val(res.stock.model_id).trigger('change');
                    $('#capacity').val(res.stock.capacity);
                    $('#color').val(res.stock.color);
                    $('#buy_price').val(res.stock.buy_price);
                    $('#sell_price').val(res.stock.sell_price);
                    $('#imei_no_1').val(res.stock.imei_no_1);
                    $('#imei_no_2').val(res.stock.imei_no_2);
                    $('#warranty').val(res.stock.warranty);
                    $('#stockFormModal').css('display', 'flex');
                }
            });
        }
    }

    function closeStockModal() { $('#stockFormModal').hide(); }

    function approveAsset(id) {
        if(confirm("Approve this stock asset submission?")) {
            $.ajax({
                url: `/admin/dealer-stock/approve/${id}`,
                method: "POST",
                data: { _token: "{{ csrf_token() }}" },
                success: function(res) { 
                    alert(res.success); 
                    location.reload();
                }
            });
        }
    }

    function rejectOrDeleteAsset(id) {
        if(confirm("Are you sure you want to delete this stock profile and completely erase its media files?")) {
            $.ajax({
                url: `/admin/dealer-stock/reject/${id}`,
                method: "POST",
                data: { _token: "{{ csrf_token() }}", _method: "DELETE" },
                success: function(res) { 
                    alert(res.success); 
                    $(`.row-node-${id}`).remove();
                    location.reload();
                }
            });
        }
    }

    function launchLightbox(resourcePath, type) {
    let targetHtml = '';
    if(type === 'video') {
        targetHtml = `<video src="${resourcePath}" controls autoplay style="max-height:80vh; width:auto;"></video>`;
    } else {
        targetHtml = `<img src="${resourcePath}" alt="Enlarged Inventory Asset Snapshot">`;
    }
    
    $('#lightbox_resource_render_hook').html(targetHtml);
    $('#mediaLightboxModal').css('display', 'flex');
}

function closeLightbox() {
    $('#mediaLightboxModal').hide();
    $('#lightbox_resource_render_hook').empty(); // Stops playing audio tracking blocks if video is closed
}

</script>
@endpush