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
            <form action="{{ route('admin.product-categories.update', $category->id) }}" method="POST" enctype="multipart/form-data">
                @csrf
                @method('PUT')
                <div class="card mb-4">
                    <div class="card-header">
                        <h6 class="mb-0 fw-semibold">{{ __('Category Information') }}</h6>
                    </div>
                    <div class="card-body">
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

                        <div class="row">
                            <div class="col-md-6 mb-3">
                                <x-input-label for="sort_order" :value="__('Sort Order')" />
                                <x-text-input id="sort_order" name="sort_order" type="number" class="form-control" :value="old('sort_order', $category->sort_order)" min="0" />
                                <small class="text-muted">{{ __('Lower numbers appear first.') }}</small>
                                <x-input-error :messages="$errors->get('sort_order')" class="mt-1" />
                            </div>
                            <div class="col-md-6 mb-3">
                                <div class="mt-4">
                                    <div class="form-check form-switch">
                                        <input class="form-check-input" type="checkbox" name="is_active" value="1" id="is_active" {{ old('is_active', $category->is_active) ? 'checked' : '' }}>
                                        <label class="form-check-label" for="is_active">{{ __('Active') }}</label>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="d-flex justify-content-end gap-2">
                    <a href="{{ route('admin.product-categories.index') }}" class="btn btn-outline-secondary">{{ __('Cancel') }}</a>
                    <x-primary-button>{{ __('Update Category') }}</x-primary-button>
                </div>
            </form>
        </div>

        <div class="col-lg-4">
            <div class="card mb-4">
                <div class="card-header">
                    <h6 class="mb-0 fw-semibold">{{ __('Category Image') }}</h6>
                </div>
                <div class="card-body">
                    @if ($category->image)
                        <div class="mb-3 text-center" id="current-image-container">
                            <img src="{{ asset('storage/' . $category->image) }}" alt="{{ $category->name }}" class="img-thumbnail" style="max-width:200px;" id="current-image">
                            <div class="mt-2">
                                <div class="form-check form-check-inline">
                                    <input class="form-check-input" type="checkbox" name="remove_image" value="1" id="remove_image" onchange="toggleImageRemove()">
                                    <label class="form-check-label" for="remove_image">{{ __('Remove image') }}</label>
                                </div>
                            </div>
                        </div>
                    @endif
                    <div class="mb-3">
                        <input type="file" class="form-control @error('image') is-invalid @enderror" name="image" id="category-image" accept=".jpg,.jpeg,.png,.gif,.webp">
                        <small class="text-muted">{{ __('Upload new image (max 2MB).') }}</small>
                        <x-input-error :messages="$errors->get('image')" class="mt-1" />
                    </div>
                    <div id="image-preview" class="text-center"></div>
                </div>
            </div>

            <div class="card mb-4">
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
        </div>
    </div>

    @push('scripts')
    <script>
        function toggleImageRemove() {
            const img = document.getElementById('current-image');
            const checkbox = document.getElementById('remove_image');
            if (img && checkbox) {
                img.style.opacity = checkbox.checked ? '0.3' : '1';
            }
        }

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
