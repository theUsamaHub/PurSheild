@extends('layouts.owner.app')

@section('content')
    <div class="mb-4">
        <a href="{{ route('owner.browse-adoption') }}" class="text-decoration-none text-muted" style="font-size:0.85rem;">
            <i class="bi bi-arrow-left me-1"></i>{{ __('Back to Listings') }}
        </a>
    </div>

    <div class="row g-4">
        {{-- Left: Image Gallery --}}
        <div class="col-lg-7">
            <div class="card border-0 shadow-sm overflow-hidden">
                <div class="position-relative" style="height:420px;background:#f0f7f2;" x-data="{ activeImage: 0 }">
                    @if ($listing->images->count())
                        @foreach ($listing->images as $idx => $image)
                            <img src="{{ asset('uploads/adoptions/' . $image->image_path) }}"
                                 alt="{{ $listing->pet_name }} - {{ $idx + 1 }}"
                                 class="w-100 h-100 position-absolute top-0 start-0"
                                 style="object-fit:cover;transition:opacity 0.3s;"
                                 :style="{ opacity: activeImage === {{ $idx }} ? 1 : 0 }">
                        @endforeach
                        @if ($listing->images->count() > 1)
                            <button class="btn btn-sm btn-dark position-absolute top-50 start-0 translate-middle-y ms-2 opacity-75" @click="activeImage = activeImage > 0 ? activeImage - 1 : {{ $listing->images->count() - 1 }}">
                                <i class="bi bi-chevron-left"></i>
                            </button>
                            <button class="btn btn-sm btn-dark position-absolute top-50 end-0 translate-middle-y me-2 opacity-75" @click="activeImage = activeImage < {{ $listing->images->count() - 1 }} ? activeImage + 1 : 0">
                                <i class="bi bi-chevron-right"></i>
                            </button>
                            <div class="position-absolute bottom-0 start-50 translate-middle-x mb-2 d-flex gap-1">
                                @foreach ($listing->images as $idx => $image)
                                    <button class="btn btn-sm rounded-pill p-0 border-0" style="width:10px;height:10px;"
                                            :class="activeImage === {{ $idx }} ? 'bg-primary' : 'bg-white opacity-50'"
                                            @click="activeImage = {{ $idx }}"></button>
                                @endforeach
                            </div>
                        @endif
                    @else
                        <div class="w-100 h-100 d-flex align-items-center justify-content-center">
                            <div class="text-center">
                                <i class="bi bi-heart" style="font-size:5rem;color:#1a6b3c;opacity:0.1;"></i>
                                <p class="text-muted mt-2">{{ __('No photos available') }}</p>
                            </div>
                        </div>
                    @endif
                </div>

                {{-- Thumbnail strip --}}
                @if ($listing->images->count() > 1)
                    <div class="card-body py-2 px-3 bg-light" x-data="{ activeImage: 0 }">
                        <div class="d-flex gap-2 overflow-auto">
                            @foreach ($listing->images as $idx => $image)
                                <img src="{{ asset('uploads/adoptions/' . $image->image_path) }}"
                                     alt="{{ $listing->pet_name }} thumb {{ $idx + 1 }}"
                                     class="rounded flex-shrink-0"
                                     style="width:50px;height:50px;object-fit:cover;cursor:pointer;border:2px solid {{ $idx === 0 ? '#1a6b3c' : 'transparent' }};"
                                     onclick="document.querySelectorAll('[x-data]').forEach(el => el.__x.$data.activeImage = {{ $idx }})">
                            @endforeach
                        </div>
                    </div>
                @endif
            </div>

            {{-- Description --}}
            <div class="card border-0 shadow-sm mt-4">
                <div class="card-body">
                    <h5 class="fw-semibold mb-3"><i class="bi bi-card-text me-2"></i>{{ __('About') }} {{ $listing->pet_name }}</h5>
                    <p class="text-muted mb-0" style="white-space:pre-line;">{{ $listing->description ?? 'No description provided by the shelter.' }}</p>
                </div>
            </div>
        </div>

        {{-- Right: Info & Actions --}}
        <div class="col-lg-5">
            {{-- Pet Info Card --}}
            <div class="card border-0 shadow-sm">
                <div class="card-body">
                    <div class="d-flex justify-content-between align-items-start mb-3">
                        <div>
                            <h3 class="fw-bold mb-1">{{ $listing->pet_name }}</h3>
                            <div class="text-muted">
                                {{ $listing->species?->name ?? '' }}
                                {{ $listing->breed ? ' - ' . $listing->breed->name : '' }}
                            </div>
                        </div>
                        @if ($listing->status === 'available')
                            <span class="badge bg-success"><i class="bi bi-check-circle me-1"></i>{{ __('Available') }}</span>
                        @else
                            <span class="badge bg-secondary">{{ ucfirst($listing->status) }}</span>
                        @endif
                    </div>

                    {{-- Rating --}}
                    @if ($reviewCount > 0)
                        <div class="d-flex align-items-center gap-2 mb-3">
                            <div class="text-warning">
                                @for ($i = 1; $i <= 5; $i++)
                                    @if ($i <= floor($avgRating))
                                        <i class="bi bi-star-fill"></i>
                                    @elseif ($i - $avgRating < 1)
                                        <i class="bi bi-star-half"></i>
                                    @else
                                        <i class="bi bi-star"></i>
                                    @endif
                                @endfor
                            </div>
                            <span class="text-muted" style="font-size:0.85rem;">{{ number_format($avgRating, 1) }} ({{ $reviewCount }} {{ __('reviews') }})</span>
                        </div>
                    @endif

                    <hr>

                    {{-- Quick info grid --}}
                    <div class="row g-3 mb-3">
                        <div class="col-6">
                            <div class="text-center p-2 rounded bg-light">
                                <i class="bi bi-clock text-primary fs-5 d-block mb-1"></i>
                                <small class="text-muted d-block">{{ __('Age') }}</small>
                                <strong>{{ $listing->age ?? '?' }} {{ __('months') }}</strong>
                            </div>
                        </div>
                        <div class="col-6">
                            <div class="text-center p-2 rounded bg-light">
                                <i class="bi bi-{{ $listing->gender === 'female' ? 'gender-female text-danger' : 'gender-male text-primary' }} fs-5 d-block mb-1"></i>
                                <small class="text-muted d-block">{{ __('Gender') }}</small>
                                <strong>{{ ucfirst($listing->gender ?? 'Unknown') }}</strong>
                            </div>
                        </div>
                        <div class="col-6">
                            <div class="text-center p-2 rounded bg-light">
                                <i class="bi bi-heart-pulse text-success fs-5 d-block mb-1"></i>
                                <small class="text-muted d-block">{{ __('Health') }}</small>
                                <strong>
                                    @if ($listing->health_status === 'healthy')
                                        <span class="text-success">{{ __('Healthy') }}</span>
                                    @elseif ($listing->health_status === 'needs_care')
                                        <span class="text-warning">{{ __('Needs Care') }}</span>
                                    @else
                                        {{ ucfirst($listing->health_status ?? 'Unknown') }}
                                    @endif
                                </strong>
                            </div>
                        </div>
                        <div class="col-6">
                            <div class="text-center p-2 rounded bg-light">
                                <i class="bi bi-tag text-info fs-5 d-block mb-1"></i>
                                <small class="text-muted d-block">{{ __('Status') }}</small>
                                <strong>{{ ucfirst($listing->status) }}</strong>
                            </div>
                        </div>
                    </div>

                    <hr>

                    {{-- Shelter info --}}
                    <h6 class="fw-semibold mb-3"><i class="bi bi-building me-2"></i>{{ __('Shelter') }}</h6>
                    <div class="d-flex align-items-start gap-3 mb-3">
                        <div class="rounded d-flex align-items-center justify-content-center flex-shrink-0" style="width:48px;height:48px;background:linear-gradient(135deg,#1a6b3c,#2e9e5a);">
                            <i class="bi bi-building text-white fs-6"></i>
                        </div>
                        <div>
                            <strong>{{ $listing->shelter?->name ?? 'Unknown Shelter' }}</strong>
                            @if ($listing->shelter?->shelterProfile)
                                <div class="text-muted" style="font-size:0.8rem;">
                                    @if ($listing->shelter->shelterProfile->shelter_name)
                                        {{ $listing->shelter->shelterProfile->shelter_name }}<br>
                                    @endif
                                    @if ($listing->shelter->shelterProfile->city)
                                        <i class="bi bi-geo-alt me-1"></i>{{ $listing->shelter->shelterProfile->city }}
                                    @endif
                                    @if ($listing->shelter->shelterProfile->contact_number)
                                        <br><i class="bi bi-telephone me-1"></i>{{ $listing->shelter->shelterProfile->contact_number }}
                                    @endif
                                </div>
                            @endif
                        </div>
                    </div>

                    <hr>

                    {{-- Actions --}}
                    <div class="d-grid gap-2">
                        @if ($hasApplied)
                            <button class="btn btn-secondary btn-sm" disabled>
                                <i class="bi bi-check-circle me-1"></i>{{ __('Already Applied') }}
                            </button>
                        @elseif ($listing->status !== 'available')
                            <button class="btn btn-secondary btn-sm" disabled>
                                <i class="bi bi-x-circle me-1"></i>{{ __('Not Available') }}
                            </button>
                        @else
                            <button type="button" class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#applyModal">
                                <i class="bi bi-hand-thumbs-up me-1"></i>{{ __('Apply to Adopt') }}
                            </button>
                        @endif
                        <a href="{{ route('owner.browse-adoption') }}" class="btn btn-outline-secondary btn-sm">
                            <i class="bi bi-arrow-left me-1"></i>{{ __('Browse More Pets') }}
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </div>

    {{-- Reviews Section --}}
    @if ($listing->reviews->count())
        <div class="card border-0 shadow-sm mt-4">
            <div class="card-body">
                <h5 class="fw-semibold mb-4"><i class="bi bi-chat-left-text me-2"></i>{{ __('Reviews') }} ({{ $reviewCount }})</h5>
                @foreach ($listing->reviews as $review)
                    <div class="d-flex gap-3 mb-3 pb-3 {{ !$loop->last ? 'border-bottom' : '' }}">
                        <div class="rounded-circle d-flex align-items-center justify-content-center flex-shrink-0" style="width:40px;height:40px;background:linear-gradient(135deg,#1a6b3c,#2e9e5a);color:white;font-weight:600;font-size:0.85rem;">
                            {{ strtoupper(substr($review->user->name ?? 'U', 0, 2)) }}
                        </div>
                        <div class="flex-grow-1">
                            <div class="d-flex justify-content-between align-items-center mb-1">
                                <strong style="font-size:0.9rem;">{{ $review->user->name ?? 'Anonymous' }}</strong>
                                <small class="text-muted">{{ $review->created_at->diffForHumans() }}</small>
                            </div>
                            <div class="text-warning mb-1" style="font-size:0.8rem;">
                                @for ($i = 1; $i <= 5; $i++)
                                    @if ($i <= $review->rating)
                                        <i class="bi bi-star-fill"></i>
                                    @else
                                        <i class="bi bi-star"></i>
                                    @endif
                                @endfor
                            </div>
                            @if ($review->comment)
                                <p class="text-muted mb-0" style="font-size:0.85rem;">{{ $review->comment }}</p>
                            @endif
                        </div>
                    </div>
                @endforeach
            </div>
        </div>
    @endif

    {{-- Apply Modal (outside row for proper scrolling) --}}
    @if ($listing->status === 'available' && !$hasApplied)
        <div class="modal fade" id="applyModal" tabindex="-1" aria-hidden="true">
            <div class="modal-dialog modal-dialog-scrollable">
                <div class="modal-content">
                    <form action="{{ route('owner.adoption.apply', $listing) }}" method="POST">
                        @csrf
                        <div class="modal-header">
                            <div>
                                <h5 class="modal-title fw-semibold">{{ __('Apply to Adopt') }} {{ $listing->pet_name }}</h5>
                                <small class="text-muted">{{ __('at') }} {{ $listing->shelter?->name ?? 'Unknown Shelter' }}</small>
                            </div>
                            <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                        </div>
                        <div class="modal-body">
                            <div class="alert alert-light border mb-3">
                                <div class="d-flex align-items-center gap-3">
                                    @if ($listing->images->count())
                                        <img src="{{ asset('uploads/adoptions/' . $listing->images->first()->image_path) }}"
                                             alt="{{ $listing->pet_name }}"
                                             class="rounded" style="width:60px;height:60px;object-fit:cover;">
                                    @endif
                                    <div>
                                        <strong>{{ $listing->pet_name }}</strong>
                                        <div class="text-muted" style="font-size:0.8rem;">
                                            {{ $listing->species?->name ?? '' }}
                                            {{ $listing->breed ? ' - ' . $listing->breed->name : '' }}
                                            {{ $listing->age ? ' | ' . $listing->age . ' months' : '' }}
                                            {{ ' | ' . ucfirst($listing->gender ?? 'Unknown') }}
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <h6 class="fw-semibold mb-3"><i class="bi bi-person-lines-fill me-2"></i>{{ __('Contact Information') }}</h6>
                            <div class="row g-3 mb-4">
                                <div class="col-md-6">
                                    <label for="apply_phone" class="form-label">{{ __('Phone Number') }}</label>
                                    <input type="text" class="form-control" id="apply_phone" name="phone"
                                           value="{{ old('phone', Auth::user()->phone ?? '') }}" placeholder="{{ __('Your phone number') }}">
                                </div>
                                <div class="col-md-6">
                                    <label for="apply_address" class="form-label">{{ __('Address') }}</label>
                                    <input type="text" class="form-control" id="apply_address" name="address"
                                           value="{{ old('address', Auth::user()->address ?? '') }}" placeholder="{{ __('Your address') }}">
                                </div>
                            </div>

                            <h6 class="fw-semibold mb-3"><i class="bi bi-house me-2"></i>{{ __('Your Living Situation') }}</h6>
                            <div class="row g-3 mb-4">
                                <div class="col-md-6">
                                    <label for="apply_home_type" class="form-label">{{ __('Home Type') }}</label>
                                    <select class="form-select" id="apply_home_type" name="home_type">
                                        <option value="">{{ __('Select') }}</option>
                                        <option value="house">{{ __('House') }}</option>
                                        <option value="apartment">{{ __('Apartment') }}</option>
                                        <option value="condo">{{ __('Condo') }}</option>
                                        <option value="farm">{{ __('Farm') }}</option>
                                        <option value="other">{{ __('Other') }}</option>
                                    </select>
                                </div>
                                <div class="col-md-6">
                                    <label for="apply_living" class="form-label">{{ __('Living Situation') }}</label>
                                    <select class="form-select" id="apply_living" name="living_situation">
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
                                            <input class="form-check-input" type="radio" name="has_yard" id="apply_yard_yes" value="1">
                                            <label class="form-check-label" for="apply_yard_yes">{{ __('Yes') }}</label>
                                        </div>
                                        <div class="form-check form-check-inline">
                                            <input class="form-check-input" type="radio" name="has_yard" id="apply_yard_no" value="0">
                                            <label class="form-check-label" for="apply_yard_no">{{ __('No') }}</label>
                                        </div>
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <label for="apply_work" class="form-label">{{ __('Work Schedule') }}</label>
                                    <input type="text" class="form-control" id="apply_work" name="work_schedule"
                                           placeholder="{{ __('e.g. 9-5, work from home, shift work') }}">
                                </div>
                            </div>

                            <h6 class="fw-semibold mb-3"><i class="bi bi-heart me-2"></i>{{ __('Other Pets & Family') }}</h6>
                            <div class="row g-3 mb-4">
                                <div class="col-md-6">
                                    <label class="form-label">{{ __('Do you have other pets?') }}</label>
                                    <div>
                                        <div class="form-check form-check-inline">
                                            <input class="form-check-input" type="radio" name="has_other_pets" id="apply_pets_yes" value="1">
                                            <label class="form-check-label" for="apply_pets_yes">{{ __('Yes') }}</label>
                                        </div>
                                        <div class="form-check form-check-inline">
                                            <input class="form-check-input" type="radio" name="has_other_pets" id="apply_pets_no" value="0">
                                            <label class="form-check-label" for="apply_pets_no">{{ __('No') }}</label>
                                        </div>
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <label class="form-label">{{ __('Do you have children?') }}</label>
                                    <div>
                                        <div class="form-check form-check-inline">
                                            <input class="form-check-input" type="radio" name="has_children" id="apply_kids_yes" value="1">
                                            <label class="form-check-label" for="apply_kids_yes">{{ __('Yes') }}</label>
                                        </div>
                                        <div class="form-check form-check-inline">
                                            <input class="form-check-input" type="radio" name="has_children" id="apply_kids_no" value="0">
                                            <label class="form-check-label" for="apply_kids_no">{{ __('No') }}</label>
                                        </div>
                                    </div>
                                </div>
                                <div class="col-12" x-data="{ showPets: false }">
                                    <div class="form-check mb-2">
                                        <input class="form-check-input" type="checkbox" id="apply_toggle_pets" x-model="showPets">
                                        <label class="form-check-label" for="apply_toggle_pets">{{ __('I have other pets to describe') }}</label>
                                    </div>
                                    <textarea class="form-control" name="other_pets_details" rows="2" placeholder="{{ __('Describe your other pets (species, breed, age, temperament)') }}"
                                              x-show="showPets" x-transition></textarea>
                                </div>
                                <div class="col-12" x-data="{ showKids: false }">
                                    <div class="form-check mb-2">
                                        <input class="form-check-input" type="checkbox" id="apply_toggle_kids" x-model="showKids">
                                        <label class="form-check-label" for="apply_toggle_kids">{{ __('I have children to describe') }}</label>
                                    </div>
                                    <input type="text" class="form-control" name="children_ages" placeholder="{{ __('e.g. 3 years, 7 years') }}"
                                           x-show="showKids" x-transition>
                                </div>
                            </div>

                            <h6 class="fw-semibold mb-3"><i class="bi bi-clipboard-check me-2"></i>{{ __('Experience & Motivation') }}</h6>
                            <div class="mb-3">
                                <label for="apply_experience" class="form-label">{{ __('Pet Experience') }}</label>
                                <textarea class="form-control" id="apply_experience" name="pet_experience" rows="3"
                                          placeholder="{{ __('Have you owned pets before? Tell us about your experience...') }}"></textarea>
                            </div>
                            <div class="mb-3">
                                <label for="apply_why" class="form-label fw-semibold">{{ __('Why do you want to adopt this pet?') }} <span class="text-danger">*</span></label>
                                <textarea class="form-control" id="apply_why" name="why_adopt" rows="3" required
                                          placeholder="{{ __('Tell the shelter about yourself and why you would be a great match...') }}"></textarea>
                            </div>
                            <div class="mb-0">
                                <label for="apply_message" class="form-label fw-semibold">{{ __('Additional Message') }} <span class="text-danger">*</span></label>
                                <textarea class="form-control" id="apply_message" name="message" rows="3" required
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
    @endif
@endsection
