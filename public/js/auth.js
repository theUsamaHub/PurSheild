document.addEventListener("DOMContentLoaded", () => {

    /* ROLE SELECTOR */

    const roleButtons =
        document.querySelectorAll(
            ".role-option"
        );

    const selectedRole =
        document.getElementById(
            "selectedRole"
        );


    roleButtons.forEach(button => {

        button.addEventListener(
            "click",
            () => {

                roleButtons.forEach(
                    item =>
                        item.classList.remove(
                            "active"
                        )
                );


                button.classList.add(
                    "active"
                );


                if (selectedRole) {

                    selectedRole.value =
                        button.dataset.role;

                }

            }
        );

    });



    /* PASSWORD SHOW / HIDE */

    document
        .querySelectorAll(
            ".password-toggle"
        )
        .forEach(button => {

            button.addEventListener(
                "click",
                () => {

                    const target =
                        document.getElementById(
                            button.dataset.target
                        );


                    if (!target) return;


                    const icon =
                        button.querySelector("i");


                    if (
                        target.type === "password"
                    ) {

                        target.type = "text";

                        icon.className =
                            "fa-regular fa-eye-slash";

                    } else {

                        target.type = "password";

                        icon.className =
                            "fa-regular fa-eye";

                    }

                }
            );

        });



    /* PASSWORD STRENGTH */

    const password =
        document.getElementById(
            "registerPassword"
        );

    const strengthBar =
        document.getElementById(
            "strengthBar"
        );

    const strengthText =
        document.getElementById(
            "strengthText"
        );


    password?.addEventListener(
        "input",
        () => {

            const value =
                password.value;


            let score = 0;


            if (value.length >= 8) score++;
            if (/[A-Z]/.test(value)) score++;
            if (/[0-9]/.test(value)) score++;
            if (/[^A-Za-z0-9]/.test(value)) score++;


            const widths = [
                "0%",
                "25%",
                "50%",
                "75%",
                "100%"
            ];


            const labels = [
                "Password strength",
                "Weak password",
                "Fair password",
                "Good password",
                "Strong password"
            ];


            if (strengthBar) {

                strengthBar.style.width =
                    widths[score];

            }


            if (strengthText) {

                strengthText.textContent =
                    labels[score];

            }

        }
    );



    /* DEMO LOGIN */

    const loginForm =
        document.getElementById(
            "loginForm"
        );

    const registerForm =
        document.getElementById(
            "registerForm"
        );

    const toast =
        document.getElementById(
            "authToast"
        );


    const showToast = message => {

        if (!toast) return;


        toast.querySelector("span")
            .textContent = message;


        toast.classList.add(
            "show"
        );


        setTimeout(
            () => {

                toast.classList.remove(
                    "show"
                );

            },
            2200
        );

    };


    loginForm?.addEventListener(
        "submit",
        event => {

            event.preventDefault();


            showToast(
                "Login UI is ready. Connect your Laravel authentication backend next."
            );

        }
    );


    registerForm?.addEventListener(
        "submit",
        event => {

            event.preventDefault();


            const password =
                document.getElementById(
                    "registerPassword"
                );

            const confirm =
                document.getElementById(
                    "confirmPassword"
                );


            if (
                password
                && confirm
                && password.value
                    !== confirm.value
            ) {

                showToast(
                    "Passwords do not match."
                );

                return;

            }


            showToast(
                "Registration UI is ready. Connect your Laravel backend next."
            );

        }
    );



    /* AUTH PARTICLES */

    const canvas =
        document.getElementById(
            "authParticles"
        );


    if (canvas) {

        const ctx =
            canvas.getContext("2d");


        let dots = [];


        const resize = () => {

            canvas.width =
                window.innerWidth;

            canvas.height =
                window.innerHeight;

        };


        class Dot {

            constructor() {
                this.reset();
            }


            reset() {

                this.x =
                    Math.random()
                    * canvas.width;

                this.y =
                    Math.random()
                    * canvas.height;

                this.radius =
                    Math.random()
                    * 1.7
                    + .5;

                this.speed =
                    Math.random()
                    * .18
                    + .04;

                this.alpha =
                    Math.random()
                    * .15
                    + .03;

            }


            update() {

                this.y -=
                    this.speed;


                if (
                    this.y < -10
                ) {

                    this.y =
                        canvas.height + 10;

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


        const create = () => {

            dots = [];


            const count =
                window.innerWidth < 700
                    ? 25
                    : 55;


            for (
                let i = 0;
                i < count;
                i++
            ) {

                dots.push(
                    new Dot()
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


            dots.forEach(dot => {

                dot.update();

                dot.draw();

            });


            requestAnimationFrame(
                animate
            );

        };


        resize();

        create();

        animate();


        window.addEventListener(
            "resize",
            () => {

                resize();

                create();

            }
        );

    }

});