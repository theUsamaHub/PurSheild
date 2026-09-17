<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">

    <meta name="viewport"
          content="width=device-width, initial-scale=1.0">

    <title>Our Services | FurShield</title>

    <meta name="description"
          content="Explore FurShield premium pet care services including veterinary care, health records, adoption, pet profiles, grooming, products and shelter support.">

    {{-- Google Font --}}
    <link rel="preconnect"
          href="https://fonts.googleapis.com">

    <link rel="preconnect"
          href="https://fonts.gstatic.com"
          crossorigin>

    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@400;500;600;700;800&display=swap"
          rel="stylesheet">

    {{-- Font Awesome --}}
    <link rel="stylesheet"
          href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.2/css/all.min.css">

    {{-- Service CSS --}}
    <link rel="stylesheet"
          href="{{ asset('css/service.css') }}">
</head>

<body>

{{-- =====================================================
     PARTICLE BACKGROUND
===================================================== --}}
<canvas id="serviceParticles"></canvas>


{{-- =====================================================
     NAVBAR
===================================================== --}}
@include('partials.furshield-navbar', ['activePage' => 'service'])



{{-- =====================================================
     HERO SECTION
===================================================== --}}
<section class="service-hero">

    {{-- Decoration --}}
    <div class="service-hero-glow glow-one"></div>
    <div class="service-hero-glow glow-two"></div>

    <div class="floating-service-icon floating-icon-one">
        <i class="fa-solid fa-paw"></i>
    </div>

    <div class="floating-service-icon floating-icon-two">
        <i class="fa-solid fa-heart"></i>
    </div>

    <div class="floating-service-icon floating-icon-three">
        <i class="fa-solid fa-bone"></i>
    </div>


    <div class="service-container service-hero-container">

        {{-- Left --}}
        <div class="service-hero-content service-reveal-left">

            <div class="service-section-pill light-pill">

                <span></span>

                PREMIUM PET CARE SERVICES

            </div>


            <h1>
                Everything Your Pet Needs

                <span>
                    Under One Shield
                </span>
            </h1>


            <p>
                FurShield brings trusted veterinary care, pet health
                management, adoption support, products, wellness services
                and compassionate care together in one premium platform.
            </p>


            <div class="service-hero-buttons">

                <a href="#premiumServices"
                   class="service-primary-btn ripple-btn">

                    Explore Services

                    <i class="fa-solid fa-arrow-right"></i>
                </a>


                <a href="#videoSection"
                   class="service-secondary-btn ripple-btn">

                    <i class="fa-solid fa-circle-play"></i>

                    Watch Our Story
                </a>

            </div>


            <div class="service-trust-row">

                <div>
                    <i class="fa-solid fa-circle-check"></i>

                    Trusted Care
                </div>

                <div>
                    <i class="fa-solid fa-circle-check"></i>

                    Pet First
                </div>

                <div>
                    <i class="fa-solid fa-circle-check"></i>

                    Secure Platform
                </div>

            </div>

        </div>


        {{-- Right Image --}}
        <div class="service-hero-visual service-reveal-right">

            <div class="service-image-ring"></div>

            <div class="service-main-image">

                <img src="{{ asset('images/service-pets.jpg') }}"
                     alt="FurShield pet care services">

            </div>


            <div class="hero-service-card hero-service-card-one">

                <div class="hero-card-icon">
                    <i class="fa-solid fa-user-doctor"></i>
                </div>

                <div>
                    <strong>500+</strong>
                    <span>Trusted Vets</span>
                </div>

            </div>


            <div class="hero-service-card hero-service-card-two">

                <div class="hero-card-icon">
                    <i class="fa-solid fa-heart-pulse"></i>
                </div>

                <div>
                    <strong>24/7</strong>
                    <span>Care Support</span>
                </div>

            </div>


            <div class="hero-service-rating">

                <div class="rating-stars">
                    <i class="fa-solid fa-star"></i>
                    <i class="fa-solid fa-star"></i>
                    <i class="fa-solid fa-star"></i>
                    <i class="fa-solid fa-star"></i>
                    <i class="fa-solid fa-star"></i>
                </div>

                <strong>4.9 / 5</strong>

                <span>Pet Parent Rating</span>

            </div>

        </div>

    </div>

</section>



{{-- =====================================================
     STATS
===================================================== --}}
<section class="service-stats-section">

    <div class="service-container">

        <div class="service-stats-box service-reveal-up">

            <div class="service-stat-card">

                <div class="stat-icon">
                    <i class="fa-solid fa-paw"></i>
                </div>

                <div>

                    <strong data-target="10"
                            data-suffix="K+">
                        0
                    </strong>

                    <span>Happy Pets</span>

                </div>

            </div>


            <div class="service-stat-card">

                <div class="stat-icon">
                    <i class="fa-solid fa-user-doctor"></i>
                </div>

                <div>

                    <strong data-target="500"
                            data-suffix="+">
                        0
                    </strong>

                    <span>Veterinarians</span>

                </div>

            </div>


            <div class="service-stat-card">

                <div class="stat-icon">
                    <i class="fa-solid fa-house-chimney"></i>
                </div>

                <div>

                    <strong data-target="120"
                            data-suffix="+">
                        0
                    </strong>

                    <span>Animal Shelters</span>

                </div>

            </div>


            <div class="service-stat-card">

                <div class="stat-icon">
                    <i class="fa-solid fa-heart"></i>
                </div>

                <div>

                    <strong data-target="25"
                            data-suffix="K+">
                        0
                    </strong>

                    <span>Lives Supported</span>

                </div>

            </div>

        </div>

    </div>

</section>



{{-- =====================================================
     SERVICES INTRODUCTION
===================================================== --}}
<section class="service-intro-section">

    <div class="service-container service-intro-grid">

        {{-- Image --}}
        <div class="service-intro-image service-reveal-left">

            <div class="service-intro-image-wrapper">

                <img src="{{ asset('images/hero-pets.jpg') }}"
                     alt="Premium pet care">

                <div class="intro-experience-card">

                    <div class="experience-icon">
                        <i class="fa-solid fa-shield-heart"></i>
                    </div>

                    <div>
                        <strong>Complete Care</strong>
                        <span>One Trusted Platform</span>
                    </div>

                </div>

            </div>

        </div>


        {{-- Content --}}
        <div class="service-intro-content service-reveal-right">

            <div class="service-section-pill">

                <span></span>

                COMPLETE PET CARE
            </div>


            <h2>
                Care That Goes Beyond

                <span>Basic Pet Services</span>
            </h2>


            <p>
                Your pet deserves more than basic care. FurShield connects
                pet owners, veterinarians and shelters through one
                intelligent ecosystem designed around safety, health,
                happiness and long-term wellbeing.
            </p>


            <div class="service-intro-features">

                <div class="intro-feature">

                    <div class="intro-feature-icon">
                        <i class="fa-solid fa-circle-check"></i>
                    </div>

                    <div>
                        <strong>Verified Care Network</strong>

                        <span>
                            Connect with trusted veterinary and pet-care
                            professionals.
                        </span>
                    </div>

                </div>


                <div class="intro-feature">

                    <div class="intro-feature-icon">
                        <i class="fa-solid fa-circle-check"></i>
                    </div>

                    <div>
                        <strong>Centralized Pet Management</strong>

                        <span>
                            Keep profiles, medical history and care information
                            organized.
                        </span>
                    </div>

                </div>


                <div class="intro-feature">

                    <div class="intro-feature-icon">
                        <i class="fa-solid fa-circle-check"></i>
                    </div>

                    <div>
                        <strong>Compassionate Community</strong>

                        <span>
                            Helping owners, shelters and professionals support
                            more pets.
                        </span>
                    </div>

                </div>

            </div>


            <a href="#premiumServices"
               class="service-text-link">

                Discover Our Services

                <i class="fa-solid fa-arrow-right"></i>
            </a>

        </div>

    </div>

</section>



{{-- =====================================================
     PREMIUM SERVICES GRID
===================================================== --}}
<section class="premium-services-section"
         id="premiumServices">

    <div class="service-container">

        <div class="service-heading service-reveal-up">

            <div class="service-section-pill center-pill">
                <span></span>
                OUR SERVICES
            </div>

            <h2>
                Premium Services For

                <span>Every Stage Of Pet Life</span>
            </h2>

            <p>
                Everything you need to manage your pet's health, wellbeing
                and happiness through one beautifully connected platform.
            </p>

        </div>


        <div class="premium-services-grid">


            {{-- Pet Profiles --}}
            <article class="premium-service-card service-reveal-up">

                <div class="premium-service-image">

                    <img src="{{ asset('images/service-pets.jpg') }}"
                         alt="Pet Profiles">

                    <div class="service-card-icon">
                        <i class="fa-solid fa-dog"></i>
                    </div>

                </div>


                <div class="premium-service-content">

                    <span class="service-number">01</span>

                    <h3>Pet Profiles</h3>

                    <p>
                        Create and manage detailed digital profiles for all
                        your pets with essential information in one place.
                    </p>

                    <div class="service-card-features">

                        <span>
                            <i class="fa-solid fa-check"></i>
                            Multiple Pets
                        </span>

                        <span>
                            <i class="fa-solid fa-check"></i>
                            Personal Details
                        </span>

                        <span>
                            <i class="fa-solid fa-check"></i>
                            Pet Photo Gallery
                        </span>

                    </div>

                    <a href="#">
                        Learn More

                        <i class="fa-solid fa-arrow-right"></i>
                    </a>

                </div>

            </article>



            {{-- Health Records --}}
            <article class="premium-service-card service-reveal-up">

                <div class="premium-service-image">

                    <img src="{{ asset('images/health.jpg') }}"
                         alt="Pet Health Records">

                    <div class="service-card-icon">
                        <i class="fa-solid fa-heart-pulse"></i>
                    </div>

                </div>


                <div class="premium-service-content">

                    <span class="service-number">02</span>

                    <h3>Health Records</h3>

                    <p>
                        Maintain vaccination records, treatments, medical
                        history and important pet health information.
                    </p>

                    <div class="service-card-features">

                        <span>
                            <i class="fa-solid fa-check"></i>
                            Vaccination History
                        </span>

                        <span>
                            <i class="fa-solid fa-check"></i>
                            Treatment Records
                        </span>

                        <span>
                            <i class="fa-solid fa-check"></i>
                            Medical Timeline
                        </span>

                    </div>

                    <a href="#">
                        Learn More

                        <i class="fa-solid fa-arrow-right"></i>
                    </a>

                </div>

            </article>



            {{-- Veterinary --}}
            <article class="premium-service-card service-reveal-up">

                <div class="premium-service-image">

                    <img src="{{ asset('images/vet.jpg') }}"
                         alt="Veterinary appointments">

                    <div class="service-card-icon">
                        <i class="fa-solid fa-user-doctor"></i>
                    </div>

                </div>


                <div class="premium-service-content">

                    <span class="service-number">03</span>

                    <h3>Vet Appointments</h3>

                    <p>
                        Find veterinarians and request appointments through
                        an easy, modern and organized booking experience.
                    </p>

                    <div class="service-card-features">

                        <span>
                            <i class="fa-solid fa-check"></i>
                            Find Veterinarians
                        </span>

                        <span>
                            <i class="fa-solid fa-check"></i>
                            Easy Scheduling
                        </span>

                        <span>
                            <i class="fa-solid fa-check"></i>
                            Appointment History
                        </span>

                    </div>

                    <a href="#">
                        Book Appointment

                        <i class="fa-solid fa-arrow-right"></i>
                    </a>

                </div>

            </article>



            {{-- Adoption --}}
            <article class="premium-service-card service-reveal-up">

                <div class="premium-service-image">

                    <img src="{{ asset('images/adoption.jpg') }}"
                         alt="Pet Adoption">

                    <div class="service-card-icon">
                        <i class="fa-solid fa-house-chimney-heart"></i>
                    </div>

                </div>


                <div class="premium-service-content">

                    <span class="service-number">04</span>

                    <h3>Pet Adoption</h3>

                    <p>
                        Discover pets searching for loving homes and connect
                        directly with participating animal shelters.
                    </p>

                    <div class="service-card-features">

                        <span>
                            <i class="fa-solid fa-check"></i>
                            Browse Adoptable Pets
                        </span>

                        <span>
                            <i class="fa-solid fa-check"></i>
                            Shelter Connection
                        </span>

                        <span>
                            <i class="fa-solid fa-check"></i>
                            Adoption Support
                        </span>

                    </div>

                    <a href="#">
                        Explore Adoption

                        <i class="fa-solid fa-arrow-right"></i>
                    </a>

                </div>

            </article>



            {{-- Products --}}
            <article class="premium-service-card service-reveal-up">

                <div class="premium-service-image">

                    <img src="{{ asset('images/dog-food.jpg') }}"
                         alt="Pet Products">

                    <div class="service-card-icon">
                        <i class="fa-solid fa-bag-shopping"></i>
                    </div>

                </div>


                <div class="premium-service-content">

                    <span class="service-number">05</span>

                    <h3>Pet Products</h3>

                    <p>
                        Browse essential pet products including food,
                        grooming supplies, vitamins, toys and accessories.
                    </p>

                    <div class="service-card-features">

                        <span>
                            <i class="fa-solid fa-check"></i>
                            Pet Nutrition
                        </span>

                        <span>
                            <i class="fa-solid fa-check"></i>
                            Toys & Accessories
                        </span>

                        <span>
                            <i class="fa-solid fa-check"></i>
                            Wellness Products
                        </span>

                    </div>

                    <a href="{{ route('products') }}">
                        Browse Products

                        <i class="fa-solid fa-arrow-right"></i>
                    </a>

                </div>

            </article>



            {{-- Grooming --}}
            <article class="premium-service-card service-reveal-up">

                <div class="premium-service-image">

                    <img src="{{ asset('images/shampoo.jpg') }}"
                         alt="Pet grooming and wellness">

                    <div class="service-card-icon">
                        <i class="fa-solid fa-scissors"></i>
                    </div>

                </div>


                <div class="premium-service-content">

                    <span class="service-number">06</span>

                    <h3>Grooming & Wellness</h3>

                    <p>
                        Support your pet's hygiene and wellness through
                        helpful grooming products and care resources.
                    </p>

                    <div class="service-card-features">

                        <span>
                            <i class="fa-solid fa-check"></i>
                            Grooming Guidance
                        </span>

                        <span>
                            <i class="fa-solid fa-check"></i>
                            Hygiene Essentials
                        </span>

                        <span>
                            <i class="fa-solid fa-check"></i>
                            Wellness Support
                        </span>

                    </div>

                    <a href="#">
                        Explore Wellness

                        <i class="fa-solid fa-arrow-right"></i>
                    </a>

                </div>

            </article>



            {{-- Shelter --}}
            <article class="premium-service-card service-reveal-up">

                <div class="premium-service-image">

                    <img src="{{ asset('images/adoption-2.jpg') }}"
                         alt="Animal Shelter Support">

                    <div class="service-card-icon">
                        <i class="fa-solid fa-house"></i>
                    </div>

                </div>


                <div class="premium-service-content">

                    <span class="service-number">07</span>

                    <h3>Shelter Support</h3>

                    <p>
                        Give shelters tools to manage pet listings, care
                        information and communication with adopters.
                    </p>

                    <div class="service-card-features">

                        <span>
                            <i class="fa-solid fa-check"></i>
                            Pet Listings
                        </span>

                        <span>
                            <i class="fa-solid fa-check"></i>
                            Care Logs
                        </span>

                        <span>
                            <i class="fa-solid fa-check"></i>
                            Adopter Coordination
                        </span>

                    </div>

                    <a href="{{ route('shelters') }}">
                        Explore Shelters

                        <i class="fa-solid fa-arrow-right"></i>
                    </a>

                </div>

            </article>



            {{-- Resources --}}
            <article class="premium-service-card service-reveal-up">

                <div class="premium-service-image">

                    <img src="{{ asset('images/pets-pic.jpg') }}"
                         alt="Pet Care Resources">

                    <div class="service-card-icon">
                        <i class="fa-solid fa-book-open"></i>
                    </div>

                </div>


                <div class="premium-service-content">

                    <span class="service-number">08</span>

                    <h3>Care Resources</h3>

                    <p>
                        Learn through helpful pet care articles, videos,
                        guides and frequently asked questions.
                    </p>

                    <div class="service-card-features">

                        <span>
                            <i class="fa-solid fa-check"></i>
                            Care Articles
                        </span>

                        <span>
                            <i class="fa-solid fa-check"></i>
                            Educational Videos
                        </span>

                        <span>
                            <i class="fa-solid fa-check"></i>
                            Helpful FAQs
                        </span>

                    </div>

                    <a href="{{ route('care') }}">
                        Learn About Care

                        <i class="fa-solid fa-arrow-right"></i>
                    </a>

                </div>

            </article>

        </div>

    </div>

</section>



{{-- =====================================================
     HOW IT WORKS
===================================================== --}}
<section class="service-process-section">

    <div class="service-container">

        <div class="service-heading service-reveal-up">

            <div class="service-section-pill center-pill">

                <span></span>

                SIMPLE PROCESS

            </div>

            <h2>
                Better Pet Care In

                <span>Four Simple Steps</span>
            </h2>

            <p>
                FurShield makes pet management simple, connected and easy
                for owners, veterinarians and shelters.
            </p>

        </div>


        <div class="service-process-grid">

            <div class="process-card service-reveal-up">

                <div class="process-number">01</div>

                <div class="process-icon">
                    <i class="fa-solid fa-user-plus"></i>
                </div>

                <h3>Create Account</h3>

                <p>
                    Register on FurShield and access your personalized
                    pet-care dashboard.
                </p>

                <div class="process-line"></div>

            </div>


            <div class="process-card service-reveal-up">

                <div class="process-number">02</div>

                <div class="process-icon">
                    <i class="fa-solid fa-paw"></i>
                </div>

                <h3>Add Your Pet</h3>

                <p>
                    Build detailed profiles for your pets and keep all their
                    information organized.
                </p>

                <div class="process-line"></div>

            </div>


            <div class="process-card service-reveal-up">

                <div class="process-number">03</div>

                <div class="process-icon">
                    <i class="fa-solid fa-stethoscope"></i>
                </div>

                <h3>Choose A Service</h3>

                <p>
                    Access vets, health records, care resources, products
                    and adoption services.
                </p>

                <div class="process-line"></div>

            </div>


            <div class="process-card service-reveal-up">

                <div class="process-number">04</div>

                <div class="process-icon">
                    <i class="fa-solid fa-heart"></i>
                </div>

                <h3>Care With Confidence</h3>

                <p>
                    Stay informed and give your pet a healthier and happier
                    life with FurShield.
                </p>

            </div>

        </div>

    </div>

</section>



{{-- =====================================================
     VIDEO SECTION
===================================================== --}}
<section class="service-video-section"
         id="videoSection">

    <video id="serviceVideo"
           class="service-video"
           autoplay
           muted
           loop
           playsinline
           poster="{{ asset('images/hero-pets.jpg') }}">

        <source src="{{ asset('videos/pet-care.mp4') }}"
                type="video/mp4">

    </video>


    <div class="service-video-overlay"></div>


    <div class="service-video-content service-reveal-up">

        <span class="video-small-title">
            WATCH OUR STORY
        </span>

        <h2>
            Because Every Pet Deserves

            <span>Premium Care</span>
        </h2>

        <p>
            FurShield is designed to help pet owners make smarter,
            easier and more compassionate care decisions.
        </p>


        <button type="button"
                id="serviceVideoToggle"
                class="service-video-toggle">

            <span class="video-play-icon">

                <i class="fa-solid fa-pause"></i>

            </span>

            <span class="video-control-label">
                Pause Video
            </span>

        </button>

    </div>

</section>



{{-- =====================================================
     WHY CHOOSE FURSHIELD
===================================================== --}}
<section class="service-why-section">

    <div class="service-container service-why-grid">

        {{-- Content --}}
        <div class="service-why-content service-reveal-left">

            <div class="service-section-pill">

                <span></span>

                WHY FURSHIELD

            </div>

            <h2>
                Built Around

                <span>Your Pet's Wellbeing</span>
            </h2>

            <p>
                We combine care, convenience and modern technology so pet
                owners can manage important needs without jumping between
                different platforms.
            </p>


            <div class="service-why-list">

                <div class="service-why-item">

                    <div class="why-item-icon">
                        <i class="fa-solid fa-shield-heart"></i>
                    </div>

                    <div>

                        <h4>Safety First</h4>

                        <p>
                            Pet wellbeing stays at the center of every
                            FurShield experience.
                        </p>

                    </div>

                </div>


                <div class="service-why-item">

                    <div class="why-item-icon">
                        <i class="fa-solid fa-clock"></i>
                    </div>

                    <div>

                        <h4>Save Valuable Time</h4>

                        <p>
                            Manage pet profiles, health information and
                            appointments from one place.
                        </p>

                    </div>

                </div>


                <div class="service-why-item">

                    <div class="why-item-icon">
                        <i class="fa-solid fa-users"></i>
                    </div>

                    <div>

                        <h4>Connected Community</h4>

                        <p>
                            Bringing pet owners, veterinarians and shelters
                            closer together.
                        </p>

                    </div>

                </div>


                <div class="service-why-item">

                    <div class="why-item-icon">
                        <i class="fa-solid fa-mobile-screen"></i>
                    </div>

                    <div>

                        <h4>Easy To Use</h4>

                        <p>
                            A modern responsive experience built for desktop,
                            tablet and mobile.
                        </p>

                    </div>

                </div>

            </div>

        </div>


        {{-- Visual --}}
        <div class="service-why-visual service-reveal-right">

            <div class="why-main-image">

                <img src="{{ asset('images/pets-pic.jpg') }}"
                     alt="Why choose FurShield">

            </div>


            <div class="why-floating-card why-floating-card-one">

                <div>
                    <i class="fa-solid fa-shield"></i>
                </div>

                <span>
                    <strong>Trusted</strong>
                    Pet Platform
                </span>

            </div>


            <div class="why-floating-card why-floating-card-two">

                <div>
                    <i class="fa-solid fa-heart-pulse"></i>
                </div>

                <span>
                    <strong>Complete</strong>
                    Health Support
                </span>

            </div>

        </div>

    </div>

</section>



{{-- =====================================================
     FEATURE BANNER
===================================================== --}}
<section class="service-feature-banner">

    <div class="service-container">

        <div class="feature-banner-wrapper service-reveal-up">

            <div class="feature-banner-item">

                <div>
                    <i class="fa-solid fa-heart"></i>
                </div>

                <span>
                    <strong>Pet First</strong>
                    Compassionate Care
                </span>

            </div>


            <div class="feature-banner-divider"></div>


            <div class="feature-banner-item">

                <div>
                    <i class="fa-solid fa-user-doctor"></i>
                </div>

                <span>
                    <strong>Trusted Vets</strong>
                    Professional Support
                </span>

            </div>


            <div class="feature-banner-divider"></div>


            <div class="feature-banner-item">

                <div>
                    <i class="fa-solid fa-shield"></i>
                </div>

                <span>
                    <strong>Secure</strong>
                    Organized Platform
                </span>

            </div>


            <div class="feature-banner-divider"></div>


            <div class="feature-banner-item">

                <div>
                    <i class="fa-solid fa-headset"></i>
                </div>

                <span>
                    <strong>Support</strong>
                    Here When Needed
                </span>

            </div>

        </div>

    </div>

</section>



{{-- =====================================================
     CTA
===================================================== --}}
<section class="service-cta-section">

    <div class="cta-decoration cta-decoration-one"></div>
    <div class="cta-decoration cta-decoration-two"></div>

    <div class="service-container">

        <div class="service-cta-content service-reveal-up">

            <div class="cta-icon">
                <i class="fa-solid fa-paw"></i>
            </div>

            <span class="cta-small-title">
                START YOUR JOURNEY
            </span>

            <h2>
                Give Your Pet The Care

                <span>They Truly Deserve</span>
            </h2>

            <p>
                Join FurShield and manage your pet's complete care journey
                from one trusted platform.
            </p>


            <div class="service-cta-buttons">

                <a href="{{ route('register') }}"
                   class="service-cta-primary ripple-btn">

                    Join FurShield

                    <i class="fa-solid fa-arrow-right"></i>
                </a>


                <a href="{{ route('contact') }}"
                   class="service-cta-secondary ripple-btn">

                    Contact Us
                </a>

            </div>

        </div>

    </div>

</section>



{{-- =====================================================
     FOOTER
===================================================== --}}
<footer class="service-footer">

    <div class="service-container">

        <div class="service-footer-grid">

            {{-- Brand --}}
            <div class="service-footer-brand">

                <a href="{{ route('home') }}"
                   class="footer-logo">

                    <div class="footer-logo-icon">
                        <i class="fa-solid fa-shield-dog"></i>
                    </div>

                    <div>
                        <strong>FurShield</strong>
                        <span>Protect • Care • Love</span>
                    </div>

                </a>

                <p>
                    Your complete pet care platform connecting pet owners,
                    veterinarians, shelters and essential pet services.
                </p>


                <div class="service-social-links">

                    <a href="#"
                       aria-label="Facebook">
                        <i class="fa-brands fa-facebook-f"></i>
                    </a>

                    <a href="#"
                       aria-label="Instagram">
                        <i class="fa-brands fa-instagram"></i>
                    </a>

                    <a href="#"
                       aria-label="Twitter">
                        <i class="fa-brands fa-x-twitter"></i>
                    </a>

                    <a href="#"
                       aria-label="Youtube">
                        <i class="fa-brands fa-youtube"></i>
                    </a>

                </div>

            </div>


            {{-- Platform --}}
            <div class="service-footer-column">

                <h4>Platform</h4>

                <a href="{{ route('home') }}">Home</a>

                <a href="{{ route('about') }}">About Us</a>

                <a href="{{ route('vets') }}">Vets</a>

                <a href="{{ route('products') }}">Products</a>

                <a href="{{ route('care') }}">Pet Care</a>

            </div>


            {{-- Services --}}
            <div class="service-footer-column">

                <h4>Our Services</h4>

                <a href="#premiumServices">Pet Profiles</a>

                <a href="#premiumServices">Health Records</a>

                <a href="#premiumServices">Vet Appointments</a>

                <a href="#premiumServices">Pet Adoption</a>

                <a href="#premiumServices">Shelter Support</a>

            </div>


            {{-- Support --}}
            <div class="service-footer-column">

                <h4>Support</h4>

                <a href="#">Help Center</a>

                <a href="#">FAQs</a>

                <a href="#">Privacy Policy</a>

                <a href="#">Terms & Conditions</a>

                <a href="{{ route('contact') }}">Contact</a>

            </div>


            {{-- Contact --}}
            <div class="service-footer-column service-footer-contact">

                <h4>Get In Touch</h4>

                <div>

                    <i class="fa-solid fa-envelope"></i>

                    <span>
                        support@furshield.com
                    </span>

                </div>

                <div>

                    <i class="fa-solid fa-phone"></i>

                    <span>
                        +92 300 1234567
                    </span>

                </div>

                <div>

                    <i class="fa-solid fa-location-dot"></i>

                    <span>
                        Pakistan
                    </span>

                </div>

            </div>

        </div>


        <div class="service-footer-bottom">

            <p>
                © {{ date('Y') }} FurShield. All rights reserved.
            </p>

            <p>
                Made with
                <i class="fa-solid fa-heart"></i>
                for pets everywhere.
            </p>

        </div>

    </div>

</footer>



{{-- =====================================================
     BACK TO TOP
===================================================== --}}
<button id="serviceBackTop"
        class="service-back-top"
        aria-label="Back to top">

    <i class="fa-solid fa-arrow-up"></i>

</button>


{{-- =====================================================
     JS
===================================================== --}}
<script src="{{ asset('js/service.js') }}"></script>

</body>
</html>