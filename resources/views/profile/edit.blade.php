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
<div class="pc-profile-page">
    <div class="pc-profile-heading mb-4">
        <h2 class="mb-1">{{ __('Profile') }}</h2>
        <p class="mb-0">{{ __('Manage your personal information, account security and profile details.') }}</p>
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
                        <span class="pc-role-badge pc-role-vet">{{ __('Veterinarian') }}</span>
                    @elseif ($isShelter)
                        <span class="pc-role-badge pc-role-shelter">{{ __('Shelter') }}</span>
                    @elseif ($isOwner)
                        <span class="pc-role-badge pc-role-owner">{{ __('Pet Owner') }}</span>
                    @else
                        <span class="pc-role-badge pc-role-admin">{{ __('Admin') }}</span>
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

@push('styles')
<style>
    .pc-profile-page{
        width:100%;
        max-width:100%;
        overflow-x:hidden;
    }

    .pc-profile-heading h2{
        font-size:1.65rem;
        font-weight:700;
        color:#111827;
        line-height:1.2;
    }

    .pc-profile-heading p{
        color:#6b7280;
        font-size:.9rem;
    }

    .pc-profile-layout{
        display:grid;
        grid-template-columns:minmax(0, 1fr) 300px;
        gap:1.25rem;
        align-items:start;
        width:100%;
        min-width:0;
    }

    .pc-profile-main,
    .pc-profile-side{
        min-width:0;
    }

    .pc-profile-card,
    .pc-profile-summary-card{
        background:#fff;
        border:1px solid #e2e8f0;
        border-radius:14px;
        box-shadow:0 1px 2px rgba(15,23,42,.02);
    }

    .pc-profile-card{
        overflow:hidden;
    }

    .pc-profile-card-body{
        padding:1.5rem;
        min-width:0;
    }

    .pc-profile-danger-card{
        border-color:#fecaca;
    }

    .pc-profile-summary-card{
        padding:1.5rem;
        text-align:center;
        position:sticky;
        top:88px;
    }

    .pc-profile-avatar-wrap{
        display:flex;
        justify-content:center;
        margin-bottom:.9rem;
    }

    .pc-profile-avatar{
        width:88px;
        height:88px;
        border-radius:50%;
        object-fit:cover;
        display:flex;
        align-items:center;
        justify-content:center;
        flex-shrink:0;
    }

    .pc-profile-avatar-fallback{
        color:#fff;
        font-size:1.75rem;
        font-weight:700;
        background:linear-gradient(135deg,#1a6b3c,#2e9e5a);
    }

    .pc-profile-name{
        margin:0 0 .3rem;
        font-size:1.05rem;
        font-weight:700;
        color:#111827;
    }

    .pc-profile-email{
        color:#6b7280;
        font-size:.82rem;
        word-break:break-word;
    }

    .pc-role-badge{
        display:inline-flex;
        align-items:center;
        padding:.35rem .7rem;
        border-radius:999px;
        font-size:.75rem;
        font-weight:600;
    }

    .pc-role-vet{ background:#dcfce7;color:#15803d; }
    .pc-role-shelter{ background:#e0f2fe;color:#0369a1; }
    .pc-role-owner{ background:#fef3c7;color:#b45309; }
    .pc-role-admin{ background:#e5e7eb;color:#111827; }

    .pc-profile-divider{
        height:1px;
        background:#eef2f7;
        margin:1.25rem 0;
    }

    .pc-profile-meta{
        display:flex;
        flex-direction:column;
        gap:.8rem;
        text-align:left;
    }

    .pc-profile-meta-row{
        display:grid;
        grid-template-columns:20px minmax(0,1fr);
        gap:.65rem;
        align-items:start;
        color:#475569;
        font-size:.84rem;
        line-height:1.45;
    }

    .pc-profile-meta-row i{
        color:#1a6b3c;
        margin-top:2px;
    }

    .pc-profile-meta-row span{
        min-width:0;
        overflow-wrap:anywhere;
    }

    .pc-specializations{
        text-align:left;
    }

    .pc-specializations-title{
        font-size:.82rem;
        font-weight:600;
        color:#334155;
    }

    .pc-specialization-badge{
        background:#f1f5f9;
        color:#334155;
        padding:.3rem .55rem;
        border-radius:6px;
        font-size:.72rem;
        font-weight:500;
    }

    .pc-profile-website{
        display:flex;
        align-items:center;
        justify-content:center;
        gap:.45rem;
        text-decoration:none;
        color:#1a6b3c;
        font-size:.84rem;
        font-weight:600;
    }

    .pc-member-since{
        color:#94a3b8;
        font-size:.76rem;
        display:flex;
        align-items:center;
        justify-content:center;
        gap:.4rem;
    }

    /* Keep every included profile form inside the available card width. */
    .pc-profile-main form,
    .pc-profile-main form > div,
    .pc-profile-main .row,
    .pc-profile-main [class*="col-"]{
        min-width:0;
        max-width:100%;
    }

    .pc-profile-main input:not([type="checkbox"]):not([type="radio"]),
    .pc-profile-main select,
    .pc-profile-main textarea{
        width:100%;
        max-width:100% !important;
        box-sizing:border-box;
    }

    .pc-profile-main img{
        max-width:100%;
    }

    /* Prevent fixed-width children inside the existing partial from pushing the page horizontally. */
    .pc-profile-main .d-flex{
        min-width:0;
    }

    .pc-profile-main .d-flex > *{
        min-width:0;
    }

    @media (max-width:1199.98px){
        .pc-profile-layout{
            grid-template-columns:1fr;
        }

        .pc-profile-side{
            order:-1;
        }

        .pc-profile-summary-card{
            position:static;
        }

        .pc-profile-main form .row > [class*="col-"]{
            flex:0 0 100%;
            width:100%;
        }
    }

    @media (max-width:767.98px){
        .pc-profile-heading h2{
            font-size:1.4rem;
        }

        .pc-profile-card-body,
        .pc-profile-summary-card{
            padding:1.1rem;
        }
    }
</style>
@endpush
