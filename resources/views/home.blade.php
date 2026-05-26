@extends('layouts.app')

@section('title', 'RevoDevice - Buy & Sell Certified Refurbished Mobiles, Laptops & Tablets | Best Prices in India')

@section('meta_description', 'India\'s most trusted platform to buy certified refurbished iPhones, Samsung, MacBooks, and sell old gadgets. Get instant cash, 12 months warranty, free doorstep pickup.')

@section('meta_keywords', 'refurbished phones, sell old phone online, buy refurbished laptop, second hand mobile India, certified refurbished devices, RevoDevice')

@section('og_title', 'RevoDevice - Buy Certified Refurbished Devices & Sell Old Gadgets Instantly')

@section('og_description', 'Get best prices for your old phone, laptop, tablet. Buy quality refurbished devices with warranty and free pickup.')

@section('twitter_title', 'RevoDevice - Buy & Sell Refurbished Electronics')

@section('twitter_description', 'Sell your old devices for instant cash or buy certified refurbished gadgets.')

@section('canonical_url', url('/'))

@section('content')

<div class="container">
    @include('components.breadcrumbs')

    @include('components.hero-banner')

    @include('components.categories')

    @include('components.brands')

    @include('components.featured-products')

    <div class="info-grid">
        @include('components.why-buy')
        @include('components.why-sell')
    </div>

    @include('components.customer-feedback')

</div>

@endsection


@push('styles')
<style>
.info-grid{
    display:grid;
    grid-template-columns:1fr 1fr;
    gap:30px;
    margin:60px 0;
}

@media (max-width:768px){
    .info-grid{
        grid-template-columns:1fr;
    }
}
</style>
@endpush


@push('scripts')

@verbatim
<script type="application/ld+json">
{
    "@context":"https://schema.org",
    "@type":"WebSite",
    "name":"RevoDevice",
    "url":"{{ url('/') }}",
    "description":"Buy certified refurbished devices and sell old gadgets online in India",
    "potentialAction":{
        "@type":"SearchAction",
        "target":{
            "@type":"EntryPoint",
            "urlTemplate":"{{ url('/search?q={search_term_string}') }}"
        },
        "query-input":"required name=search_term_string"
    }
}
</script>

<script type="application/ld+json">
{
    "@context":"https://schema.org",
    "@type":"ItemList",
    "name":"Featured Products",
    "description":"Best selling refurbished devices on RevoDevice",
    "numberOfItems":3,
    "itemListElement":[
        {
            "@type":"ListItem",
            "position":1,
            "name":"iPhone 13 Refurbished",
            "url":"{{ url('/product/iphone-13') }}"
        },
        {
            "@type":"ListItem",
            "position":2,
            "name":"MacBook Air M1 Refurbished",
            "url":"{{ url('/product/macbook-air-m1') }}"
        },
        {
            "@type":"ListItem",
            "position":3,
            "name":"iPad Pro Refurbished",
            "url":"{{ url('/product/ipad-pro') }}"
        }
    ]
}
</script>
@endverbatim

@endpush