@extends('layouts.owner.app')

@section('content')
    <div class="mb-4">
        <div class="d-flex justify-content-between align-items-center">
            <h2 class="h4 mb-0 fw-semibold">{{ __('Edit Pet') }}</h2>
            <a href="{{ route('owner.pets.show', $pet) }}" class="btn btn-outline-secondary btn-sm">
                <i class="bi bi-arrow-left me-1"></i>{{ __('Back') }}
            </a>
        </div>
    </div>

    <form action="{{ route('owner.pets.update', $pet) }}" method="POST" enctype="multipart/form-data">
        @csrf
        @method('PUT')
    <div class="row">
        <div class="col-lg-8">
            <div class="card">
                <div class="card-body p-4">
                        <div class="row mb-3">
                            <div class="col-md-6">
                                <x-input-label for="name" :value="__('Pet Name')" />
                                <x-text-input id="name" name="name" type="text" class="form-control" :value="old('name', $pet->name)" required autofocus />
                                <x-input-error :messages="$errors->get('name')" class="mt-1" />
                            </div>
                            <div class="col-md-6">
                                <x-input-label for="species_id" :value="__('Species')" />
                                <select class="form-select @error('species_id') is-invalid @enderror" id="species_id" name="species_id" required>
                                    <option value="">{{ __('Select Species') }}</option>
                                    @foreach($species as $specie)
                                        <option value="{{ $specie->id }}" {{ old('species_id', $pet->species_id) == $specie->id ? 'selected' : '' }}>{{ $specie->name }}</option>
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
                                    @if($pet->breed)
                                        <option value="{{ $pet->breed->id }}" selected>{{ $pet->breed->name }}</option>
                                    @endif
                                </select>
                                @error('breed_id')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                            <div class="col-md-6">
                                <x-input-label for="gender" :value="__('Gender')" />
                                <div class="d-flex gap-4 mt-2">
                                    <div class="form-check">
                                        <input class="form-check-input" type="radio" name="gender" id="gender_male" value="male" {{ old('gender', $pet->gender) === 'male' ? 'checked' : '' }}>
                                        <label class="form-check-label" for="gender_male">
                                            <i class="bi bi-gender-male me-1"></i>{{ __('Male') }}
                                        </label>
                                    </div>
                                    <div class="form-check">
                                        <input class="form-check-input" type="radio" name="gender" id="gender_female" value="female" {{ old('gender', $pet->gender) === 'female' ? 'checked' : '' }}>
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
                                <x-text-input id="date_of_birth" name="date_of_birth" type="date" class="form-control" :value="old('date_of_birth', $pet->date_of_birth ? $pet->date_of_birth->format('Y-m-d') : '')" />
                                <x-input-error :messages="$errors->get('date_of_birth')" class="mt-1" />
                            </div>
                            <div class="col-md-6">
                                <x-input-label for="weight" :value="__('Weight (kg)')" />
                                <x-text-input id="weight" name="weight" type="number" step="0.1" min="0" class="form-control" :value="old('weight', $pet->weight)" />
                                <x-input-error :messages="$errors->get('weight')" class="mt-1" />
                            </div>
                        </div>

                        <div class="row mb-3">
                            <div class="col-md-6">
                                <x-input-label for="color" :value="__('Color')" />
                                <x-text-input id="color" name="color" type="text" class="form-control" :value="old('color', $pet->color)" />
                                <x-input-error :messages="$errors->get('color')" class="mt-1" />
                            </div>
                            <div class="col-md-6">
                                <x-input-label for="microchip_number" :value="__('Microchip Number')" />
                                <x-text-input id="microchip_number" name="microchip_number" type="text" class="form-control" :value="old('microchip_number', $pet->microchip_number)" />
                                <small class="text-muted">{{ __('Optional') }}</small>
                                <x-input-error :messages="$errors->get('microchip_number')" class="mt-1" />
                            </div>
                        </div>

                        <div class="mb-3">
                            <x-input-label for="description" :value="__('Description')" />
                            <textarea class="form-control @error('description') is-invalid @enderror" id="description" name="description" rows="4">{{ old('description', $pet->description) }}</textarea>
                            @error('description')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="mb-3">
                            <div class="form-check form-switch">
                                <input class="form-check-input" type="checkbox" name="is_neutered" value="1" id="is_neutered" {{ old('is_neutered', $pet->is_neutered) ? 'checked' : '' }}>
                                <label class="form-check-label" for="is_neutered">{{ __('Neutered / Spayed') }}</label>
                            </div>
                        </div>

                        <div class="d-flex justify-content-end gap-2">
                            <a href="{{ route('owner.pets.show', $pet) }}" class="btn btn-outline-secondary">{{ __('Cancel') }}</a>
                            <x-primary-button>{{ __('Update Pet') }}</x-primary-button>
                        </div>
                </div>
            </div>
        </div>

        <div class="col-lg-4">
            <!-- Existing Images -->
            <div class="card mb-4">
                <div class="card-header">
                    <h6 class="mb-0 fw-semibold">{{ __('Current Images') }} ({{ $pet->images->count() }})</h6>
                </div>
                <div class="card-body">
                    @if ($pet->images->count() > 0)
                        <div class="d-flex flex-wrap gap-2 mb-3">
                            @foreach ($pet->images->sortBy('sort_order') as $image)
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
                        @error('images')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                    <div id="image-previews" class="d-flex flex-wrap gap-2"></div>
                </div>
            </div>

            <div class="card">
                <div class="card-header">
                    <h6 class="mb-0 fw-semibold">{{ __('Pet Info') }}</h6>
                </div>
                <div class="card-body">
                    <div class="mb-2">
                        <small class="text-muted">{{ __('Species') }}</small>
                        <div>{{ $pet->species->name ?? '-' }}</div>
                    </div>
                    <div class="mb-2">
                        <small class="text-muted">{{ __('Breed') }}</small>
                        <div>{{ $pet->breed->name ?? '-' }}</div>
                    </div>
                    <div class="mb-2">
                        <small class="text-muted">{{ __('Created') }}</small>
                        <div>{{ $pet->created_at->format('M d, Y H:i') }}</div>
                    </div>
                    <div>
                        <small class="text-muted">{{ __('Last Updated') }}</small>
                        <div>{{ $pet->updated_at->format('M d, Y H:i') }}</div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    </form>

    <hr class="my-4">

    <div class="mb-3">
        <h5 class="fw-semibold">{{ __('Health Management') }}</h5>
    </div>

    <ul class="nav nav-tabs mb-4" role="tablist">
        <li class="nav-item" role="presentation">
            <button class="nav-link active" data-bs-toggle="tab" data-bs-target="#edit-health-records" type="button" role="tab">
                <i class="bi bi-clipboard2-pulse me-1"></i>{{ __('Health Records') }}
            </button>
        </li>
        <li class="nav-item" role="presentation">
            <button class="nav-link" data-bs-toggle="tab" data-bs-target="#edit-vaccinations" type="button" role="tab">
                <i class="bi bi-shield-check me-1"></i>{{ __('Vaccinations') }}
            </button>
        </li>
        <li class="nav-item" role="presentation">
            <button class="nav-link" data-bs-toggle="tab" data-bs-target="#edit-documents" type="button" role="tab">
                <i class="bi bi-file-earmark-medical me-1"></i>{{ __('Documents') }}
            </button>
        </li>
    </ul>

    <div class="tab-content">
        <!-- Health Records Tab -->
        <div class="tab-pane fade show active" id="edit-health-records" role="tabpanel">
            <div class="card">
                <div class="card-header d-flex justify-content-between align-items-center">
                    <h6 class="mb-0 fw-semibold">{{ __('Health Records') }}</h6>
                    <button class="btn btn-sm btn-primary" data-bs-toggle="modal" data-bs-target="#addHealthRecordModal">
                        <i class="bi bi-plus-circle me-1"></i>{{ __('Add Record') }}
                    </button>
                </div>
                <div class="card-body p-0">
                    <div class="table-responsive">
                        <table class="table table-hover mb-0">
                            <thead>
                                <tr>
                                    <th>{{ __('Date') }}</th>
                                    <th>{{ __('Type') }}</th>
                                    <th>{{ __('Description') }}</th>
                                    <th>{{ __('Vet') }}</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($pet->healthRecords ?? [] as $record)
                                    <tr>
                                        <td class="text-muted">{{ $record->record_date ? \Carbon\Carbon::parse($record->record_date)->format('M d, Y') : '-' }}</td>
                                        <td><span class="badge bg-primary">{{ ucfirst($record->record_type ?? '-') }}</span></td>
                                        <td>{{ $record->description ?: '-' }}</td>
                                        <td class="text-muted">{{ $record->vet->name ?? '-' }}</td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="4" class="text-center py-4">
                                            <i class="bi bi-clipboard2-pulse" style="font-size:2rem;opacity:0.3;"></i>
                                            <p class="mt-2 text-muted mb-0" style="font-size:0.875rem;">{{ __('No health records yet.') }}</p>
                                        </td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>

        <!-- Vaccinations Tab -->
        <div class="tab-pane fade" id="edit-vaccinations" role="tabpanel">
            <div class="card">
                <div class="card-header d-flex justify-content-between align-items-center">
                    <h6 class="mb-0 fw-semibold">{{ __('Vaccinations') }}</h6>
                    <button class="btn btn-sm btn-primary" data-bs-toggle="modal" data-bs-target="#addVaccinationModal">
                        <i class="bi bi-plus-circle me-1"></i>{{ __('Add Vaccination') }}
                    </button>
                </div>
                <div class="card-body p-0">
                    <div class="table-responsive">
                        <table class="table table-hover mb-0">
                            <thead>
                                <tr>
                                    <th>{{ __('Vaccine') }}</th>
                                    <th>{{ __('Date') }}</th>
                                    <th>{{ __('Next Due') }}</th>
                                    <th>{{ __('Batch No.') }}</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($pet->vaccinations ?? [] as $vaccination)
                                    <tr>
                                        <td class="fw-medium">{{ $vaccination->vaccine_name ?: '-' }}</td>
                                        <td class="text-muted">{{ $vaccination->vaccination_date ? \Carbon\Carbon::parse($vaccination->vaccination_date)->format('M d, Y') : '-' }}</td>
                                        <td>
                                            @if ($vaccination->next_due_date)
                                                @php $isOverdue = \Carbon\Carbon::parse($vaccination->next_due_date)->isPast(); @endphp
                                                <span class="{{ $isOverdue ? 'text-danger fw-semibold' : 'text-muted' }}">
                                                    {{ \Carbon\Carbon::parse($vaccination->next_due_date)->format('M d, Y') }}
                                                    @if ($isOverdue)<i class="bi bi-exclamation-triangle ms-1"></i>@endif
                                                </span>
                                            @else
                                                -
                                            @endif
                                        </td>
                                        <td><code>{{ $vaccination->batch_number ?: '-' }}</code></td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="4" class="text-center py-4">
                                            <i class="bi bi-shield-check" style="font-size:2rem;opacity:0.3;"></i>
                                            <p class="mt-2 text-muted mb-0" style="font-size:0.875rem;">{{ __('No vaccination records yet.') }}</p>
                                        </td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>

        <!-- Documents Tab -->
        <div class="tab-pane fade" id="edit-documents" role="tabpanel">
            <div class="card">
                <div class="card-header d-flex justify-content-between align-items-center">
                    <h6 class="mb-0 fw-semibold">{{ __('Medical Documents') }}</h6>
                    <button class="btn btn-sm btn-primary" data-bs-toggle="modal" data-bs-target="#addDocumentModal">
                        <i class="bi bi-upload me-1"></i>{{ __('Upload') }}
                    </button>
                </div>
                <div class="card-body p-0">
                    @forelse($pet->medicalDocuments ?? [] as $doc)
                        <div class="d-flex align-items-center justify-content-between px-3 py-2 border-bottom">
                            <div class="d-flex align-items-center">
                                @if (str_contains($doc->mime_type ?? '', 'image'))
                                    <i class="bi bi-image text-success me-2 fs-5"></i>
                                @elseif (str_contains($doc->mime_type ?? '', 'pdf'))
                                    <i class="bi bi-file-earmark-pdf text-danger me-2 fs-5"></i>
                                @else
                                    <i class="bi bi-file-earmark text-primary me-2 fs-5"></i>
                                @endif
                                <div>
                                    <div class="fw-medium" style="font-size:0.875rem;">{{ $doc->file_name }}</div>
                                    <small class="text-muted">{{ $doc->document_type ?: __('Medical Document') }} &middot; {{ $doc->created_at->format('M d, Y') }}</small>
                                </div>
                            </div>
                            <a href="{{ asset('storage/' . $doc->file_path) }}" target="_blank" class="btn btn-outline-secondary btn-sm">
                                <i class="bi bi-eye"></i>
                            </a>
                        </div>
                    @empty
                        <div class="text-center py-4">
                            <i class="bi bi-file-earmark-medical" style="font-size:2rem;opacity:0.3;"></i>
                            <p class="mt-2 text-muted mb-0" style="font-size:0.875rem;">{{ __('No medical documents uploaded yet.') }}</p>
                        </div>
                    @endforelse
                </div>
            </div>
        </div>
    </div>

    <!-- Add Health Record Modal -->
    <div class="modal fade" id="addHealthRecordModal" tabindex="-1">
        <div class="modal-dialog">
            <div class="modal-content">
                <form action="{{ route('owner.health.store', $pet) }}" method="POST">
                    @csrf
                    <div class="modal-header">
                        <h5 class="modal-title fw-semibold">{{ __('Add Health Record') }}</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                    </div>
                    <div class="modal-body">
                        <div class="mb-3">
                            <label class="form-label fw-semibold">{{ __('Date') }} *</label>
                            <input type="date" name="record_date" class="form-control" value="{{ old('record_date', now()->format('Y-m-d')) }}" required>
                        </div>
                        <div class="mb-3">
                            <label class="form-label fw-semibold">{{ __('Type') }} *</label>
                            <select name="record_type" class="form-select" required>
                                <option value="">{{ __('Select type') }}</option>
                                @foreach(['checkup','surgery','illness','injury','dental','emergency','other'] as $type)
                                    <option value="{{ $type }}" {{ old('record_type') === $type ? 'selected' : '' }}>{{ ucfirst(__($type)) }}</option>
                                @endforeach
                            </select>
                        </div>
                        <div class="mb-3">
                            <label class="form-label fw-semibold">{{ __('Description') }}</label>
                            <textarea name="description" class="form-control" rows="3" placeholder="{{ __('Describe the health record...') }}">{{ old('description') }}</textarea>
                        </div>
                        <div class="mb-3">
                            <label class="form-label fw-semibold">{{ __('Notes') }}</label>
                            <textarea name="notes" class="form-control" rows="2" placeholder="{{ __('Additional notes (optional)') }}">{{ old('notes') }}</textarea>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-outline-secondary" data-bs-dismiss="modal">{{ __('Cancel') }}</button>
                        <button type="submit" class="btn" style="background:#1a6b3c;color:#fff;">{{ __('Save Record') }}</button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <!-- Add Vaccination Modal -->
    <div class="modal fade" id="addVaccinationModal" tabindex="-1">
        <div class="modal-dialog">
            <div class="modal-content">
                <form action="{{ route('owner.health.storeVaccination', $pet) }}" method="POST">
                    @csrf
                    <div class="modal-header">
                        <h5 class="modal-title fw-semibold">{{ __('Add Vaccination') }}</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                    </div>
                    <div class="modal-body">
                        <div class="mb-3">
                            <label class="form-label fw-semibold">{{ __('Vaccine Name') }} *</label>
                            <input type="text" name="vaccine_name" class="form-control" value="{{ old('vaccine_name') }}" placeholder="{{ __('e.g. Rabies, DHPP, FVRCP') }}" required>
                        </div>
                        <div class="row mb-3">
                            <div class="col-md-6">
                                <label class="form-label fw-semibold">{{ __('Vaccination Date') }} *</label>
                                <input type="date" name="vaccination_date" class="form-control" value="{{ old('vaccination_date', now()->format('Y-m-d')) }}" required>
                            </div>
                            <div class="col-md-6">
                                <label class="form-label fw-semibold">{{ __('Next Due Date') }}</label>
                                <input type="date" name="next_due_date" class="form-control" value="{{ old('next_due_date') }}">
                            </div>
                        </div>
                        <div class="mb-3">
                            <label class="form-label fw-semibold">{{ __('Batch Number') }}</label>
                            <input type="text" name="batch_number" class="form-control" value="{{ old('batch_number') }}" placeholder="{{ __('Optional') }}">
                        </div>
                        <div class="mb-3">
                            <label class="form-label fw-semibold">{{ __('Notes') }}</label>
                            <textarea name="notes" class="form-control" rows="2" placeholder="{{ __('Additional notes (optional)') }}">{{ old('notes') }}</textarea>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-outline-secondary" data-bs-dismiss="modal">{{ __('Cancel') }}</button>
                        <button type="submit" class="btn" style="background:#1a6b3c;color:#fff;">{{ __('Save Vaccination') }}</button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <!-- Upload Document Modal -->
    <div class="modal fade" id="addDocumentModal" tabindex="-1">
        <div class="modal-dialog">
            <div class="modal-content">
                <form action="{{ route('owner.health.storeDocument', $pet) }}" method="POST" enctype="multipart/form-data">
                    @csrf
                    <div class="modal-header">
                        <h5 class="modal-title fw-semibold">{{ __('Upload Medical Document') }}</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                    </div>
                    <div class="modal-body">
                        <div class="mb-3">
                            <label class="form-label fw-semibold">{{ __('Document Type') }}</label>
                            <select name="document_type" class="form-select">
                                <option value="">{{ __('Select type') }}</option>
                                @foreach(['lab_result','prescription','xray','vaccination_cert','insurance','other'] as $type)
                                    <option value="{{ $type }}" {{ old('document_type') === $type ? 'selected' : '' }}>{{ ucfirst(str_replace('_',' ',$type)) }}</option>
                                @endforeach
                            </select>
                        </div>
                        <div class="mb-3">
                            <label class="form-label fw-semibold">{{ __('File') }} *</label>
                            <input type="file" name="file" class="form-control" accept=".pdf,.jpg,.jpeg,.png,.doc,.docx" required>
                            <small class="text-muted">{{ __('PDF, JPG, PNG, DOC. Max 10MB.') }}</small>
                        </div>
                        <div class="mb-3">
                            <label class="form-label fw-semibold">{{ __('Description') }}</label>
                            <input type="text" name="description" class="form-control" value="{{ old('description') }}" placeholder="{{ __('Brief description (optional)') }}">
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-outline-secondary" data-bs-dismiss="modal">{{ __('Cancel') }}</button>
                        <button type="submit" class="btn" style="background:#1a6b3c;color:#fff;">{{ __('Upload') }}</button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    @push('scripts')
    <script>
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

        document.getElementById('species_id').addEventListener('change', function() {
            const speciesId = this.value;
            const breedSelect = document.getElementById('breed_id');
            const currentBreedId = '{{ old("breed_id", $pet->breed_id) }}';

            breedSelect.innerHTML = '<option value="">{{ __("Loading...") }}</option>';

            if (!speciesId) {
                breedSelect.innerHTML = '<option value="">{{ __("Select breed (optional)") }}</option>';
                return;
            }

                fetch(`/owner/species/${speciesId}/breeds`)
                .then(response => response.json())
                .then(data => {
                    let options = '<option value="">{{ __("Select breed (optional)") }}</option>';
                    const breeds = data.data || (Array.isArray(data) ? data : []);
                    breeds.forEach(breed => {
                        const selected = breed.id == currentBreedId ? 'selected' : '';
                        options += `<option value="${breed.id}" ${selected}>${breed.name}</option>`;
                    });
                    breedSelect.innerHTML = options;
                })
                .catch(() => {
                    breedSelect.innerHTML = '<option value="">{{ __("Select breed (optional)") }}</option>';
                });
        });

        // Trigger on load if species is selected
        const speciesSelect = document.getElementById('species_id');
        if (speciesSelect.value) {
            speciesSelect.dispatchEvent(new Event('change'));
        }
    </script>
    @endpush
@endsection
