<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>XP Redemption | Curhatorium</title>
    <link rel="stylesheet" href="{{ asset('css/global.css') }}">
    @vite('resources/css/app.css')
</head>
<body class="bg-gray-100">
    @include('components.navbar')

    <main class="container mx-auto px-4 py-8">
        <section class="bg-white rounded-lg shadow-md p-6 md:p-8">
            <h1 class="text-3xl md:text-4xl font-bold text-center text-gray-800 mb-4">Tukarkan XP-mu dengan<br>reward yang kamu inginkan!</h1>

            <!-- User XP Display -->
            <div class="bg-teal-100 border-l-4 border-teal-500 text-teal-700 p-4 rounded-md mb-6">
                <div class="flex justify-between items-center mb-2">
                    <span class="font-semibold">XP Kamu:</span>
                    <span class="text-xl font-bold">{{ number_format($user->total_xp) }} XP</span>
                </div>
                <div class="w-full bg-gray-200 rounded-full h-4">
                    <div class="bg-teal-500 h-4 rounded-full" style="width: {{ min(100, ($user->total_xp / 10000) * 100) }}%"></div>
                </div>
                <p class="text-right text-sm mt-1">Progress to highest tier: {{ number_format($user->total_xp) }}/10,000 XP</p>
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
                    'share_talk_psy_video' => 'Share and Talk via Video Call w/ Psikolog',
                ];
            @endphp
            <div class="overflow-x-auto">
                <table class="min-w-full bg-white border border-gray-200">
                    <thead class="bg-gray-50">
                        <tr>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Skema Voucher XP</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">XP</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Type</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Action</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-200">
                        @foreach($redemptionScheme as $key => $item)
                            @php
                                $canAfford = $user->total_xp >= $item['xp_cost'];
                                $xpShortage = $item['xp_cost'] - $user->total_xp;
                                $displayName = $featureNames[$key] ?? ucfirst(str_replace(['_', '-'], ' ', $key));
                            @endphp
                            <tr class="{{ $canAfford ? 'bg-green-50' : 'bg-red-50' }}">
                                <td class="px-6 py-4 whitespace-nowrap font-medium text-gray-900">{{ $displayName }}</td>
                                <td class="px-6 py-4 whitespace-nowrap text-gray-700">{{ number_format($item['xp_cost']) }} XP</td>
                                <td class="px-6 py-4 whitespace-nowrap">
                                    @if($item['is_unlimited'])
                                        <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full bg-purple-100 text-purple-800">Unlimited (30 days)</span>
                                    @else
                                        <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full bg-teal-100 text-teal-800">1 Session (30 days)</span>
                                    @endif
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap">
                                    @if($canAfford)
                                        <form method="POST" action="{{ route('xp-redemption.redeem') }}">
                                            @csrf
                                            <input type="hidden" name="ticket_type" value="{{ $key }}">
                                            <button type="submit" class="bg-green-500 hover:bg-green-600 text-white font-bold py-2 px-4 rounded-md transition duration-200" onclick="return confirm('Are you sure you want to redeem {{ $displayName }} for {{ number_format($item['xp_cost']) }} XP?')">
                                                Redeem
                                            </button>
                                        </form>
                                    @else
                                        <div class="text-red-600">
                                            <span>Butuh {{ number_format($xpShortage) }} XP lagi</span>
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
                <div class="mt-6 bg-green-100 border-l-4 border-green-500 text-green-700 p-4 rounded-md">
                    {{ session('success') }}
                </div>
            @endif

            @if(session('error'))
                <div class="mt-6 bg-red-100 border-l-4 border-red-500 text-red-700 p-4 rounded-md">
                    {{ session('error') }}
                </div>
            @endif

            <!-- Info Section -->
            <div class="mt-8 bg-gray-50 p-6 rounded-lg">
                <h3 class="text-xl font-semibold mb-4">Cara Kerjanya:</h3>
                <ul class="list-disc list-inside space-y-2 text-gray-700">
                    <li>Tukarkan akumulasi XP kamu dengan berbagai tiket layanan.</li>
                    <li>Sebagian besar tiket menyediakan 1 sesi dan berlaku selama 30 hari.</li>
                    <li>Mood & Productivity Tracker dan Ment-AI & Deep Cards tidak terbatas selama 30 hari.</li>
                    <li>Semua tiket akan hangus setelah 30 hari sejak penukaran.</li>
                    <li>Kamu dapat menukarkan beberapa tiket jika XP kamu mencukupi.</li>
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