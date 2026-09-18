document.addEventListener("DOMContentLoaded", () => {

    /* =========================================================
       NAVBAR
    ========================================================= */

    const navbar =
        document.getElementById("productNavbar");

    const updateNavbar = () => {

        if (!navbar) return;

        navbar.classList.toggle(
            "scrolled",
            window.scrollY > 35
        );

    };

    updateNavbar();

    window.addEventListener(
        "scroll",
        updateNavbar
    );



    /* =========================================================
       MOBILE NAV
    ========================================================= */

    const mobileToggle =
        document.getElementById(
            "productMobileToggle"
        );

    const mobileNav =
        document.getElementById(
            "productMobileNav"
        );


    if (mobileToggle && mobileNav) {

        mobileToggle.addEventListener(
            "click",
            () => {

                mobileNav.classList.toggle(
                    "open"
                );

            }
        );


        mobileNav
            .querySelectorAll("a")
            .forEach(link => {

                link.addEventListener(
                    "click",
                    () => {

                        mobileNav
                            .classList
                            .remove("open");

                    }
                );

            });


        document.addEventListener(
            "keydown",
            event => {

                if (event.key === "Escape") {

                    mobileNav.classList.remove(
                        "open"
                    );

                }

            }
        );

    }



    /* =========================================================
       CURSOR GLOW
    ========================================================= */

    const cursorGlow =
        document.getElementById(
            "cursorGlow"
        );


    if (cursorGlow) {

        window.addEventListener(
            "mousemove",
            event => {

                cursorGlow.style.left =
                    `${event.clientX}px`;

                cursorGlow.style.top =
                    `${event.clientY}px`;

            }
        );

    }



    /* =========================================================
       REVEAL ANIMATION
    ========================================================= */

    const revealElements =
        document.querySelectorAll(
            ".reveal-up, .reveal-left, .reveal-right"
        );


    const revealObserver =
        new IntersectionObserver(

            entries => {

                entries.forEach(
                    (entry) => {

                        if (
                            entry.isIntersecting
                        ) {

                            entry.target
                                .classList
                                .add(
                                    "product-visible"
                                );

                            revealObserver
                                .unobserve(
                                    entry.target
                                );

                        }

                    }
                );

            },

            {
                threshold: 0.12
            }

        );


    revealElements.forEach(
        (element, index) => {

            if (
                element.classList.contains(
                    "vip-product-card"
                )
            ) {

                element.style.transitionDelay =
                    `${
                        (index % 6) * 70
                    }ms`;

            }

            revealObserver.observe(
                element
            );

        }
    );



    /* =========================================================
       PRODUCT CARD 3D TILT
    ========================================================= */

    const productCards =
        document.querySelectorAll(
            ".vip-product-card"
        );


    productCards.forEach(card => {

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


                const centerX =
                    rect.width / 2;

                const centerY =
                    rect.height / 2;


                const rotateY =
                    ((x - centerX)
                    / centerX)
                    * 5.5;

                const rotateX =
                    ((y - centerY)
                    / centerY)
                    * -5.5;


                card.style.transform = `
                    perspective(950px)
                    rotateX(${rotateX}deg)
                    rotateY(${rotateY}deg)
                    translateY(-8px)
                    scale(1.015)
                `;

            }
        );


        card.addEventListener(
            "mouseleave",
            () => {

                card.style.transform = `
                    perspective(950px)
                    rotateX(0deg)
                    rotateY(0deg)
                    translateY(0)
                    scale(1)
                `;

            }
        );

    });



    /* =========================================================
       HERO PRODUCT 3D
    ========================================================= */

    const heroStage =
        document.getElementById(
            "heroProductStage"
        );

    const heroCard =
        document.getElementById(
            "heroProductCard"
        );


    if (heroStage && heroCard) {

        heroStage.addEventListener(
            "mousemove",
            event => {

                if (
                    window.innerWidth <= 850
                ) {
                    return;
                }


                const rect =
                    heroStage
                        .getBoundingClientRect();


                const mouseX =
                    event.clientX
                    - rect.left;

                const mouseY =
                    event.clientY
                    - rect.top;


                const rotateY =
                    (
                        (
                            mouseX
                            - rect.width / 2
                        )
                        / (rect.width / 2)
                    ) * 9;


                const rotateX =
                    (
                        (
                            mouseY
                            - rect.height / 2
                        )
                        / (rect.height / 2)
                    ) * -9;


                heroCard.style.animation =
                    "none";

                heroCard.style.transform = `
                    perspective(1100px)
                    rotateX(${rotateX}deg)
                    rotateY(${rotateY}deg)
                    translateZ(15px)
                `;

            }
        );


        heroStage.addEventListener(
            "mouseleave",
            () => {

                heroCard.style.transform =
                    "perspective(1100px) rotateX(0deg) rotateY(0deg)";

                heroCard.style.animation =
                    "heroCardFloat 5s ease-in-out infinite";

            }
        );

    }



    /* =========================================================
       FEATURED PRODUCT 3D
    ========================================================= */

    const featuredVisual =
        document.querySelector(
            ".featured-visual"
        );

    const featuredCard =
        document.getElementById(
            "featuredImageCard"
        );


    if (
        featuredVisual
        && featuredCard
    ) {

        featuredVisual.addEventListener(
            "mousemove",
            event => {

                if (
                    window.innerWidth <= 850
                ) {
                    return;
                }


                const rect =
                    featuredVisual
                        .getBoundingClientRect();


                const x =
                    event.clientX
                    - rect.left;

                const y =
                    event.clientY
                    - rect.top;


                const rotateY =
                    (
                        (
                            x
                            - rect.width / 2
                        )
                        / (rect.width / 2)
                    ) * 7;


                const rotateX =
                    (
                        (
                            y
                            - rect.height / 2
                        )
                        / (rect.height / 2)
                    ) * -7;


                featuredCard.style.transform = `
                    perspective(1000px)
                    rotateX(${rotateX}deg)
                    rotateY(${rotateY}deg)
                    scale(1.025)
                `;

            }
        );


        featuredVisual.addEventListener(
            "mouseleave",
            () => {

                featuredCard.style.transform =
                    "perspective(1000px) rotateX(0deg) rotateY(0deg) scale(1)";

            }
        );

    }



    /* =========================================================
       PRODUCT FILTER + SEARCH
    ========================================================= */

    const filterButtons =
        document.querySelectorAll(
            ".filter-btn"
        );

    const searchInput =
        document.getElementById(
            "productSearch"
        );

    const noProducts =
        document.getElementById(
            "noProducts"
        );


    let currentFilter = "all";


    const filterProducts = () => {

        const query =
            searchInput
                ? searchInput.value
                    .toLowerCase()
                    .trim()
                : "";


        let visibleCount = 0;


        productCards.forEach(card => {

            const category =
                card.dataset.category;

            const name =
                (
                    card.dataset.name || ""
                ).toLowerCase();


            const categoryMatch =
                currentFilter === "all"
                || category === currentFilter;


            const searchMatch =
                name.includes(query);


            const show =
                categoryMatch
                && searchMatch;


            card.classList.toggle(
                "hidden-product",
                !show
            );


            if (show) {
                visibleCount++;
            }

        });


        if (noProducts) {

            noProducts.classList.toggle(
                "show",
                visibleCount === 0
            );

        }

    };


    filterButtons.forEach(button => {

        button.addEventListener(
            "click",
            () => {

                filterButtons.forEach(
                    item => {

                        item.classList.remove(
                            "active"
                        );

                    }
                );


                button.classList.add(
                    "active"
                );


                currentFilter =
                    button.dataset.filter;


                filterProducts();

            }
        );

    });


    if (searchInput) {

        searchInput.addEventListener(
            "input",
            filterProducts
        );

    }



    /* =========================================================
       CATEGORY CARDS → FILTER
    ========================================================= */

    document
        .querySelectorAll(
            ".category-showcase-card"
        )
        .forEach(card => {

            card.addEventListener(
                "click",
                () => {

                    const targetFilter =
                        card.dataset
                            .targetFilter;


                    const targetButton =
                        document.querySelector(
                            `.filter-btn[data-filter="${targetFilter}"]`
                        );


                    if (targetButton) {

                        targetButton.click();

                    }


                    const collection =
                        document.getElementById(
                            "productCollection"
                        );


                    if (collection) {

                        collection.scrollIntoView({
                            behavior: "smooth"
                        });

                    }

                }
            );

        });



    /* =========================================================
       CART
    ========================================================= */

    const cart = [];


    const cartCount =
        document.getElementById(
            "cartCount"
        );

    const cartItems =
        document.getElementById(
            "cartItems"
        );

    const cartTotal =
        document.getElementById(
            "cartTotal"
        );

    const emptyCart =
        document.getElementById(
            "emptyCart"
        );

    const cartDrawer =
        document.getElementById(
            "cartDrawer"
        );

    const cartOverlay =
        document.getElementById(
            "cartOverlay"
        );

    const openCart =
        document.getElementById(
            "openCart"
        );

    const closeCart =
        document.getElementById(
            "closeCart"
        );

    const toast =
        document.getElementById(
            "cartToast"
        );


    const formatPKR = value => {

        return (
            "PKR "
            + Number(value)
                .toLocaleString()
        );

    };


    const openCartDrawer = () => {

        cartDrawer?.classList.add(
            "open"
        );

        cartOverlay?.classList.add(
            "show"
        );

        document.body.classList.add(
            "cart-open"
        );

    };


    const closeCartDrawer = () => {

        cartDrawer?.classList.remove(
            "open"
        );

        cartOverlay?.classList.remove(
            "show"
        );

        document.body.classList.remove(
            "cart-open"
        );

    };


    openCart?.addEventListener(
        "click",
        openCartDrawer
    );


    closeCart?.addEventListener(
        "click",
        closeCartDrawer
    );


    cartOverlay?.addEventListener(
        "click",
        closeCartDrawer
    );



    const showToast = () => {

        if (!toast) return;


        toast.classList.add(
            "show"
        );


        setTimeout(
            () => {

                toast.classList.remove(
                    "show"
                );

            },
            1700
        );

    };



    const renderCart = () => {

        if (
            !cartItems
            || !cartCount
            || !cartTotal
        ) {
            return;
        }


        cartItems
            .querySelectorAll(
                ".cart-item"
            )
            .forEach(item => {

                item.remove();

            });


        cartCount.textContent =
            cart.length;


        if (emptyCart) {

            emptyCart.style.display =
                cart.length
                    ? "none"
                    : "flex";

        }


        let total = 0;


        cart.forEach(
            (item,index) => {

                total += item.price;


                const cartItem =
                    document.createElement(
                        "div"
                    );


                cartItem.className =
                    "cart-item";


                cartItem.innerHTML = `

                    <img
                        src="${item.image}"
                        alt="${item.name}"
                    >

                    <div class="cart-item-info">

                        <strong>
                            ${item.name}
                        </strong>

                        <span>
                            ${formatPKR(item.price)}
                        </span>

                    </div>

                    <button
                        class="cart-remove"
                        data-index="${index}"
                    >

                        <i class="fa-solid fa-trash"></i>

                    </button>

                `;


                cartItems.appendChild(
                    cartItem
                );

            }
        );


        cartTotal.textContent =
            formatPKR(total);


        cartItems
            .querySelectorAll(
                ".cart-remove"
            )
            .forEach(button => {

                button.addEventListener(
                    "click",
                    () => {

                        const index =
                            Number(
                                button.dataset.index
                            );


                        cart.splice(
                            index,
                            1
                        );


                        renderCart();

                    }
                );

            });

    };



    document
        .querySelectorAll(
            ".add-cart-btn"
        )
        .forEach(button => {

            button.addEventListener(
                "click",
                event => {

                    if (button.tagName === "A" || button.type === "submit" || button.closest("form")) {
                        return;
                    }

                    event.preventDefault();


                    const product = {

                        name:
                            button.dataset.name,

                        price:
                            Number(
                                button.dataset.price
                            ),

                        image:
                            button.dataset.image

                    };


                    cart.push(product);


                    renderCart();

                    showToast();


                    button.classList.add(
                        "added"
                    );


                    const icon =
                        button.querySelector(
                            "i"
                        );


                    if (icon) {

                        const original =
                            icon.className;


                        icon.className =
                            "fa-solid fa-check";


                        setTimeout(
                            () => {

                                icon.className =
                                    original;

                                button.classList.remove(
                                    "added"
                                );

                            },
                            900
                        );

                    }

                }
            );

        });



    /* =========================================================
       MAGNETIC BUTTONS
    ========================================================= */

    document
        .querySelectorAll(
            ".magnetic-btn"
        )
        .forEach(button => {

            button.addEventListener(
                "mousemove",
                event => {

                    if (
                        window.innerWidth <= 850
                    ) {
                        return;
                    }


                    const rect =
                        button.getBoundingClientRect();


                    const x =
                        event.clientX
                        - rect.left
                        - rect.width / 2;


                    const y =
                        event.clientY
                        - rect.top
                        - rect.height / 2;


                    button.style.transform = `
                        translate(
                            ${x * .08}px,
                            ${y * .08}px
                        )
                    `;

                }
            );


            button.addEventListener(
                "mouseleave",
                () => {

                    button.style.transform =
                        "translate(0,0)";

                }
            );

        });



    /* =========================================================
       SMOOTH INTERNAL LINKS
    ========================================================= */

    document
        .querySelectorAll(
            'a[href^="#"]'
        )
        .forEach(anchor => {

            anchor.addEventListener(
                "click",
                event => {

                    const href =
                        anchor
                            .getAttribute(
                                "href"
                            );


                    if (
                        !href
                        || href === "#"
                    ) {
                        return;
                    }


                    const target =
                        document
                            .querySelector(
                                href
                            );


                    if (!target) {
                        return;
                    }


                    event.preventDefault();


                    const navbarHeight =
                        navbar
                            ? navbar.offsetHeight
                            : 0;


                    const top =
                        target
                            .getBoundingClientRect()
                            .top
                        + window.scrollY
                        - navbarHeight
                        - 10;


                    window.scrollTo({

                        top,

                        behavior: "smooth"

                    });

                }
            );

        });



    /* =========================================================
       BACK TO TOP
    ========================================================= */

    const backTop =
        document.getElementById(
            "productBackTop"
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



    /* =========================================================
       PARTICLE BACKGROUND
    ========================================================= */

    const canvas =
        document.getElementById(
            "productParticles"
        );


    if (canvas) {

        const ctx =
            canvas.getContext("2d");


        let particles = [];


        const resizeCanvas = () => {

            canvas.width =
                window.innerWidth;

            canvas.height =
                window.innerHeight;

        };


        resizeCanvas();


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
                    Math.random()
                    * 1.8 + .6;


                this.speedY =
                    Math.random()
                    * .28 + .08;


                this.speedX =
                    (
                        Math.random()
                        - .5
                    ) * .15;


                this.gold =
                    Math.random() > .82;


                this.alpha =
                    Math.random()
                    * .14 + .04;

            }


            update() {

                this.y -=
                    this.speedY;


                this.x +=
                    this.speedX;


                if (
                    this.y < -20
                    || this.x < -20
                    || this.x
                        > canvas.width + 20
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
                    this.gold
                        ? `rgba(212,168,83,${this.alpha})`
                        : `rgba(9,168,106,${this.alpha})`;


                ctx.fill();

            }

        }


        const createParticles = () => {

            particles = [];


            const amount =
                window.innerWidth < 700
                    ? 22
                    : 50;


            for (
                let i = 0;
                i < amount;
                i++
            ) {

                particles.push(
                    new Particle()
                );

            }

        };


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
                            dx * dx
                            + dy * dy
                        );


                    if (
                        distance < 110
                    ) {

                        const opacity =
                            (
                                1
                                - distance / 110
                            ) * .045;


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


            connectParticles();


            requestAnimationFrame(
                animate
            );

        };


        createParticles();

        animate();


        window.addEventListener(
            "resize",
            () => {

                resizeCanvas();

                createParticles();

            }
        );

    }



    /* =========================================================
       IMAGE FALLBACK
    ========================================================= */

    document
        .querySelectorAll("img")
        .forEach(image => {

            image.addEventListener(
                "error",
                () => {

                    image.style.display =
                        "none";


                    if (
                        image.parentElement
                    ) {

                        image
                            .parentElement
                            .style
                            .background = `
                                linear-gradient(
                                    135deg,
                                    #effcf5,
                                    #dff8ec
                                )
                            `;

                    }

                }
            );

        });

});