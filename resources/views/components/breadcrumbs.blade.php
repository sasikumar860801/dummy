@if(!isset($hideBreadcrumbs) || !$hideBreadcrumbs)
<div style="padding: 15px 0; font-size: 13px;">
    <nav aria-label="breadcrumb">
        <ol style="display: flex; gap: 8px; list-style: none; flex-wrap: wrap;">
            <li><a href="{{ url('/') }}" style="color: #3b82f6; text-decoration: none;">Home</a></li>
            @if(isset($breadcrumbs))
                @foreach($breadcrumbs as $crumb)
                    <li><i class="fas fa-chevron-right" style="font-size: 10px; color: #64748b;"></i></li>
                    <li><a href="{{ $crumb['url'] }}" style="color: #cbd5e1; text-decoration: none;">{{ $crumb['name'] }}</a></li>
                @endforeach
            @else
                <li><i class="fas fa-chevron-right" style="font-size: 10px; color: #64748b;"></i></li>
                <li style="color: #64748b;">Buy & Sell Refurbished Devices</li>
            @endif
        </ol>
    </nav>
</div>
@endif