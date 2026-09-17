<!DOCTYPE html>
<html lang="en">

<head>

    <meta charset="UTF-8">

    <meta name="viewport"
          content="width=device-width, initial-scale=1.0">

    <title>Create Account | FurShield</title>

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


<div class="auth-page register-page">

    <section class="auth-showcase register-showcase">

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
                JOIN FURSHIELD
            </div>

            <h1>
                Better Pet Care
                <span>Starts Here.</span>
            </h1>

            <p>
                Create your role-based FurShield account and become part of a
                connected pet-care community.
            </p>


            <div class="auth-stat-row">

                <div>
                    <strong>10K+</strong>
                    <span>Happy Pets</span>
                </div>

                <div>
                    <strong>500+</strong>
                    <span>Vets</span>
                </div>

                <div>
                    <strong>120+</strong>
                    <span>Shelters</span>
                </div>

            </div>

        </div>


        <div class="auth-pet-image">

            <img src="{{ asset('images/pets-pic.jpg') }}"
                 alt="FurShield pets">

        </div>

    </section>


    <section class="auth-form-side">

        <div class="auth-form-wrapper register-wrapper">

            <div class="mobile-auth-logo">

                <i class="fa-solid fa-shield-dog"></i>

                <strong>
                    FurShield
                </strong>

            </div>


            <div class="auth-title">

                <span>
                    CREATE ACCOUNT
                </span>

                <h2>
                    Join FurShield
                </h2>

                <p>
                    Select your account type and enter your details.
                </p>

            </div>


            <div class="role-switch">

                <button type="button"
                        class="role-option active"
                        data-role="owner">

                    <i class="fa-solid fa-paw"></i>

                    Pet Owner

                </button>

                <button type="button"
                        class="role-option"
                        data-role="vet">

                    <i class="fa-solid fa-user-doctor"></i>

                    Veterinarian

                </button>

                <button type="button"
                        class="role-option"
                        data-role="shelter">

                    <i class="fa-solid fa-house"></i>

                    Shelter

                </button>

            </div>


            <form method="POST" action="{{ route('register') }}" id="registerForm">
                @csrf

                <input type="hidden" name="role" id="selectedRole" value="{{ old('role', 'owner') }}">

                <div class="register-two-column">
                    <div class="auth-form-group">
                        <label for="name">Full Name</label>
                        <div class="auth-input">
                            <i class="fa-regular fa-user"></i>
                            <input type="text" id="name" name="name" value="{{ old('name') }}" required autofocus placeholder="Your full name">
                        </div>
                        @error('name')<span style="color: red; font-size: 12px; margin-top: 5px;">{{ $message }}</span>@enderror
                    </div>

                    <div class="auth-form-group">
                        <label for="phone">Phone Number</label>
                        <div class="auth-input">
                            <i class="fa-solid fa-phone"></i>
                            <input type="tel" id="phone" name="phone" value="{{ old('phone') }}" required placeholder="+92 300 1234567">
                        </div>
                        @error('phone')<span style="color: red; font-size: 12px; margin-top: 5px;">{{ $message }}</span>@enderror
                    </div>
                </div>

                <div class="auth-form-group">
                    <label for="email">Email Address</label>
                    <div class="auth-input">
                        <i class="fa-regular fa-envelope"></i>
                        <input type="email" id="email" name="email" value="{{ old('email') }}" required placeholder="name@example.com">
                    </div>
                    @error('email')<span style="color: red; font-size: 12px; margin-top: 5px;">{{ $message }}</span>@enderror
                </div>

                <div class="register-two-column">
                    <div class="auth-form-group">
                        <label for="password">Password</label>
                        <div class="auth-input">
                            <i class="fa-solid fa-lock"></i>
                            <input type="password" id="registerPassword" name="password" required minlength="8" autocomplete="new-password" placeholder="Minimum 8 characters">
                            <button type="button" class="password-toggle" data-target="registerPassword">
                                <i class="fa-regular fa-eye"></i>
                            </button>
                        </div>
                        @error('password')<span style="color: red; font-size: 12px; margin-top: 5px;">{{ $message }}</span>@enderror
                    </div>

                    <div class="auth-form-group">
                        <label for="password_confirmation">Confirm Password</label>
                        <div class="auth-input">
                            <i class="fa-solid fa-shield"></i>
                            <input type="password" id="confirmPassword" name="password_confirmation" required minlength="8" autocomplete="new-password" placeholder="Repeat password">
                            <button type="button" class="password-toggle" data-target="confirmPassword">
                                <i class="fa-regular fa-eye"></i>
                            </button>
                        </div>
                    </div>
                </div>

                <div class="password-strength">
                    <div>
                        <span id="strengthBar"></span>
                    </div>
                    <small id="strengthText">Password strength</small>
                </div>

                <label class="remember-check terms-check">
                    <input type="checkbox" name="terms" required>
                    <span>I agree to the Terms & Conditions and Privacy Policy</span>
                </label>

                <button type="submit" class="auth-submit">
                    <span>Create FurShield Account</span>
                    <i class="fa-solid fa-arrow-right"></i>
                </button>

                <p class="auth-switch-text">
                    Already have an account?
                    <a href="{{ route('login') }}">Login</a>
                </p>

            </form>

        </div>

    </section>

</div>


<div class="auth-toast"
     id="authToast">

    <i class="fa-solid fa-circle-check"></i>

    <span>
        Registration form ready for backend integration.
    </span>

</div>


<script src="{{ asset('js/auth.js') }}"></script>

</body>
</html>