<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Your Safest Place | Curhatorium</title>
    <link rel="stylesheet" href="{{ asset('css/global.css') }}?v={{ time() }}">
    <link rel="stylesheet" href="{{ asset('css/components/navbar.css') }}?v={{ time() }}">
    <link rel="stylesheet" href="{{ asset('css/landing.css') }}?v={{ time() }}">
</head>
<body>
    <!-- Navigation -->
    <nav>
        <div id="logo-box" onclick="window.location.href = '/'">
            <img src="{{ asset('assets/mini_logo.png') }}" alt="mini_logo" id="mini-logo">
            <h1>Curhatorium</h1>
        </div>
        
        <div class="nav-links">
            <a href="#features" class="nav-link">Fitur</a>
            <a href="#testimonials" class="nav-link">Cerita</a>
            <a href="#pricing" class="nav-link">Harga</a>
            <a href="/login" class="nav-btn">Masuk</a>
        </div>
        
        <!-- Mobile menu button -->
        <button class="mobile-menu-button" aria-label="Toggle menu">
            <span></span>
            <span></span>
            <span></span>
        </button>
    </nav>

    <!-- Hero Section -->
    <section class="hero-section">
        <div class="hero-container">
            <div class="fade-in">
                {{-- <div class="hero-badge">
                    ✨ Dipercaya oleh 50.000+ Mahasiswa di Seluruh Dunia
                </div> --}}
                
                <h1 class="hero-title">
                    <span class="highlight">Kesehatan Mental Anda</span><br>
                    Pantas Mendapatkan yang Lebih Baik
                </h1>
                
                <p class="hero-subtitle">
                    Temukan tempat yang aman untuk berbagi, belajar, dan berkembang. Curhatorium hadir sebagai teman terpercaya dalam perjalanan kesehatan mental Anda, dengan dukungan yang Anda butuhkan, kapan pun Anda membutuhkannya.
                </p>
                
                <div class="hero-buttons">
                    <a href="/register" class="btn-primary">
                        Mulai Perjalanan Anda Gratis
                        <span>→</span>
                    </a>
                    <a href="#features" class="btn-secondary">Pelajari Lebih Lanjut</a>
                </div>

                <div class="hero-stats">
                    <div class="stat-item">
                        <div class="stat-number">100+</div>
                        <div class="stat-label">Terbantu</div>
                    </div>
                    <div class="stat-item">
                        <div class="stat-number">24/7</div>
                        <div class="stat-label">Dukungan Tersedia</div>
                    </div>
                    <div class="stat-item">
                        <div class="stat-number">98%</div>
                        <div class="stat-label">Merasa Puas</div>
                    </div>
                    <div class="stat-item">
                        <div class="stat-number">100%</div>
                        <div class="stat-label">Anonim & Aman</div>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- Features Section -->
    <section id="features" class="features">
        <div class="features-container">
            <div class="features-header fade-in">
                <h2 class="features-title">
                    <span class="highlight">Semua yang Anda Butuhkan</span><br>
                    Untuk Kesejahteraan Mental
                </h2>
                <p class="features-subtitle">
                    Dukungan kesehatan mental yang komprehensif, dirancang khusus untuk Anda, menggabungkan teknologi dengan keahlian manusia.
                </p>
            </div>

            <div class="features-grid">
                <div class="feature-card fade-in">
                    <div class="feature-icon">🤖</div>
                    <h3 class="feature-title">Ment-AI Chatbot</h3>
                    <p class="feature-description">
                        Curhat dengan AI 24/7 yang memahami perasaan Anda. Dapatkan dukungan emosional dan saran praktis kapan saja.
                    </p>
                </div>

                <div class="feature-card fade-in">
                    <div class="feature-icon">👨‍⚕️</div>
                    <h3 class="feature-title">Konsultasi Profesional</h3>
                    <p class="feature-description">
                        Sesi Share & Talk dengan profesional kesehatan mental berlisensi melalui chat atau video call yang aman dan rahasia.
                    </p>
                </div>

                <div class="feature-card fade-in">
                    <div class="feature-icon">👥</div>
                    <h3 class="feature-title">Support Group Discussion</h3>
                    <p class="feature-description">
                        Bergabung dalam grup diskusi anonim yang dipandu profesional. Berbagi pengalaman dan dukungan dengan sesama mahasiswa.
                    </p>
                </div>

                <div class="feature-card fade-in">
                    <div class="feature-icon">📊</div>
                    <h3 class="feature-title">Mental Health Tracker</h3>
                    <p class="feature-description">
                        Pantau mood, energi, dan produktivitas harian Anda. Dapatkan analisis dan feedback dari AI untuk kemajuan yang terukur.
                    </p>
                </div>

                <div class="feature-card fade-in">
                    <div class="feature-icon">🃏</div>
                    <h3 class="feature-title">Deep Cards</h3>
                    <p class="feature-description">
                        Kartu panduan untuk memulai percakapan dan refleksi diri secara mendalam.
                    </p>
                </div>

                <div class="feature-card fade-in">
                    <div class="feature-icon">📝</div>
                    <h3 class="feature-title">Mental Health Test</h3>
                    <p class="feature-description">
                        Tes kesehatan mental standar untuk mengenali kondisi dan kebutuhan Anda.
                    </p>
                </div>

                <div class="feature-card fade-in">
                    <div class="feature-icon">🎯</div>
                    <h3 class="feature-title">Missions of the Day</h3>
                    <p class="feature-description">
                        Misi harian untuk membangun kebiasaan sehat dan meningkatkan kesejahteraan mental.
                    </p>
                </div>
            </div>
        </div>
    </section>

    <!-- Testimonials Section -->
    <section id="testimonials" class="testimonials">
        <div class="testimonials-container">
            <div class="testimonials-header fade-in">
                <h2 class="testimonials-title">
                    Cerita Nyata,<br>
                    <span class="highlight">Dampak Nyata</span>
                </h2>
                <p class="features-subtitle">
                    Lihat bagaimana Curhatorium telah mengubah hidup mahasiswa seperti Anda.
                </p>
            </div>

            <div class="testimonials-grid">
                <div class="testimonial-card fade-in">
                    <div class="stars">
                        <span class="star">★</span>
                        <span class="star">★</span>
                        <span class="star">★</span>
                        <span class="star">★</span>
                        <span class="star">★</span>
                    </div>
                    <p class="testimonial-text">
                        "Curhatorium membantu saya melewati semester tersulit. Chatbot AI selalu ada saat saya butuhkan untuk curhat, bahkan jam 3 pagi."
                    </p>
                    <div class="testimonial-author">Sarah M.</div>
                    <div class="testimonial-role">Mahasiswa Universitas Indonesia</div>
                </div>

                <div class="testimonial-card fade-in">
                    <div class="stars">
                        <span class="star">★</span>
                        <span class="star">★</span>
                        <span class="star">★</span>
                        <span class="star">★</span>
                        <span class="star">★</span>
                    </div>
                    <p class="testimonial-text">
                        "Support Group Discussion membuat saya sadar saya tidak sendirian. Sesi Share & Talk dengan profesional sangat membantu."
                    </p>
                    <div class="testimonial-author">Alex K.</div>
                    <div class="testimonial-role">Mahasiswa Pascasarjana ITB</div>
                </div>

                <div class="testimonial-card fade-in">
                    <div class="stars">
                        <span class="star">★</span>
                        <span class="star">★</span>
                        <span class="star">★</span>
                        <span class="star">★</span>
                        <span class="star">★</span>
                    </div>
                    <p class="testimonial-text">
                        "Mental Health Tracker membantu saya memantau mood dan kemajuan. Missions harian membuat saya lebih produktif dan bahagia."
                    </p>
                    <div class="testimonial-author">Maya P.</div>
                    <div class="testimonial-role">Mahasiswa Baru UGM</div>
                </div>
            </div>
        </div>
    </section>

    <!-- Pricing Section -->
    <section id="pricing" class="pricing">
        <div class="pricing-container">
            <div class="pricing-header fade-in">
                <h2 class="pricing-title">Paket & Harga</h2>
                <p class="pricing-subtitle">
                    Pilih paket membership sesuai kebutuhanmu.
                </p>
            </div>

            <div class="pricing-grid">
                @php
                    $membershipMeta = [
                        'Calm Starter' => [
                            'price' => 0,
                            'badge' => 'Gratis',
                            'benefits' => [
                                '(Unlimited) Tes Kesehatan Mental',
                                '(7 Hari) Mood and Productivity Tracker',
                                '(2 Jam) Ment-AI Chatbot',
                                '(7 Hari) Missions of The Day',
                                '(1x) Support Group Discussion',
                                '(30x) Deep Cards',
                            ],
                        ],
                        'Growth Path' => [
                            'price' => 23900,
                            'benefits' => [
                                '(Unlimited) Tes Kesehatan Mental',
                                '(Unlimited) Mood and Productivity Tracker',
                                '(1 Jam) Ment-AI Chatbot',
                                '(Unlimited) Missions of The Day',
                                '(1x) Support Group Discussion',
                                '(Unlimited) Deep Cards',
                                '(1x) Share and Talk via Chat w/ Rangers',
                                'Extra gain XP',
                            ],
                        ],
                        'Blossom' => [
                            'price' => 59900,
                            'benefits' => [
                                '(Unlimited) Tes Kesehatan Mental',
                                '(Unlimited) Mood and Productivity Tracker',
                                '(Unlimited) Ment-AI Chatbot',
                                '(Unlimited) Missions of The Day',
                                '(3x) Support Group Discussion',
                                '(Unlimited) Deep Cards',
                                '(2x) Share and Talk via Chat w/ Rangers',
                                'Extra gain XP',
                            ],
                        ],
                        'Inner Peace' => [
                            'price' => 86900,
                            'benefits' => [
                                '(Unlimited) Tes Kesehatan Mental',
                                '(Unlimited) Mood and Productivity Tracker w/ Extended Report',
                                '(Unlimited) Ment-AI Chatbot',
                                '(Unlimited) Missions of The Day',
                                '(5x) Support Group Discussion',
                                '(Unlimited) Deep Cards',
                                '(3x) Share and Talk via Chat w/ Rangers',
                                'Extra gain XP',
                            ],
                        ],
                        'Harmony' => [
                            'price' => 19900,
                            'benefits' => [
                                '(1x) Support Group Discussion',
                                '(Unlimited 1 bulan) Deep Cards',
                            ],
                        ],
                        'Serenity' => [
                            'price' => 15000,
                            'benefits' => [
                                '(1x) Share and Talk via Chat w/ Rangers',
                                '(Unlimited 1 bulan) Deep Cards',
                            ],
                        ],
                        "Chat with Sanny's Aid" => [
                            'price' => 77900,
                            'benefits' => [
                                '(1x) Share and Talk via Chat w/ Psikolog',
                                '(Unlimited 1 bulan) Mood and Productivity Tracker',
                                '(Unlimited 1 bulan) Deep Cards',
                            ],
                        ],
                        "Meet with Sanny's Aid" => [
                            'price' => 199000,
                            'benefits' => [
                                '(1x) Share and Talk via Video Call w/ Psikiater',
                                '(Unlimited 1 bulan) Mood and Productivity Tracker w/ Extended Report',
                                '(Unlimited 1 bulan) Deep Cards',
                            ],
                        ],
                    ];
                @endphp

                @foreach($membershipMeta as $name => $meta)
                    @php
                        // Add "Terpopuler" badge to Harmony
                        $isHarmony = strtolower($name) === 'harmony';
                        $badge = $meta['badge'] ?? null;
                        if ($isHarmony) {
                            $badge = 'Terpopuler';
                        }
                    @endphp
                    <div class="pricing-card fade-in{{ $isHarmony ? ' pricing-card--highlight' : '' }}">
                        <div class="pricing-card__top"></div>
                        @if($badge)
                            <div class="pricing-card__badge">{{ $badge }}</div>
                        @endif
                        <div class="pricing-title">{{ $name }}</div>
                        <div class="pricing-price">Rp{{ number_format($meta['price'], 0, ',', '.') }}</div>
                        <ul class="pricing-features">
                            @foreach($meta['benefits'] as $benefit)
                                <li>{{ $benefit }}</li>
                            @endforeach
                        </ul>
                    </div>
                @endforeach
            </div>
        </div>
    </section>

    <!-- CTA Section -->
    <section class="cta">
        <div class="cta-container">
            <h2 class="cta-title">
                Siap Mengubah<br>
                Kesehatan Mental Anda?
            </h2>
            <p class="cta-subtitle">
                Bergabunglah dengan ribuan mahasiswa yang sudah memulai perjalanan menuju kesehatan mental yang lebih baik.
            </p>

            <div class="cta-buttons">
                <a href="/register" class="btn-white">
                    Mulai Gratis
                    <span>→</span>
                </a>
            </div>

            <div class="cta-features">
                <div class="cta-feature">
                    <span>✓</span>
                    <span>Gratis selamanya</span>
                </div>
                <div class="cta-feature">
                    <span>🛡️</span>
                    <span>100% Anonim</span>
                </div>
                <div class="cta-feature">
                    <span>🕐</span>
                    <span>Dukungan 24/7</span>
                </div>
            </div>
        </div>
    </section>

    <!-- Footer -->
    @include('components.footer')

    <script>
        // Mobile menu functionality
        document.addEventListener('DOMContentLoaded', function() {
            const mobileMenuButton = document.querySelector('.mobile-menu-button');
            const navLinks = document.querySelector('.nav-links');
            
            if (mobileMenuButton && navLinks) {
                mobileMenuButton.addEventListener('click', function() {
                    navLinks.classList.toggle('active');
                });
                
                // Close menu when clicking outside
                document.addEventListener('click', function(e) {
                    if (!mobileMenuButton.contains(e.target) && !navLinks.contains(e.target)) {
                        navLinks.classList.remove('active');
                    }
                });
            }
        });

        // Fade in animation on scroll
        const observerOptions = {
            threshold: 0.1,
            rootMargin: '0px 0px -50px 0px'
        };

        const observer = new IntersectionObserver((entries) => {
            entries.forEach(entry => {
                if (entry.isIntersecting) {
                    entry.target.classList.add('visible');
                }
            });
        }, observerOptions);

        // Observe all fade-in elements
        document.querySelectorAll('.fade-in').forEach(el => {
            observer.observe(el);
        });

        // Initial fade in for hero
        setTimeout(() => {
            document.querySelector('.hero-section .fade-in').classList.add('visible');
        }, 100);

        // Smooth scrolling for navigation links
        document.querySelectorAll('a[href^="#"]').forEach(anchor => {
            anchor.addEventListener('click', function (e) {
                e.preventDefault();
                const target = document.querySelector(this.getAttribute('href'));
                if (target) {
                    target.scrollIntoView({
                        behavior: 'smooth',
                        block: 'start'
                    });
                }
                // Close mobile menu after clicking a link
                const navLinks = document.querySelector('.nav-links');
                if (navLinks) {
                    navLinks.classList.remove('active');
                }
            });
        });

        // Navigation background on scroll
        window.addEventListener('scroll', () => {
            const nav = document.querySelector('nav');
            if (window.scrollY > 50) {
                nav.style.background = 'rgba(255, 255, 255, 0.98)';
                nav.style.boxShadow = '0 4px 20px rgba(0, 0, 0, 0.1)';
            } else {
                nav.style.background = 'rgba(255, 255, 255, 0.95)';
                nav.style.boxShadow = '0 1px 4px rgba(0, 0, 0, 0.1)';
            }
        });
    </script>
</body>
</html>