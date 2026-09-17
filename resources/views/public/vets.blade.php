<!DOCTYPE html>
<html lang="en">

<head>

    <meta charset="UTF-8">

    <meta name="viewport"
          content="width=device-width, initial-scale=1.0">

    <title>Veterinarians | FurShield</title>

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

@include('partials.furshield-navbar', ['activePage' => 'vets'])


<section class="inner-hero vet-hero">

    <div class="inner-container inner-hero-grid">

        <div class="inner-hero-content reveal-left">

            <div class="inner-pill light-pill">

                <span></span>

                VETERINARY NETWORK

            </div>

            <h1>

                Find Care From

                <span>
                    Trusted Veterinarians
                </span>

            </h1>

            <p>
                Discover veterinary professionals, explore their profiles and
                conveniently request appointments for your pets.
            </p>

            <div class="hero-buttons">

                <a href="#vetDirectory"
                   class="primary-hero-btn">

                    Find A Vet

                    <i class="fa-solid fa-arrow-right"></i>

                </a>

                <a href="{{ route('register') }}"
                   class="glass-hero-btn">

                    <i class="fa-solid fa-user-doctor"></i>

                    Join As Vet

                </a>

            </div>

        </div>


        <div class="hero-visual reveal-right vip-tilt">

            <div class="hero-image-circle">

                <img src="{{ asset('images/vet.jpg') }}"
                     alt="Veterinarian">

            </div>

            <div class="floating-info-card float-one">

                <i class="fa-solid fa-user-doctor"></i>

                <div>
                    <strong>500+</strong>
                    <span>Veterinarians</span>
                </div>

            </div>

            <div class="floating-info-card float-two">

                <i class="fa-solid fa-calendar-check"></i>

                <div>
                    <strong>Easy Booking</strong>
                    <span>Manage Appointments</span>
                </div>

            </div>

        </div>

    </div>

</section>


<section class="directory-section"
         id="vetDirectory">

    <div class="inner-container">

        <div class="section-heading reveal-up">

            <div class="inner-pill center-pill">

                <span></span>

                VET DIRECTORY

            </div>

            <h2>
                Meet Pet-Care
                <span>Professionals</span>
            </h2>

            <p>
                Search profiles by name or specialty.
            </p>

        </div>


        <div class="directory-toolbar reveal-up">

            <div class="directory-search">

                <i class="fa-solid fa-magnifying-glass"></i>

                <input type="text"
                       id="directorySearch"
                       placeholder="Search veterinarian...">

            </div>

            <div class="directory-filters">

                <button class="directory-filter active"
                        data-filter="all">
                    All
                </button>

                <button class="directory-filter"
                        data-filter="general">
                    General Care
                </button>

                <button class="directory-filter"
                        data-filter="surgery">
                    Surgery
                </button>

                <button class="directory-filter"
                        data-filter="dental">
                    Dental
                </button>

            </div>

        </div>


                <div class="directory-grid">
            @forelse($vets as $vet)
            <article class="profile-card vip-tilt reveal-up"
                     data-category="general"
                     data-name="{{ strtolower($vet->name) }}">
                <div class="profile-photo">
                    <img src="{{ $vet->vetProfile?->profile_image ?? asset('images/vet.jpg') }}" alt="Veterinarian">
                    <span class="availability-dot"></span>
                </div>
                <div class="profile-body">
                    <div class="profile-top">
                        <div>
                            <span>Veterinarian</span>
                            <h3>Dr. {{ $vet->name }}</h3>
                        </div>
                        <div class="profile-rating">
                            <i class="fa-solid fa-star"></i> 4.9
                        </div>
                    </div>
                    <p>{{ Str::limit($vet->vetProfile?->bio ?? 'General veterinary consultations, preventive care and routine pet wellness.', 100) }}</p>
                    <div class="profile-meta">
                        <span><i class="fa-solid fa-briefcase-medical"></i> {{ $vet->vetProfile?->experience_years ?? 0 }} Years</span>
                        <span><i class="fa-solid fa-location-dot"></i> {{ $vet->vetProfile?->city ?? 'Unknown' }}</span>
                    </div>
                </div>
                <div class="profile-actions">
                    <button class="btn-outline">View Profile</button>
                    <button class="btn-primary">Book Visit</button>
                </div>
            </article>
            @empty
            <p>No veterinarians found.</p>
            @endforelse
        </div>
        
        <div style="margin-top:20px;">
            {{ $vets->links() }}
        </div>

    </section>


<section class="process-section">

    <div class="inner-container">

        <div class="section-heading reveal-up">

            <div class="inner-pill center-pill">
                <span></span>
                EASY BOOKING
            </div>

            <h2>
                Book Care In
                <span>Three Simple Steps</span>
            </h2>

        </div>


        <div class="process-grid">

            <div class="process-box reveal-up">

                <span>01</span>

                <div>
                    <i class="fa-solid fa-magnifying-glass"></i>
                </div>

                <h3>Find A Vet</h3>

                <p>
                    Browse professionals and review their profile information.
                </p>

            </div>


            <div class="process-box reveal-up">

                <span>02</span>

                <div>
                    <i class="fa-solid fa-calendar-days"></i>
                </div>

                <h3>Request A Time</h3>

                <p>
                    Select a suitable appointment request for your pet.
                </p>

            </div>


            <div class="process-box reveal-up">

                <span>03</span>

                <div>
                    <i class="fa-solid fa-heart-pulse"></i>
                </div>

                <h3>Manage Care</h3>

                <p>
                    Keep appointment and treatment information organized.
                </p>

            </div>

        </div>

    </div>

</section>


@include('partials.inner-cta-footer', [
    'title' => 'Your Pet Deserves Professional Care',
    'text' => 'Explore FurShield veterinarians and organize appointments from one convenient platform.'
])


<script src="{{ asset('js/inner-pages.js') }}"></script>

</body>
</html>