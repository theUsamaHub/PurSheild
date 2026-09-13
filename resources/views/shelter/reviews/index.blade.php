@extends('layouts.shelter.app')

@section('content')
<div class="mb-4">
    <div class="d-flex justify-content-between align-items-center">
        <h2 class="h4 mb-0 fw-semibold">{{ __('My Reviews') }}</h2>
    </div>
</div>

<div class="row g-4">
    <div class="col-lg-8">
        <div class="card">
            <div class="card-body p-0">
                @forelse($reviews as $review)
                    <div class="px-4 py-3 {{ !$loop->last ? 'border-bottom' : '' }}">
                        <div class="d-flex justify-content-between align-items-start">
                            <div class="d-flex">
                                <div class="rounded-circle d-flex align-items-center justify-content-center me-3 flex-shrink-0" style="width:40px;height:40px;background:linear-gradient(135deg,#1a6b3c,#2e9e5a);">
                                    <span class="text-white fw-semibold" style="font-size:0.85rem;">{{ substr($review->user->name ?? '?', 0, 1) }}</span>
                                </div>
                                <div>
                                    <div class="d-flex align-items-center mb-1">
                                        <strong style="font-size:0.875rem;">{{ $review->user->name ?? 'Anonymous' }}</strong>
                                        <small class="text-muted ms-2">{{ $review->created_at->diffForHumans() }}</small>
                                    </div>
                                    @if($review->reviewable_type === \App\Models\AdoptionListing::class)
                                        <small class="text-muted">for <strong>{{ $review->reviewable->pet_name ?? 'a listing' }}</strong></small>
                                    @endif
                                    <div class="mb-1">
                                        @for($i = 1; $i <= 5; $i++)
                                            <i class="bi {{ $i <= $review->rating ? 'bi-star-fill' : 'bi-star' }} text-warning" style="font-size:0.8rem;"></i>
                                        @endfor
                                    </div>
                                    @if($review->comment)
                                        <p class="mb-0" style="font-size:0.875rem;">{{ $review->comment }}</p>
                                    @endif
                                </div>
                            </div>
                        </div>
                    </div>
                @empty
                    <div class="text-center py-5">
                        <i class="bi bi-star" style="font-size:3rem;opacity:0.3;"></i>
                        <p class="mt-2 text-muted">{{ __('No reviews yet.') }}</p>
                    </div>
                @endforelse
            </div>
            @if($reviews->hasPages())
                <div class="card-footer">{{ $reviews->links() }}</div>
            @endif
        </div>
    </div>

    <div class="col-lg-4">
        <div class="card">
            <div class="card-header">
                <h6 class="mb-0 fw-semibold">{{ __('Rating Summary') }}</h6>
            </div>
            <div class="card-body text-center">
                <div class="fw-bold fs-2 mb-1">{{ $avgRating ? number_format($avgRating, 1) : '-' }}</div>
                <div class="mb-3">
                    @for($i = 1; $i <= 5; $i++)
                        <i class="bi {{ $i <= round($avgRating) ? 'bi-star-fill' : 'bi-star' }} text-warning"></i>
                    @endfor
                </div>

                @for($i = 5; $i >= 1; $i--)
                    @php $count = $distribution->get($i, 0); @endphp
                    <div class="d-flex align-items-center mb-1" style="font-size:0.8rem;">
                        <span class="me-2" style="width:20px;">{{ $i }}<i class="bi bi-star-fill text-warning" style="font-size:0.6rem;"></i></span>
                        <div class="flex-grow-1 bg-light rounded" style="height:8px;">
                            <div class="bg-warning rounded" style="height:100%;width:{{ $reviews->total() > 0 ? ($count / $reviews->total() * 100) : 0 }}%;"></div>
                        </div>
                        <span class="ms-2 text-muted">{{ $count }}</span>
                    </div>
                @endfor
            </div>
        </div>
    </div>
</div>
@endsection
