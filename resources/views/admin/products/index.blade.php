@extends('layouts.app')

@section('page-title', __('Pet Products Management'))

@section('content')
<style>
    .admin-page-header {
        background: #ffffff;
        border: 1px solid #e6f0eb;
        border-radius: 16px;
        padding: 20px 24px;
        margin-bottom: 24px;
        box-shadow: 0 4px 18px rgba(0, 0, 0, 0.02);
    }
    .admin-card {
        background: #ffffff;
        border: 1px solid #e6f0eb;
        border-radius: 16px;
        box-shadow: 0 4px 18px rgba(0, 0, 0, 0.02);
        overflow: hidden;
    }
    .admin-stat-card {
        background: #ffffff;
        border: 1px solid #e6f0eb;
        border-radius: 16px;
        padding: 18px;
        box-shadow: 0 4px 18px rgba(0, 0, 0, 0.02);
        height: 100%;
    }
    .stat-icon {
        width: 44px;
        height: 44px;
        border-radius: 12px;
        display: flex;
        align-items: center;
        justify-content: center;
        font-size: 1.25rem;
    }
    .admin-table th {
        background: #f0f7f4 !important;
        color: #074f3e;
        font-weight: 700;
        font-size: 0.8rem;
        text-transform: uppercase;
        letter-spacing: 0.05em;
        padding: 14px 18px;
        border-bottom: 1px solid #e6f0eb;
    }
    .admin-table td {
        padding: 14px 18px;
        vertical-align: middle;
        font-size: 0.88rem;
        border-bottom: 1px solid #f2f7f4;
    }
    .admin-table tbody tr:hover {
        background: #f8fcf9;
    }
    .btn-mint-primary {
        background: #087657;
        color: #ffffff;
        border-radius: 10px;
        font-weight: 600;
        padding: 8px 18px;
        transition: all 0.2s ease;
        text-decoration: none;
    }
    .btn-mint-primary:hover {
        background: #065c44;
        color: #ffffff;
        box-shadow: 0 4px 14px rgba(8, 118, 87, 0.25);
    }
    .badge-soft-success { background: #e6f7f1; color: #087657; font-weight: 600; padding: 4px 10px; border-radius: 20px; }
    .badge-soft-secondary { background: #f3f4f6; color: #6b7280; font-weight: 600; padding: 4px 10px; border-radius: 20px; }
    .badge-soft-danger { background: #ffe4e6; color: #e11d48; font-weight: 600; padding: 4px 10px; border-radius: 20px; }
    .badge-soft-warning { background: #fef3c7; color: #d97706; font-weight: 600; padding: 4px 10px; border-radius: 20px; }
    .sku-code { background: #f0fdf4; color: #166534; border: 1px solid #bbf7d0; font-family: monospace; font-size: 0.78rem; padding: 2px 7px; border-radius: 6px; }
</style>

<!-- Header Banner -->
<div class="admin-page-header d-flex justify-content-between align-items-center">
    <div class="d-flex align-items-center gap-2">
        <div class="p-2 rounded-3 text-success" style="background:#e6f7f1;">
            <i class="bi bi-box-seam-fill fs-5"></i>
        </div>
        <div>
            <h4 class="mb-0 fw-bold text-dark">{{ __('Pet Products Inventory') }}</h4>
            <small class="text-muted">{{ __('Manage shop products, pricing, stock levels and categories.') }}</small>
        </div>
    </div>
    <a href="{{ route('admin.products.create') }}" class="btn-mint-primary">
        <i class="bi bi-plus-lg me-1"></i> {{ __('Add Product') }}
    </a>
</div>

<!-- Stats Row -->
<div class="row g-3 mb-4">
    <div class="col-md-3">
        <div class="admin-stat-card d-flex justify-content-between align-items-center">
            <div>
                <small class="text-muted fw-semibold d-block mb-1">{{ __('Total Products') }}</small>
                <h3 class="fw-bold mb-0 text-dark">{{ $stats['total'] }}</h3>
            </div>
            <div class="stat-icon" style="background:#e6f7f1; color:#087657;">
                <i class="bi bi-box-seam"></i>
            </div>
        </div>
    </div>
    <div class="col-md-3">
        <div class="admin-stat-card d-flex justify-content-between align-items-center">
            <div>
                <small class="text-muted fw-semibold d-block mb-1">{{ __('Active Products') }}</small>
                <h3 class="fw-bold mb-0 text-dark">{{ $stats['active'] }}</h3>
            </div>
            <div class="stat-icon" style="background:#e0f2fe; color:#0284c7;">
                <i class="bi bi-check-circle"></i>
            </div>
        </div>
    </div>
    <div class="col-md-3">
        <div class="admin-stat-card d-flex justify-content-between align-items-center">
            <div>
                <small class="text-muted fw-semibold d-block mb-1">{{ __('Inactive Products') }}</small>
                <h3 class="fw-bold mb-0 text-dark">{{ $stats['inactive'] }}</h3>
            </div>
            <div class="stat-icon" style="background:#f3f4f6; color:#6b7280;">
                <i class="bi bi-pause-circle"></i>
            </div>
        </div>
    </div>
    <div class="col-md-3">
        <div class="admin-stat-card d-flex justify-content-between align-items-center">
            <div>
                <small class="text-muted fw-semibold d-block mb-1">{{ __('Out of Stock') }}</small>
                <h3 class="fw-bold mb-0 text-danger">{{ $stats['out_of_stock'] }}</h3>
            </div>
            <div class="stat-icon" style="background:#ffe4e6; color:#e11d48;">
                <i class="bi bi-exclamation-triangle"></i>
            </div>
        </div>
    </div>
</div>

<!-- Filters & Bulk Actions -->
<form method="GET" action="{{ route('admin.products.index') }}">
    <div class="admin-card mb-4 p-3" style="background:#fafdfb;">
        <div class="row g-2 align-items-center">
            <div class="col-md-3">
                <div class="input-group">
                    <span class="input-group-text bg-white border-end-0 text-muted" style="border-color:#d4ebe2;"><i class="bi bi-search"></i></span>
                    <input type="text" class="form-control border-start-0 ps-0" name="search" style="border-color:#d4ebe2;" placeholder="{{ __('Search name, SKU...') }}" value="{{ request('search') }}">
                </div>
            </div>
            <div class="col-md-2">
                <select class="form-select" name="category_id" style="border-color:#d4ebe2;">
                    <option value="">{{ __('All Categories') }}</option>
                    @foreach ($categories as $category)
                        <option value="{{ $category->id }}" {{ request('category_id') == $category->id ? 'selected' : '' }}>{{ $category->name }}</option>
                    @endforeach
                </select>
            </div>
            <div class="col-md-2">
                <select class="form-select" name="status" style="border-color:#d4ebe2;">
                    <option value="">{{ __('All Status') }}</option>
                    <option value="active" {{ request('status') === 'active' ? 'selected' : '' }}>{{ __('Active') }}</option>
                    <option value="inactive" {{ request('status') === 'inactive' ? 'selected' : '' }}>{{ __('Inactive') }}</option>
                </select>
            </div>
            <div class="col-md-2">
                <button type="submit" class="btn btn-success w-100 fw-semibold rounded-3" style="background:#087657; border-color:#087657;"><i class="bi bi-filter me-1"></i>{{ __('Filter') }}</button>
            </div>
            <div class="col-md-3">
                <div id="bulk-actions" class="d-none">
                    <div class="btn-group btn-group-sm w-100">
                        <button type="button" class="btn btn-outline-success" onclick="submitBulk('activate')">
                            <i class="bi bi-check-circle me-1"></i>{{ __('Activate') }}
                        </button>
                        <button type="button" class="btn btn-outline-warning" onclick="submitBulk('deactivate')">
                            <i class="bi bi-pause-circle me-1"></i>{{ __('Deactivate') }}
                        </button>
                        <button type="button" class="btn btn-outline-danger" onclick="submitBulk('delete')">
                            <i class="bi bi-trash me-1"></i>{{ __('Delete') }}
                        </button>
                    </div>
                </div>
                <div id="bulk-count" class="d-none text-end mt-1">
                    <small class="text-muted"><span id="selected-count">0</span> {{ __('selected') }}</small>
                </div>
            </div>
        </div>
    </div>
</form>

<!-- Products Table -->
<div class="admin-card">
    <div class="table-responsive">
        <table class="table admin-table mb-0">
            <thead>
                <tr>
                    <th style="width:40px;">
                        <input type="checkbox" class="form-check-input" id="select-all">
                    </th>
                    <th style="width:60px;">{{ __('Image') }}</th>
                    <th>{{ __('Product Name') }}</th>
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
                            <input type="checkbox" class="form-check-input product-checkbox" value="{{ $product->id }}">
                        </td>
                        <td>
                            @php $primaryImage = $product->images->where('is_primary', true)->first() ?? $product->images->first(); @endphp
                            @if ($primaryImage)
                                <img src="{{ asset('storage/' . $primaryImage->image_path) }}" alt="{{ $product->name }}" class="rounded-3 border" style="width:42px;height:42px;object-fit:cover;">
                            @else
                                <div class="rounded-3 d-flex align-items-center justify-content-center border" style="width:42px;height:42px;background:#f4fbf8;">
                                    <i class="bi bi-bag text-success fs-5"></i>
                                </div>
                            @endif
                        </td>
                        <td class="fw-bold text-dark">
                            {{ $product->name }}
                            @if ($product->is_featured)
                                <span class="badge-soft-warning ms-1" style="font-size:0.65rem;"><i class="bi bi-star-fill me-1"></i>{{ __('Featured') }}</span>
                            @endif
                        </td>
                        <td><span class="sku-code">{{ $product->sku ?: '-' }}</span></td>
                        <td><span class="badge-soft-secondary">{{ $product->category?->name ?? '-' }}</span></td>
                        <td class="fw-bold text-dark">${{ number_format($product->price, 2) }}</td>
                        <td>
                            @if ($product->stock_quantity <= 0)
                                <span class="badge-soft-danger">{{ $product->stock_quantity }} {{ __('out of stock') }}</span>
                            @elseif ($product->stock_quantity <= 5)
                                <span class="badge-soft-warning">{{ $product->stock_quantity }} {{ __('low stock') }}</span>
                            @else
                                <span class="badge-soft-success">{{ $product->stock_quantity }} {{ __('in stock') }}</span>
                            @endif
                        </td>
                        <td>
                            @if ($product->status === 'active')
                                <span class="badge-soft-success"><i class="bi bi-check-circle me-1"></i>{{ __('Active') }}</span>
                            @else
                                <span class="badge-soft-secondary">{{ __('Inactive') }}</span>
                            @endif
                        </td>
                        <td class="text-end">
                            <div class="btn-group btn-group-sm">
                                <a href="{{ route('admin.products.show', $product) }}" class="btn btn-light border text-info rounded-2 me-1" title="{{ __('View') }}">
                                    <i class="bi bi-eye"></i>
                                </a>
                                <a href="{{ route('admin.products.edit', $product) }}" class="btn btn-light border text-primary rounded-2 me-1" title="{{ __('Edit') }}">
                                    <i class="bi bi-pencil"></i>
                                </a>
                                <form action="{{ route('admin.products.destroy', $product) }}" method="POST" class="d-inline" onsubmit="return confirm('{{ __('Are you sure you want to delete this product?') }}')">
                                    @csrf @method('DELETE')
                                    <button type="submit" class="btn btn-light border text-danger rounded-2" title="{{ __('Delete') }}">
                                        <i class="bi bi-trash"></i>
                                    </button>
                                </form>
                            </div>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="9" class="text-center py-5 text-muted">
                            <i class="bi bi-box-seam fs-2 d-block mb-2 text-muted" style="opacity:0.4;"></i>
                            {{ __('No products found matching your search.') }}
                        </td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>

    @if ($products->hasPages())
        <div class="p-3 border-top bg-white">
            {{ $products->links() }}
        </div>
    @endif
</div>

@push('scripts')
<script>
    const selectAll = document.getElementById('select-all');
    const checkboxes = document.querySelectorAll('.product-checkbox');
    const bulkActions = document.getElementById('bulk-actions');
    const bulkCount = document.getElementById('bulk-count');
    const selectedCount = document.getElementById('selected-count');

    if (selectAll) {
        selectAll.addEventListener('change', function() {
            checkboxes.forEach(cb => cb.checked = this.checked);
            updateBulkUI();
        });
    }

    checkboxes.forEach(cb => {
        cb.addEventListener('change', updateBulkUI);
    });

    function updateBulkUI() {
        const count = document.querySelectorAll('.product-checkbox:checked').length;
        if (selectedCount) selectedCount.textContent = count;
        if (count > 0) {
            bulkActions?.classList.remove('d-none');
            bulkCount?.classList.remove('d-none');
        } else {
            bulkActions?.classList.add('d-none');
            bulkCount?.classList.add('d-none');
        }
    }

    function submitBulk(action) {
        const checked = document.querySelectorAll('.product-checkbox:checked');
        if (checked.length === 0) {
            alert('{{ __("Please select at least one product.") }}');
            return;
        }

        if (action === 'delete' && !confirm('{{ __("Are you sure you want to delete the selected products?") }}')) {
            return;
        }

        const form = document.createElement('form');
        form.method = 'POST';
        form.action = '{{ route("admin.products.bulk-action") }}';

        const csrf = document.createElement('input');
        csrf.type = 'hidden';
        csrf.name = '_token';
        csrf.value = '{{ csrf_token() }}';
        form.appendChild(csrf);

        const actionInput = document.createElement('input');
        actionInput.type = 'hidden';
        actionInput.name = 'action';
        actionInput.value = action;
        form.appendChild(actionInput);

        checked.forEach(function(cb) {
            const input = document.createElement('input');
            input.type = 'hidden';
            input.name = 'ids[]';
            input.value = cb.value;
            form.appendChild(input);
        });

        document.body.appendChild(form);
        form.submit();
    }
</script>
@endpush
@endsection
