@extends('layouts.app')

@section('content')
    <div class="mb-4">
        <div class="d-flex justify-content-between align-items-center">
            <h2 class="h4 mb-0 fw-semibold">{{ __('Order') }} #{{ $order->order_number }}</h2>
            <a href="{{ route('admin.orders.index') }}" class="btn btn-outline-secondary btn-sm">
                <i class="bi bi-arrow-left me-1"></i>{{ __('Back') }}
            </a>
        </div>
    </div>

    <div class="row g-4">
        <div class="col-lg-8">
            {{-- Order Items --}}
            <div class="card mb-4">
                <div class="card-header">
                    <h6 class="mb-0 fw-semibold">{{ __('Order Items') }} ({{ $order->items->count() }})</h6>
                </div>
                <div class="card-body p-0">
                    <div class="table-responsive">
                        <table class="table mb-0">
                            <thead class="table-light">
                                <tr>
                                    <th style="font-size:0.8rem;">{{ __('Product') }}</th>
                                    <th style="font-size:0.8rem;">{{ __('Price') }}</th>
                                    <th style="font-size:0.8rem;">{{ __('Qty') }}</th>
                                    <th style="font-size:0.8rem;" class="text-end">{{ __('Subtotal') }}</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($order->items as $item)
                                    <tr>
                                        <td>
                                            <div class="d-flex align-items-center">
                                                @if($item->product && $item->product->images->where('is_primary', true)->first())
                                                    <img src="{{ asset('storage/' . $item->product->images->where('is_primary', true)->first()->image_path) }}" class="rounded me-2" style="width:40px;height:40px;object-fit:cover;">
                                                @else
                                                    <div class="rounded me-2 d-flex align-items-center justify-content-center bg-light" style="width:40px;height:40px;">
                                                        <i class="bi bi-box text-muted"></i>
                                                    </div>
                                                @endif
                                                <div>
                                                    <div class="fw-semibold" style="font-size:0.85rem;">{{ $item->product->name ?? 'Deleted Product' }}</div>
                                                    @if($item->product && $item->product->sku)
                                                        <small class="text-muted" style="font-size:0.7rem;">SKU: {{ $item->product->sku }}</small>
                                                    @endif
                                                </div>
                                            </div>
                                        </td>
                                        <td style="font-size:0.85rem;">${{ number_format($item->price_each, 2) }}</td>
                                        <td style="font-size:0.85rem;">{{ $item->quantity }}</td>
                                        <td class="text-end fw-bold" style="font-size:0.85rem;">${{ number_format($item->price_each * $item->quantity, 2) }}</td>
                                    </tr>
                                @endforeach
                            </tbody>
                            <tfoot class="table-light">
                                <tr>
                                    <td colspan="3" class="text-end fw-semibold">{{ __('Total') }}</td>
                                    <td class="text-end fw-bold text-success">${{ number_format($order->total_amount, 2) }}</td>
                                </tr>
                            </tfoot>
                        </table>
                    </div>
                </div>
            </div>

            {{-- Status History --}}
            @if($order->status_history && count($order->status_history) > 0)
                <div class="card">
                    <div class="card-header">
                        <h6 class="mb-0 fw-semibold">{{ __('Status History') }}</h6>
                    </div>
                    <div class="card-body">
                        @foreach($order->status_history as $entry)
                            <div class="d-flex align-items-start mb-3 {{ !$loop->last ? 'pb-3 border-bottom' : '' }}">
                                <div class="rounded-circle d-flex align-items-center justify-content-center me-3 flex-shrink-0" style="width:32px;height:32px;background:#e0f2fe;">
                                    <i class="bi bi-arrow-repeat text-info" style="font-size:0.8rem;"></i>
                                </div>
                                <div>
                                    <div style="font-size:0.85rem;">
                                        <span class="badge bg-secondary">{{ ucfirst($entry['from']) }}</span>
                                        <i class="bi bi-arrow-right mx-1 text-muted"></i>
                                        <span class="badge bg-primary">{{ ucfirst($entry['to']) }}</span>
                                    </div>
                                    <small class="text-muted">{{ \Carbon\Carbon::parse($entry['at'])->diffForHumans() }}</small>
                                </div>
                            </div>
                        @endforeach
                    </div>
                </div>
            @endif
        </div>

        <div class="col-lg-4">
            {{-- Order Info --}}
            <div class="card mb-4">
                <div class="card-header">
                    <h6 class="mb-0 fw-semibold">{{ __('Order Info') }}</h6>
                </div>
                <div class="card-body">
                    <div class="d-flex justify-content-between mb-2">
                        <span class="text-muted" style="font-size:0.8rem;">{{ __('Status') }}</span>
                        @if($order->status === 'placed')
                            <span class="badge bg-warning text-dark">{{ __('Placed') }}</span>
                        @elseif($order->status === 'processing')
                            <span class="badge bg-info">{{ __('Processing') }}</span>
                        @elseif($order->status === 'completed')
                            <span class="badge bg-success">{{ __('Completed') }}</span>
                        @elseif($order->status === 'cancelled')
                            <span class="badge bg-danger">{{ __('Cancelled') }}</span>
                        @endif
                    </div>
                    <div class="d-flex justify-content-between mb-2">
                        <span class="text-muted" style="font-size:0.8rem;">{{ __('Order Date') }}</span>
                        <span style="font-size:0.8rem;">{{ $order->created_at->format('M d, Y H:i') }}</span>
                    </div>
                    <div class="d-flex justify-content-between mb-2">
                        <span class="text-muted" style="font-size:0.8rem;">{{ __('Total Amount') }}</span>
                        <span class="fw-bold" style="font-size:0.8rem;">${{ number_format($order->total_amount, 2) }}</span>
                    </div>
                    @if($order->shipping_address)
                        <hr>
                        <div class="mb-0">
                            <small class="text-muted d-block mb-1">{{ __('Shipping Address') }}</small>
                            <div style="font-size:0.85rem;">{{ $order->shipping_address }}</div>
                        </div>
                    @endif
                    @if($order->notes)
                        <hr>
                        <div class="mb-0">
                            <small class="text-muted d-block mb-1">{{ __('Notes') }}</small>
                            <div style="font-size:0.85rem;">{{ $order->notes }}</div>
                        </div>
                    @endif
                </div>
            </div>

            {{-- Customer --}}
            <div class="card mb-4">
                <div class="card-header">
                    <h6 class="mb-0 fw-semibold">{{ __('Customer') }}</h6>
                </div>
                <div class="card-body">
                    <div class="d-flex align-items-center">
                        <div class="rounded-circle d-flex align-items-center justify-content-center me-3" style="width:40px;height:40px;background:linear-gradient(135deg,#1a6b3c,#2e9e5a);">
                            <span class="text-white fw-semibold" style="font-size:0.85rem;">{{ substr($order->owner->name ?? '?', 0, 1) }}</span>
                        </div>
                        <div>
                            <div class="fw-semibold" style="font-size:0.85rem;">{{ $order->owner->name ?? '-' }}</div>
                            <small class="text-muted" style="font-size:0.7rem;">{{ $order->owner->email ?? '' }}</small>
                        </div>
                    </div>
                </div>
            </div>

            {{-- Update Status --}}
            <div class="card">
                <div class="card-header">
                    <h6 class="mb-0 fw-semibold">{{ __('Update Status') }}</h6>
                </div>
                <div class="card-body">
                    <form action="{{ route('admin.orders.updateStatus', $order) }}" method="POST">
                        @csrf
                        @method('PUT')
                        <div class="mb-3">
                            <select class="form-select" name="status" required>
                                <option value="placed" {{ $order->status === 'placed' ? 'selected' : '' }}>{{ __('Placed') }}</option>
                                <option value="processing" {{ $order->status === 'processing' ? 'selected' : '' }}>{{ __('Processing') }}</option>
                                <option value="completed" {{ $order->status === 'completed' ? 'selected' : '' }}>{{ __('Completed') }}</option>
                                <option value="cancelled" {{ $order->status === 'cancelled' ? 'selected' : '' }}>{{ __('Cancelled') }}</option>
                            </select>
                        </div>
                        <button type="submit" class="btn btn-primary w-100">
                            <i class="bi bi-check-circle me-1"></i>{{ __('Update Status') }}
                        </button>
                    </form>
                </div>
            </div>
        </div>
    </div>
@endsection
