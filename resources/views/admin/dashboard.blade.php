@extends('admin.layout')

@section('title', 'Admin Dashboard')

@section('admin-content')
<div>
    <h2 class="gradient-text" style="font-size: 28px; font-weight: 700; margin-bottom: 6px;">Dashboard Overview</h2>
    <p style="color: #64748b; font-size: 14px; margin-bottom: 30px;">System status metrics, monitoring indexes, and operational modules overview panel.</p>

    <div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(240px, 1fr)); gap: 24px;">
        
        <div style="background: #111118; border: 1px solid #1e1e2a; border-radius: 20px; padding: 24px;">
            <div style="color: #64748b; font-size: 13px; font-weight: 600; margin-bottom: 10px; text-transform: uppercase; letter-spacing: 0.5px;">Pending Inbound Volume</div>
            <h3 style="font-size: 32px; font-weight: 800; color: white;">--</h3>
        </div>

        <div style="background: #111118; border: 1px solid #1e1e2a; border-radius: 20px; padding: 24px;">
            <div style="color: #64748b; font-size: 13px; font-weight: 600; margin-bottom: 10px; text-transform: uppercase; letter-spacing: 0.5px;">Active Stock Catalog</div>
            <h3 style="font-size: 32px; font-weight: 800; color: white;">--</h3>
        </div>

        <div style="background: #111118; border: 1px solid #1e1e2a; border-radius: 20px; padding: 24px;">
            <div style="color: #64748b; font-size: 13px; font-weight: 600; margin-bottom: 10px; text-transform: uppercase; letter-spacing: 0.5px;">Completed Evaluations</div>
            <h3 style="font-size: 32px; font-weight: 800; color: white;">--</h3>
        </div>

    </div>
</div>
@endsection