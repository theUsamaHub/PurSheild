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
<link rel="stylesheet" href="{{ asset('css/profile.css') }}">
<div class="pc-profile-page">
    <div class="pc-profile-heading mb-4">
        <h2>{{ __('Profile') }}</h2>
        <p>{{ __('Manage your personal information, account security and profile details.') }}</p>
    </div>

    <div class="pc-profile-layout">
        <div class="pc-profile-main">
            <div class="pc-profile-card mb-4">
                <div class="pc-profile-card-body">
                    @include('profile.partials.update-profile-information-form')
                </div>
            </div>

            <div class="pc-profile-card mb-4">
                <div class="pc-profile-card-body">
                    @include('profile.partials.update-password-form')
                </div>
            </div>

            <div class="pc-profile-card pc-profile-danger-card">
                <div class="pc-profile-card-body">
                    @include('profile.partials.delete-user-form')
                </div>
            </div>
        </div>

        <aside class="pc-profile-side">
            <div class="pc-profile-summary-card">
                <div class="pc-profile-avatar-wrap">
                    @if ($user->profile_image)
                        <img
                            src="{{ asset('storage/' . $user->profile_image) }}"
                            alt="{{ $user->name }}"
                            class="pc-profile-avatar"
                        >
                    @else
                        <div class="pc-profile-avatar pc-profile-avatar-fallback">
                            {{ strtoupper(substr($user->name, 0, 1)) }}
                        </div>
                    @endif
                </div>

                <h5 class="pc-profile-name">{{ $user->name }}</h5>
                <div class="pc-profile-email">{{ $user->email }}</div>

                <div class="mt-3">
                    @if ($isVet)
                        <span class="pc-role-badge pc-role-vet"><i class="bi bi-heart-pulse"></i> {{ __('Veterinarian') }}</span>
                    @elseif ($isShelter)
                        <span class="pc-role-badge pc-role-shelter"><i class="bi bi-house-heart"></i> {{ __('Shelter') }}</span>
                    @elseif ($isOwner)
                        <span class="pc-role-badge pc-role-owner"><i class="bi bi-heart"></i> {{ __('Pet Owner') }}</span>
                    @else
                        <span class="pc-role-badge pc-role-admin"><i class="bi bi-shield-check"></i> {{ __('Admin') }}</span>
                    @endif
                </div>

                <div class="pc-profile-divider"></div>

                <div class="pc-profile-meta">
                    @if ($user->phone)
                        <div class="pc-profile-meta-row">
                            <i class="bi bi-telephone"></i>
                            <span>{{ $user->phone }}</span>
                        </div>
                    @endif

                    @if ($user->address)
                        <div class="pc-profile-meta-row">
                            <i class="bi bi-geo-alt"></i>
                            <span>{{ $user->address }}</span>
                        </div>
                    @endif

                    @if ($isVet && $vetProfile)
                        @if ($vetProfile->clinic_name)
                            <div class="pc-profile-meta-row">
                                <i class="bi bi-hospital"></i>
                                <span>{{ $vetProfile->clinic_name }}</span>
                            </div>
                        @endif

                        @if ($vetProfile->qualification)
                            <div class="pc-profile-meta-row">
                                <i class="bi bi-mortarboard"></i>
                                <span>{{ $vetProfile->qualification }}</span>
                            </div>
                        @endif

                        @if ($vetProfile->experience_years)
                            <div class="pc-profile-meta-row">
                                <i class="bi bi-briefcase"></i>
                                <span>{{ $vetProfile->experience_years }} {{ __('years experience') }}</span>
                            </div>
                        @endif

                        @if ($vetProfile->consultation_fee)
                            <div class="pc-profile-meta-row">
                                <i class="bi bi-cash"></i>
                                <span>${{ number_format($vetProfile->consultation_fee, 2) }} {{ __('/ consultation') }}</span>
                            </div>
                        @endif
                    @endif

                    @if ($isShelter && $shelterProfile)
                        @if ($shelterProfile->shelter_name)
                            <div class="pc-profile-meta-row">
                                <i class="bi bi-house-heart"></i>
                                <span>{{ $shelterProfile->shelter_name }}</span>
                            </div>
                        @endif

                        @if ($shelterProfile->city)
                            <div class="pc-profile-meta-row">
                                <i class="bi bi-geo-alt"></i>
                                <span>{{ $shelterProfile->city }}</span>
                            </div>
                        @endif

                        @if ($shelterProfile->capacity)
                            <div class="pc-profile-meta-row">
                                <i class="bi bi-grid"></i>
                                <span>{{ __('Capacity:') }} {{ $shelterProfile->capacity }} {{ __('animals') }}</span>
                            </div>
                        @endif
                    @endif
                </div>

                @if ($isVet && $user->specializations->count())
                    <div class="pc-profile-divider"></div>
                    <div class="pc-specializations">
                        <div class="pc-specializations-title">
                            <i class="bi bi-tags"></i> {{ __('Specializations') }}
                        </div>
                        <div class="d-flex flex-wrap gap-2 mt-2">
                            @foreach ($user->specializations as $spec)
                                <span class="pc-specialization-badge">{{ $spec->name }}</span>
                            @endforeach
                        </div>
                    </div>
                @endif

                @if ($isShelter && $shelterProfile && $shelterProfile->website)
                    <div class="pc-profile-divider"></div>
                    <a href="{{ $shelterProfile->website }}" target="_blank" class="pc-profile-website">
                        <i class="bi bi-globe"></i>
                        {{ __('Visit Website') }}
                    </a>
                @endif

                <div class="pc-profile-divider"></div>
                <div class="pc-member-since">
                    <i class="bi bi-calendar3"></i>
                    {{ __('Member since') }} {{ $user->created_at->format('M Y') }}
                </div>
            </div>
        </aside>
    </div>
</div>
@endsection
