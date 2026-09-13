@extends('layouts.owner.app')

@section('content')
    <div class="mb-4">
        <div class="d-flex justify-content-between align-items-center">
            <h2 class="h4 mb-0 fw-semibold">{{ __('Add New Pet') }}</h2>
            <a href="{{ route('owner.pets.index') }}" class="btn btn-outline-secondary btn-sm">
                <i class="bi bi-arrow-left me-1"></i>{{ __('Back') }}
            </a>
        </div>
    </div>

    <form action="{{ route('owner.pets.store') }}" method="POST" enctype="multipart/form-data">
        @csrf
    <div class="row">
        <div class="col-lg-8">
            <div class="card">
                <div class="card-body p-4">
                        <div class="row mb-3">
                            <div class="col-md-6">
                                <x-input-label for="name" :value="__('Pet Name')" />
                                <x-text-input id="name" name="name" type="text" class="form-control" :value="old('name')" required autofocus />
                                <x-input-error :messages="$errors->get('name')" class="mt-1" />
                            </div>
                            <div class="col-md-6">
                                <x-input-label for="species_id" :value="__('Species')" />
                                <select class="form-select @error('species_id') is-invalid @enderror" id="species_id" name="species_id" required>
                                    <option value="">{{ __('Select Species') }}</option>
                                    @foreach($species as $specie)
                                        <option value="{{ $specie->id }}" {{ old('species_id') == $specie->id ? 'selected' : '' }}>{{ $specie->name }}</option>
                                    @endforeach
                                </select>
                                @error('species_id')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>

                        <div class="row mb-3">
                            <div class="col-md-6">
                                <x-input-label for="breed_id" :value="__('Breed')" />
                                <select class="form-select @error('breed_id') is-invalid @enderror" id="breed_id" name="breed_id">
                                    <option value="">{{ __('Select breed (optional)') }}</option>
                                </select>
                                @error('breed_id')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                            <div class="col-md-6">
                                <x-input-label for="gender" :value="__('Gender')" />
                                <div class="d-flex gap-4 mt-2">
                                    <div class="form-check">
                                        <input class="form-check-input" type="radio" name="gender" id="gender_male" value="male" {{ old('gender') === 'male' ? 'checked' : '' }}>
                                        <label class="form-check-label" for="gender_male">
                                            <i class="bi bi-gender-male me-1"></i>{{ __('Male') }}
                                        </label>
                                    </div>
                                    <div class="form-check">
                                        <input class="form-check-input" type="radio" name="gender" id="gender_female" value="female" {{ old('gender') === 'female' ? 'checked' : '' }}>
                                        <label class="form-check-label" for="gender_female">
                                            <i class="bi bi-gender-female me-1"></i>{{ __('Female') }}
                                        </label>
                                    </div>
                                </div>
                                <x-input-error :messages="$errors->get('gender')" class="mt-1" />
                            </div>
                        </div>

                        <div class="row mb-3">
                            <div class="col-md-6">
                                <x-input-label for="date_of_birth" :value="__('Date of Birth')" />
                                <x-text-input id="date_of_birth" name="date_of_birth" type="date" class="form-control" :value="old('date_of_birth')" />
                                <x-input-error :messages="$errors->get('date_of_birth')" class="mt-1" />
                            </div>
                            <div class="col-md-6">
                                <x-input-label for="weight" :value="__('Weight (kg)')" />
                                <x-text-input id="weight" name="weight" type="number" step="0.1" min="0" class="form-control" :value="old('weight')" />
                                <x-input-error :messages="$errors->get('weight')" class="mt-1" />
                            </div>
                        </div>

                        <div class="row mb-3">
                            <div class="col-md-6">
                                <x-input-label for="color" :value="__('Color')" />
                                <x-text-input id="color" name="color" type="text" class="form-control" :value="old('color')" />
                                <x-input-error :messages="$errors->get('color')" class="mt-1" />
                            </div>
                            <div class="col-md-6">
                                <x-input-label for="microchip_number" :value="__('Microchip Number')" />
                                <x-text-input id="microchip_number" name="microchip_number" type="text" class="form-control" :value="old('microchip_number')" />
                                <small class="text-muted">{{ __('Optional') }}</small>
                                <x-input-error :messages="$errors->get('microchip_number')" class="mt-1" />
                            </div>
                        </div>

                        <div class="mb-3">
                            <x-input-label for="description" :value="__('Description')" />
                            <textarea class="form-control @error('description') is-invalid @enderror" id="description" name="description" rows="4">{{ old('description') }}</textarea>
                            @error('description')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="mb-3">
                            <div class="form-check form-switch">
                                <input class="form-check-input" type="checkbox" name="is_neutered" value="1" id="is_neutered" {{ old('is_neutered') ? 'checked' : '' }}>
                                <label class="form-check-label" for="is_neutered">{{ __('Neutered / Spayed') }}</label>
                            </div>
                        </div>

                        <div class="d-flex justify-content-end gap-2">
                            <a href="{{ route('owner.pets.index') }}" class="btn btn-outline-secondary">{{ __('Cancel') }}</a>
                            <x-primary-button>{{ __('Add Pet') }}</x-primary-button>
                        </div>
                </div>
            </div>
        </div>

        <div class="col-lg-4">
            <div class="card mb-4">
                <div class="card-header">
                    <h6 class="mb-0 fw-semibold">{{ __('Pet Images') }}</h6>
                </div>
                <div class="card-body">
                    <div class="mb-3">
                        <input type="file" class="form-control @error('images') is-invalid @enderror" name="images[]" id="images" multiple accept=".jpg,.jpeg,.png,.gif,.webp">
                        <small class="text-muted">{{ __('Max 6 images, 5MB each. JPG, PNG, GIF, WebP.') }}</small>
                        @error('images')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                    <div id="image-previews" class="d-flex flex-wrap gap-2"></div>
                </div>
            </div>

            <div class="card">
                <div class="card-header">
                    <h6 class="mb-0 fw-semibold">{{ __('Tips') }}</h6>
                </div>
                <div class="card-body">
                    <ul class="mb-0" style="font-size: 0.875rem;">
                        <li class="mb-2">{{ __('Provide accurate information for better vet consultations.') }}</li>
                        <li class="mb-2">{{ __('Microchip number helps in case your pet gets lost.') }}</li>
                        <li class="mb-2">{{ __('Weight is used for medication dosage calculations.') }}</li>
                        <li class="mb-0">{{ __('Date of birth helps track vaccination schedules.') }}</li>
                    </ul>
                </div>
            </div>
        </div>
    </div>
    </form>

    @push('scripts')
    <script>
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

        document.getElementById('species_id').addEventListener('change', function() {
            const speciesId = this.value;
            const breedSelect = document.getElementById('breed_id');

            breedSelect.innerHTML = '<option value="">{{ __("Loading...") }}</option>';

            if (!speciesId) {
                breedSelect.innerHTML = '<option value="">{{ __("Select breed (optional)") }}</option>';
                return;
            }

                fetch(`/owner/species/${speciesId}/breeds`)
                .then(response => response.json())
                .then(data => {
                    let options = '<option value="">{{ __("Select breed (optional)") }}</option>';
                    if (data.data && data.data.length > 0) {
                        data.data.forEach(breed => {
                            options += `<option value="${breed.id}">${breed.name}</option>`;
                        });
                    } else if (Array.isArray(data)) {
                        data.forEach(breed => {
                            options += `<option value="${breed.id}">${breed.name}</option>`;
                        });
                    }
                    breedSelect.innerHTML = options;
                })
                .catch(() => {
                    breedSelect.innerHTML = '<option value="">{{ __("Select breed (optional)") }}</option>';
                });
        });
    </script>
    @endpush
@endsection
