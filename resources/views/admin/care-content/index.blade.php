@extends('layouts.app')

@section('content')
    <div class="mb-4">
        <div class="d-flex justify-content-between align-items-center">
            <h2 class="h4 mb-0 fw-semibold">{{ __('Care Content') }}</h2>
            <a href="{{ route('admin.care-content.create') }}" class="btn btn-primary btn-sm">
                <i class="bi bi-plus-circle me-1"></i>{{ __('Add Content') }}
            </a>
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
                    <div class="text-muted" style="font-size:0.75rem;">{{ __('Total') }}</div>
                    <div class="fs-4 fw-bold">{{ $stats['total'] }}</div>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card border-start border-success border-4 h-100">
                <div class="card-body">
                    <div class="text-muted" style="font-size:0.75rem;">{{ __('Articles') }}</div>
                    <div class="fs-4 fw-bold">{{ $stats['articles'] }}</div>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card border-start border-info border-4 h-100">
                <div class="card-body">
                    <div class="text-muted" style="font-size:0.75rem;">{{ __('Videos') }}</div>
                    <div class="fs-4 fw-bold">{{ $stats['videos'] }}</div>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card border-start border-warning border-4 h-100">
                <div class="card-body">
                    <div class="text-muted" style="font-size:0.75rem;">{{ __('FAQs') }}</div>
                    <div class="fs-4 fw-bold">{{ $stats['faqs'] }}</div>
                </div>
            </div>
        </div>
    </div>

    <!-- Search & Filters -->
    <div class="card mb-4">
        <div class="card-body">
            <form method="GET" action="{{ route('admin.care-content.index') }}" class="row g-3">
                <div class="col-md-3">
                    <input type="text" class="form-control" name="search" placeholder="{{ __('Search content...') }}" value="{{ request('search') }}">
                </div>
                <div class="col-md-2">
                    <select class="form-select" name="category">
                        <option value="">{{ __('All Categories') }}</option>
                        <option value="feeding" {{ request('category') === 'feeding' ? 'selected' : '' }}>{{ __('Feeding') }}</option>
                        <option value="hygiene" {{ request('category') === 'hygiene' ? 'selected' : '' }}>{{ __('Hygiene') }}</option>
                        <option value="exercise" {{ request('category') === 'exercise' ? 'selected' : '' }}>{{ __('Exercise') }}</option>
                        <option value="health" {{ request('category') === 'health' ? 'selected' : '' }}>{{ __('Health') }}</option>
                        <option value="training" {{ request('category') === 'training' ? 'selected' : '' }}>{{ __('Training') }}</option>
                    </select>
                </div>
                <div class="col-md-2">
                    <select class="form-select" name="content_type">
                        <option value="">{{ __('All Types') }}</option>
                        <option value="article" {{ request('content_type') === 'article' ? 'selected' : '' }}>{{ __('Article') }}</option>
                        <option value="video" {{ request('content_type') === 'video' ? 'selected' : '' }}>{{ __('Video') }}</option>
                        <option value="faq" {{ request('content_type') === 'faq' ? 'selected' : '' }}>{{ __('FAQ') }}</option>
                    </select>
                </div>
                <div class="col-md-5 d-flex gap-2">
                    <button type="submit" class="btn btn-outline-secondary flex-grow-1">
                        <i class="bi bi-search me-1"></i>{{ __('Filter') }}
                    </button>
                    <a href="{{ route('admin.care-content.index') }}" class="btn btn-outline-danger"><i class="bi bi-x"></i></a>
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
                            <th>{{ __('Title') }}</th>
                            <th>{{ __('Category') }}</th>
                            <th>{{ __('Type') }}</th>
                            <th>{{ __('Status') }}</th>
                            <th class="text-end">{{ __('Actions') }}</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse ($careContents as $item)
                            <tr>
                                <td class="fw-medium">{{ $item->title }}</td>
                                <td>
                                    @php
                                        $categoryColors = [
                                            'feeding' => 'success',
                                            'hygiene' => 'info',
                                            'exercise' => 'warning',
                                            'health' => 'danger',
                                            'training' => 'primary',
                                        ];
                                    @endphp
                                    <span class="badge bg-{{ $categoryColors[$item->category] ?? 'secondary' }}">{{ ucfirst($item->category) }}</span>
                                </td>
                                <td>
                                    @php
                                        $typeColors = [
                                            'article' => 'primary',
                                            'video' => 'info',
                                            'faq' => 'warning',
                                        ];
                                    @endphp
                                    <span class="badge bg-{{ $typeColors[$item->content_type] ?? 'secondary' }}">{{ ucfirst($item->content_type) }}</span>
                                </td>
                                <td>
                                    @if ($item->status === 'active')
                                        <span class="badge bg-success">{{ __('Active') }}</span>
                                    @else
                                        <span class="badge bg-secondary">{{ __('Inactive') }}</span>
                                    @endif
                                </td>
                                <td class="text-end">
                                    <div class="btn-group btn-group-sm">
                                        <a href="{{ route('admin.care-content.edit', $item) }}" class="btn btn-outline-primary" title="{{ __('Edit') }}">
                                            <i class="bi bi-pencil"></i>
                                        </a>
                                        <form action="{{ route('admin.care-content.destroy', $item) }}" method="POST" class="d-inline" onsubmit="return confirm('{{ __('Are you sure you want to delete this content?') }}')">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit" class="btn btn-outline-danger" title="{{ __('Delete') }}">
                                                <i class="bi bi-trash"></i>
                                            </button>
                                        </form>
                                    </div>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="5" class="text-center py-5">
                                    <div class="empty-state">
                                        <i class="bi bi-journal-text"></i>
                                        <p>{{ __('No care content found.') }}</p>
                                    </div>
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>

        @if ($careContents->hasPages())
            <div class="card-footer bg-white">
                {{ $careContents->links() }}
            </div>
        @endif
    </div>
@endsection
