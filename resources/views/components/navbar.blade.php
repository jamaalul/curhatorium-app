<link rel="stylesheet" href="{{ asset('css/components/navbar.css') }}">
@vite('resources/css/app.css')

<nav x-data="{ mobileMenuOpen: false }" class="top-0 left-0 z-50 fixed flex items-center justify-between bg-white px-4 md:px-6 w-full h-16 shadow-sm" @keydown.escape="mobileMenuOpen = false">
    <div id="logo-box" onclick="window.location.href = '/dashboard'" class="cursor-pointer flex items-center gap-2">
        @if (!request()->is('dashboard'))
            <svg xmlns="http://www.w3.org/2000/svg" width="36" height="36" fill="currentColor"
                class="bi-arrow-left-short bi" viewBox="0 0 16 16">
                <path fill-rule="evenodd"
                    d="M12 8a.5.5 0 0 1-.5.5H5.707l2.147 2.146a.5.5 0 0 1-.708.708l-3-3a.5.5 0 0 1 0-.708l3-3a.5.5 0 1 1 .708.708L5.707 7.5H11.5a.5.5 0 0 1 .5.5" />
            </svg>
        @endif
        <img src="{{ asset('assets/mini_logo.png') }}" alt="mini_logo" id="mini-logo">
        <h1 class="text-[#222222] text-xl font-medium">Curhatorium</h1>
    </div>

    @php
        $navUser = isset($user) ? $user : (Auth::check() ? Auth::user() : null);
        $membershipName = 'Free';
        $isPremium = false;
        
        if ($navUser) {
            $plan = $navUser->subscription?->membershipPlan;
            if ($plan) {
                $membershipName = $plan->name;
                $isPremium = $plan->price_idr > 0;
            }
        }
    @endphp

    <!-- Desktop Menu -->
    <div class="hidden md:flex items-center gap-4">
        <a href="/membership" class="hover:opacity-80 transition-opacity" style="text-decoration:none;color:inherit;">
            <div class="flex items-center gap-2 px-4 py-2 bg-gray-200 rounded-lg text-gray-900 font-semibold text-[0.9rem]">
                <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5"
                    stroke="currentColor" width="16" height="16">
                    <path stroke-linecap="round" stroke-linejoin="round"
                        d="M16.5 6v.75m0 3v.75m0 3v.75m0 3V18m-9-5.25h5.25M7.5 15h3M3.375 5.25c-.621 0-1.125.504-1.125 1.125v3.026a2.999 2.999 0 0 1 0 5.198v3.026c0 .621.504 1.125 1.125 1.125h17.25c.621 0 1.125-.504 1.125-1.125v-3.026a2.999 2.999 0 0 1 0-5.198V6.375c0-.621-.504-1.125-1.125-1.125H3.375Z" />
                </svg>
                <span class="hidden lg:inline">Membership</span>
                <span class="px-2 py-0.5 rounded text-xs font-bold border shadow-sm whitespace-nowrap {{ $isPremium ? 'bg-gradient-to-r from-amber-100 to-yellow-200 text-yellow-800 border-yellow-300' : 'bg-white text-gray-600 border-gray-200' }}">
                    {{ $membershipName }}
                </span>
            </div>
        </a>

        @if($navUser && $navUser->is_admin)
            <a href="/admin" style="text-decoration:none;color:inherit;">
                <div style="display: flex; align-items: center; gap: 8px; padding: 8px 16px; background: #f8c932; border-radius: 8px; color: #222; font-weight: 600; font-size: 0.9rem;">
                    <svg width="16" height="16" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
                        <path d="M12 2L2 7L12 12L22 7L12 2Z" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" />
                        <path d="M2 17L12 22L22 17" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" />
                        <path d="M2 12L12 17L22 12" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" />
                    </svg>
                    Admin
                </div>
            </a>
        @endif

        <button id="profile-box" onclick="window.location.href = '/profile'" class="hover:shadow-md transition-all duration-200">
            <div id="xp-box">
                <div id="badge-box">
                    <img class="badge-logo" src="{{ asset('assets/kindle.svg') }}" alt="badge">
                    <p class="badge-text">Kindle</p>
                </div>
                <p id="xp"></p>
                <div id="daily-xp-progress" class="daily-xp-circle" title="Daily XP Progress">
                    <svg width="24" height="24" viewBox="0 0 24 24">
                        <circle cx="12" cy="12" r="10" stroke="#e5e7eb" stroke-width="2" fill="none" />
                        <circle id="daily-xp-circle" cx="12" cy="12" r="10" stroke="#10b981" stroke-width="2"
                            fill="none" stroke-dasharray="62.83" stroke-dashoffset="62.83"
                            transform="rotate(-90 12 12)" />
                    </svg>
                    <span id="daily-xp-text">0/0</span>
                </div>
            </div>
            <p class="username">Loading...</p>
            <img src="{{ $navUser && $navUser->profile_picture ? asset('storage/' . $navUser->profile_picture) : asset('assets/profile_pict.svg') }}"
                alt="pict" id="pict">
        </button>
    </div>

    <!-- Mobile menu button & Badge -->
    <div class="md:hidden flex items-center gap-2">
        @if($navUser)
        <a href="/membership" class="flex items-center gap-1 px-2.5 py-1 text-[11px] sm:text-xs font-bold rounded-full border shadow-sm whitespace-nowrap transition-transform active:scale-95 {{ $isPremium ? 'bg-gradient-to-r from-amber-100 to-yellow-200 text-yellow-800 border-yellow-300' : 'bg-white text-gray-600 border-gray-200' }}">
            <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" class="w-3.5 h-3.5">
                <path stroke-linecap="round" stroke-linejoin="round" d="M16.5 6v.75m0 3v.75m0 3v.75m0 3V18m-9-5.25h5.25M7.5 15h3M3.375 5.25c-.621 0-1.125.504-1.125 1.125v3.026a2.999 2.999 0 0 1 0 5.198v3.026c0 .621.504 1.125 1.125 1.125h17.25c.621 0 1.125-.504 1.125-1.125v-3.026a2.999 2.999 0 0 1 0-5.198V6.375c0-.621-.504-1.125-1.125-1.125H3.375Z" />
            </svg>
            {{ $membershipName }}
        </a>
        @endif
        
        <button @click="mobileMenuOpen = !mobileMenuOpen" class="p-2 -mr-2 text-gray-700 hover:text-gray-900 focus:outline-none" aria-label="Toggle menu">
            <svg x-show="!mobileMenuOpen" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="#222222" class="w-8 h-8">
                <path stroke-linecap="round" stroke-linejoin="round" d="M3.75 6A2.25 2.25 0 0 1 6 3.75h2.25A2.25 2.25 0 0 1 10.5 6v2.25a2.25 2.25 0 0 1-2.25 2.25H6a2.25 2.25 0 0 1-2.25-2.25V6ZM3.75 15.75A2.25 2.25 0 0 1 6 13.5h2.25a2.25 2.25 0 0 1 2.25 2.25V18a2.25 2.25 0 0 1-2.25 2.25H6A2.25 2.25 0 0 1 3.75 18v-2.25ZM13.5 6a2.25 2.25 0 0 1 2.25-2.25H18A2.25 2.25 0 0 1 20.25 6v2.25A2.25 2.25 0 0 1 18 10.5h-2.25a2.25 2.25 0 0 1-2.25-2.25V6ZM13.5 15.75a2.25 2.25 0 0 1 2.25-2.25H18a2.25 2.25 0 0 1 2.25 2.25V18A2.25 2.25 0 0 1 18 20.25h-2.25A2.25 2.25 0 0 1 13.5 18v-2.25Z" />
            </svg>
            <svg x-show="mobileMenuOpen" style="display: none;" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="#222222" class="w-8 h-8">
                <path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12" />
            </svg>
        </button>
    </div>

    <!-- Mobile Full Page Menu -->
    <div x-show="mobileMenuOpen" 
         x-transition:enter="transition ease-out duration-200"
         x-transition:enter-start="opacity-0 -translate-y-2"
         x-transition:enter-end="opacity-100 translate-y-0"
         x-transition:leave="transition ease-in duration-150"
         x-transition:leave-start="opacity-100 translate-y-0"
         x-transition:leave-end="opacity-0 -translate-y-2"
         class="md:hidden absolute top-16 left-0 w-full bg-white shadow-xl border-t border-gray-100 flex flex-col p-4 z-50 overflow-y-auto"
         style="display: none; height: calc(100vh - 4rem);"
         @click.away="mobileMenuOpen = false">
        
        <div class="flex flex-col gap-3">
            <!-- Mobile Profile Card -->
            <a href="/profile" class="flex items-center gap-4 p-4 bg-gray-50 rounded-2xl hover:bg-gray-100 transition-colors border border-gray-100 shadow-sm">
                <img src="{{ $navUser && $navUser->profile_picture ? asset('storage/' . $navUser->profile_picture) : asset('assets/profile_pict.svg') }}"
                    alt="pict" class="w-14 h-14 rounded-full object-cover border-2 border-white shadow-sm">
                <div class="flex-1">
                    <p class="font-semibold text-gray-800 text-lg username-mobile">Loading...</p>
                    <div class="flex items-center gap-2 mt-1">
                        <div class="px-2.5 py-1 rounded-full flex items-center gap-1.5 badge-box-mobile shadow-sm" style="background: linear-gradient(to right, #eab308, #f97316);">
                            <img src="{{ asset('assets/kindle.svg') }}" class="w-3.5 h-3.5 badge-logo-mobile" alt="badge">
                            <span class="text-[11px] font-bold text-white badge-text-mobile tracking-wide">Kindle</span>
                        </div>
                        <p class="text-sm text-gray-600 font-bold xp-mobile"></p>
                    </div>
                </div>
                <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 text-gray-400" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7" />
                </svg>
            </a>

            <div class="h-px bg-gray-100 my-2"></div>

            <!-- Mobile Membership Link -->
            <a href="/membership" class="flex items-center justify-between p-4 rounded-xl hover:bg-gray-50 transition-colors text-gray-800 font-semibold text-lg">
                <div class="flex items-center gap-4">
                    <div class="p-2 bg-gray-100 rounded-lg text-gray-600">
                        <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2"
                            stroke="currentColor" class="w-6 h-6">
                            <path stroke-linecap="round" stroke-linejoin="round"
                                d="M16.5 6v.75m0 3v.75m0 3v.75m0 3V18m-9-5.25h5.25M7.5 15h3M3.375 5.25c-.621 0-1.125.504-1.125 1.125v3.026a2.999 2.999 0 0 1 0 5.198v3.026c0 .621.504 1.125 1.125 1.125h17.25c.621 0 1.125-.504 1.125-1.125v-3.026a2.999 2.999 0 0 1 0-5.198V6.375c0-.621-.504-1.125-1.125-1.125H3.375Z" />
                        </svg>
                    </div>
                    Membership
                </div>
                <span class="px-3 py-1 text-sm font-bold rounded-full border shadow-sm whitespace-nowrap {{ $isPremium ? 'bg-gradient-to-r from-amber-100 to-yellow-200 text-yellow-800 border-yellow-300' : 'bg-white text-gray-600 border-gray-200' }}">
                    {{ $membershipName }}
                </span>
            </a>

            <!-- Mobile Admin Link -->
            @if($navUser && $navUser->is_admin)
            <a href="/admin" class="flex items-center gap-4 p-4 rounded-xl hover:bg-yellow-50 transition-colors text-gray-800 font-semibold text-lg">
                <div class="p-2 bg-[#f8c932] bg-opacity-30 rounded-lg text-yellow-700">
                    <svg width="24" height="24" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg" stroke="currentColor" stroke-width="2">
                        <path d="M12 2L2 7L12 12L22 7L12 2Z" stroke-linecap="round" stroke-linejoin="round" />
                        <path d="M2 17L12 22L22 17" stroke-linecap="round" stroke-linejoin="round" />
                        <path d="M2 12L12 17L22 12" stroke-linecap="round" stroke-linejoin="round" />
                    </svg>
                </div>
                Admin Panel
            </a>
            @endif
        </div>
    </div>
</nav>

<!-- Mouseflow Tracking Script -->
<script type="text/javascript">
    window._mfq = window._mfq || [];
    (function () {
        var mf = document.createElement("script");
        mf.type = "text/javascript"; mf.defer = true;
        mf.src = "//cdn.mouseflow.com/projects/c5eb0d0a-6b75-427c-81f3-ee3c9e946eca.js";
        document.getElementsByTagName("head")[0].appendChild(mf);
    })();
</script>

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
                const usernames = document.querySelectorAll(".username, .username-mobile");
                usernames.forEach(el => el.textContent = data.username);

                const xps = document.querySelectorAll("#xp, .xp-mobile");
                xps.forEach(el => el.textContent = data.total_xp + " XP");

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

                const badgeBoxes = document.querySelectorAll("#badge-box, .badge-box-mobile");
                const badgeTexts = document.querySelectorAll(".badge-text, .badge-text-mobile");
                const badgeLogos = document.querySelectorAll(".badge-logo, .badge-logo-mobile");

                if (badgeBoxes.length > 0) {
                    let gradient;
                    let badgeName;
                    let badgeSrc;
                    if (data.total_xp >= 4000) {
                        badgeName = "Nova";
                        badgeSrc = "{{ asset('assets/nova.svg') }}";
                        gradient = 'linear-gradient(to right, #ec4899, #9333ea)';
                    } else if (data.total_xp >= 3000) {
                        badgeName = "Inferno";
                        badgeSrc = "{{ asset('assets/inferno.svg') }}";
                        gradient = 'linear-gradient(to right, #9333ea, #4f46e5)';
                    } else if (data.total_xp >= 2000) {
                        badgeName = "Beacon";
                        badgeSrc = "{{ asset('assets/beacon.svg') }}";
                        gradient = 'linear-gradient(to right, #6366f1, #2563eb)';
                    } else if (data.total_xp >= 1000) {
                        badgeName = "Torch";
                        badgeSrc = "{{ asset('assets/torch.svg') }}";
                        gradient = 'linear-gradient(to right, #f97316, #ef4444)';
                    } else {
                        badgeName = "Kindle";
                        badgeSrc = "{{ asset('assets/kindle.svg') }}";
                        gradient = 'linear-gradient(to right, #eab308, #f97316)';
                    }
                    
                    badgeTexts.forEach(el => el.textContent = badgeName);
                    badgeLogos.forEach(el => el.src = badgeSrc);
                    badgeBoxes.forEach(el => el.style.background = gradient);
                }
            })
            .catch((error) => {
                console.error("Fetch error:", error);
            });
    });
</script>