/**
 * Onboarding Tour Module
 * Handles the user onboarding experience using Intro.js
 */

class OnboardingTour {
    constructor() {
        this.stepsDesktop = [
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

        this.stepsMobile = [
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

        this.init();
    }

    init() {
        this.setupReplayButton();
    }

    setupReplayButton() {
        const replayButton = document.getElementById('replay-onboarding-btn');
        if (replayButton) {
            replayButton.addEventListener('click', () => {
                this.resetAndStartTour();
            });
        }
    }

    async resetAndStartTour() {
        try {
            // Reset onboarding status first
            await fetch('/reset-onboarding', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
                }
            });
            
            // Start the onboarding tour
            this.startTour();
        } catch (error) {
            console.error('Error resetting onboarding:', error);
        }
    }

    startTour() {
        const isMobile = window.innerWidth <= 600;
        const steps = isMobile ? this.stepsMobile : this.stepsDesktop;
        
        introJs().setOptions({
            steps: steps,
            showProgress: true,
            showBullets: false,
            nextLabel: 'Selanjutnya',
            prevLabel: 'Sebelumnya',
            doneLabel: 'Selesai',
            exitOnOverlayClick: false,
            exitOnEsc: false,
        }).oncomplete(() => {
            this.markOnboardingCompleted();
        }).onexit(() => {
            this.markOnboardingCompleted();
        }).start();
    }

    async markOnboardingCompleted() {
        try {
            await fetch('/mark-onboarding-completed', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
                }
            });
        } catch (error) {
            console.error('Error marking onboarding completed:', error);
        }
    }
}

// Auto-start onboarding for new users
document.addEventListener("DOMContentLoaded", function() {
    // Check if user has completed onboarding
    const onboardingCompleted = document.querySelector('meta[name="onboarding-completed"]')?.getAttribute('content') === 'true';
    
    if (!onboardingCompleted) {
        new OnboardingTour().startTour();
    } else {
        // Initialize replay functionality for existing users
        new OnboardingTour();
    }
});

// Make available globally for debugging
window.OnboardingTour = OnboardingTour; 