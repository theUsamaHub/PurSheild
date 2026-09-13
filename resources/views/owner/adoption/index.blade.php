@extends('layouts.owner.app')

@section('content')
    <div class="mb-4">
        <div class="d-flex justify-content-between align-items-center">
            <h2 class="h4 mb-0 fw-semibold">{{ __('Adoption Listings') }}</h2>
            <span class="badge bg-primary bg-opacity-10 text-primary">{{ $listings->total() }} {{ __('pets available') }}</span>
        </div>
    </div>

    <!-- Search & Filters -->
    <div class="card mb-4">
        <div class="card-body">
            <form method="GET" action="{{ route('owner.browse-adoption') }}" class="row g-3">
                <div class="col-md-4">
                    <input type="text" class="form-control" name="search" placeholder="{{ __('Search by name, breed, shelter...') }}" value="{{ request('search') }}">
                </div>
                <div class="col-md-3">
                    <select class="form-select" name="species_id">
                        <option value="">{{ __('All Species') }}</option>
                        @foreach ($species as $sp)
                            <option value="{{ $sp->id }}" {{ request('species_id') == $sp->id ? 'selected' : '' }}>{{ $sp->name }}</option>
                        @endforeach
                    </select>
                </div>
                <div class="col-md-2">
                    <select class="form-select" name="gender">
                        <option value="">{{ __('All Genders') }}</option>
                        <option value="male" {{ request('gender') === 'male' ? 'selected' : '' }}>{{ __('Male') }}</option>
                        <option value="female" {{ request('gender') === 'female' ? 'selected' : '' }}>{{ __('Female') }}</option>
                    </select>
                </div>
                <div class="col-md-2">
                    <select class="form-select" name="health_status">
                        <option value="">{{ __('All Health') }}</option>
                        <option value="healthy" {{ request('health_status') === 'healthy' ? 'selected' : '' }}>{{ __('Healthy') }}</option>
                        <option value="needs_care" {{ request('health_status') === 'needs_care' ? 'selected' : '' }}>{{ __('Needs Care') }}</option>
                    </select>
                </div>
                <div class="col-md-1 d-flex gap-1">
                    <button type="submit" class="btn btn-primary flex-grow-1">
                        <i class="bi bi-search"></i>
                    </button>
                    <a href="{{ route('owner.browse-adoption') }}" class="btn btn-outline-secondary" title="{{ __('Clear') }}">
                        <i class="bi bi-x"></i>
                    </a>
                </div>
            </form>
        </div>
    </div>

    <!-- Adoption Cards -->
    <div class="row g-4">
        @forelse ($listings as $listing)
            <div class="col-md-6 col-lg-4">
                <div class="card h-100 border-0 shadow-sm overflow-hidden">
                    <div class="position-relative" style="height:220px;background:#f0f7f2;">
                        @if ($listing->images->count())
                            <img src="{{ asset('storage/' . $listing->images->first()->image_path) }}"
                                 alt="{{ $listing->pet_name }}"
                                 class="w-100 h-100"
                                 style="object-fit:cover;">
                        @else
                            <div class="w-100 h-100 d-flex align-items-center justify-content-center">
                                <i class="bi bi-heart" style="font-size:4rem;color:#1a6b3c;opacity:0.15;"></i>
                            </div>
                        @endif
                        <span class="badge bg-white text-dark position-absolute top-0 end-0 m-2 shadow-sm" style="font-size:0.7rem;">
                            <i class="bi bi-{{ $listing->gender === 'female' ? 'gender-female text-danger' : 'gender-male text-primary' }} me-1"></i>
                            {{ ucfirst($listing->gender ?? 'Unknown') }}
                        </span>
                    </div>

                    <div class="card-body">
                        <div class="d-flex justify-content-between align-items-start mb-2">
                            <div>
                                <h5 class="card-title mb-0 fw-semibold">{{ $listing->pet_name }}</h5>
                                <small class="text-muted">
                                    <i class="bi bi-building me-1"></i>{{ $listing->shelter?->shelterProfile->shelter_name ?? $listing->shelter?->name ?? '-' }}
                                </small>
                            </div>
                        </div>

                        <p class="text-muted mb-3" style="font-size:0.85rem;display:-webkit-box;-webkit-line-clamp:2;-webkit-box-orient:vertical;overflow:hidden;">
                            {{ $listing->description ?? 'No description available.' }}
                        </p>

                        <div class="d-flex flex-wrap gap-2 mb-3">
                            <span class="badge bg-info bg-opacity-10 text-info">
                                <i class="bi bi-paw me-1"></i>{{ $listing->species?->name ?? '-' }}
                            </span>
                            @if ($listing->breed)
                                <span class="badge bg-secondary bg-opacity-10 text-secondary">
                                    {{ $listing->breed->name }}
                                </span>
                            @endif
                            <span class="badge bg-dark bg-opacity-10 text-dark">
                                <i class="bi bi-clock me-1"></i>{{ $listing->age ?? '?' }}
                            </span>
                        </div>

                        @if ($listing->health_status)
                            <div class="mb-3">
                                @if ($listing->health_status === 'healthy')
                                    <span class="badge bg-success"><i class="bi bi-check-circle me-1"></i>{{ __('Healthy') }}</span>
                                @elseif ($listing->health_status === 'needs_care')
                                    <span class="badge bg-warning text-dark"><i class="bi bi-heart-pulse me-1"></i>{{ __('Needs Care') }}</span>
                                @else
                                    <span class="badge bg-secondary">{{ ucfirst($listing->health_status) }}</span>
                                @endif
                            </div>
                        @endif

                        {{-- Shelter details --}}
                        @if ($listing->shelter?->shelterProfile)
                            <div class="border-top pt-2 mt-1">
                                @if ($listing->shelter->shelterProfile->city)
                                    <small class="text-muted d-block">
                                        <i class="bi bi-geo-alt me-1"></i>{{ $listing->shelter->shelterProfile->city }}
                                    </small>
                                @endif
                                @if ($listing->shelter->shelterProfile->capacity)
                                    <small class="text-muted d-block">
                                        <i class="bi bi-grid me-1"></i>{{ __('Capacity:') }} {{ $listing->shelter->shelterProfile->capacity }} {{ __('animals') }}
                                    </small>
                                @endif
                                @if ($listing->shelter->shelterProfile->website)
                                    <small class="d-block">
                                        <a href="{{ $listing->shelter->shelterProfile->website }}" target="_blank" class="text-decoration-none" style="font-size:0.8rem;">
                                            <i class="bi bi-link-45deg me-1"></i>{{ __('View on Map') }}
                                        </a>
                                    </small>
                                @endif
                            </div>
                        @endif
                    </div>

                    <div class="card-footer bg-transparent border-0 pt-0 pb-3 px-3">
                        <div class="d-flex gap-2">
                            <a href="{{ route('owner.adoption.show', $listing) }}" class="btn btn-outline-primary btn-sm flex-grow-1">
                                <i class="bi bi-eye me-1"></i>{{ __('View Details') }}
                            </a>
                            <button type="button" class="btn btn-primary btn-sm flex-grow-1" data-bs-toggle="modal" data-bs-target="#applyModal{{ $listing->id }}">
                                <i class="bi bi-hand-thumbs-up me-1"></i>{{ __('Apply') }}
                            </button>
                        </div>
                    </div>
                </div>
            </div>
        @empty
            <div class="col-12">
                <div class="card">
                    <div class="card-body text-center py-5">
                        <div class="empty-state">
                            <i class="bi bi-bookmark-heart" style="font-size:3rem;opacity:0.3;"></i>
                            <p class="mt-2 text-muted">{{ __('No adoption listings found.') }}</p>
                            <a href="{{ route('owner.browse-adoption') }}" class="btn btn-outline-primary btn-sm">{{ __('Clear Filters') }}</a>
                        </div>
                    </div>
                </div>
            </div>
        @endforelse
    </div>

    @if ($listings->hasPages())
        <div class="mt-4">
            {{ $listings->links() }}
        </div>
    @endif

    {{-- All Apply Modals (outside flex containers for proper scrolling) --}}
    @foreach ($listings as $listing)
        <div class="modal fade" id="applyModal{{ $listing->id }}" tabindex="-1" aria-hidden="true">
            <div class="modal-dialog modal-dialog-scrollable">
                <div class="modal-content">
                    <form action="{{ route('owner.adoption.apply', $listing) }}" method="POST">
                        @csrf
                        <div class="modal-header">
                            <div>
                                <h5 class="modal-title fw-semibold">{{ __('Apply to Adopt') }} {{ $listing->pet_name }}</h5>
                                <small class="text-muted">{{ __('at') }} {{ $listing->shelter?->shelterProfile->shelter_name ?? $listing->shelter?->name ?? 'Unknown Shelter' }}</small>
                            </div>
                            <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                        </div>
                        <div class="modal-body">
                            <div class="alert alert-light border mb-3">
                                <div class="d-flex align-items-center gap-3">
                                    @if ($listing->images->count())
                                        <img src="{{ asset('storage/' . $listing->images->first()->image_path) }}"
                                             alt="{{ $listing->pet_name }}"
                                             class="rounded" style="width:60px;height:60px;object-fit:cover;">
                                    @endif
                                    <div>
                                        <strong>{{ $listing->pet_name }}</strong>
                                        <div class="text-muted" style="font-size:0.8rem;">
                                            {{ $listing->species?->name ?? '' }}
                                            {{ $listing->breed ? ' - ' . $listing->breed->name : '' }}
                                            {{ $listing->age ? ' | ' . $listing->age : '' }}
                                            {{ ' | ' . ucfirst($listing->gender ?? 'Unknown') }}
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <h6 class="fw-semibold mb-3"><i class="bi bi-person-lines-fill me-2"></i>{{ __('Contact Information') }}</h6>
                            <div class="row g-3 mb-4">
                                <div class="col-md-6">
                                    <label for="phone{{ $listing->id }}" class="form-label">{{ __('Phone Number') }}</label>
                                    <input type="text" class="form-control" id="phone{{ $listing->id }}" name="phone"
                                           value="{{ old('phone', Auth::user()->phone ?? '') }}" placeholder="{{ __('Your phone number') }}">
                                </div>
                                <div class="col-md-6">
                                    <label for="address{{ $listing->id }}" class="form-label">{{ __('Address') }}</label>
                                    <input type="text" class="form-control" id="address{{ $listing->id }}" name="address"
                                           value="{{ old('address', Auth::user()->address ?? '') }}" placeholder="{{ __('Your address') }}">
                                </div>
                            </div>

                            <h6 class="fw-semibold mb-3"><i class="bi bi-house me-2"></i>{{ __('Your Living Situation') }}</h6>
                            <div class="row g-3 mb-4">
                                <div class="col-md-6">
                                    <label for="home_type{{ $listing->id }}" class="form-label">{{ __('Home Type') }}</label>
                                    <select class="form-select" id="home_type{{ $listing->id }}" name="home_type">
                                        <option value="">{{ __('Select') }}</option>
                                        <option value="house">{{ __('House') }}</option>
                                        <option value="apartment">{{ __('Apartment') }}</option>
                                        <option value="condo">{{ __('Condo') }}</option>
                                        <option value="farm">{{ __('Farm') }}</option>
                                        <option value="other">{{ __('Other') }}</option>
                                    </select>
                                </div>
                                <div class="col-md-6">
                                    <label for="living_situation{{ $listing->id }}" class="form-label">{{ __('Living Situation') }}</label>
                                    <select class="form-select" id="living_situation{{ $listing->id }}" name="living_situation">
                                        <option value="">{{ __('Select') }}</option>
                                        <option value="alone">{{ __('Alone') }}</option>
                                        <option value="family">{{ __('With Family') }}</option>
                                        <option value="roommates">{{ __('With Roommates') }}</option>
                                        <option value="other">{{ __('Other') }}</option>
                                    </select>
                                </div>
                                <div class="col-md-6">
                                    <label class="form-label">{{ __('Do you have a yard?') }}</label>
                                    <div>
                                        <div class="form-check form-check-inline">
                                            <input class="form-check-input" type="radio" name="has_yard" id="yard_yes{{ $listing->id }}" value="1">
                                            <label class="form-check-label" for="yard_yes{{ $listing->id }}">{{ __('Yes') }}</label>
                                        </div>
                                        <div class="form-check form-check-inline">
                                            <input class="form-check-input" type="radio" name="has_yard" id="yard_no{{ $listing->id }}" value="0">
                                            <label class="form-check-label" for="yard_no{{ $listing->id }}">{{ __('No') }}</label>
                                        </div>
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <label for="work_schedule{{ $listing->id }}" class="form-label">{{ __('Work Schedule') }}</label>
                                    <input type="text" class="form-control" id="work_schedule{{ $listing->id }}" name="work_schedule"
                                           placeholder="{{ __('e.g. 9-5, work from home, shift work') }}">
                                </div>
                            </div>

                            <h6 class="fw-semibold mb-3"><i class="bi bi-heart me-2"></i>{{ __('Other Pets & Family') }}</h6>
                            <div class="row g-3 mb-4">
                                <div class="col-md-6">
                                    <label class="form-label">{{ __('Do you have other pets?') }}</label>
                                    <div>
                                        <div class="form-check form-check-inline">
                                            <input class="form-check-input" type="radio" name="has_other_pets" id="pets_yes{{ $listing->id }}" value="1">
                                            <label class="form-check-label" for="pets_yes{{ $listing->id }}">{{ __('Yes') }}</label>
                                        </div>
                                        <div class="form-check form-check-inline">
                                            <input class="form-check-input" type="radio" name="has_other_pets" id="pets_no{{ $listing->id }}" value="0">
                                            <label class="form-check-label" for="pets_no{{ $listing->id }}">{{ __('No') }}</label>
                                        </div>
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <label class="form-label">{{ __('Do you have children?') }}</label>
                                    <div>
                                        <div class="form-check form-check-inline">
                                            <input class="form-check-input" type="radio" name="has_children" id="kids_yes{{ $listing->id }}" value="1">
                                            <label class="form-check-label" for="kids_yes{{ $listing->id }}">{{ __('Yes') }}</label>
                                        </div>
                                        <div class="form-check form-check-inline">
                                            <input class="form-check-input" type="radio" name="has_children" id="kids_no{{ $listing->id }}" value="0">
                                            <label class="form-check-label" for="kids_no{{ $listing->id }}">{{ __('No') }}</label>
                                        </div>
                                    </div>
                                </div>
                                <div class="col-12" x-data="{ showPets: false }">
                                    <div class="form-check mb-2">
                                        <input class="form-check-input" type="checkbox" id="toggle_pets{{ $listing->id }}" x-model="showPets">
                                        <label class="form-check-label" for="toggle_pets{{ $listing->id }}">{{ __('I have other pets to describe') }}</label>
                                    </div>
                                    <textarea class="form-control" name="other_pets_details" rows="2" placeholder="{{ __('Describe your other pets (species, breed, age, temperament)') }}"
                                              x-show="showPets" x-transition></textarea>
                                </div>
                                <div class="col-12" x-data="{ showKids: false }">
                                    <div class="form-check mb-2">
                                        <input class="form-check-input" type="checkbox" id="toggle_kids{{ $listing->id }}" x-model="showKids">
                                        <label class="form-check-label" for="toggle_kids{{ $listing->id }}">{{ __('I have children to describe') }}</label>
                                    </div>
                                    <input type="text" class="form-control" name="children_ages" placeholder="{{ __('e.g. 3 years, 7 years') }}"
                                           x-show="showKids" x-transition>
                                </div>
                            </div>

                            <h6 class="fw-semibold mb-3"><i class="bi bi-clipboard-check me-2"></i>{{ __('Experience & Motivation') }}</h6>
                            <div class="mb-3">
                                <label for="pet_experience{{ $listing->id }}" class="form-label">{{ __('Pet Experience') }}</label>
                                <textarea class="form-control" id="pet_experience{{ $listing->id }}" name="pet_experience" rows="3"
                                          placeholder="{{ __('Have you owned pets before? Tell us about your experience...') }}"></textarea>
                            </div>
                            <div class="mb-3">
                                <label for="why_adopt{{ $listing->id }}" class="form-label fw-semibold">{{ __('Why do you want to adopt this pet?') }} <span class="text-danger">*</span></label>
                                <textarea class="form-control" id="why_adopt{{ $listing->id }}" name="why_adopt" rows="3" required
                                          placeholder="{{ __('Tell the shelter about yourself and why you would be a great match...') }}"></textarea>
                            </div>
                            <div class="mb-0">
                                <label for="message{{ $listing->id }}" class="form-label fw-semibold">{{ __('Additional Message') }} <span class="text-danger">*</span></label>
                                <textarea class="form-control" id="message{{ $listing->id }}" name="message" rows="3" required
                                          placeholder="{{ __('Anything else you would like the shelter to know...') }}"></textarea>
                            </div>

                            <div class="alert alert-info mb-0 mt-3" style="font-size:0.8rem;">
                                <i class="bi bi-info-circle me-1"></i>
                                {{ __('The shelter will review your application and contact you if approved.') }}
                            </div>
                        </div>
                        <div class="modal-footer">
                            <button type="button" class="btn btn-outline-secondary btn-sm" data-bs-dismiss="modal">{{ __('Cancel') }}</button>
                            <button type="submit" class="btn btn-primary btn-sm">
                                <i class="bi bi-send me-1"></i>{{ __('Submit Application') }}
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    @endforeach
@endsection
