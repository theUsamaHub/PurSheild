<!DOCTYPE html>
<html lang="en">

<head>

    <meta charset="UTF-8">

    <meta name="viewport"
          content="width=device-width, initial-scale=1.0">

    <title>Login | FurShield</title>

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


<div class="auth-page">

    <section class="auth-showcase">

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

            <div class="auth-badge">
                WELCOME BACK
            </div>

            <h1>
                Your Pet's World,
                <span>One Login Away.</span>
            </h1>

            <p>
                Access pet profiles, health records, appointments, shelter
                services and care resources from your FurShield account.
            </p>


            <div class="auth-benefits">

                <div>
                    <i class="fa-solid fa-paw"></i>
                    Multiple pet profiles
                </div>

                <div>
                    <i class="fa-solid fa-heart-pulse"></i>
                    Health information
                </div>

                <div>
                    <i class="fa-solid fa-calendar-check"></i>
                    Vet appointments
                </div>

            </div>

        </div>


        <div class="auth-pet-image">

            <img src="{{ asset('images/hero-pets.jpg') }}"
                 alt="FurShield pets">

        </div>

    </section>


    <section class="auth-form-side">

        <div class="auth-form-wrapper">

            <div class="mobile-auth-logo">

                <i class="fa-solid fa-shield-dog"></i>

                <strong>
                    FurShield
                </strong>

            </div>


            <div class="auth-title">

                <span>
                    ACCOUNT LOGIN
                </span>

                <h2>
                    Welcome Back
                </h2>

                <p>
                    Enter your account details to continue.
                </p>

            </div>


            <form method="POST" action="{{ route('login') }}">
                @csrf

                <div class="auth-form-group">
                    <label for="email">Email Address</label>
                    <div class="auth-input">
                        <i class="fa-regular fa-envelope"></i>
                        <input type="email"
                               id="email"
                               name="email"
                               value="{{ old('email') }}"
                               required
                               autofocus
                               placeholder="name@example.com">
                    </div>
                    @error('email')
                        <span style="color: red; font-size: 12px; margin-top: 5px; display: block;">{{ $message }}</span>
                    @enderror
                </div>

                <div class="auth-form-group">
                    <div class="label-row">
                        <label for="password">Password</label>
                        @if (Route::has('password.request'))
                            <a href="{{ route('password.request') }}">Forgot password?</a>
                        @endif
                    </div>
                    <div class="auth-input">
                        <i class="fa-solid fa-lock"></i>
                        <input type="password"
                               id="password"
                               name="password"
                               required
                               autocomplete="current-password"
                               placeholder="Enter your password">
                        <button type="button" class="password-toggle" data-target="password">
                            <i class="fa-regular fa-eye"></i>
                        </button>
                    </div>
                    @error('password')
                        <span style="color: red; font-size: 12px; margin-top: 5px; display: block;">{{ $message }}</span>
                    @enderror
                </div>

                <label class="remember-check">
                    <input type="checkbox" name="remember" id="remember" {{ old('remember') ? 'checked' : '' }}>
                    <span>Remember me</span>
                </label>

                <button type="submit" class="auth-submit">
                    <span>Login To FurShield</span>
                    <i class="fa-solid fa-arrow-right"></i>
                </button>


                <div class="auth-divider">

                    <span>or continue with</span>

                </div>


                <button type="button"
                        class="google-auth-btn">

                    <i class="fa-brands fa-google"></i>

                    Continue With Google

                </button>


                <p class="auth-switch-text">

                    Don't have an account?

                    <a href="{{ route('register') }}">
                        Create Account
                    </a>

                </p>

            </form>

        </div>

    </section>

</div>


<div class="auth-toast"
     id="authToast">

    <i class="fa-solid fa-circle-check"></i>

    <span>
        Login form ready for backend integration.
    </span>

</div>


<script src="{{ asset('js/auth.js') }}"></script>

</body>
</html>