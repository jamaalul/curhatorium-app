<!-- Ebook Header Section -->
<div style="display: flex; justify-content: space-between; align-items: flex-start; width: 100%;">
    <!-- Left Title, Description & Library Button Frame -->
    <div style="display: flex; flex-direction: column; gap: 10px; max-width: 672px; width: 100%;">
        <h1 style="font-family: 'Bricolage Grotesque', sans-serif; font-size: 32px; sm:font-size: 48px; font-weight: 600; line-height: 40px; sm:line-height: 56px; letter-spacing: -0.015em; color: #18181B; margin: 0;">
            Ebook Curhatorium
        </h1>
        <p style="font-family: 'DM Sans', sans-serif; font-size: 15px; sm:font-size: 18px; font-weight: 500; line-height: 24px; sm:line-height: 28px; color: #71717A; margin: 0;">
            Pilih bacaan digital untuk mendukung proses refleksi dan pengembangan diri.
        </p>

        <!-- Library Saya Button (Neat, Non-wrapping Pill below description) -->
        @auth
            <div style="margin-top: 6px; display: flex;">
                <a href="{{ route('ebooks.library') }}" 
                   style="display: inline-flex; align-items: center; gap: 8px; background: #00BBA7; color: #FFFFFF; font-family: 'DM Sans', sans-serif; font-size: 14px; font-weight: 500; padding: 8px 18px; border-radius: 9999px; text-decoration: none; transition: all 0.15s ease; box-shadow: 0 2px 6px rgba(0, 187, 167, 0.25);"
                   class="hover:bg-[#009B8A] hover:no-underline">
                    <svg style="width: 18px; height: 18px; color: #FFFFFF; flex-shrink: 0;" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5s3.332.477 4.5 1.253v13C19.832 18.477 18.246 18 16.5 18s-3.332.477-4.5 1.253" />
                    </svg>
                    <span style="white-space: nowrap; color: #FFFFFF; font-size: 14px; font-weight: 500; font-family: 'DM Sans', sans-serif;">Library Saya</span>
                </a>
            </div>
        @endauth
    </div>
    
    <!-- Right Mascot Illustration (Hidden on mobile < 768px) -->
    <div class="hidden md:block" style="width: 140px; height: 90px; flex-shrink: 0;">
        <img src="{{ asset('images/marketplace/header-illustration.svg') }}" alt="Mascot Illustration" style="width: 100%; height: 100%; object-fit: contain;" />
    </div>
</div>
