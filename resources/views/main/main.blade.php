<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>Curhatorium | Main</title>
    <link rel="stylesheet" href="{{ asset('css/global.css') }}">
    <link rel="stylesheet" href="https://unpkg.com/intro.js/minified/introjs.min.css">
    <style>
        .introjs-tooltip {
            background-color: #ffffff !important;
            color: #333333 !important;
            border-radius: 12px !important;
            font-size: 15px;
            padding: 20px;
            box-shadow: 0 10px 15px -3px rgba(0, 0, 0, 0.1), 0 4px 6px -2px rgba(0, 0, 0, 0.05);
            border: 1px solid rgba(142, 203, 207, 0.1);
        }
        
        .introjs-tooltiptext {
            font-family: 'FigtreeReg', sans-serif;
            color: #333333;
        }
        
        .introjs-helperLayer {
            border: 2px solid #8ecbcf !important;
            box-shadow: 0 0 0 4px rgba(142, 203, 207, 0.3);
        }
        
        .introjs-button {
            background-color: #8ecbcf !important;
            color: #fff !important;
            border: none;
            border-radius: 8px;
            font-weight: 600;
            transition: all 0.3s ease;
            text-shadow: none !important;
        }
        
        .introjs-button:hover {
            background-color: #7ab8bd !important;
            transform: translateY(-1px);
        }
        
        .introjs-tooltip-title {
            font-weight: 600;
            color: #ffcd2d;
            font-family: 'FigtreeBold', sans-serif;
        }
        
        .introjs-progressbar {
            background-color: #ffcd2d !important;
        }
        </style>
</head>
<body>
    @include('components.navbar')
    @if ($errors->has('msg'))
        <div id="toast-error" style="position: fixed; top: 24px; right: 24px; z-index: 9999; background: #f87171; color: white; padding: 16px 24px; border-radius: 8px; box-shadow: 0 2px 8px rgba(0,0,0,0.15); font-size: 1rem;">
            {{ $errors->first('msg') }}
        </div>
        <script>
            setTimeout(function() {
                var toast = document.getElementById('toast-error');
                if (toast) toast.style.display = 'none';
            }, 4000);
        </script>
    @endif
    @include('main.hero')
    @include('main.qotd')
    @include('main.stats')
    @include('main.cta')
    @include('main.xp-redemption')
    @include('main.features')
    @include('main.agenda')
    @include('components.footer')

    <script src="/js/main.js"></script>
    <script src="https://unpkg.com/intro.js/minified/intro.min.js"></script>
    <script>
        document.addEventListener("DOMContentLoaded", function() {
            // Check if user has completed onboarding
            @if(auth()->check() && !auth()->user()->onboarding_completed)
            // Use conditions based on screen width to adjust the steps
            var stepsDesktop = [
                // {
                // element: document.querySelector('.icon-block'),
                // intro: "Selamat datang di Curhatorium!" // Step 1 (no element)
                // },
                {
                    element: document.querySelector('#profile-box'),
                    intro: "Ini statistik kamu. Jumlah XP, Badge, dan Username kamu dapat dilihat di sini. Klik ini untuk ke menu profil"
                },
                {
                    element: document.querySelector('.quote-carousel'),
                    intro: "Ini adalah quote hari ini. Geser untuk melihat quote selanjutnya."
                },
                {
                    element: document.querySelector('.container'),
                    intro: "Ini adalah fitur Mood and Productivity Tracker. Ceritakan hari-mu di sini."
                },
                {
                    element: document.querySelector('.membership-btn'),
                    intro: "Dapatkan akses lebih Curhatorium dengan membeli membership!"
                },
                {
                    element: document.querySelector('.xp-redemption-button'),
                    intro: "Tukarkan XP kamu dengan reward yang kamu inginkan di sini!"
                },
                {
                    element: document.querySelector('#to-mental-test'),
                    intro: "Tes kesehatan mental kamu dengan standar yang teruji"
                },
                {
                    element: document.querySelector('#to-share-talk'),
                    intro: "Cari teman untuk berbagi pengalaman dan mendapatkan dukungan di sini"
                },
                {
                    element: document.querySelector('#to-chatbot'),
                    intro: "Ceritakan harimu dengan AI yang hadir kapan pun di mana pun"
                },
                {
                    element: document.querySelector('#to-missions'),
                    intro: "Selesaikan tugas harian untuk menjaga kesejahteraan psikologis dan mental"
                },
                {
                    element: document.querySelector('#to-sgd'),
                    intro: "Cari kelompok untuk saling berbagi cerita secara anonim"
                },
                {
                    element: document.querySelector('#to-deep-cards'),
                    intro: "Gunakan Deep Card untuk membantumu refleksi diri"
                },
                {
                    element: document.querySelector('.agenda-section'),
                    intro: "Lihat agenda mendatang Curhatorium di sini"
                },
                {
                    element: document.querySelector('.footer-social'),
                    intro: "Kenal lebih dekat dengan Curhatorium di sini"
                },
                {
                    element: document.querySelector('.contact-info'),
                    intro: "Hubungi kami di sini"
                },
            ];

            var stepsMobile = [
                {
                    element: document.querySelector('.mobile-menu-button'),
                    intro: "Klik ini untuk ke menu profil."
                },
                {
                    element: document.querySelector('.quote-carousel'),
                    intro: "Quote hari ini. Swipe untuk melihat lainnya."
                },
                {
                    element: document.querySelector('.container'),
                    intro: "Mood & Productivity Tracker. Ceritakan hari-mu di sini."
                },
                {
                    element: document.querySelector('.membership-btn'),
                    intro: "Akses lebih Curhatorium dengan membership!"
                },
                {
                    element: document.querySelector('.xp-redemption-button'),
                    intro: "Tukarkan XP dengan reward di sini!"
                },
                {
                    element: document.querySelector('#to-mental-test'),
                    intro: "Tes kesehatan mental kamu di sini"
                },
                {
                    element: document.querySelector('#to-share-talk'),
                    intro: "Cari teman untuk berbagi pengalaman"
                },
                {
                    element: document.querySelector('#to-chatbot'),
                    intro: "Ceritakan harimu dengan AI"
                },
                {
                    element: document.querySelector('#to-missions'),
                    intro: "Selesaikan tugas harian untuk kesejahteraan mental"
                },
                {
                    element: document.querySelector('.agenda-section'),
                    intro: "Agenda mendatang Curhatorium"
                },
                {
                    element: document.querySelector('.footer-social'),
                    intro: "Kenal lebih dekat dengan Curhatorium"
                },
                {
                    element: document.querySelector('.contact-info'),
                    intro: "Hubungi kami di sini"
                },
            ];

            var isMobile = window.innerWidth <= 600;

            introJs().setOptions({
                steps: isMobile ? stepsMobile : stepsDesktop,
                showProgress: true,
                showBullets: false,
                nextLabel: 'Selanjutnya',
                prevLabel: 'Sebelumnya',
                doneLabel: 'Selesai',
                exitOnOverlayClick: false,
                exitOnEsc: false,
            }).oncomplete(function() {
                // Mark onboarding as completed when tour is finished
                fetch('/mark-onboarding-completed', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
                    }
                });
            }).onexit(function() {
                // Also mark as completed if user exits the tour
                fetch('/mark-onboarding-completed', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
                    }
                });
            }).start();
            @endif
        });
    </script>
</body>
</html>