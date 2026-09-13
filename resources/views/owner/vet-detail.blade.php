@extends('layouts.owner.app')

@section('content')
    <div class="mb-4">
        <div class="d-flex justify-content-between align-items-center">
            <h2 class="h4 mb-0 fw-semibold">{{ __('Veterinarian Profile') }}</h2>
            <a href="{{ route('owner.browse-vets') }}" class="btn btn-outline-secondary btn-sm">
                <i class="bi bi-arrow-left me-1"></i>{{ __('Back') }}
            </a>
        </div>
    </div>

    <div class="row">
        <!-- Profile Card -->
        <div class="col-lg-4 mb-4">
            <div class="card">
                <div class="card-body p-4 text-center">
                    <div class="rounded-circle d-inline-flex align-items-center justify-content-center mb-3" style="width:96px;height:96px;background:linear-gradient(135deg,#1a6b3c,#2e9e5a);">
                        @if ($vet->profile_image)
                            <img src="{{ Storage::url($vet->profile_image) }}" alt="{{ $vet->name }}" class="rounded-circle" style="width:96px;height:96px;object-fit:cover;">
                        @else
                            <span class="text-white fw-bold" style="font-size:2.5rem;">{{ substr($vet->name, 0, 1) }}</span>
                        @endif
                    </div>
                    <h4 class="fw-bold mb-1">{{ $vet->name }}</h4>
                    <div class="text-muted mb-2">{{ $vet->vetProfile->qualification ?? '-' }}</div>

                    <!-- Rating -->
                    <div class="mb-3">
                        @for ($i = 1; $i <= 5; $i++)
                            @if ($i <= floor($avgRating))
                                <i class="bi bi-star-fill text-warning"></i>
                            @elseif ($i - $avgRating < 1 && $i - $avgRating > 0)
                                <i class="bi bi-star-half text-warning"></i>
                            @else
                                <i class="bi bi-star text-warning"></i>
                            @endif
                        @endfor
                        <span class="text-muted ms-1">({{ number_format($avgRating, 1) }}) &middot; {{ $reviewCount }} {{ __('reviews') }}</span>
                    </div>

                    <hr>

                    <table class="table table-sm table-borderless text-start mb-0">
                        <tbody>
                            <tr>
                                <td class="text-muted" style="width:140px;"><i class="bi bi-briefcase me-1"></i>{{ __('Experience') }}</td>
                                <td>{{ $vet->vetProfile->experience_years ? $vet->vetProfile->experience_years . ' ' . __('years') : '-' }}</td>
                            </tr>
                            <tr>
                                <td class="text-muted"><i class="bi bi-hospital me-1"></i>{{ __('Clinic') }}</td>
                                <td>{{ $vet->vetProfile->clinic_name ?? '-' }}</td>
                            </tr>
                            <tr>
                                <td class="text-muted"><i class="bi bi-geo-alt me-1"></i>{{ __('Address') }}</td>
                                <td>{{ $vet->vetProfile->clinic_address ?? '-' }}</td>
                            </tr>
                            <tr>
                                <td class="text-muted"><i class="bi bi-cash me-1"></i>{{ __('Fee') }}</td>
                                <td class="fw-bold text-success">{{ $vet->vetProfile->consultation_fee ? '$' . number_format($vet->vetProfile->consultation_fee, 2) : '-' }}</td>
                            </tr>
                        </tbody>
                    </table>

                    @if ($vet->vetProfile->bio)
                        <hr>
                        <div class="text-start">
                            <small class="text-muted fw-semibold">{{ __('About') }}</small>
                            <p class="mt-1 mb-0" style="font-size:0.875rem;">{{ $vet->vetProfile->bio }}</p>
                        </div>
                    @endif

                    @if ($vet->specializations->count())
                        <hr>
                        <div class="text-start">
                            <small class="text-muted fw-semibold d-block mb-2">{{ __('Specializations') }}</small>
                            @foreach ($vet->specializations as $spec)
                                <span class="badge bg-success bg-opacity-10 text-success me-1 mb-1">{{ $spec->name }}</span>
                            @endforeach
                        </div>
                    @endif

                    <hr>
                    <a href="{{ route('owner.appointments.create', ['vet_id' => $vet->id]) }}" class="btn w-100" style="background:#1a6b3c;color:#fff;">
                        <i class="bi bi-calendar-plus me-1"></i>{{ __('Book Appointment') }}
                    </a>
                </div>
            </div>
        </div>

        <!-- Reviews -->
        <div class="col-lg-8">
            <div class="card mb-4">
                <div class="card-header d-flex justify-content-between align-items-center">
                    <h6 class="mb-0 fw-semibold">{{ __('Reviews') }} ({{ $reviewCount }})</h6>
                    @if (!$hasReviewed)
                        <button class="btn btn-sm btn-primary" data-bs-toggle="modal" data-bs-target="#reviewModal">
                            <i class="bi bi-chat-left-text me-1"></i>{{ __('Write a Review') }}
                        </button>
                    @endif
                </div>
                <div class="card-body">
                    @forelse($reviews as $review)
                        <div class="d-flex mb-3 {{ !$loop->last ? 'pb-3 border-bottom' : '' }}">
                            <div class="rounded-circle d-flex align-items-center justify-content-center flex-shrink-0 me-3"
                                style="width:40px;height:40px;background:linear-gradient(135deg,#1a6b3c,#2e9e5a);">
                                <span class="text-white fw-semibold" style="font-size:0.85rem;">{{ substr($review->user->name ?? '?', 0, 1) }}</span>
                            </div>
                            <div class="flex-grow-1">
                                <div class="d-flex justify-content-between align-items-center mb-1">
                                    <div>
                                        <strong style="font-size:0.875rem;">{{ $review->user->name ?? 'Anonymous' }}</strong>
                                        <span class="text-muted ms-2" style="font-size:0.75rem;">{{ $review->created_at->diffForHumans() }}</span>
                                    </div>
                                    <div>
                                        @for ($i = 1; $i <= 5; $i++)
                                            <i class="bi {{ $i <= $review->rating ? 'bi-star-fill' : 'bi-star' }} text-warning" style="font-size:0.75rem;"></i>
                                        @endfor
                                    </div>
                                </div>
                                @if ($review->comment)
                                    <p class="mb-0" style="font-size:0.875rem;">{{ $review->comment }}</p>
                                @endif
                            </div>
                        </div>
                    @empty
                        <div class="text-center py-4">
                            <i class="bi bi-chat-left-text" style="font-size:2rem;opacity:0.3;"></i>
                            <p class="mt-2 text-muted mb-0">{{ __('No reviews yet. Be the first to review!') }}</p>
                        </div>
                    @endforelse
                </div>
                @if ($reviews->hasPages())
                    <div class="card-footer">
                        {{ $reviews->links() }}
                    </div>
                @endif
            </div>
        </div>
    </div>

    <!-- Review Modal -->
    @if (!$hasReviewed)
        <div class="modal fade" id="reviewModal" tabindex="-1">
            <div class="modal-dialog">
                <div class="modal-content">
                    <form action="{{ route('owner.reviews.store', $vet) }}" method="POST">
                        @csrf
                        <div class="modal-header">
                            <h5 class="modal-title fw-semibold">{{ __('Review') }} {{ $vet->name }}</h5>
                            <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                        </div>
                        <div class="modal-body">
                            <div class="mb-3">
                                <label class="form-label fw-semibold">{{ __('Rating') }} *</label>
                                <div class="d-flex gap-1" x-data="{ rating: {{ old('rating', 0) }} }">
                                    @for ($i = 1; $i <= 5; $i++)
                                        <button type="button" class="btn btn-link p-0 text-warning" style="font-size:1.5rem;text-decoration:none;" x-on:click="rating = {{ $i }}">
                                            <i class="bi" :class="rating >= {{ $i }} ? 'bi-star-fill' : 'bi-star'"></i>
                                        </button>
                                    @endfor
                                    <input type="hidden" name="rating" :value="rating" required>
                                </div>
                            </div>
                            <div class="mb-3">
                                <label class="form-label fw-semibold">{{ __('Comment') }}</label>
                                <textarea class="form-control" name="comment" rows="4" placeholder="{{ __('Share your experience...') }}">{{ old('comment') }}</textarea>
                            </div>
                        </div>
                        <div class="modal-footer">
                            <button type="button" class="btn btn-outline-secondary btn-sm" data-bs-dismiss="modal">{{ __('Cancel') }}</button>
                            <button type="submit" class="btn btn-primary btn-sm">
                                <i class="bi bi-send me-1"></i>{{ __('Submit Review') }}
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    @endif
@endsection
