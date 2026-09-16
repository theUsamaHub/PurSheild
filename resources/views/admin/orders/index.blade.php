@extends('layouts.app')

@section('content')
    <div class="mb-4">
        <h2 class="h4 mb-0 fw-semibold">{{ __('Orders') }}</h2>
    </div>

    {{-- Stats --}}
    <div class="row g-3 mb-4">
        <div class="col-md-2">
            <div class="card border-0" style="border-left:4px solid var(--bs-primary);">
                <div class="card-body py-2">
                    <div class="text-muted" style="font-size:0.7rem;">{{ __('Total') }}</div>
                    <div class="fw-bold fs-5">{{ $stats['total'] }}</div>
                </div>
            </div>
        </div>
        <div class="col-md-2">
            <div class="card border-0" style="border-left:4px solid var(--bs-warning);">
                <div class="card-body py-2">
                    <div class="text-muted" style="font-size:0.7rem;">{{ __('Placed') }}</div>
                    <div class="fw-bold fs-5">{{ $stats['placed'] }}</div>
                </div>
            </div>
        </div>
        <div class="col-md-2">
            <div class="card border-0" style="border-left:4px solid var(--bs-info);">
                <div class="card-body py-2">
                    <div class="text-muted" style="font-size:0.7rem;">{{ __('Processing') }}</div>
                    <div class="fw-bold fs-5">{{ $stats['processing'] }}</div>
                </div>
            </div>
        </div>
        <div class="col-md-2">
            <div class="card border-0" style="border-left:4px solid var(--bs-success);">
                <div class="card-body py-2">
                    <div class="text-muted" style="font-size:0.7rem;">{{ __('Completed') }}</div>
                    <div class="fw-bold fs-5">{{ $stats['completed'] }}</div>
                </div>
            </div>
        </div>
        <div class="col-md-2">
            <div class="card border-0" style="border-left:4px solid var(--bs-danger);">
                <div class="card-body py-2">
                    <div class="text-muted" style="font-size:0.7rem;">{{ __('Cancelled') }}</div>
                    <div class="fw-bold fs-5">{{ $stats['cancelled'] }}</div>
                </div>
            </div>
        </div>
        <div class="col-md-2">
            <div class="card border-0" style="border-left:4px solid #1a6b3c;">
                <div class="card-body py-2">
                    <div class="text-muted" style="font-size:0.7rem;">{{ __('Revenue') }}</div>
                    <div class="fw-bold fs-5">${{ number_format($stats['revenue'], 0) }}</div>
                </div>
            </div>
        </div>
    </div>

    {{-- Filter --}}
    <div class="card mb-4">
        <div class="card-body">
            <form method="GET" action="{{ route('admin.orders.index') }}" class="row g-3">
                <div class="col-md-3">
                    <input type="text" class="form-control" name="search" placeholder="{{ __('Search order #, customer...') }}" value="{{ request('search') }}">
                </div>
                <div class="col-md-2">
                    <select class="form-select" name="status">
                        <option value="">{{ __('All Status') }}</option>
                        <option value="placed" {{ request('status') === 'placed' ? 'selected' : '' }}>{{ __('Placed') }}</option>
                        <option value="processing" {{ request('status') === 'processing' ? 'selected' : '' }}>{{ __('Processing') }}</option>
                        <option value="completed" {{ request('status') === 'completed' ? 'selected' : '' }}>{{ __('Completed') }}</option>
                        <option value="cancelled" {{ request('status') === 'cancelled' ? 'selected' : '' }}>{{ __('Cancelled') }}</option>
                    </select>
                </div>
                <div class="col-md-2">
                    <input type="date" class="form-control" name="date_from" value="{{ request('date_from') }}" placeholder="{{ __('From') }}">
                </div>
                <div class="col-md-2">
                    <input type="date" class="form-control" name="date_to" value="{{ request('date_to') }}" placeholder="{{ __('To') }}">
                </div>
                <div class="col-md-3 d-flex gap-2">
                    <button type="submit" class="btn btn-outline-secondary flex-grow-1">
                        <i class="bi bi-search me-1"></i>{{ __('Filter') }}
                    </button>
                    <a href="{{ route('admin.orders.index') }}" class="btn btn-outline-danger"><i class="bi bi-x"></i></a>
                </div>
            </form>
        </div>
    </div>

    {{-- Orders Table --}}
    <div class="card">
        <div class="card-body p-0">
            @if($orders->count())
                <div class="table-responsive">
                    <table class="table table-hover mb-0">
                        <thead class="table-light">
                            <tr>
                                <th style="font-size:0.8rem;">{{ __('Order #') }}</th>
                                <th style="font-size:0.8rem;">{{ __('Customer') }}</th>
                                <th style="font-size:0.8rem;">{{ __('Items') }}</th>
                                <th style="font-size:0.8rem;">{{ __('Total') }}</th>
                                <th style="font-size:0.8rem;">{{ __('Status') }}</th>
                                <th style="font-size:0.8rem;">{{ __('Date') }}</th>
                                <th style="font-size:0.8rem;"></th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($orders as $order)
                                <tr>
                                    <td><span class="fw-semibold" style="font-size:0.85rem;">#{{ $order->order_number }}</span></td>
                                    <td>
                                        <div style="font-size:0.85rem;">{{ $order->owner->name ?? '-' }}</div>
                                        <small class="text-muted" style="font-size:0.7rem;">{{ $order->owner->email ?? '' }}</small>
                                    </td>
                                    <td style="font-size:0.85rem;">{{ $order->items->count() }}</td>
                                    <td class="fw-bold" style="font-size:0.85rem;">${{ number_format($order->total_amount, 2) }}</td>
                                    <td>
                                        @if($order->status === 'placed')
                                            <span class="badge bg-warning text-dark">{{ __('Placed') }}</span>
                                        @elseif($order->status === 'processing')
                                            <span class="badge bg-info">{{ __('Processing') }}</span>
                                        @elseif($order->status === 'completed')
                                            <span class="badge bg-success">{{ __('Completed') }}</span>
                                        @elseif($order->status === 'cancelled')
                                            <span class="badge bg-danger">{{ __('Cancelled') }}</span>
                                        @endif
                                    </td>
                                    <td style="font-size:0.85rem;">{{ $order->created_at->format('M d, Y') }}</td>
                                    <td>
                                        <a href="{{ route('admin.orders.show', $order) }}" class="btn btn-sm btn-outline-primary">
                                            <i class="bi bi-eye"></i>
                                        </a>
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            @else
                <div class="text-center py-5">
                    <i class="bi bi-bag" style="font-size:3rem;opacity:0.3;"></i>
                    <p class="mt-2 text-muted">{{ __('No orders found.') }}</p>
                </div>
            @endif
        </div>
        @if($orders->hasPages())
            <div class="card-footer">{{ $orders->links() }}</div>
        @endif
    </div>
@endsection
