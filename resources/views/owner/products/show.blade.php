@extends('layouts.owner.app')
@section('title', $product->name)
@push('styles')
@include('owner.partials.discovery-styles')
<style>
.od-lightbox{position:fixed;inset:0;z-index:9999;background:rgba(0,0,0,.88);display:flex;align-items:center;justify-content:center;opacity:0;pointer-events:none;transition:opacity .25s ease;}
.od-lightbox.is-open{opacity:1;pointer-events:auto;}
.od-lightbox img{max-width:92vw;max-height:88vh;border-radius:8px;box-shadow:0 8px 40px rgba(0,0,0,.45);object-fit:contain;transition:transform .2s ease;}
.od-lightbox.is-open img{transform:scale(1);}
.od-lightbox-close{position:absolute;top:16px;right:20px;width:40px;height:40px;border-radius:50%;border:0;background:rgba(255,255,255,.15);color:#fff;font-size:22px;cursor:pointer;display:flex;align-items:center;justify-content:center;transition:background .15s ease;z-index:1;}
.od-lightbox-close:hover{background:rgba(255,255,255,.3);}
.od-lightbox-nav{position:absolute;top:50%;transform:translateY(-50%);width:44px;height:44px;border-radius:50%;border:0;background:rgba(255,255,255,.12);color:#fff;font-size:20px;cursor:pointer;display:flex;align-items:center;justify-content:center;transition:background .15s ease;z-index:1;}
.od-lightbox-nav:hover{background:rgba(255,255,255,.28);}
.od-lightbox-prev{left:16px;}
.od-lightbox-next{right:16px;}
.od-lightbox-counter{position:absolute;bottom:16px;left:50%;transform:translateX(-50%);color:rgba(255,255,255,.7);font-size:13px;font-weight:500;}
.od-product-main-img{width:100%;max-height:420px;border-radius:8px;object-fit:contain;background:#f6f9fc;cursor:zoom-in;transition:opacity .2s ease;}
.od-product-main-img:hover{opacity:.92;}
.od-product-thumbs{display:flex;gap:8px;flex-wrap:wrap;margin-top:10px;}
.od-product-thumb{width:72px;height:72px;border-radius:6px;object-fit:cover;border:2px solid transparent;cursor:pointer;transition:border-color .15s ease,opacity .15s ease;opacity:.65;}
.od-product-thumb:hover,.od-product-thumb.is-active{border-color:#00865e;opacity:1;}
.od-product-thumb.is-primary{position:relative;}
</style>
@endpush
@section('content')
@php
$hasDiscount=$product->special_price&&$product->special_price>0&&$product->special_price<$product->price;
$discountPct=$hasDiscount?round((1-$product->special_price/$product->price)*100):0;
$savings=$hasDiscount?($product->price-$product->special_price):0;
$sortedImages=$product->images->sortBy('sort_order')->values();
$imageUrls=$sortedImages->map(fn($img)=>asset('storage/'.$img->image_path))->toArray();
@endphp
<div class="od-page">
<div class="op-breadcrumb"><a href="{{ route('owner.dashboard') }}"><i class="bi bi-house-door-fill"></i> Dashboard</a><i class="bi bi-chevron-right"></i><a href="{{ route('owner.products.index') }}">Shop</a><i class="bi bi-chevron-right"></i><span>{{ $product->name }}</span></div>
<div class="od-layout"><div>
<div class="op-heading"><div><h1>{{ $product->name }}</h1><p>{{ $product->category?->name??'Uncategorized' }}</p></div><a class="op-button" href="{{ route('owner.products.index') }}"><i class="bi bi-arrow-left"></i> Back to Shop</a></div>

{{-- Images --}}
<section class="od-section" style="margin-bottom:12px;" x-data="productLightbox()">
<div class="od-section-header"><h2>@include('owner.partials.icon',['name'=>'images']) Product Images</h2></div>
@if($sortedImages->count()>0)
<div>
<img class="od-product-main-img" :src="images[current]" alt="{{ $product->name }}" @click="open(current)" loading="lazy">
@if($sortedImages->count()>1)
<div class="od-product-thumbs">
@foreach($sortedImages as $idx=>$image)
<img class="od-product-thumb" :class="{'is-active':current==={{ $idx }}}" src="{{ asset('storage/'.$image->image_path) }}" alt="{{ $product->name }}" @click="current={{ $idx }}" loading="lazy">
@endforeach
</div>
@endif
</div>
@else
<div style="text-align:center;padding:50px;background:#f6f9fc;border-radius:8px;color:#99a8c2;"><i class="bi bi-image" style="font-size:2.5rem;"></i><p style="margin:10px 0 0;font-size:13px;">No images available</p></div>
@endif

{{-- Lightbox --}}
<div class="od-lightbox" :class="{'is-open':opened}" @click.self="close()" @keydown.escape.window="close()" @keydown.left.window="prev()" @keydown.right.window="next()" x-show="opened" x-cloak x-transition:enter="transition ease-out duration-200" x-transition:enter-start="opacity-0" x-transition:enter-end="opacity-100" x-transition:leave="transition ease-in duration-150" x-transition:leave-start="opacity-100" x-transition:leave-end="opacity-0">
<button class="od-lightbox-close" @click="close()" aria-label="Close"><i class="bi bi-x-lg"></i></button>
<button class="od-lightbox-nav od-lightbox-prev" @click="prev()" aria-label="Previous" x-show="images.length>1"><i class="bi bi-chevron-left"></i></button>
<img :src="images[current]" alt="{{ $product->name }}">
<button class="od-lightbox-nav od-lightbox-next" @click="next()" aria-label="Next" x-show="images.length>1"><i class="bi bi-chevron-right"></i></button>
<div class="od-lightbox-counter" x-text="`${current+1} / ${images.length}`" x-show="images.length>1"></div>
</div>
</section>

{{-- Info --}}
<section class="od-section" style="margin-bottom:12px;">
<div class="od-section-header"><h2>@include('owner.partials.icon',['name'=>'info-circle']) General Information</h2></div>
<table style="width:100%;font-size:12px;border-collapse:collapse;">
<tr><td style="padding:8px 0;color:#52699b;font-weight:500;width:140px;border-bottom:1px solid #edf2f7;">Name</td><td style="padding:8px 0;border-bottom:1px solid #edf2f7;color:#0a1648;font-weight:600;">{{ $product->name }}</td></tr>
<tr><td style="padding:8px 0;color:#52699b;font-weight:500;border-bottom:1px solid #edf2f7;">SKU</td><td style="padding:8px 0;border-bottom:1px solid #edf2f7;"><code style="font-size:11px;">{{ $product->sku?:'-' }}</code></td></tr>
<tr><td style="padding:8px 0;color:#52699b;font-weight:500;border-bottom:1px solid #edf2f7;">Category</td><td style="padding:8px 0;border-bottom:1px solid #edf2f7;">{{ $product->category?->name??'-' }}</td></tr>
<tr><td style="padding:8px 0;color:#52699b;font-weight:500;border-bottom:1px solid #edf2f7;">Price</td><td style="padding:8px 0;border-bottom:1px solid #edf2f7;">
@if($hasDiscount)<s style="color:#99a8c2;">{{ number_format($product->price,2) }}</s> <strong style="color:#00865e;">{{ number_format($product->special_price,2) }}</strong> <span style="background:#ffe9ef;color:#e92e56;padding:2px 6px;border-radius:3px;font-size:10px;font-weight:600;">-{{ $discountPct }}%</span>
@else <strong style="color:#00865e;">{{ number_format($product->price,2) }}</strong>@endif
</td></tr>
<tr><td style="padding:8px 0;color:#52699b;font-weight:500;border-bottom:1px solid #edf2f7;">Stock</td><td style="padding:8px 0;border-bottom:1px solid #edf2f7;">@if($product->stock_quantity>0)<span style="color:#00865e;font-weight:600;"><i class="bi bi-check-circle-fill"></i> In Stock ({{ $product->stock_quantity }})</span>@else<span style="color:#e92e56;font-weight:600;"><i class="bi bi-x-circle-fill"></i> Out of Stock</span>@endif</td></tr>
@if($product->weight)<tr><td style="padding:8px 0;color:#52699b;font-weight:500;border-bottom:1px solid #edf2f7;">Weight</td><td style="padding:8px 0;border-bottom:1px solid #edf2f7;">{{ number_format($product->weight,2) }} kg</td></tr>@endif
<tr><td style="padding:8px 0;color:#52699b;font-weight:500;">Description</td><td style="padding:8px 0;color:#3d5885;">{{ $product->description?:'-' }}</td></tr>
</table>
</section>

{{-- Reviews --}}
<section class="od-section">
<div class="od-section-header"><h2>@include('owner.partials.icon',['name'=>'chat-left-text']) Reviews ({{ $product->reviews->count() }})</h2></div>
@forelse($product->reviews as $review)
<div style="{{ !$loop->last?'border-bottom:1px solid #edf2f7;padding-bottom:10px;margin-bottom:10px;':'' }}">
<div style="display:flex;align-items:center;gap:8px;margin-bottom:4px;">
<div style="width:28px;height:28px;border-radius:50%;background:linear-gradient(135deg,#00865e,#27bd96);display:flex;align-items:center;justify-content:center;color:#fff;font-size:11px;font-weight:700;">{{ mb_substr($review->user?->name??'A',0,1) }}</div>
<div><span style="font-size:12px;font-weight:600;">{{ $review->user?->name??'Anonymous' }}</span> <small style="color:#99a8c2;">{{ $review->created_at->diffForHumans() }}</small></div>
</div>
<div style="margin-bottom:3px;">@for($i=1;$i<=5;$i++)<i class="bi bi-{{ $i<=$review->rating?'star-fill':'star' }}" style="color:#f5ae00;font-size:11px;"></i>@endfor</div>
@if($review->comment)<p style="font-size:12px;color:#52699b;margin:0;">{{ $review->comment }}</p>@endif
</div>
@empty
<div style="text-align:center;padding:20px;color:#99a8c2;"><i class="bi bi-chat-left-text" style="font-size:1.5rem;opacity:.3;"></i><p style="margin:6px 0 0;font-size:12px;">No reviews yet.</p></div>
@endforelse
</section>
</div>

<aside class="od-aside">
{{-- Add to Cart --}}
<section class="od-side-card">
<div class="od-section-header"><h2>@include('owner.partials.icon',['name'=>'cart-plus']) Add to Cart</h2></div>
@if($hasDiscount)
<div style="background:linear-gradient(110deg,#e9fbf4,#f3fdfa);border:1px solid #d6eee6;border-radius:8px;padding:12px;margin-bottom:12px;">
<div style="display:flex;align-items:baseline;gap:8px;flex-wrap:wrap;"><s style="color:#99a8c2;font-size:13px;">{{ number_format($product->price,2) }}</s><span style="font-size:22px;font-weight:700;color:#00865e;">{{ number_format($product->special_price,2) }}</span><span style="background:#e92e56;color:#fff;padding:2px 7px;border-radius:4px;font-size:11px;font-weight:700;">-{{ $discountPct }}%</span></div>
<div style="font-size:11px;color:#006557;font-weight:600;margin-top:4px;"><i class="bi bi-piggy-bank"></i> You save {{ number_format($savings,2) }}</div>
</div>
@else
<div style="background:linear-gradient(110deg,#e9fbf4,#f3fdfa);border:1px solid #d6eee6;border-radius:8px;padding:12px;margin-bottom:12px;">
<span style="font-size:22px;font-weight:700;color:#00865e;">{{ number_format($product->price,2) }}</span>
</div>
@endif
@if($product->stock_quantity>0)
<form action="{{ route('owner.cart.add') }}" method="POST" class="od-fields">
@csrf
<input type="hidden" name="product_id" value="{{ $product->id }}">
<div><label for="qty">Quantity</label><div style="display:flex;align-items:center;gap:0;"><button type="button" class="op-button op-small" onclick="this.nextElementSibling.stepDown()" style="border-radius:6px 0 0 6px;min-height:35px;width:35px;padding:0;"><i class="bi bi-dash"></i></button><input type="number" id="qty" name="quantity" value="1" min="1" max="{{ $product->stock_quantity }}" style="border-radius:0;text-align:center;width:60px;min-height:35px;"><button type="button" class="op-button op-small" onclick="this.previousElementSibling.stepUp()" style="border-radius:0 6px 6px 0;min-height:35px;width:35px;padding:0;"><i class="bi bi-plus"></i></button></div></div>
<button type="submit" class="op-button op-primary" style="width:100%;margin-top:3px;">Add to Cart <i class="bi bi-cart-plus"></i></button>
</form>
@else
<div style="background:#f6f9fc;border:1px solid #e8eef7;border-radius:6px;padding:10px;font-size:12px;color:#99a8c2;text-align:center;">This product is currently out of stock.</div>
@endif
</section>

{{-- Details --}}
<section class="od-side-card">
<div class="od-section-header"><h2>@include('owner.partials.icon',['name'=>'list-check']) Details</h2></div>
<div style="font-size:12px;display:grid;gap:8px;">
<div style="display:flex;justify-content:space-between;"><span style="color:#52699b;">Category</span><strong style="color:#0a1648;">{{ $product->category?->name??'-' }}</strong></div>
<hr style="margin:0;border-color:#edf2f6;">
<div style="display:flex;justify-content:space-between;"><span style="color:#52699b;">SKU</span><code style="font-size:11px;">{{ $product->sku?:'-' }}</code></div>
<hr style="margin:0;border-color:#edf2f6;">
<div style="display:flex;justify-content:space-between;"><span style="color:#52699b;">Availability</span>@if($product->stock_quantity>0)<span style="color:#00865e;font-weight:600;">In Stock</span>@else<span style="color:#e92e56;font-weight:600;">Out of Stock</span>@endif</div>
</div>
</section>
</aside>
</div>
@include('owner.partials.discovery-banner')
</div>
@push('scripts')
<script>
function productLightbox(){return{
current:0,opened:false,
images:@js($imageUrls),
open(i){this.current=i;this.opened=true;document.body.style.overflow='hidden';},
close(){this.opened=false;document.body.style.overflow='';},
next(){this.current=(this.current+1)%this.images.length;},
prev(){this.current=(this.current-1+this.images.length)%this.images.length;},
}}
</script>
@endpush
@endsection
