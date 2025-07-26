<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Cards</title>
    <link rel="stylesheet" href="{{ asset('css/global.css') }}">
    <style>
    body {
        background: #F1EDE7;
        min-height: 100vh;
        margin: 0;
    }
    .center-wrapper {
        min-height: 100vh;
        display: flex;
        flex-direction: column;
        justify-content: center;
        align-items: center;
    }
    .cards-container {
        display: flex;
        flex-direction: row;
        justify-content: center;
        align-items: flex-start;
        gap: 2.5rem;
        max-width: 1300px;
        margin: 0 auto;
        padding: 0;
        flex-wrap: nowrap;
    }
    .card-with-form {
        display: flex;
        flex-direction: column;
        align-items: center;
        max-width: 300px;
        width: 300px;
        box-sizing: border-box;
    }
    .card-flip {
        background: none;
        border: none;
        perspective: 1200px;
        width: 240px;
        aspect-ratio: 7/10;
        cursor: pointer;
        outline: none;
        padding: 0;
        box-sizing: border-box;
        position: relative;
        display: block;
        height: auto;
        min-width: 0;
    }
    .card-flip:focus {
        box-shadow: none;
    }
    .card-inner {
        position: relative;
        width: 100%;
        height: 100%;
        transition: transform 0.7s cubic-bezier(.4,2,.3,1);
        transform-style: preserve-3d;
        transform: rotateY(0deg);
        box-sizing: border-box;
    }
    .card-flip.flipped .card-inner {
        transform: rotateY(180deg);
    }
    .card-front, .card-back {
        position: absolute;
        width: 100%;
        height: 100%;
        left: 0;
        top: 0;
        backface-visibility: hidden;
        border-radius: 16px;
        box-shadow: 0 4px 16px rgba(60,60,120,0.10), 0 1.5px 6px rgba(60,60,120,0.08);
        display: flex;
        flex-direction: column;
        justify-content: space-between;
        align-items: center;
        padding: 1.7rem 1rem 1.2rem 1rem;
        box-sizing: border-box;
        background: #fff;
    }
    .card-front {
        color: #222;
        z-index: 2;
        transform: rotateY(180deg);
    }
    .card-category {
        font-size: 1.05rem;
        color: #222;
        font-family: FigtreeBold, FigtreeReg, Arial, sans-serif;
        text-align: center;
        margin-bottom: 0.5rem;
        font-weight: 600;
        letter-spacing: 0.04em;
        text-transform: none;
        position: relative;
    }
    .card-category-underline {
        display: block;
        width: 60px;
        height: 10px;
        margin: 0.2rem auto 0 auto;
        background: none;
        border-bottom: 1.5px solid #222;
        border-radius: 2px;
        position: relative;
    }
    .card-content {
        font-size: 1.12rem;
        font-weight: 400;
        text-align: center;
        color: #22223b;
        margin: 1.1rem 0 1.1rem 0;
        flex: 1 1 auto;
        display: flex;
        align-items: center;
        justify-content: center;
    }
    .card-front-bottom {
        display: flex;
        flex-direction: column;
        align-items: center;
        gap: 0.5rem;
    }
    .card-logo {
        width: 70px;
        height: auto;
        margin-bottom: 0.2rem;
    }
    .card-handle {
        font-size: 0.95rem;
        color: #222;
        opacity: 0.7;
        letter-spacing: 0.02em;
        text-align: center;
    }
    .card-back {
        background: #9acbd0;
        color: #222;
        transform: rotateY(0deg);
        z-index: 1;
        font-weight: 700;
        letter-spacing: 0.1em;
        box-shadow: 0 4px 16px rgba(60,60,120,0.10), 0 1.5px 6px rgba(60,60,120,0.08);
        transition: background 0.3s;
        justify-content: space-between;
        padding: 2.2rem 1.2rem 1.5rem 1.2rem;
    }
    .card-back-top {
        text-align: center;
    }
    .card-back-logo {
        font-family: FigtreeBold, FigtreeReg, Arial, sans-serif;
        font-size: 1.25rem;
        font-weight: 700;
        letter-spacing: 0.01em;
        margin-bottom: 0.1rem;
    }
    .card-back-tagline {
        font-size: 0.95rem;
        color: #222;
        opacity: 0.7;
        margin-bottom: 0.5rem;
    }
    .card-back-middle {
        display: flex;
        flex-direction: column;
        align-items: center;
        justify-content: center;
        flex: 1 1 auto;
    }
    .card-back-logoimg {
        width: 110px;
        height: auto;
        margin: 0.5rem 0 0.5rem 0;
    }
    .card-back-title {
        font-size: 2rem;
        font-family: "PlayfairDisplay-reg", "Times New Roman", Times, serif;
        margin-top: 1.2rem;
        margin-bottom: 0.5rem;
        letter-spacing: 0.01em;
    }
    .card-back-bottom {
        text-align: center;
    }
    .card-response-form {
        display: none;
        opacity: 0;
        transform: translateY(-20px);
        transition: opacity 0.4s, transform 0.4s;
        flex-direction: column;
        align-items: center;
        justify-content: center;
        width: 100%;
        max-width: 340px;
        margin-top: 1.2rem;
        background: #fff;
        border-radius: 14px;
        box-shadow: 0 2px 12px rgba(60,60,120,0.07);
        padding: 1.1rem 1rem 1rem 1rem;
        box-sizing: border-box;
    }
    .card-response-form.visible {
        display: flex !important;
        opacity: 1;
        transform: translateY(0);
    }
    .card-response-form.hiding {
        opacity: 0;
        transform: translateY(-20px);
        transition: opacity 0.4s, transform 0.4s;
    }
    .card-response-form textarea {
        width: 100%;
        padding: 0.8rem;
        border-radius: 8px;
        border: 1.5px solid #ccc;
        font-size: 1.05rem;
        resize: vertical;
        box-sizing: border-box;
        min-height: 60px;
        margin-bottom: 0.5rem;
    }
    .card-response-form button[type="submit"] {
        margin-top: 0.7rem;
        padding: 0.6rem 1.5rem;
        font-size: 1rem;
        border-radius: 8px;
        background: #222;
        color: #fff;
        border: none;
        cursor: pointer;
        width: 100%;
        max-width: 140px;
    }
    .card-response-form button[type="submit"]:hover {
        background: #444;
    }
    .card-response-affirmation {
        margin-top: 1rem;
        font-size: 1rem;
        color: #2b7a4b;
        display: none;
        text-align: center;
    }
    @media (max-width: 1200px) {
        .cards-container {
            gap: 1.2rem;
        }
        .card-with-form {
            max-width: 90vw;
            width: 90vw;
        }
    }
    @media (max-width: 900px) {
        .cards-container {
            flex-direction: column;
            align-items: center;
            gap: 1.2rem;
            flex-wrap: nowrap;
        }
        .card-with-form {
            max-width: 90vw;
            width: 90vw;
        }
        .card-flip {
            width: 75vw;
            max-width: 340px;
        }
        .center-wrapper {
            padding-top: 70px;
        }
    }
    </style>
</head>
<body>
    @include('components.navbar')
    <div class="center-wrapper">
        <div class="cards-container">
            @foreach($cards as $index => $card)
                <div class="card-with-form">
                    <button class="card-flip" data-card-index="{{ $index }}">
                        <div class="card-inner">
                            <div class="card-front">
                                <div>
                                    <div class="card-category">{{ $card->category ?? '' }}</div>
                                    <span class="card-category-underline"></span>
                                </div>
                                <div class="card-content">{{ $card->content ?? '' }}</div>
                                <div class="card-front-bottom">
                                    <div class="card-handle">@curhatorium</div>
                                </div>
                            </div>
                            <div class="card-back">
                                <div class="card-back-top">
                                    <div class="card-back-logo">curhatorium</div>
                                    <div class="card-back-tagline">your safest place</div>
                                </div>
                                <div class="card-back-middle">
                                    <div class="card-back-title">Deep<br>Cards</div>
                                </div>
                                <div class="card-back-bottom">
                                    <div class="card-handle">@curhatorium</div>
                                </div>
                            </div>
                        </div>
                    </button>
                    <form class="card-response-form" data-card-index="{{ $index }}">
                        <textarea rows="3" placeholder="Tulis jawaban atau refleksimu di sini..."></textarea>
                        <button type="submit">Kirim</button>
                        <div class="card-response-affirmation"></div>
                    </form>
                </div>
            @endforeach
        </div>
    </div>
    <script>
    document.querySelectorAll('.card-flip').forEach(card => {
        card.classList.remove('flipped');
        card.addEventListener('click', function() {
            const index = this.getAttribute('data-card-index');
            const form = document.querySelector('.card-response-form[data-card-index="' + index + '"]');
            // Hide all forms except the current one
            document.querySelectorAll('.card-response-form').forEach(f => {
                if (f !== form) {
                    f.classList.remove('visible');
                    f.classList.remove('hiding');
                    if (f.style.display !== 'none') {
                        f.classList.add('hiding');
                        setTimeout(() => {
                            f.classList.remove('hiding');
                            f.style.display = 'none';
                        }, 400);
                    }
                }
            });
            // Unflip all cards except this
            document.querySelectorAll('.card-flip').forEach(c => {
                if (c !== this) c.classList.remove('flipped');
            });
            // Toggle this card
            this.classList.toggle('flipped');
            if (this.classList.contains('flipped')) {
                form.style.display = 'flex';
                setTimeout(() => { form.classList.add('visible'); }, 10);
            } else {
                form.classList.remove('visible');
                form.classList.add('hiding');
                setTimeout(() => {
                    form.classList.remove('hiding');
                    form.style.display = 'none';
                }, 400);
            }
        });
    });
    // Handle each card's response form
    document.querySelectorAll('.card-response-form').forEach(form => {
        form.addEventListener('submit', function(e) {
            e.preventDefault();
            const textarea = this.querySelector('textarea');
            const affirmation = this.querySelector('.card-response-affirmation');
            if (textarea.value.trim() !== '') {
                affirmation.textContent = 'Terima kasih sudah berbagi atau merefleksikan. Kamu luar biasa!';
                affirmation.style.display = 'block';
                textarea.value = '';
            } else {
                affirmation.textContent = 'Silakan tulis sesuatu sebelum mengirim.';
                affirmation.style.display = 'block';
            }
        });
    });
    </script>
</body>
</html>
