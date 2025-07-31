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
                {
                    element: document.querySelector('#profile-box'),
                    intro: "Semua perjalananmu terekam di sini: XP, badge, username, dan jumlah tiket yang kamu miliki. Beberapa fitur Curhatorium memerlukan tiket untuk diakses, jadi pastikan kamu memantau stok tiketmu melalui halaman profil dengan mengklik ini."
                },
                {
                    element: document.querySelector('.quote-carousel'),
                    intro: "Kumpulan kutipan harian yang bisa kamu geser. Selalu ada kalimat yang tepat untuk menemani harimu."
                },
                {
                    element: document.querySelector('.container'),
                    intro: "Ruang untuk mencurahkan suasana hati dan menilai produktivitasmu hari ini. Satu langkah kecil untuk mengenal diri."
                },
                {
                    element: document.querySelector('.membership-btn'),
                    intro: "Tombol menuju pengalaman premium—akses fitur tambahan untuk dukungan mental yang lebih menyeluruh."
                },
                {
                    element: document.querySelector('.xp-redemption-button'),
                    intro: "Setiap pencapaian punya nilai. Gunakan XP yang kamu kumpulkan untuk reward yang kamu pilih sendiri."
                },
                {
                    element: document.querySelector('.grid-container'),
                    intro: "Akses fitur-fitur Curhatorium lainnya di sini."
                },
                {
                    element: document.querySelector('.agenda-section'),
                    intro: "Jadwal kegiatan mendatang dari Curhatorium, agar kamu selalu terinformasi dan bisa ikut ambil bagian."
                },
                {
                    element: document.querySelector('.footer-social'),
                    intro: "Jalan pintas untuk mengenal Curhatorium lebih dekat lewat media sosial resmi kami."
                },
                {
                    element: document.querySelector('.contact-info'),
                    intro: "Butuh bantuan atau ingin menyampaikan sesuatu? Kami bisa dihubungi dengan mudah dari sini."
                },
            ];

            var stepsMobile = [
                {
                    element: document.querySelector('.mobile-menu-button'),
                    intro: "Semua detailmu terekam di sini: XP, badge, username, dan jumlah tiket yang kamu miliki. Beberapa fitur Curhatorium memerlukan tiket untuk diakses, jadi pastikan kamu memantau stok tiketmu melalui halaman profil dengan mengklik ini."
                },
                {
                    element: document.querySelector('.quote-carousel'),
                    intro: "Kutipan inspirasional yang bisa kamu geser setiap hari. Selalu ada kata yang tepat untukmu."
                },
                {
                    element: document.querySelector('.container'),
                    intro: "Tempat mencatat perasaan dan produktivitas harian—untuk dirimu yang lebih sadar dan seimbang."
                },
                {
                    element: document.querySelector('.membership-btn'),
                    intro: "Tingkatkan pengalamanmu dengan keanggotaan premium dan nikmati fitur lebih lengkap."
                },
                {
                    element: document.querySelector('.xp-redemption-button'),
                    intro: "Reward menanti! Tukarkan XP yang kamu kumpulkan dengan pilihan hadiah menarik."
                },
                {
                    element: document.querySelector('.grid-container'),
                    intro: "Akses fitur-fitur Curhatorium lainnya di sini."
                },
                {
                    element: document.querySelector('.agenda-section'),
                    intro: "Kegiatan dan event terbaru yang bisa kamu ikuti untuk terus tumbuh bersama komunitas."
                },
                {
                    element: document.querySelector('.footer-social'),
                    intro: "Ikuti kami di media sosial untuk terhubung lebih dekat dan dapatkan konten eksklusif."
                },
                {
                    element: document.querySelector('.contact-info'),
                    intro: "Kanal untuk menghubungi kami langsung jika kamu membutuhkan bantuan atau ingin berbagi."
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
            
            // Replay onboarding functionality
            const replayButton = document.getElementById('replay-onboarding-btn');
            if (replayButton) {
                replayButton.addEventListener('click', function() {
                    // Reset onboarding status first
                    fetch('/reset-onboarding', {
                        method: 'POST',
                        headers: {
                            'Content-Type': 'application/json',
                            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
                        }
                    }).then(() => {
                        // Start the onboarding tour
                        startOnboardingTour();
                    });
                });
            }
            
            // Function to start onboarding tour
            function startOnboardingTour() {
                var isMobile = window.innerWidth <= 600;
                var steps = isMobile ? stepsMobile : stepsDesktop;
                
                introJs().setOptions({
                    steps: steps,
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
            }
        });
    </script>
</body>
</html>