@extends('layouts.shelter.app')

@section('content')
<div class="mb-4">
    <div class="d-flex justify-content-between align-items-center">
        <h2 class="h4 mb-0 fw-semibold">{{ __('New Adoption Listing') }}</h2>
        <a href="{{ route('shelter.listings.index') }}" class="btn btn-outline-secondary btn-sm">
            <i class="bi bi-arrow-left me-1"></i>{{ __('Back') }}
        </a>
    </div>
</div>

<form action="{{ route('shelter.listings.store') }}" method="POST" enctype="multipart/form-data">
    @csrf
<div class="row">
    <div class="col-lg-8">
        <div class="card">
            <div class="card-body p-4">
                    <div class="row mb-3">
                        <div class="col-md-6">
                            <label class="form-label fw-semibold">{{ __('Pet Name') }} *</label>
                            <input type="text" class="form-control @error('pet_name') is-invalid @enderror" name="pet_name" value="{{ old('pet_name') }}" required>
                            @error('pet_name')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                        <div class="col-md-6">
                            <label class="form-label fw-semibold">{{ __('Species') }} *</label>
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
                            <label class="form-label fw-semibold">{{ __('Breed') }}</label>
                            <select class="form-select @error('breed_id') is-invalid @enderror" id="breed_id" name="breed_id">
                                <option value="">{{ __('Select breed (optional)') }}</option>
                            </select>
                            @error('breed_id')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                        <div class="col-md-6">
                            <label class="form-label fw-semibold">{{ __('Gender') }}</label>
                            <div class="d-flex gap-4 mt-2">
                                <div class="form-check">
                                    <input class="form-check-input" type="radio" name="gender" id="gender_male" value="male" {{ old('gender') === 'male' ? 'checked' : '' }}>
                                    <label class="form-check-label" for="gender_male"><i class="bi bi-gender-male me-1"></i>{{ __('Male') }}</label>
                                </div>
                                <div class="form-check">
                                    <input class="form-check-input" type="radio" name="gender" id="gender_female" value="female" {{ old('gender') === 'female' ? 'checked' : '' }}>
                                    <label class="form-check-label" for="gender_female"><i class="bi bi-gender-female me-1"></i>{{ __('Female') }}</label>
                                </div>
                            </div>
                            @error('gender')
                                <div class="invalid-feedback d-block">{{ $message }}</div>
                            @enderror
                        </div>
                    </div>

                    <div class="row mb-3">
                        <div class="col-md-6">
                            <label class="form-label fw-semibold">{{ __('Age') }}</label>
                            <input type="text" class="form-control @error('age') is-invalid @enderror" name="age" value="{{ old('age') }}" placeholder="{{ __('e.g. 2 years') }}">
                            @error('age')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                        <div class="col-md-6">
                            <label class="form-label fw-semibold">{{ __('Health Status') }}</label>
                            <input type="text" class="form-control @error('health_status') is-invalid @enderror" name="health_status" value="{{ old('health_status') }}" placeholder="{{ __('e.g. Vaccinated, Healthy') }}">
                            @error('health_status')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                    </div>

                    <div class="mb-3">
                        <label class="form-label fw-semibold">{{ __('Description') }}</label>
                        <textarea class="form-control @error('description') is-invalid @enderror" name="description" rows="4">{{ old('description') }}</textarea>
                        @error('description')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    <div class="d-flex justify-content-end gap-2">
                        <a href="{{ route('shelter.listings.index') }}" class="btn btn-outline-secondary">{{ __('Cancel') }}</a>
                        <button type="submit" class="btn btn-primary"><i class="bi bi-plus me-1"></i>{{ __('Create Listing') }}</button>
                    </div>
            </div>
        </div>
    </div>

    <div class="col-lg-4">
        <div class="card">
            <div class="card-header">
                <h6 class="mb-0 fw-semibold">{{ __('Images') }}</h6>
            </div>
            <div class="card-body">
                <div class="mb-3">
                    <input type="file" class="form-control @error('images') is-invalid @enderror" name="images[]" id="images" multiple accept=".jpg,.jpeg,.png,.gif,.webp">
                    <small class="text-muted">{{ __('Max 6 images, 5MB each.') }}</small>
                    @error('images')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>
                <div id="image-previews" class="d-flex flex-wrap gap-2"></div>
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
        Array.from(e.target.files).slice(0, 6).forEach(function(file) {
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
        breedSelect.innerHTML = '<option value="">Loading...</option>';
        if (!speciesId) {
            breedSelect.innerHTML = '<option value="">Select breed (optional)</option>';
            return;
        }
        fetch('/owner/species/' + speciesId + '/breeds')
            .then(r => r.json())
            .then(data => {
                let options = '<option value="">Select breed (optional)</option>';
                const breeds = data.data || data;
                if (breeds.length) breeds.forEach(b => { options += '<option value="' + b.id + '">' + b.name + '</option>'; });
                breedSelect.innerHTML = options;
            })
            .catch(() => { breedSelect.innerHTML = '<option value="">Select breed (optional)</option>'; });
    });
</script>
@endpush
@endsection
