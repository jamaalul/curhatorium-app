<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>Reschedules | {{ $professional->name }}</title>
    @vite('resources/css/app.css')
    <link rel="stylesheet" href="{{ asset('css/global.css') }}">
</head>

<body class="bg-gray-100">
    <div class="flex flex-col md:flex-row h-screen">
        <!-- Sidebar -->
        <aside class="w-full md:w-64 bg-white shadow-md flex-shrink-0">
            <div class="p-4 md:p-6 flex justify-between md:block">
                <div>
                    <h1 class="text-xl md:text-2xl font-bold text-gray-800">Curhatorium</h1>
                    <p class="text-sm text-gray-500">Fasilitator Panel</p>
                </div>
                <button id="mobile-menu-button" class="md:hidden">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M4 6h16M4 12h16m-7 6h7"></path>
                    </svg>
                </button>
            </div>
            <nav id="mobile-menu" class="mt-2 md:mt-6 hidden md:block">
                <a href="{{ route('professional.dashboard', $professional->id) }}"
                    class="block px-4 py-2 md:px-6 md:py-3 text-gray-700 font-semibold bg-gray-200">
                    <svg class="inline-block w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M3 10h18M3 14h18m-9-4v8m-7 0h14a2 2 0 002-2V8a2 2 0 00-2-2H5a2 2 0 00-2 2v8a2 2 0 002 2z">
                        </path>
                    </svg>
                    Dashboard
                </a>
                <a href="{{ route('professional.reschedule.list', $professional->id) }}"
                    class="block px-4 py-2 md:px-6 md:py-3 text-gray-600 hover:bg-gray-100">
                    <svg class="inline-block w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z">
                        </path>
                    </svg>
                    Reschedules
                </a>
                <form method="POST" action="{{ route('professional.dashboard.logout') }}">
                    @csrf
                    <button type="submit"
                        class="w-full text-left block px-4 py-2 md:px-6 md:py-3 text-gray-600 hover:bg-gray-100">
                        <svg class="inline-block w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M17 16l4-4m0 0l-4-4m4 4H7m6 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h4a3 3 0 013 3v1">
                            </path>
                        </svg>
                        Logout
                    </button>
                </form>
            </nav>
        </aside>

        <!-- Main Content -->
        <main class="flex-1 p-4 md:p-8 overflow-y-auto">
            <div class="max-w-7xl mx-auto">
                <header class="flex flex-col md:flex-row justify-between md:items-center mb-6 md:mb-8">
                    <div>
                        <h2 class="text-2xl md:text-3xl font-bold text-gray-800">Reschedules</h2>
                        @if (session('success'))
                            <div class="mt-2 p-3 bg-green-100 border border-green-400 text-green-700 rounded">
                                {{ session('success') }}
                            </div>
                        @endif
                        @if (session('error'))
                            <div class="mt-2 p-3 bg-red-100 border border-red-400 text-red-700 rounded">
                                {{ session('error') }}
                            </div>
                        @endif
                    </div>
                    <div class="flex items-center gap-4 mt-4 md:mt-0">
                        <div class="text-right">
                            <p class="font-semibold">{{ $professional->name }}</p>
                            <p class="text-sm text-gray-500">{{ $professional->title }}</p>
                        </div>
                        <img src="{{ asset('assets/profile_pict.svg') }}" alt="Avatar"
                            class="w-12 h-12 rounded-full">
                    </div>
                </header>

                <!-- Reschedules List -->
                <div class="bg-white p-4 md:p-6 rounded-lg shadow-md">
                    <h3 class="text-lg md:text-xl font-bold mb-4">Reschedule History</h3>
                    <div class="overflow-x-auto">
                        <table class="w-full text-sm text-left text-gray-500">
                            <thead class="text-xs text-gray-700 uppercase bg-gray-50">
                                <tr>
                                    <th scope="col" class="px-4 py-3">Client</th>
                                    <th scope="col" class="px-4 py-3">Original Date</th>
                                    <th scope="col" class="px-4 py-3">New Date</th>
                                    <th scope="col" class="px-4 py-3">Status</th>
                                    <th scope="col" class="px-4 py-3">Created</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($reschedules as $reschedule)
                                    <tr class="bg-white border-b">
                                        <td class="px-4 py-4">
                                            {{ $reschedule->originalSlot->bookedBy->username ?? 'N/A' }}
                                        </td>
                                        <td class="px-4 py-4">
                                            {{ \Carbon\Carbon::parse($reschedule->originalSlot->slot_start_time)->format('d M Y, H:i') }}
                                        </td>
                                        <td class="px-4 py-4">
                                            @if ($reschedule->status === 'accepted' && $reschedule->rescheduleSlots->where('is_selected', true)->first())
                                                {{ \Carbon\Carbon::parse($reschedule->rescheduleSlots->where('is_selected', true)->first()->slot->slot_start_time)->format('d M Y, H:i') }}
                                            @else
                                                N/A
                                            @endif
                                        </td>
                                        <td class="px-4 py-4">
                                            <span
                                                class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full
                                                @if ($reschedule->status === 'pending') bg-yellow-100 text-yellow-800
                                                @elseif($reschedule->status === 'accepted') bg-green-100 text-green-800
                                                @elseif($reschedule->status === 'cancelled') bg-gray-100 text-gray-800
                                                @elseif($reschedule->status === 'expired') bg-red-100 text-red-800 @endif">
                                                @php
                                                    $statusText = match ($reschedule->status) {
                                                        'pending' => 'Pending',
                                                        'accepted' => 'Accepted',
                                                        'cancelled' => 'Cancelled',
                                                        'expired' => 'Expired',
                                                        default => ucfirst($reschedule->status),
                                                    };
                                                @endphp
                                                {{ $statusText }}
                                            </span>
                                        </td>
                                        <td class="px-4 py-4">
                                            {{ \Carbon\Carbon::parse($reschedule->created_at)->format('d M Y, H:i') }}
                                        </td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="5" class="px-4 py-4 text-center text-gray-500">
                                            No reschedules found.
                                        </td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>

                    <div class="mt-4">
                        {{ $reschedules->links() }}
                    </div>
                </div>
            </div>
        </main>
    </div>

    <script>
        document.getElementById('mobile-menu-button').addEventListener('click', function() {
            document.getElementById('mobile-menu').classList.toggle('hidden');
        });
    </script>
</body>

</html>
