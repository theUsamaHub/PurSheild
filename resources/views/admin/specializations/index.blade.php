@extends('layouts.app')

@section('content')
    <div class="mb-4">
        <div class="d-flex justify-content-between align-items-center">
            <h2 class="h4 mb-0 fw-semibold">{{ __('Specializations') }}</h2>
            <a href="{{ route('admin.specializations.create') }}" class="btn btn-primary btn-sm"><i class="bi bi-plus-circle me-1"></i>{{ __('Add Specialization') }}</a>
        </div>
    </div>

    <div class="card mb-4">
        <div class="card-body">
            <form method="GET" class="row g-3">
                <div class="col-md-8">
                    <input type="text" class="form-control" name="search" placeholder="{{ __('Search specializations...') }}" value="{{ request('search') }}">
                </div>
                <div class="col-md-2">
                    <button type="submit" class="btn btn-outline-secondary w-100"><i class="bi bi-search me-1"></i>{{ __('Filter') }}</button>
                </div>
                <div class="col-md-2">
                    <a href="{{ route('admin.specializations.index') }}" class="btn btn-outline-danger w-100"><i class="bi bi-x"></i></a>
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
                            <th>{{ __('Description') }}</th>
                            <th>{{ __('Vets') }}</th>
                            <th>{{ __('Status') }}</th>
                            <th class="text-end">{{ __('Actions') }}</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse ($specializations as $item)
                            <tr>
                                <td class="fw-medium">{{ $item->name }}</td>
                                <td>{{ $item->description ?? '-' }}</td>
                                <td>{{ $item->vets_count }}</td>
                                <td>
                                    @if ($item->status === 'active')
                                        <span class="badge bg-success">{{ __('Active') }}</span>
                                    @else
                                        <span class="badge bg-secondary">{{ __('Inactive') }}</span>
                                    @endif
                                </td>
                                <td class="text-end">
                                    <div class="btn-group btn-group-sm">
                                        <a href="{{ route('admin.specializations.edit', $item) }}" class="btn btn-outline-primary"><i class="bi bi-pencil"></i></a>
                                        @if ($item->vets_count === 0)
                                            <form action="{{ route('admin.specializations.destroy', $item) }}" method="POST" class="d-inline" onsubmit="return confirm()">
                                                @csrf @method('DELETE')
                                                <button class="btn btn-outline-danger"><i class="bi bi-trash"></i></button>
                                            </form>
                                        @endif
                                    </div>
                                </td>
                            </tr>
                        @empty
                            <tr><td colspan="5" class="text-center py-4 text-muted">{{ __('No specializations found.') }}</td></tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
        @if ($specializations->hasPages())
            <div class="card-footer bg-white">{{ $specializations->links() }}</div>
        @endif
    </div>
@endsection
