<!DOCTYPE html>
<html lang="en">
<head>

    <meta charset="UTF-8">

    <meta name="viewport"
          content="width=device-width, initial-scale=1.0">

    <title>About Us | FurShield</title>

    <meta name="description"
          content="Learn about FurShield - a complete pet care platform connecting pet owners, veterinarians, shelters and quality pet products.">

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
          href="{{ asset('css/inner-pages.css') }}">

    <link rel="stylesheet"
          href="{{ asset('css/about.css') }}">

</head>

<body>

<canvas id="aboutParticles"></canvas>


<!-- =====================================================
     NAVBAR
===================================================== -->

@include('partials.furshield-navbar', ['activePage' => 'about'])



<!-- =====================================================
     ABOUT HERO
===================================================== -->

<section class="about-hero">

    <div class="about-hero-glow"></div>


    <div class="about-floating about-float-1">
        <i class="fa-solid fa-paw"></i>
    </div>

    <div class="about-floating about-float-2">
        <i class="fa-solid fa-heart"></i>
    </div>

    <div class="about-floating about-float-3">
        <i class="fa-solid fa-shield-heart"></i>
    </div>

    <div class="about-floating about-float-4">
        <i class="fa-solid fa-star"></i>
    </div>


    <div class="about-hero-container">


        <div class="about-hero-content about-reveal-left">

            <div class="about-pill">

                <i class="fa-solid fa-shield-heart"></i>

                ABOUT FURSHIELD

            </div>


            <h1>

                Caring For Pets

                <br>

                <span>With Love & Purpose</span>

            </h1>


            <p>

                FurShield is a complete pet care platform
                created to make pet care easier, smarter
                safer and more connected for every
                pet and every loving family.

            </p>


            <div class="about-hero-buttons">

                <a href="#our-story"
                   class="about-primary-btn">

                    Our Story

                    <i class="fa-solid fa-arrow-down"></i>

                </a>


                <a href="{{ route('vets') }}"
                   class="about-outline-btn">

                    Explore Services

                    <i class="fa-solid fa-arrow-right"></i>

                </a>

            </div>


            <div class="about-mini-points">

                <div>

                    <i class="fa-solid fa-heart"></i>

                    <span>
                        Pet First
                        <small>Always</small>
                    </span>

                </div>


                <div>

                    <i class="fa-solid fa-shield"></i>

                    <span>
                        Safe & Secure
                        <small>Trusted Platform</small>
                    </span>

                </div>


                <div>

                    <i class="fa-solid fa-users"></i>

                    <span>
                        One Community
                        <small>Pet Lovers</small>
                    </span>

                </div>

            </div>

        </div>



        <div class="about-hero-visual about-reveal-right">

            <div class="about-image-glow"></div>

            <div class="about-image-circle"></div>


            <img src="{{ asset('images/pets.jpg') }}"
                 alt="FurShield pets"
                 class="about-main-image">


            <div class="about-floating-card about-care-card">

                <div class="about-card-icon">

                    <i class="fa-solid fa-shield-heart"></i>

                </div>

                <div>

                    <strong>Pet Care</strong>

                    <small>Made With Love</small>

                </div>

            </div>


            <div class="about-floating-card about-rating-card">

                <div class="about-stars">
                    ★★★★★
                </div>

                <strong>4.9/5</strong>

                <small>Pet Parents</small>

            </div>


            <div class="about-heart">

                <i class="fa-solid fa-heart"></i>

            </div>

        </div>

    </div>


    <div class="about-hero-wave"></div>

</section>



<!-- =====================================================
     STATS
===================================================== -->

<section class="about-stats-section">

    <div class="about-stats-box about-reveal-up">


        <div class="about-stat">

            <div class="about-stat-icon">

                <i class="fa-solid fa-paw"></i>

            </div>

            <div>

                <strong data-target="10000">
                    0
                </strong>

                <span>
                    Happy Pets
                </span>

            </div>

        </div>


        <div class="about-stat">

            <div class="about-stat-icon">

                <i class="fa-solid fa-user-doctor"></i>

            </div>

            <div>

                <strong data-target="500">
                    0
                </strong>

                <span>
                    Veterinarians
                </span>

            </div>

        </div>


        <div class="about-stat">

            <div class="about-stat-icon">

                <i class="fa-solid fa-house"></i>

            </div>

            <div>

                <strong data-target="120">
                    0
                </strong>

                <span>
                    Animal Shelters
                </span>

            </div>

        </div>


        <div class="about-stat">

            <div class="about-stat-icon">

                <i class="fa-solid fa-heart"></i>

            </div>

            <div>

                <strong data-target="25000">
                    0
                </strong>

                <span>
                    Lives Supported
                </span>

            </div>

        </div>

    </div>

</section>



<!-- =====================================================
     OUR STORY
===================================================== -->

<section class="about-story-section"
         id="our-story">

    <div class="about-story-container">


        <div class="about-story-image about-reveal-left">

            <div class="about-story-image-bg"></div>

            <img src="{{ asset('images/hero-pets.jpg') }}"
                 alt="Happy pets at FurShield">


            <div class="about-story-badge">

                <i class="fa-solid fa-heart"></i>

                <div>

                    <strong>
                        Built With Love
                    </strong>

                    <small>
                        For Every Pet
                    </small>

                </div>

            </div>

        </div>


        <div class="about-story-content about-reveal-right">

            <span class="about-section-label">
                OUR STORY
            </span>

            <h2>

                More Than A Platform,

                <b>
                    A Promise To Care
                </b>

            </h2>


            <p>

                FurShield was created around one simple
                idea: every pet deserves access to
                quality care, protection and love.

            </p>


            <p>

                Pet owners often need to manage many
                different things — veterinary appointments,
                health records, products, adoption and
                everyday care.

            </p>


            <p>

                FurShield brings these important services
                together in one connected platform so
                pet parents can spend less time searching
                and more time caring.

            </p>


            <div class="about-story-features">

                <div>

                    <i class="fa-solid fa-check"></i>

                    <span>
                        Simple & Convenient
                    </span>

                </div>

                <div>

                    <i class="fa-solid fa-check"></i>

                    <span>
                        Pet-Centered Approach
                    </span>

                </div>

                <div>

                    <i class="fa-solid fa-check"></i>

                    <span>
                        Connected Pet Community
                    </span>

                </div>

                <div>

                    <i class="fa-solid fa-check"></i>

                    <span>
                        Quality Care Resources
                    </span>

                </div>

            </div>

        </div>

    </div>

</section>



<!-- =====================================================
     MISSION & VISION
===================================================== -->

<section class="mission-section">

    <div class="about-section-heading about-reveal-up">

        <span>
            WHAT DRIVES US
        </span>

        <h2>

            Our Mission &

            <b>Vision</b>

        </h2>

        <p>

            Everything we build is focused on
            creating better lives for pets and
            the people who love them.

        </p>

    </div>


    <div class="mission-grid">


        <div class="mission-card mission-card-main about-reveal-up">

            <div class="mission-number">
                01
            </div>

            <div class="mission-icon">

                <i class="fa-solid fa-bullseye"></i>

            </div>

            <h3>
                Our Mission
            </h3>

            <p>

                To make quality pet care accessible,
                organized and convenient by connecting
                pet owners with veterinarians, shelters,
                care resources and trusted products.

            </p>

            <div class="mission-bottom">

                <i class="fa-solid fa-paw"></i>

                <span>
                    Better Care. Happier Pets.
                </span>

            </div>

        </div>



        <div class="mission-card about-reveal-up">

            <div class="mission-number">
                02
            </div>

            <div class="mission-icon">

                <i class="fa-solid fa-eye"></i>

            </div>

            <h3>
                Our Vision
            </h3>

            <p>

                We envision a world where every pet
                owner can easily find the support,
                knowledge and services needed to
                give their companion a healthy
                and happy life.

            </p>

            <div class="mission-bottom">

                <i class="fa-solid fa-heart"></i>

                <span>
                    A Healthier Pet Community
                </span>

            </div>

        </div>



        <div class="mission-card about-reveal-up">

            <div class="mission-number">
                03
            </div>

            <div class="mission-icon">

                <i class="fa-solid fa-hand-holding-heart"></i>

            </div>

            <h3>
                Our Promise
            </h3>

            <p>

                We continuously work to create a
                simple, welcoming and reliable
                experience that puts the wellbeing
                of pets first.

            </p>

            <div class="mission-bottom">

                <i class="fa-solid fa-shield-heart"></i>

                <span>
                    Every Paw Deserves Protection
                </span>

            </div>

        </div>

    </div>

</section>



<!-- =====================================================
     WHY FURSHIELD
===================================================== -->

<section class="about-why-section">

    <div class="about-why-container">


        <div class="about-why-content about-reveal-left">

            <span class="about-section-label">
                WHY FURSHIELD
            </span>

            <h2>

                Everything Your Pet

                <b>
                    Needs In One Place
                </b>

            </h2>

            <p>

                FurShield connects the essential parts
                of pet care into one simple experience.

            </p>


            <div class="about-why-list">


                <div class="about-why-item">

                    <div class="about-why-icon">

                        <i class="fa-solid fa-user-doctor"></i>

                    </div>

                    <div>

                        <strong>
                            Veterinary Care
                        </strong>

                        <small>
                            Find veterinarians and manage
                            appointments with ease.
                        </small>

                    </div>

                </div>


                <div class="about-why-item">

                    <div class="about-why-icon">

                        <i class="fa-solid fa-file-medical"></i>

                    </div>

                    <div>

                        <strong>
                            Health Management
                        </strong>

                        <small>
                            Keep important health records,
                            treatments and vaccinations organized.
                        </small>

                    </div>

                </div>


                <div class="about-why-item">

                    <div class="about-why-icon">

                        <i class="fa-solid fa-house-heart"></i>

                    </div>

                    <div>

                        <strong>
                            Pet Adoption
                        </strong>

                        <small>
                            Discover pets waiting for
                            loving forever homes.
                        </small>

                    </div>

                </div>


                <div class="about-why-item">

                    <div class="about-why-icon">

                        <i class="fa-solid fa-cart-shopping"></i>

                    </div>

                    <div>

                        <strong>
                            Quality Products
                        </strong>

                        <small>
                            Explore essential products
                            for everyday pet care.
                        </small>

                    </div>

                </div>

            </div>

        </div>


        <div class="about-why-visual about-reveal-right">

            <div class="about-why-glow"></div>

            <img src="{{ asset('images/service-pet.jpg') }}"
                 alt="Pet care services">


            <div class="about-protection-card">

                <div class="protection-icon">

                    <i class="fa-solid fa-shield-heart"></i>

                </div>

                <div>

                    <strong>
                        Protected With Love
                    </strong>

                    <small>
                        FurShield Care
                    </small>

                </div>

            </div>

        </div>

    </div>

</section>



<!-- =====================================================
     COMMUNITY
===================================================== -->

<section class="community-section">

    <div class="community-container">


        <div class="community-image about-reveal-left">

            <img src="{{ asset('images/adoption-2.jpg') }}"
                 alt="Pet adoption community">

            <div class="community-image-overlay"></div>

            <div class="community-image-text">

                <i class="fa-solid fa-heart"></i>

                <strong>
                    Together We Care
                </strong>

            </div>

        </div>


        <div class="community-content about-reveal-right">

            <span class="about-section-label">
                OUR COMMUNITY
            </span>

            <h2>

                Connecting People

                <b>
                    Who Love Animals
                </b>

            </h2>

            <p>

                FurShield is more than technology.
                It is a growing community of pet owners,
                veterinarians, shelters and animal lovers
                working toward one shared goal.

            </p>


            <div class="community-stats">


                <div>

                    <strong>
                        10K+
                    </strong>

                    <span>
                        Pet Owners
                    </span>

                </div>


                <div>

                    <strong>
                        500+
                    </strong>

                    <span>
                        Vets
                    </span>

                </div>


                <div>

                    <strong>
                        120+
                    </strong>

                    <span>
                        Shelters
                    </span>

                </div>

            </div>


            <a href="{{ route('vets') }}"
               class="about-primary-btn">

                Explore FurShield

                <i class="fa-solid fa-arrow-right"></i>

            </a>

        </div>

    </div>

</section>



<!-- =====================================================
     VALUES
===================================================== -->

<section class="values-section">

    <div class="about-section-heading about-reveal-up">

        <span>
            OUR VALUES
        </span>

        <h2>

            What We

            <b>Believe In</b>

        </h2>

        <p>

            Our values guide every feature,
            service and experience we create.

        </p>

    </div>


    <div class="values-grid">


        <div class="value-card about-reveal-up">

            <div class="value-icon">

                <i class="fa-solid fa-heart"></i>

            </div>

            <h3>
                Compassion
            </h3>

            <p>
                Every decision starts with
                genuine care for animals.
            </p>

        </div>


        <div class="value-card about-reveal-up">

            <div class="value-icon">

                <i class="fa-solid fa-shield"></i>

            </div>

            <h3>
                Trust
            </h3>

            <p>
                We aim to provide a safe,
                reliable and transparent experience.
            </p>

        </div>


        <div class="value-card about-reveal-up">

            <div class="value-icon">

                <i class="fa-solid fa-lightbulb"></i>

            </div>

            <h3>
                Innovation
            </h3>

            <p>
                We use technology to make
                pet care easier and smarter.
            </p>

        </div>


        <div class="value-card about-reveal-up">

            <div class="value-icon">

                <i class="fa-solid fa-people-group"></i>

            </div>

            <h3>
                Community
            </h3>

            <p>
                Better pet care happens when
                people work together.
            </p>

        </div>

    </div>

</section>



<!-- =====================================================
     CTA
===================================================== -->

<section class="about-cta-section">

    <div class="about-cta-inner about-reveal-up">

        <div class="about-cta-paw">

            <i class="fa-solid fa-paw"></i>

        </div>


        <div class="about-cta-content">

            <span>
                <i class="fa-solid fa-shield-heart"></i>
                JOIN THE FURSHIELD COMMUNITY
            </span>

            <h2>
                Better Care Starts
                <b>With You</b>
            </h2>

            <p>

                Join FurShield and become part of a
                community working toward healthier,
                happier lives for pets.

            </p>

        </div>


        <a href="{{ route('register') }}"
           class="about-cta-button">

            Get Started

            <i class="fa-solid fa-arrow-right"></i>

        </a>

    </div>

</section>



<!-- =====================================================
     FOOTER
===================================================== -->

<footer class="about-footer">

    <div class="about-footer-container">


        <div class="about-footer-brand">

            <a href="{{ route('home') }}"
               class="about-footer-logo">

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


            <div class="about-social-links">

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


        <div class="about-footer-column">

            <h4>
                Platform
            </h4>

            <a href="{{ route('home') }}">
                Home
            </a>

            <a href="{{ route('about') }}">
                About
            </a>

            <a href="{{ route('vets') }}">
                Vets
            </a>

            <a href="{{ route('products') }}">
                Products
            </a>

        </div>


        <div class="about-footer-column">

            <h4>
                Support
            </h4>

            <a href="#">
                Help Center
            </a>

            <a href="{{ route('contact') }}">
                Contact Us
            </a>

            <a href="#">
                Privacy
            </a>

            <a href="#">
                Terms
            </a>

        </div>


        <div class="about-footer-column">

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


    <div class="about-footer-bottom">

        <span>

            © {{ date('Y') }} FurShield.
            All Rights Reserved.

        </span>

        <div>

            <a href="#">
                Privacy Policy
            </a>

            <a href="#">
                Terms & Conditions
            </a>

        </div>

    </div>

</footer>



<!-- =====================================================
     BACK TO TOP
===================================================== -->

<button id="aboutBackTop"
        class="about-back-top">

    <i class="fa-solid fa-arrow-up"></i>

</button>


<script src="{{ asset('js/about.js') }}"></script>

</body>
</html>