<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Membership Fleksibel</title>
    <link rel="stylesheet" href="{{ asset('css/global.css') }}?v={{ time() }}">
    <link rel="stylesheet" href="{{ asset('css/components/navbar.css') }}?v={{ time() }}">
    <link rel="stylesheet" href="{{ asset('css/main/membership.css') }}?v={{ time() }}">
    <!-- Styles will be added externally -->
</head>
<body>
    @include('components.navbar')

    <div class="container">
        <div class="header">
            <h1>Membership <span class="highlight">Fleksibel</span></h1>
            <p>Pilih paket sesuai kebutuhanmu dan sesukamu</p>
        </div>

        @if(session('success'))
            <div class="alert alert-success">{{ session('success') }}</div>
        @endif
        @if(session('error'))
            <div class="alert alert-error">{{ session('error') }}</div>
        @endif

        @php
            $membershipMeta = [
                'Calm Starter' => [
                    'class' => '',
                    'svg' => '<svg width="42" height="42" viewBox="0 0 42 42" fill="none" xmlns="http://www.w3.org/2000/svg"><path d="M5.20248 8.5594C5.20248 12.2423 3.74772 15.7707 1.16512 18.3518L1.0405 18.4763C-0.346832 19.8628 -0.346832 22.1372 1.0405 23.5237L1.16512 23.6482C3.74772 26.2293 5.20248 29.7577 5.20248 33.4406C5.20248 35.336 6.71295 36.8725 8.5762 36.8725H8.69046C12.348 36.8725 15.8838 38.3581 18.4928 40.9655C19.863 42.3349 22.0739 42.3497 23.444 40.9804C26.0784 38.3477 29.6237 36.8725 33.3166 36.8725H33.5131C35.4204 36.8725 36.9665 35.2997 36.9665 33.3595V33.2084C36.9665 29.5352 38.4175 26.016 40.9933 23.4417C42.3356 22.1002 42.3356 19.8998 40.9933 18.5583C38.4175 15.984 36.9665 12.4648 36.9665 8.7916V8.64048C36.9665 6.70031 35.4204 5.1275 33.5131 5.1275H33.3166C29.6237 5.1275 26.0784 3.65232 23.444 1.01957C22.0739 -0.349742 19.863 -0.334852 18.4928 1.03446C15.8838 3.64193 12.348 5.1275 8.69046 5.1275H8.5762C6.71295 5.1275 5.20248 6.66401 5.20248 8.5594Z" fill="#FFCD2D"/></svg>',
                    'benefits' => [
                        '(Unlimited) Tes Kesehatan Mental',
                        '(7 Hari) Mood and Productivity Tracker',
                        '(7 Hari) Missions of The Day',
                        '(1x) Support Group Discussion',
                    ],
                ],
                'Growth Path' => [
                    'class' => '',
                    'svg' => '<svg width="42" height="42" viewBox="0 0 42 42" fill="none" xmlns="http://www.w3.org/2000/svg"><path d="M5.20248 8.5594C5.20248 12.2423 3.74772 15.7707 1.16512 18.3518L1.0405 18.4763C-0.346832 19.8628 -0.346832 22.1372 1.0405 23.5237L1.16512 23.6482C3.74772 26.2293 5.20248 29.7577 5.20248 33.4406C5.20248 35.336 6.71295 36.8725 8.5762 36.8725H8.69046C12.348 36.8725 15.8838 38.3581 18.4928 40.9655C19.863 42.3349 22.0739 42.3497 23.444 40.9804C26.0784 38.3477 29.6237 36.8725 33.3166 36.8725H33.5131C35.4204 36.8725 36.9665 35.2997 36.9665 33.3595V33.2084C36.9665 29.5352 38.4175 26.016 40.9933 23.4417C42.3356 22.1002 42.3356 19.8998 40.9933 18.5583C38.4175 15.984 36.9665 12.4648 36.9665 8.7916V8.64048C36.9665 6.70031 35.4204 5.1275 33.5131 5.1275H33.3166C29.6237 5.1275 26.0784 3.65232 23.444 1.01957C22.0739 -0.349742 19.863 -0.334852 18.4928 1.03446C15.8838 3.64193 12.348 5.1275 8.69046 5.1275H8.5762C6.71295 5.1275 5.20248 6.66401 5.20248 8.5594Z" fill="#FFCD2D"/></svg>',
                    'benefits' => [
                        '(Unlimited) Tes Kesehatan Mental',
                        '(Unlimited) Mood and Productivity Tracker',
                        '(Unlimited) Missions of The Day',
                        '(1x) Support Group Discussion',
                        '(1x) Share and Talk via Chat w/ Rangers',
                        'Extra gain XP',
                    ],
                ],
                'Blossom' => [
                    'class' => '',
                    'svg' =>'<svg width="42" height="42" viewBox="0 0 42 42" fill="none" xmlns="http://www.w3.org/2000/svg"><path d="M5.20248 8.5594C5.20248 12.2423 3.74772 15.7707 1.16512 18.3518L1.0405 18.4763C-0.346832 19.8628 -0.346832 22.1372 1.0405 23.5237L1.16512 23.6482C3.74772 26.2293 5.20248 29.7577 5.20248 33.4406C5.20248 35.336 6.71295 36.8725 8.5762 36.8725H8.69046C12.348 36.8725 15.8838 38.3581 18.4928 40.9655C19.863 42.3349 22.0739 42.3497 23.444 40.9804C26.0784 38.3477 29.6237 36.8725 33.3166 36.8725H33.5131C35.4204 36.8725 36.9665 35.2997 36.9665 33.3595V33.2084C36.9665 29.5352 38.4175 26.016 40.9933 23.4417C42.3356 22.1002 42.3356 19.8998 40.9933 18.5583C38.4175 15.984 36.9665 12.4648 36.9665 8.7916V8.64048C36.9665 6.70031 35.4204 5.1275 33.5131 5.1275H33.3166C29.6237 5.1275 26.0784 3.65232 23.444 1.01957C22.0739 -0.349742 19.863 -0.334852 18.4928 1.03446C15.8838 3.64193 12.348 5.1275 8.69046 5.1275H8.5762C6.71295 5.1275 5.20248 6.66401 5.20248 8.5594Z" fill="#FFCD2D"/></svg>',
                    'benefits' => [
                        '(Unlimited) Tes Kesehatan Mental',
                        '(Unlimited) Mood and Productivity Tracker',
                        '(Unlimited) Missions of The Day',
                        '(3x) Support Group Discussion',
                        '(2x) Share and Talk via Chat w/ Rangers',
                        'Extra gain XP',
                    ],
                ],
                'Inner Peace' => [
                    'class' => '',
                    'svg' =>  '<svg width="42" height="42" viewBox="0 0 42 42" fill="none" xmlns="http://www.w3.org/2000/svg"><path d="M5.20248 8.5594C5.20248 12.2423 3.74772 15.7707 1.16512 18.3518L1.0405 18.4763C-0.346832 19.8628 -0.346832 22.1372 1.0405 23.5237L1.16512 23.6482C3.74772 26.2293 5.20248 29.7577 5.20248 33.4406C5.20248 35.336 6.71295 36.8725 8.5762 36.8725H8.69046C12.348 36.8725 15.8838 38.3581 18.4928 40.9655C19.863 42.3349 22.0739 42.3497 23.444 40.9804C26.0784 38.3477 29.6237 36.8725 33.3166 36.8725H33.5131C35.4204 36.8725 36.9665 35.2997 36.9665 33.3595V33.2084C36.9665 29.5352 38.4175 26.016 40.9933 23.4417C42.3356 22.1002 42.3356 19.8998 40.9933 18.5583C38.4175 15.984 36.9665 12.4648 36.9665 8.7916V8.64048C36.9665 6.70031 35.4204 5.1275 33.5131 5.1275H33.3166C29.6237 5.1275 26.0784 3.65232 23.444 1.01957C22.0739 -0.349742 19.863 -0.334852 18.4928 1.03446C15.8838 3.64193 12.348 5.1275 8.69046 5.1275H8.5762C6.71295 5.1275 5.20248 6.66401 5.20248 8.5594Z" fill="#FFCD2D"/></svg>',
                    'benefits' => [
                        '(Unlimited) Tes Kesehatan Mental',
                        '(Unlimited) Mood and Productivity Tracker w/ Extended Report',
                        '(Unlimited) Missions of The Day',
                        '(5x) Support Group Discussion',
                        '(3x) Share and Talk via Chat w/ Rangers',
                        'Extra gain XP',
                    ],
                ],
                'Harmony' => [
                    'class' => 'yellow harmony-popular',
                    'badge' => 'Terpopuler',
                    'svg' => '<svg width="42" height="42" viewBox="0 0 42 42" fill="none" xmlns="http://www.w3.org/2000/svg"><path d="M5.20248 8.5594C5.20248 12.2423 3.74772 15.7707 1.16512 18.3518L1.0405 18.4763C-0.346832 19.8628 -0.346832 22.1372 1.0405 23.5237L1.16512 23.6482C3.74772 26.2293 5.20248 29.7577 5.20248 33.4406C5.20248 35.336 6.71295 36.8725 8.5762 36.8725H8.69046C12.348 36.8725 15.8838 38.3581 18.4928 40.9655C19.863 42.3348 22.0739 42.3497 23.444 40.9804C26.0784 38.3477 29.6237 36.8725 33.3166 36.8725H33.5131C35.4204 36.8725 36.9665 35.2997 36.9665 33.3595V33.2084C36.9665 29.5352 38.4175 26.016 40.9933 23.4417C42.3356 22.1002 42.3356 19.8998 40.9933 18.5583C38.4175 15.984 36.9665 12.4648 36.9665 8.7916V8.64048C36.9665 6.70031 35.4204 5.1275 33.5131 5.1275H33.3166C29.6237 5.1275 26.0784 3.65232 23.444 1.01957C22.0739 -0.349742 19.863 -0.334853 18.4928 1.03446C15.8838 3.64193 12.348 5.1275 8.69046 5.1275H8.5762C6.71295 5.1275 5.20248 6.66401 5.20248 8.5594Z" fill="#47A6A6"/></svg>',
                    'benefits' => [
                        '(1x) Support Group Discussion',
                    ],
                ],
                'Serenity' => [
                    'class' => 'yellow',
                    'svg' => '<svg width="42" height="42" viewBox="0 0 42 42" fill="none" xmlns="http://www.w3.org/2000/svg"><path d="M5.20248 8.5594C5.20248 12.2423 3.74772 15.7707 1.16512 18.3518L1.0405 18.4763C-0.346832 19.8628 -0.346832 22.1372 1.0405 23.5237L1.16512 23.6482C3.74772 26.2293 5.20248 29.7577 5.20248 33.4406C5.20248 35.336 6.71295 36.8725 8.5762 36.8725H8.69046C12.348 36.8725 15.8838 38.3581 18.4928 40.9655C19.863 42.3348 22.0739 42.3497 23.444 40.9804C26.0784 38.3477 29.6237 36.8725 33.3166 36.8725H33.5131C35.4204 36.8725 36.9665 35.2997 36.9665 33.3595V33.2084C36.9665 29.5352 38.4175 26.016 40.9933 23.4417C42.3356 22.1002 42.3356 19.8998 40.9933 18.5583C38.4175 15.984 36.9665 12.4648 36.9665 8.7916V8.64048C36.9665 6.70031 35.4204 5.1275 33.5131 5.1275H33.3166C29.6237 5.1275 26.0784 3.65232 23.444 1.01957C22.0739 -0.349742 19.863 -0.334853 18.4928 1.03446C15.8838 3.64193 12.348 5.1275 8.69046 5.1275H8.5762C6.71295 5.1275 5.20248 6.66401 5.20248 8.5594Z" fill="#47A6A6"/></svg>',
                    'benefits' => [
                        '(1x) Share and Talk via Chat w/ Rangers',
                    ],
                ],
                "Chat with Sanny's Aid" => [
                    'class' => 'dark',
                    'svg' => '<svg width="42" height="42" viewBox="0 0 42 42" fill="none" xmlns="http://www.w3.org/2000/svg"><path d="M5.20248 8.5594C5.20248 12.2423 3.74772 15.7707 1.16512 18.3518L1.0405 18.4763C-0.346832 19.8628 -0.346832 22.1372 1.0405 23.5237L1.16512 23.6482C3.74772 26.2293 5.20248 29.7577 5.20248 33.4406C5.20248 35.336 6.71295 36.8725 8.5762 36.8725H8.69046C12.348 36.8725 15.8838 38.3581 18.4928 40.9655C19.863 42.3348 22.0739 42.3497 23.444 40.9804C26.0784 38.3477 29.6237 36.8725 33.3166 36.8725H33.5131C35.4204 36.8725 36.9665 35.2997 36.9665 33.3595V33.2084C36.9665 29.5352 38.4175 26.016 40.9933 23.4417C42.3356 22.1002 42.3356 19.8998 40.9933 18.5583C38.4175 15.984 36.9665 12.4648 36.9665 8.7916V8.64048C36.9665 6.70031 35.4204 5.1275 33.5131 5.1275H33.3166C29.6237 5.1275 26.0784 3.65232 23.444 1.01957C22.0739 -0.349742 19.863 -0.334853 18.4928 1.03446C15.8838 3.64193 12.348 5.1275 8.69046 5.1275H8.5762C6.71295 5.1275 5.20248 6.66401 5.20248 8.5594Z" fill="white"/></svg>',
                    'benefits' => [
                        '(1x) Share and Talk via Chat w/ Psikolog',
                        '(Unlimited 1 bulan) Mood and Productivity Tracker',
                    ],
                ],
                "Meet with Sanny's Aid" => [
                    'class' => 'dark',
                    'svg' => '<svg width="42" height="42" viewBox="0 0 42 42" fill="none" xmlns="http://www.w3.org/2000/svg"><path d="M5.20248 8.5594C5.20248 12.2423 3.74772 15.7707 1.16512 18.3518L1.0405 18.4763C-0.346832 19.8628 -0.346832 22.1372 1.0405 23.5237L1.16512 23.6482C3.74772 26.2293 5.20248 29.7577 5.20248 33.4406C5.20248 35.336 6.71295 36.8725 8.5762 36.8725H8.69046C12.348 36.8725 15.8838 38.3581 18.4928 40.9655C19.863 42.3348 22.0739 42.3497 23.444 40.9804C26.0784 38.3477 29.6237 36.8725 33.3166 36.8725H33.5131C35.4204 36.8725 36.9665 35.2997 36.9665 33.3595V33.2084C36.9665 29.5352 38.4175 26.016 40.9933 23.4417C42.3356 22.1002 42.3356 19.8998 40.9933 18.5583C38.4175 15.984 36.9665 12.4648 36.9665 8.7916V8.64048C36.9665 6.70031 35.4204 5.1275 33.5131 5.1275H33.3166C29.6237 5.1275 26.0784 3.65232 23.444 1.01957C22.0739 -0.349742 19.863 -0.334853 18.4928 1.03446C15.8838 3.64193 12.348 5.1275 8.69046 5.1275H8.5762C6.71295 5.1275 5.20248 6.66401 5.20248 8.5594Z" fill="white"/></svg>',
                    'benefits' => [
                        '(1x) Share and Talk via Video Call w/ Psikolog',
                        '(Unlimited 1 bulan) Mood and Productivity Tracker w/ Extended Report',
                    ],
                ],
            ];
        @endphp
        <!-- Basic Memberships Section -->
        <div class="membership-section">
            <h2 class="section-title">Basic Membership</h2>
            <div class="pricing-grid basic-memberships">
                @foreach($memberships as $membership)
                    @php
                        $meta = $membershipMeta[$membership->name] ?? ['class' => '', 'svg' => '', 'benefits' => []];
                        $isBasic = in_array($membership->name, ['Calm Starter', 'Growth Path', 'Harmony', 'Serenity']);
                    @endphp
                    @if($isBasic)
                        <div class="pricing-card{{ $meta['class'] ? ' ' . $meta['class'] : '' }}">
                            @if(isset($meta['badge']))
                                <div class="popular-badge">{{ $meta['badge'] }}</div>
                            @endif
                            <div class="card-header">
                                {!! $meta['svg'] !!}
                                <h3 class="card-title">{{ $membership->name }}</h3>
                            </div>
                            <div class="card-price">
                                Rp{{ number_format($membership->price, 0, ',', '.') }}
                                @if($membership->duration_days > 0)
                                    <span class="period">/{{ $membership->duration_days >= 30 ? 'bulan' : $membership->duration_days . ' hari' }}</span>
                                @endif
                            </div>
                            <ul class="benefits-list">
                                @foreach($meta['benefits'] as $i => $benefit)
                                        <li>{{ $benefit }}</li>
                                @endforeach
                            </ul>
                            @if($membership->name === 'Calm Starter')
                                <form class="subscribe-form" method="POST" action="{{ route('membership.buy', $membership->id) }}" onsubmit="return confirm('Yakin ingin membeli {{ $membership->name }}?');" style="display:none;">
                                    @csrf
                                    <button class="subscribe-btn" type="submit">Langganan Sekarang</button>
                                </form>
                            @else
                                <button class="subscribe-btn" onclick="redirectToWhatsApp('{{ $membership->name }}', {{ $membership->price }}, {{ Auth::user()->id }})" type="button">Beli Sekarang</button>
                            @endif
                        </div>
                    @endif
                @endforeach
            </div>
        </div>

        <!-- See More Button -->
        <div class="see-more-container">
            <button class="see-more-btn" onclick="togglePremiumMemberships()">
                <span class="see-more-text">Lihat Premium Membership</span>
                <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="currentColor" class="bi bi-caret-down see-more-icon" viewBox="0 0 16 16">
                    <path d="M3.204 5h9.592L8 10.481zm-.753.659 4.796 5.48a1 1 0 0 0 1.506 0l4.796-5.48c.566-.647.106-1.659-.753-1.659H3.204a1 1 0 0 0-.753 1.659"/>
                </svg>
            </button>
        </div>

        <!-- Premium Memberships Section -->
        <div class="membership-section premium-memberships" id="premium-memberships" style="display: none;">
            <h2 class="section-title">Premium Membership</h2>
            <div class="pricing-grid">
                @foreach($memberships as $membership)
                    @php
                        $meta = $membershipMeta[$membership->name] ?? ['class' => '', 'svg' => '', 'benefits' => []];
                        $isPremium = in_array($membership->name, ['Blossom', 'Inner Peace', "Chat with Sanny's Aid", "Meet with Sanny's Aid"]);
                    @endphp
                    @if($isPremium)
                        <div class="pricing-card{{ $meta['class'] ? ' ' . $meta['class'] : '' }}">
                            @if(isset($meta['badge']))
                                <div class="popular-badge">{{ $meta['badge'] }}</div>
                            @endif
                            <div class="card-header">
                                {!! $meta['svg'] !!}
                                <h3 class="card-title">{{ $membership->name }}</h3>
                            </div>
                            <div class="card-price">
                                Rp{{ number_format($membership->price, 0, ',', '.') }}
                                @if($membership->duration_days > 0)
                                    <span class="period">/{{ $membership->duration_days >= 30 ? 'bulan' : $membership->duration_days . ' hari' }}</span>
                                @endif
                            </div>
                            <ul class="benefits-list">
                                @foreach($meta['benefits'] as $i => $benefit)
                                        <li>{{ $benefit }}</li>
                                @endforeach
                            </ul>
                            <button class="subscribe-btn" onclick="redirectToWhatsApp('{{ $membership->name }}', {{ $membership->price }}, {{ Auth::user()->id }})" type="button">Beli Sekarang</button>
                        </div>
                    @endif
                @endforeach
            </div>
        </div>
    </div>

    </div>

    <script>
        function redirectToWhatsApp(membershipName, price, userId) {
            // Create a disguised payment ID: CURH-YYYYMMDD-XXXX where XXXX is the user ID padded with zeros
            const now = new Date();
            const dateStr = now.getFullYear().toString() + 
                           (now.getMonth() + 1).toString().padStart(2, '0') + 
                           now.getDate().toString().padStart(2, '0');
            const paymentId = `CURH-${dateStr}-${userId.toString().padStart(4, '0')}`;
            
            const message = `Halo! Saya ingin membeli membership "${membershipName}" seharga Rp${price.toLocaleString('id-ID')}.\n\n> Payment ID: ${paymentId}\n\nMohon informasi lebih lanjut untuk proses pembayaran.`;
            const whatsappUrl = `https://wa.me/6285117593233?text=${encodeURIComponent(message)}`;
            window.open(whatsappUrl, '_blank');
        }

        function togglePremiumMemberships() {
            const premiumSection = document.getElementById('premium-memberships');
            const seeMoreBtn = document.querySelector('.see-more-btn');
            const seeMoreText = document.querySelector('.see-more-text');
            const seeMoreIcon = document.querySelector('.see-more-icon');
            
            if (premiumSection.style.display === 'none') {
                premiumSection.style.display = 'block';
                seeMoreText.textContent = 'Sembunyikan Premium Membership';
                seeMoreIcon.style.transform = 'rotate(180deg)';
                
                // Smooth scroll to premium section
                premiumSection.scrollIntoView({ behavior: 'smooth', block: 'start' });
            } else {
                premiumSection.style.display = 'none';
                seeMoreText.textContent = 'Lihat Premium Membership';
                seeMoreIcon.style.transform = 'rotate(0deg)';
            }
        }
    </script>
</body>
</html>