<!DOCTYPE html>
<html lang="en">

<head>

    <meta charset="UTF-8">

    <meta name="viewport"
          content="width=device-width, initial-scale=1.0">

    <title>Animal Shelters | FurShield</title>

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

</head>

<body>

<canvas id="innerParticles"></canvas>

@include('partials.furshield-navbar', ['activePage' => 'shelters'])


<section class="inner-hero shelter-hero">

    <div class="inner-container inner-hero-grid">

        <div class="inner-hero-content reveal-left">

            <div class="inner-pill light-pill">

                <span></span>

                SHELTER COMMUNITY

            </div>

            <h1>

                Give Every Pet

                <span>
                    A Chance At Home
                </span>

            </h1>

            <p>
                Connect with animal shelters, discover pets waiting for adoption
                and support organizations caring for animals every day.
            </p>

            <div class="hero-buttons">

                <a href="#adoptionPets"
                   class="primary-hero-btn">

                    Meet Adoptable Pets

                    <i class="fa-solid fa-arrow-right"></i>

                </a>

                <a href="{{ route('register') }}"
                   class="glass-hero-btn">

                    <i class="fa-solid fa-house"></i>

                    Register Shelter

                </a>

            </div>

        </div>


        <div class="hero-visual reveal-right vip-tilt">

            <div class="hero-image-circle">

                <img src="{{ asset('images/adoption-2.jpg') }}"
                     alt="Animal shelter">

            </div>

            <div class="floating-info-card float-one">

                <i class="fa-solid fa-house-chimney"></i>

                <div>
                    <strong>120+</strong>
                    <span>Shelters</span>
                </div>

            </div>

            <div class="floating-info-card float-two">

                <i class="fa-solid fa-heart"></i>

                <div>
                    <strong>Find A Friend</strong>
                    <span>Adoption Support</span>
                </div>

            </div>

        </div>

    </div>

</section>


<section class="content-section"
         id="adoptionPets">

    <div class="inner-container">

        <div class="section-heading reveal-up">

            <div class="inner-pill center-pill">

                <span></span>

                WAITING FOR LOVE

            </div>

            <h2>
                Pets Looking For
                <span>Loving Homes</span>
            </h2>

            <p>
                Example adoption listings for your FurShield shelter system.
            </p>

        </div>


        <div class="feature-grid three-grid">

            <article class="adoption-card vip-tilt reveal-up">

                <div class="adoption-image">

                    <img src="{{ asset('images/adoption.jpg') }}"
                         alt="Adoptable pet">

                    <span>
                        Available
                    </span>

                </div>

                <div class="adoption-body">

                    <div class="adoption-heading">

                        <div>
                            <small>Dog • Young</small>
                            <h3>Buddy</h3>
                        </div>

                        <i class="fa-regular fa-heart"></i>

                    </div>

                    <p>
                        Friendly, playful and ready to become part of a caring family.
                    </p>

                    <div class="pet-tags">
                        <span>Friendly</span>
                        <span>Playful</span>
                        <span>Vaccinated</span>
                    </div>

                    <button class="profile-action">
                        View Adoption Details
                    </button>

                </div>

            </article>


            <article class="adoption-card vip-tilt reveal-up">

                <div class="adoption-image">

                    <img src="{{ asset('images/adoption-2.jpg') }}"
                         alt="Adoptable pet">

                    <span>
                        Available
                    </span>

                </div>

                <div class="adoption-body">

                    <div class="adoption-heading">

                        <div>
                            <small>Cat • Adult</small>
                            <h3>Luna</h3>
                        </div>

                        <i class="fa-regular fa-heart"></i>

                    </div>

                    <p>
                        Calm and affectionate companion searching for a peaceful home.
                    </p>

                    <div class="pet-tags">
                        <span>Calm</span>
                        <span>Indoor</span>
                        <span>Healthy</span>
                    </div>

                    <button class="profile-action">
                        View Adoption Details
                    </button>

                </div>

            </article>


            <article class="adoption-card vip-tilt reveal-up">

                <div class="adoption-image">

                    <img src="{{ asset('images/hero-pets.jpg') }}"
                         alt="Adoptable pet">

                    <span>
                        Available
                    </span>

                </div>

                <div class="adoption-body">

                    <div class="adoption-heading">

                        <div>
                            <small>Dog • Adult</small>
                            <h3>Max</h3>
                        </div>

                        <i class="fa-regular fa-heart"></i>

                    </div>

                    <p>
                        Gentle companion with a loving personality and calm nature.
                    </p>

                    <div class="pet-tags">
                        <span>Gentle</span>
                        <span>Social</span>
                        <span>Cared For</span>
                    </div>

                    <button class="profile-action">
                        View Adoption Details
                    </button>

                </div>

            </article>

        </div>

    </div>

</section>


<section class="split-section soft-background">

    <div class="inner-container split-grid">

        <div class="split-content reveal-left">

            <div class="inner-pill">

                <span></span>

                FOR SHELTERS

            </div>

            <h2>
                Manage Shelter Care
                <span>More Easily</span>
            </h2>

            <p>
                FurShield can help shelters organize animal listings, care information
                and communication with potential adopters.
            </p>

            <div class="check-list">

                <div>
                    <i class="fa-solid fa-check"></i>
                    Create and manage pet listings
                </div>

                <div>
                    <i class="fa-solid fa-check"></i>
                    Maintain care and treatment logs
                </div>

                <div>
                    <i class="fa-solid fa-check"></i>
                    Coordinate with interested adopters
                </div>

                <div>
                    <i class="fa-solid fa-check"></i>
                    Keep adoption information organized
                </div>

            </div>

            <a href="{{ route('register') }}"
               class="green-action-btn">

                Register Your Shelter

                <i class="fa-solid fa-arrow-right"></i>

            </a>

        </div>


        <div class="split-image reveal-right vip-tilt">

            <img src="{{ asset('images/adoption-2.jpg') }}"
                 alt="Shelter support">

            <div class="image-floating-badge">

                <i class="fa-solid fa-heart"></i>

                <span>
                    <strong>Compassion</strong>
                    Creates New Beginnings
                </span>

            </div>

        </div>

    </div>

</section>


<section class="process-section">

    <div class="inner-container">

        <div class="section-heading reveal-up">

            <div class="inner-pill center-pill">

                <span></span>

                ADOPTION JOURNEY

            </div>

            <h2>
                From Shelter To
                <span>Forever Home</span>
            </h2>

        </div>

        <div class="process-grid">

            <div class="process-box reveal-up">

                <span>01</span>

                <div>
                    <i class="fa-solid fa-paw"></i>
                </div>

                <h3>Explore Pets</h3>

                <p>
                    Browse pets currently listed by participating shelters.
                </p>

            </div>


            <div class="process-box reveal-up">

                <span>02</span>

                <div>
                    <i class="fa-solid fa-comments"></i>
                </div>

                <h3>Connect</h3>

                <p>
                    Contact the shelter to discuss the pet and adoption process.
                </p>

            </div>


            <div class="process-box reveal-up">

                <span>03</span>

                <div>
                    <i class="fa-solid fa-house-heart"></i>
                </div>

                <h3>Welcome Home</h3>

                <p>
                    Complete shelter coordination and begin a new chapter together.
                </p>

            </div>

        </div>

    </div>

</section>


@include('partials.inner-cta-footer', [
    'title' => 'Help More Pets Find Loving Homes',
    'text' => 'Join FurShield as a pet owner or shelter and become part of a compassionate pet-care community.'
])


<script src="{{ asset('js/inner-pages.js') }}"></script>

</body>
</html>