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
            <form action="{{ route('admin.product-categories.store') }}" method="POST" enctype="multipart/form-data">
                @csrf
                <div class="card mb-4">
                    <div class="card-header">
                        <h6 class="mb-0 fw-semibold">{{ __('Category Information') }}</h6>
                    </div>
                    <div class="card-body">
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

                        <div class="row">
                            <div class="col-md-6 mb-3">
                                <x-input-label for="sort_order" :value="__('Sort Order')" />
                                <x-text-input id="sort_order" name="sort_order" type="number" class="form-control" :value="old('sort_order', 0)" min="0" />
                                <small class="text-muted">{{ __('Lower numbers appear first.') }}</small>
                                <x-input-error :messages="$errors->get('sort_order')" class="mt-1" />
                            </div>
                            <div class="col-md-6 mb-3">
                                <div class="mt-4">
                                    <div class="form-check form-switch">
                                        <input class="form-check-input" type="checkbox" name="is_active" value="1" id="is_active" {{ old('is_active', '1') ? 'checked' : '' }}>
                                        <label class="form-check-label" for="is_active">{{ __('Active') }}</label>
                                    </div>
                                    <small class="text-muted">{{ __('Inactive categories will not appear on the storefront.') }}</small>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="d-flex justify-content-end gap-2">
                    <a href="{{ route('admin.product-categories.index') }}" class="btn btn-outline-secondary">{{ __('Cancel') }}</a>
                    <x-primary-button>{{ __('Create Category') }}</x-primary-button>
                </div>
            </form>
        </div>

        <div class="col-lg-4">
            <div class="card mb-4">
                <div class="card-header">
                    <h6 class="mb-0 fw-semibold">{{ __('Category Image') }}</h6>
                </div>
                <div class="card-body">
                    <div class="mb-3">
                        <input type="file" class="form-control @error('image') is-invalid @enderror" name="image" id="category-image" accept=".jpg,.jpeg,.png,.gif,.webp">
                        <small class="text-muted">{{ __('Max 2MB. JPG, PNG, GIF, WebP.') }}</small>
                        <x-input-error :messages="$errors->get('image')" class="mt-1" />
                    </div>
                    <div id="image-preview" class="text-center"></div>
                </div>
            </div>

            <div class="card">
                <div class="card-header">
                    <h6 class="mb-0 fw-semibold">{{ __('Tips') }}</h6>
                </div>
                <div class="card-body">
                    <ul class="mb-0" style="font-size:0.875rem;">
                        <li class="mb-2">{{ __('Keep category names short and descriptive.') }}</li>
                        <li class="mb-2">{{ __('Slugs are used in URLs and should be lowercase.') }}</li>
                        <li class="mb-2">{{ __('Sort order controls display position on the storefront.') }}</li>
                        <li class="mb-0">{{ __('Upload a thumbnail image for visual identification.') }}</li>
                    </ul>
                </div>
            </div>
        </div>
    </div>

    @push('scripts')
    <script>
        document.getElementById('category-image').addEventListener('change', function(e) {
            const container = document.getElementById('image-preview');
            container.innerHTML = '';
            const file = e.target.files[0];
            if (file && file.type.startsWith('image/')) {
                const reader = new FileReader();
                reader.onload = function(ev) {
                    container.innerHTML = '<img src="' + ev.target.result + '" class="img-thumbnail" style="max-width:200px;">';
                };
                reader.readAsDataURL(file);
            }
        });
    </script>
    @endpush
@endsection
