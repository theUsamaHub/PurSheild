<aside class="sidebar d-none d-lg-flex flex-column" id="sidebar">
    <div class="p-3 border-bottom border-secondary">
        <a href="{{ route('vet.dashboard') }}" class="text-decoration-none d-flex align-items-center">
            <div class="d-flex align-items-center justify-content-center rounded" style="width:36px;height:36px;background:linear-gradient(135deg,#1a6b3c,#2e9e5a);">
                <i class="bi bi-shield-check text-white fs-6"></i>
            </div>
            <span class="text-white fw-semibold ms-2 fs-6">FurShield</span>
        </a>
    </div>

    <nav class="flex-grow-1 py-3 overflow-auto">
        <ul class="nav flex-column">
            <li class="nav-item">
                <a class="nav-link {{ request()->routeIs('vet.dashboard') ? 'active' : '' }}" href="{{ route('vet.dashboard') }}">
                    <i class="bi bi-speedometer2"></i> {{ __('Dashboard') }}
                </a>
            </li>
            <li class="nav-item">
                <a class="nav-link {{ request()->routeIs('profile.edit') ? 'active' : '' }}" href="{{ route('profile.edit') }}">
                    <i class="bi bi-person"></i> {{ __('Profile') }}
                </a>
            </li>

            <li class="nav-item mt-2">
                <small class="text-uppercase text-secondary px-3 fw-semibold" style="font-size:0.7rem;letter-spacing:0.05em;">{{ __('Appointments') }}</small>
            </li>
            <li class="nav-item">
                <a class="nav-link {{ request()->routeIs('vet.appointments.*') ? 'active' : '' }}" href="#">
                    <i class="bi bi-calendar-check"></i> {{ __('My Appointments') }}
                </a>
            </li>
            <li class="nav-item">
                <a class="nav-link {{ request()->routeIs('vet.availability.*') ? 'active' : '' }}" href="#">
                    <i class="bi bi-clock"></i> {{ __('Availability') }}
                </a>
            </li>

            <li class="nav-item mt-2">
                <small class="text-uppercase text-secondary px-3 fw-semibold" style="font-size:0.7rem;letter-spacing:0.05em;">{{ __('Patients') }}</small>
            </li>
            <li class="nav-item">
                <a class="nav-link {{ request()->routeIs('vet.patients.*') ? 'active' : '' }}" href="#">
                    <i class="bi bi-heart-pulse"></i> {{ __('My Patients') }}
                </a>
            </li>
            <li class="nav-item">
                <a class="nav-link {{ request()->routeIs('vet.treatments.*') ? 'active' : '' }}" href="#">
                    <i class="bi bi-clipboard2-pulse"></i> {{ __('Treatments') }}
                </a>
            </li>

            <li class="nav-item mt-2">
                <small class="text-uppercase text-secondary px-3 fw-semibold" style="font-size:0.7rem;letter-spacing:0.05em;">{{ __('Account') }}</small>
            </li>
            <li class="nav-item">
                <a class="nav-link {{ request()->routeIs('vet.reviews.*') ? 'active' : '' }}" href="#">
                    <i class="bi bi-star"></i> {{ __('My Reviews') }}
                </a>
            </li>
        </ul>
    </nav>

    <div class="p-3 border-top border-secondary">
        <div class="d-flex align-items-center">
            <div class="rounded-circle d-flex align-items-center justify-content-center" style="width:36px;height:36px;background:linear-gradient(135deg,#0ea5e9,#38bdf8);">
                <span class="text-white fw-semibold" style="font-size:0.875rem;">{{ substr(Auth::user()->name, 0, 1) }}</span>
            </div>
            <div class="ms-2 overflow-hidden">
                <div class="text-white fw-medium text-truncate" style="font-size:0.875rem;">{{ Auth::user()->name }}</div>
                <div class="text-secondary text-truncate" style="font-size:0.75rem;">
                    @if(Auth::user()->status === 'active')
                        <i class="bi bi-check-circle-fill text-success" style="font-size:0.6rem;"></i> {{ __('Verified Vet') }}
                    @else
                        <i class="bi bi-clock-fill text-warning" style="font-size:0.6rem;"></i> {{ __('Pending Verification') }}
                    @endif
                </div>
            </div>
        </div>
    </div>
</aside>

<div class="sidebar-overlay d-lg-none" id="sidebarOverlay" style="display:none;position:fixed;inset:0;background:rgba(0,0,0,0.5);z-index:1040;" onclick="toggleSidebar()"></div>
