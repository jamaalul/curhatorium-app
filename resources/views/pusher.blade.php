<x-layout title="Pusher Test" bodyClass="bg-gray-50 text-gray-800">
    <x-slot:head>
        @vite('resources/js/app.js')
        <link rel="stylesheet" href="{{ asset('css/global.css') }}">
    </x-slot:head>

    <div class="max-w-xl mx-auto p-4">
        <h1 class="text-2xl font-bold text-[#48A6A6] mb-2">PUSHER</h1>
    </div>
</x-layout>