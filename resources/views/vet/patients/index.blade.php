@extends('layouts.vet.app')

@section('content')
<div class="fade-in">
    <div class="mb-4">
        <h4 class="mb-1 fw-bold">{{ __('My Patients') }}</h4>
        <p class="text-muted mb-0" style="font-size:0.875rem;">{{ __('Pets you have treated') }}</p>
    </div>

    <div class="card mb-4">
        <div class="card-body">
            <form method="GET" class="row g-3">
                <div class="col-md-5">
                    <input type="text" class="form-control" name="search" placeholder="{{ __('Search by pet or owner name...') }}" value="{{ request('search') }}">
                </div>
                <div class="col-md-4">
                    <select class="form-select" name="species_id">
                        <option value="">{{ __('All Species') }}</option>
                        @foreach($species as $sp)
                            <option value="{{ $sp->id }}" {{ request('species_id') == $sp->id ? 'selected' : '' }}>{{ $sp->name }}</option>
                        @endforeach
                    </select>
                </div>
                <div class="col-md-3 d-flex gap-1">
                    <button type="submit" class="btn btn-primary flex-grow-1"><i class="bi bi-search"></i></button>
                    <a href="{{ route('vet.patients.index') }}" class="btn btn-outline-secondary"><i class="bi bi-x"></i></a>
                </div>
            </form>
        </div>
    </div>

    <div class="row g-4">
        @forelse($patients as $pet)
            <div class="col-md-6 col-lg-4">
                <div class="card h-100 border-0 shadow-sm">
                    <div class="card-body">
                        <div class="d-flex align-items-center mb-3">
                            @if($pet->profile_image)
                                <img src="{{ asset('uploads/pets/' . $pet->profile_image) }}" alt="{{ $pet->name }}" class="rounded-circle" style="width:50px;height:50px;object-fit:cover;">
                            @else
                                <div class="rounded-circle d-flex align-items-center justify-content-center" style="width:50px;height:50px;background:linear-gradient(135deg,#1a6b3c,#2e9e5a);">
                                    <span class="text-white fw-semibold">{{ substr($pet->name, 0, 1) }}</span>
                                </div>
                            @endif
                            <div class="ms-3">
                                <h6 class="mb-0 fw-semibold">{{ $pet->name }}</h6>
                                <small class="text-muted">{{ $pet->species?->name ?? '' }} {{ $pet->breed ? '- ' . $pet->breed->name : '' }}</small>
                            </div>
                        </div>
                        <div class="d-flex flex-wrap gap-2 mb-3">
                            <span class="badge bg-dark bg-opacity-10 text-dark"><i class="bi bi-person me-1"></i>{{ $pet->owner->name }}</span>
                            @if($pet->gender)
                                <span class="badge bg-info bg-opacity-10 text-info">{{ ucfirst($pet->gender) }}</span>
                            @endif
                            @if($pet->weight)
                                <span class="badge bg-secondary bg-opacity-10 text-secondary">{{ $pet->weight }} kg</span>
                            @endif
                        </div>
                        <a href="{{ route('vet.patients.show', $pet) }}" class="btn btn-outline-primary btn-sm w-100">
                            <i class="bi bi-folder2-open me-1"></i>{{ __('View Medical History') }}
                        </a>
                    </div>
                </div>
            </div>
        @empty
            <div class="col-12">
                <div class="card">
                    <div class="card-body text-center py-5">
                        <i class="bi bi-heart-pulse" style="font-size:3rem;opacity:0.3;"></i>
                        <p class="mt-2 text-muted">{{ __('No patients found.') }}</p>
                    </div>
                </div>
            </div>
        @endforelse
    </div>

    @if($patients->hasPages())
        <div class="mt-4">{{ $patients->links() }}</div>
    @endif
</div>
@endsection
