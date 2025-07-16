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
        margin-top: 2rem;
    }

    .footer-container {
        max-width: 1200px;
        margin: 0 auto;
        padding: 0 1.5rem;
        display: flex;
        width: 100%;
    }

    .footer-main {
        padding: 2rem 0 1rem;
    }

    .footer-grid {
        display: grid;
        grid-template-columns: 2fr 1fr 1fr;
        gap: 2rem;
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
        margin-bottom: 1rem;
        line-height: 1.6;
        font-size: 1rem;
    }

    .footer-social {
        display: flex;
        gap: 1rem;
        margin-top: 0.5rem;
    }

    .social-link {
        display: flex;
        align-items: center;
        justify-content: center;
        width: 36px;
        height: 36px;
        background: rgba(255, 255, 255, 0.1);
        border-radius: 50%;
        color: white;
        text-decoration: none;
        transition: var(--transition);
    }

    .social-link:hover {
        background: var(--primary-color);
        transform: translateY(-2px);
    }

    .footer-section h4 {
        font-size: 1.1rem;
        font-weight: 600;
        margin-bottom: 0.75rem;
        color: white;
    }

    .footer-links {
        list-style: none;
        padding: 0;
    }

    .footer-links li {
        margin-bottom: 0.5rem;
    }

    .footer-links a {
        color: #d1d5db;
        text-decoration: none;
        transition: var(--transition);
        font-size: 0.95rem;
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

    @media (max-width: 900px) {
        .footer-grid {
            grid-template-columns: 1fr 1fr;
            gap: 1.5rem;
        }
    }

    @media (max-width: 600px) {
        .footer-main {
            padding: 1.5rem 0 1rem;
        }
        .footer-container {
            padding: 0 1rem;
        }
        .footer-grid {
            grid-template-columns: 1fr;
            gap: 1rem;
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
                        Your trusted companion for mental health support and community connection.
                    </p>
                    <div class="footer-social">
                        <a href="#" class="social-link" aria-label="Facebook">
                            <svg width="20" height="20" fill="currentColor" viewBox="0 0 24 24">
                                <path d="M24 12.073c0-6.627-5.373-12-12-12s-12 5.373-12 12c0 5.99 4.388 10.954 10.125 11.854v-8.385H7.078v-3.47h3.047V9.43c0-3.007 1.792-4.669 4.533-4.669 1.312 0 2.686.235 2.686.235v2.953H15.83c-1.491 0-1.956.925-1.956 1.874v2.25h3.328l-.532 3.47h-2.796v8.385C19.612 23.027 24 18.062 24 12.073z"/>
                            </svg>
                        </a>
                        <a href="#" class="social-link" aria-label="Twitter">
                            <svg width="20" height="20" fill="currentColor" viewBox="0 0 24 24">
                                <path d="M23.953 4.57a10 10 0 01-2.825.775 4.958 4.958 0 002.163-2.723c-.951.555-2.005.959-3.127 1.184a4.92 4.92 0 00-8.384 4.482C7.69 8.095 4.067 6.13 1.64 3.162a4.822 4.822 0 00-.666 2.475c0 1.71.87 3.213 2.188 4.096a4.904 4.904 0 01-2.228-.616v.06a4.923 4.923 0 003.946 4.827 4.996 4.996 0 01-2.212.085 4.936 4.936 0 004.604 3.417 9.867 9.867 0 01-6.102 2.105c-.39 0-.779-.023-1.17-.067a13.995 13.995 0 007.557 2.209c9.053 0 13.998-7.496 13.998-13.985 0-.21 0-.42-.015-.63A9.935 9.935 0 0024 4.59z"/>
                            </svg>
                        </a>
                        <a href="#" class="social-link" aria-label="Instagram">
                            <svg width="20" height="20" fill="currentColor" viewBox="0 0 24 24">
                                <path d="M12.017 0C5.396 0 .029 5.367.029 11.987c0 6.62 5.367 11.987 11.988 11.987 6.62 0 11.987-5.367 11.987-11.987C24.014 5.367 18.637.001 12.017.001zM8.449 16.988c-1.297 0-2.448-.49-3.323-1.297C4.198 14.895 3.708 13.744 3.708 12.447s.49-2.448 1.418-3.323c.875-.875 2.026-1.297 3.323-1.297s2.448.422 3.323 1.297c.928.875 1.418 2.026 1.418 3.323s-.49 2.448-1.418 3.244c-.875.807-2.026 1.297-3.323 1.297zm7.83-9.405c-.49 0-.928-.422-.928-.928 0-.49.438-.928.928-.928.49 0 .928.438.928.928 0 .506-.438.928-.928.928zm-4.262 1.364c-1.297 0-2.346 1.049-2.346 2.346s1.049 2.346 2.346 2.346 2.346-1.049 2.346-2.346-1.049-2.346-2.346-2.346z"/>
                            </svg>
                        </a>
                    </div>
                    <div class="contact-info" style="margin-top:1rem;">
                        <div class="contact-item">
                            <svg class="contact-icon" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 8l7.89 4.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"/>
                            </svg>
                            <span>support@curhatorium.com</span>
                        </div>
                    </div>
                </div>
                
                <!-- Resources -->
                <div class="footer-section">
                    <h4>Resources</h4>
                    <ul class="footer-links">
                        <li><a href="#">Help Center</a></li>
                        <li><a href="#">Blog</a></li>
                        <li><a href="#">Guides</a></li>
                    </ul>
                </div>
                <!-- Services -->
                <div class="footer-section">
                    <h4>Services</h4>
                    <ul class="footer-links">
                        <li><a href="#">Mental Health Test</a></li>
                        <li><a href="#">Ment-AI</a></li>
                        <li><a href="#">Support Group Discussion</a></li>
                        <li><a href="#">Share and Talk</a></li>
                        <li><a href="#">Missions of The Day</a></li>
                        <li><a href="#">Deep Cards</a></li>
                    </ul>
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
                <a href="#">Privacy Policy</a>
                <a href="#">Terms of Service</a>
            </div>
        </div>
    </div>
</footer>