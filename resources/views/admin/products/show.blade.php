@extends('layouts.app')

@section('content')
    <div class="mb-4">
        <div class="d-flex justify-content-between align-items-center">
            <h2 class="h4 mb-0 fw-semibold">{{ __('Product Details') }}</h2>
            <div class="d-flex gap-2">
                <a href="{{ route('admin.products.edit', $product) }}" class="btn btn-outline-primary btn-sm">
                    <i class="bi bi-pencil me-1"></i>{{ __('Edit') }}
                </a>
                <a href="{{ route('admin.products.index') }}" class="btn btn-outline-secondary btn-sm">
                    <i class="bi bi-arrow-left me-1"></i>{{ __('Back') }}
                </a>
            </div>
        </div>
    </div>

    <div class="row">
        <div class="col-lg-8">
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
                                <td class="fw-semibold">{{ __('Slug') }}</td>
                                <td><code>{{ $product->slug }}</code></td>
                            </tr>
                            <tr>
                                <td class="fw-semibold">{{ __('SKU') }}</td>
                                <td>
                                    <code>{{ $product->sku ?: '-' }}</code>
                                    @if ($product->skuTemplate)
                                        <small class="text-muted ms-2">({{ $product->skuTemplate->name }}: <code>{{ $product->skuTemplate->pattern }}</code>)</small>
                                    @endif
                                </td>
                            </tr>
                            <tr>
                                <td class="fw-semibold">{{ __('Category') }}</td>
                                <td>{{ $product->category?->name ?? '-' }}</td>
                            </tr>
                            <tr>
                                <td class="fw-semibold">{{ __('Description') }}</td>
                                <td>{{ $product->description ?: '-' }}</td>
                            </tr>
                            <tr>
                                <td class="fw-semibold">{{ __('Price') }}</td>
                                <td>
                                    @if ($product->special_price && $product->special_price > 0)
                                        <span class="text-decoration-line-through text-muted">{{ number_format($product->price, 2) }}</span>
                                        <span class="fw-bold text-danger ms-2">{{ number_format($product->special_price, 2) }}</span>
                                        @if ($product->discount_percent)
                                            <span class="badge bg-danger ms-2">-{{ number_format($product->discount_percent, 0) }}%</span>
                                        @endif
                                        <br><small class="text-success">{{ __('You save: ') . number_format($product->price - $product->special_price, 2) }}</small>
                                    @else
                                        <span class="fw-bold text-success">{{ number_format($product->price, 2) }}</span>
                                    @endif
                                </td>
                            </tr>
                            <tr>
                                <td class="fw-semibold">{{ __('Stock Quantity') }}</td>
                                <td>
                                    @if ($product->stock_quantity <= 0)
                                        <span class="badge bg-danger">{{ $product->stock_quantity }} {{ __('(Out of Stock)') }}</span>
                                    @elseif ($product->stock_quantity <= 5)
                                        <span class="badge bg-warning text-dark">{{ $product->stock_quantity }} {{ __('(Low Stock)') }}</span>
                                    @else
                                        <span class="badge bg-success">{{ $product->stock_quantity }}</span>
                                    @endif
                                </td>
                            </tr>
                            <tr>
                                <td class="fw-semibold">{{ __('Weight') }}</td>
                                <td>{{ $product->weight ? number_format($product->weight, 2) . ' kg' : '-' }}</td>
                            </tr>
                            <tr>
                                <td class="fw-semibold">{{ __('Featured') }}</td>
                                <td>
                                    @if ($product->is_featured)
                                        <span class="badge bg-warning text-dark"><i class="bi bi-star-fill me-1"></i>{{ __('Yes') }}</span>
                                    @else
                                        <span class="badge bg-secondary">{{ __('No') }}</span>
                                    @endif
                                </td>
                            </tr>
                            <tr>
                                <td class="fw-semibold">{{ __('Status') }}</td>
                                <td>
                                    @if ($product->status === 'active')
                                        <span class="badge bg-success">{{ __('Active') }}</span>
                                    @elseif ($product->status === 'inactive')
                                        <span class="badge bg-secondary">{{ __('Inactive') }}</span>
                                    @else
                                        <span class="badge bg-danger">{{ __('Out of Stock') }}</span>
                                    @endif
                                </td>
                            </tr>
                        </tbody>
                    </table>
                </div>
            </div>

            <!-- Images Gallery -->
            <div class="card mb-4">
                <div class="card-header">
                    <h6 class="mb-0 fw-semibold">{{ __('Product Images') }} ({{ $product->images->count() }})</h6>
                </div>
                <div class="card-body">
                    @if ($product->images->count() > 0)
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
                        <p class="text-muted mb-0">{{ __('No images uploaded.') }}</p>
                    @endif
                </div>
            </div>
        </div>

        <div class="col-lg-4">
            <!-- SEO -->
            <div class="card mb-4">
                <div class="card-header">
                    <h6 class="mb-0 fw-semibold">{{ __('SEO') }}</h6>
                </div>
                <div class="card-body">
                    <div class="mb-2">
                        <small class="text-muted">{{ __('Meta Title') }}</small>
                        <div>{{ $product->meta_title ?: '-' }}</div>
                    </div>
                    <div>
                        <small class="text-muted">{{ __('Meta Description') }}</small>
                        <div>{{ $product->meta_description ?: '-' }}</div>
                    </div>
                </div>
            </div>

            <!-- Meta -->
            <div class="card mb-4">
                <div class="card-header">
                    <h6 class="mb-0 fw-semibold">{{ __('Meta') }}</h6>
                </div>
                <div class="card-body">
                    <div class="mb-2">
                        <small class="text-muted">{{ __('Created By') }}</small>
                        <div>{{ $product->createdBy?->name ?? '-' }}</div>
                    </div>
                    <div class="mb-2">
                        <small class="text-muted">{{ __('Last Updated By') }}</small>
                        <div>{{ $product->updatedBy?->name ?? '-' }}</div>
                    </div>
                    <div class="mb-2">
                        <small class="text-muted">{{ __('Created At') }}</small>
                        <div>{{ $product->created_at->format('M d, Y H:i:s') }}</div>
                    </div>
                    <div>
                        <small class="text-muted">{{ __('Updated At') }}</small>
                        <div>{{ $product->updated_at->format('M d, Y H:i:s') }}</div>
                    </div>
                </div>
            </div>

            <!-- Danger Zone -->
            <div class="card border-danger">
                <div class="card-header">
                    <h6 class="mb-0 fw-semibold text-danger">{{ __('Danger Zone') }}</h6>
                </div>
                <div class="card-body">
                    <p class="text-muted mb-2" style="font-size:0.875rem;">
                        {{ __('Deleting this product will remove it and all its images permanently.') }}
                    </p>
                    <form action="{{ route('admin.products.destroy', $product) }}" method="POST" onsubmit="return confirm('{{ __('Are you sure you want to delete this product?') }}')">
                        @csrf
                        @method('DELETE')
                        <button type="submit" class="btn btn-danger btn-sm w-100">
                            <i class="bi bi-trash me-1"></i>{{ __('Delete Product') }}
                        </button>
                    </form>
                </div>
            </div>
        </div>
    </div>
@endsection
