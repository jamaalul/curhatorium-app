@php
    $titles = [
        'Diskusi Proyek AI',
        'Ide Startup Baru',
        'Tips Belajar Laravel',
        'Ngobrol Santai',
        'Rencana Liburan',
        'Sharing Pengalaman Kerja',
        'Belajar Blade Template',
        'Tanya Jawab Coding',
        'Review Buku Terbaru',
        'Curhat Seputar Kuliah',
        'Tips Produktivitas',
        'Ngoding Bareng',
        'Motivasi Harian',
        'Belajar Bahasa Inggris',
        'Diskusi Film Favorit',
        'Cerita Lucu',
        'Tips Menulis',
        'Belajar Desain Grafis',
        'Diskusi Musik',
        'Curhat Relationship',
        'Pengalaman Interview Kerja',
        'Belajar Public Speaking'
    ];
@endphp
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <title>Ment-AI | Curhatorium</title>
    <link rel="stylesheet" href="{{ asset('css/global.css') }}">
    @vite('resources/css/app.css')
</head>
<body class="w-screen h-screen flex bg-gray-800">
    <div id="sidebar" class="h-screen bg-gray-800 text-white flex flex-col p-4 w-64 transition-all duration-300 ease-out">
        <!-- Header -->
        <div class="flex items-center justify-between w-full flex-none">
            <img src="{{ asset('assets/mini_logo.png') }}" alt="mini_logo" class="size-8">
            <svg id="sidebar-toggle-a" onclick="toggleSidebar()" xmlns="http://www.w3.org/2000/svg"
                class="size-6 text-gray-400 hover:text-gray-100 cursor-pointer transition-all duration-100"
                fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
            <rect width="18" height="18" x="3" y="3" rx="2"/>
            <path d="M9 3v18"/>
            </svg>
        </div>

        <!-- Buttons -->
        <div class="flex flex-col w-full mt-4 pb-2 border-b border-gray-700 flex-none">
            <button class="w-full hover:bg-gray-700 transition-all duration-100 flex items-center gap-2 p-2 rounded-md text-gray-300 hover:text-white">
            <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="size-5">
                <path stroke-linecap="round" stroke-linejoin="round" d="m16.862 4.487 1.687-1.688a1.875 1.875 0 1 1 2.652 2.652L10.582 16.07a4.5 4.5 0 0 1-1.897 1.13L6 18l.8-2.685a4.5 4.5 0 0 1 1.13-1.897l8.932-8.931Zm0 0L19.5 7.125M18 14v4.75A2.25 2.25 0 0 1 15.75 21H5.25A2.25 2.25 0 0 1 3 18.75V8.25A2.25 2.25 0 0 1 5.25 6H10" />
            </svg>
            Chat baru
            </button>

            <button class="w-full hover:bg-gray-700 transition-all duration-100 flex items-center gap-2 p-2 rounded-md text-gray-300 hover:text-white">
            <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="size-5">
                <path stroke-linecap="round" stroke-linejoin="round" d="m21 21-5.197-5.197m0 0A7.5 7.5 0 1 0 5.196 5.196a7.5 7.5 0 0 0 10.607 10.607Z" />
            </svg>
            Cari chat
            </button>
        </div>

        <!-- Chat list (fills remaining height and scrolls) -->
        <div class="flex-1 min-h-0 overflow-y-auto w-full mt-4 pb-4" style="scrollbar-width:thin; scrollbar-color:#374151 #1f2937;">
            <p class="text-gray-500 mb-2">Chat</p>
            @foreach($titles as $title)
            <div class="group w-full hover:bg-gray-700 transition-all duration-100 flex items-center gap-2 p-2 pr-7 rounded-md cursor-pointer text-gray-300 hover:text-white relative">
                <p class="truncate text-sm">{{ $title }}</p>

                <!-- show on hover via group-hover -->
                <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24"
                    stroke-width="1.5" stroke="currentColor"
                    class="size-4 absolute right-2 top-3 text-gray-500 hover:text-gray-300 cursor-pointer hidden group-hover:block"
                    onclick="deleteChat(this)">
                <path stroke-linecap="round" stroke-linejoin="round" d="m14.74 9-.346 9m-4.788 0L9.26 9m9.968-3.21c.342.052.682.107 1.022.166m-1.022-.165L18.16 19.673a2.25 2.25 0 0 1-2.244 2.077H8.084a2.25 2.25 0 0 1-2.244-2.077L4.772 5.79m14.456 0a48.108 48.108 0 0 0-3.478-.397m-12 .562c.34-.059.68-.114 1.022-.165m0 0a48.11 48.11 0 0 1 3.478-.397m7.5 0v-.916c0-1.18-.91-2.164-2.09-2.201a51.964 51.964 0 0 0-3.32 0c-1.18.037-2.09 1.022-2.09 2.201v.916m7.5 0a48.667 48.667 0 0 0-7.5 0" />
                </svg>
            </div>
            @endforeach
        </div>

        <!-- Footer / profile (fixed at bottom) -->
        <div class="flex gap-2 items-center justify-between w-full border-t border-gray-700 pt-2 flex-none">
            <div class="flex items-center gap-4">
            <img src="{{ asset('assets/avatar.png') }}" alt="avatar" class="size-8 rounded-full">
            <p class="text-sm font-semibold">John Doe</p>
            </div>
        </div>
    </div>
    <div class="w-full h-full flex flex-col justify-center items-center bg-white rounded-md relative overflow-hidden">
        <nav class="w-full h-16 bg-none absolute top-0 px-4 flex items-center gap-4 justify-between" style="background: linear-gradient(180deg,rgba(255, 255, 255, 1) 0%, rgba(255, 255, 255, 0) 100%);">
            <div class="flex items-center gap-2">
                <svg id="sidebar-toggle-b" onclick="toggleSidebar()" xmlns="http://www.w3.org/2000/svg" class="h-6 text-gray-400 hover:text-gray-700 cursor-pointer transition-all duration-100 w-0 hidden opacity-0" fill="none" viewBox="0 0 24 24"stroke="currentColor"stroke-width="2"stroke-linecap="round"stroke-linejoin="round"><rect width="18" height="18" x="3" y="3" rx="2"/><path d="M9 3v18"/></svg>
                <h3 class="text-2xl font-semibold">Ment-AI</h3>
                <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="size-6 text-[#48a6a6]">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M9.813 15.904 9 18.75l-.813-2.846a4.5 4.5 0 0 0-3.09-3.09L2.25 12l2.846-.813a4.5 4.5 0 0 0 3.09-3.09L9 5.25l.813 2.846a4.5 4.5 0 0 0 3.09 3.09L15.75 12l-2.846.813a4.5 4.5 0 0 0-3.09 3.09ZM18.259 8.715 18 9.75l-.259-1.035a3.375 3.375 0 0 0-2.455-2.456L14.25 6l1.036-.259a3.375 3.375 0 0 0 2.455-2.456L18 2.25l.259 1.035a3.375 3.375 0 0 0 2.456 2.456L21.75 6l-1.035.259a3.375 3.375 0 0 0-2.456 2.456ZM16.894 20.567 16.5 21.75l-.394-1.183a2.25 2.25 0 0 0-1.423-1.423L13.5 18.75l1.183-.394a2.25 2.25 0 0 0 1.423-1.423l.394-1.183.394 1.183a2.25 2.25 0 0 0 1.423 1.423l1.183.394-1.183.394a2.25 2.25 0 0 0-1.423 1.423Z" />
                </svg>
            </div>
            {{-- Mode options. This should show drpodown for user to change mode to 'deep', 'realistic', 'friendly' --}}
            <div class="bg-green-200 text-green-800 px-3 py-1 rounded-full shadow-sm">Friendly</div>
        </nav>
        <h1 class="text-[#48a6a6] text-4xl font-semibold mb-8">Curhatin Ajah</h1>
        <div class="flex gap-1 rounded-full shadow-md p-2 w-full md:w-2/3 border border-gray-300 hover:shadow-lg transition-all duration-100">
            <div class="h-full aspect-square rounded-full flex items-center justify-center p-1 cursor-pointer">
                <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1" stroke="currentColor" class="h-full aspect-square rounded-full text-gray-500"><path stroke-linecap="round" stroke-linejoin="round" d="M15.182 15.182a4.5 4.5 0 0 1-6.364 0M21 12a9 9 0 1 1-18 0 9 9 0 0 1 18 0ZM9.75 9.75c0 .414-.168.75-.375.75S9 10.164 9 9.75 9.168 9 9.375 9s.375.336.375.75Zm-.375 0h.008v.015h-.008V9.75Zm5.625 0c0 .414-.168.75-.375.75s-.375-.336-.375-.75.168-.75.375-.75.375.336.375.75Zm-.375 0h.008v.015h-.008V9.75Z" /></svg>
            </div>
            <input type="text" placeholder="Ceritakan apa saja..." class="w-full py-2 px-0 border-none focus:outline-none focus:ring-0">
            <div class="h-full aspect-square bg-[#48a6a6] rounded-full flex items-center justify-center p-1 cursor-pointer hover:bg-[#357979] transition-all duration-100">
                <svg xmlns="http://www.w3.org/2000/svg" class="h-full aspect-square rounded-full text-white" viewBox="0 0 20 20" fill="#ffffff"><g fill="#ffffff" fill-rule="evenodd" clip-rule="evenodd"><path d="M5.232 8.974a1 1 0 0 1 .128-1.409l4-3.333a1 1 0 1 1 1.28 1.536l-4 3.334a1 1 0 0 1-1.408-.128Z"/><path d="M14.768 8.974a1 1 0 0 1-1.408.128l-4-3.334a1 1 0 1 1 1.28-1.536l4 3.333a1 1 0 0 1 .128 1.409Z"/><path d="M10 6a1 1 0 0 1 1 1v8a1 1 0 1 1-2 0V7a1 1 0 0 1 1-1Z"/></g></svg>
            </div>
        </div>
    </div>

    <script>
        function toggleSidebar() {
            const sidebar = document.getElementById('sidebar');
            const toggleA = document.getElementById('sidebar-toggle-a');
            const toggleB = document.getElementById('sidebar-toggle-b');
            if (sidebar.style.width === '0px') {
                sidebar.style.width = '256px'; 
                sidebar.style.padding = '16px';
                toggleA.classList.remove('hidden');
                toggleB.style.width = '0px';
                toggleB.style.opacity = '0';
                toggleB.classList.add('hidden');
            } else {
                sidebar.style.width = '0px';
                sidebar.style.padding = '0px';
                toggleA.classList.add('hidden');
                toggleB.classList.remove('hidden');
                toggleB.style.width = '24px';
                toggleB.style.opacity = '1';
            }
        }

        function deleteChat(element) {
            // make chat deletion here (show confirmation modal first)
            element.parentElement.remove();
        }
    </script>
</body>
</html>