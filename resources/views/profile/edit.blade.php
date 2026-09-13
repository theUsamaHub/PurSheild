@php
    $user = auth()->user();
    if ($user->hasRole('admin')) {
        $layout = 'layouts.app';
    } elseif ($user->hasRole('vet')) {
        $layout = 'layouts.vet.app';
    } elseif ($user->hasRole('shelter')) {
        $layout = 'layouts.shelter.app';
    } else {
        $layout = 'layouts.owner.app';
    }
    $isVet = $user->hasRole('vet');
    $isShelter = $user->hasRole('shelter');
    $isOwner = $user->hasRole('owner');
@endphp
@extends($layout)

@section('content')
    <div class="mb-4">
        <h2 class="h4 mb-0 fw-semibold">{{ __('Profile') }}</h2>
    </div>

    <div class="row g-4">
        <div class="col-lg-8">
            <div class="card mb-4">
                <div class="card-body p-4">
                    @include('profile.partials.update-profile-information-form')
                </div>
            </div>

            <div class="card mb-4">
                <div class="card-body p-4">
                    @include('profile.partials.update-password-form')
                </div>
            </div>

            <div class="card border-danger">
                <div class="card-body p-4">
                    @include('profile.partials.delete-user-form')
                </div>
            </div>
        </div>

        <div class="col-lg-4">
            <div class="card mb-4">
                <div class="card-body p-4 text-center">
                    @if ($user->profile_image)
                        <img src="{{ asset('storage/' . $user->profile_image) }}" alt="{{ $user->name }}"
                            class="rounded-circle mb-3" style="width:80px;height:80px;object-fit:cover;">
                    @else
                        <div class="rounded-circle d-inline-flex align-items-center justify-content-center mb-3"
                            style="width:80px;height:80px;background:linear-gradient(135deg,#1a6b3c,#2e9e5a);">
                            <span class="text-white fw-bold fs-3">{{ substr($user->name, 0, 1) }}</span>
                        </div>
                    @endif
                    <h6 class="fw-semibold mb-1">{{ $user->name }}</h6>
                    <div class="text-muted" style="font-size:0.85rem;">{{ $user->email }}</div>

                    @if ($user->phone)
                        <div class="text-muted mt-1" style="font-size:0.85rem;">
                            <i class="bi bi-telephone me-1"></i>{{ $user->phone }}
                        </div>
                    @endif
                    @if ($user->address)
                        <div class="text-muted mt-1" style="font-size:0.85rem;">
                            <i class="bi bi-geo-alt me-1"></i>{{ $user->address }}
                        </div>
                    @endif

                    <div class="mt-2">
                        @if ($isVet)
                            <span class="badge" style="background:#1a6b3c;">{{ __('Veterinarian') }}</span>
                        @elseif ($isShelter)
                            <span class="badge" style="background:#0ea5e9;">{{ __('Shelter') }}</span>
                        @elseif ($isOwner)
                            <span class="badge" style="background:#f59e0b;">{{ __('Pet Owner') }}</span>
                        @else
                            <span class="badge bg-dark">{{ __('Admin') }}</span>
                        @endif
                    </div>

                    <hr>

                    {{-- Vet-specific sidebar info --}}
                    @if ($isVet && $vetProfile)
                        @if ($vetProfile->clinic_name)
                            <div class="text-start mb-2" style="font-size:0.85rem;">
                                <i class="bi bi-hospital me-1 text-muted"></i>
                                <span class="fw-semibold">{{ $vetProfile->clinic_name }}</span>
                            </div>
                        @endif
                        @if ($vetProfile->qualification)
                            <div class="text-start mb-2" style="font-size:0.85rem;">
                                <i class="bi bi-mortarboard me-1 text-muted"></i>
                                {{ $vetProfile->qualification }}
                            </div>
                        @endif
                        @if ($vetProfile->experience_years)
                            <div class="text-start mb-2" style="font-size:0.85rem;">
                                <i class="bi bi-briefcase me-1 text-muted"></i>
                                {{ $vetProfile->experience_years }} {{ __('years experience') }}
                            </div>
                        @endif
                        @if ($vetProfile->consultation_fee)
                            <div class="text-start mb-2" style="font-size:0.85rem;">
                                <i class="bi bi-cash me-1 text-muted"></i>
                                ${{ number_format($vetProfile->consultation_fee, 2) }} {{ __('/ consultation') }}
                            </div>
                        @endif
                        @if ($user->specializations->count())
                            <div class="text-start mb-2" style="font-size:0.85rem;">
                                <i class="bi bi-tags me-1 text-muted"></i>
                                @foreach ($user->specializations as $spec)
                                    <span class="badge bg-light text-dark">{{ $spec->name }}</span>
                                @endforeach
                            </div>
                        @endif
                        <hr>
                    @endif

                    {{-- Shelter-specific sidebar info --}}
                    @if ($isShelter && $shelterProfile)
                        @if ($shelterProfile->shelter_name)
                            <div class="text-start mb-2" style="font-size:0.85rem;">
                                <i class="bi bi-house-heart me-1 text-muted"></i>
                                <span class="fw-semibold">{{ $shelterProfile->shelter_name }}</span>
                            </div>
                        @endif
                        @if ($shelterProfile->city)
                            <div class="text-start mb-2" style="font-size:0.85rem;">
                                <i class="bi bi-geo-alt me-1 text-muted"></i>
                                {{ $shelterProfile->city }}
                            </div>
                        @endif
                        @if ($shelterProfile->capacity)
                            <div class="text-start mb-2" style="font-size:0.85rem;">
                                <i class="bi bi-grid me-1 text-muted"></i>
                                {{ __('Capacity:') }} {{ $shelterProfile->capacity }} {{ __('animals') }}
                            </div>
                        @endif
                        @if ($shelterProfile->website)
                            <div class="text-start mb-2" style="font-size:0.85rem;">
                                <i class="bi bi-globe me-1 text-muted"></i>
                                <a href="{{ $shelterProfile->website }}" target="_blank" class="text-decoration-none">{{ __('Visit Website') }}</a>
                            </div>
                        @endif
                        <hr>
                    @endif

                    <small class="text-muted">
                        {{ __('Member since') }} {{ $user->created_at->format('M Y') }}
                    </small>
                </div>
            </div>
        </div>
    </div>
@endsection
