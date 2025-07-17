<link rel="stylesheet" href="{{ asset('css/components/navbar.css') }}">

    <nav>
        <div id="logo-box" onclick="window.location.href = '/dashboard'">
            <img src="{{ asset('assets/mini_logo.png') }}" alt="mini_logo" id="mini-logo">
            <h1>Curhatorium</h1>
        </div>
        
        <!-- Mobile menu button -->
        <button class="mobile-menu-button" aria-label="Toggle menu">
            <span></span>
            <span></span>
            <span></span>
        </button>
        
        <div id="profile-box">
            <div id="xp-box">
                <div id="badge-box">
                    <img class="badge-logo" src="{{ asset('assets/kindle.svg') }}" alt="badge">
                    <p class="badge-text">Kindle</p>
                </div>
                <p id="xp"></p>
            </div>
            <p class="username">Loading...</p>
            <img src="{{ asset('assets/profile_pict.svg') }}" alt="pict" id="pict">
        </div>
    </nav>
    
    <script>
        document.addEventListener("DOMContentLoaded", () => {
            fetch("/user")
                .then((response) => {
                    if (!response.ok) {
                        throw new Error("Network response was not ok");
                    }
                    return response.json();
                })
                .then((data) => {
                    const username = document.querySelector(".username");
                    if (username) {
                        username.textContent = data.username;
                    }

                    const xp = document.querySelector("#xp");
                    if (xp) {
                        xp.textContent = data.total_xp + " XP";
                    }

                    const badgeBox = document.querySelector("#badge-box");
                    const badgeText = document.querySelector(".badge-text");
                    const badgeLogo = document.querySelector(".badge-logo");

                    if (badgeBox && badgeText && badgeLogo) {
                        if (data.total_xp >= 4000) {
                            badgeText.textContent = "Nova";
                            badgeLogo.src = "{{ asset('assets/nova.svg') }}";
                            badgeBox.style.backgroundColor = "#FF00D4";
                        } else if (data.total_xp >= 3000) {
                            badgeText.textContent = "Inferno";
                            badgeLogo.src = "{{ asset('assets/inferno.svg') }}";
                            badgeBox.style.backgroundColor = "#7220C5";
                        } else if (data.total_xp >= 2000) {
                            badgeText.textContent = "Beacon";
                            badgeLogo.src = "{{ asset('assets/beacon.svg') }}";
                            badgeBox.style.backgroundColor = "#4E42A6";
                        } else if (data.total_xp >= 1000) {
                            badgeText.textContent = "Torch";
                            badgeLogo.src = "{{ asset('assets/torch.svg') }}";
                            badgeBox.style.backgroundColor = "#865A5A";
                        }
                    }
                })
                .catch((error) => {
                    console.error("Fetch error:", error);
                });
        });

        // Toggle mobile menu
        document
            .querySelector(".mobile-menu-button")
            .addEventListener("click", function () {
                const profileBox = document.getElementById("profile-box");
                profileBox.classList.toggle("active");

                // Toggle hamburger to X
                const spans = this.querySelectorAll("span");
                if (profileBox.classList.contains("active")) {
                    spans[0].style.transform = "rotate(-45deg) translate(-5px, 6px)";
                    spans[1].style.opacity = "0";
                    spans[2].style.transform = "rotate(45deg) translate(-5px, -6px)";
                } else {
                    spans[0].style.transform = "none";
                    spans[1].style.opacity = "1";
                    spans[2].style.transform = "none";
                }
            });

        // Close menu when clicking outside
        document.addEventListener("click", function (event) {
            const profileBox = document.getElementById("profile-box");
            const mobileMenuButton = document.querySelector(".mobile-menu-button");

            if (
                !profileBox.contains(event.target) &&
                !mobileMenuButton.contains(event.target) &&
                profileBox.classList.contains("active")
            ) {
                profileBox.classList.remove("active");

                // Reset hamburger icon
                const spans = mobileMenuButton.querySelectorAll("span");
                spans[0].style.transform = "none";
                spans[1].style.opacity = "1";
                spans[2].style.transform = "none";
            }
        });

        // Example: Set username and XP from JavaScript
        // document.querySelector(".username").textContent = "JohnDoe";
        // document.getElementById("xp").textContent = "1250 XP";
    </script>