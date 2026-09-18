<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">

    <meta name="viewport"
          content="width=device-width, initial-scale=1.0">

    <title>Pet Products | FurShield</title>

    <meta name="description"
          content="Explore premium pet food, wellness, grooming, toys and comfort products on FurShield.">

    <link rel="preconnect"
          href="https://fonts.googleapis.com">

    <link rel="preconnect"
          href="https://fonts.gstatic.com"
          crossorigin>

    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@400;500;600;700;800&display=swap"
          rel="stylesheet">

    <link rel="stylesheet"
          href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.2/css/all.min.css">

    <link rel="stylesheet"
          href="{{ asset('css/inner-pages.css') }}">

    <link rel="stylesheet"
          href="{{ asset('css/products.css') }}">
</head>

<body>

<canvas id="productParticles"></canvas>

<div class="cursor-glow"
     id="cursorGlow"></div>


{{-- ============================================================
     NAVBAR
============================================================ --}}
@include('partials.furshield-navbar', ['activePage' => 'products'])



{{-- ============================================================
     HERO
============================================================ --}}
<section class="products-hero">

    <div class="hero-orb hero-orb-one"></div>
    <div class="hero-orb hero-orb-two"></div>
    <div class="hero-orb hero-orb-three"></div>


    <div class="floating-paw paw-one">
        <i class="fa-solid fa-paw"></i>
    </div>

    <div class="floating-paw paw-two">
        <i class="fa-solid fa-bone"></i>
    </div>

    <div class="floating-paw paw-three">
        <i class="fa-solid fa-heart"></i>
    </div>


    <div class="products-container hero-grid">

        <div class="products-hero-content reveal-left">

            <div class="products-pill light-pill">

                <span></span>

                PREMIUM PET ESSENTIALS

            </div>


            <h1>

                Better Products For

                <span>
                    Happier Pets
                </span>

            </h1>


            <p>
                Discover carefully selected pet food, grooming essentials,
                toys, wellness products and comfort items designed for
                healthier and happier everyday pet care.
            </p>


            <div class="hero-actions">

                <a href="#productCollection"
                   class="hero-main-btn magnetic-btn">

                    Shop Collection

                    <i class="fa-solid fa-arrow-right"></i>

                </a>


                <a href="#featuredProduct"
                   class="hero-outline-btn magnetic-btn">

                    <i class="fa-solid fa-star"></i>

                    Featured Product

                </a>

            </div>


            <div class="hero-trust">

                <div>
                    <i class="fa-solid fa-circle-check"></i>

                    Quality Picks
                </div>

                <div>
                    <i class="fa-solid fa-circle-check"></i>

                    Pet Friendly
                </div>

                <div>
                    <i class="fa-solid fa-circle-check"></i>

                    Easy Cart
                </div>

            </div>

        </div>


        {{-- 3D Hero Product --}}
        <div class="hero-product-stage reveal-right"
             id="heroProductStage">

            <div class="hero-product-ring ring-one"></div>
            <div class="hero-product-ring ring-two"></div>

            <div class="hero-product-platform"></div>


            <div class="hero-product-card"
                 id="heroProductCard">

                <div class="hero-sale-tag">
                    BEST SELLER
                </div>

                <div class="hero-product-image">

                    <img src="{{ asset('images/dog-food.jpg') }}"
                         alt="Premium Dog Food">

                </div>


                <div class="hero-product-info">

                    <span>
                        Premium Nutrition
                    </span>

                    <h3>
                        Healthy Dog Food
                    </h3>

                    <div class="hero-rating">

                        <i class="fa-solid fa-star"></i>
                        <i class="fa-solid fa-star"></i>
                        <i class="fa-solid fa-star"></i>
                        <i class="fa-solid fa-star"></i>
                        <i class="fa-solid fa-star"></i>

                    </div>

                </div>

            </div>


            <div class="hero-floating-card float-card-one">

                <div class="float-icon">
                    <i class="fa-solid fa-leaf"></i>
                </div>

                <div>
                    <strong>Premium</strong>
                    <span>Ingredients</span>
                </div>

            </div>


            <div class="hero-floating-card float-card-two">

                <div class="float-icon">
                    <i class="fa-solid fa-heart-pulse"></i>
                </div>

                <div>
                    <strong>Healthy</strong>
                    <span>Daily Choice</span>
                </div>

            </div>


            <div class="hero-floating-card float-card-three">

                <div class="float-icon">
                    <i class="fa-solid fa-star"></i>
                </div>

                <div>
                    <strong>4.9</strong>
                    <span>Customer Rating</span>
                </div>

            </div>

        </div>

    </div>

</section>



{{-- ============================================================
     TRUST STRIP
============================================================ --}}
<section class="product-trust-section">

    <div class="products-container">

        <div class="product-trust-box reveal-up">

            <div class="trust-item">

                <div>
                    <i class="fa-solid fa-shield-heart"></i>
                </div>

                <span>
                    <strong>Quality Products</strong>
                    Selected for pet wellbeing
                </span>

            </div>


            <div class="trust-divider"></div>


            <div class="trust-item">

                <div>
                    <i class="fa-solid fa-paw"></i>
                </div>

                <span>
                    <strong>Pet Friendly</strong>
                    Everyday essentials
                </span>

            </div>


            <div class="trust-divider"></div>


            <div class="trust-item">

                <div>
                    <i class="fa-solid fa-cart-shopping"></i>
                </div>

                <span>
                    <strong>Simple Shopping</strong>
                    Easy product browsing
                </span>

            </div>


            <div class="trust-divider"></div>


            <div class="trust-item">

                <div>
                    <i class="fa-solid fa-headset"></i>
                </div>

                <span>
                    <strong>Care Support</strong>
                    FurShield assistance
                </span>

            </div>

        </div>

    </div>

</section>



{{-- ============================================================
     SHOP SECTION
============================================================ --}}
<section class="product-shop-section"
         id="productCollection">

    <div class="products-container">

        <div class="product-section-heading reveal-up">

            <div class="products-pill center-pill">

                <span></span>

                OUR COLLECTION

            </div>


            <h2>

                Premium Essentials For

                <span>Every Pet</span>

            </h2>


            <p>
                Search and filter FurShield products by category.
            </p>

        </div>


        {{-- FILTER AREA --}}
        <div class="product-toolbar reveal-up">

            <div class="category-filters">

                <button class="filter-btn active"
                        data-filter="all">

                    All Products

                </button>

                @foreach($categories as $category)
                    <button class="filter-btn"
                            data-filter="{{ Str::slug($category->name) }}">

                        {{ $category->name }}

                    </button>
                @endforeach

            </div>


            <div class="product-search-box">

                <i class="fa-solid fa-magnifying-glass"></i>

                <input type="text"
                       id="productSearch"
                       placeholder="Search products...">

            </div>

        </div>



        {{-- PRODUCTS --}}
        <div class="products-grid" id="productsGrid">
            @forelse($products as $product)
            <article class="vip-product-card reveal-up"
                     data-category="{{ Str::slug($product->category?->name ?? 'general') }}"
                     data-name="{{ strtolower($product->name) }}">

                <div class="product-card-shine"></div>

                <div class="product-image-box">
                    @if($product->images && $product->images->count() > 0)
                        <img src="{{ Storage::url($product->images->first()->image_path) }}" alt="{{ $product->name }}">
                    @else
                        <img src="{{ asset('images/dog-food.jpg') }}" alt="{{ $product->name }}">
                    @endif

                    @if($product->is_featured)
                        <span class="product-badge">Featured</span>
                    @endif

                    <div class="product-floating-actions">
                        <button aria-label="Favorite">
                            <i class="fa-regular fa-heart"></i>
                        </button>
                    </div>
                </div>

                <div class="product-card-body">
                    <div class="product-category">
                        {{ $product->category?->name ?? 'General' }}
                    </div>

                    <h3>{{ $product->name }}</h3>

                    <div class="product-rating">
                        <div>
                            <i class="fa-solid fa-star"></i>
                            <i class="fa-solid fa-star"></i>
                            <i class="fa-solid fa-star"></i>
                            <i class="fa-solid fa-star"></i>
                            <i class="fa-solid fa-star"></i>
                        </div>
                        <span>4.9</span>
                    </div>

                    <p>{{ Str::limit($product->description ?? 'Quality pet care product available on FurShield.', 80) }}</p>

                    <div class="product-bottom">
                        <div class="product-price">
                            <strong>${{ number_format($product->effective_price, 2) }}</strong>
                            @if($product->special_price)
                                <span>${{ number_format($product->price, 2) }}</span>
                            @endif
                        </div>

                        @auth
                            @if(auth()->user()->hasRole('owner'))
                                <form method="POST" action="{{ route('owner.cart.add') }}" style="display:inline;">
                                    @csrf
                                    <input type="hidden" name="product_id" value="{{ $product->id }}">
                                    <input type="hidden" name="quantity" value="1">
                                    <button type="submit" class="add-cart-btn" aria-label="Add to Cart">
                                        <i class="fa-solid fa-plus"></i>
                                    </button>
                                </form>
                            @else
                                <a href="{{ route('login') }}" class="add-cart-btn" style="text-decoration:none; display:inline-flex; align-items:center; justify-content:center;" aria-label="Only Pet Owners can buy" title="Only Pet Owners can add products to cart">
                                    <i class="fa-solid fa-lock"></i>
                                </a>
                            @endif
                        @else
                            <a href="{{ route('login') }}" class="add-cart-btn" style="text-decoration:none; display:inline-flex; align-items:center; justify-content:center;" aria-label="Login to Buy">
                                <i class="fa-solid fa-plus"></i>
                            </a>
                        @endauth
                    </div>
                </div>
            </article>
            @empty
                <p>No products available right now.</p>
            @endforelse
        </div>

        <div class="fs-pagination-wrap">
            {{ $products->links('vendor.pagination.furshield') }}
        </div>


        <div class="no-product-message"
             id="noProducts">

            <i class="fa-solid fa-paw"></i>

            <h3>No products found</h3>

            <p>
                Try another search or product category.
            </p>

        </div>

    </div>

</section>



{{-- ============================================================
     FEATURED 3D PRODUCT
============================================================ --}}
<section class="featured-product-section"
         id="featuredProduct">

    <div class="products-container featured-product-grid">

        <div class="featured-visual reveal-left">

            <div class="featured-circle"></div>
            <div class="featured-circle circle-small"></div>


            <div class="featured-image-card"
                 id="featuredImageCard">

                <img src="{{ asset('images/vitamins.jpg') }}"
                     alt="FurShield Pet Vitamins">

            </div>


            <div class="featured-mini-card mini-one">

                <i class="fa-solid fa-heart-pulse"></i>

                <span>
                    <strong>Daily Support</strong>
                    Wellness Formula
                </span>

            </div>


            <div class="featured-mini-card mini-two">

                <i class="fa-solid fa-shield"></i>

                <span>
                    <strong>Premium Pick</strong>
                    FurShield Choice
                </span>

            </div>

        </div>


        <div class="featured-content reveal-right">

            <div class="products-pill">

                <span></span>

                PRODUCT SPOTLIGHT

            </div>


            <h2>

                Daily Wellness For A

                <span>Healthier Pet</span>

            </h2>


            <p>
                Support your pet's everyday wellbeing with essential
                nutritional care designed to complement a balanced lifestyle.
            </p>


            <div class="featured-points">

                <div>

                    <i class="fa-solid fa-check"></i>

                    Everyday wellness support

                </div>

                <div>

                    <i class="fa-solid fa-check"></i>

                    Easy daily routine

                </div>

                <div>

                    <i class="fa-solid fa-check"></i>

                    Selected FurShield product

                </div>

            </div>


            <div class="featured-price">

                <strong>
                    PKR 2,750
                </strong>

                <span>
                    PKR 3,100
                </span>

            </div>


            @auth
                @if(auth()->user()->hasRole('owner'))
                    <a href="{{ route('owner.products.index') }}" class="featured-cart-btn magnetic-btn add-cart-btn" style="text-decoration:none; display:inline-flex; align-items:center; justify-content:center;">
                        <i class="fa-solid fa-bag-shopping"></i> Add To Cart
                    </a>
                @else
                    <a href="{{ route('login') }}" class="featured-cart-btn magnetic-btn add-cart-btn" style="text-decoration:none; display:inline-flex; align-items:center; justify-content:center;">
                        <i class="fa-solid fa-lock"></i> Owner Login Required
                    </a>
                @endif
            @else
                <a href="{{ route('login') }}" class="featured-cart-btn magnetic-btn add-cart-btn" style="text-decoration:none; display:inline-flex; align-items:center; justify-content:center;">
                    <i class="fa-solid fa-bag-shopping"></i> Add To Cart
                </a>
            @endauth

        </div>

    </div>

</section>



{{-- ============================================================
     CATEGORIES
============================================================ --}}
<section class="product-category-section">

    <div class="products-container">

        <div class="product-section-heading reveal-up">

            <div class="products-pill center-pill">

                <span></span>

                SHOP BY NEED

            </div>

            <h2>

                Everything Your Pet

                <span>Needs Daily</span>

            </h2>

        </div>


        <div class="category-showcase">

            @foreach($categories as $category)
            <div class="category-showcase-card reveal-up"
                 data-target-filter="{{ Str::slug($category->name) }}">

                <div class="category-icon">
                    <i class="fa-solid fa-layer-group"></i>
                </div>

                <h3>{{ $category->name }}</h3>

                <p>
                    {{ Str::limit($category->description ?? 'Quality '.$category->name.' products for your pets.', 60) }}
                </p>

                <span>
                    Explore
                    <i class="fa-solid fa-arrow-right"></i>
                </span>

            </div>
            @endforeach

        </div>

    </div>

</section>



{{-- ============================================================
     PROMO BANNER
============================================================ --}}
<section class="product-promo-section">

    <div class="products-container">

        <div class="product-promo-box reveal-up">

            <div class="promo-decoration"></div>

            <div class="promo-content">

                <span>
                    FURSHIELD PET CARE
                </span>

                <h2>
                    One Platform.
                    <br>
                    Complete Pet Care.
                </h2>

                <p>
                    Products, health records, vet appointments and adoption
                    support in one trusted FurShield ecosystem.
                </p>


                <a href="{{ route('vets') }}"
                   class="promo-btn magnetic-btn">

                    Explore Services

                    <i class="fa-solid fa-arrow-right"></i>

                </a>

            </div>


            <div class="promo-image">

                <img src="{{ asset('images/pets-pic.jpg') }}"
                     alt="FurShield pets">

            </div>

        </div>

    </div>

</section>



{{-- ============================================================
     CTA
============================================================ --}}
<section class="product-cta-section">

    <div class="products-container">

        <div class="product-cta-content reveal-up">

            <div class="cta-paw">

                <i class="fa-solid fa-paw"></i>

            </div>


            <span class="cta-label">
                JOIN FURSHIELD TODAY
            </span>


            <h2>

                Better Care Starts With

                <span>Better Choices</span>

            </h2>


            <p>
                Create your FurShield account and manage your pet's care,
                health and daily essentials from one platform.
            </p>


            <div class="product-cta-actions">

                <a href="{{ url('/register') }}"
                   class="cta-white-btn magnetic-btn">

                    Create Account

                    <i class="fa-solid fa-arrow-right"></i>

                </a>

                <a href="{{ url('/#contact') }}"
                   class="cta-glass-btn magnetic-btn">

                    Contact Us

                </a>

            </div>

        </div>

    </div>

</section>



{{-- ============================================================
     FOOTER
============================================================ --}}
<footer class="product-footer">

    <div class="products-container">

        <div class="product-footer-grid">

            <div class="product-footer-brand">

                <a href="{{ url('/') }}"
                   class="footer-brand">

                    <div>
                        <i class="fa-solid fa-shield-dog"></i>
                    </div>

                    <span>

                        <strong>
                            FurShield
                        </strong>

                        <small>
                            Protect • Care • Love
                        </small>

                    </span>

                </a>


                <p>
                    A complete platform connecting pet owners,
                    veterinarians, shelters and pet care services.
                </p>


                <div class="product-socials">

                    <a href="#">
                        <i class="fa-brands fa-facebook-f"></i>
                    </a>

                    <a href="#">
                        <i class="fa-brands fa-instagram"></i>
                    </a>

                    <a href="#">
                        <i class="fa-brands fa-x-twitter"></i>
                    </a>

                    <a href="#">
                        <i class="fa-brands fa-youtube"></i>
                    </a>

                </div>

            </div>


            <div class="footer-column">

                <h4>Platform</h4>

                <a href="{{ url('/') }}">Home</a>

                <a href="{{ route('about') }}">About</a>

                <a href="{{ route('vets') }}">Vets</a>

                <a href="{{ route('products') }}">Products</a>

            </div>


            <div class="footer-column">

                <h4>Categories</h4>

                <a href="#productCollection">Nutrition</a>

                <a href="#productCollection">Grooming</a>

                <a href="#productCollection">Toys</a>

                <a href="#productCollection">Wellness</a>

            </div>


            <div class="footer-column">

                <h4>Support</h4>

                <a href="#">Help Center</a>

                <a href="#">FAQs</a>

                <a href="#">Privacy</a>

                <a href="{{ url('/#contact') }}">Contact</a>

            </div>


            <div class="footer-column footer-contact">

                <h4>Contact</h4>

                <span>
                    <i class="fa-solid fa-envelope"></i>
                    support@furshield.com
                </span>

                <span>
                    <i class="fa-solid fa-phone"></i>
                    +92 300 1234567
                </span>

                <span>
                    <i class="fa-solid fa-location-dot"></i>
                    Pakistan
                </span>

            </div>

        </div>


        <div class="product-footer-bottom">

            <span>
                © {{ date('Y') }} FurShield. All rights reserved.
            </span>

            <span>
                Made with
                <i class="fa-solid fa-heart"></i>
                for pets.
            </span>

        </div>

    </div>

</footer>



{{-- ============================================================
     CART DRAWER
============================================================ --}}
<div class="cart-overlay"
     id="cartOverlay"></div>


<aside class="cart-drawer"
       id="cartDrawer">

    <div class="cart-header">

        <div>

            <span>
                YOUR CART
            </span>

            <h3>
                FurShield Bag
            </h3>

        </div>


        <button id="closeCart">

            <i class="fa-solid fa-xmark"></i>

        </button>

    </div>


    <div class="cart-items"
         id="cartItems">

        <div class="empty-cart"
             id="emptyCart">

            <i class="fa-solid fa-bag-shopping"></i>

            <h4>
                Your cart is empty
            </h4>

            <p>
                Add your favorite pet products.
            </p>

        </div>

    </div>


    <div class="cart-footer">

        <div class="cart-total">

            <span>
                Total
            </span>

            <strong id="cartTotal">
                PKR 0
            </strong>

        </div>


        <button class="cart-checkout-btn">

            Continue

            <i class="fa-solid fa-arrow-right"></i>

        </button>


        <small>
            Demo cart — payment is not connected.
        </small>

    </div>

</aside>



<button class="product-back-top"
        id="productBackTop"
        aria-label="Back to top">

    <i class="fa-solid fa-arrow-up"></i>

</button>


<div class="cart-toast"
     id="cartToast">

    <i class="fa-solid fa-circle-check"></i>

    <span>
        Product added to cart
    </span>

</div>


<script src="{{ asset('js/products.js') }}"></script>

</body>
</html>