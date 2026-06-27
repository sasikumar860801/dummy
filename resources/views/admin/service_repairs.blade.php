@extends('admin.layout')

@section('title', 'Manage Service & Repairs')

@push('admin-styles')
<style>
    .admin-services-container {
        padding: 30px;
        background: #050508;
        min-height: 100vh;
    }
    .page-title { font-size: 24px; font-weight: 700; color: white; margin-bottom: 25px; }

    /* Admin Tabs */
    .admin-tabs { display: flex; gap: 10px; margin-bottom: 25px; border-bottom: 1px solid #1e1e2a; padding-bottom: 15px; }
    .admin-tab-btn { background: #111118; border: 1px solid #1e1e2a; color: #64748b; padding: 10px 24px; border-radius: 8px; font-weight: 600; cursor: pointer; transition: 0.2s; }
    .admin-tab-btn:hover { background: #1a1a2e; color: white; }
    .admin-tab-btn.active { background: #3b82f6; color: white; border-color: #3b82f6; }
    
    .tab-content { display: none; }
    .tab-content.active { display: block; animation: fadeIn 0.3s ease; }
    @keyframes fadeIn { from { opacity: 0; transform: translateY(5px); } to { opacity: 1; transform: translateY(0); } }

    /* Admin Service Card */
    .admin-card {
        background: #111118;
        border: 1px solid #1e1e2a;
        border-radius: 12px;
        padding: 20px;
        margin-bottom: 15px;
        display: grid;
        grid-template-columns: 2fr 1.5fr auto;
        gap: 20px;
        align-items: center;
    }

    .details-block h4 { color: white; margin: 0 0 8px 0; font-size: 16px; }
    .details-block p { color: #94a3b8; font-size: 13px; margin: 0 0 4px 0; }
    
    .issue-badge { display: inline-block; background: rgba(236, 72, 153, 0.1); color: #ec4899; border: 1px solid rgba(236, 72, 153, 0.2); padding: 4px 10px; border-radius: 6px; font-size: 12px; font-weight: bold; margin-top: 8px; }

    .buyer-info { background: rgba(59, 130, 246, 0.05); border: 1px solid rgba(59, 130, 246, 0.1); padding: 12px 15px; border-radius: 8px; }
    .buyer-info strong { display: block; color: #e2e8f0; font-size: 14px; margin-bottom: 4px; }
    .buyer-info span { display: block; color: #94a3b8; font-size: 12px; margin-bottom: 2px; }
    
    .action-block { text-align: right; }
    .status-badge { padding: 5px 12px; border-radius: 20px; font-size: 11px; font-weight: 600; text-transform: uppercase; }
    .status-pending { background: rgba(234, 179, 8, 0.1); color: #eab308; border: 1px solid rgba(234, 179, 8, 0.2); }
    .status-completed { background: rgba(16, 185, 129, 0.1); color: #10b981; border: 1px solid rgba(16, 185, 129, 0.2); }
    .empty-state { text-align: center; padding: 60px 20px; color: #64748b; background: #111118; border-radius: 12px; border: 1px dashed #1e1e2a; }
</style>
@endpush

@section('admin-content')
<div class="admin-services-container">
    <h1 class="page-title"><i class="fas fa-tools" style="color: #ec4899; margin-right: 10px;"></i> Service & Repair Tickets</h1>

    <div class="admin-tabs">
        <button class="admin-tab-btn active" onclick="switchAdminTab('pending')">Pending Tickets ({{ collect($pendingServices)->count() }})</button>
        <button class="admin-tab-btn" onclick="switchAdminTab('completed')">Completed Tickets ({{ collect($completedServices)->count() }})</button>
    </div>

    <div id="pending" class="tab-content active">
        @forelse($pendingServices as $service)
            <div class="admin-card">
                <div class="details-block">
                    <h4>{{ $service->model_title ?? 'Unknown Model' }}</h4>
                    <p>Ticket ID: <strong>{{ $service->service_id }}</strong></p>
                    <p>Date: {{ \Carbon\Carbon::parse($service->created_at)->format('M d, Y h:i A') }}</p>
                    <div class="issue-badge">{{ $service->category_name }} > {{ $service->subcategory_name }}</div>
                </div>

                <div class="buyer-info">
                    <strong><i class="fas fa-user"></i> {{ $service->name }}</strong>
                    <span><i class="fas fa-phone"></i> {{ $service->phone }} {{ $service->alt_phone ? ' / '.$service->alt_phone : '' }}</span>
                    <span><i class="fas fa-map-marker-alt"></i> {{ $service->pincode }} - {{ Str::limit($service->address, 30) }}</span>
                </div>

                <div class="action-block">
                    <span class="status-badge status-pending">Pending</span>
                </div>
            </div>
        @empty
            <div class="empty-state">
                <i class="fas fa-check-circle" style="font-size: 48px; margin-bottom: 15px; opacity: 0.3;"></i>
                <h3>No Pending Tickets</h3>
            </div>
        @endforelse
    </div>

    <div id="completed" class="tab-content">
        @forelse($completedServices as $service)
            <div class="admin-card" style="opacity: 0.8;">
                <div class="details-block">
                    <h4>{{ $service->model_title ?? 'Unknown Model' }}</h4>
                    <p>Ticket ID: <strong>{{ $service->service_id }}</strong></p>
                    <p>Completed on: {{ \Carbon\Carbon::parse($service->updated_at)->format('M d, Y') }}</p>
                    <div class="issue-badge">{{ $service->category_name }} > {{ $service->subcategory_name }}</div>
                </div>

                <div class="buyer-info">
                    <strong><i class="fas fa-user"></i> {{ $service->name }}</strong>
                    <span><i class="fas fa-phone"></i> {{ $service->phone }}</span>
                </div>

                <div class="action-block">
                    <span class="status-badge status-completed">Completed</span>
                </div>
            </div>
        @empty
            <div class="empty-state">
                <i class="fas fa-inbox" style="font-size: 48px; margin-bottom: 15px; opacity: 0.3;"></i>
                <h3>No Completed Tickets</h3>
            </div>
        @endforelse
    </div>
</div>
@endsection

@push('admin-scripts')
<script>
    function switchAdminTab(tabId) {
        document.querySelectorAll('.admin-tab-btn').forEach(btn => btn.classList.remove('active'));
        document.querySelectorAll('.tab-content').forEach(content => content.classList.remove('active'));
        event.target.classList.add('active');
        document.getElementById(tabId).classList.add('active');
    }
</script>
@endpush