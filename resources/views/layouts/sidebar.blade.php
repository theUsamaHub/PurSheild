<aside class="sidebar d-none d-lg-flex flex-column" id="sidebar">
    <style>
        #sidebar {
            width: 260px;
            min-height: 100vh;
            background: linear-gradient(105deg, #074f3e, #003d30 80%) !important;
            color: #ffffff;
            box-shadow: 2px 0 15px rgba(0, 0, 0, 0.15);
            z-index: 1045;
            font-family: 'Poppins', 'Inter', -apple-system, sans-serif;
        }
        .admin-sidebar-brand {
            padding: 20px 20px 16px;
            border-bottom: 1px solid rgba(177, 216, 206, 0.2);
            display: flex;
            align-items: center;
            gap: 13px;
            text-decoration: none !important;
        }
        .admin-brand-icon {
            width: 44px;
            height: 44px;
            border-radius: 12px;
            background: rgba(75, 223, 172, 0.15);
            border: 1px solid rgba(75, 223, 172, 0.3);
            color: #4bdfac;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 1.4rem;
            box-shadow: 0 4px 12px rgba(0, 0, 0, 0.15);
            flex-shrink: 0;
        }
        .admin-brand-text strong {
            font-size: 1.35rem;
            font-weight: 700;
            color: #ffffff;
            display: block;
            line-height: 1.2;
            letter-spacing: -0.3px;
        }
        .admin-brand-text small {
            font-size: 0.72rem;
            color: #93c1b6;
            display: block;
            font-weight: 500;
            letter-spacing: 0.02em;
        }
        .admin-section-label {
            font-size: 0.7rem;
            font-weight: 700;
            text-transform: uppercase;
            letter-spacing: 0.08em;
            color: #73ad9e;
            padding: 18px 18px 6px;
        }
        #sidebar .nav-link {
            color: #e2f4ef !important;
            font-size: 0.88rem;
            font-weight: 500;
            padding: 10px 14px;
            border-radius: 9px;
            margin: 2px 10px;
            display: flex;
            align-items: center;
            gap: 12px;
            transition: all 0.2s cubic-bezier(0.4, 0, 0.2, 1);
            position: relative;
            text-decoration: none;
        }
        #sidebar .nav-link .bi {
            font-size: 1.15rem;
            min-width: 22px;
            text-align: center;
            color: #4bdfac;
            transition: transform 0.2s ease;
        }
        #sidebar .nav-link:hover {
            background: rgba(255, 255, 255, 0.09) !important;
            color: #ffffff !important;
            transform: translateX(4px);
        }
        #sidebar .nav-link:hover .bi {
            transform: scale(1.15);
        }
        #sidebar .nav-link.active {
            background: linear-gradient(105deg, #168f6b, #087657) !important;
            color: #ffffff !important;
            font-weight: 600;
            box-shadow: 0 4px 14px rgba(8, 118, 87, 0.35);
        }
        #sidebar .nav-link.active::before {
            content: '';
            position: absolute;
            left: -10px;
            top: 15%;
            height: 70%;
            width: 5px;
            background: #48ddab;
            border-radius: 0 4px 4px 0;
            box-shadow: 0 0 10px rgba(72, 221, 171, 0.7);
        }
        #sidebar .badge {
            font-size: 0.72rem;
            font-weight: 700;
            padding: 3px 8px;
            border-radius: 20px;
            margin-left: auto;
        }
        .admin-sidebar-profile {
            padding: 16px 18px;
            border-top: 1px solid rgba(177, 216, 206, 0.2);
            background: rgba(0, 0, 0, 0.12);
            display: flex;
            align-items: center;
            gap: 12px;
        }
        .admin-profile-avatar {
            width: 40px;
            height: 40px;
            border-radius: 50%;
            background: linear-gradient(135deg, #168f6b, #48ddab);
            color: #ffffff;
            font-weight: 700;
            font-size: 1rem;
            display: flex;
            align-items: center;
            justify-content: center;
            box-shadow: 0 2px 8px rgba(0, 0, 0, 0.2);
            flex-shrink: 0;
        }
        .admin-profile-info strong {
            color: #ffffff;
            font-size: 0.88rem;
            font-weight: 600;
            display: block;
            line-height: 1.2;
        }
        .admin-profile-info small {
            color: #88cbb9;
            font-size: 0.75rem;
            display: block;
        }
    </style>

    <div class="admin-sidebar-brand-wrapper">
        <a href="{{ route('admin.dashboard') }}" class="admin-sidebar-brand">
            <div class="admin-brand-icon">
                <i class="bi bi-shield-check"></i>
            </div>
            <div class="admin-brand-text">
                <strong>FurShield</strong>
                <small>Admin Control Panel</small>
            </div>
        </a>
    </div>

    <nav class="flex-grow-1 py-2 overflow-auto">
        <ul class="nav flex-column">
            <li class="nav-item">
                <a class="nav-link {{ request()->routeIs('admin.dashboard') ? 'active' : '' }}" href="{{ route('admin.dashboard') }}">
                    <i class="bi bi-speedometer2"></i> <span>{{ __('Dashboard') }}</span>
                </a>
            </li>
            <li class="nav-item">
                <a class="nav-link {{ request()->routeIs('profile.edit') ? 'active' : '' }}" href="{{ route('profile.edit') }}">
                    <i class="bi bi-person"></i> <span>{{ __('Profile') }}</span>
                </a>
            </li>

            <li class="admin-section-label">{{ __('Management') }}</li>
            <li class="nav-item">
                <a class="nav-link {{ request()->routeIs('admin.verification.*') ? 'active' : '' }}" href="{{ route('admin.verification.index') }}">
                    <i class="bi bi-patch-check"></i> <span>{{ __('Verify Vets & Shelters') }}</span>
                    @php $vc = cache()->remember('verification.pending_count', 60, fn() => \App\Models\VetProfile::where('is_verified', false)->count() + \App\Models\ShelterProfile::where('is_verified', false)->count()); @endphp
                    @if ($vc > 0)<span class="badge bg-warning text-dark">{{ $vc }}</span>@endif
                </a>
            </li>
            <li class="nav-item"><a class="nav-link {{ request()->routeIs('admin.users.*') ? 'active' : '' }}" href="{{ route('admin.users.index') }}"><i class="bi bi-people"></i> <span>{{ __('Users') }}</span></a></li>
            <li class="nav-item"><a class="nav-link {{ request()->routeIs('admin.roles.*') ? 'active' : '' }}" href="{{ route('admin.roles.index') }}"><i class="bi bi-shield-lock"></i> <span>{{ __('Roles') }}</span></a></li>
            <li class="nav-item"><a class="nav-link {{ request()->routeIs('admin.reviews.*') ? 'active' : '' }}" href="{{ route('admin.reviews.index') }}"><i class="bi bi-star"></i> <span>{{ __('Reviews') }}</span></a></li>

            <li class="admin-section-label">{{ __('E-Commerce') }}</li>
            <li class="nav-item"><a class="nav-link {{ request()->routeIs('admin.products.*') ? 'active' : '' }}" href="{{ route('admin.products.index') }}"><i class="bi bi-box-seam"></i> <span>{{ __('Products') }}</span></a></li>
            <li class="nav-item"><a class="nav-link {{ request()->routeIs('admin.product-categories.*') ? 'active' : '' }}" href="{{ route('admin.product-categories.index') }}"><i class="bi bi-grid"></i> <span>{{ __('Product Categories') }}</span></a></li>
            <li class="nav-item"><a class="nav-link {{ request()->routeIs('admin.orders.*') ? 'active' : '' }}" href="{{ route('admin.orders.index') }}"><i class="bi bi-bag"></i> <span>{{ __('Orders') }}</span></a></li>
            <li class="nav-item"><a class="nav-link {{ request()->routeIs('admin.sku-templates.*') ? 'active' : '' }}" href="{{ route('admin.sku-templates.index') }}"><i class="bi bi-upc-scan"></i> <span>{{ __('SKU Templates') }}</span></a></li>
            <li class="nav-item"><a class="nav-link {{ request()->routeIs('admin.care-content.*') ? 'active' : '' }}" href="{{ route('admin.care-content.index') }}"><i class="bi bi-journal-richtext"></i> <span>{{ __('Care Content') }}</span></a></li>

            <li class="admin-section-label">{{ __('Veterinary') }}</li>
            <li class="nav-item"><a class="nav-link {{ request()->routeIs('admin.species.*') ? 'active' : '' }}" href="{{ route('admin.species.index') }}"><i class="bi bi-bug"></i> <span>{{ __('Species') }}</span></a></li>
            <li class="nav-item"><a class="nav-link {{ request()->routeIs('admin.breeds.*') ? 'active' : '' }}" href="{{ route('admin.breeds.index') }}"><i class="bi bi-heart"></i> <span>{{ __('Breeds') }}</span></a></li>
            <li class="nav-item"><a class="nav-link {{ request()->routeIs('admin.specializations.*') ? 'active' : '' }}" href="{{ route('admin.specializations.index') }}"><i class="bi bi-award"></i> <span>{{ __('Specializations') }}</span></a></li>

            <li class="admin-section-label">{{ __('Communication') }}</li>
            <li class="nav-item">
                <a class="nav-link {{ request()->routeIs('admin.contacts.*') ? 'active' : '' }}" href="{{ route('admin.contacts.index') }}">
                    <i class="bi bi-envelope"></i> <span>{{ __('Contacts') }}</span>
                    @php $nc = cache()->remember('contacts.new_count', 60, fn() => \App\Models\Contact::where('status', 'new')->count()); @endphp
                    @if ($nc > 0)<span class="badge bg-danger">{{ $nc }}</span>@endif
                </a>
            </li>

            <li class="admin-section-label">{{ __('System') }}</li>
            <li class="nav-item"><a class="nav-link {{ request()->routeIs('admin.notifications.*') ? 'active' : '' }}" href="{{ route('admin.notifications.index') }}"><i class="bi bi-bell"></i> <span>{{ __('Notifications') }}</span> @php $nc = cache()->remember('notifications.unread.' . auth()->id(), 60, fn() => auth()->user()->unreadNotifications()->count()); @endphp @if($nc > 0)<span class="badge bg-danger">{{ $nc }}</span>@endif</a></li>
            <li class="nav-item"><a class="nav-link {{ request()->routeIs('admin.subscribers.*') ? 'active' : '' }}" href="{{ route('admin.subscribers.index') }}"><i class="bi bi-envelope-check"></i> <span>{{ __('Subscribers') }}</span></a></li>
            <li class="nav-item"><a class="nav-link {{ request()->routeIs('admin.media.*') ? 'active' : '' }}" href="{{ route('admin.media.index') }}"><i class="bi bi-folder"></i> <span>{{ __('Media') }}</span></a></li>
            <li class="nav-item"><a class="nav-link {{ request()->routeIs('admin.settings.*') ? 'active' : '' }}" href="{{ route('admin.settings.index') }}"><i class="bi bi-gear"></i> <span>{{ __('Settings') }}</span></a></li>
            <li class="nav-item"><a class="nav-link {{ request()->routeIs('admin.activity-logs.*') ? 'active' : '' }}" href="{{ route('admin.activity-logs.index') }}"><i class="bi bi-clock-history"></i> <span>{{ __('Activity') }}</span></a></li>
            <li class="nav-item"><a class="nav-link {{ request()->routeIs('admin.ip-restrictions.*') ? 'active' : '' }}" href="{{ route('admin.ip-restrictions.index') }}"><i class="bi bi-shield-check"></i> <span>{{ __('IP Restrictions') }}</span></a></li>
            <li class="nav-item"><a class="nav-link {{ request()->routeIs('admin.sessions.*') ? 'active' : '' }}" href="{{ route('admin.sessions.index') }}"><i class="bi bi-person-badge"></i> <span>{{ __('Sessions') }}</span></a></li>
            <li class="nav-item"><a class="nav-link {{ request()->routeIs('admin.maintenance.*') ? 'active' : '' }}" href="{{ route('admin.maintenance.index') }}"><i class="bi bi-tools"></i> <span>{{ __('Maintenance') }}</span></a></li>
            <li class="nav-item"><a class="nav-link {{ request()->routeIs('admin.health.*') ? 'active' : '' }}" href="{{ route('admin.health.index') }}"><i class="bi bi-heart-pulse"></i> <span>{{ __('Health') }}</span></a></li>
            <li class="nav-item"><a class="nav-link {{ request()->routeIs('admin.logs.*') ? 'active' : '' }}" href="{{ route('admin.logs.index') }}"><i class="bi bi-journal-text"></i> <span>{{ __('Logs') }}</span></a></li>
            <li class="nav-item"><a class="nav-link {{ request()->routeIs('admin.backup.*') ? 'active' : '' }}" href="{{ route('admin.backup.index') }}"><i class="bi bi-database"></i> <span>{{ __('Backup') }}</span></a></li>
            <li class="nav-item">
                <a class="nav-link {{ request()->routeIs('admin.trash.*') ? 'active' : '' }}" href="{{ route('admin.trash.index') }}">
                    <i class="bi bi-trash"></i> <span>{{ __('Recycle Bin') }}</span>
                    @php $tc = cache()->remember('trash.count', 60, fn() => \App\Models\Product::onlyTrashed()->count() + \App\Models\Category::onlyTrashed()->count() + \App\Models\CareContent::onlyTrashed()->count()); @endphp
                    @if ($tc > 0)<span class="badge bg-danger">{{ $tc }}</span>@endif
                </a>
            </li>
        </ul>
    </nav>

    <div class="admin-sidebar-profile">
        <div class="admin-profile-avatar">
            {{ substr(Auth::user()->name ?? 'A', 0, 1) }}
        </div>
        <div class="admin-profile-info overflow-hidden">
            <strong class="text-truncate">{{ Auth::user()->name ?? 'Admin' }}</strong>
            <small class="text-truncate">{{ __('System Administrator') }}</small>
        </div>
    </div>
</aside>

<div class="sidebar-overlay d-lg-none" id="sidebarOverlay" style="display:none;position:fixed;inset:0;background:rgba(6,58,52,0.6);z-index:1040;" onclick="toggleSidebar()"></div>
