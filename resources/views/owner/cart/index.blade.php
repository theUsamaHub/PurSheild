@extends('layouts.owner.app')
@section('title', 'My Cart')
@push('styles')
@include('owner.partials.discovery-styles')
@endpush
@section('content')
<div class="od-page">
<div class="op-breadcrumb"><a href="{{ route('owner.dashboard') }}"><i class="bi bi-house-door-fill"></i> Dashboard</a><i class="bi bi-chevron-right"></i><span>My Cart</span></div>
<div class="od-layout"><div>
<div class="op-heading"><div><h1>@include('owner.partials.icon',['name'=>'bag']) My Cart</h1><p>Review your items before checkout.</p></div><a class="op-button" href="{{ route('owner.products.index') }}"><i class="bi bi-arrow-left"></i> Continue Shopping</a></div>

@if($cart&&$cart->items->count()>0)
<section class="od-section" style="margin-bottom:0;">
<div class="od-section-header"><h2>@include('owner.partials.icon',['name'=>'bag']) Cart Items ({{ $cart->items->count() }})</h2></div>
<div style="overflow-x:auto;">
<table class="od-health-table" style="min-width:600px;">
<thead><tr><th style="width:35%;">Product</th><th style="width:15%;">Price</th><th style="width:20%;">Quantity</th><th style="width:15%;">Subtotal</th><th style="width:15%;text-align:right;">Action</th></tr></thead>
<tbody>
@foreach($cart->items as $item)
@php
$prod=$item->product;
$hasDiscount=$prod&&$prod->special_price&&$prod->special_price>0&&$prod->special_price<$prod->price;
$unitPrice=$hasDiscount?$prod->special_price:($prod?$prod->price:0);
@endphp
<tr>
<td><div style="display:flex;align-items:center;gap:10px;">
@if($prod&&$prod->images->count())
<img src="{{ asset('storage/'.$prod->images->sortBy('sort_order')->first()->image_path) }}" alt="{{ $prod->name }}" style="width:44px;height:44px;border-radius:6px;object-fit:cover;" loading="lazy">
@else
<div style="width:44px;height:44px;border-radius:6px;background:#e1f5ef;display:flex;align-items:center;justify-content:center;color:#00865e;">@include('owner.partials.icon',['name'=>'tag'])</div>
@endif
<div><strong style="font-size:12px;color:#0a1648;">{{ $prod?->name??'Deleted Product' }}</strong><br><small style="color:#99a8c2;">{{ $prod?->category?->name??'' }}</small></div>
</div></td>
<td style="font-size:12px;">@if($hasDiscount)<s style="color:#99a8c2;">{{ number_format($prod->price,2) }}</s> <strong style="color:#00865e;">{{ number_format($unitPrice,2) }}</strong>@else<span style="color:#52699b;">{{ number_format($unitPrice,2) }}</span>@endif</td>
<td>
<form action="{{ route('owner.cart.update',$item) }}" method="POST" style="display:inline;">
@csrf @method('PUT')
<div style="display:flex;align-items:center;gap:0;"><button type="button" class="op-button op-small" onclick="this.nextElementSibling.stepDown();this.closest('form').submit();" style="border-radius:5px 0 0 5px;min-height:30px;width:28px;padding:0;"><i class="bi bi-dash"></i></button><input type="number" name="quantity" value="{{ $item->quantity }}" min="1" max="{{ $prod?->stock_quantity??99 }}" onchange="this.closest('form').submit()" style="width:42px;text-align:center;border-radius:0;min-height:30px;font-size:12px;"><button type="button" class="op-button op-small" onclick="this.previousElementSibling.stepUp();this.closest('form').submit();" style="border-radius:0 5px 5px 0;min-height:30px;width:28px;padding:0;"><i class="bi bi-plus"></i></button></div>
</form>
</td>
<td style="font-size:12px;font-weight:700;color:#00865e;">{{ number_format($unitPrice*$item->quantity,2) }}</td>
<td style="text-align:right;">
<form action="{{ route('owner.cart.remove',$item) }}" method="POST" style="display:inline;" onsubmit="return confirm('Are you sure you want to remove this item?')">
@csrf @method('DELETE')
<button type="submit" class="op-button op-danger op-small" title="Remove" style="min-height:30px;font-size:11px;"><i class="bi bi-trash"></i></button>
</form>
</td>
</tr>
@endforeach
</tbody>
</table>
</div>
</section>
</div>

<aside class="od-aside">
<section class="od-side-card">
<div class="od-section-header"><h2>@include('owner.partials.icon',['name'=>'receipt']) Order Summary</h2></div>
@php
$subtotal=0;
foreach($cart->items as $item){if($item->product){$hasD=$item->product->special_price&&$item->product->special_price>0&&$item->product->special_price<$item->product->price;$subtotal+=($hasD?$item->product->special_price:$item->product->price)*$item->quantity;}}
@endphp
<div class="od-fields" style="gap:9px;">
<div style="display:flex;justify-content:space-between;font-size:12px;"><span style="color:#52699b;">Subtotal</span><strong style="color:#0a1648;">{{ number_format($subtotal,2) }}</strong></div>
<hr style="margin:0;border-color:#edf2f6;">
<div style="display:flex;justify-content:space-between;font-size:12px;"><span style="color:#52699b;">Shipping</span><span style="color:#99a8c2;font-size:11px;">Calculated at checkout</span></div>
<hr style="margin:0;border-color:#edf2f6;">
<div style="display:flex;justify-content:space-between;font-size:14px;"><strong style="color:#0a1648;">Total</strong><strong style="color:#00865e;font-size:16px;">{{ number_format($subtotal,2) }}</strong></div>
</div>
@if(Auth::user()->address)
<div style="background:#e9fbf4;border:1px solid #d6eee6;border-radius:6px;padding:8px 10px;font-size:11px;color:#006557;margin:12px 0;display:flex;align-items:center;gap:6px;"><i class="bi bi-geo-alt-fill"></i> <strong>Shipping to:</strong> {{ Auth::user()->address }}</div>
@else
<div style="background:#fff3df;border:1px solid #f7e7c7;border-radius:6px;padding:8px 10px;font-size:11px;color:#b45309;margin:12px 0;display:flex;align-items:center;gap:6px;"><i class="bi bi-exclamation-triangle-fill"></i> <a href="{{ route('profile.edit') }}" style="font-weight:600;text-decoration:underline;">Add your address</a> for delivery</div>
@endif
<form action="{{ route('owner.cart.checkout') }}" method="POST">
@csrf
<button type="submit" class="op-button op-primary" style="width:100%;margin-top:3px;">Checkout <i class="bi bi-credit-card"></i></button>
</form>
</section>
</aside>
@else
<div style="grid-column:1/-1;">
<section class="od-section">
<div style="text-align:center;padding:40px;color:#99a8c2;">
@include('owner.partials.icon',['name'=>'bag'])<br>
<p style="margin:10px 0;font-size:13px;">Your cart is empty.</p>
<a class="op-button op-primary" href="{{ route('owner.products.index') }}">Browse Products</a>
</div>
</section>
</div>
@endif
</div>
@include('owner.partials.discovery-banner')
</div>
@endsection
