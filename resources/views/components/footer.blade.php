<style>
    :root {
        --primary-color: #8ecbcf;
        --primary-dark: #7ab8bd;
        --text-primary: #333333;
        --text-secondary: #595959;
        --bg-dark: #1f2937;
        --border-color: #e5e7eb;
        --shadow-md: 0 4px 6px -1px rgba(0, 0, 0, 0.1), 0 2px 4px -1px rgba(0, 0, 0, 0.06);
        --radius-md: 0.5rem;
        --transition: all 0.3s ease;
    }

    .footer {
        background: var(--bg-dark);
        color: white;
        width: 100%;
        max-width: 100vw;
        overflow: visible;
        box-sizing: border-box;
    }

    .footer-container {
        max-width: 1200px;
        margin: 0 auto;
        padding: 0 1.5rem;
        display: flex;
        width: 100%;
        box-sizing: border-box;
        overflow: visible;
    }

    .footer-main {
        padding: 2rem 0 1rem;
    }

    .footer-grid {
        display: grid;
        grid-template-columns: 1fr 1fr;
        gap: 3rem;
        margin-bottom: 1.5rem;
    }

    .footer-logo {
        display: flex;
        align-items: center;
        gap: 0.75rem;
        margin-bottom: 1rem;
    }

    .footer-logo img {
        width: 40px;
        height: 40px;
        border-radius: 8px;
    }

    .footer-logo h3 {
        font-size: 1.5rem;
        font-weight: 700;
        color: var(--primary-color);
    }

    .footer-description {
        color: #d1d5db;
        margin-bottom: 1.5rem;
        line-height: 1.6;
        font-size: 1rem;
    }

    .footer-social {
        display: flex;
        gap: 1rem;
        margin-bottom: 1.5rem;
        padding: 1rem 0;
        overflow: visible;
    }

    .social-link {
        display: flex;
        align-items: center;
        justify-content: center;
        width: 40px;
        height: 40px;
        background: rgba(255, 255, 255, 0.1);
        border-radius: 50%;
        color: white;
        text-decoration: none;
        transition: var(--transition);
        position: relative;
        z-index: 1;
    }

    .social-link:hover {
        background: var(--primary-color);
        transform: translateY(-2px);
        box-shadow: 0 4px 12px rgba(142, 203, 207, 0.3);
    }

    .footer-section h4 {
        font-size: 1.2rem;
        font-weight: 600;
        margin-bottom: 1rem;
        color: white;
    }

    .footer-links {
        list-style: none;
        padding: 0;
        display: grid;
        grid-template-columns: 1fr 1fr;
        gap: 0.5rem 2rem;
    }

    .footer-links li {
        margin-bottom: 0.5rem;
    }

    .footer-links a {
        color: #d1d5db;
        text-decoration: none;
        transition: var(--transition);
        font-size: 0.95rem;
        display: block;
        padding: 0.25rem 0;
    }

    .footer-links a:hover {
        color: var(--primary-color);
        padding-left: 0.25rem;
    }

    .contact-info {
        margin-top: 1rem;
    }

    .contact-item {
        display: flex;
        align-items: center;
        gap: 0.75rem;
        margin-bottom: 0.5rem;
        color: #d1d5db;
        font-size: 0.95rem;
    }

    .contact-icon {
        width: 16px;
        height: 16px;
        color: var(--primary-color);
        flex-shrink: 0;
    }

    .footer-bottom {
        border-top: 1px solid rgba(255, 255, 255, 0.1);
        padding: 1rem 0;
        display: flex;
        justify-content: space-between;
        align-items: center;
        flex-wrap: wrap;
        gap: 1rem;
    }

    .footer-copyright {
        color: #9ca3af;
        font-size: 0.875rem;
    }

    .footer-legal {
        display: flex;
        gap: 1.5rem;
        margin-left: auto;
    }

    .footer-legal a {
        color: #9ca3af;
        text-decoration: none;
        font-size: 0.875rem;
        transition: var(--transition);
    }

    .footer-legal a:hover {
        color: var(--primary-color);
    }

    .ftr {
        padding-bottom: 10px;
    }

    @media (max-width: 768px) {
        .footer-grid {
            grid-template-columns: 1fr;
            gap: 2rem;
        }
        
        .footer-links {
            grid-template-columns: 1fr;
            gap: 0.5rem;
        }
    }

    @media (max-width: 600px) {
        .footer-main {
            padding: 1.5rem 0 1rem;
        }
        .footer-container {
            padding: 0 1rem;
        }
        .footer-logo {
            justify-content: center;
        }
        .footer-social {
            justify-content: center;
        }
        .footer-bottom {
            flex-direction: column;
            text-align: center;
        }
        .footer-legal {
            justify-content: center;
        }
    }
</style>
</head>
<footer class="footer">
    <div class="footer-main">
        <div class="footer-container">
            <div class="footer-grid">
                <!-- Company Section -->
                <div>
                    <div class="footer-logo">
                        <img src="{{ asset('assets/mini_logo.png') }}" alt="Curhatorium Logo">
                        <h3>curhatorium</h3>
                    </div>
                    <p class="footer-description">
                        Mulai perjalanan kesehatan mental Anda dengan Curhatorium. Daftar gratis dan temukan dukungan yang Anda butuhkan.
                    </p>
                    <div class="footer-social">
                        <a href="https://instagram.com/curhatorium_" class="social-link" aria-label="Instagram">
                            <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="currentColor" class="bi bi-instagram" viewBox="0 0 16 16">
                                <path d="M8 0C5.829 0 5.556.01 4.703.048 3.85.088 3.269.222 2.76.42a3.9 3.9 0 0 0-1.417.923A3.9 3.9 0 0 0 .42 2.76C.222 3.268.087 3.85.048 4.7.01 5.555 0 5.827 0 8.001c0 2.172.01 2.444.048 3.297.04.852.174 1.433.372 1.942.205.526.478.972.923 1.417.444.445.89.719 1.416.923.51.198 1.09.333 1.942.372C5.555 15.99 5.827 16 8 16s2.444-.01 3.298-.048c.851-.04 1.434-.174 1.943-.372a3.9 3.9 0 0 0 1.416-.923c.445-.445.718-.891.923-1.417.197-.509.332-1.09.372-1.942C15.99 10.445 16 10.173 16 8s-.01-2.445-.048-3.299c-.04-.851-.175-1.433-.372-1.941a3.9 3.9 0 0 0-.923-1.417A3.9 3.9 0 0 0 13.24.42c-.51-.198-1.092-.333-1.943-.372C10.443.01 10.172 0 7.998 0zm-.717 1.442h.718c2.136 0 2.389.007 3.232.046.78.035 1.204.166 1.486.275.373.145.64.319.92.599s.453.546.598.92c.11.281.24.705.275 1.485.039.843.047 1.096.047 3.231s-.008 2.389-.047 3.232c-.035.78-.166 1.203-.275 1.485a2.5 2.5 0 0 1-.599.919c-.28.28-.546.453-.92.598-.28.11-.704.24-1.485.276-.843.038-1.096.047-3.232.047s-2.39-.009-3.233-.047c-.78-.036-1.203-.166-1.485-.276a2.5 2.5 0 0 1-.92-.598 2.5 2.5 0 0 1-.6-.92c-.109-.281-.24-.705-.275-1.485-.038-.843-.046-1.096-.046-3.233s.008-2.388.046-3.231c.036-.78.166-1.204.276-1.486.145-.373.319-.64.599-.92s.546-.453.92-.598c.282-.11.705-.24 1.485-.276.738-.034 1.024-.044 2.515-.045zm4.988 1.328a.96.96 0 1 0 0 1.92.96.96 0 0 0 0-1.92m-4.27 1.122a4.109 4.109 0 1 0 0 8.217 4.109 4.109 0 0 0 0-8.217m0 1.441a2.667 2.667 0 1 1 0 5.334 2.667 2.667 0 0 1 0-5.334"/>
                            </svg>
                        </a>
                        <a href="https://tiktok.com/curhatorium" class="social-link" aria-label="TikTok">
                            <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="currentColor" class="bi bi-tiktok" viewBox="0 0 16 16">
                                <path d="M9 0h1.98c.144.715.54 1.617 1.235 2.512C12.895 3.389 13.797 4 15 4v2c-1.753 0-3.07-.814-4-1.829V11a5 5 0 1 1-5-5v2a3 3 0 1 0 3 3z"/>
                            </svg>
                        </a>
                    </div>
                    <div class="contact-info">
                        <div class="contact-item">
                            <svg class="contact-icon" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 8l7.89 4.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"/>
                            </svg>
                            <span>hello@curhatorium.com</span>
                        </div>
                        <div class="contact-item">
                            <svg class="contact-icon" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/>
                            </svg>
                            <a href="https://bit.ly/PengaduanCurhatorium" target="_blank" rel="noopener noreferrer" style="color: #d1d5db; text-decoration: none; transition: var(--transition);">Laporkan Masalah</a>
                        </div>
                    </div>
                </div>
                
                <!-- Quick Links -->
                <div class="footer-section">
                    @if(request()->is('/'))
                            <h4 class="ftr">Tautan Cepat</h4>
                            <ul class="footer-links">
                                <!-- Unauthenticated menu for landing page -->
                                <li><a href="#about">Tentang Kami</a></li>
                                <li><a href="#features">Fitur</a></li>
                                <li><a href="#testimonials">Cerita</a></li>
                                <li><a href="#pricing">Harga</a></li>
                                <li><a href="/register">Daftar</a></li>
                                <li><a href="/login">Masuk</a></li>
                            </ul>
                        @else
                            <h4 class="ftr">Fitur & Layanan</h4>
                            <ul class="footer-links">
                                <!-- Authenticated menu for other pages -->
                                <li><a href="/tracker">Mental and Productivity Tracker</a></li>
                                <li><a href="/mental-health-test">Mental Health Test</a></li>
                                <li><a href="/share-and-talk">Share and Talk</a></li>
                                <li><a href="/mental-support-chatbot">Ment-AI</a></li>
                                <li><a href="/missions-of-the-day">Missions of The Day</a></li>
                                <li><a href="/support-group-discussion">Support Group Discussion</a></li>
                                <li><a href="/deep-cards">Deep Cards</a></li>
                            </ul>
                        @endif
                </div>

            </div>
        </div>
    </div>

    <div class="footer-bottom">
        <div class="footer-container">
            <div class="footer-copyright">
                <p>&copy; 2025 Curhatorium. All rights reserved.</p>
            </div>
            <div class="footer-legal">
                <a href="/privacy-policy">Kebijakan Privasi</a>
                <a href="/terms-and-conditions">Syarat dan Ketentuan</a>
                @if(request()->is('dashboard'))
                    <button id="replay-onboarding-btn" style="background: none; border: none; color: #9ca3af; text-decoration: none; font-size: 0.875rem; cursor: pointer; transition: var(--transition); padding: 0; font-family: inherit;" title="Replay Tutorial">
                        Tutorial
                    </button>
                @endif
            </div>
        </div>
    </div>
</footer>