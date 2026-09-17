@extends('layouts.app')

@section('page-title', __('Pet Breeds Management'))

@section('content')
<style>
    .admin-page-header {
        background: #ffffff;
        border: 1px solid #e6f0eb;
        border-radius: 16px;
        padding: 20px 24px;
        margin-bottom: 24px;
        box-shadow: 0 4px 18px rgba(0, 0, 0, 0.02);
    }
    .admin-card {
        background: #ffffff;
        border: 1px solid #e6f0eb;
        border-radius: 16px;
        box-shadow: 0 4px 18px rgba(0, 0, 0, 0.02);
        overflow: hidden;
    }
    .admin-table th {
        background: #f0f7f4 !important;
        color: #074f3e;
        font-weight: 700;
        font-size: 0.8rem;
        text-transform: uppercase;
        letter-spacing: 0.05em;
        padding: 14px 18px;
        border-bottom: 1px solid #e6f0eb;
    }
    .admin-table td {
        padding: 14px 18px;
        vertical-align: middle;
        font-size: 0.88rem;
        border-bottom: 1px solid #f2f7f4;
    }
    .admin-table tbody tr:hover {
        background: #f8fcf9;
    }
    .btn-mint-primary {
        background: #087657;
        color: #ffffff;
        border-radius: 10px;
        font-weight: 600;
        padding: 8px 18px;
        transition: all 0.2s ease;
        text-decoration: none;
    }
    .btn-mint-primary:hover {
        background: #065c44;
        color: #ffffff;
        box-shadow: 0 4px 14px rgba(8, 118, 87, 0.25);
    }
    .badge-soft-success { background: #e6f7f1; color: #087657; font-weight: 600; padding: 4px 10px; border-radius: 20px; }
    .badge-soft-secondary { background: #f3f4f6; color: #6b7280; font-weight: 600; padding: 4px 10px; border-radius: 20px; }
    .badge-soft-info { background: #e0f2fe; color: #0284c7; font-weight: 600; padding: 4px 10px; border-radius: 20px; }
</style>

<!-- Header Banner -->
<div class="admin-page-header d-flex justify-content-between align-items-center">
    <div class="d-flex align-items-center gap-2">
        <div class="p-2 rounded-3 text-success" style="background:#e6f7f1;">
            <i class="bi bi-heart-pulse-fill fs-5"></i>
        </div>
        <div>
            <h4 class="mb-0 fw-bold text-dark">{{ __('Pet Breeds Directory') }}</h4>
            <small class="text-muted">{{ __('Manage specific pet breeds grouped by species for health and medical tracking.') }}</small>
        </div>
    </div>
    <a href="{{ route('admin.breeds.create') }}" class="btn-mint-primary">
        <i class="bi bi-plus-lg me-1"></i> {{ __('Add New Breed') }}
    </a>
</div>

<!-- Search & Filter Card -->
<div class="admin-card mb-4 p-3" style="background:#fafdfb;">
    <form method="GET" class="row g-2 align-items-center">
        <div class="col-md-5">
            <div class="input-group">
                <span class="input-group-text bg-white border-end-0 text-muted" style="border-color:#d4ebe2;"><i class="bi bi-search"></i></span>
                <input type="text" class="form-control border-start-0 ps-0" name="search" style="border-color:#d4ebe2;" placeholder="{{ __('Search by breed name...') }}" value="{{ request('search') }}">
            </div>
        </div>
        <div class="col-md-3">
            <select name="species_id" class="form-select" style="border-color:#d4ebe2;">
                <option value="">{{ __('All Species') }}</option>
                @foreach ($speciesList as $species)
                    <option value="{{ $species->id }}" {{ request('species_id') == $species->id ? 'selected' : '' }}>{{ $species->name }}</option>
                @endforeach
            </select>
        </div>
        <div class="col-md-2">
            <button type="submit" class="btn btn-success w-100 fw-semibold rounded-3" style="background:#087657; border-color:#087657;"><i class="bi bi-filter me-1"></i>{{ __('Filter') }}</button>
        </div>
        <div class="col-md-2">
            <a href="{{ route('admin.breeds.index') }}" class="btn btn-outline-secondary w-100 rounded-3"><i class="bi bi-arrow-counterclockwise me-1"></i>{{ __('Reset') }}</a>
        </div>
    </form>
</div>

<!-- Breeds Table -->
<div class="admin-card">
    <div class="table-responsive">
        <table class="table admin-table mb-0">
            <thead>
                <tr>
                    <th>{{ __('Breed Name') }}</th>
                    <th>{{ __('Species') }}</th>
                    <th>{{ __('Description') }}</th>
                    <th>{{ __('Status') }}</th>
                    <th class="text-end">{{ __('Actions') }}</th>
                </tr>
            </thead>
            <tbody>
                @forelse ($breeds as $breed)
                    <tr>
                        <td class="fw-bold text-dark">
                            <i class="bi bi-tag-fill me-2 text-success" style="opacity:0.7;"></i>{{ $breed->name }}
                        </td>
                        <td>
                            <span class="badge-soft-info"><i class="bi bi-bug me-1"></i>{{ $breed->species->name ?? '-' }}</span>
                        </td>
                        <td class="text-muted">{{ $breed->description ?? '-' }}</td>
                        <td>
                            @if ($breed->status === 'active')
                                <span class="badge-soft-success"><i class="bi bi-check-circle me-1"></i>{{ __('Active') }}</span>
                            @else
                                <span class="badge-soft-secondary">{{ __('Inactive') }}</span>
                            @endif
                        </td>
                        <td class="text-end">
                            <div class="btn-group btn-group-sm">
                                <a href="{{ route('admin.breeds.edit', $breed) }}" class="btn btn-light border text-primary rounded-2 me-1" title="Edit"><i class="bi bi-pencil"></i></a>
                                @if ($breed->pets()->count() === 0)
                                    <form action="{{ route('admin.breeds.destroy', $breed) }}" method="POST" class="d-inline" onsubmit="return confirm('Are you sure you want to delete this breed?')">
                                        @csrf @method('DELETE')
                                        <button class="btn btn-light border text-danger rounded-2" title="Delete"><i class="bi bi-trash"></i></button>
                                    </form>
                                @endif
                            </div>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="5" class="text-center py-5 text-muted">
                            <i class="bi bi-folder-x fs-2 d-block mb-2 text-muted" style="opacity:0.4;"></i>
                            {{ __('No breeds found.') }}
                        </td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>
    @if ($breeds->hasPages())
        <div class="p-3 border-top bg-white">{{ $breeds->links() }}</div>
    @endif
</div>
@endsection
