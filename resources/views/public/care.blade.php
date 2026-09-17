<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">

    <title>Pet Care | FurShield</title>

    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>

    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@400;500;600;700;800&display=swap"
          rel="stylesheet">

    <link rel="stylesheet"
          href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.2/css/all.min.css">

    <link rel="stylesheet"
          href="{{ asset('css/inner-pages.css') }}">
</head>

<body>

<canvas id="innerParticles"></canvas>

@include('partials.furshield-navbar', ['activePage' => 'care'])


{{-- HERO --}}
<section class="inner-hero care-hero">

    <div class="hero-glow glow-a"></div>
    <div class="hero-glow glow-b"></div>

    <div class="inner-container inner-hero-grid">

        <div class="inner-hero-content reveal-left">

            <div class="inner-pill light-pill">

                <span></span>

                SMART PET CARE

            </div>

            <h1>

                Helping You Give

                <span>
                    Better Care Every Day
                </span>

            </h1>

            <p>
                Explore pet health guidance, grooming tips, nutrition advice,
                educational videos and practical resources designed to support
                happier and healthier pets.
            </p>

            <div class="hero-buttons">

                <a href="#careGuides"
                   class="primary-hero-btn">

                    Explore Care Guides

                    <i class="fa-solid fa-arrow-right"></i>

                </a>

                <a href="#careVideo"
                   class="glass-hero-btn">

                    <i class="fa-solid fa-circle-play"></i>

                    Watch Guide

                </a>

            </div>

            <div class="hero-mini-points">

                <span>
                    <i class="fa-solid fa-check"></i>
                    Nutrition
                </span>

                <span>
                    <i class="fa-solid fa-check"></i>
                    Wellness
                </span>

                <span>
                    <i class="fa-solid fa-check"></i>
                    Grooming
                </span>

            </div>

        </div>


        <div class="hero-visual reveal-right vip-tilt">

            <div class="hero-image-circle">

                <img src="{{ asset('images/pets-pic.jpg') }}"
                     alt="Pet care">

            </div>

            <div class="floating-info-card float-one">

                <i class="fa-solid fa-heart-pulse"></i>

                <div>
                    <strong>Healthy Pets</strong>
                    <span>Daily Care Matters</span>
                </div>

            </div>

            <div class="floating-info-card float-two">

                <i class="fa-solid fa-bowl-food"></i>

                <div>
                    <strong>Smart Nutrition</strong>
                    <span>Balanced Routine</span>
                </div>

            </div>

        </div>

    </div>

</section>


{{-- CARE CATEGORIES --}}
<section class="content-section" id="careGuides">

    <div class="inner-container">

        <div class="section-heading reveal-up">

            <div class="inner-pill center-pill">

                <span></span>

                CARE ESSENTIALS

            </div>

            <h2>
                Complete Care For
                <span>Every Stage Of Life</span>
            </h2>

            <p>
                Practical resources to help you understand your pet's everyday needs.
            </p>

        </div>


        <div class="feature-grid four-grid">

            <article class="feature-card vip-tilt reveal-up">

                <div class="feature-image">

                    <img src="{{ asset('images/dog-food.jpg') }}"
                         alt="Pet nutrition">

                </div>

                <div class="feature-card-body">

                    <div class="feature-icon">
                        <i class="fa-solid fa-bowl-food"></i>
                    </div>

                    <h3>Nutrition & Diet</h3>

                    <p>
                        Understand balanced feeding, portion routines and healthier
                        nutrition choices.
                    </p>

                    <a href="#">
                        Explore Guide
                        <i class="fa-solid fa-arrow-right"></i>
                    </a>

                </div>

            </article>


            <article class="feature-card vip-tilt reveal-up">

                <div class="feature-image">

                    <img src="{{ asset('images/health.jpg') }}"
                         alt="Pet health">

                </div>

                <div class="feature-card-body">

                    <div class="feature-icon">
                        <i class="fa-solid fa-heart-pulse"></i>
                    </div>

                    <h3>Health & Wellness</h3>

                    <p>
                        Learn about preventive care, health records and everyday
                        wellbeing.
                    </p>

                    <a href="#">
                        Explore Guide
                        <i class="fa-solid fa-arrow-right"></i>
                    </a>

                </div>

            </article>


            <article class="feature-card vip-tilt reveal-up">

                <div class="feature-image">

                    <img src="{{ asset('images/shampoo.jpg') }}"
                         alt="Pet grooming">

                </div>

                <div class="feature-card-body">

                    <div class="feature-icon">
                        <i class="fa-solid fa-scissors"></i>
                    </div>

                    <h3>Grooming</h3>

                    <p>
                        Keep coats, paws and hygiene routines comfortable and consistent.
                    </p>

                    <a href="#">
                        Explore Guide
                        <i class="fa-solid fa-arrow-right"></i>
                    </a>

                </div>

            </article>


            <article class="feature-card vip-tilt reveal-up">

                <div class="feature-image">

                    <img src="{{ asset('images/chew-toy.jpg') }}"
                         alt="Pet play">

                </div>

                <div class="feature-card-body">

                    <div class="feature-icon">
                        <i class="fa-solid fa-bone"></i>
                    </div>

                    <h3>Play & Enrichment</h3>

                    <p>
                        Help your pet stay active, engaged and mentally stimulated.
                    </p>

                    <a href="#">
                        Explore Guide
                        <i class="fa-solid fa-arrow-right"></i>
                    </a>

                </div>

            </article>

        </div>

    </div>

</section>


{{-- VIDEO --}}
<section class="inner-video-section"
         id="careVideo">

    <video id="innerVideo"
           autoplay
           muted
           loop
           playsinline
           poster="{{ asset('images/hero-pets.jpg') }}">

        <source src="{{ asset('videos/pet-care.mp4') }}"
                type="video/mp4">

    </video>

    <div class="video-dark-overlay"></div>

    <div class="video-content reveal-up">

        <span>
            PET CARE VIDEO
        </span>

        <h2>
            Small Habits Create
            <strong>Healthier Lives</strong>
        </h2>

        <p>
            Discover simple daily routines that can support your pet's comfort,
            health and happiness.
        </p>

        <button id="innerVideoToggle"
                class="video-toggle">

            <i class="fa-solid fa-pause"></i>

            <span>
                Pause Video
            </span>

        </button>

    </div>

</section>


{{-- DAILY TIPS --}}
<section class="split-section">

    <div class="inner-container split-grid">

        <div class="split-image reveal-left vip-tilt">

            <img src="{{ asset('images/hero-pets.jpg') }}"
                 alt="Pet care routine">

            <div class="image-floating-badge">

                <i class="fa-solid fa-shield-heart"></i>

                <span>
                    <strong>Daily Care</strong>
                    Builds Better Health
                </span>

            </div>

        </div>


        <div class="split-content reveal-right">

            <div class="inner-pill">

                <span></span>

                DAILY CHECKLIST

            </div>

            <h2>
                Simple Habits For A
                <span>Happier Pet</span>
            </h2>

            <p>
                A consistent daily routine helps you notice changes early and keeps
                your pet comfortable.
            </p>

            <div class="check-list">

                <div>
                    <i class="fa-solid fa-check"></i>
                    Fresh water and balanced meals
                </div>

                <div>
                    <i class="fa-solid fa-check"></i>
                    Daily movement and play
                </div>

                <div>
                    <i class="fa-solid fa-check"></i>
                    Regular hygiene and grooming
                </div>

                <div>
                    <i class="fa-solid fa-check"></i>
                    Observe behaviour and appetite
                </div>

                <div>
                    <i class="fa-solid fa-check"></i>
                    Maintain vaccinations and health records
                </div>

            </div>

        </div>

    </div>

</section>


{{-- FAQ --}}
<section class="faq-section">

    <div class="inner-container faq-layout">

        <div class="faq-intro reveal-left">

            <div class="inner-pill">

                <span></span>

                PET CARE FAQ

            </div>

            <h2>
                Questions Pet Parents
                <span>Often Ask</span>
            </h2>

            <p>
                Quick guidance for common pet-care questions.
            </p>

        </div>


        <div class="faq-list reveal-right">

            <div class="faq-item">

                <button class="faq-question">

                    How often should my pet visit a veterinarian?

                    <i class="fa-solid fa-plus"></i>

                </button>

                <div class="faq-answer">

                    <p>
                        Routine veterinary checkups are important for preventive care.
                        Your veterinarian can recommend a schedule based on age,
                        condition and individual needs.
                    </p>

                </div>

            </div>


            <div class="faq-item">

                <button class="faq-question">

                    Why should I keep digital health records?

                    <i class="fa-solid fa-plus"></i>

                </button>

                <div class="faq-answer">

                    <p>
                        Organized records make vaccination dates, treatment history
                        and important medical details easier to review when needed.
                    </p>

                </div>

            </div>


            <div class="faq-item">

                <button class="faq-question">

                    How important is daily exercise?

                    <i class="fa-solid fa-plus"></i>

                </button>

                <div class="faq-answer">

                    <p>
                        Appropriate physical activity and enrichment can support
                        healthy routines, behaviour and quality of life.
                    </p>

                </div>

            </div>


            <div class="faq-item">

                <button class="faq-question">

                    Can FurShield replace professional veterinary care?

                    <i class="fa-solid fa-plus"></i>

                </button>

                <div class="faq-answer">

                    <p>
                        No. FurShield helps organize pet care and connect users with
                        services, but medical concerns should be discussed with a
                        qualified veterinary professional.
                    </p>

                </div>

            </div>

        </div>

    </div>

</section>


@include('partials.inner-cta-footer', [
    'title' => 'Give Your Pet Better Care Every Day',
    'text' => 'Join FurShield and keep care resources, pet profiles and health information together.'
])


<script src="{{ asset('js/inner-pages.js') }}"></script>

</body>
</html>