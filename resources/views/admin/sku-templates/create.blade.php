@extends('layouts.app')

@section('content')
    <div class="mb-4">
        <div class="d-flex justify-content-between align-items-center">
            <h2 class="h4 mb-0 fw-semibold">{{ __('Create SKU Template') }}</h2>
            <a href="{{ route('admin.sku-templates.index') }}" class="btn btn-outline-secondary btn-sm">
                <i class="bi bi-arrow-left me-1"></i>{{ __('Back') }}
            </a>
        </div>
    </div>

    <div class="row">
        <div class="col-lg-8">
            <form action="{{ route('admin.sku-templates.store') }}" method="POST">
                @csrf
                <div class="card mb-4">
                    <div class="card-header">
                        <h6 class="mb-0 fw-semibold">{{ __('Template Details') }}</h6>
                    </div>
                    <div class="card-body">
                        <div class="mb-3">
                            <x-input-label for="name" :value="__('Template Name')" />
                            <x-text-input id="name" name="name" type="text" class="form-control" :value="old('name')" placeholder="e.g. Standard Product" required autofocus />
                            <x-input-error :messages="$errors->get('name')" class="mt-1" />
                        </div>

                        <div class="mb-3">
                            <x-input-label for="pattern" :value="__('SKU Pattern')" />
                            <x-text-input id="pattern" name="pattern" type="text" class="form-control font-monospace" :value="old('pattern')" placeholder="e.g. PRD-{SLUG}-{####}" required />
                            <small class="text-muted">{{ __('Use placeholders like {NAME}, {SLUG}, {####}, {RANDOM}, etc.') }}</small>
                            <x-input-error :messages="$errors->get('pattern')" class="mt-1" />
                        </div>

                        <div class="row">
                            <div class="col-md-6 mb-3">
                                <div class="form-check form-switch">
                                    <input class="form-check-input" type="checkbox" name="is_active" value="1" id="is_active" {{ old('is_active', '1') ? 'checked' : '' }}>
                                    <label class="form-check-label" for="is_active">{{ __('Active') }}</label>
                                </div>
                            </div>
                            <div class="col-md-6 mb-3">
                                <x-input-label for="sort_order" :value="__('Sort Order')" />
                                <x-text-input id="sort_order" name="sort_order" type="number" class="form-control" :value="old('sort_order', 0)" min="0" />
                            </div>
                        </div>
                    </div>
                </div>

                <div class="d-flex justify-content-end gap-2">
                    <a href="{{ route('admin.sku-templates.index') }}" class="btn btn-outline-secondary">{{ __('Cancel') }}</a>
                    <x-primary-button>{{ __('Create Template') }}</x-primary-button>
                </div>
            </form>
        </div>

        <div class="col-lg-4">
            <div class="card">
                <div class="card-header">
                    <h6 class="mb-0 fw-semibold">{{ __('Pattern Examples') }}</h6>
                </div>
                <div class="card-body">
                    <ul class="mb-0" style="font-size:0.85rem;">
                        <li class="mb-2"><code>PRD-{SLUG}-{####}</code><br><small class="text-muted">→ PRD-DOGFO-0001</small></li>
                        <li class="mb-2"><code>ELEC-{CATEGORY}-{RANDOM}</code><br><small class="text-muted">→ ELEC-DOG-X7K9</small></li>
                        <li class="mb-2"><code>{NAME}-{ID}</code><br><small class="text-muted">→ PET-0042</small></li>
                        <li class="mb-2"><code>PET-{CAT_SLUG}-{YEAR}{MONTH}</code><br><small class="text-muted">→ PET-DOG-202609</small></li>
                        <li class="mb-0"><code>FS-{####}</code><br><small class="text-muted">→ FS-0001</small></li>
                    </ul>
                </div>
            </div>

            <div class="card mt-4">
                <div class="card-header">
                    <h6 class="mb-0 fw-semibold">{{ __('Live Preview') }}</h6>
                </div>
                <div class="card-body text-center">
                    <div id="live-preview" class="p-3 rounded" style="background:var(--bs-tertiary-bg);min-height:60px;">
                        <code class="fs-5 text-primary">Enter a pattern above</code>
                    </div>
                </div>
            </div>
        </div>
    </div>

    @push('scripts')
    <script>
        const patternInput = document.getElementById('pattern');
        const preview = document.getElementById('live-preview');

        function updatePreview() {
            let sku = patternInput.value || 'Enter a pattern above';
            sku = sku.replace('{NAME}', 'PET').replace('{SLUG}', 'DOGFO').replace('{CATEGORY}', 'DOG').replace('{CAT_SLUG}', 'DOG').replace('{ID}', '0042').replace('{####}', '0001').replace('{RANDOM}', 'X7K9').replace('{YEAR}', '{{ now()->format("Y") }}').replace('{MONTH}', '{{ now()->format("m") }}');
            preview.innerHTML = '<code class="fs-5 text-primary">' + sku + '</code>';
        }

        patternInput.addEventListener('input', updatePreview);
        updatePreview();
    </script>
    @endpush
@endsection
