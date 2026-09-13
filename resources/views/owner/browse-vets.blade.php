@extends('layouts.owner.app')

@section('content')
    <div class="mb-4">
        <div class="d-flex justify-content-between align-items-center">
            <h2 class="h4 mb-0 fw-semibold">{{ __('Browse Veterinarians') }}</h2>
        </div>
    </div>

    <!-- Search & Filters -->
    <div class="card mb-4">
        <div class="card-body">
            <form method="GET" action="{{ route('owner.browse-vets') }}" class="row g-3">
                <div class="col-md-5">
                    <input type="text" class="form-control" name="search" placeholder="{{ __('Search by name, clinic...') }}" value="{{ request('search') }}">
                </div>
                <div class="col-md-4">
                    <select class="form-select" name="specialization">
                        <option value="">{{ __('All Specializations') }}</option>
                        @foreach ($specializations as $spec)
                            <option value="{{ $spec }}" {{ request('specialization') === $spec ? 'selected' : '' }}>{{ $spec }}</option>
                        @endforeach
                    </select>
                </div>
                <div class="col-md-3 d-flex gap-2">
                    <button type="submit" class="btn btn-outline-secondary flex-grow-1">
                        <i class="bi bi-search me-1"></i>{{ __('Filter') }}
                    </button>
                    <a href="{{ route('owner.browse-vets') }}" class="btn btn-outline-danger"><i class="bi bi-x"></i></a>
                </div>
            </form>
        </div>
    </div>

    <!-- Vet Cards -->
    <div class="row g-4">
        @forelse ($vets as $vet)
            <div class="col-md-6 col-lg-4">
                <div class="card h-100 border-0 shadow-sm">
                    <div class="card-body">
                        <div class="d-flex align-items-center mb-3">
                            <div class="rounded-circle d-flex align-items-center justify-content-center flex-shrink-0" style="width:56px;height:56px;background:linear-gradient(135deg,#1a6b3c,#2e9e5a);">
                                <span class="text-white fw-bold fs-5">{{ substr($vet->name, 0, 1) }}</span>
                            </div>
                            <div class="ms-3">
                                <h6 class="mb-0 fw-semibold">
                                    <a href="{{ route('owner.browse-vets.show', $vet) }}" class="text-decoration-none text-dark">{{ $vet->name }}</a>
                                </h6>
                                <small class="text-muted">{{ $vet->vetProfile->qualification ?? '-' }}</small>
                            </div>
                        </div>

                        <table class="table table-sm table-borderless mb-3">
                            <tbody>
                                <tr>
                                    <td class="text-muted" style="width:130px;font-size:0.8rem;">{{ __('Experience') }}</td>
                                    <td style="font-size:0.8rem;">{{ $vet->vetProfile->experience ? $vet->vetProfile->experience . ' ' . __('years') : '-' }}</td>
                                </tr>
                                <tr>
                                    <td class="text-muted" style="font-size:0.8rem;">{{ __('Clinic') }}</td>
                                    <td style="font-size:0.8rem;">{{ $vet->vetProfile->clinic_name ?? '-' }}</td>
                                </tr>
                                <tr>
                                    <td class="text-muted" style="font-size:0.8rem;">{{ __('Consultation Fee') }}</td>
                                    <td style="font-size:0.8rem;">
                                        <span class="fw-bold text-success">
                                            {{ $vet->vetProfile->consultation_fee ? number_format($vet->vetProfile->consultation_fee, 2) : '-' }}
                                        </span>
                                    </td>
                                </tr>
                                <tr>
                                    <td class="text-muted" style="font-size:0.8rem;">{{ __('Rating') }}</td>
                                    <td style="font-size:0.8rem;">
                                        @php
                                            $stats = $reviewStats->get($vet->id);
                                            $rating = $stats ? $stats->avg_rating : 0;
                                            $count = $stats ? $stats->review_count : 0;
                                        @endphp
                                        @for ($i = 1; $i <= 5; $i++)
                                            @if ($i <= floor($rating))
                                                <i class="bi bi-star-fill text-warning"></i>
                                            @elseif ($i - $rating < 1 && $i - $rating > 0)
                                                <i class="bi bi-star-half text-warning"></i>
                                            @else
                                                <i class="bi bi-star text-warning"></i>
                                            @endif
                                        @endfor
                                        <span class="text-muted ms-1">({{ number_format($rating, 1) }}) {{ $count > 0 ? $count . ' ' . __('reviews') : '' }}</span>
                                    </td>
                                </tr>
                            </tbody>
                        </table>

                        @if ($vet->specializations->count() > 0)
                            <div class="mb-3">
                                @foreach ($vet->specializations as $spec)
                                    <span class="badge bg-success bg-opacity-10 text-success" style="font-size:0.7rem;">{{ $spec->name }}</span>
                                @endforeach
                            </div>
                        @endif

                        <div class="d-flex gap-2">
                            <a href="{{ route('owner.browse-vets.show', $vet) }}" class="btn btn-sm btn-outline-primary flex-grow-1">
                                <i class="bi bi-person me-1"></i>{{ __('View') }}
                            </a>
                            <a href="{{ route('owner.appointments.create', ['vet_id' => $vet->id]) }}" class="btn btn-sm btn-primary flex-grow-1">
                                <i class="bi bi-calendar-plus me-1"></i>{{ __('Book') }}
                            </a>
                        </div>
                    </div>
                </div>
            </div>
        @empty
            <div class="col-12">
                <div class="card">
                    <div class="card-body text-center py-5">
                        <div class="empty-state">
                            <i class="bi bi-heartbeat" style="font-size:3rem;opacity:0.3;"></i>
                            <p class="mt-2 text-muted">{{ __('No veterinarians found.') }}</p>
                        </div>
                    </div>
                </div>
            </div>
        @endforelse
    </div>

    @if ($vets->hasPages())
        <div class="mt-4">
            {{ $vets->links() }}
        </div>
    @endif
@endsection
