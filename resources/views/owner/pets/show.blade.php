@extends('layouts.owner.app')

@section('content')
    <div class="mb-4">
        <div class="d-flex justify-content-between align-items-center">
            <h2 class="h4 mb-0 fw-semibold">{{ __('Pet Details') }}</h2>
            <div class="d-flex gap-2">
                <a href="{{ route('owner.pets.edit', $pet) }}" class="btn btn-outline-primary btn-sm">
                    <i class="bi bi-pencil me-1"></i>{{ __('Edit') }}
                </a>
                <a href="{{ route('owner.pets.index') }}" class="btn btn-outline-secondary btn-sm">
                    <i class="bi bi-arrow-left me-1"></i>{{ __('Back') }}
                </a>
            </div>
        </div>
    </div>

    <div class="row">
        <div class="col-lg-4 mb-4">
            <div class="card">
                <div class="card-body p-4">
                    <div class="text-center mb-3">
                        @php $primaryImage = $pet->images->firstWhere('is_primary') ?? $pet->images->first(); @endphp
                        @if ($primaryImage)
                            <img src="{{ asset('storage/' . $primaryImage->image_path) }}" alt="{{ $pet->name }}"
                                class="rounded-circle mb-3" style="width:96px;height:96px;object-fit:cover;">
                        @elseif ($pet->profile_image)
                            <img src="{{ asset('storage/' . $pet->profile_image) }}" alt="{{ $pet->name }}"
                                class="rounded-circle mb-3" style="width:96px;height:96px;object-fit:cover;">
                        @else
                            <div class="rounded-circle d-inline-flex align-items-center justify-content-center mb-3"
                                style="width:96px;height:96px;background:linear-gradient(135deg,#1a6b3c,#2e9e5a);">
                                <span class="text-white fw-bold" style="font-size:2rem;">{{ substr($pet->name, 0, 1) }}</span>
                            </div>
                        @endif
                        <h4 class="mt-2 mb-1 fw-bold">{{ $pet->name }}</h4>
                        <div class="text-muted" style="font-size:0.875rem;">
                            {{ $pet->species->name ?? '-' }}{{ $pet->breed ? ' - ' . $pet->breed->name : '' }}
                        </div>
                    </div>

                    <hr>

                    <table class="table table-sm mb-0">
                        <tbody>
                            <tr>
                                <td class="fw-semibold text-muted" style="width:140px;">{{ __('Gender') }}</td>
                                <td>
                                    @if ($pet->gender)
                                        <span class="badge {{ $pet->gender === 'male' ? 'bg-info' : '' }}" style="{{ $pet->gender === 'female' ? 'background-color:#ec4899 !important;' : '' }}">
                                            <i class="bi {{ $pet->gender === 'male' ? 'bi-gender-male' : 'bi-gender-female' }} me-1"></i>{{ ucfirst($pet->gender) }}
                                        </span>
                                    @else
                                        -
                                    @endif
                                </td>
                            </tr>
                            <tr>
                                <td class="fw-semibold text-muted">{{ __('Date of Birth') }}</td>
                                <td>{{ $pet->date_of_birth ? \Carbon\Carbon::parse($pet->date_of_birth)->format('M d, Y') : '-' }}</td>
                            </tr>
                            <tr>
                                <td class="fw-semibold text-muted">{{ __('Weight') }}</td>
                                <td>{{ $pet->weight ? $pet->weight . ' kg' : '-' }}</td>
                            </tr>
                            <tr>
                                <td class="fw-semibold text-muted">{{ __('Color') }}</td>
                                <td>{{ $pet->color ?: '-' }}</td>
                            </tr>
                            <tr>
                                <td class="fw-semibold text-muted">{{ __('Neutered') }}</td>
                                <td>
                                    @if ($pet->is_neutered)
                                        <span class="badge bg-success">{{ __('Yes') }}</span>
                                    @else
                                        <span class="badge bg-secondary">{{ __('No') }}</span>
                                    @endif
                                </td>
                            </tr>
                            <tr>
                                <td class="fw-semibold text-muted">{{ __('Microchip') }}</td>
                                <td><code>{{ $pet->microchip_number ?: '-' }}</code></td>
                            </tr>
                        </tbody>
                    </table>

                    @if ($pet->description)
                        <hr>
                        <div>
                            <small class="text-muted">{{ __('Description') }}</small>
                            <p class="mt-1 mb-0" style="font-size:0.875rem;">{{ $pet->description }}</p>
                        </div>
                    @endif
                </div>
            </div>

            @if ($pet->images->count())
                <div class="card mt-4">
                    <div class="card-header">
                        <h6 class="mb-0 fw-semibold"><i class="bi bi-images me-1"></i>{{ __('Photos') }} ({{ $pet->images->count() }})</h6>
                    </div>
                    <div class="card-body">
                        <div class="d-flex flex-wrap gap-2">
                            @foreach ($pet->images->sortBy('sort_order') as $image)
                                <div class="position-relative">
                                    <img src="{{ asset('storage/' . $image->image_path) }}" alt=""
                                        class="img-thumbnail" style="width:80px;height:80px;object-fit:cover;">
                                    @if ($image->is_primary)
                                        <span class="position-absolute top-0 end-0 badge bg-warning" style="font-size:0.55rem;">
                                            <i class="bi bi-star-fill"></i>
                                        </span>
                                    @endif
                                </div>
                            @endforeach
                        </div>
                    </div>
                </div>
            @endif
        </div>

        <div class="col-lg-8">
            <ul class="nav nav-tabs mb-4" role="tablist">
                <li class="nav-item" role="presentation">
                    <button class="nav-link active" data-bs-toggle="tab" data-bs-target="#health-records" type="button" role="tab">
                        <i class="bi bi-clipboard2-pulse me-1"></i>{{ __('Health Records') }}
                    </button>
                </li>
                <li class="nav-item" role="presentation">
                    <button class="nav-link" data-bs-toggle="tab" data-bs-target="#vaccinations" type="button" role="tab">
                        <i class="bi bi-shield-check me-1"></i>{{ __('Vaccinations') }}
                    </button>
                </li>
                <li class="nav-item" role="presentation">
                    <button class="nav-link" data-bs-toggle="tab" data-bs-target="#documents" type="button" role="tab">
                        <i class="bi bi-file-earmark-medical me-1"></i>{{ __('Documents') }}
                    </button>
                </li>
            </ul>

            <div class="tab-content">
                <!-- Health Records Tab -->
                <div class="tab-pane fade show active" id="health-records" role="tabpanel">
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
                <div class="tab-pane fade" id="vaccinations" role="tabpanel">
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
                <div class="tab-pane fade" id="documents" role="tabpanel">
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
                                    <a href="{{ route('owner.health.document',[$pet,$doc]) }}" target="_blank" class="btn btn-outline-secondary btn-sm">
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
                            <label for="record_date" class="form-label fw-semibold">{{ __('Date') }} *</label>
                            <input type="date" id="record_date" name="record_date" class="form-control" value="{{ old('record_date', now()->format('Y-m-d')) }}" required>
                        </div>
                        <div class="mb-3">
                            <label for="record_type" class="form-label fw-semibold">{{ __('Type') }} *</label>
                            <select id="record_type" name="record_type" class="form-select" required>
                                <option value="">{{ __('Select type') }}</option>
                                <option value="checkup" {{ old('record_type') === 'checkup' ? 'selected' : '' }}>{{ __('Checkup') }}</option>
                                <option value="surgery" {{ old('record_type') === 'surgery' ? 'selected' : '' }}>{{ __('Surgery') }}</option>
                                <option value="illness" {{ old('record_type') === 'illness' ? 'selected' : '' }}>{{ __('Illness') }}</option>
                                <option value="injury" {{ old('record_type') === 'injury' ? 'selected' : '' }}>{{ __('Injury') }}</option>
                                <option value="dental" {{ old('record_type') === 'dental' ? 'selected' : '' }}>{{ __('Dental') }}</option>
                                <option value="emergency" {{ old('record_type') === 'emergency' ? 'selected' : '' }}>{{ __('Emergency') }}</option>
                                <option value="other" {{ old('record_type') === 'other' ? 'selected' : '' }}>{{ __('Other') }}</option>
                            </select>
                        </div>
                        <div class="mb-3">
                            <label for="description" class="form-label fw-semibold">{{ __('Description') }}</label>
                            <textarea id="description" name="description" class="form-control" rows="3" placeholder="{{ __('Describe the health record...') }}">{{ old('description') }}</textarea>
                        </div>
                        <div class="mb-3">
                            <label for="notes" class="form-label fw-semibold">{{ __('Notes') }}</label>
                            <textarea id="notes" name="notes" class="form-control" rows="2" placeholder="{{ __('Additional notes (optional)') }}">{{ old('notes') }}</textarea>
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
                            <label for="document_type" class="form-label fw-semibold">{{ __('Document Type') }}</label>
                            <select id="document_type" name="document_type" class="form-select">
                                <option value="">{{ __('Select type') }}</option>
                                <option value="lab_result" {{ old('document_type') === 'lab_result' ? 'selected' : '' }}>{{ __('Lab Result') }}</option>
                                <option value="prescription" {{ old('document_type') === 'prescription' ? 'selected' : '' }}>{{ __('Prescription') }}</option>
                                <option value="xray" {{ old('document_type') === 'xray' ? 'selected' : '' }}>{{ __('X-Ray') }}</option>
                                <option value="vaccination_cert" {{ old('document_type') === 'vaccination_cert' ? 'selected' : '' }}>{{ __('Vaccination Certificate') }}</option>
                                <option value="insurance" {{ old('document_type') === 'insurance' ? 'selected' : '' }}>{{ __('Insurance') }}</option>
                                <option value="other" {{ old('document_type') === 'other' ? 'selected' : '' }}>{{ __('Other') }}</option>
                            </select>
                        </div>
                        <div class="mb-3">
                            <label for="file" class="form-label fw-semibold">{{ __('File') }} *</label>
                            <input type="file" id="file" name="file" class="form-control" accept=".pdf,.jpg,.jpeg,.png,.doc,.docx" required>
                            <small class="text-muted">{{ __('PDF, JPG, PNG, DOC. Max 10MB.') }}</small>
                        </div>
                        <div class="mb-3">
                            <label for="doc_description" class="form-label fw-semibold">{{ __('Description') }}</label>
                            <input type="text" id="doc_description" name="description" class="form-control" value="{{ old('description') }}" placeholder="{{ __('Brief description (optional)') }}">
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
                            <label for="vaccine_name" class="form-label fw-semibold">{{ __('Vaccine Name') }} *</label>
                            <input type="text" id="vaccine_name" name="vaccine_name" class="form-control" value="{{ old('vaccine_name') }}" placeholder="{{ __('e.g. Rabies, DHPP, FVRCP') }}" required>
                        </div>
                        <div class="row mb-3">
                            <div class="col-md-6">
                                <label for="vaccination_date" class="form-label fw-semibold">{{ __('Vaccination Date') }} *</label>
                                <input type="date" id="vaccination_date" name="vaccination_date" class="form-control" value="{{ old('vaccination_date', now()->format('Y-m-d')) }}" required>
                            </div>
                            <div class="col-md-6">
                                <label for="next_due_date" class="form-label fw-semibold">{{ __('Next Due Date') }}</label>
                                <input type="date" id="next_due_date" name="next_due_date" class="form-control" value="{{ old('next_due_date') }}">
                            </div>
                        </div>
                        <div class="mb-3">
                            <label for="batch_number" class="form-label fw-semibold">{{ __('Batch Number') }}</label>
                            <input type="text" id="batch_number" name="batch_number" class="form-control" value="{{ old('batch_number') }}" placeholder="{{ __('Optional') }}">
                        </div>
                        <div class="mb-3">
                            <label for="vaccination_notes" class="form-label fw-semibold">{{ __('Notes') }}</label>
                            <textarea id="vaccination_notes" name="notes" class="form-control" rows="2" placeholder="{{ __('Additional notes (optional)') }}">{{ old('notes') }}</textarea>
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

    <div class="mt-4">
        <div class="card border-danger">
            <div class="card-header">
                <h6 class="mb-0 fw-semibold text-danger">{{ __('Danger Zone') }}</h6>
            </div>
            <div class="card-body">
                <div class="d-flex justify-content-between align-items-center">
                    <div>
                        <p class="mb-0">{{ __('Permanently delete this pet and all associated records.') }}</p>
                        <small class="text-muted">{{ __('This action cannot be undone.') }}</small>
                    </div>
                    <form action="{{ route('owner.pets.destroy', $pet) }}" method="POST" onsubmit="return confirm('{{ __('Are you sure you want to delete this pet? This cannot be undone.') }}')">
                        @csrf
                        @method('DELETE')
                        <button type="submit" class="btn btn-danger btn-sm">
                            <i class="bi bi-trash me-1"></i>{{ __('Delete Pet') }}
                        </button>
                    </form>
                </div>
            </div>
        </div>
    </div>
@endsection
