@extends('layouts.app')

@section('title', 'MentAI - Mental Support Chatbot | Curhatorium')

@section('head')
    @vite('resources/css/app.css')
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Bricolage+Grotesque:opsz,wght@12..96,500;600;700;800&family=DM+Sans:ital,opsz,wght@0,9..40,400;500;600;700;1,9..40,400&display=swap" rel="stylesheet">
    <style>
        .font-bricolage {
            font-family: 'Bricolage Grotesque', sans-serif !important;
        }
        .font-dmsans {
            font-family: 'DM Sans', sans-serif !important;
        }
        
        /* Hide scrollbars everywhere while maintaining scroll functionality */
        .mentai-scrollbar::-webkit-scrollbar,
        textarea::-webkit-scrollbar,
        *::-webkit-scrollbar {
            display: none !important;
            width: 0 !important;
            height: 0 !important;
        }
        .mentai-scrollbar,
        textarea,
        * {
            -ms-overflow-style: none !important;  /* IE and Edge */
            scrollbar-width: none !important;  /* Firefox */
        }

        /* Scoped Layout Rules to Guarantee Exact Dimensions */
        .mentai-page-wrap {
            display: flex;
            width: 100vw;
            height: 100vh;
            overflow: hidden;
            background-color: #F4F4F5;
            font-family: 'DM Sans', sans-serif;
            color: #1E1E1E;
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

        .mentai-main-stage {
            flex: 1;
            height: 100vh;
            overflow: hidden;
            background-color: #F4F4F5;
            display: flex;
            flex-direction: column;
            align-items: center;
            justify-content: center;
            padding: 24px 32px;
            box-sizing: border-box;
        }

        .mentai-floating-card {
            width: 100%;
            height: 100%;
            background-color: #FFFFFF;
            border: 1px solid #E4E4E7;
            border-radius: 24px;
            padding: 36px 36px 28px;
            display: flex;
            flex-direction: column;
            align-items: center;
            box-sizing: border-box;
            overflow-y: auto;
        }

        .mentai-content-wrapper {
            display: flex;
            width: 700.242px;
            max-width: 100%;
            flex-direction: column;
            align-items: center;
            gap: 128px;
            margin-top: auto;
            margin-bottom: 0;
        }

        .mentai-middle-bottom-group {
            display: flex;
            flex-direction: column;
            align-items: flex-start;
            gap: 80px;
            align-self: stretch;
            width: 100%;
        }

        .mentai-top-header {
            display: flex;
            flex-direction: column;
            align-items: center;
            text-align: center;
            width: 100%;
            max-width: 520px;
        }

        .mentai-mascot-circle {
            width: 76px;
            height: 76px;
            border-radius: 9999px;
            background-color: #FAFAFA;
            border: 1px solid #F4F4F5;
            display: flex;
            align-items: center;
            justify-content: center;
            flex-shrink: 0;
        }

        .mentai-mascot-circle img,
        .mentai-mascot-circle svg {
            width: 38px;
            height: 38px;
            object-fit: contain;
        }

        .mentai-title {
            font-family: 'Bricolage Grotesque', sans-serif !important;
            font-weight: 700;
            font-size: 32px;
            line-height: 38px;
            letter-spacing: -0.02em;
            color: #1F2937;
            margin: 18px 0 8px 0;
        }

        .mentai-subtitle {
            font-size: 14.5px;
            line-height: 24px;
            color: #71717A;
            margin: 0;
            max-width: 460px;
        }

        /* 3-column Prompts Grid */
        .mentai-prompts-grid {
            display: grid;
            grid-template-columns: repeat(3, 1fr);
            gap: 14px;
            width: 100%;
            box-sizing: border-box;
        }

        /* Screen Height Media Queries */
        @media (max-height: 900px) {
            .mentai-content-wrapper {
                gap: 96px;
            }
            .mentai-middle-bottom-group {
                gap: 64px;
            }
        }

        @media (max-height: 750px) {
            .mentai-content-wrapper {
                gap: 64px;
            }
            .mentai-middle-bottom-group {
                gap: 40px;
            }
            .mentai-floating-card {
                padding: 28px 28px 20px;
            }
            .mentai-mascot-circle {
                width: 64px;
                height: 64px;
            }
            .mentai-mascot-circle img,
            .mentai-mascot-circle svg {
                width: 32px;
                height: 32px;
            }
            .mentai-title {
                font-size: 28px;
                line-height: 34px;
                margin: 14px 0 6px 0;
            }
        }

        @media (max-height: 620px) {
            .mentai-content-wrapper {
                gap: 32px;
            }
            .mentai-middle-bottom-group {
                gap: 20px;
            }
            .mentai-floating-card {
                padding: 16px 16px;
                border-radius: 16px;
            }
        }

        /* Mobile / Tablet Screen Width Media Queries */
        @media (max-width: 768px) {
            .mentai-content-wrapper {
                gap: 32px;
            }
            .mentai-middle-bottom-group {
                gap: 20px;
            }
            .mentai-prompts-grid {
                grid-template-columns: 1fr;
            }
            .mentai-main-stage {
                padding: 12px;
            }
            .mentai-floating-card {
                padding: 20px 16px;
                border-radius: 18px;
                max-height: 96vh;
            }
            .mentai-sidebar {
                position: fixed;
                top: 0;
                left: 0;
                bottom: 0;
            }
        }

        /* Card 1 Highlight */
        .mentai-card-teal {
            background-color: #00BBA7;
            border-radius: 16px;
            padding: 6px 6px 10px;
            display: flex;
            flex-direction: column;
            justify-content: space-between;
            gap: 8px;
            cursor: pointer;
            transition: background-color 0.2s ease;
            text-decoration: none;
            box-sizing: border-box;
        }
        .mentai-card-teal:hover {
            background-color: #009689; /* primary-600 */
        }

        /* Cards 2 & 3 Neutral */
        .mentai-card-neutral {
            background-color: #F4F4F5;
            border: 1px solid #E4E4E7;
            border-radius: 16px;
            padding: 6px 6px 10px;
            display: flex;
            flex-direction: column;
            justify-content: space-between;
            gap: 8px;
            cursor: pointer;
            transition: background-color 0.2s ease, border-color 0.2s ease;
            text-decoration: none;
            box-sizing: border-box;
        }
        .mentai-card-neutral:hover {
            background-color: #E4E4E7; /* base-200 / slightly darker */
            border-color: #D4D4D8;
        }

        .mentai-card-inner-white {
            background-color: #FFFFFF;
            border-radius: 12px;
            padding: 12px 14px;
            min-height: 94px;
            display: flex;
            flex-direction: column;
            justify-content: flex-start;
            box-sizing: border-box;
        }

        /* Input Container */
        .mentai-input-wrap {
            width: 100%;
            max-width: 700px;
            display: flex;
            flex-direction: column;
            gap: 10px;
            box-sizing: border-box;
        }

        .mentai-input-outer-box {
            background-color: #CCFBF1;
            border: 1px solid #96F7E4;
            border-radius: 18px;
            padding: 6px 6px 8px;
            display: flex;
            flex-direction: column;
            gap: 6px;
            box-sizing: border-box;
        }

        .mentai-input-inner-form {
            background-color: #FFFFFF;
            border-radius: 12px;
            padding: 12px 14px;
            display: flex;
            align-items: center;
            justify-content: space-between;
            gap: 12px;
            box-sizing: border-box;
        }

        .mentai-textarea {
            width: 100%;
            background: transparent !important;
            border: none !important;
            outline: none !important;
            box-shadow: none !important;
            resize: none !important;
            padding: 0 !important;
            margin: 0 !important;
            font-family: 'DM Sans', sans-serif !important;
            font-size: 15px !important;
            font-weight: 400 !important;
            color: #09090B !important;
            line-height: 24px !important;
            min-height: 24px;
            max-height: 100px;
        }
        .mentai-textarea::placeholder {
            color: #A1A1AA !important;
            font-weight: 400 !important;
        }

        .mentai-send-btn {
            width: 36px;
            height: 36px;
            min-width: 36px;
            border-radius: 9999px;
            border: none;
            display: flex;
            align-items: center;
            justify-content: center;
            background-color: #00BBA7;
            color: #FFFFFF;
            cursor: pointer;
            transition: all 0.2s ease;
            padding: 0;
        }
        .mentai-send-btn:hover:not(:disabled) {
            background-color: #009e8d;
            transform: scale(1.05);
        }
        .mentai-send-btn:disabled {
            background-color: #E4E4E7;
            color: #A1A1AA;
            cursor: not-allowed;
            transform: none;
        }
    </style>
@endsection

@section('content')
    <div class="mentai-page-wrap" x-data="mentaiIndex()" x-init="initChat()">

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
                            <img src="{{ asset('assets/mini_logo.png') }}" alt="Curhatorium" style="width: 24px; height: 24px; object-fit: contain;" />
                            <span class="font-bricolage" style="font-family: 'Bricolage Grotesque', sans-serif !important; font-weight: 700; font-size: 16px; line-height: 24px; letter-spacing: -0.02em; color: #1E1E1E;">Curhatorium</span>
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
                    <div x-show="showSearch" x-transition style="padding: 2px 4px; display: none;">
                        <div style="position: relative; width: 100%;">
                            <input type="text" x-model="searchQuery" placeholder="Cari percakapan..." 
                                style="width: 100%; background: #FFFFFF; border: 1px solid #E4E4E7; border-radius: 8px; padding: 6px 10px; font-size: 12px; color: #1E1E1E; box-sizing: border-box; outline: none;" />
                            <button x-show="searchQuery" @click="searchQuery = ''" style="position: absolute; right: 8px; top: 6px; color: #A1A1AA; border: none; background: transparent; cursor: pointer; font-size: 11px;">✕</button>
                        </div>
                    </div>

                    <!-- 2. "+ New chat" Button -->
                    <a href="{{ route('mentai.index') }}"
                        style="display: flex; align-items: center; justify-content: center; gap: 8px; background-color: #00BBA7; color: #FFFFFF; padding: 10px 16px; border-radius: 8px; font-weight: 500; font-size: 15px; text-decoration: none; transition: background 0.2s ease;">
                        <svg style="width: 16px; height: 16px;" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M12 4v16m8-8H4"/>
                        </svg>
                        <span>New chat</span>
                    </a>

                    <!-- 3. Conversation History Timeline (Scrollable) -->
                    <div class="mentai-scrollbar" @scroll.passive="onSidebarScroll($event)" style="flex: 1; overflow-y: auto; display: flex; flex-direction: column; gap: 12px; padding-right: 4px;">
                        
                        <!-- Loading State -->
                        <div x-show="loadingConversations" style="padding: 12px 0; text-align: center; font-size: 12px; color: #71717A;">
                            <svg class="animate-spin" style="width: 16px; height: 16px; color: #00BBA7; margin: 0 auto 6px;" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
                                <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                                <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                            </svg>
                            Memuat riwayat...
                        </div>

                        <!-- Real / Filtered Conversations Mode -->
                        <template x-if="conversations.length > 0">
                            <div style="display: flex; flex-direction: column; gap: 12px;">
                                
                                <!-- Group: Hari Ini -->
                                <div x-show="todayConversations.length > 0" style="display: flex; flex-direction: column; gap: 3px; padding-bottom: 10px; border-bottom: 1px solid #E4E4E7;">
                                    <div style="padding: 2px 8px; font-size: 11px; color: #71717A;">Hari ini</div>
                                    <template x-for="conv in todayConversations" :key="conv.id">
                                        <a :href="`/mental-support-chatbot/${conv.id}`"
                                            style="display: block; padding: 6px 10px; border-radius: 8px; font-size: 13.5px; color: #374151; text-decoration: none; white-space: nowrap; overflow: hidden; text-overflow: ellipsis; transition: background 0.15s ease;"
                                            onmouseover="this.style.background='#FFFFFF'; this.style.color='#1E1E1E';"
                                            onmouseout="this.style.background='transparent'; this.style.color='#374151';"
                                            :title="conv.title || 'Percakapan baru'">
                                            <span x-text="conv.title || 'Percakapan baru'"></span>
                                        </a>
                                    </template>
                                </div>

                                <!-- Group: Kemarin -->
                                <div x-show="yesterdayConversations.length > 0" style="display: flex; flex-direction: column; gap: 3px; padding-bottom: 10px; border-bottom: 1px solid #E4E4E7;">
                                    <div style="padding: 2px 8px; font-size: 11px; color: #71717A;">Kemarin</div>
                                    <template x-for="conv in yesterdayConversations" :key="conv.id">
                                        <a :href="`/mental-support-chatbot/${conv.id}`"
                                            style="display: block; padding: 6px 10px; border-radius: 8px; font-size: 13.5px; color: #374151; text-decoration: none; white-space: nowrap; overflow: hidden; text-overflow: ellipsis; transition: background 0.15s ease;"
                                            onmouseover="this.style.background='#FFFFFF'; this.style.color='#1E1E1E';"
                                            onmouseout="this.style.background='transparent'; this.style.color='#374151';"
                                            :title="conv.title || 'Percakapan baru'">
                                            <span x-text="conv.title || 'Percakapan baru'"></span>
                                        </a>
                                    </template>
                                </div>

                                <!-- Group: 7 Hari Terakhir -->
                                <div x-show="last7DaysConversations.length > 0" style="display: flex; flex-direction: column; gap: 3px; padding-bottom: 8px; border-bottom: 1px solid #E4E4E7;">
                                    <div style="padding: 2px 8px; font-size: 11px; color: #71717A;">7 Hari terakhir</div>
                                    <template x-for="conv in last7DaysConversations" :key="conv.id">
                                        <a :href="`/mental-support-chatbot/${conv.id}`"
                                            style="display: block; padding: 6px 10px; border-radius: 8px; font-size: 13.5px; color: #374151; text-decoration: none; white-space: nowrap; overflow: hidden; text-overflow: ellipsis; transition: background 0.15s ease;"
                                            onmouseover="this.style.background='#FFFFFF'; this.style.color='#1E1E1E';"
                                            onmouseout="this.style.background='transparent'; this.style.color='#374151';"
                                            :title="conv.title || 'Percakapan baru'">
                                            <span x-text="conv.title || 'Percakapan baru'"></span>
                                        </a>
                                    </template>
                                </div>

                                <!-- Group: Lebih Lama -->
                                <div x-show="olderConversations.length > 0" style="display: flex; flex-direction: column; gap: 3px;">
                                    <div style="padding: 2px 8px; font-size: 11px; color: #71717A;">Lebih lama</div>
                                    <template x-for="conv in olderConversations" :key="conv.id">
                                        <a :href="`/mental-support-chatbot/${conv.id}`"
                                            style="display: block; padding: 6px 10px; border-radius: 8px; font-size: 13.5px; color: #374151; text-decoration: none; white-space: nowrap; overflow: hidden; text-overflow: ellipsis; transition: background 0.15s ease;"
                                            onmouseover="this.style.background='#FFFFFF'; this.style.color='#1E1E1E';"
                                            onmouseout="this.style.background='transparent'; this.style.color='#374151';"
                                            :title="conv.title || 'Percakapan baru'">
                                            <span x-text="conv.title || 'Percakapan baru'"></span>
                                        </a>
                                    </template>
                                </div>

                                <!-- Lazy Loading Spinner Indicator at Bottom -->
                                <div x-show="loadingMoreConversations" style="padding: 8px 0; text-align: center; font-size: 11px; color: #71717A;">
                                    <svg class="animate-spin" style="width: 14px; height: 14px; color: #00BBA7; margin: 0 auto;" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
                                        <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                                        <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                                    </svg>
                                </div>

                            </div>
                        </template>

                        <!-- Fallback Sample Showcase (Exact Figma Representation when empty) -->
                        <template x-if="!loadingConversations && conversations.length === 0">
                            <div style="display: flex; flex-direction: column; gap: 12px;">
                                
                                <!-- Group: Hari ini -->
                                <div style="display: flex; flex-direction: column; gap: 3px; padding-bottom: 10px; border-bottom: 1px solid #E4E4E7;">
                                    <div style="padding: 2px 8px; font-size: 11px; color: #71717A;">Hari ini</div>
                                    <span style="display: block; padding: 6px 10px; border-radius: 8px; font-size: 13.5px; color: #374151; white-space: nowrap; overflow: hidden; text-overflow: ellipsis;">Percakapan Sebelumnya</span>
                                    <span style="display: block; padding: 6px 10px; border-radius: 8px; font-size: 13.5px; font-weight: 500; color: #1E1E1E; background: #FFFFFF; border: 1px solid rgba(228,228,231,0.6); white-space: nowrap; overflow: hidden; text-overflow: ellipsis;">Keluhan Medis Terdahulu</span>
                                    <span style="display: block; padding: 6px 10px; border-radius: 8px; font-size: 13.5px; color: #374151; white-space: nowrap; overflow: hidden; text-overflow: ellipsis;">Rekap Pertanyaan Administra...</span>
                                </div>

                                <!-- Group: Kemarin -->
                                <div style="display: flex; flex-direction: column; gap: 3px; padding-bottom: 10px; border-bottom: 1px solid #E4E4E7;">
                                    <div style="padding: 2px 8px; font-size: 11px; color: #71717A;">Kemarin</div>
                                    <span style="display: block; padding: 6px 10px; border-radius: 8px; font-size: 13.5px; color: #374151; white-space: nowrap; overflow: hidden; text-overflow: ellipsis;">Pertanyaan Seputar Administr...</span>
                                    <span style="display: block; padding: 6px 10px; border-radius: 8px; font-size: 13.5px; color: #374151; white-space: nowrap; overflow: hidden; text-overflow: ellipsis;">Masalah Administratif</span>
                                    <span style="display: block; padding: 6px 10px; border-radius: 8px; font-size: 13.5px; color: #374151; white-space: nowrap; overflow: hidden; text-overflow: ellipsis;">Pertanyaan Terkait Administra...</span>
                                </div>

                                <!-- Group: 7 Hari terakhir -->
                                <div style="display: flex; flex-direction: column; gap: 3px; padding-bottom: 8px;">
                                    <div style="padding: 2px 8px; font-size: 11px; color: #71717A;">7 Hari terakhir</div>
                                    <span style="display: block; padding: 6px 10px; border-radius: 8px; font-size: 13.5px; color: #374151; white-space: nowrap; overflow: hidden; text-overflow: ellipsis;">Keluhan Medis Terdahulu</span>
                                </div>

                            </div>
                        </template>

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
                        <img src="{{ asset('assets/mini_logo.png') }}" alt="Curhatorium" style="width: 24px; height: 24px; object-fit: contain;" />
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

        <!-- ================= MAIN CONTENT: FLOATING WHITE CARD ================= -->
        <main class="mentai-main-stage">
            
            <!-- THE BIG FLOATING WHITE CARD -->
            <div class="mentai-floating-card mentai-scrollbar">
                
                <div class="mentai-content-wrapper">
                    
                    <!-- 1. TOP: Avatar + Welcome Header -->
                    <div class="mentai-top-header">
                        
                        <!-- Mascot Avatar Circle -->
                        <div class="mentai-mascot-circle">
                            <img src="{{ asset('assets/mentai/mentai_icon.svg') }}" alt="MentAI" />
                        </div>

                        <!-- Header Typography -->
                        <h1 class="mentai-title">Halo, Aku MentAI</h1>
                        <p class="mentai-subtitle">
                            Teman cerita 24/7 yang siap mendengarkanmu tanpa menghakimi.<br>
                            Ada yang ingin kamu sampaikan hari ini?
                        </p>

                    </div>

                    <!-- 2. MIDDLE & BOTTOM GROUP -->
                    <div class="mentai-middle-bottom-group">
                        
                        <!-- 2. MIDDLE: 3 Action/Prompt Cards -->
                        <div class="mentai-prompts-grid">
                            
                            <!-- Card 1: Highlight Teal Card -->
                            <div @click="selectStarter('Cerita apa aja, aku di sini buat dengerin 🫰🏼')" class="mentai-card-teal">
                                <div class="mentai-card-inner-white" style="gap: 8px;">
                                    <div style="display: flex; align-items: center; justify-content: space-between; width: 100%;">
                                        <div style="display: flex; align-items: center; gap: 5px;">
                                            <img src="{{ asset('assets/mentai/mentai_icon.svg') }}" alt="MentAI" style="width: 14px; height: 14px; object-fit: contain;" />
                                            <span style="font-size: 12px; font-weight: 600; color: #00BBA7;">MentAI</span>
                                        </div>
                                        <span style="background-color: #00BBA7; color: #FFFFFF; font-size: 10.5px; font-weight: 500; padding: 2px 8px; border-radius: 9999px;">Temen curhat</span>
                                    </div>
                                    <p style="font-size: 13.5px; line-height: 20px; font-weight: 400; color: #1E1E1E; margin: 0;">
                                        Cerita apa aja, aku di sini buat dengerin 🫰🏼
                                    </p>
                                </div>
                                <div style="padding: 2px 8px 0;">
                                    <span style="color: #FFFFFF; font-size: 11px; font-weight: 500; letter-spacing: 0.02em;">Prompt disarankan</span>
                                </div>
                            </div>

                            <!-- Card 2: Neutral Card -->
                            <div @click="selectStarter('Saya merasa sedikit cemas dan butuh teman bicara.')" class="mentai-card-neutral">
                                <div class="mentai-card-inner-white">
                                    <p style="font-size: 13.5px; line-height: 20px; font-weight: 400; color: #1E1E1E; margin: 0;">
                                        Saya merasa sedikit cemas dan butuh teman bicara.
                                    </p>
                                </div>
                                <div style="padding: 2px 8px 0;">
                                    <span style="color: #A1A1AA; font-size: 11px; font-weight: 500; letter-spacing: 0.02em;">Prompt disarankan</span>
                                </div>
                            </div>

                            <!-- Card 3: Neutral Card -->
                            <div @click="selectStarter('Hari ini cukup melelahkan, bagaimana cara menenangkan pikiran?')" class="mentai-card-neutral">
                                <div class="mentai-card-inner-white">
                                    <p style="font-size: 13.5px; line-height: 20px; font-weight: 400; color: #1E1E1E; margin: 0;">
                                        Hari ini cukup melelahkan, bagaimana cara menenangkan pikiran?
                                    </p>
                                </div>
                                <div style="padding: 2px 8px 0;">
                                    <span style="color: #A1A1AA; font-size: 11px; font-weight: 500; letter-spacing: 0.02em;">Prompt disarankan</span>
                                </div>
                            </div>

                        </div>

                        <!-- 3. BOTTOM: Input Box & Disclaimer -->
                        <div class="mentai-input-wrap">
                            
                            <!-- Outer Mint Container -->
                            <div class="mentai-input-outer-box">
                                
                                <!-- Inner White Form -->
                                <form @submit.prevent="sendMessage" class="mentai-input-inner-form">
                                    <textarea x-model="input" x-ref="messageInput"
                                        @keydown.enter.prevent="if(!loading) { sendMessage(); }" @input="autoResize()"
                                        placeholder="Kirim pesan ke MentAI..."
                                        rows="1"
                                        :disabled="loading"
                                        class="mentai-textarea"></textarea>

                                    <button type="submit"
                                        class="mentai-send-btn"
                                        :disabled="loading || input.trim() === ''"
                                        title="Kirim pesan">
                                        <template x-if="!loading">
                                            <img src="{{ asset('assets/mentai/send_icon.svg') }}" alt="Send" style="width: 20px; height: 20px; object-fit: contain;" />
                                        </template>
                                        <template x-if="loading">
                                            <svg class="animate-spin" style="width: 16px; height: 16px;" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
                                                <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                                                <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                                            </svg>
                                        </template>
                                    </button>
                                </form>

                                <!-- Bottom Info Row -->
                                <div style="display: flex; align-items: center; gap: 6px; padding: 2px 8px 0; color: #00BBA7; font-size: 11.5px; font-weight: 500;">
                                    <img src="{{ asset('assets/mentai/history_icon.svg') }}" alt="History" style="width: 14px; height: 14px; object-fit: contain;" />
                                    <span>Riwayat Tersimpan</span>
                                </div>

                            </div>

                            <!-- Disclaimer Text -->
                            <p style="text-align: center; font-size: 11.5px; line-height: 16px; color: #71717A; margin: 2px 0 0 0;">
                                MentAI dapat membuat kesalahan. Harap pertimbangkan untuk memverifikasi informasi penting.
                            </p>
                        </div>
                    </div>

                </div>

            </div>

        </main>
    </div>

    <!-- Alpine.js Component Script -->
    <script>
        function mentaiIndex() {
            return {
                conversations: [],
                currentPage: 1,
                hasMorePages: true,
                loadingConversations: false,
                loadingMoreConversations: false,
                input: '',
                loading: false,
                sidebarOpen: false,
                sidebarCollapsed: false,
                showSearch: false,
                searchQuery: '',

                _getCSRFToken() {
                    const meta = document.querySelector('meta[name="csrf-token"]');
                    return meta ? meta.getAttribute('content') : '';
                },

                autoResize() {
                    const el = this.$refs.messageInput;
                    if (!el) return;
                    el.style.height = 'auto';
                    el.style.height = Math.min(el.scrollHeight, 100) + 'px';
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

                selectStarter(text) {
                    this.input = text;
                    this.$nextTick(() => {
                        this.autoResize();
                        if (this.$refs.messageInput) {
                            this.$refs.messageInput.focus();
                        }
                    });
                },

                async initChat() {
                    await this.fetchConversations();
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

                async sendMessage() {
                    if (this.loading || !this.input.trim()) return;

                    const userText = this.input.trim();
                    this.loading = true;

                    try {
                        const startRes = await fetch('{{ route("ai.start") }}', {
                            method: 'POST',
                            headers: {
                                'Content-Type': 'application/json',
                                'X-CSRF-TOKEN': this._getCSRFToken(),
                                'Accept': 'application/json'
                            }
                        });

                        if (!startRes.ok) {
                            throw new Error('Network error starting conversation');
                        }

                        const startData = await startRes.json();
                        const conversationId = startData.conversationId;

                        sessionStorage.setItem('mentai_pending_prompt', JSON.stringify({
                            conversationId: conversationId,
                            message: userText
                        }));

                        window.location.href = `/mental-support-chatbot/${conversationId}`;
                    } catch (error) {
                        console.error('Failed to initiate chat:', error);
                        this.loading = false;
                        alert('Gagal memulai percakapan. Silakan coba lagi.');
                    }
                }
            };
        }
    </script>
@endsection
