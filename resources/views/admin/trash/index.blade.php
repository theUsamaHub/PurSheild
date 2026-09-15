@extends('layouts.app')

@section('content')
    <div class="mb-4">
        <h2 class="h4 mb-0 fw-semibold"><i class="bi bi-trash me-2"></i>{{ __('Recycle Bin') }}</h2>
        <small class="text-muted">{{ __('Restore or permanently delete soft-deleted items.') }}</small>
    </div>

    @if (session('success'))
        <div class="alert alert-success alert-dismissible fade show" role="alert">
            {{ session('success') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    @endif

    <!-- Stats -->
    <div class="row g-3 mb-4">
        <div class="col-md-4">
            <a href="{{ route('admin.trash.index', array_merge(request()->query(), ['type' => 'products'])) }}" class="text-decoration-none">
                <div class="card border-start border-primary border-4 {{ ($type ?? 'all') === 'products' ? 'shadow' : '' }}">
                    <div class="card-body">
                        <div class="d-flex justify-content-between align-items-center">
                            <div>
                                <div class="text-muted" style="font-size:0.75rem;">{{ __('Products') }}</div>
                                <div class="fs-4 fw-bold text-primary">{{ $stats['products'] }}</div>
                            </div>
                            <i class="bi bi-box-seam text-primary" style="font-size:2rem;opacity:0.3;"></i>
                        </div>
                    </div>
                </div>
            </a>
        </div>
        <div class="col-md-4">
            <a href="{{ route('admin.trash.index', array_merge(request()->query(), ['type' => 'categories'])) }}" class="text-decoration-none">
                <div class="card border-start border-info border-4 {{ ($type ?? 'all') === 'categories' ? 'shadow' : '' }}">
                    <div class="card-body">
                        <div class="d-flex justify-content-between align-items-center">
                            <div>
                                <div class="text-muted" style="font-size:0.75rem;">{{ __('Categories') }}</div>
                                <div class="fs-4 fw-bold text-info">{{ $stats['categories'] }}</div>
                            </div>
                            <i class="bi bi-grid text-info" style="font-size:2rem;opacity:0.3;"></i>
                        </div>
                    </div>
                </div>
            </a>
        </div>
        <div class="col-md-4">
            <a href="{{ route('admin.trash.index', array_merge(request()->query(), ['type' => 'care-content'])) }}" class="text-decoration-none">
                <div class="card border-start border-warning border-4 {{ ($type ?? 'all') === 'care-content' ? 'shadow' : '' }}">
                    <div class="card-body">
                        <div class="d-flex justify-content-between align-items-center">
                            <div>
                                <div class="text-muted" style="font-size:0.75rem;">{{ __('Care Content') }}</div>
                                <div class="fs-4 fw-bold text-warning">{{ $stats['care_contents'] }}</div>
                            </div>
                            <i class="bi bi-journal-text text-warning" style="font-size:2rem;opacity:0.3;"></i>
                        </div>
                    </div>
                </div>
            </a>
        </div>
    </div>

    <!-- Filter -->
    <div class="card mb-4">
        <div class="card-body">
            <form method="GET" action="{{ route('admin.trash.index') }}" class="row g-3 align-items-end">
                <div class="col-md-6">
                    <input type="hidden" name="type" value="{{ $type ?? 'all' }}">
                    <label class="form-label" style="font-size:0.8rem;">{{ __('Search') }}</label>
                    <input type="text" class="form-control" name="search" placeholder="{{ __('Search deleted items...') }}" value="{{ request('search') }}">
                </div>
                <div class="col-md-4">
                    <label class="form-label" style="font-size:0.8rem;">{{ __('Filter by type') }}</label>
                    <select class="form-select" onchange="this.form.submit()" name="type">
                        <option value="all" {{ ($type ?? 'all') === 'all' ? 'selected' : '' }}>{{ __('All Types') }}</option>
                        <option value="products" {{ ($type ?? '') === 'products' ? 'selected' : '' }}>{{ __('Products') }}</option>
                        <option value="categories" {{ ($type ?? '') === 'categories' ? 'selected' : '' }}>{{ __('Categories') }}</option>
                        <option value="care-content" {{ ($type ?? '') === 'care-content' ? 'selected' : '' }}>{{ __('Care Content') }}</option>
                    </select>
                </div>
                <div class="col-md-2 d-flex gap-2">
                    <button type="submit" class="btn btn-outline-secondary flex-grow-1">
                        <i class="bi bi-search"></i>
                    </button>
                    <a href="{{ route('admin.trash.index') }}" class="btn btn-outline-danger">
                        <i class="bi bi-x"></i>
                    </a>
                </div>
            </form>
        </div>
    </div>

    <!-- Products -->
    @if (($type ?? 'all') === 'all' || ($type ?? '') === 'products')
        @if ($products->count() > 0)
            <div class="card mb-4">
                <div class="card-header bg-primary bg-opacity-10">
                    <h6 class="mb-0 fw-semibold"><i class="bi bi-box-seam me-2 text-primary"></i>{{ __('Products') }} ({{ $stats['products'] }})</h6>
                </div>
                <div class="card-body p-0">
                    <div class="table-responsive">
                        <table class="table table-hover mb-0">
                            <thead>
                                <tr>
                                    <th>{{ __('Name') }}</th>
                                    <th>{{ __('Category') }}</th>
                                    <th>{{ __('Price') }}</th>
                                    <th>{{ __('Deleted At') }}</th>
                                    <th class="text-end">{{ __('Actions') }}</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach ($products as $product)
                                    <tr>
                                        <td class="fw-medium">{{ $product->name }}</td>
                                        <td>{{ $product->category?->name ?? '-' }}</td>
                                        <td>{{ number_format($product->price, 2) }}</td>
                                        <td>{{ $product->deleted_at->format('M d, Y H:i') }}</td>
                                        <td class="text-end">
                                            <div class="btn-group btn-group-sm">
                                                <form action="{{ route('admin.trash.restore', ['type' => 'product', 'id' => $product->id]) }}" method="POST" class="d-inline">
                                                    @csrf
                                                    <button type="submit" class="btn btn-outline-success" title="{{ __('Restore') }}">
                                                        <i class="bi bi-arrow-counterclockwise"></i>
                                                    </button>
                                                </form>
                                                <form action="{{ route('admin.trash.force-delete', ['type' => 'product', 'id' => $product->id]) }}" method="POST" class="d-inline" onsubmit="return confirm('{{ __('This will permanently delete this product. Are you sure?') }}')">
                                                    @csrf
                                                    @method('DELETE')
                                                    <button type="submit" class="btn btn-outline-danger" title="{{ __('Permanently Delete') }}">
                                                        <i class="bi bi-trash"></i>
                                                    </button>
                                                </form>
                                            </div>
                                        </td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                </div>
                @if ($products->hasPages())
                    <div class="card-footer bg-white">{{ $products->links() }}</div>
                @endif
            </div>
        @elseif (($type ?? '') === 'products')
            <div class="text-center py-5">
                <i class="bi bi-box-seam text-muted" style="font-size:3rem;opacity:0.3;"></i>
                <p class="mt-2 text-muted">{{ __('No trashed products.') }}</p>
            </div>
        @endif
    @endif

    <!-- Categories -->
    @if (($type ?? 'all') === 'all' || ($type ?? '') === 'categories')
        @if ($categories->count() > 0)
            <div class="card mb-4">
                <div class="card-header bg-info bg-opacity-10">
                    <h6 class="mb-0 fw-semibold"><i class="bi bi-grid me-2 text-info"></i>{{ __('Categories') }} ({{ $stats['categories'] }})</h6>
                </div>
                <div class="card-body p-0">
                    <div class="table-responsive">
                        <table class="table table-hover mb-0">
                            <thead>
                                <tr>
                                    <th>{{ __('Name') }}</th>
                                    <th>{{ __('Products') }}</th>
                                    <th>{{ __('Deleted At') }}</th>
                                    <th class="text-end">{{ __('Actions') }}</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach ($categories as $category)
                                    <tr>
                                        <td class="fw-medium">{{ $category->name }}</td>
                                        <td><span class="badge bg-info">{{ $category->products_count }}</span></td>
                                        <td>{{ $category->deleted_at->format('M d, Y H:i') }}</td>
                                        <td class="text-end">
                                            <div class="btn-group btn-group-sm">
                                                <form action="{{ route('admin.trash.restore', ['type' => 'category', 'id' => $category->id]) }}" method="POST" class="d-inline">
                                                    @csrf
                                                    <button type="submit" class="btn btn-outline-success" title="{{ __('Restore') }}">
                                                        <i class="bi bi-arrow-counterclockwise"></i>
                                                    </button>
                                                </form>
                                                <form action="{{ route('admin.trash.force-delete', ['type' => 'category', 'id' => $category->id]) }}" method="POST" class="d-inline" onsubmit="return confirm('{{ __('This will permanently delete this category. Are you sure?') }}')">
                                                    @csrf
                                                    @method('DELETE')
                                                    <button type="submit" class="btn btn-outline-danger" title="{{ __('Permanently Delete') }}">
                                                        <i class="bi bi-trash"></i>
                                                    </button>
                                                </form>
                                            </div>
                                        </td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                </div>
                @if ($categories->hasPages())
                    <div class="card-footer bg-white">{{ $categories->links() }}</div>
                @endif
            </div>
        @elseif (($type ?? '') === 'categories')
            <div class="text-center py-5">
                <i class="bi bi-grid text-muted" style="font-size:3rem;opacity:0.3;"></i>
                <p class="mt-2 text-muted">{{ __('No trashed categories.') }}</p>
            </div>
        @endif
    @endif

    <!-- Care Content -->
    @if (($type ?? 'all') === 'all' || ($type ?? '') === 'care-content')
        @if ($careContents->count() > 0)
            <div class="card mb-4">
                <div class="card-header bg-warning bg-opacity-10">
                    <h6 class="mb-0 fw-semibold"><i class="bi bi-journal-text me-2 text-warning"></i>{{ __('Care Content') }} ({{ $stats['care_contents'] }})</h6>
                </div>
                <div class="card-body p-0">
                    <div class="table-responsive">
                        <table class="table table-hover mb-0">
                            <thead>
                                <tr>
                                    <th>{{ __('Title') }}</th>
                                    <th>{{ __('Category') }}</th>
                                    <th>{{ __('Type') }}</th>
                                    <th>{{ __('Deleted At') }}</th>
                                    <th class="text-end">{{ __('Actions') }}</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach ($careContents as $item)
                                    <tr>
                                        <td class="fw-medium">{{ $item->title }}</td>
                                        <td>
                                            @php
                                                $categoryColors = [
                                                    'feeding' => 'success',
                                                    'hygiene' => 'info',
                                                    'exercise' => 'warning',
                                                    'health' => 'danger',
                                                    'training' => 'primary',
                                                ];
                                            @endphp
                                            <span class="badge bg-{{ $categoryColors[$item->category] ?? 'secondary' }}">{{ ucfirst($item->category) }}</span>
                                        </td>
                                        <td>
                                            @php
                                                $typeColors = [
                                                    'article' => 'primary',
                                                    'video' => 'info',
                                                    'faq' => 'warning',
                                                ];
                                            @endphp
                                            <span class="badge bg-{{ $typeColors[$item->content_type] ?? 'secondary' }}">{{ ucfirst($item->content_type) }}</span>
                                        </td>
                                        <td>{{ $item->deleted_at->format('M d, Y H:i') }}</td>
                                        <td class="text-end">
                                            <div class="btn-group btn-group-sm">
                                                <form action="{{ route('admin.trash.restore', ['type' => 'care-content', 'id' => $item->id]) }}" method="POST" class="d-inline">
                                                    @csrf
                                                    <button type="submit" class="btn btn-outline-success" title="{{ __('Restore') }}">
                                                        <i class="bi bi-arrow-counterclockwise"></i>
                                                    </button>
                                                </form>
                                                <form action="{{ route('admin.trash.force-delete', ['type' => 'care-content', 'id' => $item->id]) }}" method="POST" class="d-inline" onsubmit="return confirm('{{ __('This will permanently delete this content. Are you sure?') }}')">
                                                    @csrf
                                                    @method('DELETE')
                                                    <button type="submit" class="btn btn-outline-danger" title="{{ __('Permanently Delete') }}">
                                                        <i class="bi bi-trash"></i>
                                                    </button>
                                                </form>
                                            </div>
                                        </td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                </div>
                @if ($careContents->hasPages())
                    <div class="card-footer bg-white">{{ $careContents->links() }}</div>
                @endif
            </div>
        @elseif (($type ?? '') === 'care-content')
            <div class="text-center py-5">
                <i class="bi bi-journal-text text-muted" style="font-size:3rem;opacity:0.3;"></i>
                <p class="mt-2 text-muted">{{ __('No trashed care content.') }}</p>
            </div>
        @endif
    @endif

    <!-- Empty state for all -->
    @if ($products->count() === 0 && $categories->count() === 0 && $careContents->count() === 0 && ($type ?? 'all') === 'all')
        <div class="text-center py-5">
            <i class="bi bi-trash text-muted" style="font-size:4rem;opacity:0.2;"></i>
            <h5 class="mt-3 text-muted">{{ __('Recycle bin is empty') }}</h5>
            <p class="text-muted">{{ __('Deleted items will appear here.') }}</p>
        </div>
    @endif
@endsection
