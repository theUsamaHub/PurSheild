@extends('layouts.app')

@section('page-title', __('User Roles Management'))

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
    .badge-soft-info { background: #e0f2fe; color: #0284c7; font-weight: 600; padding: 4px 10px; border-radius: 20px; }
    .badge-slug { background: #f0fdf4; color: #166534; border: 1px solid #bbf7d0; font-family: monospace; font-size: 0.8rem; padding: 2px 8px; border-radius: 6px; }
</style>

<!-- Header Banner -->
<div class="admin-page-header d-flex justify-content-between align-items-center">
    <div class="d-flex align-items-center gap-2">
        <div class="p-2 rounded-3 text-success" style="background:#e6f7f1;">
            <i class="bi bi-shield-lock-fill fs-5"></i>
        </div>
        <div>
            <h4 class="mb-0 fw-bold text-dark">{{ __('User Roles & Permissions') }}</h4>
            <small class="text-muted">{{ __('Manage ecosystem access roles (Admin, Veterinarian, Animal Shelter, Pet Owner).') }}</small>
        </div>
    </div>
    <a href="{{ route('admin.roles.create') }}" class="btn-mint-primary">
        <i class="bi bi-plus-lg me-1"></i> {{ __('Add New Role') }}
    </a>
</div>

<!-- Roles Table -->
<div class="admin-card">
    <div class="table-responsive">
        <table class="table admin-table mb-0">
            <thead>
                <tr>
                    <th>{{ __('Role Name') }}</th>
                    <th>{{ __('System Identifier (Slug)') }}</th>
                    <th>{{ __('Description') }}</th>
                    <th>{{ __('Assigned Users') }}</th>
                    <th class="text-end">{{ __('Actions') }}</th>
                </tr>
            </thead>
            <tbody>
                @forelse ($roles as $role)
                    <tr>
                        <td class="fw-bold text-dark">
                            <i class="bi bi-shield-check me-2 text-success" style="opacity:0.7;"></i>{{ $role->name }}
                        </td>
                        <td>
                            <span class="badge-slug">{{ $role->slug }}</span>
                        </td>
                        <td class="text-muted">{{ $role->description ?? '-' }}</td>
                        <td>
                            <span class="badge-soft-info"><i class="bi bi-people me-1"></i>{{ $role->users_count }} {{ __('users') }}</span>
                        </td>
                        <td class="text-end">
                            <div class="btn-group btn-group-sm">
                                <a href="{{ route('admin.roles.edit', $role) }}" class="btn btn-light border text-primary rounded-2 me-1" title="Edit"><i class="bi bi-pencil"></i></a>
                                @if ($role->users_count === 0)
                                    <form action="{{ route('admin.roles.destroy', $role) }}" method="POST" class="d-inline" onsubmit="return confirm('Are you sure you want to delete this role?')">
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
                            {{ __('No roles found.') }}
                        </td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>
</div>
@endsection
