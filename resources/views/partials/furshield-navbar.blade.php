{{--
    FurShield Public Navbar Partial
    Usage: @include('partials.furshield-navbar', ['activePage' => 'home'])
    activePage options: home, about, service, products, care, vets, shelters, contact
--}}

<header class="inner-navbar" id="innerNavbar">

    <div class="inner-nav-wrapper">

        <a href="{{ url('/') }}" class="inner-brand">

            <div class="brand-icon">
                <i class="fa-solid fa-shield-dog"></i>
            </div>

            <div>
                <strong>FurShield</strong>
                <span>Protect • Care • Love</span>
            </div>

        </a>

        <nav class="inner-desktop-nav">

            <a href="{{ url('/') }}"                          {{ ($activePage ?? '') === 'home'     ? 'class=active' : '' }}>Home</a>
            <a href="{{ route('about') }}"                    {{ ($activePage ?? '') === 'about'    ? 'class=active' : '' }}>About</a>

            <a href="{{ route('products') }}"                 {{ ($activePage ?? '') === 'products' ? 'class=active' : '' }}>Products</a>
            <a href="{{ route('care') }}"                     {{ ($activePage ?? '') === 'care'     ? 'class=active' : '' }}>Care</a>
            <a href="{{ route('vets') }}"                     {{ ($activePage ?? '') === 'vets'     ? 'class=active' : '' }}>Vets</a>
            <a href="{{ route('shelters') }}"                 {{ ($activePage ?? '') === 'shelters' ? 'class=active' : '' }}>Shelters</a>
            <a href="{{ route('contact') }}"                  {{ ($activePage ?? '') === 'contact'  ? 'class=active' : '' }}>Contact</a>

        </nav>

        <div class="inner-nav-actions" style="display: flex; align-items: center; gap: 1rem;">
            <a href="{{ route('login') }}" class="nav-cart-btn" title="View Cart" style="display: inline-flex; align-items: center; justify-content: center; width: 40px; height: 40px; border-radius: 12px; border: 1px solid rgba(255, 255, 255, 0.3); background: rgba(255, 255, 255, 0.15); color: #ffffff; text-decoration: none; font-size: 1.1rem; transition: all 0.3s ease;">
                <i class="fa-solid fa-cart-shopping" style="color: #ffffff;"></i>
            </a>
            @auth
                <a href="{{ url('/dashboard') }}" class="nav-register">
                    <i class="fa-solid fa-gauge"></i> Dashboard
                </a>
            @else
                <a href="{{ route('login') }}" class="nav-login">Login</a>
                <a href="{{ route('register') }}" class="nav-register">Register</a>
            @endauth
        </div>

        <button class="inner-mobile-toggle" id="innerMobileToggle">
            <span></span>
            <span></span>
            <span></span>
        </button>

    </div>

    <div class="inner-mobile-nav" id="innerMobileNav">

        <a href="{{ url('/') }}"         {{ ($activePage ?? '') === 'home'     ? 'class=active' : '' }}>Home</a>
        <a href="{{ route('about') }}"   {{ ($activePage ?? '') === 'about'    ? 'class=active' : '' }}>About</a>

        <a href="{{ route('products') }}"{{ ($activePage ?? '') === 'products' ? 'class=active' : '' }}>Products</a>
        <a href="{{ route('care') }}"    {{ ($activePage ?? '') === 'care'     ? 'class=active' : '' }}>Care</a>
        <a href="{{ route('vets') }}"    {{ ($activePage ?? '') === 'vets'     ? 'class=active' : '' }}>Vets</a>
        <a href="{{ route('shelters') }}"{{ ($activePage ?? '') === 'shelters' ? 'class=active' : '' }}>Shelters</a>
        <a href="{{ route('contact') }}" {{ ($activePage ?? '') === 'contact'  ? 'class=active' : '' }}>Contact</a>
        <a href="{{ route('login') }}"   style="display: flex; align-items: center; gap: 0.5rem; color: #ffffff;"><i class="fa-solid fa-cart-shopping" style="color: #ffffff;"></i> Cart</a>

        @auth
            <a href="{{ url('/dashboard') }}" class="nav-register" style="text-align:center; margin-top:1rem;">
                Dashboard
            </a>
        @else
            <div style="display:flex; gap:0.75rem; padding: 1rem;">
                <a href="{{ route('login') }}" class="nav-login" style="flex:1; text-align:center;">Login</a>
                <a href="{{ route('register') }}" class="nav-register" style="flex:1; text-align:center;">Register</a>
            </div>
        @endauth

    </div>

</header>
