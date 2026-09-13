@extends('layouts.owner.app')

@section('content')
    <div class="mb-4">
        <div class="d-flex justify-content-between align-items-center">
            <h2 class="h4 mb-0 fw-semibold">{{ __('My Orders') }}</h2>
        </div>
    </div>

    <!-- Orders Table -->
    <div class="card">
        <div class="card-body p-0">
            <div class="table-responsive">
                <table class="table table-hover mb-0">
                    <thead>
                        <tr>
                            <th>{{ __('Order #') }}</th>
                            <th>{{ __('Date') }}</th>
                            <th>{{ __('Items') }}</th>
                            <th>{{ __('Total') }}</th>
                            <th>{{ __('Status') }}</th>
                            <th class="text-end">{{ __('Action') }}</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse ($orders as $order)
                            <tr>
                                <td class="fw-medium">
                                    <code>{{ $order->order_number }}</code>
                                </td>
                                <td class="text-muted">
                                    {{ $order->created_at->format('M d, Y') }}
                                </td>
                                <td>
                                    <span class="badge bg-secondary bg-opacity-10 text-secondary">
                                        {{ $order->items->count() }} {{ __('item(s)') }}
                                    </span>
                                </td>
                                <td class="fw-bold" style="color:#1a6b3c;">
                                    {{ number_format($order->total_amount, 2) }}
                                </td>
                                <td>
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
                                </td>
                                <td class="text-end">
                                    <a href="{{ route('owner.orders.show', $order) }}" class="btn btn-outline-info btn-sm" title="{{ __('View') }}">
                                        <i class="bi bi-eye"></i>
                                    </a>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="6" class="text-center py-5">
                                    <div class="empty-state">
                                        <i class="bi bi-receipt" style="font-size:3rem;opacity:0.3;"></i>
                                        <p class="mt-2 text-muted">{{ __('No orders found.') }}</p>
                                        <a href="{{ route('owner.products.index') }}" class="btn btn-primary btn-sm mt-2">
                                            <i class="bi bi-shop me-1"></i>{{ __('Start Shopping') }}
                                        </a>
                                    </div>
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>

        @if ($orders->hasPages())
            <div class="card-footer bg-white">
                {{ $orders->links() }}
            </div>
        @endif
    </div>
@endsection
