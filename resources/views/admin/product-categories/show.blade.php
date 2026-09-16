@extends('layouts.app')

@section('content')
    <div class="mb-4">
        <div class="d-flex justify-content-between align-items-center">
            <h2 class="h4 mb-0 fw-semibold">{{ $category->name }}</h2>
            <div class="d-flex gap-2">
                <a href="{{ route('admin.product-categories.edit', $category->id) }}" class="btn btn-outline-primary btn-sm">
                    <i class="bi bi-pencil me-1"></i>{{ __('Edit') }}
                </a>
                <a href="{{ route('admin.product-categories.index') }}" class="btn btn-outline-secondary btn-sm">
                    <i class="bi bi-arrow-left me-1"></i>{{ __('Back') }}
                </a>
            </div>
        </div>
    </div>

    <div class="row">
        <div class="col-lg-8">
            <!-- Products -->
            <div class="card mb-4">
                <div class="card-header">
                    <h6 class="mb-0 fw-semibold">{{ __('Products') }} ({{ $category->products_count }})</h6>
                </div>
                <div class="card-body p-0">
                    @if ($products->count() > 0)
                        <div class="table-responsive">
                            <table class="table table-hover mb-0">
                                <thead>
                                    <tr>
                                        <th style="width:50px;">{{ __('Image') }}</th>
                                        <th>{{ __('Name') }}</th>
                                        <th>{{ __('Price') }}</th>
                                        <th>{{ __('Stock') }}</th>
                                        <th>{{ __('Status') }}</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach ($products as $product)
                                        <tr>
                                            <td>
                                                @php $primaryImage = $product->images->where('is_primary', true)->first() ?? $product->images->first(); @endphp
                                                @if ($primaryImage)
                                                    <img src="{{ asset('storage/' . $primaryImage->image_path) }}" alt="{{ $product->name }}" class="rounded" style="width:40px;height:40px;object-fit:cover;">
                                                @else
                                                    <div class="rounded d-flex align-items-center justify-content-center bg-secondary bg-opacity-25" style="width:40px;height:40px;">
                                                        <i class="bi bi-image text-secondary"></i>
                                                    </div>
                                                @endif
                                            </td>
                                            <td class="fw-medium">
                                                <a href="{{ route('admin.products.show', $product) }}" class="text-decoration-none">{{ $product->name }}</a>
                                            </td>
                                            <td>{{ number_format($product->price, 2) }}</td>
                                            <td>{{ $product->stock_quantity }}</td>
                                            <td>
                                                @if ($product->status === 'active')
                                                    <span class="badge bg-success">{{ __('Active') }}</span>
                                                @else
                                                    <span class="badge bg-secondary">{{ __('Inactive') }}</span>
                                                @endif
                                            </td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>

                        @if ($products->hasPages())
                            <div class="card-footer bg-white">
                                {{ $products->links() }}
                            </div>
                        @endif
                    @else
                        <div class="text-center py-5">
                            <i class="bi bi-box-seam" style="font-size:3rem;opacity:0.3;"></i>
                            <p class="mt-2 text-muted">{{ __('No products in this category.') }}</p>
                            <a href="{{ route('admin.products.create') }}" class="btn btn-primary btn-sm mt-2">
                                {{ __('Add a product') }}
                            </a>
                        </div>
                    @endif
                </div>
            </div>
        </div>

        <div class="col-lg-4">
            <!-- Category Info -->
            <div class="card mb-4">
                <div class="card-header">
                    <h6 class="mb-0 fw-semibold">{{ __('Category Info') }}</h6>
                </div>
                <div class="card-body">
                    @if ($category->image)
                        <div class="mb-3 text-center">
                            <img src="{{ asset('storage/' . $category->image) }}" alt="{{ $category->name }}" class="img-thumbnail" style="max-width:200px;">
                        </div>
                    @endif

                    <div class="mb-2">
                        <small class="text-muted">{{ __('Description') }}</small>
                        <div>{{ $category->description ?: '-' }}</div>
                    </div>
                    <div class="mb-2">
                        <small class="text-muted">{{ __('Slug') }}</small>
                        <div><code>{{ $category->slug }}</code></div>
                    </div>
                    <div class="mb-2">
                        <small class="text-muted">{{ __('Sort Order') }}</small>
                        <div>{{ $category->sort_order }}</div>
                    </div>
                    <div class="mb-2">
                        <small class="text-muted">{{ __('Status') }}</small>
                        <div>
                            @if ($category->is_active)
                                <span class="badge bg-success">{{ __('Active') }}</span>
                            @else
                                <span class="badge bg-secondary">{{ __('Inactive') }}</span>
                            @endif
                        </div>
                    </div>
                    <div class="mb-2">
                        <small class="text-muted">{{ __('Products Count') }}</small>
                        <div><span class="badge bg-info">{{ $category->products_count }}</span></div>
                    </div>
                    <div class="mb-2">
                        <small class="text-muted">{{ __('Created At') }}</small>
                        <div>{{ $category->created_at->format('M d, Y H:i:s') }}</div>
                    </div>
                    <div>
                        <small class="text-muted">{{ __('Last Updated') }}</small>
                        <div>{{ $category->updated_at->format('M d, Y H:i:s') }}</div>
                    </div>
                </div>
            </div>

            <!-- Danger Zone -->
            <div class="card border-danger">
                <div class="card-header">
                    <h6 class="mb-0 fw-semibold text-danger">{{ __('Danger Zone') }}</h6>
                </div>
                <div class="card-body">
                    @if ($category->products_count > 0)
                        <p class="text-muted mb-2" style="font-size:0.875rem;">
                            {{ __('This category has') }} {{ $category->products_count }} {{ __('product(s). Remove all products before deleting.') }}
                        </p>
                        <button type="button" class="btn btn-danger btn-sm w-100" disabled>
                            <i class="bi bi-trash me-1"></i>{{ __('Delete Category') }}
                        </button>
                    @else
                        <p class="text-muted mb-2" style="font-size:0.875rem;">
                            {{ __('Deleting this category will remove it permanently.') }}
                        </p>
                        <form action="{{ route('admin.product-categories.destroy', $category->id) }}" method="POST" onsubmit="return confirm('{{ __('Are you sure you want to delete this category?') }}')">
                            @csrf
                            @method('DELETE')
                            <button type="submit" class="btn btn-danger btn-sm w-100">
                                <i class="bi bi-trash me-1"></i>{{ __('Delete Category') }}
                            </button>
                        </form>
                    @endif
                </div>
            </div>
        </div>
    </div>
@endsection
