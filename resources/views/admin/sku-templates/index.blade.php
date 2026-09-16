@extends('layouts.app')

@section('content')
    <div class="mb-4">
        <div class="d-flex justify-content-between align-items-center">
            <h2 class="h4 mb-0 fw-semibold">{{ __('SKU Templates') }}</h2>
            <a href="{{ route('admin.sku-templates.create') }}" class="btn btn-primary btn-sm">
                <i class="bi bi-plus-circle me-1"></i>{{ __('Add Template') }}
            </a>
        </div>
        <small class="text-muted">{{ __('Define patterns for auto-generating product SKUs.') }}</small>
    </div>

    <!-- Available Placeholders -->
    <div class="card mb-4 border-info">
        <div class="card-header bg-info bg-opacity-10">
            <h6 class="mb-0 fw-semibold"><i class="bi bi-info-circle me-1"></i>{{ __('Available Placeholders') }}</h6>
        </div>
        <div class="card-body">
            <div class="row g-2" style="font-size:0.85rem;">
                <div class="col-md-3"><code>{NAME}</code> — {{ __('First 3 letters of product name') }}</div>
                <div class="col-md-3"><code>{SLUG}</code> — {{ __('First 6 chars of slug') }}</div>
                <div class="col-md-3"><code>{CATEGORY}</code> — {{ __('First 3 letters of category') }}</div>
                <div class="col-md-3"><code>{CAT_SLUG}</code> — {{ __('First 3 chars of category slug') }}</div>
                <div class="col-md-3"><code>{ID}</code> — {{ __('Zero-padded product ID (4 digits)') }}</div>
                <div class="col-md-3"><code>{####}</code> — {{ __('Auto-increment counter (4 digits)') }}</div>
                <div class="col-md-3"><code>{RANDOM}</code> — {{ __('4 random characters') }}</div>
                <div class="col-md-3"><code>{YEAR}</code> / <code>{MONTH}</code> — {{ __('Current year/month') }}</div>
            </div>
        </div>
    </div>

    <!-- Templates Table -->
    <div class="card">
        <div class="card-body p-0">
            <div class="table-responsive">
                <table class="table table-hover mb-0">
                    <thead>
                        <tr>
                            <th>{{ __('Name') }}</th>
                            <th>{{ __('Pattern') }}</th>
                            <th>{{ __('Preview') }}</th>
                            <th>{{ __('Status') }}</th>
                            <th class="text-end">{{ __('Actions') }}</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse ($templates as $template)
                            <tr>
                                <td class="fw-medium">{{ $template->name }}</td>
                                <td><code class="text-primary">{{ $template->pattern }}</code></td>
                                <td>
                                    <code class="text-success">{{ str_replace(
                                        ['{NAME}', '{SLUG}', '{CATEGORY}', '{CAT_SLUG}', '{ID}', '{####}', '{RANDOM}', '{YEAR}', '{MONTH}'],
                                        ['PET', 'DOGFO', 'DOG', 'DOG', '0042', '0001', 'X7K9', now()->format('Y'), now()->format('m')],
                                        $template->pattern
                                    ) }}</code>
                                </td>
                                <td>
                                    @if ($template->is_active)
                                        <span class="badge bg-success">{{ __('Active') }}</span>
                                    @else
                                        <span class="badge bg-secondary">{{ __('Inactive') }}</span>
                                    @endif
                                </td>
                                <td class="text-end">
                                    <div class="btn-group btn-group-sm">
                                        <a href="{{ route('admin.sku-templates.edit', $template->id) }}" class="btn btn-outline-primary" title="{{ __('Edit') }}">
                                            <i class="bi bi-pencil"></i>
                                        </a>
                                        <form action="{{ route('admin.sku-templates.destroy', $template->id) }}" method="POST" class="d-inline" onsubmit="return confirm('{{ __('Are you sure you want to delete this template?') }}')">
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
                                <td colspan="5" class="text-center py-5">
                                    <div class="empty-state">
                                        <i class="bi bi-upc-scan" style="font-size:3rem;opacity:0.3;"></i>
                                        <p class="mt-2 text-muted">{{ __('No SKU templates yet.') }}</p>
                                        <a href="{{ route('admin.sku-templates.create') }}" class="btn btn-primary btn-sm mt-2">
                                            {{ __('Create your first template') }}
                                        </a>
                                    </div>
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>

        @if ($templates->hasPages())
            <div class="card-footer bg-white">
                {{ $templates->links() }}
            </div>
        @endif
    </div>
@endsection
