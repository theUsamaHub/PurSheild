@extends('layouts.app')

@section('content')
    <div class="mb-4">
        <div class="d-flex justify-content-between align-items-center">
            <h2 class="h4 mb-0 fw-semibold">{{ __('Products') }}</h2>
            <a href="{{ route('admin.products.create') }}" class="btn btn-primary btn-sm">
                <i class="bi bi-plus-circle me-1"></i>{{ __('Add Product') }}
            </a>
        </div>
    </div>

    <!-- Stats -->
    <div class="row g-3 mb-4">
        <div class="col-md-4">
            <div class="card border-start border-primary border-4">
                <div class="card-body">
                    <div class="d-flex justify-content-between align-items-center">
                        <div>
                            <div class="text-muted mb-1" style="font-size:0.8rem;">{{ __('Total Products') }}</div>
                            <div class="fw-bold fs-4">{{ $stats['total'] }}</div>
                        </div>
                        <i class="bi bi-box-seam text-primary" style="font-size:2rem;opacity:0.3;"></i>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-md-4">
            <div class="card border-start border-success border-4">
                <div class="card-body">
                    <div class="d-flex justify-content-between align-items-center">
                        <div>
                            <div class="text-muted mb-1" style="font-size:0.8rem;">{{ __('Active') }}</div>
                            <div class="fw-bold fs-4">{{ $stats['active'] }}</div>
                        </div>
                        <i class="bi bi-check-circle text-success" style="font-size:2rem;opacity:0.3;"></i>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-md-4">
            <div class="card border-start border-danger border-4">
                <div class="card-body">
                    <div class="d-flex justify-content-between align-items-center">
                        <div>
                            <div class="text-muted mb-1" style="font-size:0.8rem;">{{ __('Out of Stock') }}</div>
                            <div class="fw-bold fs-4">{{ $stats['out_of_stock'] }}</div>
                        </div>
                        <i class="bi bi-exclamation-triangle text-danger" style="font-size:2rem;opacity:0.3;"></i>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Filters -->
    <div class="card mb-4">
        <div class="card-body">
            <form method="GET" action="{{ route('admin.products.index') }}" class="row g-3">
                <div class="col-md-4">
                    <input type="text" class="form-control" name="search" placeholder="{{ __('Search by name, SKU...') }}" value="{{ request('search') }}">
                </div>
                <div class="col-md-3">
                    <select class="form-select" name="category_id">
                        <option value="">{{ __('All Categories') }}</option>
                        @foreach ($categories as $category)
                            <option value="{{ $category->id }}" {{ request('category_id') == $category->id ? 'selected' : '' }}>{{ $category->name }}</option>
                        @endforeach
                    </select>
                </div>
                <div class="col-md-3">
                    <select class="form-select" name="status">
                        <option value="">{{ __('All Status') }}</option>
                        <option value="active" {{ request('status') === 'active' ? 'selected' : '' }}>{{ __('Active') }}</option>
                        <option value="inactive" {{ request('status') === 'inactive' ? 'selected' : '' }}>{{ __('Inactive') }}</option>
                    </select>
                </div>
                <div class="col-md-2">
                    <button type="submit" class="btn btn-outline-secondary w-100">
                        <i class="bi bi-search me-1"></i>{{ __('Filter') }}
                    </button>
                </div>
            </form>
        </div>
    </div>

    <!-- Products Table -->
    <div class="card">
        <div class="card-body p-0">
            <div class="table-responsive">
                <table class="table table-hover mb-0">
                    <thead>
                        <tr>
                            <th style="width:50px;">{{ __('Image') }}</th>
                            <th>{{ __('Name') }}</th>
                            <th>{{ __('SKU') }}</th>
                            <th>{{ __('Category') }}</th>
                            <th>{{ __('Price') }}</th>
                            <th>{{ __('Stock') }}</th>
                            <th>{{ __('Status') }}</th>
                            <th class="text-end">{{ __('Actions') }}</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse ($products as $product)
                            <tr>
                                <td>
                                    @php $primaryImage = $product->images->where('is_primary', true)->first() ?? $product->images->first(); @endphp
                                    @if ($primaryImage)
                                        <img src="{{ Storage::url($primaryImage->image_path) }}" alt="{{ $product->name }}" class="rounded" style="width:40px;height:40px;object-fit:cover;">
                                    @else
                                        <div class="rounded d-flex align-items-center justify-content-center bg-secondary bg-opacity-25" style="width:40px;height:40px;">
                                            <i class="bi bi-image text-secondary"></i>
                                        </div>
                                    @endif
                                </td>
                                <td class="fw-medium">
                                    {{ $product->name }}
                                    @if ($product->is_featured)
                                        <span class="badge bg-warning text-dark ms-1" style="font-size:0.65rem;">{{ __('Featured') }}</span>
                                    @endif
                                </td>
                                <td><code>{{ $product->sku ?: '-' }}</code></td>
                                <td>{{ $product->category?->name ?? '-' }}</td>
                                <td>{{ number_format($product->price, 2) }}</td>
                                <td>
                                    @if ($product->stock_quantity <= 0)
                                        <span class="text-danger fw-semibold">{{ $product->stock_quantity }}</span>
                                    @elseif ($product->stock_quantity <= 5)
                                        <span class="text-warning fw-semibold">{{ $product->stock_quantity }}</span>
                                    @else
                                        <span class="text-success fw-semibold">{{ $product->stock_quantity }}</span>
                                    @endif
                                </td>
                                <td>
                                    @if ($product->status === 'active')
                                        <span class="badge bg-success">{{ __('Active') }}</span>
                                    @else
                                        <span class="badge bg-secondary">{{ __('Inactive') }}</span>
                                    @endif
                                </td>
                                <td class="text-end">
                                    <div class="btn-group btn-group-sm">
                                        <a href="{{ route('admin.products.show', $product) }}" class="btn btn-outline-info" title="{{ __('View') }}">
                                            <i class="bi bi-eye"></i>
                                        </a>
                                        <a href="{{ route('admin.products.edit', $product) }}" class="btn btn-outline-primary" title="{{ __('Edit') }}">
                                            <i class="bi bi-pencil"></i>
                                        </a>
                                        <form action="{{ route('admin.products.destroy', $product) }}" method="POST" class="d-inline" onsubmit="return confirm('{{ __('Are you sure you want to delete this product?') }}')">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit" class="btn btn-outline-danger" title="{{ __('Delete') }}">
                                                <i class="bi bi-trash"></i>
                                            </button>
                                        </form>
                                    </div>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="8" class="text-center py-5">
                                    <div class="empty-state">
                                        <i class="bi bi-box-seam" style="font-size:3rem;opacity:0.3;"></i>
                                        <p class="mt-2 text-muted">{{ __('No products found.') }}</p>
                                        <a href="{{ route('admin.products.create') }}" class="btn btn-primary btn-sm mt-2">
                                            {{ __('Create your first product') }}
                                        </a>
                                    </div>
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>

        @if ($products->hasPages())
            <div class="card-footer bg-white">
                {{ $products->links() }}
            </div>
        @endif
    </div>
@endsection
