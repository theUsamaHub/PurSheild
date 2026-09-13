<aside class="sidebar d-none d-lg-flex flex-column" id="sidebar">
    <div class="p-3 border-bottom border-secondary">
        <a href="{{ route('admin.dashboard') }}" class="text-decoration-none d-flex align-items-center">
            <div class="d-flex align-items-center justify-content-center rounded" style="width:36px;height:36px;background:linear-gradient(135deg,#1a6b3c,#2e9e5a);">
                <i class="bi bi-shield-check text-white fs-6"></i>
            </div>
            <span class="text-white fw-semibold ms-2 fs-6">FurShield</span>
        </a>
    </div>

    <nav class="flex-grow-1 py-3 overflow-auto">
        <ul class="nav flex-column">
            <li class="nav-item">
                <a class="nav-link {{ request()->routeIs('admin.dashboard') ? 'active' : '' }}" href="{{ route('admin.dashboard') }}">
                    <i class="bi bi-speedometer2"></i> {{ __('Dashboard') }}
                </a>
            </li>
            <li class="nav-item">
                <a class="nav-link {{ request()->routeIs('profile.edit') ? 'active' : '' }}" href="{{ route('profile.edit') }}">
                    <i class="bi bi-person"></i> {{ __('Profile') }}
                </a>
            </li>

            <li class="nav-item mt-2">
                <small class="text-uppercase text-secondary px-3 fw-semibold" style="font-size:0.7rem;letter-spacing:0.05em;">{{ __('Management') }}</small>
            </li>
            <li class="nav-item">
                <a class="nav-link {{ request()->routeIs('admin.verification.*') ? 'active' : '' }}" href="{{ route('admin.verification.index') }}">
                    <i class="bi bi-patch-check"></i> {{ __('Verify Vets & Shelters') }}
                    @php $vc = cache()->remember('verification.pending_count', 60, fn() => \App\Models\VetProfile::where('is_verified', false)->count() + \App\Models\ShelterProfile::where('is_verified', false)->count()); @endphp
                    @if ($vc > 0)<span class="badge bg-warning ms-1">{{ $vc }}</span>@endif
                </a>
            </li>
            <li class="nav-item"><a class="nav-link {{ request()->routeIs('admin.users.*') ? 'active' : '' }}" href="{{ route('admin.users.index') }}"><i class="bi bi-people"></i> {{ __('Users') }}</a></li>
            <li class="nav-item"><a class="nav-link {{ request()->routeIs('admin.roles.*') ? 'active' : '' }}" href="{{ route('admin.roles.index') }}"><i class="bi bi-shield-check"></i> {{ __('Roles') }}</a></li>
            <li class="nav-item"><a class="nav-link {{ request()->routeIs('admin.reviews.*') ? 'active' : '' }}" href="{{ route('admin.reviews.index') }}"><i class="bi bi-star"></i> {{ __('Reviews') }}</a></li>

            <li class="nav-item mt-2">
                <small class="text-uppercase text-secondary px-3 fw-semibold" style="font-size:0.7rem;letter-spacing:0.05em;">{{ __('E-Commerce') }}</small>
            </li>
            <li class="nav-item"><a class="nav-link {{ request()->routeIs('admin.products.*') ? 'active' : '' }}" href="{{ route('admin.products.index') }}"><i class="bi bi-box-seam"></i> {{ __('Products') }}</a></li>
            <li class="nav-item"><a class="nav-link {{ request()->routeIs('admin.product-categories.*') ? 'active' : '' }}" href="{{ route('admin.product-categories.index') }}"><i class="bi bi-grid"></i> {{ __('Product Categories') }}</a></li>
            <li class="nav-item"><a class="nav-link {{ request()->routeIs('admin.care-content.*') ? 'active' : '' }}" href="{{ route('admin.care-content.index') }}"><i class="bi bi-journal-richtext"></i> {{ __('Care Content') }}</a></li>

            <li class="nav-item mt-2">
                <small class="text-uppercase text-secondary px-3 fw-semibold" style="font-size:0.7rem;letter-spacing:0.05em;">{{ __('Veterinary') }}</small>
            </li>
            <li class="nav-item"><a class="nav-link {{ request()->routeIs('admin.species.*') ? 'active' : '' }}" href="{{ route('admin.species.index') }}"><i class="bi bi-bug"></i> {{ __('Species') }}</a></li>
            <li class="nav-item"><a class="nav-link {{ request()->routeIs('admin.breeds.*') ? 'active' : '' }}" href="{{ route('admin.breeds.index') }}"><i class="bi bi-heart"></i> {{ __('Breeds') }}</a></li>
            <li class="nav-item"><a class="nav-link {{ request()->routeIs('admin.specializations.*') ? 'active' : '' }}" href="{{ route('admin.specializations.index') }}"><i class="bi bi-star"></i> {{ __('Specializations') }}</a></li>

            <li class="nav-item mt-2">
                <small class="text-uppercase text-secondary px-3 fw-semibold" style="font-size:0.7rem;letter-spacing:0.05em;">{{ __('Communication') }}</small>
            </li>
            <li class="nav-item">
                <a class="nav-link {{ request()->routeIs('admin.contacts.*') ? 'active' : '' }}" href="{{ route('admin.contacts.index') }}">
                    <i class="bi bi-envelope"></i> {{ __('Contacts') }}
                    @php $nc = cache()->remember('contacts.new_count', 60, fn() => \App\Models\Contact::where('status', 'new')->count()); @endphp
                    @if ($nc > 0)<span class="badge bg-danger ms-1">{{ $nc }}</span>@endif
                </a>
            </li>

            <li class="nav-item mt-2">
                <small class="text-uppercase text-secondary px-3 fw-semibold" style="font-size:0.7rem;letter-spacing:0.05em;">{{ __('System') }}</small>
            </li>
            <li class="nav-item"><a class="nav-link {{ request()->routeIs('admin.notifications.*') ? 'active' : '' }}" href="{{ route('admin.notifications.index') }}"><i class="bi bi-bell"></i> {{ __('Notifications') }} @php $nc = cache()->remember('notifications.unread.' . auth()->id(), 60, fn() => auth()->user()->unreadNotifications()->count()); @endphp @if($nc > 0)<span class="badge bg-danger ms-1">{{ $nc }}</span>@endif</a></li>
            <li class="nav-item"><a class="nav-link {{ request()->routeIs('admin.activity-logs.*') ? 'active' : '' }}" href="{{ route('admin.activity-logs.index') }}"><i class="bi bi-clock-history"></i> {{ __('Activity') }}</a></li>
            <li class="nav-item"><a class="nav-link {{ request()->routeIs('admin.sessions.*') ? 'active' : '' }}" href="{{ route('admin.sessions.index') }}"><i class="bi bi-person-badge"></i> {{ __('Sessions') }}</a></li>
            <li class="nav-item"><a class="nav-link {{ request()->routeIs('admin.maintenance.*') ? 'active' : '' }}" href="{{ route('admin.maintenance.index') }}"><i class="bi bi-shield-exclamation"></i> {{ __('Maintenance') }}</a></li>
            <li class="nav-item"><a class="nav-link {{ request()->routeIs('admin.health.*') ? 'active' : '' }}" href="{{ route('admin.health.index') }}"><i class="bi bi-heart-pulse"></i> {{ __('Health') }}</a></li>
            <li class="nav-item"><a class="nav-link {{ request()->routeIs('admin.logs.*') ? 'active' : '' }}" href="{{ route('admin.logs.index') }}"><i class="bi bi-journal-text"></i> {{ __('Logs') }}</a></li>
            <li class="nav-item"><a class="nav-link {{ request()->routeIs('admin.backup.*') ? 'active' : '' }}" href="{{ route('admin.backup.index') }}"><i class="bi bi-database"></i> {{ __('Backup') }}</a></li>
        </ul>
    </nav>

    <div class="p-3 border-top border-secondary">
        <div class="d-flex align-items-center">
            <div class="rounded-circle d-flex align-items-center justify-content-center" style="width:36px;height:36px;background:linear-gradient(135deg,#1a6b3c,#2e9e5a);">
                <span class="text-white fw-semibold" style="font-size:0.875rem;">{{ substr(Auth::user()->name, 0, 1) }}</span>
            </div>
            <div class="ms-2 overflow-hidden">
                <div class="text-white fw-medium text-truncate" style="font-size:0.875rem;">{{ Auth::user()->name }}</div>
                <div class="text-secondary text-truncate" style="font-size:0.75rem;">{{ __('Administrator') }}</div>
            </div>
        </div>
    </div>
</aside>

<div class="sidebar-overlay d-lg-none" id="sidebarOverlay" style="display:none;position:fixed;inset:0;background:rgba(0,0,0,0.5);z-index:1040;" onclick="toggleSidebar()"></div>
