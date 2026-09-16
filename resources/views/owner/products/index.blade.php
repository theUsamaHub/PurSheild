@extends('layouts.owner.app')
@section('title', 'Shop')
@push('styles')
@include('owner.partials.discovery-styles')
@endpush
@section('content')
<div class="od-page">
<div class="op-breadcrumb"><a href="{{ route('owner.dashboard') }}"><i class="bi bi-house-door-fill"></i> Dashboard</a><i class="bi bi-chevron-right"></i><span>Shop</span></div>
<div class="od-layout"><div>
<div class="op-heading"><div><h1>@include('owner.partials.icon',['name'=>'tag']) Shop</h1><p>Browse our collection of pet products and supplies.</p></div></div>
<form class="od-vet-search" style="grid-template-columns:minmax(0,1.8fr) 1fr 92px;" method="GET" action="{{ route('owner.products.index') }}">
@foreach(request()->except(['search','category_id','page']) as $field=>$value)@if(is_scalar($value))<input type="hidden" name="{{ $field }}" value="{{ $value }}">@endif
@endforeach
<input type="search" name="search" value="{{ request('search') }}" maxlength="200" aria-label="Search products" placeholder="Search by name, description, or keyword...">
<select name="category_id" aria-label="Category"><option value="">All Categories</option>@foreach($categories as $cat)<option value="{{ $cat->id }}" @selected(request('category_id')==$cat->id)>{{ $cat->name }}</option>@endforeach</select>
<button type="submit" class="op-button op-primary">Search</button></form>
<nav class="od-vet-categories" aria-label="Product categories"><a class="{{ !request('category_id')?'active':'' }}" href="{{ route('owner.products.index',request()->except(['category_id','page'])) }}">All</a>@foreach($categories as $cat)<a class="{{ request('category_id')==$cat->id?'active':'' }}" href="{{ route('owner.products.index',array_merge(request()->except(['category_id','page']),['category_id'=>$cat->id])) }}">{{ $cat->name }}</a>@endforeach</nav>
<div class="od-vet-toolbar"><span>Showing {{ $products->total() }} products</span><form method="GET" action="{{ route('owner.products.index') }}" data-owner-filters>@foreach(request()->except(['sort','page']) as $field=>$value)@if(is_scalar($value))<input type="hidden" name="{{ $field }}" value="{{ $value }}">@endif
@endforeach<label for="productSort">Sort by:</label><select name="sort" id="productSort">@foreach(['newest'=>'Newest','name'=>'Name A–Z','price_low'=>'Price: Low to High','price_high'=>'Price: High to Low','popular'=>'Most Popular'] as $value=>$label)<option value="{{ $value }}" @selected(request('sort','newest')===$value)>{{ $label }}</option>@endforeach</select><button class="visually-hidden" type="submit">Sort</button></form></div>
<div class="od-vet-grid" style="grid-template-columns:repeat(3,minmax(0,1fr));">
@forelse($products as $product)
@php
$hasDiscount=$product->special_price&&$product->special_price>0&&$product->special_price<$product->price;
$discountPct=$hasDiscount?round((1-$product->special_price/$product->price)*100):0;
$primaryImage=$product->images->sortBy('sort_order')->first();
@endphp
<article class="od-vet">
<div class="od-vet-main">
@if($primaryImage)
<img class="od-vet-photo" src="{{ asset('storage/'.$primaryImage->image_path) }}" alt="{{ $product->name }}" loading="lazy" onerror="this.hidden=true;this.nextElementSibling.hidden=false">
@endif
<span class="od-vet-photo od-photo-placeholder" @if($primaryImage) hidden @endif aria-label="{{ $product->name }} photo">@include('owner.partials.icon',['name'=>'tag'])</span>
<div>
<h2>{{ $product->name }}</h2>
<p>{{ $product->category?->name??'Uncategorized' }}</p>
@if($hasDiscount)
<p style="color:#e92e56;font-weight:700;font-size:12px;"><s style="color:#999;font-weight:400;">{{ number_format($product->price,2) }}</s> → {{ number_format($product->special_price,2) }} <span style="background:#ffe9ef;padding:2px 6px;border-radius:3px;font-size:10px;">-{{ $discountPct }}%</span></p>
@else
<p style="color:#00865e;font-weight:700;">{{ number_format($product->price,2) }}</p>
@endif
<p>@if($product->stock_quantity>0)<i class="bi bi-check-circle-fill" style="color:#00865e;"></i> In Stock ({{ $product->stock_quantity }})@else<i class="bi bi-x-circle-fill" style="color:#e92e56;"></i> Out of Stock @endif</p>
@if($product->is_featured)<p><i class="bi bi-star-fill" style="color:#f5ae00;"></i> Featured</p>@endif
</div>
</div>
<div class="od-vet-actions"><a class="op-button" href="{{ route('owner.products.show',$product) }}">View Details</a></div>
</article>
@empty
<div class="op-empty" style="grid-column:1/-1">No products match your filters.<br><a href="{{ route('owner.products.index') }}">Clear Filters</a></div>
@endforelse
</div>
@include('owner.partials.pagination',['items'=>$products])
</div>
<aside class="od-aside">
<section class="od-side-card"><div class="od-section-header"><h2>@include('owner.partials.icon',['name'=>'tag-fill']) Shop Info</h2></div>
<div class="od-fields" style="gap:9px;">
<div style="display:flex;justify-content:space-between;font-size:12px;"><span style="color:#52699b;">Total Products</span><strong style="color:#0a1648;">{{ $stats['total'] }}</strong></div>
<hr style="margin:0;border-color:#edf2f6;">
<div style="display:flex;justify-content:space-between;font-size:12px;"><span style="color:#52699b;">In Stock</span><strong style="color:#00865e;">{{ $stats['in_stock'] }}</strong></div>
<hr style="margin:0;border-color:#edf2f6;">
<div style="display:flex;justify-content:space-between;font-size:12px;"><span style="color:#52699b;">Out of Stock</span><strong style="color:#e92e56;">{{ $stats['out_stock'] }}</strong></div>
<hr style="margin:0;border-color:#edf2f6;">
<div style="display:flex;justify-content:space-between;font-size:12px;"><span style="color:#52699b;">On Sale</span><strong style="color:#823aff;">{{ $stats['on_sale'] }}</strong></div>
</div>
</section>
<section class="od-info-card">@include('owner.partials.icon',['name'=>'paw'])<div><h2>Need Help?</h2><p>Browse categories or use the search to find the perfect products for your pets.</p></div></section>
</aside>
</div>
@include('owner.partials.discovery-banner')
</div>
@endsection
