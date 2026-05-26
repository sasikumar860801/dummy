@extends('layouts.app')

@section('title', $seo['title'])

@section('meta_description', $seo['meta_description'])

@section('meta_keywords', $seo['meta_keywords'])

@section('og_title', $seo['og_title'])

@section('og_description', $seo['og_description'])

@section('canonical_url', $seo['canonical_url'])

@section('content')
<div class="container">

    <!-- Breadcrumb -->
    <div style="padding: 20px 0; font-size: 14px;">
        <nav aria-label="breadcrumb">
            <ol style="display: flex; gap: 8px; list-style: none; flex-wrap: wrap;">
                <li>
                    <a href="{{ url('/') }}" style="color: #3b82f6; text-decoration: none;">
                        Home
                    </a>
                </li>

                <li>
                    <i class="fas fa-chevron-right" style="font-size: 10px; color: #64748b;"></i>
                </li>

                <li style="color: #64748b;">
                    All Brands
                </li>
            </ol>
        </nav>
    </div>

    <!-- Header -->
  <div style="text-align: center; margin-bottom: 50px;">
    <h1 style="font-size: 36px; font-weight: 800; margin-bottom: 16px; background: linear-gradient(135deg, #60a5fa, #a78bfa); -webkit-background-clip: text; background-clip: text; color: transparent;">
        Buy & Sell <span style="color: white;">Top Brands</span>
    </h1>

    <p style="color: #94a3b8; max-width: 600px; margin: 0 auto;">
        Trade in your old devices or shop certified refurbished smartphones from trusted brands across India.
    </p>
</div>

    <!-- Brands Grid -->
    <div class="brands-grid">

        @forelse($brands as $brand)

            <div class="brand-card">

                <!-- Brand Image -->
                <div class="brand-image-wrap">

                    @if(!empty($brand->image))
                        <img
                            src="{{ $brand->image_url }}"
                            alt="{{ $brand->title }}"
                            class="brand-image"
                        >
                    @else
                        <i class="fas fa-building brand-icon"></i>
                    @endif

                </div>

                <!-- Brand Name -->
                <h3 class="brand-title">
                    {{ $brand->title }}
                </h3>

                <!-- Description -->
                <!-- @if(!empty($brand->description))
                    <p class="brand-description">
                        {{ \Illuminate\Support\Str::limit($brand->description, 80) }}
                    </p>
                @endif -->

                <!-- Button -->
                <a
                    href="{{ url('/brand/' . ($brand->sef_url ?: \Illuminate\Support\Str::slug($brand->title))) }}"
                    class="brand-button"
                >
                    View Products →
                </a>

            </div>

        @empty

            <div class="empty-box">

                <i class="fas fa-box-open empty-icon"></i>

                <h3 style="color: white;">
                    No Brands Found
                </h3>

                <p style="color: #94a3b8;">
                    Brands will appear here once added.
                </p>

            </div>

        @endforelse

    </div>
</div>

<style>

    .brands-grid{
        display:grid;
        grid-template-columns:repeat(4,1fr);
        gap:30px;
        margin-bottom:60px;
    }

    .brand-card{
        background:#111118;
        border:1px solid #1e1e2a;
        border-radius:20px;
        padding:30px 20px;
        text-align:center;
        transition:all .3s;
    }

    .brand-card:hover{
        transform:translateY(-8px);
        border-color:#3b82f6;
        box-shadow:0 20px 35px -10px rgba(59,130,246,.2);
    }

    .brand-image-wrap{
        width:120px;
        height:120px;
        margin:0 auto 20px;
        display:flex;
        align-items:center;
        justify-content:center;
        background:#1a1a2e;
        border-radius:60px;
    }

    .brand-image{
        max-width:80px;
        max-height:80px;
        object-fit:contain;
    }

    .brand-icon{
        font-size:50px;
        color:#3b82f6;
    }

    .brand-title{
        font-size:20px;
        font-weight:700;
        margin-bottom:10px;
        color:white;
    }

    .brand-description{
        font-size:13px;
        color:#94a3b8;
        margin-bottom:20px;
        line-height:1.5;
    }

    .brand-button{
        display:inline-block;
        background:linear-gradient(135deg,#3b82f6,#8b5cf6);
        color:white;
        padding:10px 24px;
        border-radius:30px;
        text-decoration:none;
        font-size:14px;
        font-weight:500;
        transition:all .3s;
    }

    .brand-button:hover{
        transform:translateY(-2px);
        box-shadow:0 8px 20px -5px rgba(59,130,246,.4);
    }

    .empty-box{
        grid-column:1/-1;
        text-align:center;
        padding:60px;
        background:#111118;
        border-radius:20px;
    }

    .empty-icon{
        font-size:60px;
        color:#64748b;
        margin-bottom:20px;
    }

    @media(max-width:1024px){
        .brands-grid{
            grid-template-columns:repeat(3,1fr);
        }
    }

    @media(max-width:768px){
        .brands-grid{
            grid-template-columns:repeat(2,1fr);
            gap:20px;
        }
    }

    @media(max-width:480px){
        .brands-grid{
            grid-template-columns:1fr;
        }
    }

</style>
@endsection

@php
    $schemaItems = [];

    foreach($brands as $index => $brand){
        $schemaItems[] = [
            "@type" => "ListItem",
            "position" => $index + 1,
            "name" => $brand->title,
            "url" => url('/brand/' . ($brand->sef_url ?: \Illuminate\Support\Str::slug($brand->title)))
        ];
    }

    $schemaData = [
        "@context" => "https://schema.org",
        "@type" => "ItemList",
        "name" => "All Brands",
        "description" => "List of all trusted brands available for refurbished devices",
        "numberOfItems" => $brands->count(),
        "itemListElement" => $schemaItems
    ];
@endphp

<script type="application/ld+json">
{!! json_encode($schemaData, JSON_UNESCAPED_SLASHES|JSON_PRETTY_PRINT) !!}
</script>