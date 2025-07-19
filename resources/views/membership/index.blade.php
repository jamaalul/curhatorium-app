<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Membership Fleksibel</title>
    <link rel="stylesheet" href="{{ asset('css/global.css') }}">
    <link rel="stylesheet" href="{{ asset('css/main/membership.css') }}">
    <!-- Styles will be added externally -->
</head>
<body>
    @include('components.navbar')

    @php
        $cards = [
            [
                'class' => '',
                'header_svg' => '<svg width="42" height="42" viewBox="0 0 42 42" fill="none" xmlns="http://www.w3.org/2000/svg"><path d="M5.20248 8.5594C5.20248 12.2423 3.74772 15.7707 1.16512 18.3518L1.0405 18.4763C-0.346832 19.8628 -0.346832 22.1372 1.0405 23.5237L1.16512 23.6482C3.74772 26.2293 5.20248 29.7577 5.20248 33.4406C5.20248 35.336 6.71295 36.8725 8.5762 36.8725H8.69046C12.348 36.8725 15.8838 38.3581 18.4928 40.9655C19.863 42.3349 22.0739 42.3497 23.444 40.9804C26.0784 38.3477 29.6237 36.8725 33.3166 36.8725H33.5131C35.4204 36.8725 36.9665 35.2997 36.9665 33.3595V33.2084C36.9665 29.5352 38.4175 26.016 40.9933 23.4417C42.3356 22.1002 42.3356 19.8998 40.9933 18.5583C38.4175 15.984 36.9665 12.4648 36.9665 8.7916V8.64048C36.9665 6.70031 35.4204 5.1275 33.5131 5.1275H33.3166C29.6237 5.1275 26.0784 3.65232 23.444 1.01957C22.0739 -0.349742 19.863 -0.334852 18.4928 1.03446C15.8838 3.64193 12.348 5.1275 8.69046 5.1275H8.5762C6.71295 5.1275 5.20248 6.66401 5.20248 8.5594Z" fill="#FFCD2D"/></svg>',
                'title' => 'Calm Starter',
                'price_html' => '<span class="currency">Rp</span>0<span class="period"> /bulan</span>',
                'benefits' => [
                    'Tes Kesehatan Mental',
                    'Mood and Productivity Tracker (7 Hari)',
                    '1x Share and Talk (Lunars)',
                    '2x Sesi Mental Support Chatbot',
                    '7 Hari Missions of The Day',
                    '1x Support Group Discussion (2 Hari)',
                    '1x Deep Calm',
                ],
                'price_style' => '',
            ],
            [
                'class' => '',
                'header_svg' => '<svg width="42" height="42" viewBox="0 0 42 42" fill="none" xmlns="http://www.w3.org/2000/svg"><path d="M5.20248 8.5594C5.20248 12.2423 3.74772 15.7707 1.16512 18.3518L1.0405 18.4763C-0.346832 19.8628 -0.346832 22.1372 1.0405 23.5237L1.16512 23.6482C3.74772 26.2293 5.20248 29.7577 5.20248 33.4406C5.20248 35.336 6.71295 36.8725 8.5762 36.8725H8.69046C12.348 36.8725 15.8838 38.3581 18.4928 40.9655C19.863 42.3349 22.0739 42.3497 23.444 40.9804C26.0784 38.3477 29.6237 36.8725 33.3166 36.8725H33.5131C35.4204 36.8725 36.9665 35.2997 36.9665 33.3595V33.2084C36.9665 29.5352 38.4175 26.016 40.9933 23.4417C42.3356 22.1002 42.3356 19.8998 40.9933 18.5583C38.4175 15.984 36.9665 12.4648 36.9665 8.7916V8.64048C36.9665 6.70031 35.4204 5.1275 33.5131 5.1275H33.3166C29.6237 5.1275 26.0784 3.65232 23.444 1.01957C22.0739 -0.349742 19.863 -0.334852 18.4928 1.03446C15.8838 3.64193 12.348 5.1275 8.69046 5.1275H8.5762C6.71295 5.1275 5.20248 6.66401 5.20248 8.5594Z" fill="#FFCD2D"/></svg>',
                'title' => 'Growth Path',
                'price_html' => '<span class="currency">Rp</span>45<span class="thousands">.000</span><span class="period"> /bulan</span>',
                'benefits' => [
                    'Tes Kesehatan Mental',
                    '3x Share and Talk (Lunars)',
                    '3x Share and Talk (Rangers)',
                    '2x Deep Card',
                    '3 Jam Mental Support Chatbot',
                    '2x Support Group Discussion/2 Minggu',
                    'Missions of The Day',
                    'Mood and Productivity Tracker',
                ],
                'price_style' => '',
            ],
            [
                'class' => '',
                'header_svg' => '<svg width="42" height="42" viewBox="0 0 42 42" fill="none" xmlns="http://www.w3.org/2000/svg"><path d="M5.20248 8.5594C5.20248 12.2423 3.74772 15.7707 1.16512 18.3518L1.0405 18.4763C-0.346832 19.8628 -0.346832 22.1372 1.0405 23.5237L1.16512 23.6482C3.74772 26.2293 5.20248 29.7577 5.20248 33.4406C5.20248 35.336 6.71295 36.8725 8.5762 36.8725H8.69046C12.348 36.8725 15.8838 38.3581 18.4928 40.9655C19.863 42.3349 22.0739 42.3497 23.444 40.9804C26.0784 38.3477 29.6237 36.8725 33.3166 36.8725H33.5131C35.4204 36.8725 36.9665 35.2997 36.9665 33.3595V33.2084C36.9665 29.5352 38.4175 26.016 40.9933 23.4417C42.3356 22.1002 42.3356 19.8998 40.9933 18.5583C38.4175 15.984 36.9665 12.4648 36.9665 8.7916V8.64048C36.9665 6.70031 35.4204 5.1275 33.5131 5.1275H33.3166C29.6237 5.1275 26.0784 3.65232 23.444 1.01957C22.0739 -0.349742 19.863 -0.334852 18.4928 1.03446C15.8838 3.64193 12.348 5.1275 8.69046 5.1275H8.5762C6.71295 5.1275 5.20248 6.66401 5.20248 8.5594Z" fill="#FFCD2D"/></svg>',
                'title' => 'Blossom',
                'price_html' => '<span class="currency">Rp</span>67<span class="thousands">.000</span><span class="period"> /bulan</span>',
                'benefits' => [
                    'Tes Kesehatan Mental',
                    '8x Share and Talk (Lunars)',
                    '8x Share and Talk (Rangers)',
                    'Mood and Productivity Tracker (+Short Report)',
                    '5x Support Group Discussion',
                    '5x Mental Support Chatbot',
                    'Missions of The Day',
                    '3x Deep Cards',
                ],
                'price_style' => '',
            ],
            [
                'class' => '',
                'header_svg' => '<svg width="42" height="42" viewBox="0 0 42 42" fill="none" xmlns="http://www.w3.org/2000/svg"><path d="M5.20248 8.5594C5.20248 12.2423 3.74772 15.7707 1.16512 18.3518L1.0405 18.4763C-0.346832 19.8628 -0.346832 22.1372 1.0405 23.5237L1.16512 23.6482C3.74772 26.2293 5.20248 29.7577 5.20248 33.4406C5.20248 35.336 6.71295 36.8725 8.5762 36.8725H8.69046C12.348 36.8725 15.8838 38.3581 18.4928 40.9655C19.863 42.3349 22.0739 42.3497 23.444 40.9804C26.0784 38.3477 29.6237 36.8725 33.3166 36.8725H33.5131C35.4204 36.8725 36.9665 35.2997 36.9665 33.3595V33.2084C36.9665 29.5352 38.4175 26.016 40.9933 23.4417C42.3356 22.1002 42.3356 19.8998 40.9933 18.5583C38.4175 15.984 36.9665 12.4648 36.9665 8.7916V8.64048C36.9665 6.70031 35.4204 5.1275 33.5131 5.1275H33.3166C29.6237 5.1275 26.0784 3.65232 23.444 1.01957C22.0739 -0.349742 19.863 -0.334852 18.4928 1.03446C15.8838 3.64193 12.348 5.1275 8.69046 5.1275H8.5762C6.71295 5.1275 5.20248 6.66401 5.20248 8.5594Z" fill="#FFCD2D"/></svg>',
                'title' => 'Inner Peace',
                'price_html' => '<span class="currency">Rp</span>121<span class="thousands">.000</span><span class="period"> /bulan</span>',
                'benefits' => [
                    'Tes Kesehatan Mental',
                    '8x Share and Talk (Lunars)',
                    '8x Share and Talk (Rangers)',
                    'Mood and Productivity Tracker (+Full Report)',
                    '8x Support Group Discussion',
                    '8x Mental Support Chatbot',
                    'Missions of The Day',
                    '5x Deep Cards',
                ],
                'price_style' => '',
            ],
            [
                'class' => 'yellow',
                'header_svg' => '<svg width="42" height="42" viewBox="0 0 42 42" fill="none" xmlns="http://www.w3.org/2000/svg"><path d="M5.20248 8.5594C5.20248 12.2423 3.74772 15.7707 1.16512 18.3518L1.0405 18.4763C-0.346832 19.8628 -0.346832 22.1372 1.0405 23.5237L1.16512 23.6482C3.74772 26.2293 5.20248 29.7577 5.20248 33.4406C5.20248 35.336 6.71295 36.8725 8.5762 36.8725H8.69046C12.348 36.8725 15.8838 38.3581 18.4928 40.9655C19.863 42.3348 22.0739 42.3497 23.444 40.9804C26.0784 38.3477 29.6237 36.8725 33.3166 36.8725H33.5131C35.4204 36.8725 36.9665 35.2997 36.9665 33.3595V33.2084C36.9665 29.5352 38.4175 26.016 40.9933 23.4417C42.3356 22.1002 42.3356 19.8998 40.9933 18.5583C38.4175 15.984 36.9665 12.4648 36.9665 8.7916V8.64048C36.9665 6.70031 35.4204 5.1275 33.5131 5.1275H33.3166C29.6237 5.1275 26.0784 3.65232 23.444 1.01957C22.0739 -0.349742 19.863 -0.334853 18.4928 1.03446C15.8838 3.64193 12.348 5.1275 8.69046 5.1275H8.5762C6.71295 5.1275 5.20248 6.66401 5.20248 8.5594Z" fill="#47A6A6"/></svg>',
                'title' => 'Harmony',
                'price_html' => '<span class="currency">Rp</span>17<span class="thousands">.000</span>',
                'benefits' => [
                    '3x Support Group Discussion',
                ],
                'price_style' => '',
            ],
            [
                'class' => 'yellow',
                'header_svg' => '<svg width="42" height="42" viewBox="0 0 42 42" fill="none" xmlns="http://www.w3.org/2000/svg"><path d="M5.20248 8.5594C5.20248 12.2423 3.74772 15.7707 1.16512 18.3518L1.0405 18.4763C-0.346832 19.8628 -0.346832 22.1372 1.0405 23.5237L1.16512 23.6482C3.74772 26.2293 5.20248 29.7577 5.20248 33.4406C5.20248 35.336 6.71295 36.8725 8.5762 36.8725H8.69046C12.348 36.8725 15.8838 38.3581 18.4928 40.9655C19.863 42.3348 22.0739 42.3497 23.444 40.9804C26.0784 38.3477 29.6237 36.8725 33.3166 36.8725H33.5131C35.4204 36.8725 36.9665 35.2997 36.9665 33.3595V33.2084C36.9665 29.5352 38.4175 26.016 40.9933 23.4417C42.3356 22.1002 42.3356 19.8998 40.9933 18.5583C38.4175 15.984 36.9665 12.4648 36.9665 8.7916V8.64048C36.9665 6.70031 35.4204 5.1275 33.5131 5.1275H33.3166C29.6237 5.1275 26.0784 3.65232 23.444 1.01957C22.0739 -0.349742 19.863 -0.334853 18.4928 1.03446C15.8838 3.64193 12.348 5.1275 8.69046 5.1275H8.5762C6.71295 5.1275 5.20248 6.66401 5.20248 8.5594Z" fill="#47A6A6"/></svg>',
                'title' => 'Serenity',
                'price_html' => '<span class="currency">Rp</span>31<span class="thousands white">.000</span>',
                'benefits' => [
                    '1 Jam Share and Talk (Lunars and Rangers)',
                ],
                'price_style' => '',
            ],
            [
                'class' => 'dark',
                'header_svg' => '<svg width="42" height="42" viewBox="0 0 42 42" fill="none" xmlns="http://www.w3.org/2000/svg"><path d="M5.20248 8.5594C5.20248 12.2423 3.74772 15.7707 1.16512 18.3518L1.0405 18.4763C-0.346832 19.8628 -0.346832 22.1372 1.0405 23.5237L1.16512 23.6482C3.74772 26.2293 5.20248 29.7577 5.20248 33.4406C5.20248 35.336 6.71295 36.8725 8.5762 36.8725H8.69046C12.348 36.8725 15.8838 38.3581 18.4928 40.9655C19.863 42.3348 22.0739 42.3497 23.444 40.9804C26.0784 38.3477 29.6237 36.8725 33.3166 36.8725H33.5131C35.4204 36.8725 36.9665 35.2997 36.9665 33.3595V33.2084C36.9665 29.5352 38.4175 26.016 40.9933 23.4417C42.3356 22.1002 42.3356 19.8998 40.9933 18.5583C38.4175 15.984 36.9665 12.4648 36.9665 8.7916V8.64048C36.9665 6.70031 35.4204 5.1275 33.5131 5.1275H33.3166C29.6237 5.1275 26.0784 3.65232 23.444 1.01957C22.0739 -0.349742 19.863 -0.334853 18.4928 1.03446C15.8838 3.64193 12.348 5.1275 8.69046 5.1275H8.5762C6.71295 5.1275 5.20248 6.66401 5.20248 8.5594Z" fill="white"/></svg>',
                'title' => "Chat with Sanny's Aid",
                'price_html' => '<span class="currency">Rp</span>64<span class="thousands-yellow">.000</span>',
                'benefits' => [
                    '14 Hari Mood and Productivity Tracker (Full Report)',
                    '1x Deep Calm (Chat Report)',
                    '1x Missions of The Day (Chat)',
                ],
                'price_style' => 'color: #FFCD2D',
            ],
            [
                'class' => 'dark',
                'header_svg' => '<svg width="42" height="42" viewBox="0 0 42 42" fill="none" xmlns="http://www.w3.org/2000/svg"><path d="M5.20248 8.5594C5.20248 12.2423 3.74772 15.7707 1.16512 18.3518L1.0405 18.4763C-0.346832 19.8628 -0.346832 22.1372 1.0405 23.5237L1.16512 23.6482C3.74772 26.2293 5.20248 29.7577 5.20248 33.4406C5.20248 35.336 6.71295 36.8725 8.5762 36.8725H8.69046C12.348 36.8725 15.8838 38.3581 18.4928 40.9655C19.863 42.3348 22.0739 42.3497 23.444 40.9804C26.0784 38.3477 29.6237 36.8725 33.3166 36.8725H33.5131C35.4204 36.8725 36.9665 35.2997 36.9665 33.3595V33.2084C36.9665 29.5352 38.4175 26.016 40.9933 23.4417C42.3356 22.1002 42.3356 19.8998 40.9933 18.5583C38.4175 15.984 36.9665 12.4648 36.9665 8.7916V8.64048C36.9665 6.70031 35.4204 5.1275 33.5131 5.1275H33.3166C29.6237 5.1275 26.0784 3.65232 23.444 1.01957C22.0739 -0.349742 19.863 -0.334853 18.4928 1.03446C15.8838 3.64193 12.348 5.1275 8.69046 5.1275H8.5762C6.71295 5.1275 5.20248 6.66401 5.20248 8.5594Z" fill="white"/></svg>',
                'title' => "Meet with Sanny's Aid",
                'price_html' => '<span class="currency">Rp</span>192<span class="thousands-yellow">.000</span>',
                'benefits' => [
                    '1 Bulan Mood and Productivity Tracker (Full Report)',
                    '1x Deep Calm (Video Report)',
                    '1x Share and Talk (Video Meet)',
                    '3x Deep Cards',
                ],
                'price_style' => 'color: #FFCD2D',
            ],
        ];
    @endphp

    <div class="container">
        <div class="header">
            <h1>Membership <span class="highlight">Fleksibel</span></h1>
            <p>Pilih paket sesuai kebutuhanmu dan sesukamu</p>
        </div>

        <div class="pricing-grid">
            @foreach($cards as $card)
                <div class="pricing-card{{ $card['class'] ? ' ' . $card['class'] : '' }}">
                    <div class="card-header">
                        {!! $card['header_svg'] !!}
                        <h3 class="card-title">{{ $card['title'] }}</h3>
                    </div>
                    <div class="card-price" @if(!empty($card['price_style'])) style="{{ $card['price_style'] }}" @endif>
                        {!! $card['price_html'] !!}
                    </div>
                    <ul class="benefits-list">
                        @foreach($card['benefits'] as $benefit)
                            <li>{{ $benefit }}</li>
                        @endforeach
                    </ul>
                    <button class="subscribe-btn">Langganan Sekarang</button>
                </div>
            @endforeach
        </div>
    </div>
</body>
</html>