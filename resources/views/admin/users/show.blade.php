@extends('layouts.app')

@section('content')
    <div class="mb-4">
        <div class="d-flex justify-content-between align-items-center">
            <h2 class="h4 mb-0 fw-semibold">{{ __('User Details') }}</h2>
            <div class="d-flex gap-2">
                <a href="{{ route('admin.users.edit', $user) }}" class="btn btn-outline-primary btn-sm">
                    <i class="bi bi-pencil me-1"></i>{{ __('Edit') }}
                </a>
                <a href="{{ route('admin.users.index') }}" class="btn btn-outline-secondary btn-sm">
                    <i class="bi bi-arrow-left me-1"></i>{{ __('Back') }}
                </a>
            </div>
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

    <div class="row">
        <div class="col-lg-8">
            <!-- User Info -->
            <div class="card mb-4">
                <div class="card-header">
                    <h6 class="mb-0 fw-semibold">{{ __('Profile Information') }}</h6>
                </div>
                <div class="card-body p-4">
                    <table class="table mb-0">
                        <tbody>
                            <tr>
                                <td class="fw-semibold" style="width: 200px;">{{ __('Name') }}</td>
                                <td>{{ $user->name }}</td>
                            </tr>
                            <tr>
                                <td class="fw-semibold">{{ __('Email') }}</td>
                                <td>{{ $user->email }}</td>
                            </tr>
                            <tr>
                                <td class="fw-semibold">{{ __('Phone') }}</td>
                                <td>{{ $user->phone ?: '-' }}</td>
                            </tr>
                            <tr>
                                <td class="fw-semibold">{{ __('Address') }}</td>
                                <td>{{ $user->address ?: '-' }}</td>
                            </tr>
                            <tr>
                                <td class="fw-semibold">{{ __('Roles') }}</td>
                                <td>
                                    @forelse ($user->roles as $role)
                                        <span class="badge bg-{{ $role->slug === 'admin' ? 'primary' : 'secondary' }}">{{ $role->name }}</span>
                                    @empty
                                        <span class="text-muted">{{ __('No roles assigned') }}</span>
                                    @endforelse
                                </td>
                            </tr>
                            <tr>
                                <td class="fw-semibold">{{ __('Status') }}</td>
                                <td>
                                    @if ($user->status === 'active')
                                        <span class="badge bg-success">{{ __('Active') }}</span>
                                    @elseif ($user->status === 'inactive')
                                        <span class="badge bg-secondary">{{ __('Inactive') }}</span>
                                    @elseif ($user->status === 'suspended')
                                        <span class="badge bg-danger">{{ __('Suspended') }}</span>
                                    @elseif ($user->status === 'pending_verification')
                                        <span class="badge bg-warning text-dark">{{ __('Pending Verification') }}</span>
                                    @else
                                        <span class="badge bg-secondary">{{ $user->status }}</span>
                                    @endif
                                </td>
                            </tr>
                            <tr>
                                <td class="fw-semibold">{{ __('Email Verified') }}</td>
                                <td>
                                    @if ($user->email_verified_at)
                                        <span class="badge bg-success">{{ __('Verified') }}</span>
                                    @else
                                        <span class="badge bg-warning text-dark">{{ __('Not Verified') }}</span>
                                    @endif
                                </td>
                            </tr>
                            <tr>
                                <td class="fw-semibold">{{ __('Joined') }}</td>
                                <td>{{ $user->created_at->format('M d, Y H:i:s') }}</td>
                            </tr>
                            <tr>
                                <td class="fw-semibold">{{ __('Last Login') }}</td>
                                <td>{{ $user->last_login_at ? $user->last_login_at->format('M d, Y H:i:s') : __('Never') }}</td>
                            </tr>
                        </tbody>
                    </table>
                </div>
            </div>

            <!-- Vet Profile -->
            @if ($user->vetProfile)
                <div class="card mb-4">
                    <div class="card-header">
                        <h6 class="mb-0 fw-semibold"><i class="bi bi-hospital me-1"></i>{{ __('Veterinarian Profile') }}</h6>
                    </div>
                    <div class="card-body p-4">
                        <table class="table mb-0">
                            <tbody>
                                <tr>
                                    <td class="fw-semibold" style="width: 200px;">{{ __('Qualification') }}</td>
                                    <td>{{ $user->vetProfile->qualification ?: '-' }}</td>
                                </tr>
                                <tr>
                                    <td class="fw-semibold">{{ __('Clinic Name') }}</td>
                                    <td>{{ $user->vetProfile->clinic_name ?: '-' }}</td>
                                </tr>
                                <tr>
                                    <td class="fw-semibold">{{ __('Clinic Address') }}</td>
                                    <td>{{ $user->vetProfile->clinic_address ?: '-' }}</td>
                                </tr>
                                <tr>
                                    <td class="fw-semibold">{{ __('Experience') }}</td>
                                    <td>{{ $user->vetProfile->experience_years ? $user->vetProfile->experience_years . ' years' : '-' }}</td>
                                </tr>
                                <tr>
                                    <td class="fw-semibold">{{ __('Consultation Fee') }}</td>
                                    <td>{{ $user->vetProfile->consultation_fee ? '$' . number_format($user->vetProfile->consultation_fee, 2) : '-' }}</td>
                                </tr>
                                <tr>
                                    <td class="fw-semibold">{{ __('Verified') }}</td>
                                    <td>
                                        @if ($user->vetProfile->is_verified)
                                            <span class="badge bg-success">{{ __('Verified') }}</span>
                                        @else
                                            <span class="badge bg-warning text-dark">{{ __('Not Verified') }}</span>
                                        @endif
                                    </td>
                                </tr>
                                <tr>
                                    <td class="fw-semibold">{{ __('Bio') }}</td>
                                    <td>{{ $user->vetProfile->bio ?: '-' }}</td>
                                </tr>
                            </tbody>
                        </table>
                    </div>
                </div>
            @endif

            <!-- Shelter Profile -->
            @if ($user->shelterProfile)
                <div class="card mb-4">
                    <div class="card-header">
                        <h6 class="mb-0 fw-semibold"><i class="bi bi-building me-1"></i>{{ __('Shelter Profile') }}</h6>
                    </div>
                    <div class="card-body p-4">
                        <table class="table mb-0">
                            <tbody>
                                <tr>
                                    <td class="fw-semibold" style="width: 200px;">{{ __('Shelter Name') }}</td>
                                    <td>{{ $user->shelterProfile->shelter_name ?: '-' }}</td>
                                </tr>
                                <tr>
                                    <td class="fw-semibold">{{ __('City') }}</td>
                                    <td>{{ $user->shelterProfile->city ?: '-' }}</td>
                                </tr>
                                <tr>
                                    <td class="fw-semibold">{{ __('Address') }}</td>
                                    <td>{{ $user->shelterProfile->address ?: '-' }}</td>
                                </tr>
                                <tr>
                                    <td class="fw-semibold">{{ __('Capacity') }}</td>
                                    <td>{{ $user->shelterProfile->capacity ?: '-' }}</td>
                                </tr>
                                <tr>
                                    <td class="fw-semibold">{{ __('Contact Number') }}</td>
                                    <td>{{ $user->shelterProfile->contact_number ?: '-' }}</td>
                                </tr>
                                <tr>
                                    <td class="fw-semibold">{{ __('Website') }}</td>
                                    <td>{{ $user->shelterProfile->website ?: '-' }}</td>
                                </tr>
                                <tr>
                                    <td class="fw-semibold">{{ __('Verified') }}</td>
                                    <td>
                                        @if ($user->shelterProfile->is_verified)
                                            <span class="badge bg-success">{{ __('Verified') }}</span>
                                        @else
                                            <span class="badge bg-warning text-dark">{{ __('Not Verified') }}</span>
                                        @endif
                                    </td>
                                </tr>
                                <tr>
                                    <td class="fw-semibold">{{ __('Description') }}</td>
                                    <td>{{ $user->shelterProfile->description ?: '-' }}</td>
                                </tr>
                            </tbody>
                        </table>
                    </div>
                </div>
            @endif
        </div>

        <!-- Actions Sidebar -->
        <div class="col-lg-4">
            <div class="card mb-4">
                <div class="card-header">
                    <h6 class="mb-0 fw-semibold">{{ __('Actions') }}</h6>
                </div>
                <div class="card-body d-flex flex-column gap-2">
                    <a href="{{ route('admin.users.edit', $user) }}" class="btn btn-outline-primary btn-sm w-100">
                        <i class="bi bi-pencil me-1"></i>{{ __('Edit User') }}
                    </a>

                    @if ($user->id !== auth()->id())
                        @if ($user->status !== 'suspended')
                            <form action="{{ route('admin.users.toggle-status', $user) }}" method="POST">
                                @csrf
                                <button type="submit" class="btn btn-outline-{{ $user->status === 'active' ? 'warning' : 'success' }} btn-sm w-100">
                                    <i class="bi bi-{{ $user->status === 'active' ? 'pause-circle' : 'play-circle' }} me-1"></i>
                                    {{ $user->status === 'active' ? __('Deactivate User') : __('Activate User') }}
                                </button>
                            </form>
                        @endif

                        @if ($user->status === 'suspended')
                            <form action="{{ route('admin.users.reinstate', $user) }}" method="POST">
                                @csrf
                                <button type="submit" class="btn btn-outline-success btn-sm w-100">
                                    <i class="bi bi-check-circle me-1"></i>{{ __('Reinstate User') }}
                                </button>
                            </form>
                        @endif

                        @if ($user->status === 'active' || $user->status === 'inactive')
                            <button type="button" class="btn btn-outline-danger btn-sm w-100" data-bs-toggle="modal" data-bs-target="#suspendModal{{ $user->id }}">
                                <i class="bi bi-slash-circle me-1"></i>{{ __('Suspend User') }}
                            </button>
                        @endif

                        <hr class="my-1">

                        <form action="{{ route('admin.users.destroy', $user) }}" method="POST" onsubmit="return confirm('{{ __('Are you sure you want to delete this user? This action cannot be undone.') }}')">
                            @csrf
                            @method('DELETE')
                            <button type="submit" class="btn btn-danger btn-sm w-100">
                                <i class="bi bi-trash me-1"></i>{{ __('Delete User') }}
                            </button>
                        </form>
                    @else
                        <div class="alert alert-warning py-2 mb-0" style="font-size: 0.875rem;">
                            {{ __('You cannot perform actions on your own account from here.') }}
                        </div>
                    @endif
                </div>
            </div>

            <!-- Suspension Info -->
            @if ($user->status === 'suspended' && $user->suspension_reason)
                <div class="card border-danger">
                    <div class="card-header bg-danger text-white">
                        <h6 class="mb-0 fw-semibold"><i class="bi bi-info-circle me-1"></i>{{ __('Suspension Details') }}</h6>
                    </div>
                    <div class="card-body">
                        <p class="mb-1"><strong>{{ __('Reason:') }}</strong></p>
                        <p class="text-muted">{{ $user->suspension_reason }}</p>
                        @if ($user->suspended_at)
                            <p class="mb-0 text-muted" style="font-size: 0.875rem;">
                                <strong>{{ __('Suspended on:') }}</strong> {{ $user->suspended_at->format('M d, Y H:i:s') }}
                            </p>
                        @endif
                    </div>
                </div>
            @endif
        </div>
    </div>

    @if ($user->id !== auth()->id() && in_array($user->status, ['active', 'inactive']))
        @include('admin.users._suspend-modal', ['suspendUser' => $user])
    @endif
@endsection
