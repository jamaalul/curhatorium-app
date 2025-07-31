<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0, user-scalable=no, maximum-scale=1.0">
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
            <a href="#about" class="nav-link">Tentang</a>
            <a href="#features" class="nav-link">Fitur</a>
            <a href="#testimonials" class="nav-link">Cerita</a>
            <a href="#pricing" class="nav-link">Harga</a>
            <a href="/login" class="nav-btn">Masuk</a>
        </div>
        
        <!-- Mobile login button -->
        <a href="/login" class="mobile-login-btn">Masuk</a>
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

    <!-- About Us Section -->
    <section id="about" class="about">
        <div class="about-container">
            <div class="about-main-content fade-in">
                <!-- Left Side - Quote and Description -->
                <div class="about-left">
                    <div class="about-quote">
                        <div class="quote-icon">"</div>
                        <p class="quote-text">
                            Curhatorium hadir sebagai ruang aman untuk generasi muda di seluruh Indonesia dalam merawat kesehatan mentalnya. Kami berkomitmen menyediakan layanan yang terjangkau, ramah, dan efektif didukung oleh teknologi inovatif serta pendekatan yang manusiawi. Di Curhatorium, setiap perjalanan emosional layak dihargai, didengar, dan didampingi.
                        </p>
                    </div>
                    <div class="about-decoration">
                        <!-- Abstract wave decoration -->
                    </div>
                </div>

                <!-- Right Side - About Content -->
                <div class="about-right">
                    <div class="about-content-box">   
                        <div class="about-description">
                            <div class="vision-mission-section">
                                <div class="vm-item">
                                    <h4>Visi Kami</h4>
                                    <p>Menjadi ekosistem digital kesehatan mental berbasis komunitas dan gamifikasi terdepan yang inklusif, solutif, dan berkelanjutan untuk Indonesia.</p>
                                </div>
                                <div class="vm-item">
                                    <h4>Misi Kami</h4>
                                    <ul class="mission-list">
                                        <li>Menyediakan ruang aman dan anonim bagi generasi muda untuk berbagi cerita, pengalaman, dan dukungan psikologis berbasis peer-support</li>
                                        <li>Meningkatkan kesadaran dan literasi kesehatan mental melalui edukasi interaktif, fitur self-assesment, dan motivasi harian</li>
                                        <li>Mengembangkan layanan konsultasi terjangkau dengan dukungan mitra profesional, mahasiswa psikologi, dan komunitas</li>
                                    </ul>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Stats Section -->
            <div class="about-stats-container fade-in">
                <div class="values-title">
                    <h3>Nilai Utama Kami</h3>
                    <p>Prinsip yang memandu setiap langkah kami</p>
                </div>
                <div class="stats-box">
                    <div class="values-item">
                        <div class="values-icon">🤝</div>
                        <div class="values-label">Kolaborasi</div>
                    </div>
                    <div class="values-divider"></div>
                    <div class="values-item">
                        <div class="values-icon">💖</div>
                        <div class="values-label">Emosi Tanpa Syarat</div>
                    </div>
                    <div class="values-divider"></div>
                    <div class="values-item">
                        <div class="values-icon">🔒</div>
                        <div class="values-label">Ruang Aman</div>
                    </div>
                    <div class="values-divider"></div>
                    <div class="values-item">
                        <div class="values-icon">🫂</div>
                        <div class="values-label">Inklusivitas</div>
                    </div>
                    <div class="values-divider"></div>
                    <div class="values-item">
                        <div class="values-icon">💪</div>
                        <div class="values-label">Pemberdayaan</div>
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
                    Cerita Nyata,
                    <span class="highlight">Dampak Nyata</span>
                </h2>
                <p class="features-subtitle">
                    Lihat bagaimana Curhatorium telah mengubah hidup orang seperti Anda.
                </p>
            </div>

            <div class="testimonials-carousel-container">
                <div class="testimonials-carousel">
                    @php
                        $testimonials = [
                            [
                                'text' => 'Curhatorium dari dulu pas aku joinnya masih sebagai komunitas adalah tempat yang enak buat berbagi cerita, sih. Kayak kita tuh bisa ngomong masalah kita apa sama orang yang nggak kita kenal, nggak harus takut bakal disebar atau gimana. Selain cerita, kita juga bisa dapet saran penyelesaian, juga bisa denger cerita atau sudut pandang orang lain. Jadi lebih netral aja gitu...',
                                'author' => 'Adam',
                                'role' => 'Mahasiswa Universitas Airlangga',
                            ],
                            [
                                'text' => 'Curhatorium memberikan aku ruang nyaman sebagai introvert buat cerita, belajar saling memahami, dan saling mendukung antar sesama yang sekiranya bisa dibilang punya tantangan emosional yang serupa. Dari diskusi dan pertemuan yang dilakuin, aku merasa lega karena keluh kesah yang selama ini aku pendam akhirnya keluar. Di Curhatorium, aku merasa didengar, dipahami, dan terus termotivasi buat jaga kewarasanku selama aku masih jadi manusia.',
                                'author' => 'Almira',
                                'role' => 'Mahasiswa Universitas Airlangga',
                            ],
                            [
                                'text' => 'Sebagai pengguna lama, Curhatorium jadi tempat pelarian paling nyaman waktu aku stres berat karena masalah keluarga. Di sini aku bisa cerita tanpa takut dinilai, dan respons dari komunitasnya selalu hangat. Perlahan-lahan, aku merasa lebih lega dan bisa hadapin semuanya dengan lebih tenang.',
                                'author' => 'Mutmainnah F.',
                                'role' => 'Mahasiswa Psikologi Universitas Airlangga',
                            ],
                            [
                                'text' => 'Curhatorium jadi titik balik buatku kenal lebih jauh sama diri sendiri. Awalnya cuma ikut iseng, tapi ternyata cerita bareng orang lain bikin aku sadar: aku nggak sendirian. Tempat ini bukan cuma soal curhat, tapi tentang pulih pelan-pelan..',
                                'author' => 'Basmah',
                                'role' => 'Mahasiswa Gizi Universitas Negeri Surabaya',
                            ],
                            [
                                'text' => 'Sebagai mahasiswa yang cukup skeptis soal layanan kesehatan mental online, jujur aku nggak berekspektasi banyak. Tapi Curhatorium berhasil nunjukin pendekatan yang aman, sistematis, dan nyaman buat kami yang mungkin belum siap ke profesional.',
                                'author' => 'Abdul Aziz',
                                'role' => 'Mahasiswa Teknik Universitas Negeri Surabaya',
                            ],
                            [
                                'text' => 'Saya tipe orang yang sering mikir sendiri, dan susah banget cerita. Di Curhatorium, saya ketemu ruang yang nggak maksa, tapi justru ngebantu saya pelan-pelan terbuka. Nggak ada tuntutan harus \'baik-baik aja\'. Buatku, itu yang terpenting.',
                                'author' => 'Ali Ridho',
                                'role' => 'Mahasiswa Teknik Universitas Negeri Surabaya',
                            ],
                            [
                                'text' => 'Dari segi tampilan, platform Curhatorium clean banget, nggak ribet, dan gampang dipakai. Semua fitur gampang diakses, bahkan buat orang yang baru pertama kali buka. Warnanya juga bikin tenang, nggak \'ngegas\' kayak platform lain. Buatku, ini penting karena pas lagi down, aku butuh sesuatu yang sederhana, tapi tetap nyaman.',
                                'author' => 'Rohim',
                                'role' => 'HoD Campaign & Branding Digimarly',
                            ],
                            [
                                'text' => 'Komunitas ini sangat nyaman dan supportif untuk saya yang sulit berbaur, disini saya bisa berbagi cerita tanpa dihakimi dan merasa lebih diterima. Cocok banget buat yang butuh ruang seperti saya.',
                                'author' => 'Usaratus Sakinah',
                                'role' => 'Mahasiswa Universitas Airlangga',
                            ],
                        ];
                        // Number of times to repeat the testimonials for marquee effect
                        $repeat = 4;
                    @endphp
                    @for($i = 0; $i < $repeat; $i++)
                        @foreach($testimonials as $testimonial)
                            <div class="testimonial-card fade-in">
                                <div class="stars">
                                    <span class="star">★</span>
                                    <span class="star">★</span>
                                    <span class="star">★</span>
                                    <span class="star">★</span>
                                    <span class="star">★</span>
                                </div>
                                <p class="testimonial-text">
                                    "{{ $testimonial['text'] }}"
                                </p>
                                <div class="testimonial-author">{{ $testimonial['author'] }}</div>
                                <div class="testimonial-role">{{ $testimonial['role'] }}</div>
                            </div>
                        @endforeach
                    @endfor
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

            <div class="landing-pricing-grid">
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
                                '(1x) Share and Talk via Video Call w/ Psikolog',
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
                    <div class="{{ 'landing-pricing-card' . ($isHarmony ? ' landing-pricing-card--highlight' : '') }} fade-in">
                        <div class="landing-pricing-card__top"></div>
                        @if($badge)
                            <div class="landing-pricing-card__badge">{{ $badge }}</div>
                        @endif
                        <div class="landing-pricing-title">{{ $name }}</div>
                        <div class="landing-pricing-price">
                            Rp{{ number_format($meta['price'], 0, ',', '.') }}
                            @if(isset($meta['period']))
                                <span class="period">/{{ $meta['period'] }}</span>
                            @endif
                        </div>
                        <ul class="landing-pricing-features">
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
                Ratusan Mahasiswa Telah Memulai<br>
                Perjalanan Kesejahteraan Mental
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
        // Landing page functionality
        document.addEventListener('DOMContentLoaded', function() {
            // Smooth scrolling and other functionality
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

        // Testimonials Marquee - No additional JavaScript needed, CSS handles the animation
    </script>
</body>
</html>