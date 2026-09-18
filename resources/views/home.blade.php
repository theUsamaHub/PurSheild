<!DOCTYPE html>
<html lang="en">
<head>

    <meta charset="UTF-8">

    <meta name="viewport"
          content="width=device-width, initial-scale=1.0">

    <title>FurShield</title>

    <meta name="description"
          content="FurShield - Complete Pet Care Platform">

    <link rel="preconnect"
          href="https://fonts.googleapis.com">

    <link rel="preconnect"
          href="https://fonts.gstatic.com"
          crossorigin>

    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700;800&display=swap"
          rel="stylesheet">

    <link rel="stylesheet"
          href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.2/css/all.min.css">

    <link rel="stylesheet"
          href="{{ asset('css/furshield.css') }}">

    <link rel="stylesheet"
          href="{{ asset('css/inner-pages.css') }}">

</head>

<body>

<canvas id="particleCanvas"></canvas>

<!-- =====================================================
     NAVBAR
===================================================== -->

@include('partials.furshield-navbar', ['activePage' => 'home'])


<!-- =====================================================
     HERO
===================================================== -->

<section class="hero-section" id="home">

    <div class="hero-overlay"></div>


    <!-- Floating 3D Elements -->

    <div class="floating-paw fp1">
        <i class="fa-solid fa-paw"></i>
    </div>

    <div class="floating-paw fp2">
        <i class="fa-solid fa-paw"></i>
    </div>

    <div class="floating-paw fp3">
        <i class="fa-solid fa-heart"></i>
    </div>

    <div class="floating-paw fp4">
        <i class="fa-solid fa-paw"></i>
    </div>

    <div class="floating-paw fp5">
        <i class="fa-solid fa-shield-heart"></i>
    </div>

    <div class="floating-paw fp6">
        <i class="fa-solid fa-star"></i>
    </div>


    <div class="hero-container">


        <!-- HERO CONTENT -->

        <div class="hero-content reveal-left">

            <div class="hero-pill">

                <i class="fa-solid fa-shield-heart"></i>

                Complete Pet Care Platform

            </div>


            <h1>

                Complete Pet Care

                <br>

                <span>in One Place</span>

            </h1>


            <p>

                Connect with trusted veterinarians,
                animal shelters and quality pet-care
                products for a healthier, happier life
                for your beloved pets.

            </p>


            <div class="hero-buttons">

                <a href="#services"
                   class="btn-primary">

                    Get Started

                    <i class="fa-solid fa-arrow-right"></i>

                </a>


                <a href="#about"
                   class="btn-outline">

                    <i class="fa-solid fa-play"></i>

                    Explore More

                </a>

            </div>


            <div class="hero-mini-features">

                <div>

                    <i class="fa-solid fa-heart"></i>

                    <span>
                        Healthy Pets
                        <small>Happy Lives</small>
                    </span>

                </div>


                <div>

                    <i class="fa-solid fa-user-doctor"></i>

                    <span>
                        Trusted Vets
                        <small>Expert Care</small>
                    </span>

                </div>


                <div>

                    <i class="fa-solid fa-cart-shopping"></i>

                    <span>
                        Quality Products
                        <small>& Care</small>
                    </span>

                </div>

            </div>

        </div>


        <!-- HERO IMAGE -->

        <div class="hero-visual reveal-right">

            <div class="hero-glow"></div>

            <div class="hero-circle"></div>


            <img src="{{ asset('/images/hero-pets.jpg') }}"
                 alt="Happy pets"
                 class="hero-pets">


            <!-- Floating card -->

            <div class="hero-floating-card health-card">

                <div class="floating-card-icon">

                    <i class="fa-solid fa-shield-heart"></i>

                </div>

                <div>

                    <strong>Pet Protected</strong>

                    <small>24/7 Care</small>

                </div>

            </div>


            <div class="hero-floating-card rating-card">

                <div class="stars">
                    ★★★★★
                </div>

                <strong>4.9/5</strong>

                <small>Pet Parents</small>

            </div>


            <div class="hero-heart">

                <i class="fa-solid fa-heart"></i>

            </div>

        </div>

    </div>


    <div class="hero-wave"></div>

</section>



<!-- =====================================================
     STATS
===================================================== -->

<section class="stats-section">

    <div class="stats-box reveal-up">


        <div class="stat-item">
            <div class="stat-icon">
                <i class="fa-solid fa-paw"></i>
            </div>
            <div>
                <strong>{{ $petsCount > 0 ? $petsCount : '100+' }}</strong>
                <span>Happy Pets</span>
            </div>
        </div>

        <div class="stat-item">
            <div class="stat-icon">
                <i class="fa-solid fa-user-doctor"></i>
            </div>
            <div>
                <strong>{{ $vetsCount > 0 ? $vetsCount : '50+' }}</strong>
                <span>Veterinarians</span>
            </div>
        </div>

        <div class="stat-item">
            <div class="stat-icon">
                <i class="fa-solid fa-house"></i>
            </div>
            <div>
                <strong>{{ $sheltersCount > 0 ? $sheltersCount : '20+' }}</strong>
                <span>Animal Shelters</span>
            </div>
        </div>

        <div class="stat-item">
            <div class="stat-icon">
                <i class="fa-solid fa-bag-shopping"></i>
            </div>
            <div>
                <strong>{{ $productsCount > 0 ? $productsCount : '100+' }}</strong>
                <span>Products & Care</span>
            </div>
        </div>

    </div>

</section>



<!-- =====================================================
     VIDEO SHOWCASE
===================================================== -->

<section class="video-showcase-section" id="services">

    <video class="video-bg"
           autoplay
           muted
           loop
           playsinline
           poster="{{ asset('images/hero-pets.jpg') }}">

        <source src="{{ asset('videos/pet-care.mp4') }}"
                type="video/mp4">

    </video>

    <div class="video-overlay"></div>

    <div class="video-content reveal-up">

        <div class="video-pill">
            <i class="fa-solid fa-play"></i>
            WATCH OUR STORY
        </div>

        <h2>
            Experience
            <span>Premium</span>
            Pet Care
        </h2>

        <p>
            See how FurShield transforms the lives of
            thousands of pets and their loving families
            every single day.
        </p>

        <button class="video-play-btn" id="videoToggle">
            <i class="fa-solid fa-pause"></i>
        </button>

    </div>

    <div class="video-scroll-indicator">
        <i class="fa-solid fa-chevron-down"></i>
    </div>

</section>



<!-- =====================================================
     ABOUT / SERVICES
===================================================== -->

<section class="services-section"
         id="about">

    <div class="section-heading reveal-up">

        <span>WHAT WE OFFER</span>

        <h2>

            Everything Your Pet

            <b>Needs</b>

        </h2>

        <p>

            One powerful platform designed to make
            pet care simple, smart and stress-free.

        </p>

    </div>


    <div class="services-grid">


        <!-- CARD 1 -->

        <div class="service-card service-green reveal-up">

            <div class="service-top">

                <span class="card-number">
                    01
                </span>

            </div>


            <div class="service-image">

                <img src="{{ asset('/images/service-pets.jpg') }}"
                     alt="Pet profiles">

            </div>


            <h3>
                Pet Profiles
            </h3>

            <p>

                Manage your pet's profile,
                images, age, breed and
                complete information.

            </p>

            <a href="#">

                Explore

                <i class="fa-solid fa-arrow-right"></i>

            </a>

        </div>


        <!-- CARD 2 -->

        <div class="service-card service-blue reveal-up">

            <div class="service-top">

                <span class="card-number">
                    02
                </span>

            </div>


            <div class="service-image">

                <img src="{{ asset('/images/health.jpg') }}"
                     alt="Health records">

            </div>


            <h3>
                Health Records
            </h3>

            <p>

                Track vaccinations,
                allergies, treatments
                and medical history.

            </p>

            <a href="#">

                Explore

                <i class="fa-solid fa-arrow-right"></i>

            </a>

        </div>


        <!-- CARD 3 -->

        <div class="service-card service-orange reveal-up">

            <div class="service-top">

                <span class="card-number">
                    03
                </span>

            </div>


            <div class="service-image">

                <img src="{{ asset('/images/vet.jpg') }}"
                     alt="Veterinarian">

            </div>


            <h3>
                Vet Appointments
            </h3>

            <p>

                Find trusted veterinarians
                and book appointments
                easily.

            </p>

            <a href="#">

                Book Now

                <i class="fa-solid fa-arrow-right"></i>

            </a>

        </div>


        <!-- CARD 4 -->

        <div class="service-card service-purple reveal-up">

            <div class="service-top">

                <span class="card-number">
                    04
                </span>

            </div>


            <div class="service-image">

                <img src="{{ asset('/images/adoption.jpg') }}"
                     alt="Pet adoption">

            </div>


            <h3>
                Pet Adoption
            </h3>

            <p>

                Discover loving pets
                waiting for their
                forever homes.

            </p>

            <a href="#">

                Adopt Now

                <i class="fa-solid fa-arrow-right"></i>

            </a>

        </div>

    </div>

</section>



<!-- =====================================================
     PRODUCTS
===================================================== -->

<section class="products-section"
         id="products">

    <div class="products-container">


        <div class="products-heading reveal-up">

            <div>

                <span>
                    <i class="fa-solid fa-bag-shopping"></i>
                    FEATURED PRODUCTS
                </span>

                <h2>
                    Premium Pet Care Products
                </h2>

                <p>
                    Everything your pet needs,
                    all in one place.
                </p>

            </div>


            <a href="#">

                View All Products

                <i class="fa-solid fa-arrow-right"></i>

            </a>

        </div>


        <div class="products-grid">
            @forelse($products as $product)
                <div class="product-card reveal-up">
                    <div class="product-image">
                        @if($product->is_featured)
                            <span class="product-badge">Featured</span>
                        @endif

                        @if($product->images && $product->images->count() > 0)
                            <img src="{{ Storage::url($product->images->first()->image_path) }}" alt="{{ $product->name }}">
                        @else
                            <img src="{{ asset('/images/dog-food.jpg') }}" alt="{{ $product->name }}">
                        @endif

                        <button><i class="fa-regular fa-heart"></i></button>
                    </div>

                    <div class="product-info">
                        <h3>{{ $product->name }}</h3>
                        <div class="product-rating">
                            ★★★★★
                            <small>(Stock: {{ $product->stock_quantity }})</small>
                        </div>
                        <strong>${{ number_format($product->price, 2) }}</strong>

                        @auth
                            <form method="POST" action="{{ route('owner.cart.add') }}" style="display:inline;">
                                @csrf
                                <input type="hidden" name="product_id" value="{{ $product->id }}">
                                <input type="hidden" name="quantity" value="1">
                                <button type="submit" class="add-cart">Add to Cart</button>
                            </form>
                        @else
                            <a href="{{ route('login') }}" class="add-cart" style="text-decoration:none; display:inline-block; text-align:center;">Add to Cart</a>
                        @endauth
                    </div>
                </div>
            @empty
                <p>No products currently available.</p>
            @endforelse
        </div>

    </div>

</section>



<!-- =====================================================
     WHY FURSHIELD
===================================================== -->

<section class="why-section"
         id="care">

    <div class="why-container">


        <div class="why-image reveal-left">

            <div class="image-glow"></div>

            <img src="{{ asset('/images/pets-pic.jpg') }}"
                 alt="FurShield pets">

            <div class="shield-badge">

                <i class="fa-solid fa-shield-heart"></i>

                <span>
                    Protected
                    <small>with Love</small>
                </span>

            </div>

        </div>


        <div class="why-content reveal-right">

            <span class="small-heading">
                WHY CHOOSE FURSHIELD
            </span>

            <h2>
                Trusted Care for
                <b>Your Beloved Pets</b>
            </h2>

            <p>

                We bring together the best veterinarians,
                shelters and pet-care products — all
                in one powerful platform.

            </p>


            <div class="why-grid">


                <div class="why-card">

                    <i class="fa-solid fa-user-doctor"></i>

                    <div>

                        <strong>
                            Trusted Professionals
                        </strong>

                        <small>
                            Verified & experienced vets
                        </small>

                    </div>

                </div>


                <div class="why-card">

                    <i class="fa-solid fa-shield"></i>

                    <div>

                        <strong>
                            Safe & Secure
                        </strong>

                        <small>
                            Your data is protected
                        </small>

                    </div>

                </div>


                <div class="why-card">

                    <i class="fa-solid fa-headset"></i>

                    <div>

                        <strong>
                            24/7 Support
                        </strong>

                        <small>
                            We're here when needed
                        </small>

                    </div>

                </div>


                <div class="why-card">

                    <i class="fa-solid fa-mobile-screen-button"></i>

                    <div>

                        <strong>
                            Easy & Convenient
                        </strong>

                        <small>
                            Manage everything online
                        </small>

                    </div>

                </div>

            </div>

        </div>

    </div>

</section>



<!-- =====================================================
     TESTIMONIALS
===================================================== -->

<section class="testimonial-section">

    <div class="section-heading reveal-up">

        <span>PET PARENTS SAY</span>

        <h2>
            What Our Customers
            <b>Say</b>
        </h2>

        <p>
            Real stories from real pet parents
            who love FurShield.
        </p>

    </div>


    <div class="testimonial-grid">


        <div class="testimonial-card reveal-up">

            <div class="customer">

                <div class="avatar">
                    SK
                </div>

                <div>

                    <strong>
                        Sarah Khan
                    </strong>

                    <div class="stars">
                        ★★★★★
                    </div>

                </div>

            </div>

            <p>
                "FurShield made it so easy to book
                an appointment for my cat. The vet
                was amazing and very professional."
            </p>

            <small>
                Pet Owner
            </small>

        </div>


        <div class="testimonial-card reveal-up">

            <div class="customer">

                <div class="avatar avatar-blue">
                    AR
                </div>

                <div>

                    <strong>
                        Ahmed Raza
                    </strong>

                    <div class="stars">
                        ★★★★★
                    </div>

                </div>

            </div>

            <p>
                "I found the best food and supplies
                at great prices. The whole experience
                was simple and convenient."
            </p>

            <small>
                Pet Owner
            </small>

        </div>


        <div class="testimonial-card reveal-up">

            <div class="customer">

                <div class="avatar avatar-purple">
                    AM
                </div>

                <div>

                    <strong>
                        Ayesha Malik
                    </strong>

                    <div class="stars">
                        ★★★★★
                    </div>

                </div>

            </div>

            <p>
                "The adoption process was smooth
                and heartwarming. Now I have a
                new family member!"
            </p>

            <small>
                Pet Owner
            </small>

        </div>

    </div>

</section>



<!-- =====================================================
     ADOPTION
===================================================== -->

<section class="adoption-section"
         id="shelters">

    <div class="adoption-image">

        <img src="{{ asset('images/adoption-2.jpg') }}"
             alt="Adopt a pet">

    </div>


    <div class="adoption-content">

        <span>
            <i class="fa-solid fa-heart"></i>
            ADOPT A PET
        </span>

        <h2>
            Give Them a
            <b>Second Chance</b>
        </h2>

        <p>
            Adopt, don't shop. Help give loving homes
            to rescued pets waiting for their forever
            families.
        </p>


        <a href="#"
           class="btn-primary">

            View Adoptable Pets

            <i class="fa-solid fa-arrow-right"></i>

        </a>


        <div class="adoption-stats">

            <div>

                <strong>
                    250+
                </strong>

                <span>
                    Pets Adopted
                </span>

            </div>


            <div>

                <strong>
                    120+
                </strong>

                <span>
                    Happy Families
                </span>

            </div>


            <div>

                <strong>
                    15+
                </strong>

                <span>
                    Shelters
                </span>

            </div>

        </div>

    </div>

</section>



<!-- =====================================================
     CTA
===================================================== -->

<section class="cta-section">

    <div class="cta-inner reveal-up">

        <div class="cta-paw">

            <i class="fa-solid fa-paw"></i>

        </div>


        <div>

            <h2>
                Join FurShield Today
            </h2>

            <p>
                Be a part of a bigger mission —
                healthier pets, happier homes.
            </p>

        </div>


        <a href="{{ route('register') }}"
           class="cta-button">

            Get Started

            <i class="fa-solid fa-arrow-right"></i>

        </a>

    </div>

</section>



<!-- =====================================================
     FOOTER
===================================================== -->

<footer class="footer"
        id="contact">

    <div class="footer-container">


        <div class="footer-brand">

            <a href="#home"
               class="footer-logo">

                <div>
                    <i class="fa-solid fa-paw"></i>
                </div>

                <span>
                    FurShield
                    <small>
                        Every Paw/Wing Deserves a Shield of Love
                    </small>
                </span>

            </a>


            <p>
                Complete pet care made simple,
                smart and full of love.
            </p>


            <div class="social-links">

                <a href="#">
                    <i class="fa-brands fa-facebook-f"></i>
                </a>

                <a href="#">
                    <i class="fa-brands fa-instagram"></i>
                </a>

                <a href="#">
                    <i class="fa-brands fa-twitter"></i>
                </a>

                <a href="#">
                    <i class="fa-brands fa-youtube"></i>
                </a>

            </div>

        </div>


        <div class="footer-column">

            <h4>
                Platform
            </h4>

            <a href="#">
                Pet Owners
            </a>

            <a href="#">
                Veterinarians
            </a>

            <a href="#">
                Shelters
            </a>

            <a href="#">
                Products
            </a>

        </div>


        <div class="footer-column">

            <h4>
                Support
            </h4>

            <a href="#">
                Help Center
            </a>

            <a href="#">
                Contact Us
            </a>

            <a href="#">
                Privacy
            </a>

            <a href="#">
                Terms
            </a>

        </div>


        <div class="footer-column">

            <h4>
                Contact Us
            </h4>

            <p>
                <i class="fa-solid fa-envelope"></i>
                support@furshield.com
            </p>

            <p>
                <i class="fa-solid fa-phone"></i>
                +92 300 1234567
            </p>

            <p>
                <i class="fa-solid fa-location-dot"></i>
                Pakistan
            </p>

        </div>

    </div>


    <div class="footer-bottom">

        <span>
            © {{ date('Y') }} FurShield.
            All Rights Reserved.
        </span>

        <div>
            <a href="#">Privacy Policy</a>
            <a href="#">Terms & Conditions</a>
        </div>

    </div>

</footer>



<!-- BACK TO TOP -->

<button id="backToTop"
        class="back-to-top">

    <i class="fa-solid fa-arrow-up"></i>

</button>


<script src="{{ asset('js/furshield.js') }}"></script>

</body>
</html>