@extends('layouts.app')

@section('title', ($conversation->title ?? 'MentAI Chat') . ' | Curhatorium')

@section('head')
    @vite('resources/css/app.css')
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Bricolage+Grotesque:opsz,wght@12..96,500;600;700&family=DM+Sans:ital,opsz,wght@0,9..40,400;500;600;700;1,9..40,400&display=swap" rel="stylesheet">
    <script src="https://cdn.jsdelivr.net/npm/marked/marked.min.js"></script>
    <style>
        .font-bricolage {
            font-family: 'Bricolage Grotesque', sans-serif;
        }
        .font-dmsans {
            font-family: 'DM Sans', sans-serif;
        }
        /* Hide scrollbars everywhere while maintaining scroll functionality */
        .custom-scrollbar::-webkit-scrollbar,
        textarea::-webkit-scrollbar,
        *::-webkit-scrollbar {
            display: none !important;
            width: 0 !important;
            height: 0 !important;
        }
        .custom-scrollbar,
        textarea,
        * {
            -ms-overflow-style: none !important;  /* IE and Edge */
            scrollbar-width: none !important;  /* Firefox */
        }
        .mentai-sidebar {
            width: 260px;
            min-width: 260px;
            max-width: 260px;
            background-color: #F4F4F5;
            border-right: 1px solid #E4E4E7;
            height: 100vh;
            display: flex;
            flex-direction: column;
            box-sizing: border-box;
            user-select: none;
            transition: width 0.25s ease, min-width 0.25s ease, max-width 0.25s ease, transform 0.25s ease;
            z-index: 40;
            overflow: hidden;
        }

        .mentai-sidebar.is-collapsed {
            width: 58px;
            min-width: 58px;
            max-width: 58px;
        }

        .mentai-sidebar-action-btn {
            width: 30px;
            height: 30px;
            border-radius: 8px;
            background-color: #FFFFFF;
            border: none;
            display: flex;
            align-items: center;
            justify-content: center;
            cursor: pointer;
            color: #09090B;
            transition: all 0.2s ease;
        }
        .mentai-sidebar-action-btn:hover {
            color: #00BBA7 !important;
        }
    </style>
@endsection

@section('content')
    <div class="flex h-screen w-screen overflow-hidden bg-[#F4F4F5] font-dmsans text-[#1E1E1E]" 
         x-data="mentaiShow('{{ $conversation->id }}')" 
         x-init="initChat()">

        <!-- Mobile Sidebar Backdrop Overlay -->
        <div x-show="sidebarOpen" 
            x-transition:enter="transition-opacity ease-out duration-300"
            x-transition:enter-start="opacity-0"
            x-transition:enter-end="opacity-100"
            x-transition:leave="transition-opacity ease-in duration-200"
            x-transition:leave-start="opacity-100"
            x-transition:leave-end="opacity-0"
            @click="sidebarOpen = false"
            class="md:hidden z-30 fixed inset-0 bg-black/40 backdrop-blur-xs" 
            style="display: none;"></div>

        <!-- ================= LEFT SIDEBAR ================= -->
        <aside class="mentai-sidebar"
            :class="{
                '-translate-x-full md:translate-x-0': !sidebarOpen && !sidebarCollapsed,
                'translate-x-0': sidebarOpen,
                'is-collapsed': sidebarCollapsed
            }">
            
            <!-- Full Expanded Content (when NOT collapsed) -->
            <div x-show="!sidebarCollapsed" class="flex flex-col h-full py-6 px-3.5 justify-between w-[260px]" style="box-sizing: border-box;">
                <div class="flex flex-col gap-4 flex-1 min-h-0">
                    
                    <!-- 1. Header: Mini Logo + Curhatorium + Right Action Buttons -->
                    <div class="flex items-center justify-between">
                        <a href="{{ route('dashboard') }}" class="flex items-center gap-2 hover:opacity-80 transition-opacity">
                            <img src="{{ asset('assets/mini_logo.png') }}" alt="Curhatorium" class="w-6 h-6 object-contain" />
                            <span class="font-bricolage font-bold text-[16px] leading-6 tracking-[-0.02em] text-[#1E1E1E]">Curhatorium</span>
                        </a>
                        
                        <div class="flex items-center gap-1.5">
                            <!-- Search Icon Button (Action 1) -->
                            <button type="button" @click="toggleSearch()" title="Cari percakapan" class="mentai-sidebar-action-btn">
                                <svg style="width: 16px; height: 16px;" viewBox="0 0 16 16" fill="none" xmlns="http://www.w3.org/2000/svg">
                                    <path d="M7.66732 14.4997C3.90065 14.4997 0.833984 11.433 0.833984 7.66634C0.833984 3.89967 3.90065 0.833008 7.66732 0.833008C11.434 0.833008 14.5007 3.89967 14.5007 7.66634C14.5007 11.433 11.434 14.4997 7.66732 14.4997ZM7.66732 1.83301C4.44732 1.83301 1.83398 4.45301 1.83398 7.66634C1.83398 10.8797 4.44732 13.4997 7.66732 13.4997C10.8873 13.4997 13.5007 10.8797 13.5007 7.66634C13.5007 4.45301 10.8873 1.83301 7.66732 1.83301Z" fill="currentColor"/>
                                    <path d="M14.6657 15.1666C14.539 15.1666 14.4123 15.12 14.3123 15.02L12.979 13.6866C12.7857 13.4933 12.7857 13.1733 12.979 12.98C13.1723 12.7866 13.4923 12.7866 13.6857 12.98L15.019 14.3133C15.2123 14.5066 15.2123 14.8266 15.019 15.02C14.919 15.12 14.7923 15.1666 14.6657 15.1666Z" fill="currentColor"/>
                                </svg>
                            </button>

                            <!-- Collapse / Toggle Button (Action 2) -->
                            <button type="button" @click="if (window.innerWidth < 768) { sidebarOpen = false; } else { sidebarCollapsed = true; }" 
                                class="mentai-sidebar-action-btn" title="Tutup sidebar">
                                <svg style="width: 16px; height: 16px;" viewBox="0 0 16 16" fill="none" xmlns="http://www.w3.org/2000/svg">
                                    <path d="M14.6459 9.99967V5.99967C14.6459 2.66634 13.3125 1.33301 9.97919 1.33301H5.97917C2.64583 1.33301 1.3125 2.66634 1.3125 5.99967V9.99967C1.3125 13.333 2.64583 14.6663 5.97917 14.6663H9.97919C13.3125 14.6663 14.6459 13.333 14.6459 9.99967Z" stroke="currentColor" stroke-linecap="round" stroke-linejoin="round"/>
                                    <path d="M5.3125 1.33301V14.6663" stroke="currentColor" stroke-linecap="round" stroke-linejoin="round"/>
                                    <path d="M9.9801 6.29297L8.27344 7.99967L9.9801 9.70634" stroke="currentColor" stroke-linecap="round" stroke-linejoin="round"/>
                                </svg>
                            </button>
                        </div>
                    </div>

                    <!-- Search Input (Toggled) -->
                    <div x-show="showSearch" x-transition class="px-1 py-0.5" style="display: none;">
                        <div class="relative w-full">
                            <input type="text" x-model="searchQuery" placeholder="Cari percakapan..." 
                                class="w-full bg-white border border-[#E4E4E7] rounded-lg px-2.5 py-1.5 text-xs text-[#1E1E1E] focus:outline-none focus:border-[#00BBA7]" />
                            <button x-show="searchQuery" @click="searchQuery = ''" class="absolute right-2 top-1.5 text-[#A1A1AA] hover:text-[#71717A] text-xs">✕</button>
                        </div>
                    </div>

                    <!-- 2. "+ New chat" Button -->
                    <a href="{{ route('mentai.index') }}"
                        class="flex items-center justify-center gap-2 bg-[#00BBA7] hover:bg-[#009e8d] text-white py-2.5 px-4 rounded-lg font-medium text-[15px] transition-all">
                        <img src="{{ asset('assets/mentai/new_chat_icon.svg') }}" alt="New Chat" class="w-5 h-5 filter brightness-0 invert" />
                        <span>New chat</span>
                    </a>

                    <!-- Conversation History List (Scrollable) -->
                    <div class="flex-1 overflow-y-auto custom-scrollbar flex flex-col gap-4 pr-1" @scroll.passive="onSidebarScroll($event)">
                        
                        <!-- Loading State -->
                        <div x-show="loadingConversations" class="py-4 text-center text-xs text-[#71717A]">
                            <svg class="animate-spin h-4 w-4 text-[#00BBA7] mx-auto mb-2" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
                                <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                                <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                            </svg>
                            Memuat riwayat...
                        </div>

                        <!-- Empty State -->
                        <div x-show="!loadingConversations && filteredConversations.length === 0" class="py-6 px-2 text-center text-xs text-[#71717A]">
                            <span x-text="searchQuery ? 'Tidak ada hasil percakapan' : 'Belum ada percakapan'"></span>
                        </div>

                        <!-- Group: Hari Ini -->
                        <div x-show="todayConversations.length > 0" class="flex flex-col gap-1 pb-3 border-b border-[#E4E4E7]">
                            <div class="px-2 py-1 text-[12px] font-normal text-[#71717A]">Hari ini</div>
                            <template x-for="conv in todayConversations" :key="conv.id">
                                <a :href="`/mental-support-chatbot/${conv.id}`"
                                    class="group flex items-center px-2 py-1.5 rounded-lg text-[14px] transition-all truncate"
                                    :class="conv.id === conversationId ? 'bg-white font-medium text-[#09090B]' : 'font-medium text-[#374151] hover:bg-white hover:text-[#09090B]'"
                                    :title="conv.title || 'Percakapan baru'">
                                    <span class="truncate" x-text="conv.title || 'Percakapan baru'"></span>
                                </a>
                            </template>
                        </div>

                        <!-- Group: Kemarin -->
                        <div x-show="yesterdayConversations.length > 0" class="flex flex-col gap-1 pb-3 border-b border-[#E4E4E7]">
                            <div class="px-2 py-1 text-[12px] font-normal text-[#71717A]">Kemarin</div>
                            <template x-for="conv in yesterdayConversations" :key="conv.id">
                                <a :href="`/mental-support-chatbot/${conv.id}`"
                                    class="group flex items-center px-2 py-1.5 rounded-lg text-[14px] transition-all truncate"
                                    :class="conv.id === conversationId ? 'bg-white font-medium text-[#09090B]' : 'font-medium text-[#374151] hover:bg-white hover:text-[#09090B]'"
                                    :title="conv.title || 'Percakapan baru'">
                                    <span class="truncate" x-text="conv.title || 'Percakapan baru'"></span>
                                </a>
                            </template>
                        </div>

                        <!-- Group: 7 Hari Terakhir -->
                        <div x-show="last7DaysConversations.length > 0" class="flex flex-col gap-1 pb-3 border-b border-[#E4E4E7]">
                            <div class="px-2 py-1 text-[12px] font-normal text-[#71717A]">7 Hari terakhir</div>
                            <template x-for="conv in last7DaysConversations" :key="conv.id">
                                <a :href="`/mental-support-chatbot/${conv.id}`"
                                    class="group flex items-center px-2 py-1.5 rounded-lg text-[14px] transition-all truncate"
                                    :class="conv.id === conversationId ? 'bg-white font-medium text-[#09090B]' : 'font-medium text-[#374151] hover:bg-white hover:text-[#09090B]'"
                                    :title="conv.title || 'Percakapan baru'">
                                    <span class="truncate" x-text="conv.title || 'Percakapan baru'"></span>
                                </a>
                            </template>
                        </div>

                        <!-- Group: Lebih Lama -->
                        <div x-show="olderConversations.length > 0" class="flex flex-col gap-1">
                            <div class="px-2 py-1 text-[12px] font-normal text-[#71717A]">Lebih lama</div>
                            <template x-for="conv in olderConversations" :key="conv.id">
                                <a :href="`/mental-support-chatbot/${conv.id}`"
                                    class="group flex items-center px-2 py-1.5 rounded-lg text-[14px] transition-all truncate"
                                    :class="conv.id === conversationId ? 'bg-white font-medium text-[#09090B]' : 'font-medium text-[#374151] hover:bg-white hover:text-[#09090B]'"
                                    :title="conv.title || 'Percakapan baru'">
                                    <span class="truncate" x-text="conv.title || 'Percakapan baru'"></span>
                                </a>
                            </template>
                        </div>

                        <!-- Lazy Loading Spinner Indicator at Bottom -->
                        <div x-show="loadingMoreConversations" class="py-2 text-center text-xs text-[#71717A]">
                            <svg class="animate-spin h-3.5 w-3.5 text-[#00BBA7] mx-auto" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
                                <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                                <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                            </svg>
                        </div>

                    </div>
                </div>

                <!-- Sidebar Bottom: Back to Dashboard -->
                <div class="pt-4 border-t border-[#E4E4E7]">
                    <a href="{{ route('dashboard') }}"
                        class="flex items-center gap-2 px-2 py-2 rounded-lg text-[#71717A] hover:text-[#1E1E1E] hover:bg-white text-[14px] font-medium transition-all">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"></path>
                        </svg>
                        <span>Kembali ke dashboard</span>
                    </a>
                </div>

            </div>

            <!-- Mini Collapsed Rail (when collapsed on Desktop) -->
            <div x-show="sidebarCollapsed" class="flex flex-col items-center justify-between h-full py-6 w-full" style="display: none;">
                <div class="flex flex-col items-center gap-4">
                    <a href="{{ route('dashboard') }}" title="Curhatorium">
                        <img src="{{ asset('assets/mini_logo.png') }}" alt="Curhatorium" class="w-6 h-6 object-contain" />
                    </a>
                    
                    <button type="button" @click="sidebarCollapsed = false" class="mentai-sidebar-action-btn" title="Buka sidebar">
                        <svg style="width: 16px; height: 16px; transform: rotate(180deg);" viewBox="0 0 16 16" fill="none" xmlns="http://www.w3.org/2000/svg">
                            <path d="M14.6459 9.99967V5.99967C14.6459 2.66634 13.3125 1.33301 9.97919 1.33301H5.97917C2.64583 1.33301 1.3125 2.66634 1.3125 5.99967V9.99967C1.3125 13.333 2.64583 14.6663 5.97917 14.6663H9.97919C13.3125 14.6663 14.6459 13.333 14.6459 9.99967Z" stroke="currentColor" stroke-linecap="round" stroke-linejoin="round"/>
                            <path d="M5.3125 1.33301V14.6663" stroke="currentColor" stroke-linecap="round" stroke-linejoin="round"/>
                            <path d="M9.9801 6.29297L8.27344 7.99967L9.9801 9.70634" stroke="currentColor" stroke-linecap="round" stroke-linejoin="round"/>
                        </svg>
                    </button>

                    <a href="{{ route('mentai.index') }}" class="mentai-sidebar-action-btn" title="New Chat">
                        <svg style="width: 16px; height: 16px; color: #00BBA7;" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M12 4v16m8-8H4"/>
                        </svg>
                    </a>
                </div>

                <a href="{{ route('dashboard') }}" class="mentai-sidebar-action-btn" title="Dashboard">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"></path>
                    </svg>
                </a>
            </div>

        </aside>

        <!-- Main Chat Area -->
        <main class="relative flex flex-col flex-1 h-full overflow-hidden bg-[#F4F4F5]">
            
            <!-- Top Control Bar (Mobile toggle) -->
            <div class="flex items-center justify-between px-4 py-3 border-b border-[#E4E4E7] bg-[#F4F4F5] md:bg-transparent">
                <div class="flex items-center gap-2">
                    <!-- Mobile Hamburger -->
                    <button type="button" @click="sidebarOpen = true" class="md:hidden p-1.5 rounded-lg text-[#09090B] hover:bg-white transition-colors cursor-pointer" title="Buka menu riwayat">
                        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16"></path>
                        </svg>
                    </button>

                    <div class="flex items-center gap-2 md:hidden">
                        <img src="{{ asset('assets/mentai/mentai_icon.svg') }}" alt="MentAI" class="w-5 h-5" />
                        <span class="font-bricolage font-semibold text-[16px] text-[#1F2937]">MentAI</span>
                    </div>
                </div>

                <div class="flex items-center gap-2">
                    <a href="{{ route('dashboard') }}" class="flex items-center gap-1.5 px-3 py-1.5 rounded-lg bg-white border border-[#E4E4E7] text-[#71717A] hover:text-[#1E1E1E] text-xs font-medium transition-colors">
                        <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"></path>
                        </svg>
                        <span class="hidden sm:inline">Dashboard</span>
                    </a>
                </div>
            </div>

            <!-- Chat Scroll Area -->
            <div class="flex-1 overflow-y-auto custom-scrollbar p-3 sm:p-6" x-ref="scrollArea">
                <div class="flex flex-col mx-auto max-w-3xl min-h-full pb-44">

                    <!-- Loading Messages Spinner -->
                    <template x-if="loadingMessages">
                        <div class="flex flex-1 justify-center items-center py-24">
                            <div class="flex flex-col items-center gap-3">
                                <svg class="w-8 h-8 text-[#00BBA7] animate-spin" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
                                    <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                                    <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                                </svg>
                                <span class="text-xs text-[#71717A]">Memuat pesan...</span>
                            </div>
                        </div>
                    </template>

                    <!-- Messages List -->
                    <div class="flex flex-col space-y-6 w-full" x-show="!loadingMessages || messages.length > 0">
                        <template x-for="message in messages" :key="message.id">
                            <div class="flex w-full" :class="message.role === 'user' ? 'justify-end' : 'justify-start'">

                                <!-- Assistant Message -->
                                <template x-if="message.role === 'assistant'">
                                    <div class="flex items-start gap-3 max-w-[90%] sm:max-w-[85%]">
                                        <!-- MentAI Avatar -->
                                        <div class="w-8 h-8 rounded-full bg-white border border-[#E4E4E7] flex items-center justify-center flex-shrink-0 mt-0.5">
                                            <img src="{{ asset('assets/mentai/mentai_icon.svg') }}" alt="MentAI" class="w-4 h-4" />
                                        </div>

                                        <div class="bg-white border border-[#E4E4E7] rounded-2xl p-4 sm:p-5 text-[#09090B] text-[15px] leading-7 font-dmsans w-full overflow-hidden">
                                            <div x-show="message.content" x-html="marked.parse(message.content || '')" class="prose prose-sm max-w-none text-[#09090B]"></div>
                                            
                                            <!-- Typing indicator -->
                                            <template x-if="!message.content && streaming && !hasStreamedText && message.id === messages[messages.length-1].id">
                                                <div class="flex items-center gap-1.5 py-1">
                                                    <div class="bg-[#00BBA7] rounded-full w-2 h-2 animate-bounce"></div>
                                                    <div class="bg-[#00BBA7] rounded-full w-2 h-2 animate-bounce" style="animation-delay: 0.15s"></div>
                                                    <div class="bg-[#00BBA7] rounded-full w-2 h-2 animate-bounce" style="animation-delay: 0.3s"></div>
                                                </div>
                                            </template>
                                        </div>
                                    </div>
                                </template>

                                <!-- User Message -->
                                <template x-if="message.role === 'user'">
                                    <div class="bg-[#00BBA7] text-white px-5 py-3 rounded-2xl rounded-tr-xs max-w-[85%] sm:max-w-[75%] text-[15px] leading-6 font-dmsans">
                                        <div x-html="message.content.replace(/\n/g, '<br>')"></div>
                                    </div>
                                </template>
                            </div>
                        </template>
                    </div>

                </div>
            </div>

            <!-- Input Area (Fixed Bottom Center) -->
            <div class="bottom-0 left-0 absolute bg-gradient-to-t from-[#F4F4F5] via-[#F4F4F5]/90 to-transparent px-4 pt-4 pb-4 sm:pb-6 w-full">
                <div class="relative mx-auto max-w-3xl flex flex-col gap-2">
                    
                    <!-- Outer Teal-accented Input Box Container -->
                    <div class="bg-[#CCFBF1] border border-[#96F7E4] rounded-2xl p-1.5 pb-2.5 flex flex-col gap-2 transition-all">
                        
                        <!-- Inner White Textarea & Send Button -->
                        <form @submit.prevent="sendMessage" class="bg-white rounded-xl p-3 sm:p-4 flex items-center justify-between gap-3 transition-colors">
                            <textarea x-model="input" x-ref="messageInput"
                                @keydown.enter.prevent="if(!loading) { sendMessage(); }" @input="autoResize()"
                                placeholder="Kirim pesan ke MentAI..."
                                rows="1"
                                :disabled="loading"
                                class="bg-transparent text-[15px] sm:text-[16px] font-medium text-[#09090B] placeholder-[#A1A1AA] leading-6 border-0 focus:ring-0 focus:outline-none w-full max-h-[140px] overflow-y-auto resize-none p-0 custom-scrollbar"></textarea>

                            <button type="submit"
                                class="flex-shrink-0 w-9 h-9 sm:w-10 sm:h-10 rounded-full flex items-center justify-center transition-all duration-200 focus:outline-none cursor-pointer"
                                :class="input.trim() === '' || loading ? 'bg-gray-100 text-gray-300 cursor-not-allowed' : 'bg-[#00BBA7] hover:bg-[#009e8d] text-white hover:scale-105 active:scale-95'"
                                :disabled="loading || input.trim() === ''"
                                title="Kirim pesan">
                                <template x-if="!loading">
                                    <img src="{{ asset('assets/mentai/send_icon.svg') }}" alt="Send" class="w-5 h-5 filter brightness-0 invert" />
                                </template>
                                <template x-if="loading">
                                    <svg class="w-5 h-5 animate-spin text-white" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
                                        <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                                        <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                                    </svg>
                                </template>
                            </button>
                        </form>

                        <!-- Bottom Info Bar inside Teal Box -->
                        <div class="flex items-center gap-1.5 px-3 pt-0.5">
                            <img src="{{ asset('assets/mentai/history_icon.svg') }}" alt="History" class="w-3.5 h-3.5" />
                            <span class="font-medium text-[12px] text-[#00BBA7]">Riwayat Tersimpan</span>
                        </div>

                    </div>

                    <!-- Disclaimer Text -->
                    <p class="text-center text-[12px] leading-4 text-[#71717A]">
                        <span class="text-[#00BBA7] font-medium">MentAI</span> dapat membuat kesalahan. Harap pertimbangkan untuk memverifikasi informasi penting.
                    </p>

                </div>
            </div>

        </main>
    </div>

    <!-- Alpine.js Component Script -->
    <script>
        function mentaiShow(conversationId) {
            return {
                conversationId: conversationId,
                messages: [],
                conversations: [],
                currentPage: 1,
                hasMorePages: true,
                loadingConversations: false,
                loadingMoreConversations: false,
                input: '',
                loading: false,
                loadingMessages: false,
                streaming: false,
                hasStreamedText: false,
                sidebarOpen: false,
                sidebarCollapsed: false,
                showSearch: false,
                searchQuery: '',

                _getCSRFToken() {
                    const meta = document.querySelector('meta[name="csrf-token"]');
                    return meta ? meta.getAttribute('content') : '';
                },

                _scrollToBottom() {
                    this.$nextTick(() => {
                        if (this.$refs.scrollArea) {
                            this.$refs.scrollArea.scrollTop = this.$refs.scrollArea.scrollHeight;
                        }
                    });
                },

                autoResize() {
                    const el = this.$refs.messageInput;
                    if (!el) return;
                    el.style.height = 'auto';
                    el.style.height = Math.min(el.scrollHeight, 140) + 'px';
                },

                toggleSearch() {
                    this.showSearch = !this.showSearch;
                    if (this.showSearch) {
                        this.$nextTick(() => {
                            const input = document.querySelector('input[x-model="searchQuery"]');
                            if (input) input.focus();
                        });
                    } else {
                        this.searchQuery = '';
                    }
                },

                async initChat() {
                    this.fetchConversations();
                    await this.fetchMessages();

                    // Check if an initial prompt was queued from index
                    const pendingRaw = sessionStorage.getItem('mentai_pending_prompt');
                    if (pendingRaw) {
                        try {
                            const pending = JSON.parse(pendingRaw);
                            if (pending.conversationId === this.conversationId && pending.message) {
                                sessionStorage.removeItem('mentai_pending_prompt');
                                await this.executeSendMessage(pending.message, true);
                            }
                        } catch (e) {
                            sessionStorage.removeItem('mentai_pending_prompt');
                            console.error('Error parsing pending prompt:', e);
                        }
                    }
                },

                async fetchConversations() {
                    this.loadingConversations = true;
                    this.currentPage = 1;
                    try {
                        const res = await fetch('{{ route("ai.conversations") }}', {
                            headers: { 'Accept': 'application/json' }
                        });
                        if (res.ok) {
                            const data = await res.json();
                            this.conversations = data.data || [];
                            this.currentPage = data.current_page || 1;
                            this.hasMorePages = !!data.next_page_url;
                        }
                    } catch (e) {
                        console.error("Error fetching conversations:", e);
                    } finally {
                        this.loadingConversations = false;
                    }
                },

                onSidebarScroll(e) {
                    const el = e.target;
                    if (el.scrollHeight - el.scrollTop - el.clientHeight < 60) {
                        this.loadMoreConversations();
                    }
                },

                async loadMoreConversations() {
                    if (this.loadingMoreConversations || !this.hasMorePages || this.loadingConversations) return;
                    this.loadingMoreConversations = true;
                    try {
                        const nextPage = this.currentPage + 1;
                        const res = await fetch(`{{ route('ai.conversations') }}?page=${nextPage}`, {
                            headers: { 'Accept': 'application/json' }
                        });
                        if (res.ok) {
                            const data = await res.json();
                            const newItems = data.data || [];
                            if (newItems.length > 0) {
                                const existingIds = new Set(this.conversations.map(c => c.id));
                                const uniqueItems = newItems.filter(c => !existingIds.has(c.id));
                                this.conversations.push(...uniqueItems);
                                this.currentPage = data.current_page || nextPage;
                                this.hasMorePages = !!data.next_page_url;
                            } else {
                                this.hasMorePages = false;
                            }
                        }
                    } catch (e) {
                        console.error("Error loading more conversations:", e);
                    } finally {
                        this.loadingMoreConversations = false;
                    }
                },

                get filteredConversations() {
                    if (!this.searchQuery.trim()) {
                        return this.conversations;
                    }
                    const q = this.searchQuery.toLowerCase();
                    return this.conversations.filter(c => (c.title || 'Percakapan baru').toLowerCase().includes(q));
                },

                get todayConversations() {
                    const today = new Date();
                    today.setHours(0, 0, 0, 0);
                    return this.filteredConversations.filter(c => {
                        if (!c.created_at) return false;
                        const d = new Date(c.created_at);
                        d.setHours(0, 0, 0, 0);
                        return d.getTime() === today.getTime();
                    });
                },

                get yesterdayConversations() {
                    const today = new Date();
                    today.setHours(0, 0, 0, 0);
                    const yesterday = new Date(today);
                    yesterday.setDate(yesterday.getDate() - 1);
                    return this.filteredConversations.filter(c => {
                        if (!c.created_at) return false;
                        const d = new Date(c.created_at);
                        d.setHours(0, 0, 0, 0);
                        return d.getTime() === yesterday.getTime();
                    });
                },

                get last7DaysConversations() {
                    const today = new Date();
                    today.setHours(0, 0, 0, 0);
                    const sevenDaysAgo = new Date(today);
                    sevenDaysAgo.setDate(sevenDaysAgo.getDate() - 7);
                    const yesterday = new Date(today);
                    yesterday.setDate(yesterday.getDate() - 1);

                    return this.filteredConversations.filter(c => {
                        if (!c.created_at) return false;
                        const d = new Date(c.created_at);
                        d.setHours(0, 0, 0, 0);
                        return d.getTime() < yesterday.getTime() && d.getTime() >= sevenDaysAgo.getTime();
                    });
                },

                get olderConversations() {
                    const today = new Date();
                    today.setHours(0, 0, 0, 0);
                    const sevenDaysAgo = new Date(today);
                    sevenDaysAgo.setDate(sevenDaysAgo.getDate() - 7);

                    return this.filteredConversations.filter(c => {
                        if (!c.created_at) return true;
                        const d = new Date(c.created_at);
                        d.setHours(0, 0, 0, 0);
                        return d.getTime() < sevenDaysAgo.getTime();
                    });
                },

                async fetchMessages() {
                    this.loadingMessages = true;
                    try {
                        const res = await fetch(`/ai/${this.conversationId}/messages`, {
                            headers: { 'Accept': 'application/json' }
                        });
                        if (res.ok) {
                            this.messages = await res.json();
                            this._scrollToBottom();
                        }
                    } catch (e) {
                        console.error("Error fetching messages:", e);
                    } finally {
                        this.loadingMessages = false;
                    }
                },

                async sendMessage() {
                    if (this.loading || !this.input.trim()) return;

                    const userText = this.input.trim();
                    this.input = '';
                    this.$nextTick(() => this.autoResize());

                    const isFirstExchange = this.messages.length === 0;
                    await this.executeSendMessage(userText, isFirstExchange);
                },

                async executeSendMessage(userText, isFirstExchange = false) {
                    this.messages.push({
                        id: Date.now().toString(),
                        role: 'user',
                        content: userText
                    });

                    const assistantMessageId = (Date.now() + 1).toString();
                    this.messages.push({
                        id: assistantMessageId,
                        role: 'assistant',
                        content: ''
                    });

                    this.loading = true;
                    this.streaming = true;
                    this.hasStreamedText = false;
                    this._scrollToBottom();

                    try {
                        const response = await fetch(`/ai/${this.conversationId}/message`, {
                            method: 'POST',
                            headers: {
                                'Content-Type': 'application/json',
                                'X-CSRF-TOKEN': this._getCSRFToken(),
                                'Accept': 'text/event-stream'
                            },
                            body: JSON.stringify({ message: userText })
                        });

                        if (!response.ok) {
                            let errorMsg = 'NETWORK_ERROR';
                            if (response.status === 429) {
                                try {
                                    const errData = await response.json();
                                    if (errData && errData.message) {
                                        errorMsg = 'QUOTA_EXCEEDED:' + errData.message;
                                    }
                                } catch (e) { }
                            }
                            throw new Error(errorMsg);
                        }

                        if (!response.body) throw new Error('ReadableStream not supported');

                        const reader = response.body.getReader();
                        const decoder = new TextDecoder('utf-8');
                        let buffer = '';

                        while (true) {
                            const { done, value } = await reader.read();
                            if (done) break;

                            buffer += decoder.decode(value, { stream: true });
                            const parts = buffer.split('\n\n');
                            buffer = parts.pop() || '';

                            for (const part of parts) {
                                const lines = part.split('\n');
                                for (const line of lines) {
                                    if (line.startsWith('data:')) {
                                        const jsonStr = line.replace(/^data:/, '').trim();
                                        if (!jsonStr || jsonStr === '[DONE]') continue;

                                        try {
                                            const event = JSON.parse(jsonStr);
                                            const chunkText = event.text || event.delta || event.content || '';
                                            if (chunkText) {
                                                this.hasStreamedText = true;
                                                const messageIndex = this.messages.findIndex(m => m.id === assistantMessageId);
                                                if (messageIndex !== -1) {
                                                    this.messages[messageIndex].content += chunkText;
                                                    this._scrollToBottom();
                                                }
                                            }
                                        } catch (e) {
                                            // Skip malformed chunk
                                        }
                                    }
                                }
                            }
                        }
                    } catch (error) {
                        console.error('MentAI Stream Error:', error);
                        const messageIndex = this.messages.findIndex(m => m.id === assistantMessageId);
                        if (messageIndex !== -1) {
                            let displayMsg = "Maaf, terjadi kesalahan. Coba lagi ya.";
                            if (error.message && error.message.startsWith('QUOTA_EXCEEDED:')) {
                                displayMsg = error.message.substring(15);
                            }
                            this.messages[messageIndex].content = displayMsg;
                            this._scrollToBottom();
                        }
                    } finally {
                        this.loading = false;
                        this.streaming = false;

                        if (isFirstExchange) {
                            const assistantContent = this.messages.find(m => m.id === assistantMessageId)?.content || '';
                            this.generateTitle(userText, assistantContent);
                        }
                    }
                },

                async generateTitle(userMessage, assistantResponse) {
                    try {
                        const res = await fetch(`/ai/${this.conversationId}/generate-title`, {
                            method: 'POST',
                            headers: {
                                'Content-Type': 'application/json',
                                'X-CSRF-TOKEN': this._getCSRFToken(),
                                'Accept': 'application/json'
                            },
                            body: JSON.stringify({
                                message: userMessage.substring(0, 500),
                                response: assistantResponse.substring(0, 500)
                            })
                        });

                        if (!res.ok) return;

                        const data = await res.json();
                        const conv = this.conversations.find(c => c.id === this.conversationId);
                        if (conv) {
                            conv.title = data.title;
                        }
                    } catch (e) {
                        console.error('Title generation failed:', e);
                    }
                }
            };
        }
    </script>
@endsection
