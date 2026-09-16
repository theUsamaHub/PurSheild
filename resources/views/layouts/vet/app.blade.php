<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>{{ config('app.name', 'PawCare') }} - {{ __('Veterinarian') }}</title>
    <link rel="icon" type="image/svg+xml" href="{{ asset('favicon.svg') }}">
    <link rel="preconnect" href="https://fonts.bunny.net">
    <link href="https://fonts.bunny.net/css?family=figtree:400,500,600,700,800&display=swap" rel="stylesheet" />
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Figtree:wght@400;500;600;700;800&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">
    @vite(['resources/css/app.scss', 'resources/js/app.js'])
    <style>
        [x-cloak] { display: none !important; }

        :root{
            --pc-primary:#1a6b3c;
            --pc-primary-light:#2e9e5a;
            --pc-sidebar-from:#0b3a2c;
            --pc-sidebar-to:#123f30;
            --pc-bg:#f4f6f9;
            --pc-text-heading:#1f2937;
            --pc-text-muted:#6b7280;
            --pc-success:#16a34a;
            --pc-success-bg:#dcfce7;
            --pc-warning:#f59e0b;
            --pc-warning-bg:#ffedd5;
            --pc-info:#3b82f6;
            --pc-info-bg:#dbeafe;
            --pc-purple:#8b5cf6;
            --pc-purple-bg:#ede9fe;
            --pc-danger:#ef4444;
            --pc-danger-bg:#fee2e2;
            --pc-border:#e5e7eb;
            --fs-primary: var(--pc-primary);
            --fs-primary-bg: var(--pc-success-bg);
            --fs-info: var(--pc-info);
            --fs-success: var(--pc-success);
            --fs-accent: var(--pc-purple);
            --fs-text-heading: var(--pc-text-heading);
        }

        body{
            background: var(--pc-bg);
            font-family: "Figtree", sans-serif;
            color: var(--pc-text-heading);
        }

        /* ---------- Layout shell ---------- */
        .pc-shell{ min-height:100vh; }
        .pc-main{ min-width:0; min-height:100vh; display:flex; flex-direction:column; }
        .pc-content{ padding:1.75rem; }

        /* ---------- Sidebar ---------- */
        .pc-sidebar{
            width:280px;
            flex-shrink:0;
            min-height:100vh;
            background:linear-gradient(180deg, var(--pc-sidebar-from), var(--pc-sidebar-to));
            position:relative;
            overflow:hidden;
        }
        .pc-sidebar-brand{
            padding:1.25rem 1.25rem 1rem;
            border-bottom:1px solid rgba(255,255,255,.08);
        }
        .pc-brand-icon{
            width:42px;height:42px;border-radius:12px;
            background:#ffffff;
            display:flex;align-items:center;justify-content:center;
        }
        .pc-brand-icon svg{ width:24px; height:24px; }
        .pc-brand-title{ color:#fff; font-weight:700; font-size:1.15rem; line-height:1.1; }
        .pc-brand-sub{ color:rgba(255,255,255,.55); font-size:.68rem; letter-spacing:.02em; }

        .pc-nav{ padding:1rem .85rem; position:relative; z-index:2; }
        .pc-nav .nav-link{
            display:flex; align-items:center; gap:.65rem;
            color:rgba(255,255,255,.7);
            font-size:.9rem; font-weight:500;
            padding:.6rem .85rem; border-radius:.65rem; margin-bottom:.2rem;
            transition:background .15s ease, color .15s ease;
        }
        .pc-nav .nav-link i{ font-size:1.05rem; width:20px; text-align:center; }
        .pc-nav .nav-link:hover{ background:rgba(255,255,255,.08); color:#fff; }
        .pc-nav .nav-link.active{
            background:#fff; color:var(--pc-primary);
            box-shadow:0 4px 10px rgba(0,0,0,.15);
        }
        .pc-nav .nav-badge{
            margin-left:auto; background:var(--pc-danger); color:#fff;
            font-size:.65rem; font-weight:700; border-radius:999px;
            padding:.1rem .45rem; line-height:1.3;
        }
        .pc-nav-divider{ height:1px; background:rgba(255,255,255,.08); margin:.75rem .85rem; }

        .pc-sidebar-footer{
            padding:1.1rem 1.25rem 1.4rem; position:relative; z-index:2;
        }
        .pc-sidebar-footer .quote{
            color:rgba(255,255,255,.55); font-size:.78rem; font-style:italic; line-height:1.4;
        }

        @media (max-width: 991.98px){
            .pc-sidebar.pc-sidebar-open{
                display:flex !important; position:fixed; inset:0 auto 0 0; z-index:1045;
            }
        }

        .pc-paw-bg{
            position:absolute; left:-30px; bottom:-30px; width:220px; height:220px;
            opacity:.06; z-index:1; pointer-events:none;
        }

        /* ---------- Topbar ---------- */
        .pc-topbar{
            background:#fff; border-bottom:1px solid var(--pc-border);
            padding:.85rem 1.5rem; display:flex; align-items:center; gap:1rem;
            position:sticky; top:0; z-index:1030;
        }
        .pc-search{
            flex:1; max-width:420px; position:relative;
        }
        .pc-search input{
            width:100%; border:1px solid var(--pc-border); background:var(--pc-bg);
            border-radius:.65rem; padding:.55rem 3.2rem .55rem 2.4rem; font-size:.875rem;
        }
        .pc-search input:focus{ outline:none; border-color:var(--pc-primary); background:#fff; }
        .pc-search i{ position:absolute; left:.85rem; top:50%; transform:translateY(-50%); color:var(--pc-text-muted); }
        .pc-search kbd{
            position:absolute; right:.6rem; top:50%; transform:translateY(-50%);
            background:#fff; border:1px solid var(--pc-border); color:var(--pc-text-muted);
            font-size:.7rem; padding:.1rem .4rem; border-radius:.35rem;
        }
        .pc-topbar-right{ margin-left:auto; display:flex; align-items:center; gap:1.1rem; }
        .pc-bell{ position:relative; color:var(--pc-text-heading); font-size:1.2rem; }
        .pc-bell .badge{
            position:absolute; top:-6px; right:-8px; background:var(--pc-danger);
            font-size:.62rem; border-radius:999px; padding:.15rem .38rem;
        }
        .pc-user{ display:flex; align-items:center; gap:.6rem; cursor:pointer; }
        .pc-user-avatar{
            width:40px;height:40px;border-radius:999px;overflow:hidden;
            background:linear-gradient(135deg,#0ea5e9,#38bdf8);
            display:flex;align-items:center;justify-content:center;color:#fff;font-weight:600;
        }
        .pc-user-name{ font-weight:700; font-size:.9rem; line-height:1.1; }
        .pc-user-role{ font-size:.75rem; color:var(--pc-text-muted); }
        .pc-sidebar-toggle{ border:none; background:transparent; font-size:1.3rem; color:var(--pc-text-heading); }

        .vs-pet-avatar { width:40px; height:40px; flex-shrink:0; border-radius:50%; object-fit:cover; display:inline-flex; align-items:center; justify-content:center; background:#dcfce7; color:#1a6b3c; font-weight:700; }
        .vs-pet-avatar[hidden] { display:none; }
        /* ---------- Cards ---------- */
        .pc-card{ background:#fff; border:1px solid var(--pc-border); border-radius:.9rem; }
        .pc-card-header{
            padding:1rem 1.25rem; border-bottom:1px solid var(--pc-border);
            display:flex; align-items:center; justify-content:between;
        }
    </style>
    @stack('styles')
</head>
<body>
    @php($unreadNotificationsCount = \App\Support\VetNotifications::unreadCount(Auth::user()))
    <div class="d-flex pc-shell">
        @include('layouts.vet.sidebar')

        <div class="flex-grow-1 pc-main @yield('main-class')">
            <header class="pc-topbar">
                <button class="pc-sidebar-toggle d-lg-none" type="button" onclick="toggleSidebar()" aria-label="{{ __('Toggle navigation') }}" aria-controls="sidebar" aria-expanded="false">
                    <i class="bi bi-list"></i>
                </button>

                <form class="pc-search" action="{{ route('vet.appointments.index') }}" method="GET" role="search">
                    <i class="bi bi-search"></i>
                    <input type="search" name="search" placeholder="{{ __('Search appointments, pets, owners...') }}" value="{{ request('search') }}">
                    <kbd>Ctrl+K</kbd>
                </form>

                <div class="pc-topbar-right">
                    <a href="{{ route('vet.notifications.index') }}" class="pc-bell">
                        <i class="bi bi-bell"></i>
                        @php($unread = $unreadNotificationsCount ?? (Auth::user()->unreadNotifications->count() ?? 0))
                        @if($unread > 0)
                            <span class="badge">{{ $unread }}</span>
                        @endif
                    </a>

                    <div class="dropdown">
                        <div class="pc-user" data-bs-toggle="dropdown" aria-expanded="false">
                            <div class="pc-user-avatar">
                                @if(Auth::user()->profile_image)
                                    <img src="{{ \Illuminate\Support\Facades\Storage::disk('public')->url(Auth::user()->profile_image) }}" alt="{{ Auth::user()->name }}" style="width:100%;height:100%;object-fit:cover;">
                                @else
                                    {{ substr(Auth::user()->name, 0, 1) }}
                                @endif
                            </div>
                            <div>
                                <div class="pc-user-name">{{ Auth::user()->name }}</div>
                                <div class="pc-user-role">{{ __('Veterinarian') }}</div>
                            </div>
                            <i class="bi bi-chevron-down ms-1 text-muted" style="font-size:.75rem;"></i>
                        </div>
                        <ul class="dropdown-menu dropdown-menu-end">
                            <li><a class="dropdown-item" href="{{ route('profile.edit') }}"><i class="bi bi-person me-2"></i>{{ __('Profile') }}</a></li>
                            <li><hr class="dropdown-divider"></li>
                            <li>
                                <form method="POST" action="{{ route('logout') }}">
                                    @csrf
                                    <button type="submit" class="dropdown-item"><i class="bi bi-box-arrow-right me-2"></i>{{ __('Logout') }}</button>
                                </form>
                            </li>
                        </ul>
                    </div>
                </div>
            </header>

            <main class="pc-content">
                @if(session('success'))
                    <div class="alert alert-success alert-dismissible fade show" role="alert">
                        <i class="bi bi-check-circle me-1"></i>{{ session('success') }}
                        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                    </div>
                @endif
                @if(session('error'))
                    <div class="alert alert-danger alert-dismissible fade show" role="alert">
                        <i class="bi bi-exclamation-circle me-1"></i>{{ session('error') }}
                        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                    </div>
                @endif
                @if($errors->any())
                    <div class="alert alert-danger" role="alert"><ul class="mb-0">@foreach($errors->all() as $error)<li>{{ $error }}</li>@endforeach</ul></div>
                @endif
                @yield('content')
            </main>
        </div>
    </div>

    @stack('scripts')
    @include('partials.command-palette')

    <script>
        document.addEventListener('keydown', event => {
            if (event.key === 'Escape' && document.getElementById('sidebar')?.classList.contains('pc-sidebar-open')) toggleSidebar();
        });
        function toggleSidebar(){
            const sidebar = document.getElementById('sidebar');
            const overlay = document.getElementById('sidebarOverlay');
            if (!sidebar) return;
            const isOpen = sidebar.classList.toggle('pc-sidebar-open');
            document.querySelector('.pc-sidebar-toggle')?.setAttribute('aria-expanded', String(isOpen));
            if (overlay) overlay.style.display = isOpen ? 'block' : 'none';
        }
    </script>
</body>
</html>
