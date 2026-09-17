<section class="inner-cta">

    <div class="inner-container">

        <div class="inner-cta-content reveal-up">

            <div class="cta-icon">
                <i class="fa-solid fa-paw"></i>
            </div>

            <h2>
                {{ $title }}
            </h2>

            <p>
                {{ $text }}
            </p>

            <div class="cta-buttons">

                <a href="{{ route('register') }}"
                   class="cta-primary">
                    Join FurShield
                    <i class="fa-solid fa-arrow-right"></i>
                </a>

                <a href="{{ route('contact') }}"
                   class="cta-secondary">
                    Contact Us
                </a>

            </div>

        </div>

    </div>

</section>


<footer class="inner-footer">

    <div class="inner-container">

        <div class="inner-footer-grid">

            <div class="footer-brand-column">

                <a href="{{ url('/') }}"
                   class="footer-brand">

                    <div>
                        <i class="fa-solid fa-shield-dog"></i>
                    </div>

                    <span>
                        <strong>FurShield</strong>
                        <small>Protect • Care • Love</small>
                    </span>

                </a>

                <p>
                    Connecting pet owners, veterinarians, shelters and pet-care
                    resources in one trusted platform.
                </p>

                <div class="footer-socials">

                    <a href="#">
                        <i class="fa-brands fa-facebook-f"></i>
                    </a>

                    <a href="#">
                        <i class="fa-brands fa-instagram"></i>
                    </a>

                    <a href="#">
                        <i class="fa-brands fa-x-twitter"></i>
                    </a>

                    <a href="#">
                        <i class="fa-brands fa-youtube"></i>
                    </a>

                </div>

            </div>


            <div class="footer-column">

                <h4>Platform</h4>

                <a href="{{ url('/') }}">Home</a>
                <a href="{{ route('about') }}">About</a>
                <a href="{{ route('vets') }}">Vets</a>
                <a href="{{ route('products') }}">Products</a>

            </div>


            <div class="footer-column">

                <h4>Pet Care</h4>

                <a href="{{ route('care') }}">Care Guides</a>
                <a href="{{ route('vets') }}">Veterinarians</a>
                <a href="{{ route('shelters') }}">Shelters</a>
                <a href="{{ route('contact') }}">Contact</a>

            </div>


            <div class="footer-column">

                <h4>Account</h4>

                <a href="{{ route('login') }}">Login</a>
                <a href="{{ route('register') }}">Register</a>
                <a href="#">Help Center</a>
                <a href="#">Privacy Policy</a>

            </div>


            <div class="footer-column footer-contact">

                <h4>Get In Touch</h4>

                <span>
                    <i class="fa-solid fa-envelope"></i>
                    support@furshield.com
                </span>

                <span>
                    <i class="fa-solid fa-phone"></i>
                    +92 300 1234567
                </span>

                <span>
                    <i class="fa-solid fa-location-dot"></i>
                    Pakistan
                </span>

            </div>

        </div>


        <div class="footer-bottom">

            <span>
                © {{ date('Y') }} FurShield. All rights reserved.
            </span>

            <span>
                Made with
                <i class="fa-solid fa-heart"></i>
                for pets.
            </span>

        </div>

    </div>

</footer>


<button id="innerBackTop"
        class="inner-back-top">

    <i class="fa-solid fa-arrow-up"></i>

</button>