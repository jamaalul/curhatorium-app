<link rel="stylesheet" href="{{ asset('css/components/navbar.css') }}">
@vite('resources/css/app.css')

    <nav class="w-full h-16 bg-white flex items-center px-4 fixed top-0 left-0 z-50 gap-6">
        <div id="logo-box" onclick="window.location.href = '/dashboard'" class="mr-auto">
            @if (!request()->is('dashboard'))
                <svg xmlns="http://www.w3.org/2000/svg" width="36" height="36" fill="currentColor" class="bi bi-arrow-left-short" viewBox="0 0 16 16">
                    <path fill-rule="evenodd" d="M12 8a.5.5 0 0 1-.5.5H5.707l2.147 2.146a.5.5 0 0 1-.708.708l-3-3a.5.5 0 0 1 0-.708l3-3a.5.5 0 1 1 .708.708L5.707 7.5H11.5a.5.5 0 0 1 .5.5"/>
                </svg>
            @endif
            <img src="{{ asset('assets/mini_logo.png') }}" alt="mini_logo" id="mini-logo">
            <h1 class="text-[#222222]">Curhatorium</h1>
        </div>
        
        @php
          $navUser = isset($user) ? $user : (Auth::check() ? Auth::user() : null);
        @endphp
        <div style="text-decoration:none;color:inherit;" class="flex items-center gap-4">
            <button class="bg-none border-0 flex justify-center items-center gap-2 text-[#222222] hover:text-[#48a6a6] transition-all duration-200" onclick="window.location.href = '/membership'">
                <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="size-8 md:size-6">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M2.25 3h1.386c.51 0 .955.343 1.087.835l.383 1.437M7.5 14.25a3 3 0 0 0-3 3h15.75m-12.75-3h11.218c1.121-2.3 2.1-4.684 2.924-7.138a60.114 60.114 0 0 0-16.536-1.84M7.5 14.25 5.106 5.272M6 20.25a.75.75 0 1 1-1.5 0 .75.75 0 0 1 1.5 0Zm12.75 0a.75.75 0 1 1-1.5 0 .75.75 0 0 1 1.5 0Z" />
                </svg>
                <span class="hidden md:inline">Membership</span>
            </button>
            <button id="profile-box" onclick="window.location.href = '/profile'" class="hover:shadow-md transition-all duration-200">
                <div id="xp-box">
                    <div id="badge-box">
                        <img class="badge-logo" src="{{ asset('assets/kindle.svg') }}" alt="badge">
                        <p class="badge-text">Kindle</p>
                    </div>
                    <p id="xp"></p>
                    <!-- Daily XP Progress Circle -->
                    <div id="daily-xp-progress" class="daily-xp-circle" title="Daily XP Progress">
                        <svg width="24" height="24" viewBox="0 0 24 24">
                            <circle cx="12" cy="12" r="10" stroke="#e5e7eb" stroke-width="2" fill="none"/>
                            <circle id="daily-xp-circle" cx="12" cy="12" r="10" stroke="#10b981" stroke-width="2" fill="none" 
                                    stroke-dasharray="62.83" stroke-dashoffset="62.83" transform="rotate(-90 12 12)"/>
                        </svg>
                        <span id="daily-xp-text">0/0</span>
                    </div>
                </div>
                <p class="username">Loading...</p>
                <img src="{{ $navUser && $navUser->profile_picture ? asset('storage/' . $navUser->profile_picture) : asset('assets/profile_pict.svg') }}" alt="pict" id="pict">
            </button>
        </div>
        
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
            <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="#222222" class="size-8">
                <path stroke-linecap="round" stroke-linejoin="round" d="M3.75 6A2.25 2.25 0 0 1 6 3.75h2.25A2.25 2.25 0 0 1 10.5 6v2.25a2.25 2.25 0 0 1-2.25 2.25H6a2.25 2.25 0 0 1-2.25-2.25V6ZM3.75 15.75A2.25 2.25 0 0 1 6 13.5h2.25a2.25 2.25 0 0 1 2.25 2.25V18a2.25 2.25 0 0 1-2.25 2.25H6A2.25 2.25 0 0 1 3.75 18v-2.25ZM13.5 6a2.25 2.25 0 0 1 2.25-2.25H18A2.25 2.25 0 0 1 20.25 6v2.25A2.25 2.25 0 0 1 18 10.5h-2.25a2.25 2.25 0 0 1-2.25-2.25V6ZM13.5 15.75a2.25 2.25 0 0 1 2.25-2.25H18a2.25 2.25 0 0 1 2.25 2.25V18A2.25 2.25 0 0 1 18 20.25h-2.25A2.25 2.25 0 0 1 13.5 18v-2.25Z" />
            </svg>
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

                    // Update daily XP progress
                    const dailyXpCircle = document.querySelector("#daily-xp-circle");
                    const dailyXpText = document.querySelector("#daily-xp-text");
                    
                    if (dailyXpCircle && dailyXpText && data.daily_xp_summary) {
                        const { daily_xp_gained, max_daily_xp, daily_progress_percentage } = data.daily_xp_summary;
                        const progress = Math.min(100, daily_progress_percentage);
                        const circumference = 62.83; // 2 * π * radius (10)
                        const offset = circumference - (progress / 100) * circumference;
                        
                        dailyXpCircle.style.strokeDashoffset = offset;
                        dailyXpText.textContent = `${daily_xp_gained}/${max_daily_xp}`;
                        
                        // Change color based on progress
                        if (progress >= 90) {
                            dailyXpCircle.style.stroke = "#ef4444"; // Red when near limit
                        } else if (progress >= 70) {
                            dailyXpCircle.style.stroke = "#f59e0b"; // Orange when getting close
                        } else {
                            dailyXpCircle.style.stroke = "#10b981"; // Green for normal progress
                        }
                    }

                    const badgeBox = document.querySelector("#badge-box");
                    const badgeText = document.querySelector(".badge-text");
                    const badgeLogo = document.querySelector(".badge-logo");

                    if (badgeBox && badgeText && badgeLogo) {
                        let gradient;
                        if (data.total_xp >= 4000) {
                            badgeText.textContent = "Nova";
                            badgeLogo.src = "{{ asset('assets/nova.svg') }}";
                            gradient = 'linear-gradient(to right, #ec4899, #9333ea)';
                        } else if (data.total_xp >= 3000) {
                            badgeText.textContent = "Inferno";
                            badgeLogo.src = "{{ asset('assets/inferno.svg') }}";
                            gradient = 'linear-gradient(to right, #9333ea, #4f46e5)';
                        } else if (data.total_xp >= 2000) {
                            badgeText.textContent = "Beacon";
                            badgeLogo.src = "{{ asset('assets/beacon.svg') }}";
                            gradient = 'linear-gradient(to right, #6366f1, #2563eb)';
                        } else if (data.total_xp >= 1000) {
                            badgeText.textContent = "Torch";
                            badgeLogo.src = "{{ asset('assets/torch.svg') }}";
                            gradient = 'linear-gradient(to right, #f97316, #ef4444)';
                        } else {
                            badgeText.textContent = "Kindle";
                            badgeLogo.src = "{{ asset('assets/kindle.svg') }}";
                            gradient = 'linear-gradient(to right, #eab308, #f97316)';
                        }
                        badgeBox.style.background = gradient;
                    }
                })
                .catch((error) => {
                    console.error("Fetch error:", error);
                });
        });

        // Remove mobile menu toggle and close logic
    </script>