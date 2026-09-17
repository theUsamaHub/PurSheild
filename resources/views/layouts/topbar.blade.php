<!-- Top Navbar -->
<nav class="navbar navbar-expand-lg bg-white border-bottom px-4" style="min-height: 64px; border-color: #e6f0eb !important;">
    <div class="d-flex align-items-center">
        <!-- Mobile Hamburger -->
        <button class="btn btn-link text-dark d-lg-none me-2 p-1" onclick="toggleSidebar()">
            <i class="bi bi-list fs-4"></i>
        </button>
        <span class="fw-bold d-none d-lg-block" style="font-size:1rem;color:#074f3e;">
            @yield('page-title', __('Admin Dashboard'))
        </span>
    </div>

    <div class="ms-auto d-flex align-items-center">
        <!-- Search Button -->
        <button class="btn btn-light border d-flex align-items-center me-3" style="border-radius:20px;padding:6px 16px;font-size:0.85rem;background:#f4fbf8;border-color:#d4ebe2 !important;color:#087657;"
            onclick="window.dispatchEvent(new CustomEvent('toggle-command-palette'))">
            <i class="bi bi-search me-2 text-success"></i>
            <span class="d-none d-md-inline font-weight-medium">Search system...</span>
            <kbd class="bg-white text-muted border ms-2 px-1 rounded" style="font-size:0.65rem;">Ctrl+K</kbd>
        </button>

        <!-- Notifications -->
        @php
            $unreadCount = 0;
            if (auth()->check()) {
                $unreadCount = \App\Models\FurshieldNotification::where('user_id', auth()->id())->where('is_read', false)->count();
            }
        @endphp
        <div class="dropdown me-3" x-data="{ open: false }" @click.outside="open = false">
            <button class="btn btn-light rounded-circle text-dark p-2 position-relative d-flex align-items-center justify-content-center" style="width:38px;height:38px;background:#f4fbf8;border:1px solid #d4ebe2;" @click="open = !open" aria-expanded="false">
                <i class="bi bi-bell text-success fs-6"></i>
                @if ($unreadCount > 0)
                    <span class="position-absolute top-0 start-100 translate-middle badge rounded-pill bg-danger" style="font-size: 0.55rem;">
                        {{ $unreadCount > 99 ? '99+' : $unreadCount }}
                    </span>
                @endif
            </button>
            <div class="dropdown-menu dropdown-menu-end shadow-sm border-0" style="width:360px;max-width:90vw;right:0;left:auto;border-radius:12px;" :class="{ show: open }">
                <div class="d-flex justify-content-between align-items-center px-3 py-2 border-bottom">
                    <h6 class="mb-0 fw-semibold" style="font-size: 0.875rem;color:#074f3e;">{{ __('Notifications') }}</h6>
                </div>
                <div style="max-height: 300px; overflow-y: auto;">
                    <div class="dropdown-item text-center text-muted py-3" style="font-size: 0.875rem;">
                        <i class="bi bi-bell-slash d-block mb-1 fs-5 text-success"></i>
                        {{ __('No notifications') }}
                    </div>
                </div>
            </div>
        </div>

        <!-- User Dropdown -->
        <div class="dropdown">
            <button class="btn btn-link text-dark text-decoration-none d-flex align-items-center p-0" data-bs-toggle="dropdown">
                <div class="rounded-circle d-flex align-items-center justify-content-center shadow-sm" style="width: 36px; height: 36px;background:linear-gradient(135deg,#087657,#48ddab);">
                    <span class="text-white fw-bold" style="font-size: 0.85rem;">{{ substr(Auth::user()->name, 0, 1) }}</span>
                </div>
                <span class="ms-2 d-none d-md-inline fw-semibold" style="font-size: 0.875rem;color:#074f3e;">{{ Auth::user()->name }}</span>
                <i class="bi bi-chevron-down ms-1 text-muted" style="font-size: 0.75rem;"></i>
            </button>

            <ul class="dropdown-menu dropdown-menu-end">
                <li>
                    <span class="dropdown-item-text">
                        <div class="fw-semibold" style="font-size:0.875rem;">{{ Auth::user()->name }}</div>
                        <div class="text-muted" style="font-size:0.75rem;">{{ Auth::user()->email }}</div>
                        <div class="mt-1">
                            @if(Auth::user()->hasRole('admin'))
                                <span class="badge bg-primary">{{ __('Administrator') }}</span>
                            @elseif(Auth::user()->hasRole('vet'))
                                <span class="badge bg-info">{{ __('Veterinarian') }}</span>
                            @elseif(Auth::user()->hasRole('shelter'))
                                <span class="badge bg-warning text-dark">{{ __('Animal Shelter') }}</span>
                            @else
                                <span class="badge bg-success">{{ __('Pet Owner') }}</span>
                            @endif
                        </div>
                    </span>
                </li>
                <li><hr class="dropdown-divider"></li>
                <li>
                    <a class="dropdown-item" href="{{ route('profile.edit') }}">
                        <i class="bi bi-person me-2"></i>{{ __('Profile') }}
                    </a>
                </li>
                <li><hr class="dropdown-divider"></li>
                <li>
                    <form method="POST" action="{{ route('logout') }}">
                        @csrf
                        <button type="submit" class="dropdown-item text-danger">
                            <i class="bi bi-box-arrow-right me-2"></i>{{ __('Log Out') }}
                        </button>
                    </form>
                </li>
            </ul>
        </div>
    </div>
</nav>

@push('scripts')
<script>
    function toggleSidebar() {
        const sidebar = document.getElementById('sidebar');
        const overlay = document.getElementById('sidebarOverlay');
        if (sidebar) {
            sidebar.classList.toggle('show-mobile');
            if (overlay) {
                overlay.style.display = sidebar.classList.contains('show-mobile') ? 'block' : 'none';
            }
        }
    }
</script>
@endpush
