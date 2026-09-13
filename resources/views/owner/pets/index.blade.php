@extends('layouts.owner.app')

@section('content')
    <div class="mb-4">
        <div class="d-flex justify-content-between align-items-center">
            <h2 class="h4 mb-0 fw-semibold">{{ __('My Pets') }}</h2>
            <a href="{{ route('owner.pets.create') }}" class="btn btn-primary btn-sm">
                <i class="bi bi-plus-circle me-1"></i>{{ __('Add Pet') }}
            </a>
        </div>
    </div>

    <div class="row g-3">
        @forelse($pets as $pet)
            <div class="col-md-6 col-lg-4">
                <div class="card h-100">
                    <div class="card-body">
                        <div class="d-flex justify-content-between align-items-start mb-3">
                            <div class="d-flex align-items-center">
                                @php
                                    $primaryImage = $pet->images->firstWhere('is_primary') ?? $pet->images->first();
                                @endphp
                                @if ($primaryImage)
                                    <img src="{{ asset('storage/' . $primaryImage->image_path) }}" alt="{{ $pet->name }}"
                                        class="rounded-circle me-3" style="width:48px;height:48px;object-fit:cover;">
                                @elseif ($pet->profile_image)
                                    <img src="{{ asset('storage/' . $pet->profile_image) }}" alt="{{ $pet->name }}"
                                        class="rounded-circle me-3" style="width:48px;height:48px;object-fit:cover;">
                                @else
                                    <div class="rounded-circle d-inline-flex align-items-center justify-content-center me-3"
                                        style="width:48px;height:48px;background:linear-gradient(135deg,#1a6b3c,#2e9e5a);">
                                        <i class="bi bi-heart-fill text-white"></i>
                                    </div>
                                @endif
                                <div>
                                    <h5 class="card-title mb-1 fw-semibold">{{ $pet->name }}</h5>
                                    <div class="text-muted" style="font-size:0.8rem;">
                                        {{ $pet->species->name ?? '-' }}{{ $pet->breed ? ' - ' . $pet->breed->name : '' }}
                                    </div>
                                </div>
                            </div>
                            @if ($pet->gender)
                                <span class="badge {{ $pet->gender === 'male' ? 'bg-info' : 'bg-pink' }}" style="{{ $pet->gender === 'female' ? 'background-color:#ec4899 !important;' : '' }}">
                                    <i class="bi {{ $pet->gender === 'male' ? 'bi-gender-male' : 'bi-gender-female' }} me-1"></i>{{ ucfirst($pet->gender) }}
                                </span>
                            @endif
                        </div>

                        <div class="mb-3">
                            @if ($pet->date_of_birth)
                                <div class="d-flex justify-content-between mb-1">
                                    <span class="text-muted" style="font-size:0.8rem;">{{ __('Age') }}</span>
                                    <span style="font-size:0.8rem;">{{ \Carbon\Carbon::parse($pet->date_of_birth)->age }} {{ __('years') }}</span>
                                </div>
                            @endif
                            @if ($pet->weight)
                                <div class="d-flex justify-content-between mb-1">
                                    <span class="text-muted" style="font-size:0.8rem;">{{ __('Weight') }}</span>
                                    <span style="font-size:0.8rem;">{{ $pet->weight }} kg</span>
                                </div>
                            @endif
                            @if ($pet->color)
                                <div class="d-flex justify-content-between mb-1">
                                    <span class="text-muted" style="font-size:0.8rem;">{{ __('Color') }}</span>
                                    <span style="font-size:0.8rem;">{{ $pet->color }}</span>
                                </div>
                            @endif
                        </div>

                        <div class="d-flex gap-2">
                            <a href="{{ route('owner.pets.show', $pet) }}" class="btn btn-outline-primary btn-sm flex-grow-1">
                                <i class="bi bi-eye me-1"></i>{{ __('View') }}
                            </a>
                            <a href="{{ route('owner.pets.edit', $pet) }}" class="btn btn-outline-warning btn-sm flex-grow-1">
                                <i class="bi bi-pencil me-1"></i>{{ __('Edit') }}
                            </a>
                            <form action="{{ route('owner.pets.destroy', $pet) }}" method="POST" class="d-inline" onsubmit="return confirm('{{ __('Are you sure you want to delete this pet?') }}')">
                                @csrf
                                @method('DELETE')
                                <button type="submit" class="btn btn-outline-danger btn-sm" title="{{ __('Delete') }}">
                                    <i class="bi bi-trash"></i>
                                </button>
                            </form>
                        </div>
                    </div>
                </div>
            </div>
        @empty
            <div class="col-12">
                <div class="card">
                    <div class="card-body text-center py-5">
                        <div class="empty-state">
                            <i class="bi bi-heart" style="font-size:3rem;opacity:0.3;"></i>
                            <p class="mt-2 text-muted">{{ __('You haven\'t added any pets yet.') }}</p>
                            <a href="{{ route('owner.pets.create') }}" class="btn btn-primary btn-sm mt-2">
                                <i class="bi bi-plus-circle me-1"></i>{{ __('Add Your First Pet') }}
                            </a>
                        </div>
                    </div>
                </div>
            </div>
        @endforelse
    </div>
@endsection
