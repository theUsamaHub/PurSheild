@extends('layouts.app')

@section('content')
    <div class="mb-4">
        <div class="d-flex justify-content-between align-items-center">
            <h2 class="h4 mb-0 fw-semibold">{{ __('Reviews Moderation') }}</h2>
        </div>
    </div>

    @if (session('success'))
        <div class="alert alert-success alert-dismissible fade show" role="alert">
            {{ session('success') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    @endif

    <!-- Stats -->
    <div class="row g-3 mb-4">
        <div class="col-md-3">
            <div class="card border-start border-primary border-4 h-100">
                <div class="card-body">
                    <div class="text-muted" style="font-size:0.75rem;">{{ __('Total Reviews') }}</div>
                    <div class="fs-4 fw-bold">{{ $stats['total'] }}</div>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card border-start border-warning border-4 h-100">
                <div class="card-body">
                    <div class="text-muted" style="font-size:0.75rem;">{{ __('Average Rating') }}</div>
                    <div class="fs-4 fw-bold">
                        {{ $stats['average'] }}
                        <small class="text-warning"><i class="bi bi-star-fill"></i></small>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card border-start border-info border-4 h-100">
                <div class="card-body">
                    <div class="text-muted" style="font-size:0.75rem;">{{ __('Vet Reviews') }}</div>
                    <div class="fs-4 fw-bold">{{ $stats['vet_count'] }}</div>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card border-start border-success border-4 h-100">
                <div class="card-body">
                    <div class="text-muted" style="font-size:0.75rem;">{{ __('Product Reviews') }}</div>
                    <div class="fs-4 fw-bold">{{ $stats['product_count'] }}</div>
                </div>
            </div>
        </div>
    </div>
    <div class="row g-3 mb-4">
        <div class="col-md-3">
            <div class="card border-start border-primary border-4 h-100">
                <div class="card-body">
                    <div class="text-muted" style="font-size:0.75rem;">{{ __('Shelter Reviews') }}</div>
                    <div class="fs-4 fw-bold">{{ $stats['shelter_count'] }}</div>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card border-start border-success border-4 h-100">
                <div class="card-body">
                    <div class="text-muted" style="font-size:0.75rem;">{{ __('5-Star Reviews') }}</div>
                    <div class="fs-4 fw-bold">{{ $stats['five_star'] }}</div>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card border-start border-danger border-4 h-100">
                <div class="card-body">
                    <div class="text-muted" style="font-size:0.75rem;">{{ __('1-Star Reviews') }}</div>
                    <div class="fs-4 fw-bold">{{ $stats['one_star'] }}</div>
                </div>
            </div>
        </div>
    </div>

    <!-- Search & Filters -->
    <div class="card mb-4">
        <div class="card-body">
            <form method="GET" action="{{ route('admin.reviews.index') }}" class="row g-3">
                <div class="col-md-3">
                    <input type="text" class="form-control" name="search" placeholder="{{ __('Search by user or comment...') }}" value="{{ request('search') }}">
                </div>
                <div class="col-md-2">
                    <select class="form-select" name="type">
                        <option value="">{{ __('All Types') }}</option>
                        <option value="vet" {{ request('type') === 'vet' ? 'selected' : '' }}>{{ __('Vet Reviews') }}</option>
                        <option value="product" {{ request('type') === 'product' ? 'selected' : '' }}>{{ __('Product Reviews') }}</option>
                        <option value="shelter" {{ request('type') === 'shelter' ? 'selected' : '' }}>{{ __('Shelter Reviews') }}</option>
                    </select>
                </div>
                <div class="col-md-2">
                    <select class="form-select" name="rating">
                        <option value="">{{ __('All Ratings') }}</option>
                        <option value="5" {{ request('rating') == '5' ? 'selected' : '' }}>5 {{ __('Stars') }}</option>
                        <option value="4" {{ request('rating') == '4' ? 'selected' : '' }}>4 {{ __('Stars') }}</option>
                        <option value="3" {{ request('rating') == '3' ? 'selected' : '' }}>3 {{ __('Stars') }}</option>
                        <option value="2" {{ request('rating') == '2' ? 'selected' : '' }}>2 {{ __('Stars') }}</option>
                        <option value="1" {{ request('rating') == '1' ? 'selected' : '' }}>1 {{ __('Star') }}</option>
                    </select>
                </div>
                <div class="col-md-5 d-flex gap-2">
                    <button type="submit" class="btn btn-outline-secondary flex-grow-1">
                        <i class="bi bi-search me-1"></i>{{ __('Filter') }}
                    </button>
                    <a href="{{ route('admin.reviews.index') }}" class="btn btn-outline-danger"><i class="bi bi-x"></i></a>
                </div>
            </form>
        </div>
    </div>

    <!-- Table -->
    <div class="card">
        <div class="card-body p-0">
            <div class="table-responsive">
                <table class="table table-hover mb-0">
                    <thead>
                        <tr>
                            <th>{{ __('User') }}</th>
                            <th>{{ __('Type') }}</th>
                            <th>{{ __('Reviewable') }}</th>
                            <th>{{ __('Rating') }}</th>
                            <th>{{ __('Comment') }}</th>
                            <th>{{ __('Date') }}</th>
                            <th class="text-end">{{ __('Actions') }}</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse ($reviews as $review)
                            <tr>
                                <td class="fw-medium">{{ $review->user->name ?? __('Deleted User') }}</td>
                                <td>
                                    @php $typeName = class_basename($review->reviewable_type); @endphp
                                    @if ($typeName === 'Product')
                                        <span class="badge bg-success"><i class="bi bi-box-seam me-1"></i>{{ __('Product') }}</span>
                                    @elseif ($typeName === 'User')
                                        <span class="badge bg-info"><i class="bi bi-person me-1"></i>{{ __('Vet') }}</span>
                                    @elseif ($typeName === 'AdoptionListing')
                                        <span class="badge bg-primary"><i class="bi bi-building me-1"></i>{{ __('Shelter') }}</span>
                                    @else
                                        <span class="badge bg-secondary">{{ $typeName }}</span>
                                    @endif
                                </td>
                                <td>
                                    @if ($typeName === 'Product' && $review->reviewable)
                                        {{ $review->reviewable->name }}
                                    @elseif ($typeName === 'User' && $review->reviewable)
                                        {{ $review->reviewable->name }}
                                    @elseif ($typeName === 'AdoptionListing' && $review->reviewable)
                                        {{ $review->reviewable->pet_name }}
                                        <small class="text-muted">({{ $review->reviewable->shelter->name ?? __('Unknown Shelter') }})</small>
                                    @else
                                        <span class="text-muted">ID #{{ $review->reviewable_id }}</span>
                                    @endif
                                </td>
                                <td>
                                    @for ($i = 1; $i <= 5; $i++)
                                        @if ($i <= $review->rating)
                                            <i class="bi bi-star-fill text-warning" style="font-size:0.8rem;"></i>
                                        @else
                                            <i class="bi bi-star text-muted" style="font-size:0.8rem;"></i>
                                        @endif
                                    @endfor
                                </td>
                                <td>{{ \Illuminate\Support\Str::limit($review->comment, 80) ?: '-' }}</td>
                                <td class="text-muted">{{ $review->created_at->diffForHumans() }}</td>
                                <td class="text-end">
                                    <form action="{{ route('admin.reviews.destroy', $review) }}" method="POST" class="d-inline" onsubmit="return confirm('{{ __('Are you sure you want to delete this review?') }}')">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="btn btn-outline-danger btn-sm" title="{{ __('Delete Review') }}">
                                            <i class="bi bi-trash"></i>
                                        </button>
                                    </form>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="7" class="text-center py-5">
                                    <div class="empty-state">
                                        <i class="bi bi-star"></i>
                                        <p>{{ __('No reviews found.') }}</p>
                                    </div>
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>

        @if ($reviews->hasPages())
            <div class="card-footer bg-white">
                {{ $reviews->links() }}
            </div>
        @endif
    </div>
@endsection
