@extends('layouts.app')

@section('content')
    <div class="mb-4">
        <div class="d-flex justify-content-between align-items-center">
            <h2 class="h4 mb-0 fw-semibold">{{ __('Create Product') }}</h2>
            <a href="{{ route('admin.products.index') }}" class="btn btn-outline-secondary btn-sm">
                <i class="bi bi-arrow-left me-1"></i>{{ __('Back') }}
            </a>
        </div>
    </div>

    <form action="{{ route('admin.products.store') }}" method="POST" enctype="multipart/form-data">
        @csrf
        <div class="row">
            <!-- Left Column -->
            <div class="col-lg-8">
                <div class="card mb-4">
                    <div class="card-header">
                        <h6 class="mb-0 fw-semibold">{{ __('Product Information') }}</h6>
                    </div>
                    <div class="card-body">
                        <div class="mb-3">
                            <x-input-label for="name" :value="__('Product Name')" />
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
                            <textarea id="description" name="description" class="form-control @error('description') is-invalid @enderror" rows="5">{{ old('description') }}</textarea>
                            <x-input-error :messages="$errors->get('description')" class="mt-1" />
                        </div>

                        <div class="row">
                            <div class="col-md-6 mb-3">
                                <x-input-label for="sku" :value="__('SKU')" />
                                <x-text-input id="sku" name="sku" type="text" class="form-control" :value="old('sku')" />
                                <x-input-error :messages="$errors->get('sku')" class="mt-1" />
                            </div>
                            <div class="col-md-6 mb-3">
                                <x-input-label for="price" :value="__('Price') . ' (*)'" />
                                <x-text-input id="price" name="price" type="number" class="form-control" :value="old('price')" step="0.01" min="0" required />
                                <x-input-error :messages="$errors->get('price')" class="mt-1" />
                            </div>
                        </div>

                        <div class="row">
                            <div class="col-md-6 mb-3">
                                <x-input-label for="stock_quantity" :value="__('Stock Quantity') . ' (*)'" />
                                <x-text-input id="stock_quantity" name="stock_quantity" type="number" class="form-control" :value="old('stock_quantity', 0)" min="0" required />
                                <x-input-error :messages="$errors->get('stock_quantity')" class="mt-1" />
                            </div>
                            <div class="col-md-6 mb-3">
                                <x-input-label for="weight" :value="__('Weight (kg)')" />
                                <x-text-input id="weight" name="weight" type="number" class="form-control" :value="old('weight')" step="0.01" min="0" />
                                <x-input-error :messages="$errors->get('weight')" class="mt-1" />
                            </div>
                        </div>

                        <div class="mb-3">
                            <x-input-label for="category_id" :value="__('Category') . ' (*)'" />
                            <select id="category_id" name="category_id" class="form-select @error('category_id') is-invalid @enderror" required>
                                <option value="">{{ __('Select a category') }}</option>
                                @foreach ($categories as $category)
                                    <option value="{{ $category->id }}" {{ old('category_id') == $category->id ? 'selected' : '' }}>{{ $category->name }}</option>
                                @endforeach
                            </select>
                            <x-input-error :messages="$errors->get('category_id')" class="mt-1" />
                        </div>

                        <div class="mb-3">
                            <x-input-label for="status" :value="__('Status') . ' (*)'" />
                            <select id="status" name="status" class="form-select @error('status') is-invalid @enderror" required>
                                <option value="active" {{ old('status', 'active') === 'active' ? 'selected' : '' }}>{{ __('Active') }}</option>
                                <option value="inactive" {{ old('status') === 'inactive' ? 'selected' : '' }}>{{ __('Inactive') }}</option>
                            </select>
                            <x-input-error :messages="$errors->get('status')" class="mt-1" />
                        </div>
                    </div>
                </div>
            </div>

            <!-- Right Column -->
            <div class="col-lg-4">
                <div class="card mb-4">
                    <div class="card-header">
                        <h6 class="mb-0 fw-semibold">{{ __('Options') }}</h6>
                    </div>
                    <div class="card-body">
                        <div class="mb-3">
                            <div class="form-check form-switch">
                                <input class="form-check-input" type="checkbox" name="is_featured" value="1" id="is_featured" {{ old('is_featured') ? 'checked' : '' }}>
                                <label class="form-check-label" for="is_featured">{{ __('Featured Product') }}</label>
                            </div>
                            <small class="text-muted">{{ __('Featured products are highlighted on the storefront.') }}</small>
                        </div>

                        <div class="mb-3">
                            <x-input-label for="meta_title" :value="__('Meta Title')" />
                            <x-text-input id="meta_title" name="meta_title" type="text" class="form-control" :value="old('meta_title')" />
                            <small class="text-muted">{{ __('For SEO. Leave blank to use product name.') }}</small>
                            <x-input-error :messages="$errors->get('meta_title')" class="mt-1" />
                        </div>

                        <div class="mb-3">
                            <x-input-label for="meta_description" :value="__('Meta Description')" />
                            <textarea id="meta_description" name="meta_description" class="form-control" rows="3">{{ old('meta_description') }}</textarea>
                            <small class="text-muted">{{ __('For SEO. Leave blank for auto-generated description.') }}</small>
                            <x-input-error :messages="$errors->get('meta_description')" class="mt-1" />
                        </div>
                    </div>
                </div>

                <div class="card mb-4">
                    <div class="card-header">
                        <h6 class="mb-0 fw-semibold">{{ __('Product Images') }}</h6>
                    </div>
                    <div class="card-body">
                        <div class="mb-3">
                            <input type="file" class="form-control @error('images') is-invalid @enderror" name="images[]" id="images" multiple accept=".jpg,.jpeg,.png,.gif,.webp">
                            <small class="text-muted">{{ __('Max 6 images, 5MB each. JPG, PNG, GIF, WebP.') }}</small>
                            <x-input-error :messages="$errors->get('images')" class="mt-1" />
                        </div>
                        <div id="image-previews" class="d-flex flex-wrap gap-2"></div>
                    </div>
                </div>

                <div class="d-flex justify-content-end gap-2">
                    <a href="{{ route('admin.products.index') }}" class="btn btn-outline-secondary">{{ __('Cancel') }}</a>
                    <x-primary-button>{{ __('Create Product') }}</x-primary-button>
                </div>
            </div>
        </div>
    </form>

    @push('scripts')
    <script>
        document.getElementById('images').addEventListener('change', function(e) {
            const container = document.getElementById('image-previews');
            container.innerHTML = '';

            const files = Array.from(e.target.files).slice(0, 6);
            files.forEach(function(file) {
                if (file.type.startsWith('image/')) {
                    const reader = new FileReader();
                    reader.onload = function(ev) {
                        const div = document.createElement('div');
                        div.className = 'position-relative';
                        div.innerHTML = '<img src="' + ev.target.result + '" class="img-thumbnail" style="width:80px;height:80px;object-fit:cover;">';
                        container.appendChild(div);
                    };
                    reader.readAsDataURL(file);
                }
            });
        });
    </script>
    @endpush
@endsection
