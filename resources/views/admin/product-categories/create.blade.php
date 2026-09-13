@extends('layouts.app')

@section('content')
    <div class="mb-4">
        <div class="d-flex justify-content-between align-items-center">
            <h2 class="h4 mb-0 fw-semibold">{{ __('Create Product Category') }}</h2>
            <a href="{{ route('admin.product-categories.index') }}" class="btn btn-outline-secondary btn-sm">
                <i class="bi bi-arrow-left me-1"></i>{{ __('Back') }}
            </a>
        </div>
    </div>

    <div class="row">
        <div class="col-lg-8">
            <div class="card">
                <div class="card-body p-4">
                    <form action="{{ route('admin.product-categories.store') }}" method="POST">
                        @csrf

                        <div class="mb-3">
                            <x-input-label for="name" :value="__('Category Name')" />
                            <x-text-input id="name" name="name" type="text" class="form-control" :value="old('name')" required autofocus />
                            <x-input-error :messages="$errors->get('name')" class="mt-1" />
                        </div>

                        <div class="mb-3">
                            <x-input-label for="slug" :value="__('Slug (optional)')" />
                            <x-text-input id="slug" name="slug" type="text" class="form-control" :value="old('slug')" />
                            <small class="text-muted">{{ __('Leave blank to auto-generate from name.') }}</small>
                            <x-input-error :messages="$errors->get('slug')" class="mt-1" />
                        </div>

                        <div class="mb-3">
                            <x-input-label for="description" :value="__('Description')" />
                            <textarea id="description" name="description" class="form-control @error('description') is-invalid @enderror" rows="4">{{ old('description') }}</textarea>
                            <x-input-error :messages="$errors->get('description')" class="mt-1" />
                        </div>

                        <div class="mb-3">
                            <div class="form-check form-switch">
                                <input class="form-check-input" type="checkbox" name="is_active" value="1" id="is_active" {{ old('is_active', '1') ? 'checked' : '' }}>
                                <label class="form-check-label" for="is_active">{{ __('Active') }}</label>
                            </div>
                        </div>

                        <div class="d-flex justify-content-end gap-2">
                            <a href="{{ route('admin.product-categories.index') }}" class="btn btn-outline-secondary">{{ __('Cancel') }}</a>
                            <x-primary-button>{{ __('Create Category') }}</x-primary-button>
                        </div>
                    </form>
                </div>
            </div>
        </div>

        <div class="col-lg-4">
            <div class="card">
                <div class="card-header">
                    <h6 class="mb-0 fw-semibold">{{ __('Tips') }}</h6>
                </div>
                <div class="card-body">
                    <ul class="mb-0" style="font-size:0.875rem;">
                        <li class="mb-2">{{ __('Keep category names short and descriptive.') }}</li>
                        <li class="mb-2">{{ __('Slugs are used in URLs and should be lowercase.') }}</li>
                        <li class="mb-0">{{ __('Inactive categories will not appear on the storefront.') }}</li>
                    </ul>
                </div>
            </div>
        </div>
    </div>
@endsection
