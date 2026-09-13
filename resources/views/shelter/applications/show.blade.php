@extends('layouts.shelter.app')

@section('content')
<div class="mb-4">
    <div class="d-flex justify-content-between align-items-center">
        <h2 class="h4 mb-0 fw-semibold">{{ __('Application Details') }}</h2>
        <a href="{{ route('shelter.applications.index') }}" class="btn btn-outline-secondary btn-sm">
            <i class="bi bi-arrow-left me-1"></i>{{ __('Back') }}
        </a>
    </div>
</div>

<div class="row">
    <div class="col-lg-8">
        <div class="card mb-4">
            <div class="card-header d-flex justify-content-between align-items-center">
                <h6 class="mb-0 fw-semibold">{{ __('Applicant Info') }}</h6>
                @if($application->status === 'pending')
                    <span class="badge bg-warning text-dark">Pending</span>
                @elseif($application->status === 'approved')
                    <span class="badge bg-success">Approved</span>
                @elseif($application->status === 'rejected')
                    <span class="badge bg-danger">Rejected</span>
                @else
                    <span class="badge bg-secondary">{{ ucfirst($application->status) }}</span>
                @endif
            </div>
            <div class="card-body">
                <div class="d-flex align-items-center mb-3">
                    <div class="rounded-circle d-flex align-items-center justify-content-center me-3" style="width:56px;height:56px;background:linear-gradient(135deg,#1a6b3c,#2e9e5a);">
                        <span class="text-white fw-bold fs-5">{{ substr($application->applicant->name ?? '?', 0, 1) }}</span>
                    </div>
                    <div>
                        <h5 class="mb-0 fw-bold">{{ $application->applicant->name ?? 'Anonymous' }}</h5>
                        <small class="text-muted">{{ $application->applicant->email ?? '' }}</small>
                    </div>
                </div>

                @if($application->phone)
                    <div class="mb-2"><small class="text-muted">{{ __('Phone:') }}</small> {{ $application->phone }}</div>
                @endif
                @if($application->address)
                    <div class="mb-2"><small class="text-muted">{{ __('Address:') }}</small> {{ $application->address }}</div>
                @endif

                <hr>

                <div class="row g-3 mb-3">
                    @if($application->home_type)
                        <div class="col-md-6">
                            <small class="text-muted">{{ __('Home Type') }}</small>
                            <div class="fw-semibold">{{ ucfirst(str_replace('_', ' ', $application->home_type)) }}</div>
                        </div>
                    @endif
                    @if($application->living_situation)
                        <div class="col-md-6">
                            <small class="text-muted">{{ __('Living Situation') }}</small>
                            <div class="fw-semibold">{{ ucfirst(str_replace('_', ' ', $application->living_situation)) }}</div>
                        </div>
                    @endif
                    @if($application->has_yard !== null)
                        <div class="col-md-6">
                            <small class="text-muted">{{ __('Has Yard') }}</small>
                            <div class="fw-semibold">{{ $application->has_yard ? __('Yes') : __('No') }}</div>
                        </div>
                    @endif
                    @if($application->work_schedule)
                        <div class="col-md-6">
                            <small class="text-muted">{{ __('Work Schedule') }}</small>
                            <div class="fw-semibold">{{ $application->work_schedule }}</div>
                        </div>
                    @endif
                    @if($application->has_other_pets !== null)
                        <div class="col-md-6">
                            <small class="text-muted">{{ __('Other Pets') }}</small>
                            <div class="fw-semibold">{{ $application->has_other_pets ? __('Yes') : __('No') }}</div>
                        </div>
                    @endif
                    @if($application->has_children !== null)
                        <div class="col-md-6">
                            <small class="text-muted">{{ __('Children') }}</small>
                            <div class="fw-semibold">{{ $application->has_children ? __('Yes') : __('No') }}</div>
                        </div>
                    @endif
                </div>

                @if($application->other_pets_details)
                    <div class="mb-3">
                        <small class="text-muted">{{ __('Other Pets Details') }}</small>
                        <div>{{ $application->other_pets_details }}</div>
                    </div>
                @endif

                @if($application->children_ages)
                    <div class="mb-3">
                        <small class="text-muted">{{ __('Children Ages') }}</small>
                        <div>{{ $application->children_ages }}</div>
                    </div>
                @endif

                @if($application->pet_experience)
                    <div class="mb-3">
                        <small class="text-muted">{{ __('Pet Experience') }}</small>
                        <div>{{ $application->pet_experience }}</div>
                    </div>
                @endif

                @if($application->why_adopt)
                    <div class="mb-3">
                        <small class="text-muted">{{ __('Why They Want to Adopt') }}</small>
                        <div>{{ $application->why_adopt }}</div>
                    </div>
                @endif

                @if($application->message)
                    <div class="mb-3">
                        <small class="text-muted">{{ __('Message') }}</small>
                        <div>{{ $application->message }}</div>
                    </div>
                @endif

                <small class="text-muted">Applied {{ $application->created_at->diffForHumans() }}</small>
            </div>
        </div>

        @if($application->shelter_response)
            <div class="card mb-4">
                <div class="card-header">
                    <h6 class="mb-0 fw-semibold">{{ __('Your Response') }}</h6>
                </div>
                <div class="card-body">
                    <p class="mb-0">{{ $application->shelter_response }}</p>
                </div>
            </div>
        @endif
    </div>

    <div class="col-lg-4">
        <div class="card mb-4">
            <div class="card-header">
                <h6 class="mb-0 fw-semibold">{{ __('Listing Info') }}</h6>
            </div>
            <div class="card-body">
                @if($application->listing)
                    <div class="d-flex justify-content-between align-items-start mb-2">
                        <div>
                            <div class="fw-bold">{{ $application->listing->pet_name }}</div>
                            <div class="text-muted" style="font-size:0.8rem;">{{ $application->listing->species->name ?? '-' }} &middot; {{ $application->listing->breed->name ?? '-' }}</div>
                        </div>
                        <span class="badge {{ $application->listing->status === 'available' ? 'bg-success' : ($application->listing->status === 'adopted' ? 'bg-primary' : 'bg-secondary') }}">{{ ucfirst($application->listing->status) }}</span>
                    </div>
                    <a href="{{ route('shelter.listings.show', $application->listing) }}" class="btn btn-sm btn-outline-primary w-100">
                        <i class="bi bi-eye me-1"></i>{{ __('View Listing') }}
                    </a>
                @endif
            </div>
        </div>

        @if($application->status === 'approved' && $application->listing && $application->listing->status !== 'adopted')
            <div class="card mb-4 border-success">
                <div class="card-header bg-success bg-opacity-10">
                    <h6 class="mb-0 fw-semibold text-success"><i class="bi bi-check-circle me-1"></i>{{ __('Finalize Adoption') }}</h6>
                </div>
                <div class="card-body">
                    <p class="text-muted mb-3" style="font-size:0.875rem;">{{ __('Mark this adoption as finalized. This will set the listing as "adopted" and reject any other pending applications.') }}</p>
                    <form action="{{ route('shelter.applications.finalize', $application) }}" method="POST" onsubmit="return confirm('{{ __('Are you sure you want to finalize this adoption? This action cannot be undone.') }}')">
                        @csrf
                        <button type="submit" class="btn btn-success w-100">
                            <i class="bi bi-check-circle me-1"></i>{{ __('Mark as Adopted') }}
                        </button>
                    </form>
                </div>
            </div>
        @endif

        @if($application->status === 'pending')
            <div class="card">
                <div class="card-header">
                    <h6 class="mb-0 fw-semibold">{{ __('Take Action') }}</h6>
                </div>
                <div class="card-body">
                    <form action="{{ route('shelter.applications.updateStatus', $application) }}" method="POST">
                        @csrf
                        @method('PUT')
                        <div class="mb-3">
                            <label class="form-label fw-semibold">{{ __('Response (optional)') }}</label>
                            <textarea class="form-control" name="shelter_response" rows="3" placeholder="{{ __('Why are you approving/rejecting?') }}">{{ old('shelter_response') }}</textarea>
                        </div>
                        <div class="d-flex gap-2">
                            <button type="submit" name="status" value="approved" class="btn btn-success flex-grow-1">
                                <i class="bi bi-check-circle me-1"></i>{{ __('Approve') }}
                            </button>
                            <button type="submit" name="status" value="rejected" class="btn btn-danger flex-grow-1">
                                <i class="bi bi-x-circle me-1"></i>{{ __('Reject') }}
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        @endif
    </div>
</div>
@endsection
