<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <title>Document</title>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>

<body class="flex justify-center items-center gap-4 bg-zinc-100 w-screen h-screen">
    @foreach ($plans as $plan)
        <div class="flex flex-col bg-white shadow-sm p-6 border rounded-md w-64 h-96">
            <h1 class="mb-4 font-semibold text-2xl">{{ $plan->name }}</h1>
            <div class="flex justify-between items-center gap-2">
                <p>MHC-SF</p>
                <p>&#8734;</p>
            </div>
            <div class="flex justify-between items-center gap-2">
                <p>Mood Tracker</p>
                <p>&#8734;</p>
            </div>
            <div class="flex justify-between items-center gap-2">
                <p>Daily Missions</p>
                <p>&#8734;</p>
            </div>
            <div class="flex justify-between items-center gap-2">
                <p>Deep Cards</p>
                <p>&#8734;</p>
            </div>
            @foreach ($plan->planBenefits as $benefit)
                @if ($benefit->amount >= 1 && $benefit->benefit != 'ai_window_token')
                    <div class="flex justify-between items-center gap-2">
                        <p>{{ $benefit->benefit }}</p>
                        <p>{{ $benefit->amount }}</p>
                    </div>
                @endif
            @endforeach
            <div class="flex justify-between items-center gap-2 mb-auto">
                <p>Ment-AI</p>
                <p>
                    @if ($plan->name == 'Free')
                        Base limit
                    @elseif ($plan->name = 'Calm')
                        Extended limit
                    @else
                        Longer limit
                    @endif
                </p>
            </div>
            @if ($plan->price_idr > 0)
                <p class="mb-1 font-semibold text-xl">
                    {{ $plan->getPriceInIDR() }}
                </p>
                <button class="bg-blue-500 py-2 rounded-md w-full text-white">
                    Purchase
                </button>
            @endif
        </div>
    @endforeach
</body>

</html>