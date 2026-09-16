@extends('layouts.owner.app')
@section('title', 'Order '.$order->order_number)
@push('styles')
@include('owner.partials.discovery-styles')
@endpush
@section('content')
<div class="od-page">
<div class="op-breadcrumb"><a href="{{ route('owner.dashboard') }}"><i class="bi bi-house-door-fill"></i> Dashboard</a><i class="bi bi-chevron-right"></i><a href="{{ route('owner.orders.index') }}">My Orders</a><i class="bi bi-chevron-right"></i><span>{{ $order->order_number }}</span></div>
<div class="od-layout"><div>
<div class="op-heading"><div><h1>@include('owner.partials.icon',['name'=>'receipt']) Order {{ $order->order_number }}</h1><p>Placed on {{ $order->created_at->format('M d, Y H:i') }}</p></div><a class="op-button" href="{{ route('owner.orders.index') }}"><i class="bi bi-arrow-left"></i> Back to Orders</a></div>

{{-- Order Items --}}
<section class="od-section" style="margin-bottom:0;">
<div class="od-section-header"><h2>@include('owner.partials.icon',['name'=>'bag']) Order Items</h2>
<div>@if($order->status==='placed')<span style="background:#fff3df;color:#b45309;padding:4px 10px;border-radius:4px;font-size:11px;font-weight:600;"><i class="bi bi-clock"></i> Placed</span>@elseif($order->status==='processing')<span style="background:#e6f2ff;color:#007dff;padding:4px 10px;border-radius:4px;font-size:11px;font-weight:600;"><i class="bi bi-arrow-repeat"></i> Processing</span>@elseif($order->status==='completed')<span style="background:#e1f5ef;color:#00825a;padding:4px 10px;border-radius:4px;font-size:11px;font-weight:600;"><i class="bi bi-check-circle"></i> Completed</span>@elseif($order->status==='cancelled')<span style="background:#ffe9ef;color:#e92e56;padding:4px 10px;border-radius:4px;font-size:11px;font-weight:600;"><i class="bi bi-x-circle"></i> Cancelled</span>@endif</div>
</div>
<div style="overflow-x:auto;">
<table class="od-health-table" style="min-width:500px;">
<thead><tr><th style="width:40%;">Product</th><th style="width:15%;text-align:center;">Qty</th><th style="width:20%;text-align:right;">Price</th><th style="width:25%;text-align:right;">Subtotal</th></tr></thead>
<tbody>
@foreach($order->items as $item)
@php
$prod=$item->product;
$hasDiscount=$prod&&$prod->special_price&&$prod->special_price>0&&$prod->special_price<$prod->price;
@endphp
<tr>
<td><div style="display:flex;align-items:center;gap:10px;">
@if($prod&&$prod->images->count())
<img src="{{ asset('storage/'.$prod->images->sortBy('sort_order')->first()->image_path) }}" alt="{{ $prod->name }}" style="width:40px;height:40px;border-radius:6px;object-fit:cover;" loading="lazy">
@else
<div style="width:40px;height:40px;border-radius:6px;background:#e1f5ef;display:flex;align-items:center;justify-content:center;color:#00865e;font-size:14px;">@include('owner.partials.icon',['name'=>'tag'])</div>
@endif
<strong style="font-size:12px;color:#0a1648;">{{ $prod?->name??'Deleted Product' }}</strong>
</div></td>
<td style="text-align:center;font-size:12px;">{{ $item->quantity }}</td>
<td style="text-align:right;font-size:12px;color:#52699b;">@if($hasDiscount)<s style="color:#c5cdd9;">{{ number_format($prod->price,2) }}</s> <span style="color:#00865e;">{{ number_format($item->price_each,2) }}</span>@else{{ number_format($item->price_each,2) }}@endif</td>
<td style="text-align:right;font-weight:700;color:#00865e;font-size:13px;">{{ number_format($item->price_each*$item->quantity,2) }}</td>
</tr>
@endforeach
</tbody>
<tfoot><tr><td colspan="3" style="text-align:right;font-weight:700;border-top:2px solid #e0e9f5;">Total</td><td style="text-align:right;font-weight:700;color:#00865e;font-size:15px;border-top:2px solid #e0e9f5;">{{ number_format($order->total_amount,2) }}</td></tr></tfoot>
</table>
</div>
</section>
</div>

<aside class="od-aside">
{{-- Shipping --}}
<section class="od-side-card">
<div class="od-section-header"><h2>@include('owner.partials.icon',['name'=>'geo-alt-fill']) Shipping Address</h2></div>
@if($order->shipping_address)
<div style="font-size:12px;color:#3d5885;white-space:pre-wrap;">{{ $order->shipping_address }}</div>
@else
<p style="font-size:12px;color:#99a8c2;margin:0;">No shipping address provided.</p>
@endif
</section>

{{-- Notes --}}
@if($order->notes)
<section class="od-side-card">
<div class="od-section-header"><h2>@include('owner.partials.icon',['name'=>'sticky']) Notes</h2></div>
<div style="background:#f6f9fc;border:1px solid #e8eef7;border-radius:6px;padding:10px;font-size:12px;color:#3d5885;white-space:pre-wrap;">{{ $order->notes }}</div>
</section>
@endif

<section class="od-info-card">@include('owner.partials.icon',['name'=>'paw'])<div><h2>Need Help?</h2><p>Contact support if you have any issues with this order.</p></div></section>
</aside>
</div>
@include('owner.partials.discovery-banner')
</div>
@endsection
