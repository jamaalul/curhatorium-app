<link rel="stylesheet" href="{{ asset('css/components/navbar.css') }}">

    <nav>
        <div id="logo-box" onclick="window.location.href = '/dashboard'">
            @if (!request()->is('dashboard'))
                <svg xmlns="http://www.w3.org/2000/svg" width="36" height="36" fill="currentColor" class="bi bi-arrow-left-short" viewBox="0 0 16 16">
                    <path fill-rule="evenodd" d="M12 8a.5.5 0 0 1-.5.5H5.707l2.147 2.146a.5.5 0 0 1-.708.708l-3-3a.5.5 0 0 1 0-.708l3-3a.5.5 0 1 1 .708.708L5.707 7.5H11.5a.5.5 0 0 1 .5.5"/>
                </svg>
            @endif
            <img src="{{ asset('assets/mini_logo.png') }}" alt="mini_logo" id="mini-logo">
            <h1>Curhatorium</h1>
        </div>
        
        @php
  $navUser = isset($user) ? $user : (Auth::check() ? Auth::user() : null);
@endphp
        <a href="/profile" style="text-decoration:none;color:inherit;">
            <div id="profile-box">
                <div id="xp-box">
                    <div id="badge-box">
                        <img class="badge-logo" src="{{ asset('assets/kindle.svg') }}" alt="badge">
                        <p class="badge-text">Kindle</p>
                    </div>
                    <p id="xp"></p>
                </div>
                <p class="username">Loading...</p>
                <img src="{{ $navUser && $navUser->profile_picture ? asset('storage/' . $navUser->profile_picture) : asset('assets/profile_pict.svg') }}" alt="pict" id="pict">
            </div>
        </a>
        
        @if($navUser && $navUser->is_admin)
            <a href="/admin" style="text-decoration:none;color:inherit;margin-left: 16px;">
                <div style="display: flex; align-items: center; gap: 8px; padding: 8px 16px; background: #f8c932; border-radius: 8px; color: #222; font-weight: 600; font-size: 0.9rem;">
                    <svg width="16" height="16" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
                        <path d="M12 2L2 7L12 12L22 7L12 2Z" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
                        <path d="M2 17L12 22L22 17" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
                        <path d="M2 12L12 17L22 12" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
                    </svg>
                    Admin
                </div>
            </a>
        @endif
        <!-- Mobile menu button -->
        <button class="mobile-menu-button" aria-label="Go to profile" onclick="window.location.href = '/profile'">
            <span></span>
            <span></span>
            <span></span>
        </button>
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

        // Remove mobile menu toggle and close logic
    </script>