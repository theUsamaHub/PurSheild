<!DOCTYPE html>
<html lang="en">

<head>

    <meta charset="UTF-8">

    <meta name="viewport"
          content="width=device-width, initial-scale=1.0">

    <title>Contact FurShield</title>

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

@include('partials.furshield-navbar', ['activePage' => 'contact'])


<section class="contact-hero">

    <div class="inner-container">

        <div class="contact-hero-content reveal-up">

            <div class="inner-pill light-pill center-pill">

                <span></span>

                CONTACT FURSHIELD

            </div>

            <h1>
                We're Here To
                <span>Help You & Your Pet</span>
            </h1>

            <p>
                Questions about FurShield, pet services, veterinary appointments
                or shelter support? Send us a message.
            </p>

        </div>

    </div>

</section>


<section class="contact-info-section">

    <div class="inner-container">

        <div class="contact-info-grid">

            <div class="contact-info-card vip-tilt reveal-up">

                <div>
                    <i class="fa-solid fa-envelope"></i>
                </div>

                <span>Email Support</span>

                <h3>
                    support@furshield.com
                </h3>

                <p>
                    Send us your questions anytime.
                </p>

            </div>


            <div class="contact-info-card vip-tilt reveal-up">

                <div>
                    <i class="fa-solid fa-phone"></i>
                </div>

                <span>Call Us</span>

                <h3>
                    +92 300 1234567
                </h3>

                <p>
                    Speak with FurShield support.
                </p>

            </div>


            <div class="contact-info-card vip-tilt reveal-up">

                <div>
                    <i class="fa-solid fa-location-dot"></i>
                </div>

                <span>Location</span>

                <h3>
                    Pakistan
                </h3>

                <p>
                    Supporting the pet-care community.
                </p>

            </div>

        </div>

    </div>

</section>


<section class="contact-form-section">

    <div class="inner-container contact-layout">


        <div class="contact-form-visual reveal-left">

            <div class="contact-image vip-tilt">

                <img src="{{ asset('images/hero-pets.jpg') }}"
                     alt="Contact FurShield">

            </div>

            <div class="contact-floating-note">

                <i class="fa-solid fa-headset"></i>

                <span>
                    <strong>We're Listening</strong>
                    FurShield Support Team
                </span>

            </div>

        </div>


        <div class="contact-form-card reveal-right">

            <div class="inner-pill">

                <span></span>

                SEND A MESSAGE

            </div>

            <h2>
                How Can We
                <span>Help You?</span>
            </h2>

            @if(session('success'))
                <div style="background: #d4edda; color: #155724; padding: 12px 16px; border-radius: 8px; margin-bottom: 20px; font-weight: 500;">
                    <i class="fa-solid fa-circle-check me-2"></i> {{ session('success') }}
                </div>
            @endif

            <form method="POST" action="{{ route('contact.store') }}" id="contactForm">
                @csrf

                <div class="form-row">
                    <div class="form-group">
                        <label for="name">Your Name</label>
                        <div class="input-wrap">
                            <i class="fa-regular fa-user"></i>
                            <input type="text" id="name" name="name" required value="{{ old('name') }}" placeholder="Enter your name">
                        </div>
                        @error('name')<span style="color: red; font-size: 12px;">{{ $message }}</span>@enderror
                    </div>

                    <div class="form-group">
                        <label for="email">Email Address</label>
                        <div class="input-wrap">
                            <i class="fa-regular fa-envelope"></i>
                            <input type="email" id="email" name="email" required value="{{ old('email') }}" placeholder="Enter your email">
                        </div>
                        @error('email')<span style="color: red; font-size: 12px;">{{ $message }}</span>@enderror
                    </div>
                </div>

                <div class="form-group">
                    <label for="subject">Topic / Subject</label>
                    <div class="input-wrap">
                        <i class="fa-solid fa-layer-group"></i>
                        <input type="text" id="subject" name="subject" value="{{ old('subject') }}" placeholder="Subject / Topic">
                    </div>
                    @error('subject')<span style="color: red; font-size: 12px;">{{ $message }}</span>@enderror
                </div>

                <div class="form-group">
                    <label for="message">Message</label>
                    <div class="input-wrap textarea-wrap">
                        <i class="fa-regular fa-message"></i>
                        <textarea id="message" name="message" rows="6" required placeholder="Write your message...">{{ old('message') }}</textarea>
                    </div>
                    @error('message')<span style="color: red; font-size: 12px;">{{ $message }}</span>@enderror
                </div>

                <button type="submit" class="contact-submit">
                    Send Message
                    <i class="fa-solid fa-paper-plane"></i>
                </button>
            </form>

        </div>

    </div>

</section>


<div class="page-toast"
     id="pageToast">

    <i class="fa-solid fa-circle-check"></i>

    <span>
        Message submitted successfully.
    </span>

</div>


@include('partials.inner-cta-footer', [
    'title' => 'Join The FurShield Pet-Care Community',
    'text' => 'Create an account and keep your pet-care journey connected in one place.'
])


<script src="{{ asset('js/inner-pages.js') }}"></script>

</body>
</html>