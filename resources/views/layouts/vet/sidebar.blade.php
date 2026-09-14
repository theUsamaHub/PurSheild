<aside class="pc-sidebar d-none d-lg-flex flex-column" id="sidebar">
    <div class="pc-sidebar-brand">
        <a href="{{ route('vet.dashboard') }}" class="text-decoration-none d-flex align-items-center">
            <div class="pc-brand-icon">
                <svg viewBox="0 0 24 24" fill="var(--pc-primary)">
                    <path d="M12 15.5c-2.6 0-6 1.55-6 3.6 0 1.05.95 1.9 2.13 1.9.98 0 1.7-.5 2.6-.5.6 0 1.02.5 1.27.5s.67-.5 1.27-.5c.9 0 1.62.5 2.6.5 1.18 0 2.13-.85 2.13-1.9 0-2.05-3.4-3.6-6-3.6z"/>
                    <circle cx="5.2" cy="10.2" r="2.1"/>
                    <circle cx="9.4" cy="6.4" r="2.1"/>
                    <circle cx="14.6" cy="6.4" r="2.1"/>
                    <circle cx="18.8" cy="10.2" r="2.1"/>
                </svg>
            </div>
            <div class="ms-2">
                <div class="pc-brand-title">{{ config('app.name', 'PawCare') }}</div>
                <div class="pc-brand-sub">{{ __('For a Healthier, Happier Tomorrow') }}</div>
            </div>
        </a>
    </div>

    <nav class="pc-nav flex-grow-1 overflow-auto">
        <ul class="nav flex-column">
            <li class="nav-item">
                <a class="nav-link {{ request()->routeIs('vet.dashboard') ? 'active' : '' }}" href="{{ route('vet.dashboard') }}">
                    <i class="bi bi-house-door-fill"></i> {{ __('Dashboard') }}
                </a>
            </li>
            <li class="nav-item">
                <a class="nav-link {{ request()->routeIs('profile.edit') ? 'active' : '' }}" href="{{ route('profile.edit') }}">
                    <i class="bi bi-person"></i> {{ __('My Profile') }}
                </a>
            </li>
            <li class="nav-item">
                <a class="nav-link {{ request()->routeIs('vet.availability.*') ? 'active' : '' }}" href="{{ route('vet.availability.index') }}">
                    <i class="bi bi-calendar3"></i> {{ __('Availability') }}
                </a>
            </li>
            <li class="nav-item">
                <a class="nav-link {{ request()->routeIs('vet.appointments.*') ? 'active' : '' }}" href="{{ route('vet.appointments.index') }}">
                    <i class="bi bi-clipboard2-check"></i> {{ __('Appointments') }}
                </a>
            </li>
            <li class="nav-item">
                <a class="nav-link {{ request()->routeIs('vet.patients.*') ? 'active' : '' }}" href="{{ route('vet.patients.index') }}">
                    <i class="bi bi-people"></i> {{ __('My Patients') }}
                </a>
            </li>
            <li class="nav-item">
                <a class="nav-link {{ request()->routeIs('vet.treatments.*') ? 'active' : '' }}" href="{{ route('vet.treatments.index') }}">
                    <i class="bi bi-heart-pulse"></i> {{ __('Treatments') }}
                </a>
            </li>
            <li class="nav-item">
                <a class="nav-link {{ request()->routeIs('vet.reviews.*') ? 'active' : '' }}" href="{{ route('vet.reviews.index') }}">
                    <i class="bi bi-star"></i> {{ __('Reviews') }}
                </a>
            </li>
            <li class="nav-item">
                <a class="nav-link {{ request()->routeIs('vet.notifications.*') ? 'active' : '' }}" href="{{ route('vet.notifications.index') }}">
                    <i class="bi bi-bell"></i> {{ __('Notifications') }}
                    @php($unread = $unreadNotificationsCount ?? (Auth::user()->unreadNotifications->count() ?? 0))
                    @if($unread > 0)
                        <span class="nav-badge">{{ $unread }}</span>
                    @endif
                </a>
            </li>

            <li class="pc-nav-divider"></li>

            <li class="nav-item">
                <form method="POST" action="{{ route('logout') }}">
                    @csrf
                    <button type="submit" class="nav-link border-0 bg-transparent w-100 text-start">
                        <i class="bi bi-box-arrow-right"></i> {{ __('Logout') }}
                    </button>
                </form>
            </li>
        </ul>
    </nav>

    <div class="pc-sidebar-footer">
        <p class="quote mb-0">&ldquo;{{ __('Healthy Pets') }}<br>{{ __('Happier Lives') }}&rdquo;</p>
    </div>

    <svg class="pc-paw-bg" viewBox="0 0 200 200" fill="#fff">
        <path d="M100 130c-24 0-56 14-56 33 0 9.5 8.7 17 19.4 17 8.9 0 15.5-4.5 23.6-4.5 5.5 0 9.3 4.5 11.6 4.5s6.1-4.5 11.6-4.5c8.1 0 14.7 4.5 23.6 4.5 10.7 0 19.4-7.5 19.4-17 0-19-32-33-53.2-33z"/>
        <circle cx="47" cy="93" r="19"/>
        <circle cx="85" cy="58" r="19"/>
        <circle cx="132" cy="58" r="19"/>
        <circle cx="170" cy="93" r="19"/>
    </svg>
</aside>

<div class="sidebar-overlay d-lg-none" id="sidebarOverlay" style="display:none;position:fixed;inset:0;background:rgba(0,0,0,0.5);z-index:1040;" onclick="toggleSidebar()"></div>