@extends('layouts.app')

@section('content')
    <div class="mb-4">
        <div class="d-flex justify-content-between align-items-center">
            <h2 class="h4 mb-0 fw-semibold">{{ __('Edit Product') }}</h2>
            <a href="{{ route('admin.products.index') }}" class="btn btn-outline-secondary btn-sm">
                <i class="bi bi-arrow-left me-1"></i>{{ __('Back') }}
            </a>
        </div>
    </div>

    <form action="{{ route('admin.products.update', $product) }}" method="POST" enctype="multipart/form-data">
        @csrf
        @method('PUT')
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
                            <x-text-input id="name" name="name" type="text" class="form-control" :value="old('name', $product->name)" required autofocus />
                            <x-input-error :messages="$errors->get('name')" class="mt-1" />
                        </div>

                        <div class="mb-3">
                            <x-input-label for="slug" :value="__('Slug')" />
                            <x-text-input id="slug" name="slug" type="text" class="form-control" :value="old('slug', $product->slug)" />
                            <x-input-error :messages="$errors->get('slug')" class="mt-1" />
                        </div>

                        <div class="mb-3">
                            <x-input-label for="description" :value="__('Description')" />
                            <x-tinymce name="description" :height="350">{{ old('description', $product->description) }}</x-tinymce>
                            <x-input-error :messages="$errors->get('description')" class="mt-1" />
                        </div>

                        <div class="row">
                            <div class="col-md-6 mb-3">
                                <x-input-label for="sku" :value="__('SKU')" />
                                <x-text-input id="sku" name="sku" type="text" class="form-control" :value="old('sku', $product->sku)" readonly disabled />
                                <small class="text-muted">{{ __('Auto-generated. Cannot be changed.') }}</small>
                            </div>
                            <div class="col-md-6 mb-3">
                                <x-input-label for="price" :value="__('Price') . ' (*)'" />
                                <x-text-input id="price" name="price" type="number" class="form-control" :value="old('price', $product->price)" step="0.01" min="0" required />
                                <x-input-error :messages="$errors->get('price')" class="mt-1" />
                            </div>
                        </div>

                        <div class="row">
                            <div class="col-md-4 mb-3">
                                <x-input-label for="special_price" :value="__('Special Price')" />
                                <x-text-input id="special_price" name="special_price" type="number" class="form-control" :value="old('special_price', $product->special_price)" step="0.01" min="0" />
                                <small class="text-muted">{{ __('Discounted price. Must be less than original price.') }}</small>
                                <x-input-error :messages="$errors->get('special_price')" class="mt-1" />
                            </div>
                            <div class="col-md-4 mb-3">
                                <x-input-label for="discount_percent" :value="__('Discount %')" />
                                <div class="input-group">
                                    <x-text-input id="discount_percent" name="discount_percent" type="number" class="form-control" :value="old('discount_percent', $product->discount_percent)" step="0.01" min="0" max="100" />
                                    <span class="input-group-text">%</span>
                                </div>
                                <small class="text-muted">{{ __('Auto-calculates special price.') }}</small>
                                <x-input-error :messages="$errors->get('discount_percent')" class="mt-1" />
                            </div>
                            <div class="col-md-4 mb-3">
                                <x-input-label for="stock_quantity" :value="__('Stock Quantity') . ' (*)'" />
                                <x-text-input id="stock_quantity" name="stock_quantity" type="number" class="form-control" :value="old('stock_quantity', $product->stock_quantity)" min="0" required />
                                <x-input-error :messages="$errors->get('stock_quantity')" class="mt-1" />
                            </div>
                        </div>

                        <div class="row">
                            <div class="col-md-6 mb-3">
                                <x-input-label for="weight" :value="__('Weight (kg)')" />
                                <x-text-input id="weight" name="weight" type="number" class="form-control" :value="old('weight', $product->weight)" step="0.01" min="0" />
                                <x-input-error :messages="$errors->get('weight')" class="mt-1" />
                            </div>
                        </div>

                        <div class="mb-3">
                            <x-input-label for="category_id" :value="__('Category') . ' (*)'" />
                            <select id="category_id" name="category_id" class="form-select @error('category_id') is-invalid @enderror" required>
                                <option value="">{{ __('Select a category') }}</option>
                                @foreach ($categories as $category)
                                    <option value="{{ $category->id }}" {{ old('category_id', $product->category_id) == $category->id ? 'selected' : '' }}>{{ $category->name }}</option>
                                @endforeach
                            </select>
                            <x-input-error :messages="$errors->get('category_id')" class="mt-1" />
                        </div>

                        <div class="mb-3">
                            <x-input-label for="sku_template_id" :value="__('SKU Template')" />
                            <select id="sku_template_id" name="sku_template_id" class="form-select @error('sku_template_id') is-invalid @enderror">
                                <option value="">{{ __('Auto-generate SKU (default)') }}</option>
                                @foreach ($skuTemplates as $template)
                                    <option value="{{ $template->id }}" {{ old('sku_template_id', $product->sku_template_id) == $template->id ? 'selected' : '' }}>
                                        {{ $template->name }} — <code>{{ $template->pattern }}</code>
                                    </option>
                                @endforeach
                            </select>
                            <small class="text-muted">{{ __('Choose a pattern to auto-generate SKU, or leave blank for default.') }}</small>
                            <x-input-error :messages="$errors->get('sku_template_id')" class="mt-1" />
                        </div>

                        <div class="mb-3">
                            <x-input-label for="status" :value="__('Status') . ' (*)'" />
                            <select id="status" name="status" class="form-select @error('status') is-invalid @enderror" required>
                                <option value="active" {{ old('status', $product->status) === 'active' ? 'selected' : '' }}>{{ __('Active') }}</option>
                                <option value="inactive" {{ old('status', $product->status) === 'inactive' ? 'selected' : '' }}>{{ __('Inactive') }}</option>
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
                                <input class="form-check-input" type="checkbox" name="is_featured" value="1" id="is_featured" {{ old('is_featured', $product->is_featured) ? 'checked' : '' }}>
                                <label class="form-check-label" for="is_featured">{{ __('Featured Product') }}</label>
                            </div>
                            <small class="text-muted">{{ __('Featured products are highlighted on the storefront.') }}</small>
                        </div>

                        <div class="mb-3">
                            <x-input-label for="meta_title" :value="__('Meta Title')" />
                            <x-text-input id="meta_title" name="meta_title" type="text" class="form-control" :value="old('meta_title', $product->meta_title)" />
                            <small class="text-muted">{{ __('For SEO. Leave blank to use product name.') }}</small>
                            <x-input-error :messages="$errors->get('meta_title')" class="mt-1" />
                        </div>

                        <div class="mb-3">
                            <x-input-label for="meta_description" :value="__('Meta Description')" />
                            <textarea id="meta_description" name="meta_description" class="form-control" rows="3">{{ old('meta_description', $product->meta_description) }}</textarea>
                            <small class="text-muted">{{ __('For SEO. Leave blank for auto-generated description.') }}</small>
                            <x-input-error :messages="$errors->get('meta_description')" class="mt-1" />
                        </div>
                    </div>
                </div>

                <!-- Existing Images -->
                <div class="card mb-4">
                    <div class="card-header">
                        <h6 class="mb-0 fw-semibold">{{ __('Current Images') }} ({{ $product->images->count() }})</h6>
                    </div>
                    <div class="card-body">
                        @if ($product->images->count() > 0)
                            <div class="d-flex flex-wrap gap-2 mb-3">
                                @foreach ($product->images->sortBy('sort_order') as $image)
                                    <div class="position-relative" style="width:90px;height:90px;">
                                        <img src="{{ asset('storage/' . $image->image_path) }}" alt="" class="img-thumbnail w-100 h-100" style="object-fit:cover;">
                                        @if ($image->is_primary)
                                            <span class="position-absolute top-0 start-0 badge bg-warning" style="font-size:0.6rem;"><i class="bi bi-star-fill"></i></span>
                                        @endif
                                        <div class="position-absolute bottom-0 start-0 end-0 bg-dark bg-opacity-75 d-flex justify-content-center gap-1 p-1" style="font-size:0.6rem;">
                                            <label class="text-white mb-0" title="{{ __('Remove') }}">
                                                <input type="checkbox" name="remove_images[]" value="{{ $image->id }}" class="d-none" onchange="this.closest('.position-relative').style.opacity = this.checked ? 0.3 : 1;">
                                                <i class="bi bi-trash" style="cursor:pointer;"></i>
                                            </label>
                                            @if (!$image->is_primary)
                                                <label class="text-white mb-0" title="{{ __('Set as primary') }}">
                                                    <input type="radio" name="primary_image_id" value="{{ $image->id }}" class="d-none">
                                                    <i class="bi bi-star" style="cursor:pointer;" onclick="setPrimary(this, {{ $image->id }})"></i>
                                                </label>
                                            @endif
                                        </div>
                                    </div>
                                @endforeach
                            </div>
                        @else
                            <p class="text-muted" style="font-size:0.875rem;">{{ __('No images uploaded yet.') }}</p>
                        @endif
                    </div>
                </div>

                <!-- New Images -->
                <div class="card mb-4">
                    <div class="card-header">
                        <h6 class="mb-0 fw-semibold">{{ __('Upload New Images') }}</h6>
                    </div>
                    <div class="card-body">
                        <div class="mb-3">
                            <input type="file" class="form-control @error('images') is-invalid @enderror" name="images[]" id="images" multiple accept=".jpg,.jpeg,.png,.gif,.webp">
                            <small class="text-muted">{{ __('Max 6 images total, 5MB each.') }}</small>
                            <x-input-error :messages="$errors->get('images')" class="mt-1" />
                        </div>
                        <div id="image-previews" class="d-flex flex-wrap gap-2"></div>
                    </div>
                </div>

                <div class="d-flex justify-content-end gap-2">
                    <a href="{{ route('admin.products.index') }}" class="btn btn-outline-secondary">{{ __('Cancel') }}</a>
                    <x-primary-button>{{ __('Update Product') }}</x-primary-button>
                </div>
            </div>
        </div>
    </form>

    @push('scripts')
    <script>
        const priceInput = document.getElementById('price');
        const specialPriceInput = document.getElementById('special_price');
        const discountInput = document.getElementById('discount_percent');

        priceInput.addEventListener('input', function() {
            if (discountInput.value && !specialPriceInput.value) {
                const price = parseFloat(priceInput.value) || 0;
                const disc = parseFloat(discountInput.value) || 0;
                specialPriceInput.value = (price - (price * disc / 100)).toFixed(2);
            }
        });

        specialPriceInput.addEventListener('input', function() {
            const price = parseFloat(priceInput.value) || 0;
            const special = parseFloat(specialPriceInput.value) || 0;
            if (price > 0 && special > 0 && special < price) {
                discountInput.value = ((1 - special / price) * 100).toFixed(2);
            } else {
                discountInput.value = '';
            }
        });

        discountInput.addEventListener('input', function() {
            const price = parseFloat(priceInput.value) || 0;
            const disc = parseFloat(discountInput.value) || 0;
            if (price > 0 && disc > 0) {
                specialPriceInput.value = (price - (price * disc / 100)).toFixed(2);
            } else {
                specialPriceInput.value = '';
            }
        });

        function setPrimary(el, imageId) {
            document.querySelectorAll('input[name="primary_image_id"]').forEach(function(radio) {
                radio.checked = false;
            });
            el.previousElementSibling.checked = true;

            document.querySelectorAll('.position-relative .badge.bg-warning').forEach(function(badge) {
                badge.remove();
            });
            el.closest('.position-relative').insertAdjacentHTML('afterbegin', '<span class="position-absolute top-0 start-0 badge bg-warning" style="font-size:0.6rem;"><i class="bi bi-star-fill"></i></span>');
        }

        document.getElementById('images').addEventListener('change', function(e) {
            const container = document.getElementById('image-previews');
            container.innerHTML = '';

            var files = Array.from(e.target.files).slice(0, 6);
            files.forEach(function(file) {
                if (file.type.startsWith('image/')) {
                    var reader = new FileReader();
                    reader.onload = function(ev) {
                        var div = document.createElement('div');
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
