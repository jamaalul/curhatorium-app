@php
    $titles = $chats->sortByDesc(function ($chat) use ($activeChat) {
        return $chat->identifier === $activeChat->identifier;
    });
    $mode = $activeChat->mode;
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
    <style>
        /* Responsive font sizes */
        html { font-size: 16px; }
        @media (max-width: 640px) { html { font-size: 14px; } }
        @media (min-width: 640px) and (max-width: 1024px) { html { font-size: 15px; } }
        @media (min-width: 1024px) { html { font-size: 16px; } }

        /* Sidebar transitions for mobile */
        #sidebar {
            width: 256px;
            padding: 16px;
        }
        @media (max-width: 768px) {
            #sidebar {
                width: 0px;
                padding: 0px;
                position: fixed;
                z-index: 40;
                left: 0;
                top: 0;
                height: 100vh;
                background: #1f2937;
                transition: width 0.3s, padding 0.3s;
            }
        }
        /* Overlay for sidebar on mobile */
        #sidebar-overlay {
            display: none;
        }
        @media (max-width: 768px) {
            #sidebar-overlay.active {
                display: block;
                position: fixed;
                z-index: 30;
                left: 0;
                top: 0;
                width: 100vw;
                height: 100vh;
                background: rgba(0,0,0,0.4);
            }
        }

        .typing-cursor::after {
            content: '▋';
            animation: blink 1s step-end infinite;
        }

        @keyframes blink {
            from, to { color: transparent; }
            50% { color: black; }
        }
    </style>
    <script src="https://cdn.jsdelivr.net/npm/marked/marked.min.js"></script>
</head>
<body class="w-screen h-screen flex bg-gray-800">
    <div id="sidebar-overlay"></div>
    <div id="sidebar" class="bg-gray-800 text-white flex flex-col p-4 w-64 transition-all duration-300 ease-out">
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
            <button class="w-full hover:bg-gray-700 transition-all duration-100 flex items-center gap-2 p-2 rounded-md text-gray-300 hover:text-white text-base md:text-lg" onclick="window.location.href = '{{ route('chatbot') }}'">
            <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="size-5">
                <path stroke-linecap="round" stroke-linejoin="round" d="m16.862 4.487 1.687-1.688a1.875 1.875 0 1 1 2.652 2.652L10.582 16.07a4.5 4.5 0 0 1-1.897 1.13L6 18l.8-2.685a4.5 4.5 0 0 1 1.13-1.897l8.932-8.931Zm0 0L19.5 7.125M18 14v4.75A2.25 2.25 0 0 1 15.75 21H5.25A2.25 2.25 0 0 1 3 18.75V8.25A2.25 2.25 0 0 1 5.25 6H10" />
            </svg>
            Chat baru
            </button>

            <button class="w-full hover:bg-gray-700 transition-all duration-100 flex items-center gap-2 p-2 rounded-md text-gray-300 hover:text-white text-base md:text-lg">
            <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="size-5">
                <path stroke-linecap="round" stroke-linejoin="round" d="m21 21-5.197-5.197m0 0A7.5 7.5 0 1 0 5.196 5.196a7.5 7.5 0 0 0 10.607 10.607Z" />
            </svg>
            Cari chat
            </button>
        </div>

        <!-- Chat list (fills remaining height and scrolls) -->
        <div class="flex-1 min-h-0 overflow-y-auto w-full mt-4 pb-4" style="scrollbar-width:thin; scrollbar-color:#ffffff #1f2937;">
            <p class="text-gray-500 mb-2 text-base md:text-lg">Chat</p>
            @foreach($titles as $title)
            <div class="group w-full transition-all duration-100 flex items-center gap-2 p-2 pr-7 rounded-md cursor-pointer relative @if($title->identifier == $activeChat->identifier) bg-gray-700 text-white @else text-gray-300 hover:bg-gray-700 hover:text-white @endif" onclick="window.location.href = '{{ route('chatbot.chat', $title->identifier) }}'">
                <p class="truncate text-sm md:text-base">{{ $title->title }}</p>
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
        <div class="flex gap-2 items-center justify-between w-full border-t border-gray-700 pt-2 flex-none transition-all duration-300 ease-out">
            <div class="flex items-center gap-4">
            <img src="{{ $user->profile_picture ?? asset('assets/profile_pict.svg') }}" alt="avatar" class="size-8 rounded-full">
            <p class="text-sm md:text-base font-semibold">{{ $user->username }}</p>
            </div>
        </div>
    </div>
    <div class="w-full h-full flex flex-col bg-white rounded-md relative overflow-hidden">
        <nav class="w-full h-16 bg-none absolute top-0 px-4 flex items-center gap-4 justify-between" style="background: linear-gradient(180deg,rgba(255, 255, 255, 1) 0%, rgba(255, 255, 255, 0) 100%);">
            <div class="flex items-center gap-2">
                <svg id="sidebar-toggle-b" onclick="toggleSidebar()" xmlns="http://www.w3.org/2000/svg" class="h-6 text-gray-400 hover:text-gray-700 cursor-pointer transition-all duration-100 w-0 hidden opacity-0" fill="none" viewBox="0 0 24 24"stroke="currentColor"stroke-width="2"stroke-linecap="round"stroke-linejoin="round"><rect width="18" height="18" x="3" y="3" rx="2"/><path d="M9 3v18"/></svg>
                <h3 class="text-2xl md:text-3xl font-semibold">Ment-AI</h3>
                <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="size-6 text-[#48a6a6]">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M9.813 15.904 9 18.75l-.813-2.846a4.5 4.5 0 0 0-3.09-3.09L2.25 12l2.846-.813a4.5 4.5 0 0 0 3.09-3.09L9 5.25l.813 2.846a4.5 4.5 0 0 0 3.09 3.09L15.75 12l-2.846.813a4.5 4.5 0 0 0-3.09 3.09ZM18.259 8.715 18 9.75l-.259-1.035a3.375 3.375 0 0 0-2.455-2.456L14.25 6l1.036-.259a3.375 3.375 0 0 0 2.455-2.456L18 2.25l.259 1.035a3.375 3.375 0 0 0 2.456 2.456L21.75 6l-1.035.259a3.375 3.375 0 0 0-2.456 2.456ZM16.894 20.567 16.5 21.75l-.394-1.183a2.25 2.25 0 0 0-1.423-1.423L13.5 18.75l1.183-.394a2.25 2.25 0 0 0 1.423-1.423l.394-1.183.394 1.183a2.25 2.25 0 0 0 1.423 1.423l1.183.394-1.183.394a2.25 2.25 0 0 0-1.423 1.423Z" />
                </svg>
            </div>
            <div class="relative">
            <button id="ai-mode-btn" type="button"
                @switch($mode)
                    @case('friendly')
                        class="bg-green-200 text-green-800 px-3 py-1 rounded-full shadow-sm text-base md:text-lg flex items-center gap-2"
                        @break
                    @case('professional')
                        class="bg-blue-200 text-blue-800 px-3 py-1 rounded-full shadow-sm text-base md:text-lg flex items-center gap-2"
                        @break
                    @case('empathetic')
                        class="bg-yellow-200 text-yellow-800 px-3 py-1 rounded-full shadow-sm text-base md:text-lg flex items-center gap-2"
                        @break
                    @case('motivational')
                        class="bg-purple-200 text-purple-800 px-3 py-1 rounded-full shadow-sm text-base md:text-lg flex items-center gap-2"
                        @break
                @endswitch>
                <span id="ai-mode-label">{{ ucfirst($mode) }}</span>
                <svg id="ai-mode-icon"
                @switch($mode)
                    @case('friendly')
                        class="w-4 h-4 text-green-800"
                        @break
                    @case('professional')
                        class="w-4 h-4 text-blue-800"
                        @break
                    @case('empathetic')
                        class="w-4 h-4 text-yellow-800"
                        @break
                    @case('motivational')
                        class="w-4 h-4 text-purple-800"
                        @break
                @endswitch
                fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"/>
                </svg>
            </button>
            <div id="ai-mode-dropdown" class="absolute right-0 mt-2 w-40 bg-white rounded-md shadow-lg border border-gray-200 z-50 hidden">
                <ul>
                    <li><button type="button" class="w-full text-left px-4 py-2 hover:bg-green-100 text-green-800" onclick="setAIMode('Friendly')">Friendly</button></li>
                    <li><button type="button" class="w-full text-left px-4 py-2 hover:bg-blue-100 text-blue-800" onclick="setAIMode('Professional')">Professional</button></li>
                    <li><button type="button" class="w-full text-left px-4 py-2 hover:bg-yellow-100 text-yellow-800" onclick="setAIMode('Empathetic')">Empathetic</button></li>
                    <li><button type="button" class="w-full text-left px-4 py-2 hover:bg-purple-100 text-purple-800" onclick="setAIMode('Motivational')">Motivational</button></li>
                </ul>
            </div>
        </div>

        <script>
            const aiModeBtn = document.getElementById('ai-mode-btn');
            const aiModeDropdown = document.getElementById('ai-mode-dropdown');
            const aiModeLabel = document.getElementById('ai-mode-label');
            const aiModeIcon = document.getElementById('ai-mode-icon');

            aiModeBtn.addEventListener('click', function(e) {
                e.stopPropagation();
                aiModeDropdown.classList.toggle('hidden');
            });

            function setAIMode(mode) {
                aiModeLabel.textContent = mode;
                document.getElementById('ai-mode-input').value = mode.toLowerCase();
                aiModeDropdown.classList.add('hidden');

                // reset
                aiModeBtn.className = "px-3 py-1 rounded-full shadow-sm text-base md:text-lg flex items-center gap-2";
                aiModeIcon.className = "w-4 h-4";

                // mode-specific
                if (mode === 'Friendly') {
                    aiModeBtn.classList.add("bg-green-200","text-green-800");
                    aiModeIcon.style.color = "#166534"; // green-800
                    // $mode = 'friendly';
                } else if (mode === 'Professional') {
                    aiModeBtn.classList.add("bg-blue-200","text-blue-800");
                    aiModeIcon.style.color = "#1e40af"; // blue-800
                    // $mode = 'professional';
                } else if (mode === 'Empathetic') {
                    aiModeBtn.classList.add("bg-yellow-200","text-yellow-800");
                    aiModeIcon.style.color = "#854d0e"; // yellow-800
                    // $mode = 'empathetic';
                } else if (mode === 'Motivational') {
                    aiModeBtn.classList.add("bg-purple-200","text-purple-800");
                    aiModeIcon.style.color = "#4c1d95"; // purple-800
                    // $mode = 'motivational';
                }
            }

            document.addEventListener('click', function(e) {
                if (!aiModeBtn.contains(e.target)) {
                    aiModeDropdown.classList.add('hidden');
                }
            });
        </script>
        </nav>

        <div id="chat-container" class="flex w-2/3 flex-col mx-auto overflow-y-auto p-4 pt-16 pb-24" style="scrollbar-width:thin; scrollbar-color:#d1d5db #f9fafb;">
            @foreach($messages as $message)
                <div class="flex mb-4 @if($message->role == 'user') justify-end @endif">
                    <div class="p-3 rounded-lg @if($message->role == 'user') bg-[#48a6a6] text-white @else text-black @endif">
                        <div>
                            {!! Illuminate\Support\Str::markdown($message->message) !!}
                        </div>
                    </div>
                </div>
            @endforeach
        </div>
        <div class="w-full h-fit flex items-center justify-center absolute bottom-0 py-2" style="background: linear-gradient(180deg,rgba(255, 255, 255, 0) 0%, rgba(255, 255, 255, 1) 100%);">
            <div class="h-fit w-2/3 flex flex-col items-center justify-center">
                <form id="chat-form" method="POST" class="flex gap-1 rounded-full shadow-md p-2 w-full h-full border border-gray-300 hover:shadow-lg transition-all duration-100 bg-white">
                    @csrf
                    <input type="hidden" name="mode" id="ai-mode-input" value="{{ $mode }}">
                    <div class="h-11 aspect-square rounded-full flex items-center justify-center p-1 cursor-pointer">
                        <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1" stroke="currentColor" class="h-full aspect-square rounded-full text-gray-500"><path stroke-linecap="round" stroke-linejoin="round" d="M15.182 15.182a4.5 4.5 0 0 1-6.364 0M21 12a9 9 0 1 1-18 0 9 9 0 0 1 18 0ZM9.75 9.75c0 .414-.168.75-.375.75S9 10.164 9 9.75 9.168 9 9.375 9s.375.336.375.75Zm-.375 0h.008v.015h-.008V9.75Zm5.625 0c0 .414-.168.75-.375.75s-.375-.336-.375-.75.168-.75.375-.75.375.336.375.75Zm-.375 0h.008v.015h-.008V9.75Z" /></svg>
                    </div>
                    <input type="text" name="message" id="message-input" placeholder="Ceritakan apa saja..." class="w-full py-2 px-0 border-none focus:outline-none focus:ring-0 text-base md:text-lg">
                    <button id="send-button" type="submit" class="size-11 aspect-square bg-[#48a6a6] rounded-full flex items-center justify-center p-2 cursor-pointer hover:bg-[#357979] transition-all duration-100">
                        <svg xmlns="http://www.w3.org/2000/svg" class="h-full aspect-square rounded-full text-white" viewBox="0 0 20 20" fill="#ffffff"><g fill="#ffffff" fill-rule="evenodd" clip-rule="evenodd"><path d="M5.232 8.974a1 1 0 0 1 .128-1.409l4-3.333a1 1 0 1 1 1.28 1.536l-4 3.334a1 1 0 0 1-1.408-.128Z"/><path d="M14.768 8.974a1 1 0 0 1-1.408.128l-4-3.334a1 1 0 1 1 1.28-1.536l4 3.333a1 1 0 0 1 .128 1.409Z"/><path d="M10 6a1 1 0 0 1 1 1v8a1 1 0 1 1-2 0V7a1 1 0 0 1 1-1Z"/></g></svg>
                    </button>
                </form>
                <p class="text-sm mt-2 text-gray-500">Respon Ment-AI bisa saja keliru. Selalu verifikasi informasi dan utamakan konsultasi profesional.</p>
            </div>
        </div>
    </div>

    <script>
        // Responsive sidebar logic
        function isMobile() {
            return window.innerWidth <= 768;
        }

        function openSidebar() {
            const sidebar = document.getElementById('sidebar');
            const overlay = document.getElementById('sidebar-overlay');
            const toggleA = document.getElementById('sidebar-toggle-a');
            const toggleB = document.getElementById('sidebar-toggle-b');
            sidebar.style.width = '256px';
            sidebar.style.padding = '16px';
            overlay.classList.add('active');
            toggleA.classList.remove('hidden');
            toggleB.style.width = '0px';
            toggleB.style.opacity = '0';
            toggleB.classList.add('hidden');
        }

        function closeSidebar() {
            const sidebar = document.getElementById('sidebar');
            const overlay = document.getElementById('sidebar-overlay');
            const toggleA = document.getElementById('sidebar-toggle-a');
            const toggleB = document.getElementById('sidebar-toggle-b');
            sidebar.style.width = '0px';
            sidebar.style.paddingInline = '0px';
            overlay.classList.remove('active');
            toggleA.classList.add('hidden');
            toggleB.classList.remove('hidden');
            toggleB.style.width = '24px';
            toggleB.style.opacity = '1';
        }

        function toggleSidebar() {
            const sidebar = document.getElementById('sidebar');
            if (sidebar.style.width === '0px' || sidebar.style.width === '') {
                openSidebar();
            } else {
                closeSidebar();
            }
        }

        // Overlay click closes sidebar
        document.getElementById('sidebar-overlay').addEventListener('click', closeSidebar);

        // Set sidebar default state on load and resize
        function setSidebarDefault() {
            const sidebar = document.getElementById('sidebar');
            const overlay = document.getElementById('sidebar-overlay');
            const toggleA = document.getElementById('sidebar-toggle-a');
            const toggleB = document.getElementById('sidebar-toggle-b');
            if (isMobile()) {
                sidebar.style.width = '0px';
                sidebar.style.padding = '0px';
                overlay.classList.remove('active');
                toggleA.classList.add('hidden');
                toggleB.classList.remove('hidden');
                toggleB.style.width = '24px';
                toggleB.style.opacity = '1';
            } else {
                sidebar.style.width = '256px';
                sidebar.style.padding = '16px';
                overlay.classList.remove('active');
                toggleA.classList.remove('hidden');
                toggleB.style.width = '0px';
                toggleB.style.opacity = '0';
                toggleB.classList.add('hidden');
            }
        }

        window.addEventListener('resize', setSidebarDefault);
        window.addEventListener('DOMContentLoaded', setSidebarDefault);

        function deleteChat(element) {
            element.parentElement.remove();
        }

        const chatContainer = document.getElementById('chat-container');
        const chatForm = document.getElementById('chat-form');
        const messageInput = document.getElementById('message-input');

        function appendMessage(message, role) {
            const messageElement = document.createElement('div');
            messageElement.classList.add('flex', 'mb-4');
            if (role === 'user') {
                messageElement.classList.add('justify-end');
            }

            const messageBubble = document.createElement('div');
            messageBubble.classList.add('p-3', 'rounded-lg');
            if (role === 'user') {
                messageBubble.classList.add('bg-[#48a6a6]', 'text-white');
            } else {
                messageBubble.classList.add('text-[#222222]');
            }

            messageBubble.innerHTML = marked.parse(message);
            messageElement.appendChild(messageBubble);
            chatContainer.appendChild(messageElement);
            chatContainer.scrollTop = chatContainer.scrollHeight;
        }

        chatForm.addEventListener('submit', async (e) => {
            e.preventDefault();
            const message = messageInput.value;
            if (!message.trim()) return;

            appendMessage(message, 'user');
            messageInput.value = '';

            const messageBubble = document.createElement('div');
            messageBubble.classList.add('p-3', 'rounded-lg', 'text-[#222222]');
            const messageText = document.createElement('div');
            messageBubble.appendChild(messageText);

            const messageElement = document.createElement('div');
            messageElement.classList.add('flex', 'mb-4');
            messageElement.appendChild(messageBubble);
            chatContainer.appendChild(messageElement);

            let fullResponse = '';
            const eventSource = new EventSource('{{ route('api.chatbot.stream', $activeChat->identifier) }}?message=' + encodeURIComponent(message));

            eventSource.onmessage = function(event) {
                const data = JSON.parse(event.data);

                if (data.done) {
                    eventSource.close();
                    if (fullResponse.trim()) {
                        fetch('{{ route('api.chatbot.save', $activeChat->identifier) }}', {
                            method: 'POST',
                            headers: {
                                'Content-Type': 'application/json',
                                'X-CSRF-TOKEN': '{{ csrf_token() }}'
                            },
                            body: JSON.stringify({ message: fullResponse })
                        });
                    }
                    return;
                }

                if (data.text) {
                    fullResponse += data.text;
                    messageText.innerHTML = marked.parse(fullResponse);
                    chatContainer.scrollTop = chatContainer.scrollHeight;
                }
            };

            eventSource.onerror = function(err) {
                console.error("EventSource failed:", err);
                eventSource.close();
            };
        });
    </script>
</body>
</html>