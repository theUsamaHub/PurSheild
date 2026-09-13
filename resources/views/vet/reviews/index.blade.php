@extends('layouts.vet.app')

@section('content')
<div class="fade-in">
    <div class="mb-4">
        <h4 class="mb-1 fw-bold">{{ __('My Reviews') }}</h4>
        <p class="text-muted mb-0" style="font-size:0.875rem;">{{ __('What pet owners say about you') }}</p>
    </div>

    <div class="row g-3 mb-4">
        <div class="col-md-4">
            <div class="card border-0" style="border-left:4px solid var(--fs-accent);">
                <div class="card-body">
                    <div class="text-muted" style="font-size:0.75rem;">{{ __('Average Rating') }}</div>
                    <div class="d-flex align-items-center gap-2">
                        <div class="fw-bold fs-3">{{ $avgRating ? number_format($avgRating, 1) : '-' }}</div>
                        <div class="text-warning">
                            @for($i = 1; $i <= 5; $i++)
                                @if($i <= floor($avgRating ?? 0))
                                    <i class="bi bi-star-fill"></i>
                                @elseif(($avgRating ?? 0) - floor($avgRating ?? 0) >= 0.5 && $i === ceil($avgRating ?? 0))
                                    <i class="bi bi-star-half"></i>
                                @else
                                    <i class="bi bi-star"></i>
                                @endif
                            @endfor
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-md-4">
            <div class="card border-0" style="border-left:4px solid var(--fs-primary);">
                <div class="card-body">
                    <div class="text-muted" style="font-size:0.75rem;">{{ __('Total Reviews') }}</div>
                    <div class="fw-bold fs-3">{{ $totalReviews }}</div>
                </div>
            </div>
        </div>
        <div class="col-md-4">
            <div class="card border-0" style="border-left:4px solid var(--fs-success);">
                <div class="card-body">
                    <div class="text-muted" style="font-size:0.75rem;">{{ __('5-Star Reviews') }}</div>
                    <div class="fw-bold fs-3">{{ $ratingDistribution[5] ?? 0 }}</div>
                </div>
            </div>
        </div>
    </div>

    {{-- Rating Distribution --}}
    <div class="card mb-4">
        <div class="card-header">
            <h6 class="mb-0 fw-semibold">{{ __('Rating Distribution') }}</h6>
        </div>
        <div class="card-body">
            @for($i = 5; $i >= 1; $i--)
                @php $count = $ratingDistribution[$i] ?? 0; @endphp
                <div class="d-flex align-items-center mb-2">
                    <span class="me-2" style="font-size:0.85rem;width:20px;">{{ $i }}<i class="bi bi-star-fill text-warning ms-1" style="font-size:0.7rem;"></i></span>
                    <div class="progress flex-grow-1 me-2" style="height:8px;">
                        <div class="progress-bar bg-warning" style="width:{{ $totalReviews > 0 ? ($count / $totalReviews * 100) : 0 }}%"></div>
                    </div>
                    <span class="text-muted" style="font-size:0.8rem;width:30px;">{{ $count }}</span>
                </div>
            @endfor
        </div>
    </div>

    {{-- Reviews List --}}
    <div class="card">
        <div class="card-body p-0">
            @if($reviews->count())
                @foreach($reviews as $review)
                    <div class="px-4 py-3 {{ !$loop->last ? 'border-bottom' : '' }}">
                        <div class="d-flex justify-content-between align-items-start mb-2">
                            <div class="d-flex align-items-center gap-3">
                                <div class="rounded-circle d-flex align-items-center justify-content-center" style="width:40px;height:40px;background:linear-gradient(135deg,#1a6b3c,#2e9e5a);">
                                    <span class="text-white fw-semibold" style="font-size:0.8rem;">{{ strtoupper(substr($review->user->name ?? 'U', 0, 2)) }}</span>
                                </div>
                                <div>
                                    <div class="fw-semibold" style="font-size:0.9rem;">{{ $review->user->name ?? 'Anonymous' }}</div>
                                    <small class="text-muted">{{ $review->created_at->diffForHumans() }}</small>
                                </div>
                            </div>
                            <div class="text-warning">
                                @for($i = 1; $i <= 5; $i++)
                                    @if($i <= $review->rating)
                                        <i class="bi bi-star-fill"></i>
                                    @else
                                        <i class="bi bi-star"></i>
                                    @endif
                                @endfor
                            </div>
                        </div>
                        @if($review->comment)
                            <p class="text-muted mb-0" style="font-size:0.85rem;">{{ $review->comment }}</p>
                        @endif
                    </div>
                @endforeach
            @else
                <div class="empty-state">
                    <i class="bi bi-star d-block"></i>
                    <p>{{ __('No reviews yet.') }}</p>
                </div>
            @endif
        </div>
        @if($reviews->hasPages())
            <div class="card-footer">{{ $reviews->links() }}</div>
        @endif
    </div>
</div>
@endsection
