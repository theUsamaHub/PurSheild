@extends('layouts.app')

@section('content')
    <div class="mb-4">
        <div class="d-flex justify-content-between align-items-center">
            <h2 class="h4 mb-0 fw-semibold">{{ __('Breeds') }}</h2>
            <a href="{{ route('admin.breeds.create') }}" class="btn btn-primary btn-sm"><i class="bi bi-plus-circle me-1"></i>{{ __('Add Breed') }}</a>
        </div>
    </div>

    <div class="card mb-4">
        <div class="card-body">
            <form method="GET" class="row g-3">
                <div class="col-md-5">
                    <input type="text" class="form-control" name="search" placeholder="{{ __('Search breeds...') }}" value="{{ request('search') }}">
                </div>
                <div class="col-md-3">
                    <select name="species_id" class="form-select">
                        <option value="">{{ __('All Species') }}</option>
                        @foreach ($speciesList as $species)
                            <option value="{{ $species->id }}" {{ request('species_id') == $species->id ? 'selected' : '' }}>{{ $species->name }}</option>
                        @endforeach
                    </select>
                </div>
                <div class="col-md-2">
                    <button type="submit" class="btn btn-outline-secondary w-100"><i class="bi bi-search me-1"></i>{{ __('Filter') }}</button>
                </div>
                <div class="col-md-2">
                    <a href="{{ route('admin.breeds.index') }}" class="btn btn-outline-danger w-100"><i class="bi bi-x"></i></a>
                </div>
            </form>
        </div>
    </div>

    <div class="card">
        <div class="card-body p-0">
            <div class="table-responsive">
                <table class="table table-hover mb-0">
                    <thead>
                        <tr>
                            <th>{{ __('Name') }}</th>
                            <th>{{ __('Species') }}</th>
                            <th>{{ __('Description') }}</th>
                            <th>{{ __('Status') }}</th>
                            <th class="text-end">{{ __('Actions') }}</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse ($breeds as $breed)
                            <tr>
                                <td class="fw-medium">{{ $breed->name }}</td>
                                <td>{{ $breed->species->name ?? '-' }}</td>
                                <td>{{ $breed->description ?? '-' }}</td>
                                <td>
                                    @if ($breed->status === 'active')
                                        <span class="badge bg-success">{{ __('Active') }}</span>
                                    @else
                                        <span class="badge bg-secondary">{{ __('Inactive') }}</span>
                                    @endif
                                </td>
                                <td class="text-end">
                                    <div class="btn-group btn-group-sm">
                                        <a href="{{ route('admin.breeds.edit', $breed) }}" class="btn btn-outline-primary"><i class="bi bi-pencil"></i></a>
                                        @if ($breed->pets()->count() === 0)
                                            <form action="{{ route('admin.breeds.destroy', $breed) }}" method="POST" class="d-inline" onsubmit="return confirm()">
                                                @csrf @method('DELETE')
                                                <button class="btn btn-outline-danger"><i class="bi bi-trash"></i></button>
                                            </form>
                                        @endif
                                    </div>
                                </td>
                            </tr>
                        @empty
                            <tr><td colspan="5" class="text-center py-4 text-muted">{{ __('No breeds found.') }}</td></tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
        @if ($breeds->hasPages())
            <div class="card-footer bg-white">{{ $breeds->links() }}</div>
        @endif
    </div>
@endsection
