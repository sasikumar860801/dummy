@extends('admin.layout')

@section('title', 'Incomplete Orders - Admin Panel')

@push('admin-styles')
<style>
    .admin-list-container {
        padding: 30px;
        background: #050508;
        min-height: 100vh;
    }
    .page-header {
        margin-bottom: 30px;
    }
    .page-title {
        font-size: 24px;
        font-weight: 700;
        color: white;
        margin: 0 0 10px 0;
    }
    .page-subtitle {
        color: #94a3b8;
        font-size: 14px;
        margin: 0;
    }

    /* Dark Table Styles */
    .table-wrapper {
        background: #111118;
        border: 1px solid #1e1e2a;
        border-radius: 12px;
        overflow-x: auto;
    }
    .admin-table {
        width: 100%;
        border-collapse: collapse;
        text-align: left;
    }
    .admin-table th {
        background: #1a1a2e;
        color: #94a3b8;
        font-size: 12px;
        font-weight: 600;
        text-transform: uppercase;
        letter-spacing: 0.5px;
        padding: 15px 20px;
        border-bottom: 1px solid #1e1e2a;
    }
    .admin-table td {
        padding: 15px 20px;
        border-bottom: 1px solid #1e1e2a;
        vertical-align: top;
    }
    .admin-table tbody tr:hover {
        background: rgba(59, 130, 246, 0.05);
    }
    .admin-table tbody tr:last-child td {
        border-bottom: none;
    }

    /* Cell Content Styling */
    .user-info strong { display: block; color: #e2e8f0; font-size: 14px; margin-bottom: 4px; }
    .user-info span { display: block; color: #64748b; font-size: 12px; margin-bottom: 2px; }
    .user-info i { color: #3b82f6; width: 14px; margin-right: 4px; }

    .address-box { color: #94a3b8; font-size: 13px; line-height: 1.5; max-width: 250px; }
    .address-box strong { color: #cbd5e1; font-weight: 500; }

    .device-tag {
        display: inline-block;
        background: rgba(59, 130, 246, 0.1);
        color: #3b82f6;
        border: 1px solid rgba(59, 130, 246, 0.2);
        padding: 6px 12px;
        border-radius: 6px;
        font-size: 13px;
        font-weight: 600;
        margin-bottom: 5px;
    }
    .capacity-tag {
        display: inline-block;
        background: #1a1a2e;
        color: #cbd5e1;
        padding: 4px 10px;
        border-radius: 4px;
        font-size: 12px;
    }

    .empty-state { text-align: center; padding: 60px 20px; color: #64748b; }
    
    /* Follow-up Button */
    .btn-action {
        background: transparent;
        border: 1px solid #3b82f6;
        color: #3b82f6;
        padding: 6px 12px;
        border-radius: 6px;
        font-size: 12px;
        cursor: pointer;
        transition: 0.3s;
        text-decoration: none;
        display: inline-block;
    }
    .btn-action:hover { background: #3b82f6; color: white; }
</style>
@endpush

@section('admin-content')
<div class="admin-list-container">
    <div class="page-header">
        <h1 class="page-title"><i class="fas fa-user-clock" style="color: #3b82f6; margin-right: 10px;"></i> Incomplete Orders</h1>
        <p class="page-subtitle">List of users who added items to their cart but abandoned the checkout process.</p>
    </div>

    <div class="table-wrapper">
        <table class="admin-table">
            <thead>
                <tr>
                    <th>Customer Details</th>
                    <th>Location & Address</th>
                    <th>Pending Device</th>
                    <th>Action</th>
                </tr>
            </thead>
            <tbody>
                @forelse($abandonedOrders as $order)
                    <tr>
                        <!-- User Contact Info -->
                        <td class="user-info">
                            <strong>{{ $order->name ?? 'Unknown User' }}</strong>
                            <span><i class="fas fa-phone"></i> {{ $order->phone }}</span>
                            @if($order->alternate_mob_no)
                                <span><i class="fas fa-mobile-alt"></i> {{ $order->alternate_mob_no }} (Alt)</span>
                            @endif
                            @if($order->email)
                                <span><i class="fas fa-envelope"></i> {{ $order->email }}</span>
                            @endif
                        </td>

                        <!-- Location Info -->
                        <td>
                            <div class="address-box">
                                {{ $order->address ?? 'No address provided' }}<br>
                                <strong>District:</strong> {{ $order->district_name ?? 'N/A' }}<br>
                                <strong>Pincode:</strong> {{ $order->pincode ?? 'N/A' }}
                            </div>
                        </td>

                        <!-- Device Info -->
                        <td>
                            <span class="device-tag">{{ $order->model_name ?? 'Unknown Model' }}</span><br>
                            <span class="capacity-tag">{{ $order->capacity ?? 'N/A' }}</span>
                        </td>

                        <!-- Actions (e.g., call them to follow up) -->
                        <td style="vertical-align: middle;">
                            <a href="tel:{{ $order->phone }}" class="btn-action">
                                <i class="fas fa-phone-alt"></i> Follow Up
                            </a>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="4">
                            <div class="empty-state">
                                <i class="fas fa-check-circle" style="font-size: 40px; margin-bottom: 15px; opacity: 0.3;"></i>
                                <h3>All Caught Up</h3>
                                <p>There are no incomplete orders or abandoned carts at the moment.</p>
                            </div>
                        </td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>
</div>
@endsection