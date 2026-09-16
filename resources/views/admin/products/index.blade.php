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
        <div class="col-md-3">
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
        <div class="col-md-3">
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
        <div class="col-md-3">
            <div class="card border-start border-secondary border-4">
                <div class="card-body">
                    <div class="d-flex justify-content-between align-items-center">
                        <div>
                            <div class="text-muted mb-1" style="font-size:0.8rem;">{{ __('Inactive') }}</div>
                            <div class="fw-bold fs-4">{{ $stats['inactive'] }}</div>
                        </div>
                        <i class="bi bi-pause-circle text-secondary" style="font-size:2rem;opacity:0.3;"></i>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-md-3">
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
    <form method="GET" action="{{ route('admin.products.index') }}">
        <div class="card mb-4">
            <div class="card-body">
                <div class="row g-3 align-items-end">
                    <div class="col-md-3">
                        <input type="text" class="form-control" name="search" placeholder="{{ __('Search by name, SKU...') }}" value="{{ request('search') }}">
                    </div>
                    <div class="col-md-2">
                        <select class="form-select" name="category_id">
                            <option value="">{{ __('All Categories') }}</option>
                            @foreach ($categories as $category)
                                <option value="{{ $category->id }}" {{ request('category_id') == $category->id ? 'selected' : '' }}>{{ $category->name }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="col-md-2">
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
                        <div id="bulk-count" class="d-none">
                            <small class="text-muted"><span id="selected-count">0</span> {{ __('selected') }}</small>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </form>

    <!-- Products Table -->
    <div class="card">
        <div class="card-body p-0">
            <div class="table-responsive">
                <table class="table table-hover mb-0">
                    <thead>
                        <tr>
                            <th style="width:40px;">
                                <input type="checkbox" class="form-check-input" id="select-all">
                            </th>
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
                                    <input type="checkbox" class="form-check-input product-checkbox" value="{{ $product->id }}">
                                </td>
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
                                <td colspan="9" class="text-center py-5">
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

    @push('scripts')
    <script>
        const selectAll = document.getElementById('select-all');
        const checkboxes = document.querySelectorAll('.product-checkbox');
        const bulkActions = document.getElementById('bulk-actions');
        const bulkCount = document.getElementById('bulk-count');
        const selectedCount = document.getElementById('selected-count');

        selectAll.addEventListener('change', function() {
            checkboxes.forEach(cb => cb.checked = this.checked);
            updateBulkUI();
        });

        checkboxes.forEach(cb => {
            cb.addEventListener('change', updateBulkUI);
        });

        function updateBulkUI() {
            const count = document.querySelectorAll('.product-checkbox:checked').length;
            selectedCount.textContent = count;
            if (count > 0) {
                bulkActions.classList.remove('d-none');
                bulkCount.classList.remove('d-none');
            } else {
                bulkActions.classList.add('d-none');
                bulkCount.classList.add('d-none');
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
