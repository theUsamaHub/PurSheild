@extends('layouts.app')

@section('content')
    <div class="mb-4">
        <div class="d-flex justify-content-between align-items-center">
            <h2 class="h4 mb-0 fw-semibold">{{ __('Users') }}</h2>
        </div>
    </div>

    @if (session('success'))
        <div class="alert alert-success alert-dismissible fade show" role="alert">
            {{ session('success') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    @endif

    @if (session('error'))
        <div class="alert alert-danger alert-dismissible fade show" role="alert">
            {{ session('error') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    @endif

    <!-- Stats -->
    <div class="row g-3 mb-4">
        <div class="col-md-3">
            <div class="card border-start border-primary border-4 h-100">
                <div class="card-body">
                    <div class="text-muted" style="font-size:0.75rem;">{{ __('Total Users') }}</div>
                    <div class="fs-4 fw-bold">{{ $stats['total'] }}</div>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card border-start border-success border-4 h-100">
                <div class="card-body">
                    <div class="text-muted" style="font-size:0.75rem;">{{ __('Active') }}</div>
                    <div class="fs-4 fw-bold">{{ $stats['active'] }}</div>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card border-start border-danger border-4 h-100">
                <div class="card-body">
                    <div class="text-muted" style="font-size:0.75rem;">{{ __('Suspended') }}</div>
                    <div class="fs-4 fw-bold">{{ $stats['suspended'] }}</div>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card border-start border-warning border-4 h-100">
                <div class="card-body">
                    <div class="text-muted" style="font-size:0.75rem;">{{ __('Pending Verification') }}</div>
                    <div class="fs-4 fw-bold">{{ $stats['pending_verification'] }}</div>
                </div>
            </div>
        </div>
    </div>
    <div class="row g-3 mb-4">
        <div class="col-md-3">
            <div class="card border-start border-dark border-4 h-100">
                <div class="card-body">
                    <div class="text-muted" style="font-size:0.75rem;">{{ __('Rejected') }}</div>
                    <div class="fs-4 fw-bold">{{ $stats['rejected'] }}</div>
                </div>
            </div>
        </div>
    </div>

    <!-- Search & Filters -->
    <div class="card mb-4">
        <div class="card-body">
            <form method="GET" action="{{ route('admin.users.index') }}" class="row g-3">
                <div class="col-md-3">
                    <input type="text" class="form-control" name="search" placeholder="{{ __('Search users...') }}" value="{{ request('search') }}">
                </div>
                <div class="col-md-2">
                    <select class="form-select" name="role">
                        <option value="">{{ __('All Roles') }}</option>
                        @foreach ($roles as $role)
                            <option value="{{ $role->slug }}" {{ request('role') === $role->slug ? 'selected' : '' }}>{{ $role->name }}</option>
                        @endforeach
                    </select>
                </div>
                <div class="col-md-2">
                    <select class="form-select" name="status">
                        <option value="">{{ __('All Status') }}</option>
                        <option value="active" {{ request('status') === 'active' ? 'selected' : '' }}>{{ __('Active') }}</option>
                        <option value="inactive" {{ request('status') === 'inactive' ? 'selected' : '' }}>{{ __('Inactive') }}</option>
                        <option value="suspended" {{ request('status') === 'suspended' ? 'selected' : '' }}>{{ __('Suspended') }}</option>
                        <option value="pending_verification" {{ request('status') === 'pending_verification' ? 'selected' : '' }}>{{ __('Pending Verification') }}</option>
                    </select>
                </div>
                <div class="col-md-3 d-flex gap-2">
                    <button type="submit" class="btn btn-outline-secondary flex-grow-1">
                        <i class="bi bi-search me-1"></i>{{ __('Filter') }}
                    </button>
                    <a href="{{ route('admin.users.index') }}" class="btn btn-outline-danger"><i class="bi bi-x"></i></a>
                </div>
            </form>
        </div>
    </div>

    <!-- Users Table -->
    <div class="card">
        <div class="card-body p-0">
            <div class="table-responsive">
                <table class="table table-hover mb-0">
                    <thead>
                        <tr>
                            <th>{{ __('Name') }}</th>
                            <th>{{ __('Email') }}</th>
                            <th>{{ __('Roles') }}</th>
                            <th>{{ __('Status') }}</th>
                            <th>{{ __('Last Login') }}</th>
                            <th class="text-end">{{ __('Actions') }}</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse ($users as $user)
                            <tr>
                                <td class="fw-medium">{{ $user->name }}</td>
                                <td class="text-muted">{{ $user->email }}</td>
                                <td>
                                    @forelse ($user->roles as $role)
                                        <span class="badge bg-{{ $role->slug === 'admin' ? 'primary' : 'secondary' }}">{{ $role->name }}</span>
                                    @empty
                                        <span class="text-muted">-</span>
                                    @endforelse
                                </td>
                                @php
                                    $isRejected = in_array($user->status, ['pending_verification'])
                                        && (($user->vetProfile && $user->vetProfile->rejected_at)
                                            || ($user->shelterProfile && $user->shelterProfile->rejected_at));
                                @endphp
                                <td>
                                    @if ($isRejected)
                                        <span class="badge bg-danger">{{ __('Rejected') }}</span>
                                    @elseif ($user->status === 'active')
                                        <span class="badge bg-success">{{ __('Active') }}</span>
                                    @elseif ($user->status === 'inactive')
                                        <span class="badge bg-secondary">{{ __('Inactive') }}</span>
                                    @elseif ($user->status === 'suspended')
                                        <span class="badge bg-danger">{{ __('Suspended') }}</span>
                                    @elseif ($user->status === 'pending_verification')
                                        <span class="badge bg-warning text-dark">{{ __('Pending') }}</span>
                                    @else
                                        <span class="badge bg-secondary">{{ $user->status }}</span>
                                    @endif
                                </td>
                                <td class="text-muted">
                                    {{ $user->last_login_at ? $user->last_login_at->diffForHumans() : __('Never') }}
                                </td>
                                <td class="text-end">
                                    <div class="btn-group btn-group-sm">
                                        <a href="{{ route('admin.users.show', $user) }}" class="btn btn-outline-info" title="{{ __('View') }}">
                                            <i class="bi bi-eye"></i>
                                        </a>
                                        @if ($user->id !== auth()->id())
                                            @if ($isRejected)
                                                @php
                                                    $profile = $user->vetProfile ?? $user->shelterProfile;
                                                    $type = $user->vetProfile ? 'vet' : 'shelter';
                                                @endphp
                                                <form action="{{ route("admin.verification.{$type}.approve", $profile) }}" method="POST" class="d-inline">
                                                    @csrf
                                                    <button type="submit" class="btn btn-outline-success" title="{{ __('Re-approve') }}">
                                                        <i class="bi bi-check-circle"></i>
                                                    </button>
                                                </form>
                                            @elseif ($user->status === 'suspended')
                                                <form action="{{ route('admin.users.reinstate', $user) }}" method="POST" class="d-inline">
                                                    @csrf
                                                    <button type="submit" class="btn btn-outline-success" title="{{ __('Reinstate') }}">
                                                        <i class="bi bi-play-circle"></i>
                                                    </button>
                                                </form>
                                            @else
                                                <form action="{{ route('admin.users.toggle-status', $user) }}" method="POST" class="d-inline">
                                                    @csrf
                                                    <button type="submit" class="btn btn-outline-{{ $user->status === 'active' ? 'warning' : 'success' }}" title="{{ $user->status === 'active' ? __('Deactivate') : __('Activate') }}">
                                                        <i class="bi bi-{{ $user->status === 'active' ? 'pause-circle' : 'play-circle' }}"></i>
                                                    </button>
                                                </form>
                                                @if ($user->status === 'active' || $user->status === 'inactive')
                                                    <button type="button" class="btn btn-outline-danger" title="{{ __('Suspend') }}" data-bs-toggle="modal" data-bs-target="#suspendModal{{ $user->id }}">
                                                        <i class="bi bi-slash-circle"></i>
                                                    </button>
                                                @endif
                                            @endif
                                            <form action="{{ route('admin.users.destroy', $user) }}" method="POST" class="d-inline" onsubmit="return confirm('{{ __('Are you sure you want to delete this user?') }}')">
                                                @csrf
                                                @method('DELETE')
                                                <button type="submit" class="btn btn-outline-danger" title="{{ __('Delete') }}">
                                                    <i class="bi bi-trash"></i>
                                                </button>
                                            </form>
                                        @endif
                                    </div>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="6" class="text-center py-5">
                                    <div class="empty-state">
                                        <i class="bi bi-people"></i>
                                        <p>{{ __('No users found.') }}</p>
                                    </div>
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>

        @if ($users->hasPages())
            <div class="card-footer bg-white">
                {{ $users->links() }}
            </div>
        @endif
    </div>

    <!-- Suspend Modals -->
    @foreach ($users as $user)
        @if ($user->id !== auth()->id() && in_array($user->status, ['active', 'inactive']))
            @include('admin.users._suspend-modal', ['suspendUser' => $user])
        @endif
    @endforeach
@endsection
