<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>XP Redemption | Curhatorium</title>
    <link rel="stylesheet" href="{{ asset('css/global.css') }}">
    <link rel="stylesheet" href="{{ asset('css/xp-redemption.css') }}">
</head>
<body>
    @include('components.navbar')
    
    <main class="redemption-main">
        <section class="redemption-section">
            <h1 class="redemption-title">Tukarkan XP-mu dengan<br>reward yang kamu inginkan!</h1>
            
            <!-- User XP Display -->
            <div class="user-xp-display">
                <div class="xp-info">
                    <span class="xp-label">Your XP:</span>
                    <span class="xp-value">{{ number_format($user->total_xp) }} XP</span>
                </div>
                <div class="xp-progress">
                    <div class="progress-bar">
                        <div class="progress-fill" style="width: {{ min(100, ($user->total_xp / 10000) * 100) }}%"></div>
                    </div>
                    <span class="progress-text">Progress to highest tier: {{ number_format($user->total_xp) }}/10,000 XP</span>
                </div>
            </div>

            <!-- Redemption Table -->
            @php
                $featureNames = [
                    'mental_test' => 'Tes Kesehatan Mental',
                    'tracker' => 'Mood and Productivity Tracker',
                    'mentai_chatbot' => 'Ment-AI Chatbot',
                    'missions' => 'Missions of The Day',
                    'support_group' => 'Support Group Discussion',
                    'deep_cards' => 'Deep Cards',
                    'mentai_deepcard_unlimited' => 'Ment-AI & Deep Cards',
                    'share_talk_ranger_chat' => 'Share and Talk via Chat w/ Rangers',
                    'share_talk_psy_chat' => 'Share and Talk via Chat w/ Psikolog',
                    'share_talk_psy_video' => 'Share and Talk via Video Call w/ Psikiater',
                ];
            @endphp
            <div class="redemption-table-container">
                <table class="redemption-table">
                    <thead>
                        <tr>
                            <th>Skema Voucher XP</th>
                            <th>XP</th>
                            <th>Type</th>
                            <th>Action</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($redemptionScheme as $key => $item)
                            @php
                                $canAfford = $user->total_xp >= $item['xp_cost'];
                                $xpShortage = $item['xp_cost'] - $user->total_xp;
                                $displayName = $featureNames[$key] ?? ucfirst(str_replace(['_', '-'], ' ', $key));
                            @endphp
                            <tr class="{{ $canAfford ? 'affordable' : 'unaffordable' }}">
                                <td class="item-name">{{ $displayName }}</td>
                                <td class="xp-cost">{{ number_format($item['xp_cost']) }} XP</td>
                                <td class="ticket-type">
                                    @if($item['is_unlimited'])
                                        <span class="unlimited-badge">Unlimited (30 days)</span>
                                    @else
                                        <span class="single-badge">1 Session (30 days)</span>
                                    @endif
                                </td>
                                <td class="action-cell">
                                    @if($canAfford)
                                        <form method="POST" action="{{ route('xp-redemption.redeem') }}" class="redeem-form">
                                            @csrf
                                            <input type="hidden" name="ticket_type" value="{{ $key }}">
                                            <button type="submit" class="redeem-btn" onclick="return confirm('Are you sure you want to redeem {{ $displayName }} for {{ number_format($item['xp_cost']) }} XP?')">
                                                Redeem
                                            </button>
                                        </form>
                                    @else
                                        <div class="insufficient-xp">
                                            <span class="shortage-text">Need {{ number_format($xpShortage) }} more XP</span>
                                        </div>
                                    @endif
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>

            <!-- Success/Error Messages -->
            @if(session('success'))
                <div class="alert alert-success">
                    {{ session('success') }}
                </div>
            @endif

            @if(session('error'))
                <div class="alert alert-error">
                    {{ session('error') }}
                </div>
            @endif

            <!-- Info Section -->
            <div class="info-section">
                <h3>How it works:</h3>
                <ul>
                    <li>Redeem your accumulated XP for various service tickets</li>
                    <li>Most tickets provide 1 session and expire after 30 days</li>
                    <li>Mood & Productivity Tracker and Ment-AI & Deep Cards are unlimited for 30 days</li>
                    <li>All tickets expire after 30 days from redemption</li>
                    <li>You can redeem multiple tickets if you have enough XP</li>
                </ul>
            </div>
        </section>
    </main>

    @include('components.footer')

    <script>
        // Auto-hide alerts after 5 seconds
        document.addEventListener('DOMContentLoaded', function() {
            const alerts = document.querySelectorAll('.alert');
            alerts.forEach(alert => {
                setTimeout(() => {
                    alert.style.opacity = '0';
                    setTimeout(() => alert.remove(), 300);
                }, 5000);
            });
        });
    </script>
</body>
</html> 