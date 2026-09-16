@extends('layouts.owner.app')
@section('title', 'My Orders')
@push('styles')
@include('owner.partials.discovery-styles')
@endpush
@section('content')
<div class="od-page">
<div class="op-breadcrumb"><a href="{{ route('owner.dashboard') }}"><i class="bi bi-house-door-fill"></i> Dashboard</a><i class="bi bi-chevron-right"></i><span>My Orders</span></div>
<div class="od-layout"><div>
<div class="op-heading"><div><h1>@include('owner.partials.icon',['name'=>'receipt']) My Orders</h1><p>Track and manage your orders.</p></div></div>

<section class="od-section" style="margin-bottom:0;">
<div class="od-section-header"><h2>@include('owner.partials.icon',['name'=>'bag-check']) Order History</h2></div>
<div style="overflow-x:auto;">
<table class="od-health-table" style="min-width:600px;">
<thead><tr><th style="width:18%;">Order #</th><th style="width:16%;">Date</th><th style="width:12%;">Items</th><th style="width:16%;">Total</th><th style="width:18%;">Status</th><th style="width:10%;text-align:right;">Action</th></tr></thead>
<tbody>
@forelse($orders as $order)
<tr>
<td><code style="font-size:11px;color:#0a1648;font-weight:600;">{{ $order->order_number }}</code></td>
<td style="color:#52699b;font-size:12px;">{{ $order->created_at->format('M d, Y') }}</td>
<td><span style="background:#e6f2ff;color:#007dff;padding:3px 8px;border-radius:4px;font-size:10px;font-weight:600;">{{ $order->items->count() }} item(s)</span></td>
<td style="font-weight:700;color:#00865e;font-size:13px;">{{ number_format($order->total_amount,2) }}</td>
<td>
@if($order->status==='placed')<span style="background:#fff3df;color:#b45309;padding:3px 8px;border-radius:4px;font-size:10px;font-weight:600;"><i class="bi bi-clock"></i> Placed</span>
@elseif($order->status==='processing')<span style="background:#e6f2ff;color:#007dff;padding:3px 8px;border-radius:4px;font-size:10px;font-weight:600;"><i class="bi bi-arrow-repeat"></i> Processing</span>
@elseif($order->status==='completed')<span style="background:#e1f5ef;color:#00825a;padding:3px 8px;border-radius:4px;font-size:10px;font-weight:600;"><i class="bi bi-check-circle"></i> Completed</span>
@elseif($order->status==='cancelled')<span style="background:#ffe9ef;color:#e92e56;padding:3px 8px;border-radius:4px;font-size:10px;font-weight:600;"><i class="bi bi-x-circle"></i> Cancelled</span>
@else<span style="background:#f1f5f9;color:#475569;padding:3px 8px;border-radius:4px;font-size:10px;font-weight:600;">{{ ucfirst($order->status) }}</span>@endif
</td>
<td style="text-align:right;"><a class="op-button op-small" href="{{ route('owner.orders.show',$order) }}" style="min-height:30px;font-size:11px;background:#e4f1ff;color:#0072ff;border:0;"><i class="bi bi-eye"></i> View</a></td>
</tr>
@empty
<tr><td colspan="6" style="text-align:center;padding:40px;color:#99a8c2;">No orders found.<br><a href="{{ route('owner.products.index') }}" style="color:#00865e;font-weight:600;">Start Shopping →</a></td></tr>
@endforelse
</tbody>
</table>
</div>
@if($orders->hasPages())<div class="op-pagination" style="margin-top:12px;"><span>Showing {{ $orders->firstItem() }}–{{ $orders->lastItem() }} of {{ $orders->total() }}</span><nav>@if($orders->previousPageUrl())<a class="op-button op-small" href="{{ $orders->previousPageUrl() }}" rel="prev">‹ Previous</a>@endif<span>{{ $orders->currentPage() }} / {{ $orders->lastPage() }}</span>@if($orders->nextPageUrl())<a class="op-button op-small" href="{{ $orders->nextPageUrl() }}" rel="next">Next ›</a>@endif</nav></div>@endif
</section>
</div>

<aside class="od-aside">
<section class="od-side-card">
<div class="od-section-header"><h2>@include('owner.partials.icon',['name'=>'tag-fill']) Order Stats</h2></div>
<div class="od-fields" style="gap:9px;">
<div style="display:flex;justify-content:space-between;font-size:12px;"><span style="color:#52699b;">Total Orders</span><strong style="color:#0a1648;">{{ $stats['total'] }}</strong></div>
<hr style="margin:0;border-color:#edf2f6;">
<div style="display:flex;justify-content:space-between;font-size:12px;"><span style="color:#52699b;">Placed</span><strong style="color:#b45309;">{{ $stats['placed'] }}</strong></div>
<hr style="margin:0;border-color:#edf2f6;">
<div style="display:flex;justify-content:space-between;font-size:12px;"><span style="color:#52699b;">Processing</span><strong style="color:#007dff;">{{ $stats['processing'] }}</strong></div>
<hr style="margin:0;border-color:#edf2f6;">
<div style="display:flex;justify-content:space-between;font-size:12px;"><span style="color:#52699b;">Completed</span><strong style="color:#00825a;">{{ $stats['completed'] }}</strong></div>
<hr style="margin:0;border-color:#edf2f6;">
<div style="display:flex;justify-content:space-between;font-size:12px;"><span style="color:#52699b;">Cancelled</span><strong style="color:#e92e56;">{{ $stats['cancelled'] }}</strong></div>
<hr style="margin:0;border-color:#edf2f6;">
<div style="display:flex;justify-content:space-between;font-size:12px;"><span style="color:#52699b;">Total Spent</span><strong style="color:#00865e;font-size:14px;">{{ number_format($stats['spent'],2) }}</strong></div>
</div>
</section>
<section class="od-info-card">@include('owner.partials.icon',['name'=>'paw'])<div><h2>Need Help?</h2><p>Contact support if you have any issues with your orders.</p></div></section>
</aside>
</div>
@include('owner.partials.discovery-banner')
</div>
@endsection
