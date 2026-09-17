document.addEventListener("DOMContentLoaded", () => {

    /* NAVBAR */

    const navbar =
        document.getElementById("innerNavbar");

    const mobileToggle =
        document.getElementById("innerMobileToggle");

    const mobileNav =
        document.getElementById("innerMobileNav");


    const navbarScroll = () => {

        navbar?.classList.toggle(
            "scrolled",
            window.scrollY > 35
        );

    };


    navbarScroll();

    window.addEventListener(
        "scroll",
        navbarScroll
    );


    mobileToggle?.addEventListener(
        "click",
        () => {

            mobileNav?.classList.toggle(
                "open"
            );

        }
    );


    mobileNav
        ?.querySelectorAll("a")
        .forEach(link => {

            link.addEventListener(
                "click",
                () => {

                    mobileNav.classList.remove(
                        "open"
                    );

                }
            );

        });



    /* REVEAL */

    const reveals =
        document.querySelectorAll(
            ".reveal-up, .reveal-left, .reveal-right"
        );


    const revealObserver =
        new IntersectionObserver(

            entries => {

                entries.forEach(entry => {

                    if (
                        entry.isIntersecting
                    ) {

                        entry.target
                            .classList
                            .add(
                                "inner-visible"
                            );

                        revealObserver
                            .unobserve(
                                entry.target
                            );

                    }

                });

            },

            {
                threshold: .12
            }

        );


    reveals.forEach(
        (item, index) => {

            if (
                item.classList.contains(
                    "reveal-up"
                )
            ) {

                item.style.transitionDelay =
                    `${(index % 5) * 55}ms`;

            }

            revealObserver.observe(item);

        }
    );



    /* VIP 3D TILT */

    document
        .querySelectorAll(".vip-tilt")
        .forEach(card => {

            card.addEventListener(
                "mousemove",
                event => {

                    if (
                        window.innerWidth <= 850
                    ) {
                        return;
                    }


                    const rect =
                        card.getBoundingClientRect();


                    const x =
                        event.clientX
                        - rect.left;


                    const y =
                        event.clientY
                        - rect.top;


                    const rotateY =
                        (
                            (x - rect.width / 2)
                            / (rect.width / 2)
                        ) * 4.5;


                    const rotateX =
                        (
                            (y - rect.height / 2)
                            / (rect.height / 2)
                        ) * -4.5;


                    card.style.transform = `
                        perspective(1000px)
                        rotateX(${rotateX}deg)
                        rotateY(${rotateY}deg)
                        translateY(-5px)
                    `;

                }
            );


            card.addEventListener(
                "mouseleave",
                () => {

                    card.style.transform = `
                        perspective(1000px)
                        rotateX(0deg)
                        rotateY(0deg)
                        translateY(0)
                    `;

                }
            );

        });



    /* VIDEO */

    const video =
        document.getElementById(
            "innerVideo"
        );

    const videoToggle =
        document.getElementById(
            "innerVideoToggle"
        );


    if (
        video
        && videoToggle
    ) {

        videoToggle.addEventListener(
            "click",
            () => {

                const icon =
                    videoToggle.querySelector("i");

                const text =
                    videoToggle.querySelector("span");


                if (video.paused) {

                    video.play();

                    icon.className =
                        "fa-solid fa-pause";

                    text.textContent =
                        "Pause Video";

                } else {

                    video.pause();

                    icon.className =
                        "fa-solid fa-play";

                    text.textContent =
                        "Play Video";

                }

            }
        );

    }



    /* FAQ */

    document
        .querySelectorAll(
            ".faq-question"
        )
        .forEach(question => {

            question.addEventListener(
                "click",
                () => {

                    const item =
                        question.parentElement;

                    const answer =
                        item.querySelector(
                            ".faq-answer"
                        );


                    item.classList.toggle(
                        "active"
                    );


                    if (
                        item.classList.contains(
                            "active"
                        )
                    ) {

                        answer.style.maxHeight =
                            answer.scrollHeight
                            + "px";

                    } else {

                        answer.style.maxHeight =
                            "0px";

                    }

                }
            );

        });



    /* DIRECTORY FILTER */

    const directoryCards =
        document.querySelectorAll(
            ".profile-card"
        );

    const directorySearch =
        document.getElementById(
            "directorySearch"
        );

    const filterButtons =
        document.querySelectorAll(
            ".directory-filter"
        );

    const emptyDirectory =
        document.getElementById(
            "directoryEmpty"
        );


    let activeFilter = "all";


    const runDirectoryFilter = () => {

        const search =
            (
                directorySearch?.value
                || ""
            )
            .toLowerCase()
            .trim();


        let visible = 0;


        directoryCards.forEach(card => {

            const category =
                card.dataset.category;

            const name =
                (
                    card.dataset.name
                    || ""
                ).toLowerCase();


            const filterMatch =
                activeFilter === "all"
                || activeFilter === category;


            const searchMatch =
                name.includes(search);


            const show =
                filterMatch
                && searchMatch;


            card.classList.toggle(
                "hidden-directory",
                !show
            );


            if (show) {
                visible++;
            }

        });


        emptyDirectory
            ?.classList
            .toggle(
                "show",
                visible === 0
            );

    };


    filterButtons.forEach(button => {

        button.addEventListener(
            "click",
            () => {

                filterButtons.forEach(
                    item =>
                        item.classList.remove(
                            "active"
                        )
                );


                button.classList.add(
                    "active"
                );


                activeFilter =
                    button.dataset.filter;


                runDirectoryFilter();

            }
        );

    });


    directorySearch
        ?.addEventListener(
            "input",
            runDirectoryFilter
        );



    /* CONTACT FORM DEMO */

    const contactForm =
        document.getElementById(
            "contactForm"
        );

    const toast =
        document.getElementById(
            "pageToast"
        );


    contactForm?.addEventListener(
        "submit",
        event => {

            event.preventDefault();

            toast?.classList.add(
                "show"
            );

            contactForm.reset();


            setTimeout(
                () => {

                    toast?.classList.remove(
                        "show"
                    );

                },
                2200
            );

        }
    );



    /* BACK TOP */

    const backTop =
        document.getElementById(
            "innerBackTop"
        );


    window.addEventListener(
        "scroll",
        () => {

            backTop?.classList.toggle(
                "show",
                window.scrollY > 500
            );

        }
    );


    backTop?.addEventListener(
        "click",
        () => {

            window.scrollTo({
                top: 0,
                behavior: "smooth"
            });

        }
    );



    /* PARTICLES */

    const canvas =
        document.getElementById(
            "innerParticles"
        );


    if (canvas) {

        const ctx =
            canvas.getContext("2d");

        let particles = [];


        const resize = () => {

            canvas.width =
                window.innerWidth;

            canvas.height =
                window.innerHeight;

        };


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
                        : canvas.height + 10;

                this.radius =
                    Math.random() * 1.8 + .5;

                this.speed =
                    Math.random() * .28 + .08;

                this.horizontal =
                    (Math.random() - .5) * .13;

                this.alpha =
                    Math.random() * .13 + .04;

            }


            update() {

                this.y -= this.speed;

                this.x += this.horizontal;


                if (
                    this.y < -20
                    || this.x < -20
                    || this.x > canvas.width + 20
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
                    `rgba(9,168,106,${this.alpha})`;

                ctx.fill();

            }

        }


        const createParticles = () => {

            particles = [];

            const count =
                window.innerWidth < 700
                    ? 22
                    : 48;


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


        const animate = () => {

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


            requestAnimationFrame(
                animate
            );

        };


        resize();

        createParticles();

        animate();


        window.addEventListener(
            "resize",
            () => {

                resize();

                createParticles();

            }
        );

    }

});