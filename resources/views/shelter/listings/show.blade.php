@extends('layouts.shelter.app')

@section('content')
<div class="mb-4">
    <div class="d-flex justify-content-between align-items-center">
        <h2 class="h4 mb-0 fw-semibold">{{ $listing->pet_name }}</h2>
        <div class="d-flex gap-2">
            <a href="{{ route('shelter.listings.edit', $listing) }}" class="btn btn-outline-primary btn-sm">
                <i class="bi bi-pencil me-1"></i>{{ __('Edit') }}
            </a>
            <a href="{{ route('shelter.listings.index') }}" class="btn btn-outline-secondary btn-sm">
                <i class="bi bi-arrow-left me-1"></i>{{ __('Back') }}
            </a>
        </div>
    </div>
</div>

<div class="row">
    <div class="col-lg-8">
        {{-- Images --}}
        <div class="card mb-4">
            <div class="card-body">
                @if($listing->images->count())
                    <div class="row g-2">
                        @foreach($listing->images as $image)
                            <div class="col-4">
                                <img src="{{ asset('storage/' . $image->image_path) }}" class="img-fluid rounded" alt="{{ $listing->pet_name }}" style="height:200px;width:100%;object-fit:cover;">
                            </div>
                        @endforeach
                    </div>
                @else
                    <div class="text-center py-5">
                        <i class="bi bi-image" style="font-size:3rem;opacity:0.3;"></i>
                        <p class="text-muted mt-2">{{ __('No images') }}</p>
                    </div>
                @endif
            </div>
        </div>

        {{-- Details --}}
        <div class="card mb-4">
            <div class="card-header">
                <h6 class="mb-0 fw-semibold">{{ __('Details') }}</h6>
            </div>
            <div class="card-body">
                <div class="row g-3">
                    <div class="col-md-6">
                        <small class="text-muted">{{ __('Species') }}</small>
                        <div class="fw-semibold">{{ $listing->species->name ?? '-' }}</div>
                    </div>
                    <div class="col-md-6">
                        <small class="text-muted">{{ __('Breed') }}</small>
                        <div class="fw-semibold">{{ $listing->breed->name ?? '-' }}</div>
                    </div>
                    <div class="col-md-6">
                        <small class="text-muted">{{ __('Gender') }}</small>
                        <div class="fw-semibold">{{ $listing->gender ? ucfirst($listing->gender) : '-' }}</div>
                    </div>
                    <div class="col-md-6">
                        <small class="text-muted">{{ __('Age') }}</small>
                        <div class="fw-semibold">{{ $listing->age ?? '-' }}</div>
                    </div>
                    <div class="col-md-6">
                        <small class="text-muted">{{ __('Health Status') }}</small>
                        <div class="fw-semibold">{{ $listing->health_status ?? '-' }}</div>
                    </div>
                    <div class="col-md-6">
                        <small class="text-muted">{{ __('Status') }}</small>
                        <div>
                            @if($listing->status === 'available')
                                <span class="badge bg-success">{{ __('Available') }}</span>
                            @elseif($listing->status === 'adopted')
                                <span class="badge bg-primary">{{ __('Adopted') }}</span>
                            @elseif($listing->status === 'pending')
                                <span class="badge bg-warning text-dark">{{ __('Pending') }}</span>
                            @else
                                <span class="badge bg-secondary">{{ __('Inactive') }}</span>
                            @endif
                        </div>
                    </div>
                    <div class="col-12">
                        <small class="text-muted">{{ __('Description') }}</small>
                        <div>{{ $listing->description ?? '-' }}</div>
                    </div>
                </div>
            </div>
        </div>

        {{-- Applications --}}
        <div class="card">
            <div class="card-header">
                <h6 class="mb-0 fw-semibold">{{ __('Applications') }} ({{ $listing->applications->count() }})</h6>
            </div>
            <div class="card-body">
                @forelse($listing->applications as $app)
                    <div class="d-flex align-items-start mb-3 {{ !$loop->last ? 'pb-3 border-bottom' : '' }}">
                        <div class="rounded-circle d-flex align-items-center justify-content-center flex-shrink-0 me-3" style="width:40px;height:40px;background:linear-gradient(135deg,#1a6b3c,#2e9e5a);">
                            <span class="text-white fw-semibold" style="font-size:0.85rem;">{{ substr($app->applicant->name ?? '?', 0, 1) }}</span>
                        </div>
                        <div class="flex-grow-1">
                            <div class="d-flex justify-content-between align-items-center mb-1">
                                <strong style="font-size:0.875rem;">{{ $app->applicant->name ?? 'Anonymous' }}</strong>
                                @if($app->status === 'pending')
                                    <span class="badge bg-warning text-dark">Pending</span>
                                @elseif($app->status === 'approved')
                                    <span class="badge bg-success">Approved</span>
                                @elseif($app->status === 'rejected')
                                    <span class="badge bg-danger">Rejected</span>
                                @else
                                    <span class="badge bg-secondary">{{ ucfirst($app->status) }}</span>
                                @endif
                            </div>
                            @if($app->message)
                                <p class="text-muted mb-1" style="font-size:0.85rem;">{{ $app->message }}</p>
                            @endif
                            <small class="text-muted">{{ $app->created_at->diffForHumans() }}</small>
                            <div class="mt-1">
                                <a href="{{ route('shelter.applications.show', $app) }}" class="btn btn-sm btn-outline-primary">
                                    <i class="bi bi-eye me-1"></i>{{ __('View Details') }}
                                </a>
                            </div>
                        </div>
                    </div>
                @empty
                    <div class="text-center py-4">
                        <i class="bi bi-inbox" style="font-size:2rem;opacity:0.3;"></i>
                        <p class="mt-2 text-muted mb-0">{{ __('No applications yet.') }}</p>
                    </div>
                @endforelse
            </div>
        </div>
    </div>

    <div class="col-lg-4">
        <div class="card mb-4">
            <div class="card-header">
                <h6 class="mb-0 fw-semibold">{{ __('Quick Info') }}</h6>
            </div>
            <div class="card-body">
                <div class="d-flex justify-content-between mb-2">
                    <span class="text-muted" style="font-size:0.8rem;">{{ __('Created') }}</span>
                    <span style="font-size:0.8rem;">{{ $listing->created_at->format('M d, Y') }}</span>
                </div>
                <div class="d-flex justify-content-between mb-2">
                    <span class="text-muted" style="font-size:0.8rem;">{{ __('Applications') }}</span>
                    <span style="font-size:0.8rem;">{{ $listing->applications->count() }}</span>
                </div>
                <div class="d-flex justify-content-between mb-2">
                    <span class="text-muted" style="font-size:0.8rem;">{{ __('Images') }}</span>
                    <span style="font-size:0.8rem;">{{ $listing->images->count() }}</span>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
