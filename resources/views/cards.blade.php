<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Cards</title>
    <link rel="stylesheet" href="{{ asset('css/global.css') }}">
    <style>
    body {
        background: #f0f4f8;
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
        align-items: center;
        gap: 2.5rem;
        max-width: 1300px;
        margin: 0 auto;
        padding: 0;
    }
    .card-flip {
        background: none;
        border: none;
        perspective: 1200px;
        width: 280px;
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
        padding: 2.2rem 1.2rem 1.5rem 1.2rem;
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
        font-size: 1.18rem;
        font-weight: 400;
        text-align: center;
        color: #22223b;
        margin: 1.5rem 0 1.5rem 0;
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
        background: #fff;
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
        font-family: FigtreeBold, FigtreeReg, Arial, sans-serif;
        margin-top: 1.2rem;
        margin-bottom: 0.5rem;
        letter-spacing: 0.01em;
    }
    .card-back-bottom {
        text-align: center;
    }
    @media (max-width: 600px) {
        .center-wrapper {
            min-height: 100vh;
            padding-top: 2rem;
            padding-bottom: 2rem;
        }
        .cards-container {
            flex-direction: column;
            align-items: center;
            gap: 1.2rem;
        }
        .card-flip {
            width: 95vw;
            max-width: 380px;
            aspect-ratio: 7/10;
            height: auto;
        }
        .card-front, .card-back {
            padding: 1.2rem 0.5rem 1rem 0.5rem;
        }
        .card-back-title {
            font-size: 1.3rem;
        }
        .card-back-logoimg {
            width: 70px;
        }
        .card-logo {
            width: 45px;
        }
    }
    </style>
</head>
<body>
    @include('components.navbar')
    <div class="center-wrapper">
        <div class="cards-container">
            @foreach($cards as $card)
                <button class="card-flip">
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
                                <div class="card-back-title">DEEP<br>CARDS</div>
                            </div>
                            <div class="card-back-bottom">
                                <div class="card-handle">@curhatorium</div>
                            </div>
                        </div>
                    </div>
                </button>
            @endforeach
        </div>
    </div>
    <script>
    document.querySelectorAll('.card-flip').forEach(card => {
        card.classList.remove('flipped');
        card.addEventListener('click', function() {
            this.classList.toggle('flipped');
        });
    });
    </script>
</body>
</html>
