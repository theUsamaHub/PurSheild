@extends('layouts.owner.app')

@section('content')
    <div class="mb-4">
        <div class="d-flex justify-content-between align-items-center">
            <h2 class="h4 mb-0 fw-semibold">{{ __('My Cart') }}</h2>
            <a href="{{ route('owner.products.index') }}" class="btn btn-outline-secondary btn-sm">
                <i class="bi bi-arrow-left me-1"></i>{{ __('Continue Shopping') }}
            </a>
        </div>
    </div>

    @if ($cart && $cart->items->count() > 0)
        <div class="row">
            <div class="col-lg-8">
                <!-- Cart Items Table -->
                <div class="card mb-4">
                    <div class="card-header">
                        <h6 class="mb-0 fw-semibold">{{ __('Cart Items') }} ({{ $cart->items->count() }})</h6>
                    </div>
                    <div class="card-body p-0">
                        <div class="table-responsive">
                            <table class="table table-hover mb-0">
                                <thead>
                                    <tr>
                                        <th>{{ __('Product') }}</th>
                                        <th>{{ __('Price') }}</th>
                                        <th style="width:150px;">{{ __('Quantity') }}</th>
                                        <th>{{ __('Subtotal') }}</th>
                                        <th class="text-end">{{ __('Action') }}</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach ($cart->items as $item)
                                        <tr>
                                            <td>
                                                <div class="d-flex align-items-center">
                                                    <div class="rounded d-flex align-items-center justify-content-center bg-secondary bg-opacity-10 flex-shrink-0" style="width:48px;height:48px;">
                                                        <i class="bi bi-box-seam text-secondary"></i>
                                                    </div>
                                                    <div class="ms-3">
                                                        <div class="fw-medium">{{ $item->product?->name ?? __('Deleted Product') }}</div>
                                                        @if ($item->product?->category)
                                                            <small class="text-muted">{{ $item->product->category->name }}</small>
                                                        @endif
                                                    </div>
                                                </div>
                                            </td>
                                            <td class="text-muted">{{ $item->product ? number_format($item->product->price, 2) : '-' }}</td>
                                            <td>
                                                <form action="{{ route('owner.cart.update', $item) }}" method="POST" class="d-flex align-items-center">
                                                    @csrf
                                                    @method('PUT')
                                                    <div class="input-group input-group-sm" style="width:120px;">
                                                        <button type="button" class="btn btn-outline-secondary" onclick="this.nextElementSibling.stepDown(); this.closest('form').submit();">
                                                            <i class="bi bi-dash"></i>
                                                        </button>
                                                        <input type="number" class="form-control text-center" name="quantity" value="{{ $item->quantity }}" min="1" max="{{ $item->product?->stock_quantity ?? 99 }}" onchange="this.closest('form').submit();">
                                                        <button type="button" class="btn btn-outline-secondary" onclick="this.previousElementSibling.stepUp(); this.closest('form').submit();">
                                                            <i class="bi bi-plus"></i>
                                                        </button>
                                                    </div>
                                                </form>
                                            </td>
                                            <td class="fw-semibold">
                                                {{ $item->product ? number_format($item->product->price * $item->quantity, 2) : '-' }}
                                            </td>
                                            <td class="text-end">
                                                <form action="{{ route('owner.cart.remove', $item) }}" method="POST" class="d-inline" onsubmit="return confirm('{{ __('Are you sure you want to remove this item?') }}')">
                                                    @csrf
                                                    @method('DELETE')
                                                    <button type="submit" class="btn btn-outline-danger btn-sm" title="{{ __('Remove') }}">
                                                        <i class="bi bi-trash"></i>
                                                    </button>
                                                </form>
                                            </td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>

            <div class="col-lg-4">
                <!-- Cart Summary -->
                <div class="card mb-4">
                    <div class="card-header">
                        <h6 class="mb-0 fw-semibold">{{ __('Order Summary') }}</h6>
                    </div>
                    <div class="card-body">
                        @php
                            $subtotal = 0;
                            foreach ($cart->items as $item) {
                                if ($item->product) {
                                    $subtotal += $item->product->price * $item->quantity;
                                }
                            }
                        @endphp
                        <div class="d-flex justify-content-between mb-2">
                            <span class="text-muted">{{ __('Subtotal') }}</span>
                            <span class="fw-medium">{{ number_format($subtotal, 2) }}</span>
                        </div>
                        <div class="d-flex justify-content-between mb-3">
                            <span class="text-muted">{{ __('Shipping') }}</span>
                            <span class="text-muted" style="font-size:0.85rem;">{{ __('Calculated at checkout') }}</span>
                        </div>
                        <hr>
                        <div class="d-flex justify-content-between mb-3">
                            <span class="fw-bold">{{ __('Total') }}</span>
                            <span class="fw-bold fs-5" style="color:#1a6b3c;">{{ number_format($subtotal, 2) }}</span>
                        </div>
                        @if (Auth::user()->address)
                            <div class="p-2 rounded mb-3" style="background:#f0f7f2;font-size:0.8rem;">
                                <i class="bi bi-geo-alt me-1" style="color:#1a6b3c;"></i>
                                <strong>{{ __('Shipping to:') }}</strong> {{ Auth::user()->address }}
                            </div>
                        @else
                            <div class="p-2 rounded mb-3" style="background:#fff3cd;font-size:0.8rem;">
                                <i class="bi bi-exclamation-triangle me-1 text-warning"></i>
                                <a href="{{ route('profile.edit') }}" class="text-decoration-none">{{ __('Add your address') }}</a> {{ __('for delivery') }}
                            </div>
                        @endif
                        <form action="{{ route('owner.cart.checkout') }}" method="POST">
                            @csrf
                            <button type="submit" class="btn btn-primary w-100">
                                <i class="bi bi-credit-card me-1"></i>{{ __('Checkout') }}
                            </button>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    @else
        <!-- Empty State -->
        <div class="card">
            <div class="card-body text-center py-5">
                <div class="empty-state">
                    <i class="bi bi-cart3" style="font-size:3rem;opacity:0.3;"></i>
                    <p class="mt-2 text-muted">{{ __('Your cart is empty.') }}</p>
                    <a href="{{ route('owner.products.index') }}" class="btn btn-primary btn-sm mt-2">
                        <i class="bi bi-shop me-1"></i>{{ __('Browse Products') }}
                    </a>
                </div>
            </div>
        </div>
    @endif
@endsection
