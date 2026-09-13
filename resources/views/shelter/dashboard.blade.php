@extends('layouts.shelter.app')

@section('content')
<div class="fade-in">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h4 class="mb-1 fw-bold">{{ __('Shelter Dashboard') }}</h4>
            <p class="text-muted mb-0" style="font-size:0.875rem;">{{ __('Welcome back, ') }}{{ Auth::user()->name }}!</p>
        </div>
    </div>

    @if(Auth::user()->status === 'pending_verification')
        <div class="alert alert-warning d-flex align-items-center" role="alert">
            <i class="bi bi-clock-history me-2 fs-5"></i>
            <div>
                <strong>{{ __('Pending Verification') }}</strong> - {{ __('Your account is being reviewed by admin. You can manage your listings but they won\'t appear in public until verified.') }}
            </div>
        </div>
    @endif

    {{-- Stats --}}
    <div class="row g-3 mb-4">
        <div class="col-md-3">
            <div class="card border-0" style="border-left:4px solid var(--fs-primary);">
                <div class="card-body">
                    <div class="d-flex align-items-center">
                        <div class="rounded d-flex align-items-center justify-content-center" style="width:48px;height:48px;background:var(--fs-primary-bg);">
                            <i class="bi bi-bookmark-heart text-primary fs-5"></i>
                        </div>
                        <div class="ms-3">
                            <div class="text-muted" style="font-size:0.75rem;">{{ __('Total Listings') }}</div>
                            <div class="fw-bold fs-4" style="color:var(--fs-text-heading);">{{ $totalListings }}</div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card border-0" style="border-left:4px solid var(--fs-success);">
                <div class="card-body">
                    <div class="d-flex align-items-center">
                        <div class="rounded d-flex align-items-center justify-content-center" style="width:48px;height:48px;background:#dcfce7;">
                            <i class="bi bi-check-circle text-success fs-5"></i>
                        </div>
                        <div class="ms-3">
                            <div class="text-muted" style="font-size:0.75rem;">{{ __('Available') }}</div>
                            <div class="fw-bold fs-4" style="color:var(--fs-text-heading);">{{ $availableListings }}</div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card border-0" style="border-left:4px solid var(--fs-info);">
                <div class="card-body">
                    <div class="d-flex align-items-center">
                        <div class="rounded d-flex align-items-center justify-content-center" style="width:48px;height:48px;background:#e0f2fe;">
                            <i class="bi bi-envelope text-info fs-5"></i>
                        </div>
                        <div class="ms-3">
                            <div class="text-muted" style="font-size:0.75rem;">{{ __('Pending Applications') }}</div>
                            <div class="fw-bold fs-4" style="color:var(--fs-text-heading);">{{ $pendingApplications }}</div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card border-0" style="border-left:4px solid var(--fs-accent);">
                <div class="card-body">
                    <div class="d-flex align-items-center">
                        <div class="rounded d-flex align-items-center justify-content-center" style="width:48px;height:48px;background:#fef3c7;">
                            <i class="bi bi-star text-warning fs-5"></i>
                        </div>
                        <div class="ms-3">
                            <div class="text-muted" style="font-size:0.75rem;">{{ __('Avg Rating') }}</div>
                            <div class="fw-bold fs-4" style="color:var(--fs-text-heading);">{{ $avgRating ? number_format($avgRating, 1) : '-' }}</div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="row g-4">
        {{-- Recent Applications --}}
        <div class="col-lg-7">
            <div class="card">
                <div class="card-header d-flex justify-content-between align-items-center">
                    <h6 class="mb-0 fw-semibold">{{ __('Recent Applications') }}</h6>
                    <a href="{{ route('shelter.applications.index') }}" class="btn btn-sm btn-outline-primary">{{ __('View All') }}</a>
                </div>
                <div class="card-body p-0">
                    @if($recentApplications->count())
                        <div class="table-responsive">
                            <table class="table table-hover mb-0">
                                <thead class="table-light">
                                    <tr>
                                        <th style="font-size:0.8rem;">{{ __('Applicant') }}</th>
                                        <th style="font-size:0.8rem;">{{ __('Pet') }}</th>
                                        <th style="font-size:0.8rem;">{{ __('Date') }}</th>
                                        <th style="font-size:0.8rem;">{{ __('Status') }}</th>
                                        <th style="font-size:0.8rem;"></th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach($recentApplications as $app)
                                        <tr>
                                            <td style="font-size:0.85rem;">{{ $app->applicant->name ?? '-' }}</td>
                                            <td style="font-size:0.85rem;">{{ $app->listing->pet_name ?? '-' }}</td>
                                            <td style="font-size:0.85rem;">{{ $app->created_at->format('M d, Y') }}</td>
                                            <td>
                                                @if($app->status === 'pending')
                                                    <span class="badge bg-warning text-dark">{{ __('Pending') }}</span>
                                                @elseif($app->status === 'approved')
                                                    <span class="badge bg-success">{{ __('Approved') }}</span>
                                                @elseif($app->status === 'rejected')
                                                    <span class="badge bg-danger">{{ __('Rejected') }}</span>
                                                @else
                                                    <span class="badge bg-secondary">{{ ucfirst($app->status) }}</span>
                                                @endif
                                            </td>
                                            <td>
                                                <a href="{{ route('shelter.applications.show', $app) }}" class="btn btn-sm btn-outline-primary">
                                                    <i class="bi bi-eye"></i>
                                                </a>
                                            </td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                    @else
                        <div class="empty-state">
                            <i class="bi bi-inbox d-block"></i>
                            <p>{{ __('No applications yet.') }}</p>
                        </div>
                    @endif
                </div>
            </div>
        </div>

        {{-- Right Column --}}
        <div class="col-lg-5">
            {{-- Verification Status --}}
            <div class="card mb-4">
                <div class="card-header">
                    <h6 class="mb-0 fw-semibold">{{ __('Shelter Status') }}</h6>
                </div>
                <div class="card-body">
                    <div class="d-flex align-items-center mb-3">
                        <div class="rounded-circle d-flex align-items-center justify-content-center" style="width:48px;height:48px;background:linear-gradient(135deg,#1a6b3c,#2e9e5a);">
                            <span class="text-white fw-semibold" style="font-size:0.875rem;">{{ substr(Auth::user()->name, 0, 1) }}</span>
                        </div>
                        <div class="ms-3">
                            <div class="fw-semibold">{{ Auth::user()->name }}</div>
                            <div class="text-muted" style="font-size:0.8rem;">{{ __('Animal Shelter') }}</div>
                        </div>
                    </div>
                    <hr>
                    <div class="d-flex justify-content-between mb-2">
                        <span class="text-muted" style="font-size:0.8rem;">{{ __('Status') }}</span>
                        @if(Auth::user()->status === 'active')
                            <span class="badge bg-success"><i class="bi bi-check-circle me-1"></i>{{ __('Verified') }}</span>
                        @else
                            <span class="badge bg-warning text-dark"><i class="bi bi-clock me-1"></i>{{ __('Pending') }}</span>
                        @endif
                    </div>
                    <div class="d-flex justify-content-between mb-2">
                        <span class="text-muted" style="font-size:0.8rem;">{{ __('Active Listings') }}</span>
                        <span style="font-size:0.8rem;">{{ $availableListings }}</span>
                    </div>
                    <div class="d-flex justify-content-between mb-2">
                        <span class="text-muted" style="font-size:0.8rem;">{{ __('Adoptions Completed') }}</span>
                        <span style="font-size:0.8rem;">{{ $adoptedListings }}</span>
                    </div>
                    <a href="{{ route('profile.edit') }}" class="btn btn-sm btn-outline-primary mt-2 w-100">
                        <i class="bi bi-pencil me-1"></i>{{ __('Edit Profile') }}
                    </a>
                </div>
            </div>

            {{-- Recent Listings --}}
            <div class="card">
                <div class="card-header d-flex justify-content-between align-items-center">
                    <h6 class="mb-0 fw-semibold">{{ __('Recent Listings') }}</h6>
                    <a href="{{ route('shelter.listings.index') }}" class="btn btn-sm btn-outline-primary">{{ __('View All') }}</a>
                </div>
                <div class="card-body p-0">
                    @if($recentListings->count())
                        @foreach($recentListings as $list)
                            <div class="px-3 py-2 {{ !$loop->last ? 'border-bottom' : '' }}">
                                <div class="d-flex justify-content-between align-items-center">
                                    <div>
                                        <div class="fw-semibold" style="font-size:0.875rem;">{{ $list->pet_name }}</div>
                                        <small class="text-muted">{{ $list->species->name ?? '-' }} &middot; {{ $list->applications_count }} {{ __('applications') }}</small>
                                    </div>
                                    <span class="badge {{ $list->status === 'available' ? 'bg-success' : ($list->status === 'adopted' ? 'bg-primary' : 'bg-secondary') }}">{{ ucfirst($list->status) }}</span>
                                </div>
                            </div>
                        @endforeach
                    @else
                        <div class="empty-state py-3">
                            <p class="mb-0">{{ __('No listings yet.') }}</p>
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
