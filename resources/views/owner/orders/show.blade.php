@extends('layouts.owner.app')

@section('content')
    <div class="mb-4">
        <div class="d-flex justify-content-between align-items-center">
            <h2 class="h4 mb-0 fw-semibold">{{ __('Order Details') }}</h2>
            <a href="{{ route('owner.orders.index') }}" class="btn btn-outline-secondary btn-sm">
                <i class="bi bi-arrow-left me-1"></i>{{ __('Back to Orders') }}
            </a>
        </div>
    </div>

    <!-- Order Header -->
    <div class="card mb-4">
        <div class="card-body">
            <div class="row align-items-center">
                <div class="col-md-4">
                    <small class="text-muted">{{ __('Order Number') }}</small>
                    <div class="fw-bold fs-5"><code>{{ $order->order_number }}</code></div>
                </div>
                <div class="col-md-3">
                    <small class="text-muted">{{ __('Date') }}</small>
                    <div class="fw-medium">{{ $order->created_at->format('M d, Y H:i') }}</div>
                </div>
                <div class="col-md-3">
                    <small class="text-muted">{{ __('Status') }}</small>
                    <div>
                        @if ($order->status === 'placed')
                            <span class="badge bg-warning text-dark">{{ __('Placed') }}</span>
                        @elseif ($order->status === 'processing')
                            <span class="badge bg-info">{{ __('Processing') }}</span>
                        @elseif ($order->status === 'completed')
                            <span class="badge bg-success">{{ __('Completed') }}</span>
                        @elseif ($order->status === 'cancelled')
                            <span class="badge bg-danger">{{ __('Cancelled') }}</span>
                        @else
                            <span class="badge bg-secondary">{{ ucfirst($order->status) }}</span>
                        @endif
                    </div>
                </div>
                <div class="col-md-2">
                    <small class="text-muted">{{ __('Total') }}</small>
                    <div class="fw-bold fs-5" style="color:#1a6b3c;">{{ number_format($order->total_amount, 2) }}</div>
                </div>
            </div>
        </div>
    </div>

    <div class="row">
        <div class="col-lg-8">
            <!-- Order Items -->
            <div class="card mb-4">
                <div class="card-header">
                    <h6 class="mb-0 fw-semibold">{{ __('Order Items') }}</h6>
                </div>
                <div class="card-body p-0">
                    <div class="table-responsive">
                        <table class="table table-hover mb-0">
                            <thead>
                                <tr>
                                    <th>{{ __('Product') }}</th>
                                    <th class="text-center">{{ __('Quantity') }}</th>
                                    <th class="text-end">{{ __('Price') }}</th>
                                    <th class="text-end">{{ __('Subtotal') }}</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach ($order->items as $item)
                                    <tr>
                                        <td>
                                            <div class="d-flex align-items-center">
                                                <div class="rounded d-flex align-items-center justify-content-center bg-secondary bg-opacity-10 flex-shrink-0" style="width:40px;height:40px;">
                                                    <i class="bi bi-box-seam text-secondary"></i>
                                                </div>
                                                <div class="ms-3">
                                                    <div class="fw-medium">{{ $item->product?->name ?? __('Deleted Product') }}</div>
                                                </div>
                                            </div>
                                        </td>
                                        <td class="text-center">{{ $item->quantity }}</td>
                                        <td class="text-end text-muted">{{ number_format($item->price, 2) }}</td>
                                        <td class="text-end fw-semibold">{{ number_format($item->price * $item->quantity, 2) }}</td>
                                    </tr>
                                @endforeach
                            </tbody>
                            <tfoot>
                                <tr class="border-top">
                                    <td colspan="3" class="text-end fw-bold">{{ __('Total') }}</td>
                                    <td class="text-end fw-bold fs-5" style="color:#1a6b3c;">{{ number_format($order->total_amount, 2) }}</td>
                                </tr>
                            </tfoot>
                        </table>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-lg-4">
            <!-- Shipping Address -->
            <div class="card mb-4">
                <div class="card-header">
                    <h6 class="mb-0 fw-semibold">{{ __('Shipping Address') }}</h6>
                </div>
                <div class="card-body">
                    @if ($order->shipping_address)
                        <div style="white-space: pre-wrap;">{{ $order->shipping_address }}</div>
                    @else
                        <p class="text-muted mb-0">{{ __('No shipping address provided.') }}</p>
                    @endif
                </div>
            </div>

            <!-- Notes -->
            @if ($order->notes)
                <div class="card mb-4">
                    <div class="card-header">
                        <h6 class="mb-0 fw-semibold">{{ __('Notes') }}</h6>
                    </div>
                    <div class="card-body">
                        <div class="bg-light rounded p-3" style="white-space: pre-wrap;">{{ $order->notes }}</div>
                    </div>
                </div>
            @endif
        </div>
    </div>
@endsection
