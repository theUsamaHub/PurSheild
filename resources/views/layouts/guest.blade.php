<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">

<head>

    <meta charset="UTF-8">

    <meta name="viewport"
          content="width=device-width, initial-scale=1.0">

    <meta name="csrf-token" content="{{ csrf_token() }}">

    <title>{{ $title ?? config('app.name', 'FurShield') }}</title>

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
          href="{{ asset('css/auth.css') }}">

</head>

<body>

<canvas id="authParticles"></canvas>

<a href="{{ url('/') }}"
   class="auth-back">

    <i class="fa-solid fa-arrow-left"></i>

    Home

</a>


<div class="auth-page {{ $authPageClass ?? '' }}">

    <section class="auth-showcase {{ $authShowcaseClass ?? '' }}">

        <div class="auth-orb orb-one"></div>
        <div class="auth-orb orb-two"></div>

        <a href="{{ url('/') }}"
           class="auth-brand">

            <div>
                <i class="fa-solid fa-shield-dog"></i>
            </div>

            <span>
                <strong>FurShield</strong>
                <small>Protect • Care • Love</small>
            </span>

        </a>


        <div class="auth-showcase-content">
            {{ $showcase ?? '' }}
        </div>


        <div class="auth-pet-image">

            <img src="{{ $showcaseImage ?? asset('images/hero-pets.jpg') }}"
                 alt="FurShield pets">

        </div>

    </section>


    <section class="auth-form-side">

        <div class="auth-form-wrapper {{ $formWrapperClass ?? '' }}">

            <div class="mobile-auth-logo">

                <i class="fa-solid fa-shield-dog"></i>

                <strong>
                    FurShield
                </strong>

            </div>

            {{ $slot }}

        </div>

    </section>

</div>


<div class="auth-toast"
     id="authToast">

    <i class="fa-solid fa-circle-check"></i>

    <span id="authToastMsg">
        Welcome to FurShield.
    </span>

</div>


<script src="{{ asset('js/auth.js') }}"></script>

@stack('scripts')

</body>
</html>
