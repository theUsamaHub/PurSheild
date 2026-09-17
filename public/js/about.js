/* =====================================================
   FURSHIELD PREMIUM ABOUT PAGE JS
===================================================== */

document.addEventListener("DOMContentLoaded", () => {


    /* =====================================================
       NAVBAR SCROLL
    ===================================================== */

    const navbar =
        document.getElementById("aboutNavbar");


    function handleNavbar() {

        if (!navbar) return;

        if (window.scrollY > 50) {

            navbar.classList.add("scrolled");

        } else {

            navbar.classList.remove("scrolled");

        }

    }


    handleNavbar();


    window.addEventListener(
        "scroll",
        handleNavbar,
        { passive: true }
    );



    /* =====================================================
       MOBILE MENU
    ===================================================== */

    const mobileToggle =
        document.getElementById(
            "aboutMobileToggle"
        );

    const mobileNav =
        document.getElementById(
            "aboutMobileNav"
        );


    if (mobileToggle && mobileNav) {

        mobileToggle.addEventListener(
            "click",
            () => {

                mobileNav.classList.toggle("open");

                const icon =
                    mobileToggle.querySelector("i");

                if (!icon) return;


                if (
                    mobileNav.classList.contains("open")
                ) {

                    icon.classList.remove(
                        "fa-bars"
                    );

                    icon.classList.add(
                        "fa-xmark"
                    );

                } else {

                    icon.classList.remove(
                        "fa-xmark"
                    );

                    icon.classList.add(
                        "fa-bars"
                    );

                }

            }
        );


        mobileNav
            .querySelectorAll("a")
            .forEach(link => {

                link.addEventListener(
                    "click",
                    () => {

                        mobileNav.classList.remove(
                            "open"
                        );

                        const icon =
                            mobileToggle.querySelector(
                                "i"
                            );

                        if (icon) {

                            icon.classList.remove(
                                "fa-xmark"
                            );

                            icon.classList.add(
                                "fa-bars"
                            );

                        }

                    }
                );

            });

    }



    /* =====================================================
       ESCAPE CLOSE MENU
    ===================================================== */

    document.addEventListener(
        "keydown",
        event => {

            if (
                event.key === "Escape" &&
                mobileNav
            ) {

                mobileNav.classList.remove(
                    "open"
                );

                const icon =
                    mobileToggle?.querySelector("i");

                if (icon) {

                    icon.classList.remove(
                        "fa-xmark"
                    );

                    icon.classList.add(
                        "fa-bars"
                    );

                }

            }

        }
    );



    /* =====================================================
       SCROLL REVEAL
    ===================================================== */

    const revealElements =
        document.querySelectorAll(
            ".about-reveal-up, " +
            ".about-reveal-left, " +
            ".about-reveal-right"
        );


    if (
        "IntersectionObserver"
        in window
    ) {

        const revealObserver =
            new IntersectionObserver(
                (entries, observer) => {

                    entries.forEach(entry => {

                        if (
                            !entry.isIntersecting
                        ) return;


                        entry.target.classList.add(
                            "about-visible"
                        );


                        observer.unobserve(
                            entry.target
                        );

                    });

                },
                {
                    threshold: .12,
                    rootMargin:
                        "0px 0px -40px 0px"
                }
            );


        revealElements.forEach(element => {

            revealObserver.observe(element);

        });

    } else {

        revealElements.forEach(element => {

            element.classList.add(
                "about-visible"
            );

        });

    }



    /* =====================================================
       COUNTERS
    ===================================================== */

    const counters =
        document.querySelectorAll(
            ".about-stat strong[data-target]"
        );


    function formatNumber(value) {

        if (value >= 1000) {

            return (
                Math.floor(value / 1000)
                + "K+"
            );

        }

        return value + "+";

    }


    function animateCounter(element) {

        const target =
            parseInt(
                element.dataset.target,
                10
            );


        if (!target) return;


        const duration = 1800;

        const start =
            performance.now();


        function update(currentTime) {

            const elapsed =
                currentTime - start;


            const progress =
                Math.min(
                    elapsed / duration,
                    1
                );


            const eased =
                1 -
                Math.pow(
                    1 - progress,
                    3
                );


            const current =
                Math.floor(
                    eased * target
                );


            element.textContent =
                formatNumber(current);


            if (progress < 1) {

                requestAnimationFrame(
                    update
                );

            } else {

                element.textContent =
                    formatNumber(target);

            }

        }


        requestAnimationFrame(update);

    }


    if (
        "IntersectionObserver"
        in window
    ) {

        const counterObserver =
            new IntersectionObserver(
                entries => {

                    entries.forEach(entry => {

                        if (
                            entry.isIntersecting &&
                            !entry.target.dataset.animated
                        ) {

                            entry.target.dataset.animated =
                                "true";

                            animateCounter(
                                entry.target
                            );

                        }

                    });

                },
                {
                    threshold: .5
                }
            );


        counters.forEach(counter => {

            counterObserver.observe(
                counter
            );

        });

    } else {

        counters.forEach(counter => {

            counter.textContent =
                formatNumber(
                    parseInt(
                        counter.dataset.target,
                        10
                    )
                );

        });

    }



    /* =====================================================
       HERO 3D TILT
    ===================================================== */

    const heroVisual =
        document.querySelector(
            ".about-hero-visual"
        );

    const heroImage =
        document.querySelector(
            ".about-main-image"
        );


    if (heroVisual && heroImage) {

        heroVisual.addEventListener(
            "mousemove",
            event => {

                if (
                    window.innerWidth < 900
                ) return;


                const rect =
                    heroVisual.getBoundingClientRect();


                const x =
                    event.clientX -
                    rect.left;


                const y =
                    event.clientY -
                    rect.top;


                const rotateY =
                    (
                        (x - rect.width / 2) /
                        rect.width
                    ) * 8;


                const rotateX =
                    -(
                        (y - rect.height / 2) /
                        rect.height
                    ) * 8;


                heroImage.style.transform =
                    `
                    rotateX(${rotateX}deg)
                    rotateY(${rotateY}deg)
                    translateZ(20px)
                    `;

            }
        );


        heroVisual.addEventListener(
            "mouseleave",
            () => {

                heroImage.style.transform =
                    "rotate(2deg)";

            }
        );

    }



    /* =====================================================
       CARD 3D TILT
    ===================================================== */

    const cards =
        document.querySelectorAll(
            ".mission-card, " +
            ".value-card"
        );


    cards.forEach(card => {

        card.addEventListener(
            "mousemove",
            event => {

                if (
                    window.innerWidth < 900
                ) return;


                const rect =
                    card.getBoundingClientRect();


                const x =
                    event.clientX -
                    rect.left;


                const y =
                    event.clientY -
                    rect.top;


                const centerX =
                    rect.width / 2;


                const centerY =
                    rect.height / 2;


                const rotateY =
                    ((x - centerX) /
                    centerX) * 3;


                const rotateX =
                    -((y - centerY) /
                    centerY) * 3;


                card.style.transform =
                    `
                    translateY(-8px)
                    rotateX(${rotateX}deg)
                    rotateY(${rotateY}deg)
                    `;

            }
        );


        card.addEventListener(
            "mouseleave",
            () => {

                card.style.transform = "";

            }
        );

    });



    /* =====================================================
       SMOOTH INTERNAL LINKS
    ===================================================== */

    document
        .querySelectorAll(
            'a[href^="#"]'
        )
        .forEach(link => {

            link.addEventListener(
                "click",
                event => {

                    const href =
                        link.getAttribute(
                            "href"
                        );


                    if (
                        !href ||
                        href === "#"
                    ) return;


                    const target =
                        document.querySelector(
                            href
                        );


                    if (!target) return;


                    event.preventDefault();


                    const navbarHeight =
                        navbar
                            ? navbar.offsetHeight
                            : 0;


                    const position =
                        target
                            .getBoundingClientRect()
                            .top +
                        window.scrollY -
                        navbarHeight -
                        15;


                    window.scrollTo({

                        top: position,

                        behavior: "smooth"

                    });

                }
            );

        });



    /* =====================================================
       BACK TO TOP
    ===================================================== */

    const backTop =
        document.getElementById(
            "aboutBackTop"
        );


    if (backTop) {

        function updateBackTop() {

            if (
                window.scrollY > 500
            ) {

                backTop.classList.add(
                    "show"
                );

            } else {

                backTop.classList.remove(
                    "show"
                );

            }

        }


        updateBackTop();


        window.addEventListener(
            "scroll",
            updateBackTop,
            { passive: true }
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
       PARTICLE BACKGROUND
    ===================================================== */

    const canvas =
        document.getElementById(
            "aboutParticles"
        );


    if (canvas) {

        const ctx =
            canvas.getContext("2d");


        let particles = [];

        let animationFrame;


        function resizeCanvas() {

            canvas.width =
                window.innerWidth;

            canvas.height =
                window.innerHeight;

        }


        function createParticles() {

            particles = [];


            const amount =
                window.innerWidth < 700
                    ? 25
                    : 55;


            for (
                let i = 0;
                i < amount;
                i++
            ) {

                particles.push({

                    x:
                        Math.random() *
                        canvas.width,

                    y:
                        Math.random() *
                        canvas.height,

                    radius:
                        Math.random() *
                        1.8 + .5,

                    speed:
                        Math.random() *
                        .25 + .08,

                    drift:
                        (Math.random() - .5)
                        * .15,

                    gold:
                        Math.random() > .82

                });

            }

        }


        function drawParticles() {

            ctx.clearRect(
                0,
                0,
                canvas.width,
                canvas.height
            );


            particles.forEach(
                particle => {

                    particle.y -=
                        particle.speed;


                    particle.x +=
                        particle.drift;


                    if (
                        particle.y < -10
                    ) {

                        particle.y =
                            canvas.height + 10;

                    }


                    if (
                        particle.x < -10 ||
                        particle.x >
                        canvas.width + 10
                    ) {

                        particle.x =
                            Math.random() *
                            canvas.width;

                    }


                    ctx.beginPath();


                    ctx.arc(
                        particle.x,
                        particle.y,
                        particle.radius,
                        0,
                        Math.PI * 2
                    );


                    ctx.fillStyle =
                        particle.gold

                            ? "rgba(212,168,83,.15)"

                            : "rgba(9,168,106,.16)";


                    ctx.fill();

                }
            );


            animationFrame =
                requestAnimationFrame(
                    drawParticles
                );

        }


        resizeCanvas();

        createParticles();

        drawParticles();


        window.addEventListener(
            "resize",
            () => {

                resizeCanvas();

                createParticles();

            }
        );


        document.addEventListener(
            "visibilitychange",
            () => {

                if (
                    document.hidden
                ) {

                    cancelAnimationFrame(
                        animationFrame
                    );

                } else {

                    drawParticles();

                }

            }
        );

    }



    /* =====================================================
       IMAGE FALLBACK
    ===================================================== */

    document
        .querySelectorAll("img")
        .forEach(img => {

            img.addEventListener(
                "error",
                () => {

                    img.style.background =
                        "linear-gradient(135deg,#effcf5,#dff8ec)";

                    img.style.objectFit =
                        "cover";

                }
            );

        });



    /* =====================================================
       BUTTON RIPPLE
    ===================================================== */

    document
        .querySelectorAll(
            ".about-primary-btn, " +
            ".about-outline-btn, " +
            ".about-cta-button"
        )
        .forEach(button => {

            button.addEventListener(
                "click",
                event => {

                    const ripple =
                        document.createElement(
                            "span"
                        );


                    const rect =
                        button.getBoundingClientRect();


                    const size =
                        Math.max(
                            rect.width,
                            rect.height
                        );


                    ripple.style.position =
                        "absolute";


                    ripple.style.borderRadius =
                        "50%";


                    ripple.style.pointerEvents =
                        "none";


                    ripple.style.width =
                        `${size}px`;


                    ripple.style.height =
                        `${size}px`;


                    ripple.style.left =
                        `${event.clientX -
                        rect.left -
                        size / 2}px`;


                    ripple.style.top =
                        `${event.clientY -
                        rect.top -
                        size / 2}px`;


                    ripple.style.background =
                        "rgba(255,255,255,.22)";


                    ripple.style.transform =
                        "scale(0)";


                    ripple.style.opacity =
                        "1";


                    ripple.style.transition =
                        "transform .6s ease, opacity .6s ease";


                    if (
                        getComputedStyle(
                            button
                        ).position === "static"
                    ) {

                        button.style.position =
                            "relative";

                    }


                    button.style.overflow =
                        "hidden";


                    button.appendChild(
                        ripple
                    );


                    requestAnimationFrame(
                        () => {

                            ripple.style.transform =
                                "scale(2)";

                            ripple.style.opacity =
                                "0";

                        }
                    );


                    setTimeout(
                        () => {

                            ripple.remove();

                        },
                        650
                    );

                }
            );

        });



    /* =====================================================
       MOUSE PARALLAX FLOATING ELEMENTS
    ===================================================== */

    const floatingElements =
        document.querySelectorAll(
            ".about-floating"
        );


    window.addEventListener(
        "scroll",
        () => {

            const scroll =
                window.scrollY;


            floatingElements.forEach(
                (element, index) => {

                    const speed =
                        .015 +
                        index * .005;


                    element.style.marginTop =
                        `${scroll * speed}px`;

                }
            );

        },
        { passive: true }
    );

});