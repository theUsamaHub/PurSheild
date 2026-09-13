@extends('layouts.app')

@section('content')
    <div class="mb-4">
        <div class="d-flex justify-content-between align-items-center">
            <h2 class="h4 mb-0 fw-semibold">{{ __('Edit User') }}</h2>
            <div class="d-flex gap-2">
                <a href="{{ route('admin.users.show', $user) }}" class="btn btn-outline-info btn-sm">
                    <i class="bi bi-eye me-1"></i>{{ __('View') }}
                </a>
                <a href="{{ route('admin.users.index') }}" class="btn btn-outline-secondary btn-sm">
                    <i class="bi bi-arrow-left me-1"></i>{{ __('Back') }}
                </a>
            </div>
        </div>
    </div>

    <div class="row">
        <div class="col-lg-8">
            <div class="card">
                <div class="card-body p-4">
                    <form method="POST" action="{{ route('admin.users.update', $user) }}">
                        @csrf
                        @method('PUT')

                        <div class="mb-3">
                            <x-input-label for="name" :value="__('Name')" />
                            <x-text-input id="name" name="name" type="text" class="form-control" :value="old('name', $user->name)" required autofocus />
                            <x-input-error :messages="$errors->get('name')" class="mt-1" />
                        </div>

                        <div class="mb-3">
                            <x-input-label for="email" :value="__('Email')" />
                            <x-text-input id="email" name="email" type="email" class="form-control" :value="old('email', $user->email)" required />
                            <x-input-error :messages="$errors->get('email')" class="mt-1" />
                        </div>

                        <div class="mb-4">
                            <x-input-label :value="__('Current Roles')" />
                            <div class="d-flex gap-2 flex-wrap">
                                @forelse ($user->roles as $role)
                                    <span class="badge bg-{{ $role->slug === 'admin' ? 'primary' : 'secondary' }}">{{ $role->name }}</span>
                                @empty
                                    <span class="text-muted">{{ __('No roles assigned') }}</span>
                                @endforelse
                            </div>
                            <div class="form-text">{{ __('Roles are managed separately and cannot be changed here.') }}</div>
                        </div>

                        <div class="d-flex align-items-center gap-3">
                            <x-primary-button>{{ __('Save Changes') }}</x-primary-button>
                            <a href="{{ route('admin.users.show', $user) }}" class="text-muted text-decoration-none">{{ __('Cancel') }}</a>
                        </div>
                    </form>
                </div>
            </div>
        </div>

        <div class="col-lg-4">
            <div class="card">
                <div class="card-header">
                    <h6 class="mb-0 fw-semibold">{{ __('User Info') }}</h6>
                </div>
                <div class="card-body">
                    <p class="text-muted mb-2" style="font-size: 0.875rem;">
                        <strong>{{ __('Status:') }}</strong>
                        @if ($user->status === 'active')
                            <span class="badge bg-success">{{ __('Active') }}</span>
                        @elseif ($user->status === 'inactive')
                            <span class="badge bg-secondary">{{ __('Inactive') }}</span>
                        @elseif ($user->status === 'suspended')
                            <span class="badge bg-danger">{{ __('Suspended') }}</span>
                        @elseif ($user->status === 'pending_verification')
                            <span class="badge bg-warning text-dark">{{ __('Pending') }}</span>
                        @endif
                    </p>
                    <p class="text-muted mb-2" style="font-size: 0.875rem;">
                        <strong>{{ __('Joined:') }}</strong> {{ $user->created_at->format('M d, Y') }}
                    </p>
                    <p class="text-muted mb-0" style="font-size: 0.875rem;">
                        <strong>{{ __('Last Login:') }}</strong> {{ $user->last_login_at ? $user->last_login_at->diffForHumans() : __('Never') }}
                    </p>
                </div>
            </div>
        </div>
    </div>
@endsection
