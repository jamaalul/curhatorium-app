<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/swiper@11/swiper-bundle.min.css" />
<link href="https://cdn.jsdelivr.net/npm/aos@2.3.4/dist/aos.css" rel="stylesheet">

<style>
    .card-flip {
        perspective: 1200px;
    }
    .card-inner {
        transition: transform 0.7s cubic-bezier(.4,2,.3,1);
        transform-style: preserve-3d;
    }
    .card-flip.flipped .card-inner {
        transform: rotateY(180deg);
    }
    .card-front, .card-back {
        backface-visibility: hidden;
    }
    .card-front {
        transform: rotateY(180deg);
    }
    .card-response-form {
        transition: opacity 0.4s, transform 0.4s;
    }
    .card-hidden {
        display: none;
    }
</style>

<section class="w-full px-4 py-8 bg-cover flex flex-col items-center justify-center shadow-inner h-fit" style="background-image: url('{{ asset('images/background.jpg') }}');" id="cards-section">

    <div class="min-h-80">
        <!-- Slider View (Mobile/Tablet) -->
        <div id="slider-view" class="card-hidden w-full max-w-7xl mx-auto h-fit">
            <div class="swiper mySwiper">
                <div id="swiper-cards-container" class="swiper-wrapper mb-8 h-fit">
                    <!-- Swiper slides will be injected here -->
                </div>
                <div class="swiper-pagination"></div>
            </div>
        </div>

        <!-- Grid View (Desktop) -->
        <div id="grid-view" class="card-hidden w-full max-w-7xl mx-auto">
            <p class="text-stone-400 text-center flex items-center justify-center gap-2">
                <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="size-5">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M7.5 21 3 16.5m0 0L7.5 12M3 16.5h13.5m0-13.5L21 7.5m0 0L16.5 12M21 7.5H7.5" />
                </svg>
                Klik untuk membalik Deep Cards
            </p>
            <div id="grid-cards-container" class="flex flex-nowrap justify-center items-start mx-4 gap-4 h-full">
                <!-- Grid items will be injected here -->
            </div>
        </div>
    </div>
    <button id="shuffle-cards" class="bg-[#9acbd0] hover:bg-[#48a6a6] text-white py-2 px-12 rounded mt-4 flex items-center gap-2">
        <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="size-5">
            <path stroke-linecap="round" stroke-linejoin="round" d="M16.023 9.348h4.992v-.001M2.985 19.644v-4.992m0 0h4.992m-4.993 0 3.181 3.183a8.25 8.25 0 0 0 13.803-3.7M4.031 9.865a8.25 8.25 0 0 1 13.803-3.7l3.181 3.182m0-4.991v4.99" />
        </svg>
        Acak
    </button>
</section>

<script src="https://cdn.jsdelivr.net/npm/swiper@11/swiper-bundle.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/aos@2.3.4/dist/aos.js"></script>
<script>
document.addEventListener('DOMContentLoaded', function() {
    const shuffleButton = document.getElementById('shuffle-cards');
    const sliderView = document.getElementById('slider-view');
    const gridView = document.getElementById('grid-view');
    const swiperCardsContainer = document.getElementById('swiper-cards-container');
    const gridCardsContainer = document.getElementById('grid-cards-container');

    let swiper = null;
    let allCards = [];

    async function fetchAndRenderCards() {
        try {
            const response = await fetch('/cards');
            if (!response.ok) throw new Error('Network response was not ok');
            allCards = await response.json();
            handleViewChange();
            AOS.refresh();
        } catch (error) {
            console.error('Failed to fetch cards:', error);
            // Display error in both containers just in case
            gridCardsContainer.innerHTML = `<p class="text-red-500">Failed to load cards. Please try again later.</p>`;
            swiperCardsContainer.innerHTML = `<p class="text-red-500">Failed to load cards. Please try again later.</p>`;
        }
    }

    function getCardHTML(card, index) {
        const delay = 100 + index * 100;
        return `
            <div class="flex flex-col items-center w-full h-full p-4" data-aos="fade-up" data-aos-delay="${delay}" data-aos-duration="700">
                <button class="card-flip bg-none border-none w-[240px] aspect-[7/10] cursor-pointer outline-none p-0 relative block h-auto min-w-0" data-card-index="${index}">
                    <div class="card-inner relative w-full h-full">
                        <div class="card-front absolute w-full h-full left-0 top-0 rounded-2xl shadow-md flex flex-col justify-between items-center p-6 box-border bg-white text-gray-800 z-10 bg-[#f5f1eb]">
                            <div>
                                <div class="text-lg font-semibold text-center mb-2 tracking-wide">${card.category || ''}</div>
                                <span class="block w-[60px] h-[10px] mx-auto my-1 border-b-2 border-gray-800 rounded-sm"></span>
                            </div>
                            <div class="text-xl font-normal text-center text-[#22223b] my-4 flex-auto flex items-center justify-center">${card.content || ''}</div>
                            <div class="text-sm text-gray-800 opacity-70 tracking-wider text-center">@curhatorium</div>
                        </div>
                        <div class="card-back absolute w-full h-full left-0 top-0 rounded-2xl shadow-md flex flex-col justify-between items-center p-6 box-border bg-[#9acbd0] text-gray-800 font-bold tracking-widest">
                            <div class="text-center">
                                <div class="text-xl font-bold tracking-tight mb-1">curhatorium</div>
                                <div class="text-sm opacity-70 mb-2">your safest place</div>
                            </div>
                            <div class="flex-auto flex items-center justify-center">
                                <div class="text-4xl font-['PlayfairDisplay-reg',_serif] mt-5 mb-2 tracking-tight">Deep<br>Cards</div>
                            </div>
                            <div class="text-sm text-gray-800 opacity-70 tracking-wider text-center">@curhatorium</div>
                        </div>
                    </div>
                </button>
                <form class="card-response-form card-hidden opacity-0 -translate-y-5 flex-col items-center justify-center w-full max-w-xs mt-5 bg-white rounded-2xl shadow-md p-4 box-border" data-card-index="${index}">
                    <textarea rows="3" placeholder="Tulis jawabanmu di sini..." class="w-full p-3 rounded-lg border-2 border-gray-300 text-base resize-y box-border min-h-[60px] mb-2"></textarea>
                    <button type="submit" class="mt-3 py-2 px-6 text-base rounded-lg bg-gray-800 text-white border-none cursor-pointer w-full hover:bg-gray-600">Kirim</button>
                    <div class="card-response-affirmation mt-4 text-base text-green-700 card-hidden text-center"></div>
                </form>
            </div>
        `;
    }

    function renderSlider(cards) {
        swiperCardsContainer.innerHTML = '';
        cards.forEach((card, index) => {
            swiperCardsContainer.innerHTML += `<div class="swiper-slide">${getCardHTML(card, index)}</div>`;
        });
        addCardEventListeners(swiperCardsContainer);
        initializeSwiper();
    }

    function renderGrid(cards) {
        gridCardsContainer.innerHTML = '';
        const containerWidth = document.querySelector('section.w-full').offsetWidth;
        const cardWidth = 240 + 16; // card width 240px + gap 1rem/16px
        const maxCards = Math.floor(containerWidth / cardWidth);
        const cardsToShow = Math.max(0, Math.min(cards.length, maxCards));

        const gridCards = cards.slice(0, cardsToShow);
        gridCards.forEach((card, index) => {
            gridCardsContainer.innerHTML += `<div class="w-full" style="max-width: 240px;">${getCardHTML(card, index)}</div>`;
        });
        addCardEventListeners(gridCardsContainer);
    }

    function handleViewChange() {
        const containerWidth = document.querySelector('section.w-full').offsetWidth;
        const cardWidth = 240 + 16; // card width 240px + gap 1rem/16px
        const maxCards = Math.floor(containerWidth / cardWidth);

        if (window.innerWidth < 768 || maxCards < 3) {
            sliderView.classList.remove('card-hidden');
            gridView.classList.add('card-hidden');
            if (!swiper) {
                renderSlider(allCards);
            }
        } else {
            sliderView.classList.add('card-hidden');
            gridView.classList.remove('card-hidden');
            if (swiper) {
                swiper.destroy(true, true);
                swiper = null;
            }
            renderGrid(allCards);
        }
    }

    function initializeSwiper() {
        if (swiper) swiper.destroy(true, true);
        swiper = new Swiper('.mySwiper', {
            loop: false,
            slidesPerView: 'auto',
            centeredSlides: true,
            spaceBetween: 0,
            pagination: {
                el: '.swiper-pagination',
                clickable: true,
            },
        });
    }

    function addCardEventListeners(container) {
        container.querySelectorAll('.card-flip').forEach(card => {
            card.addEventListener('click', function() {
                const cardIndex = this.getAttribute('data-card-index');
                const isFlipped = this.classList.contains('flipped');

                // Close all other cards in the *same container*
                container.querySelectorAll('.card-flip').forEach(otherCard => {
                    otherCard.classList.remove('flipped');
                });
                container.querySelectorAll('.card-response-form').forEach(form => {
                    form.classList.remove('opacity-100', 'translate-y-0');
                    form.classList.add('card-hidden', 'opacity-0', '-translate-y-5');
                });

                // Toggle the clicked card
                if (!isFlipped) {
                    this.classList.add('flipped');
                    const form = container.querySelector(`.card-response-form[data-card-index="${cardIndex}"]`);
                    if (form) {
                        form.classList.remove('card-hidden');
                        setTimeout(() => {
                            form.classList.remove('opacity-0', '-translate-y-5');
                            form.classList.add('opacity-100', 'translate-y-0');
                        }, 10);
                    }
                }
            });
        });

        container.querySelectorAll('.card-response-form').forEach(form => {
            form.addEventListener('submit', function(e) {
                e.preventDefault();
                const textarea = this.querySelector('textarea');
                const affirmation = this.querySelector('.card-response-affirmation');
                if (textarea.value.trim() !== '') {
                    affirmation.textContent = 'Terima kasih sudah berbagi. Kamu luar biasa!';
                    affirmation.classList.remove('card-hidden');
                    textarea.value = '';
                } else {
                    affirmation.textContent = 'Silakan tulis sesuatu sebelum mengirim.';
                    affirmation.classList.remove('card-hidden');
                }
            });
        });
    }

    window.addEventListener('resize', handleViewChange);
    shuffleButton.addEventListener('click', function() {
        console.log('Shuffle button clicked');
        // 1. Shuffle the allCards array
        allCards.sort(() => Math.random() - 0.5);
        console.log('Cards shuffled:', allCards);

        // 2. Re-render the appropriate view
        handleViewChange(true); // Pass a flag to indicate a shuffle
    });

    function handleViewChange(isShuffle = false) {
        console.log('handleViewChange called. isShuffle:', isShuffle);
        const containerWidth = document.querySelector('section.w-full').offsetWidth;
        const cardWidth = 240 + 16; // card width 240px + gap 1rem/16px
        const maxCards = Math.floor(containerWidth / cardWidth);

        const isSlider = window.innerWidth < 768 || maxCards < 3;
        console.log('Current view should be:', isSlider ? 'Slider' : 'Grid');

        if (isSlider) {
            sliderView.classList.remove('card-hidden');
            gridView.classList.add('card-hidden');
            // Re-render slider if it's a shuffle or if swiper doesn't exist
            if (isShuffle || !swiper) {
                console.log('Rendering slider...');
                renderSlider(allCards);
            }
        } else {
            sliderView.classList.add('card-hidden');
            gridView.classList.remove('card-hidden');
            if (swiper) {
                swiper.destroy(true, true);
                swiper = null;
            }
            // Always re-render grid on view change or shuffle
            console.log('Rendering grid...');
            renderGrid(allCards);
        }
    }
    shuffleButton.addEventListener('click', fetchAndRenderCards);

    AOS.init();
    fetchAndRenderCards();
});
</script>