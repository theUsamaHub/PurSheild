@extends('layouts.app')

@section('content')
    <div class="mb-4">
        <div class="d-flex justify-content-between align-items-center">
            <h2 class="h4 mb-0 fw-semibold">{{ __('Edit Product Category') }}</h2>
            <a href="{{ route('admin.product-categories.index') }}" class="btn btn-outline-secondary btn-sm">
                <i class="bi bi-arrow-left me-1"></i>{{ __('Back') }}
            </a>
        </div>
    </div>

    <div class="row">
        <div class="col-lg-8">
            <div class="card">
                <div class="card-body p-4">
                    <form action="{{ route('admin.product-categories.update', $category) }}" method="POST">
                        @csrf
                        @method('PUT')

                        <div class="mb-3">
                            <x-input-label for="name" :value="__('Category Name')" />
                            <x-text-input id="name" name="name" type="text" class="form-control" :value="old('name', $category->name)" required autofocus />
                            <x-input-error :messages="$errors->get('name')" class="mt-1" />
                        </div>

                        <div class="mb-3">
                            <x-input-label for="slug" :value="__('Slug')" />
                            <x-text-input id="slug" name="slug" type="text" class="form-control" :value="old('slug', $category->slug)" disabled />
                            <small class="text-muted">{{ __('Slug cannot be changed after creation.') }}</small>
                        </div>

                        <div class="mb-3">
                            <x-input-label for="description" :value="__('Description')" />
                            <textarea id="description" name="description" class="form-control @error('description') is-invalid @enderror" rows="4">{{ old('description', $category->description) }}</textarea>
                            <x-input-error :messages="$errors->get('description')" class="mt-1" />
                        </div>

                        <div class="mb-3">
                            <div class="form-check form-switch">
                                <input class="form-check-input" type="checkbox" name="is_active" value="1" id="is_active" {{ old('is_active', $category->is_active) ? 'checked' : '' }}>
                                <label class="form-check-label" for="is_active">{{ __('Active') }}</label>
                            </div>
                        </div>

                        <div class="d-flex justify-content-end gap-2">
                            <a href="{{ route('admin.product-categories.index') }}" class="btn btn-outline-secondary">{{ __('Cancel') }}</a>
                            <x-primary-button>{{ __('Update Category') }}</x-primary-button>
                        </div>
                    </form>
                </div>
            </div>
        </div>

        <div class="col-lg-4">
            <div class="card">
                <div class="card-header">
                    <h6 class="mb-0 fw-semibold">{{ __('Category Info') }}</h6>
                </div>
                <div class="card-body">
                    <div class="mb-2">
                        <small class="text-muted">{{ __('Products Count') }}</small>
                        <div><span class="badge bg-info">{{ $category->products()->count() }}</span></div>
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

            @if ($category->products()->count() > 0)
                <div class="card border-warning mt-4">
                    <div class="card-header">
                        <h6 class="mb-0 fw-semibold text-warning">{{ __('Warning') }}</h6>
                    </div>
                    <div class="card-body">
                        <p class="text-muted mb-0" style="font-size:0.875rem;">
                            {{ __('This category has') }} {{ $category->products()->count() }} {{ __('product(s) assigned. You cannot delete it until all products are removed.') }}
                        </p>
                    </div>
                </div>
            @endif
        </div>
    </div>
@endsection
