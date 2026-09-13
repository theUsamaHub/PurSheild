@extends('layouts.shelter.app')

@section('content')
<div class="mb-4">
    <div class="d-flex justify-content-between align-items-center">
        <h2 class="h4 mb-0 fw-semibold">{{ __('My Listings') }}</h2>
        <a href="{{ route('shelter.listings.create') }}" class="btn btn-primary">
            <i class="bi bi-plus me-1"></i>{{ __('New Listing') }}
        </a>
    </div>
</div>

@if(Auth::user()->status !== 'active')
    <div class="alert alert-warning d-flex align-items-center mb-4" role="alert">
        <i class="bi bi-exclamation-triangle me-2 fs-5"></i>
        <div>
            <strong>{{ __('Listings not publicly visible') }}</strong> — {{ __('Your account is pending verification. Your listings will not appear in public adoption pages until an admin verifies your account.') }}
        </div>
    </div>
@endif

{{-- Stats --}}
<div class="row g-3 mb-4">
    <div class="col-md-3">
        <div class="card border-0" style="border-left:4px solid var(--fs-primary);">
            <div class="card-body py-2">
                <div class="text-muted" style="font-size:0.75rem;">{{ __('Total') }}</div>
                <div class="fw-bold fs-5">{{ $stats['total'] }}</div>
            </div>
        </div>
    </div>
    <div class="col-md-3">
        <div class="card border-0" style="border-left:4px solid var(--fs-success);">
            <div class="card-body py-2">
                <div class="text-muted" style="font-size:0.75rem;">{{ __('Available') }}</div>
                <div class="fw-bold fs-5">{{ $stats['available'] }}</div>
            </div>
        </div>
    </div>
    <div class="col-md-3">
        <div class="card border-0" style="border-left:4px solid var(--fs-info);">
            <div class="card-body py-2">
                <div class="text-muted" style="font-size:0.75rem;">{{ __('Pending') }}</div>
                <div class="fw-bold fs-5">{{ $stats['pending'] }}</div>
            </div>
        </div>
    </div>
    <div class="col-md-3">
        <div class="card border-0" style="border-left:4px solid var(--fs-accent);">
            <div class="card-body py-2">
                <div class="text-muted" style="font-size:0.75rem;">{{ __('Adopted') }}</div>
                <div class="fw-bold fs-5">{{ $stats['adopted'] }}</div>
            </div>
        </div>
    </div>
</div>

{{-- Filter --}}
<div class="card mb-4">
    <div class="card-body">
        <form method="GET" action="{{ route('shelter.listings.index') }}" class="row g-3">
            <div class="col-md-5">
                <input type="text" class="form-control" name="search" placeholder="{{ __('Search by name...') }}" value="{{ request('search') }}">
            </div>
            <div class="col-md-4">
                <select class="form-select" name="status">
                    <option value="">{{ __('All Statuses') }}</option>
                    <option value="available" {{ request('status') === 'available' ? 'selected' : '' }}>{{ __('Available') }}</option>
                    <option value="pending" {{ request('status') === 'pending' ? 'selected' : '' }}>{{ __('Pending') }}</option>
                    <option value="adopted" {{ request('status') === 'adopted' ? 'selected' : '' }}>{{ __('Adopted') }}</option>
                    <option value="inactive" {{ request('status') === 'inactive' ? 'selected' : '' }}>{{ __('Inactive') }}</option>
                </select>
            </div>
            <div class="col-md-3 d-flex gap-2">
                <button type="submit" class="btn btn-outline-secondary flex-grow-1">
                    <i class="bi bi-search me-1"></i>{{ __('Filter') }}
                </button>
                <a href="{{ route('shelter.listings.index') }}" class="btn btn-outline-danger"><i class="bi bi-x"></i></a>
            </div>
        </form>
    </div>
</div>

{{-- Listings Grid --}}
<div class="row g-4">
    @forelse($listings as $listing)
        <div class="col-md-6 col-lg-4">
            <div class="card h-100 border-0 shadow-sm">
                @if($listing->images->count())
                    <img src="{{ asset('storage/' . $listing->images->first()->image_path) }}" class="card-img-top" alt="{{ $listing->pet_name }}" style="height:200px;object-fit:cover;">
                @else
                    <div class="card-img-top d-flex align-items-center justify-content-center" style="height:200px;background:#f0f7f2;">
                        <i class="bi bi-heart" style="font-size:3rem;opacity:0.2;color:#1a6b3c;"></i>
                    </div>
                @endif
                <div class="card-body">
                    <div class="d-flex justify-content-between align-items-start mb-2">
                        <h6 class="fw-bold mb-0">{{ $listing->pet_name }}</h6>
                        @if($listing->status === 'available')
                            <span class="badge bg-success">Available</span>
                        @elseif($listing->status === 'adopted')
                            <span class="badge bg-primary">Adopted</span>
                        @elseif($listing->status === 'pending')
                            <span class="badge bg-warning text-dark">Pending</span>
                        @else
                            <span class="badge bg-secondary">Inactive</span>
                        @endif
                    </div>
                    <div class="text-muted mb-2" style="font-size:0.8rem;">
                        {{ $listing->species->name ?? '-' }} &middot; {{ $listing->breed->name ?? '-' }}
                        @if($listing->gender) &middot; {{ ucfirst($listing->gender) }} @endif
                    </div>
                    @if($listing->health_status)
                        <div class="mb-2"><small class="text-muted">Health:</small> <small>{{ $listing->health_status }}</small></div>
                    @endif
                    @if($listing->description)
                        <p class="text-muted mb-0" style="font-size:0.8rem;display:-webkit-box;-webkit-line-clamp:2;-webkit-box-orient:vertical;overflow:hidden;">{{ $listing->description }}</p>
                    @endif
                </div>
                <div class="card-footer bg-white border-0 d-flex gap-2">
                    <a href="{{ route('shelter.listings.show', $listing) }}" class="btn btn-sm btn-outline-primary flex-grow-1">
                        <i class="bi bi-eye me-1"></i>{{ __('View') }}
                    </a>
                    <a href="{{ route('shelter.listings.edit', $listing) }}" class="btn btn-sm btn-outline-secondary flex-grow-1">
                        <i class="bi bi-pencil me-1"></i>{{ __('Edit') }}
                    </a>
                    <form action="{{ route('shelter.listings.destroy', $listing) }}" method="POST" class="d-inline" onsubmit="return confirm('{{ __('Are you sure?') }}')">
                        @csrf @method('DELETE')
                        <button type="submit" class="btn btn-sm btn-outline-danger"><i class="bi bi-trash"></i></button>
                    </form>
                </div>
            </div>
        </div>
    @empty
        <div class="col-12">
            <div class="card">
                <div class="card-body text-center py-5">
                    <i class="bi bi-bookmark-heart" style="font-size:3rem;opacity:0.3;"></i>
                    <p class="mt-2 text-muted">{{ __('No listings found.') }}</p>
                    <a href="{{ route('shelter.listings.create') }}" class="btn btn-primary btn-sm">
                        <i class="bi bi-plus me-1"></i>{{ __('Create Your First Listing') }}
                    </a>
                </div>
            </div>
        </div>
    @endforelse
</div>

@if($listings->hasPages())
    <div class="mt-4">{{ $listings->links() }}</div>
@endif
@endsection
