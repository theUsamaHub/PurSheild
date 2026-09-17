/* =========================================================
   FURSHIELD SERVICES PAGE JS
========================================================= */

document.addEventListener("DOMContentLoaded", () => {

    /* =====================================================
       NAVBAR SCROLL
    ===================================================== */

    const navbar = document.getElementById("serviceNavbar");

    const handleNavbar = () => {

        if (!navbar) return;

        if (window.scrollY > 40) {
            navbar.classList.add("scrolled");
        } else {
            navbar.classList.remove("scrolled");
        }
    };

    handleNavbar();

    window.addEventListener("scroll", handleNavbar);



    /* =====================================================
       MOBILE MENU
    ===================================================== */

    const mobileToggle =
        document.getElementById("serviceMobileToggle");

    const mobileNav =
        document.getElementById("serviceMobileNav");


    if (mobileToggle && mobileNav) {

        mobileToggle.addEventListener("click", () => {

            mobileNav.classList.toggle("open");

            mobileToggle.classList.toggle("active");

        });


        mobileNav.querySelectorAll("a").forEach(link => {

            link.addEventListener("click", () => {

                mobileNav.classList.remove("open");

                mobileToggle.classList.remove("active");

            });

        });


        document.addEventListener("keydown", e => {

            if (e.key === "Escape") {

                mobileNav.classList.remove("open");

                mobileToggle.classList.remove("active");

            }

        });

    }



    /* =====================================================
       SCROLL REVEAL
    ===================================================== */

    const revealElements = document.querySelectorAll(
        ".service-reveal-up, " +
        ".service-reveal-left, " +
        ".service-reveal-right"
    );


    const revealObserver =
        new IntersectionObserver(

            entries => {

                entries.forEach(entry => {

                    if (entry.isIntersecting) {

                        entry.target.classList.add(
                            "service-visible"
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
       COUNTER ANIMATION
    ===================================================== */

    const counters =
        document.querySelectorAll(
            ".service-stat-card strong[data-target]"
        );


    const counterObserver =
        new IntersectionObserver(

            entries => {

                entries.forEach(entry => {

                    if (!entry.isIntersecting) return;

                    const counter = entry.target;

                    const target =
                        Number(counter.dataset.target);

                    const suffix =
                        counter.dataset.suffix || "";

                    let current = 0;

                    const duration = 1500;

                    const frameRate = 60;

                    const totalFrames =
                        duration / (1000 / frameRate);

                    const increment =
                        target / totalFrames;


                    const updateCounter = () => {

                        current += increment;

                        if (current < target) {

                            counter.textContent =
                                Math.floor(current) + suffix;

                            requestAnimationFrame(
                                updateCounter
                            );

                        } else {

                            counter.textContent =
                                target + suffix;

                        }

                    };


                    updateCounter();

                    counterObserver.unobserve(counter);

                });

            },

            {
                threshold: 0.45
            }

        );


    counters.forEach(counter => {

        counterObserver.observe(counter);

    });



    /* =====================================================
       VIDEO PLAY / PAUSE
    ===================================================== */

    const serviceVideo =
        document.getElementById("serviceVideo");

    const videoToggle =
        document.getElementById("serviceVideoToggle");


    if (serviceVideo && videoToggle) {

        const videoIcon =
            videoToggle.querySelector(
                ".video-play-icon i"
            );

        const videoLabel =
            videoToggle.querySelector(
                ".video-control-label"
            );


        const updateVideoButton = () => {

            if (serviceVideo.paused) {

                videoIcon.className =
                    "fa-solid fa-play";

                videoLabel.textContent =
                    "Play Video";

            } else {

                videoIcon.className =
                    "fa-solid fa-pause";

                videoLabel.textContent =
                    "Pause Video";

            }

        };


        videoToggle.addEventListener("click", () => {

            if (serviceVideo.paused) {

                serviceVideo.play()
                    .catch(() => {});

            } else {

                serviceVideo.pause();

            }

            updateVideoButton();

        });


        serviceVideo.addEventListener(
            "play",
            updateVideoButton
        );

        serviceVideo.addEventListener(
            "pause",
            updateVideoButton
        );

    }



    /* =====================================================
       3D TILT SERVICE CARDS
    ===================================================== */

    const premiumCards =
        document.querySelectorAll(
            ".premium-service-card"
        );


    premiumCards.forEach(card => {

        card.addEventListener(
            "mousemove",
            event => {

                if (
                    window.matchMedia(
                        "(max-width: 850px)"
                    ).matches
                ) {
                    return;
                }

                const rect =
                    card.getBoundingClientRect();

                const x =
                    event.clientX - rect.left;

                const y =
                    event.clientY - rect.top;

                const centerX =
                    rect.width / 2;

                const centerY =
                    rect.height / 2;

                const rotateX =
                    ((y - centerY) / centerY) * -4;

                const rotateY =
                    ((x - centerX) / centerX) * 4;


                card.style.transform = `
                    perspective(900px)
                    rotateX(${rotateX}deg)
                    rotateY(${rotateY}deg)
                    translateY(-7px)
                `;

            }
        );


        card.addEventListener(
            "mouseleave",
            () => {

                card.style.transform =
                    "perspective(900px) rotateX(0deg) rotateY(0deg) translateY(0)";

            }
        );

    });



    /* =====================================================
       HERO IMAGE 3D EFFECT
    ===================================================== */

    const heroVisual =
        document.querySelector(
            ".service-hero-visual"
        );

    const heroImage =
        document.querySelector(
            ".service-main-image"
        );


    if (heroVisual && heroImage) {

        heroVisual.addEventListener(
            "mousemove",
            event => {

                if (
                    window.matchMedia(
                        "(max-width: 850px)"
                    ).matches
                ) {
                    return;
                }

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
                    ((x - centerX) / centerX) * 5;

                const rotateX =
                    ((y - centerY) / centerY) * -5;


                heroImage.style.transform = `
                    perspective(1000px)
                    rotateX(${rotateX}deg)
                    rotateY(${rotateY}deg)
                    scale(1.02)
                `;

            }
        );


        heroVisual.addEventListener(
            "mouseleave",
            () => {

                heroImage.style.transform =
                    "perspective(1000px) rotateX(0deg) rotateY(0deg) scale(1)";

            }
        );

    }



    /* =====================================================
       SMOOTH INTERNAL LINKS
    ===================================================== */

    document
        .querySelectorAll('a[href^="#"]')
        .forEach(anchor => {

            anchor.addEventListener(
                "click",
                event => {

                    const href =
                        anchor.getAttribute("href");

                    if (
                        !href ||
                        href === "#"
                    ) {
                        return;
                    }


                    const target =
                        document.querySelector(href);


                    if (target) {

                        event.preventDefault();

                        const navbarHeight =
                            navbar
                                ? navbar.offsetHeight
                                : 0;

                        const top =
                            target.getBoundingClientRect()
                                .top
                            + window.scrollY
                            - navbarHeight
                            - 10;


                        window.scrollTo({
                            top: top,
                            behavior: "smooth"
                        });

                    }

                }
            );

        });



    /* =====================================================
       BACK TO TOP
    ===================================================== */

    const backTop =
        document.getElementById(
            "serviceBackTop"
        );


    if (backTop) {

        window.addEventListener(
            "scroll",
            () => {

                if (window.scrollY > 500) {

                    backTop.classList.add("show");

                } else {

                    backTop.classList.remove("show");

                }

            }
        );


        backTop.addEventListener(
            "click",
            () => {

                window.scrollTo({
                    top: 0,
                    behavior: "smooth"
                });

            }
        );

    }



    /* =====================================================
       PARTICLE CANVAS
    ===================================================== */

    const canvas =
        document.getElementById(
            "serviceParticles"
        );


    if (canvas) {

        const ctx =
            canvas.getContext("2d");

        let particles = [];

        let animationFrame;


        const resizeCanvas = () => {

            canvas.width =
                window.innerWidth;

            canvas.height =
                window.innerHeight;

        };


        resizeCanvas();


        window.addEventListener(
            "resize",
            resizeCanvas
        );


        class Particle {

            constructor() {
                this.reset(true);
            }


            reset(initial = false) {

                this.x =
                    Math.random()
                    * canvas.width;

                this.y =
                    initial
                        ? Math.random()
                          * canvas.height
                        : canvas.height + 20;

                this.radius =
                    Math.random()
                    * 2
                    + 0.6;

                this.speedY =
                    Math.random()
                    * 0.35
                    + 0.12;

                this.speedX =
                    (Math.random() - 0.5)
                    * 0.22;

                this.alpha =
                    Math.random()
                    * 0.15
                    + 0.04;

            }


            update() {

                this.y -= this.speedY;

                this.x += this.speedX;


                if (
                    this.y < -20 ||
                    this.x < -20 ||
                    this.x > canvas.width + 20
                ) {

                    this.reset();

                }

            }


            draw() {

                ctx.beginPath();

                ctx.arc(
                    this.x,
                    this.y,
                    this.radius,
                    0,
                    Math.PI * 2
                );

                ctx.fillStyle =
                    `rgba(9, 168, 106, ${this.alpha})`;

                ctx.fill();

            }

        }


        const createParticles = () => {

            particles = [];

            const count =
                window.innerWidth < 700
                    ? 25
                    : 55;


            for (
                let i = 0;
                i < count;
                i++
            ) {

                particles.push(
                    new Particle()
                );

            }

        };


        createParticles();


        const connectParticles = () => {

            for (
                let i = 0;
                i < particles.length;
                i++
            ) {

                for (
                    let j = i + 1;
                    j < particles.length;
                    j++
                ) {

                    const dx =
                        particles[i].x
                        - particles[j].x;

                    const dy =
                        particles[i].y
                        - particles[j].y;

                    const distance =
                        Math.sqrt(
                            dx * dx + dy * dy
                        );


                    if (distance < 115) {

                        const opacity =
                            (1 - distance / 115)
                            * 0.05;


                        ctx.beginPath();

                        ctx.moveTo(
                            particles[i].x,
                            particles[i].y
                        );

                        ctx.lineTo(
                            particles[j].x,
                            particles[j].y
                        );

                        ctx.strokeStyle =
                            `rgba(9,168,106,${opacity})`;

                        ctx.lineWidth = 1;

                        ctx.stroke();

                    }

                }

            }

        };


        const animateParticles = () => {

            ctx.clearRect(
                0,
                0,
                canvas.width,
                canvas.height
            );


            particles.forEach(
                particle => {

                    particle.update();

                    particle.draw();

                }
            );


            connectParticles();


            animationFrame =
                requestAnimationFrame(
                    animateParticles
                );

        };


        animateParticles();


        window.addEventListener(
            "resize",
            () => {

                cancelAnimationFrame(
                    animationFrame
                );

                createParticles();

                animateParticles();

            }
        );

    }



    /* =====================================================
       RIPPLE BUTTON EFFECT
    ===================================================== */

    document
        .querySelectorAll(
            ".ripple-btn"
        )
        .forEach(button => {

            button.addEventListener(
                "click",
                event => {

                    const rect =
                        button.getBoundingClientRect();

                    const size =
                        Math.max(
                            rect.width,
                            rect.height
                        );

                    const ripple =
                        document.createElement("span");

                    ripple.classList.add(
                        "service-ripple"
                    );

                    ripple.style.width =
                        `${size}px`;

                    ripple.style.height =
                        `${size}px`;

                    ripple.style.left =
                        `${
                            event.clientX
                            - rect.left
                            - size / 2
                        }px`;

                    ripple.style.top =
                        `${
                            event.clientY
                            - rect.top
                            - size / 2
                        }px`;


                    button.appendChild(
                        ripple
                    );


                    setTimeout(
                        () => ripple.remove(),
                        700
                    );

                }
            );

        });



    /* =====================================================
       IMAGE FALLBACK
    ===================================================== */

    document
        .querySelectorAll("img")
        .forEach(image => {

            image.addEventListener(
                "error",
                () => {

                    image.style.display =
                        "none";

                    if (image.parentElement) {

                        image.parentElement
                            .style.background =
                            "linear-gradient(135deg,#effcf5,#dff8ec)";

                    }

                }
            );

        });

});