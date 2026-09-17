/* =====================================================
   FURSHIELD PREMIUM JAVASCRIPT
===================================================== */


/* =====================================================
   NAVBAR SCROLL
===================================================== */

const navbar = document.getElementById("navbar");

window.addEventListener("scroll", function () {

    if (window.scrollY > 60) {

        navbar.classList.add("scrolled");

    } else {

        navbar.classList.remove("scrolled");

    }

});


/* =====================================================
   MOBILE MENU
===================================================== */

function toggleMobileMenu() {

    const menu =
        document.getElementById("mobileNav");

    menu.classList.toggle("show");

}


/* Close mobile menu after click */

document.querySelectorAll(".mobile-nav a")
    .forEach(link => {

        link.addEventListener("click", function () {

            document
                .getElementById("mobileNav")
                .classList.remove("show");

        });

    });


/* =====================================================
   SCROLL REVEAL
===================================================== */

const revealElements =
    document.querySelectorAll(
        ".reveal-up, .reveal-left, .reveal-right"
    );


const revealObserver =
    new IntersectionObserver(

        function (entries) {

            entries.forEach(entry => {

                if (entry.isIntersecting) {

                    entry.target.classList.add(
                        "visible"
                    );

                    revealObserver.unobserve(
                        entry.target
                    );

                }

            });

        },

        {
            threshold: 0.12
        }

    );


revealElements.forEach(element => {

    revealObserver.observe(element);

});


/* =====================================================
   BACK TO TOP
===================================================== */

const backToTop =
    document.getElementById("backToTop");


window.addEventListener("scroll", function () {

    if (window.scrollY > 500) {

        backToTop.classList.add("show");

    } else {

        backToTop.classList.remove("show");

    }

});


backToTop.addEventListener("click", function () {

    window.scrollTo({

        top: 0,

        behavior: "smooth"

    });

});


/* =====================================================
   3D MOUSE EFFECT — HERO
===================================================== */

const heroVisual =
    document.querySelector(".hero-visual");


const heroPets =
    document.querySelector(".hero-pets");


if (heroVisual && heroPets) {

    heroVisual.addEventListener(
        "mousemove",
        function (event) {

            const rect =
                heroVisual.getBoundingClientRect();

            const x =
                event.clientX - rect.left;

            const y =
                event.clientY - rect.top;

            const centerX =
                rect.width / 2;

            const centerY =
                rect.height / 2;

            const rotateY =
                (x - centerX) / 35;

            const rotateX =
                (centerY - y) / 35;

            heroPets.style.transform =
                `translateY(-8px)
                 rotateX(${rotateX}deg)
                 rotateY(${rotateY}deg)
                 scale(1.02)`;

            /* Move floating cards with parallax */

            const cards =
                heroVisual.querySelectorAll(
                    ".hero-floating-card"
                );

            cards.forEach((card, i) => {

                const depth = (i + 1) * 8;

                card.style.transform =
                    `translateY(-12px)
                     translateX(${rotateY * depth / 10}px)
                     translateZ(${depth}px)`;

            });

        }
    );


    heroVisual.addEventListener(
        "mouseleave",
        function () {

            heroPets.style.transform =
                "";

            const cards =
                heroVisual.querySelectorAll(
                    ".hero-floating-card"
                );

            cards.forEach(card => {

                card.style.transform = "";

            });

        }
    );

}


/* =====================================================
   3D TILT EFFECT — SERVICE CARDS
===================================================== */

document.querySelectorAll(".service-card")
    .forEach(card => {

        card.addEventListener("mousemove", function (e) {

            const rect =
                this.getBoundingClientRect();

            const x =
                e.clientX - rect.left;

            const y =
                e.clientY - rect.top;

            const centerX = rect.width / 2;
            const centerY = rect.height / 2;

            const rotateX =
                (centerY - y) / 20;

            const rotateY =
                (x - centerX) / 20;

            this.style.transform =
                `translateY(-14px)
                 rotateX(${rotateX}deg)
                 rotateY(${rotateY}deg)
                 scale(1.02)`;

        });


        card.addEventListener("mouseleave", function () {

            this.style.transform = "";

        });

    });


/* =====================================================
   3D TILT — PRODUCT CARDS
===================================================== */

document.querySelectorAll(".product-card")
    .forEach(card => {

        card.addEventListener("mousemove", function (e) {

            const rect =
                this.getBoundingClientRect();

            const x =
                e.clientX - rect.left;

            const y =
                e.clientY - rect.top;

            const centerX = rect.width / 2;
            const centerY = rect.height / 2;

            const rotateX =
                (centerY - y) / 30;

            const rotateY =
                (x - centerX) / 30;

            this.style.transform =
                `translateY(-9px)
                 rotateX(${rotateX}deg)
                 rotateY(${rotateY}deg)
                 scale(1.02)`;

        });


        card.addEventListener("mouseleave", function () {

            this.style.transform = "";

        });

    });


/* =====================================================
   VIDEO TOGGLE
===================================================== */

const videoToggle =
    document.getElementById("videoToggle");

const videoBg =
    document.querySelector(".video-bg");


if (videoToggle && videoBg) {

    videoToggle.addEventListener("click", function () {

        const icon =
            this.querySelector("i");

        if (videoBg.paused) {

            videoBg.play();

            icon.className =
                "fa-solid fa-pause";

        } else {

            videoBg.pause();

            icon.className =
                "fa-solid fa-play";

        }

    });

}


/* =====================================================
   PRODUCT BUTTON
===================================================== */

const cartButtons =
    document.querySelectorAll(".add-cart");


cartButtons.forEach(button => {

    button.addEventListener(
        "click",
        function () {

            const oldText =
                this.innerText;

            this.innerText =
                "✓ Added";

            this.style.background =
                "#087d59";


            setTimeout(() => {

                this.innerText =
                    oldText;

                this.style.background =
                    "";

            }, 1500);

        }
    );

});


/* =====================================================
   HEART BUTTON
===================================================== */

const heartButtons =
    document.querySelectorAll(
        ".product-image > button"
    );


heartButtons.forEach(button => {

    button.addEventListener(
        "click",
        function () {

            const icon =
                this.querySelector("i");

            icon.classList.toggle(
                "fa-regular"
            );

            icon.classList.toggle(
                "fa-solid"
            );

            if (
                icon.classList.contains(
                    "fa-solid"
                )
            ) {

                this.style.color =
                    "#ff6262";

            } else {

                this.style.color =
                    "";

            }

        }
    );

});


/* =====================================================
   SMOOTH NAVIGATION
===================================================== */

document.querySelectorAll(
    'a[href^="#"]'
).forEach(anchor => {

    anchor.addEventListener(
        "click",
        function (event) {

            const target =
                document.querySelector(
                    this.getAttribute("href")
                );

            if (!target) return;

            event.preventDefault();

            target.scrollIntoView({

                behavior: "smooth",

                block: "start"

            });

        }
    );

});


/* =====================================================
   PARTICLE CANVAS — 3D BACKGROUND EFFECT
===================================================== */

(function () {

    const canvas =
        document.getElementById("particleCanvas");

    if (!canvas) return;

    const ctx = canvas.getContext("2d");


    function resizeCanvas() {

        canvas.width = window.innerWidth;
        canvas.height = window.innerHeight;

    }

    resizeCanvas();

    window.addEventListener("resize", resizeCanvas);


    const particles = [];

    const particleCount = 50;


    for (let i = 0; i < particleCount; i++) {

        particles.push({

            x: Math.random() * canvas.width,

            y: Math.random() * canvas.height,

            z: Math.random() * 3 + 1,

            radius: Math.random() * 2.5 + .5,

            speedX:
                (Math.random() - .5) * .4,

            speedY:
                (Math.random() - .5) * .4,

            opacity:
                Math.random() * .3 + .1,

            color:
                Math.random() > .5
                    ? "rgba(101,243,165,"
                    : "rgba(212,168,83,"

        });

    }


    function animateParticles() {

        ctx.clearRect(
            0, 0,
            canvas.width,
            canvas.height
        );


        particles.forEach(p => {

            p.x += p.speedX * p.z;
            p.y += p.speedY * p.z;


            /* wrap around */

            if (p.x < 0) p.x = canvas.width;
            if (p.x > canvas.width) p.x = 0;
            if (p.y < 0) p.y = canvas.height;
            if (p.y > canvas.height) p.y = 0;


            ctx.beginPath();

            ctx.arc(
                p.x, p.y,
                p.radius * p.z,
                0,
                Math.PI * 2
            );

            ctx.fillStyle =
                p.color + p.opacity + ")";

            ctx.fill();

        });


        /* Draw connecting lines */

        for (let i = 0; i < particles.length; i++) {

            for (let j = i + 1; j < particles.length; j++) {

                const dx =
                    particles[i].x - particles[j].x;

                const dy =
                    particles[i].y - particles[j].y;

                const dist =
                    Math.sqrt(dx * dx + dy * dy);


                if (dist < 150) {

                    ctx.beginPath();

                    ctx.strokeStyle =
                        "rgba(101,243,165," +
                        (.06 * (1 - dist / 150)) +
                        ")";

                    ctx.lineWidth = .5;

                    ctx.moveTo(
                        particles[i].x,
                        particles[i].y
                    );

                    ctx.lineTo(
                        particles[j].x,
                        particles[j].y
                    );

                    ctx.stroke();

                }

            }

        }


        requestAnimationFrame(animateParticles);

    }


    animateParticles();

})();


/* =====================================================
   COUNTER ANIMATION
===================================================== */

const counterObserver =
    new IntersectionObserver(

        function (entries) {

            entries.forEach(entry => {

                if (entry.isIntersecting) {

                    const counters =
                        entry.target.querySelectorAll(
                            "strong"
                        );

                    counters.forEach(counter => {

                        const text =
                            counter.textContent.trim();

                        const match =
                            text.match(/(\d+)/);

                        if (!match) return;

                        const target =
                            parseInt(match[1]);

                        let current = 0;

                        const step =
                            Math.ceil(target / 60);


                        const timer =
                            setInterval(() => {

                                current += step;

                                if (current >= target) {

                                    current = target;

                                    clearInterval(timer);

                                }

                                counter.textContent =
                                    text.replace(
                                        match[1],
                                        current.toLocaleString()
                                    );

                            }, 25);

                    });


                    counterObserver.unobserve(
                        entry.target
                    );

                }

            });

        },

        { threshold: 0.3 }

    );


const statsBox =
    document.querySelector(".stats-box");

if (statsBox) {

    counterObserver.observe(statsBox);

}


/* =====================================================
   PARALLAX ON SCROLL
===================================================== */

window.addEventListener("scroll", function () {

    const scrolled = window.scrollY;

    /* Parallax floating paws */

    document.querySelectorAll(".floating-paw")
        .forEach((paw, i) => {

            const speed = .02 + (i * .01);

            paw.style.transform =
                `translateY(${scrolled * speed * -1}px)
                 rotate(${scrolled * speed * 2}deg)`;

        });

});