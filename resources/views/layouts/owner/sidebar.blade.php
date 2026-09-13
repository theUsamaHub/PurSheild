<aside class="sidebar d-none d-lg-flex flex-column" id="sidebar">
    <div class="p-3 border-bottom border-secondary">
        <a href="{{ route('owner.dashboard') }}" class="text-decoration-none d-flex align-items-center">
            <div class="d-flex align-items-center justify-content-center rounded" style="width:36px;height:36px;background:linear-gradient(135deg,#1a6b3c,#2e9e5a);">
                <i class="bi bi-shield-check text-white fs-6"></i>
            </div>
            <span class="text-white fw-semibold ms-2 fs-6">FurShield</span>
        </a>
    </div>

    <nav class="flex-grow-1 py-3 overflow-auto">
        <ul class="nav flex-column">
            <li class="nav-item">
                <a class="nav-link {{ request()->routeIs('owner.dashboard') ? 'active' : '' }}" href="{{ route('owner.dashboard') }}">
                    <i class="bi bi-speedometer2"></i> {{ __('Dashboard') }}
                </a>
            </li>
            <li class="nav-item">
                <a class="nav-link {{ request()->routeIs('profile.edit') ? 'active' : '' }}" href="{{ route('profile.edit') }}">
                    <i class="bi bi-person"></i> {{ __('Profile') }}
                </a>
            </li>

            <li class="nav-item mt-2">
                <small class="text-uppercase text-secondary px-3 fw-semibold" style="font-size:0.7rem;letter-spacing:0.05em;">{{ __('My Pets') }}</small>
            </li>
            <li class="nav-item">
                <a class="nav-link {{ request()->routeIs('owner.pets.*') ? 'active' : '' }}" href="{{ route('owner.pets.index') }}">
                    <i class="bi bi-heart"></i> {{ __('My Pets') }}
                </a>
            </li>

            <li class="nav-item mt-2">
                <small class="text-uppercase text-secondary px-3 fw-semibold" style="font-size:0.7rem;letter-spacing:0.05em;">{{ __('Services') }}</small>
            </li>
            <li class="nav-item">
                <a class="nav-link {{ request()->routeIs('owner.appointments.*') ? 'active' : '' }}" href="{{ route('owner.appointments.index') }}">
                    <i class="bi bi-calendar-check"></i> {{ __('Appointments') }}
                </a>
            </li>
            <li class="nav-item">
                <a class="nav-link {{ request()->routeIs('owner.browse-vets') ? 'active' : '' }}" href="{{ route('owner.browse-vets') }}">
                    <i class="bi bi-heartbeat"></i> {{ __('Browse Vets') }}
                </a>
            </li>
            <li class="nav-item">
                <a class="nav-link {{ request()->routeIs('owner.browse-adoption') ? 'active' : '' }}" href="{{ route('owner.browse-adoption') }}">
                    <i class="bi bi-bookmark-heart"></i> {{ __('Adoption') }}
                </a>
            </li>

            <li class="nav-item mt-2">
                <small class="text-uppercase text-secondary px-3 fw-semibold" style="font-size:0.7rem;letter-spacing:0.05em;">{{ __('Shop') }}</small>
            </li>
            <li class="nav-item">
                <a class="nav-link {{ request()->routeIs('owner.products.*') ? 'active' : '' }}" href="{{ route('owner.products.index') }}">
                    <i class="bi bi-shop"></i> {{ __('Products') }}
                </a>
            </li>
            <li class="nav-item">
                <a class="nav-link {{ request()->routeIs('owner.cart.*') ? 'active' : '' }}" href="{{ route('owner.cart.index') }}">
                    <i class="bi bi-cart3"></i> {{ __('My Cart') }}
                </a>
            </li>
            <li class="nav-item">
                <a class="nav-link {{ request()->routeIs('owner.orders.*') ? 'active' : '' }}" href="{{ route('owner.orders.index') }}">
                    <i class="bi bi-receipt"></i> {{ __('Orders') }}
                </a>
            </li>

            <li class="nav-item mt-2">
                <small class="text-uppercase text-secondary px-3 fw-semibold" style="font-size:0.7rem;letter-spacing:0.05em;">{{ __('Learn') }}</small>
            </li>
            <li class="nav-item">
                <a class="nav-link {{ request()->routeIs('owner.care.*') ? 'active' : '' }}" href="{{ route('owner.care.index') }}">
                    <i class="bi bi-journal-bookmark"></i> {{ __('Care Content') }}
                </a>
            </li>
        </ul>
    </nav>

    <div class="p-3 border-top border-secondary">
        <div class="d-flex align-items-center">
            <div class="rounded-circle d-flex align-items-center justify-content-center" style="width:36px;height:36px;background:linear-gradient(135deg,#1a6b3c,#2e9e5a);">
                <span class="text-white fw-semibold" style="font-size:0.875rem;">{{ substr(Auth::user()->name, 0, 1) }}</span>
            </div>
            <div class="ms-2 overflow-hidden">
                <div class="text-white fw-medium text-truncate" style="font-size:0.875rem;">{{ Auth::user()->name }}</div>
                <div class="text-secondary text-truncate" style="font-size:0.75rem;">{{ __('Pet Owner') }}</div>
            </div>
        </div>
    </div>
</aside>

<div class="sidebar-overlay d-lg-none" id="sidebarOverlay" style="display:none;position:fixed;inset:0;background:rgba(0,0,0,0.5);z-index:1040;" onclick="toggleSidebar()"></div>
