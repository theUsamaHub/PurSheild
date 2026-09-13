@extends('layouts.owner.app')

@section('content')
    <div class="mb-4">
        <div class="d-flex justify-content-between align-items-center">
            <h2 class="h4 mb-0 fw-semibold">{{ __('Product Details') }}</h2>
            <a href="{{ route('owner.products.index') }}" class="btn btn-outline-secondary btn-sm">
                <i class="bi bi-arrow-left me-1"></i>{{ __('Back') }}
            </a>
        </div>
    </div>

    <div class="row">
        <div class="col-lg-8">
            <!-- Product Info -->
            <div class="card mb-4">
                <div class="card-header">
                    <h6 class="mb-0 fw-semibold">{{ __('General Information') }}</h6>
                </div>
                <div class="card-body p-0">
                    <table class="table mb-0">
                        <tbody>
                            <tr>
                                <td class="fw-semibold" style="width:200px;">{{ __('Name') }}</td>
                                <td>{{ $product->name }}</td>
                            </tr>
                            <tr>
                                <td class="fw-semibold">{{ __('SKU') }}</td>
                                <td><code>{{ $product->sku ?: '-' }}</code></td>
                            </tr>
                            <tr>
                                <td class="fw-semibold">{{ __('Category') }}</td>
                                <td>{{ $product->category?->name ?? '-' }}</td>
                            </tr>
                            <tr>
                                <td class="fw-semibold">{{ __('Price') }}</td>
                                <td class="fw-bold" style="color:#1a6b3c;">{{ number_format($product->price, 2) }}</td>
                            </tr>
                            <tr>
                                <td class="fw-semibold">{{ __('Stock Status') }}</td>
                                <td>
                                    @if ($product->stock_quantity > 0)
                                        <span class="badge bg-success">
                                            <i class="bi bi-check-circle me-1"></i>{{ __('In Stock') }} ({{ $product->stock_quantity }})
                                        </span>
                                    @else
                                        <span class="badge bg-danger">
                                            <i class="bi bi-x-circle me-1"></i>{{ __('Out of Stock') }}
                                        </span>
                                    @endif
                                </td>
                            </tr>
                            <tr>
                                <td class="fw-semibold">{{ __('Description') }}</td>
                                <td>{{ $product->description ?: '-' }}</td>
                            </tr>
                        </tbody>
                    </table>
                </div>
            </div>

            <!-- Image Gallery -->
            <div class="card mb-4">
                <div class="card-header">
                    <h6 class="mb-0 fw-semibold">{{ __('Product Images') }}</h6>
                </div>
                <div class="card-body">
                    @if ($product->images && $product->images->count() > 0)
                        <div class="d-flex flex-wrap gap-3">
                            @foreach ($product->images->sortBy('sort_order') as $image)
                                <div class="position-relative">
                                    <img src="{{ asset('storage/' . $image->image_path) }}" alt="{{ $product->name }}" class="rounded border" style="width:120px;height:120px;object-fit:cover;">
                                    @if ($image->is_primary)
                                        <span class="position-absolute top-0 start-0 badge bg-warning" style="font-size:0.65rem;"><i class="bi bi-star-fill me-1"></i>{{ __('Primary') }}</span>
                                    @endif
                                </div>
                            @endforeach
                        </div>
                    @else
                        <div class="d-flex align-items-center justify-content-center bg-secondary bg-opacity-10 rounded" style="height:160px;">
                            <div class="text-center">
                                <i class="bi bi-image text-secondary" style="font-size:2.5rem;opacity:0.3;"></i>
                                <p class="text-muted mt-2 mb-0" style="font-size:0.85rem;">{{ __('No images available') }}</p>
                            </div>
                        </div>
                    @endif
                </div>
            </div>

            <!-- Reviews -->
            <div class="card mb-4">
                <div class="card-header">
                    <h6 class="mb-0 fw-semibold">{{ __('Reviews') }} ({{ $product->reviews->count() }})</h6>
                </div>
                <div class="card-body">
                    @forelse ($product->reviews as $review)
                        <div class="{{ !$loop->last ? 'border-bottom pb-3 mb-3' : '' }}">
                            <div class="d-flex align-items-center mb-2">
                                <div class="rounded-circle d-flex align-items-center justify-content-center" style="width:32px;height:32px;background:linear-gradient(135deg,#1a6b3c,#2e9e5a);">
                                    <span class="text-white fw-semibold" style="font-size:0.7rem;">{{ substr($review->user?->name ?? 'A', 0, 1) }}</span>
                                </div>
                                <div class="ms-2">
                                    <span class="fw-medium" style="font-size:0.85rem;">{{ $review->user?->name ?? __('Anonymous') }}</span>
                                    <small class="text-muted ms-2">{{ $review->created_at->diffForHumans() }}</small>
                                </div>
                            </div>
                            <div class="mb-1">
                                @for ($i = 1; $i <= 5; $i++)
                                    @if ($i <= $review->rating)
                                        <i class="bi bi-star-fill text-warning" style="font-size:0.8rem;"></i>
                                    @else
                                        <i class="bi bi-star text-warning" style="font-size:0.8rem;"></i>
                                    @endif
                                @endfor
                            </div>
                            @if ($review->comment)
                                <p class="mb-0 text-muted" style="font-size:0.85rem;">{{ $review->comment }}</p>
                            @endif
                        </div>
                    @empty
                        <div class="text-center py-3">
                            <i class="bi bi-chat-left-text text-muted" style="font-size:2rem;opacity:0.3;"></i>
                            <p class="text-muted mt-2 mb-0" style="font-size:0.85rem;">{{ __('No reviews yet.') }}</p>
                        </div>
                    @endforelse
                </div>
            </div>
        </div>

        <div class="col-lg-4">
            <!-- Add to Cart -->
            <div class="card mb-4">
                <div class="card-header">
                    <h6 class="mb-0 fw-semibold">{{ __('Add to Cart') }}</h6>
                </div>
                <div class="card-body">
                    @if ($product->stock_quantity > 0)
                        <form action="{{ route('owner.cart.add') }}" method="POST">
                            @csrf
                            <input type="hidden" name="product_id" value="{{ $product->id }}">
                            <div class="mb-3">
                                <label for="quantity" class="form-label fw-semibold">{{ __('Quantity') }}</label>
                                <div class="input-group" style="max-width:160px;">
                                    <button type="button" class="btn btn-outline-secondary" onclick="this.nextElementSibling.stepDown()">
                                        <i class="bi bi-dash"></i>
                                    </button>
                                    <input type="number" class="form-control text-center" id="quantity" name="quantity" value="1" min="1" max="{{ $product->stock_quantity }}">
                                    <button type="button" class="btn btn-outline-secondary" onclick="this.previousElementSibling.stepUp()">
                                        <i class="bi bi-plus"></i>
                                    </button>
                                </div>
                            </div>
                            <button type="submit" class="btn btn-primary w-100">
                                <i class="bi bi-cart-plus me-1"></i>{{ __('Add to Cart') }}
                            </button>
                        </form>
                    @else
                        <div class="alert alert-secondary mb-0" style="font-size:0.85rem;">
                            <i class="bi bi-info-circle me-1"></i>{{ __('This product is currently out of stock.') }}
                        </div>
                    @endif
                </div>
            </div>

            <!-- Product Meta -->
            <div class="card">
                <div class="card-header">
                    <h6 class="mb-0 fw-semibold">{{ __('Details') }}</h6>
                </div>
                <div class="card-body">
                    <div class="d-flex justify-content-between mb-2">
                        <span class="text-muted" style="font-size:0.85rem;">{{ __('Category') }}</span>
                        <span style="font-size:0.85rem;">{{ $product->category?->name ?? '-' }}</span>
                    </div>
                    <div class="d-flex justify-content-between mb-2">
                        <span class="text-muted" style="font-size:0.85rem;">{{ __('SKU') }}</span>
                        <code style="font-size:0.8rem;">{{ $product->sku ?: '-' }}</code>
                    </div>
                    <div class="d-flex justify-content-between mb-2">
                        <span class="text-muted" style="font-size:0.85rem;">{{ __('Price') }}</span>
                        <span class="fw-bold" style="color:#1a6b3c;font-size:0.85rem;">{{ number_format($product->price, 2) }}</span>
                    </div>
                    <div class="d-flex justify-content-between">
                        <span class="text-muted" style="font-size:0.85rem;">{{ __('Availability') }}</span>
                        @if ($product->stock_quantity > 0)
                            <span class="badge bg-success" style="font-size:0.7rem;">{{ __('In Stock') }}</span>
                        @else
                            <span class="badge bg-danger" style="font-size:0.7rem;">{{ __('Out of Stock') }}</span>
                        @endif
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection
