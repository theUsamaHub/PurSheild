@extends('layouts.owner.app')

@section('content')
    <div class="mb-4">
        <div class="d-flex justify-content-between align-items-center">
            <h2 class="h4 mb-0 fw-semibold">{{ __('Products') }}</h2>
        </div>
    </div>

    <!-- Search & Filters -->
    <div class="card mb-4">
        <div class="card-body">
            <form method="GET" action="{{ route('owner.products.index') }}" class="row g-3">
                <div class="col-md-5">
                    <input type="text" class="form-control" name="search" placeholder="{{ __('Search products...') }}" value="{{ request('search') }}">
                </div>
                <div class="col-md-4">
                    <select class="form-select" name="category_id">
                        <option value="">{{ __('All Categories') }}</option>
                        @foreach ($categories as $category)
                            <option value="{{ $category->id }}" {{ request('category_id') == $category->id ? 'selected' : '' }}>{{ $category->name }}</option>
                        @endforeach
                    </select>
                </div>
                <div class="col-md-3 d-flex gap-2">
                    <button type="submit" class="btn btn-outline-secondary flex-grow-1">
                        <i class="bi bi-search me-1"></i>{{ __('Filter') }}
                    </button>
                    <a href="{{ route('owner.products.index') }}" class="btn btn-outline-danger"><i class="bi bi-x"></i></a>
                </div>
            </form>
        </div>
    </div>

    <!-- Product Cards -->
    <div class="row g-4">
        @forelse ($products as $product)
            <div class="col-md-6 col-lg-4 col-xl-3">
                <div class="card h-100 border-0 shadow-sm">
                    <div class="d-flex align-items-center justify-content-center bg-secondary bg-opacity-10" style="height:180px;">
                        <i class="bi bi-box-seam text-secondary" style="font-size:3rem;opacity:0.3;"></i>
                    </div>
                    <div class="card-body d-flex flex-column">
                        <h6 class="card-title fw-semibold mb-1 text-truncate">{{ $product->name }}</h6>
                        <small class="text-muted mb-2">{{ $product->category?->name ?? '-' }}</small>
                        <div class="mb-2">
                            <span class="fw-bold fs-5" style="color:#1a6b3c;">{{ number_format($product->price, 2) }}</span>
                        </div>
                        <div class="mb-3">
                            @if ($product->stock_quantity > 0)
                                <span class="badge bg-success bg-opacity-10 text-success">
                                    <i class="bi bi-check-circle me-1"></i>{{ __('In Stock') }} ({{ $product->stock_quantity }})
                                </span>
                            @else
                                <span class="badge bg-danger bg-opacity-10 text-danger">
                                    <i class="bi bi-x-circle me-1"></i>{{ __('Out of Stock') }}
                                </span>
                            @endif
                        </div>
                        <div class="mt-auto">
                            <a href="{{ route('owner.products.show', $product) }}" class="btn btn-sm btn-primary w-100">
                                <i class="bi bi-eye me-1"></i>{{ __('View') }}
                            </a>
                        </div>
                    </div>
                </div>
            </div>
        @empty
            <div class="col-12">
                <div class="card">
                    <div class="card-body text-center py-5">
                        <div class="empty-state">
                            <i class="bi bi-box-seam" style="font-size:3rem;opacity:0.3;"></i>
                            <p class="mt-2 text-muted">{{ __('No products found.') }}</p>
                        </div>
                    </div>
                </div>
            </div>
        @endforelse
    </div>

    @if ($products->hasPages())
        <div class="mt-4">
            {{ $products->links() }}
        </div>
    @endif
@endsection
