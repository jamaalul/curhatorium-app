{{--
    Shared MentAI Sidebar
    Props (via @include):
      $spaNav  – bool – true = sidebar links use navigateTo() (show page), false = normal href (index page)
--}}
@php $spaNav = $spaNav ?? false; @endphp

{{-- Mobile Backdrop --}}
<div x-show="sidebarOpen"
     x-transition:enter="transition-opacity ease-out duration-300"
     x-transition:enter-start="opacity-0"
     x-transition:enter-end="opacity-100"
     x-transition:leave="transition-opacity ease-in duration-200"
     x-transition:leave-start="opacity-100"
     x-transition:leave-end="opacity-0"
     @click="sidebarOpen = false"
     class="md:hidden z-40 fixed inset-0 bg-black/40 backdrop-blur-xs"
     style="display: none;"></div>

<aside class="w-[260px] min-w-[260px] max-w-[260px] bg-[#F4F4F5] max-md:bg-white border-r border-[#E4E4E7] h-screen flex flex-col box-border select-none transition-all duration-200 z-40 overflow-hidden max-md:fixed max-md:top-0 max-md:left-0 max-md:bottom-0 max-md:z-50"
       :class="{
           '-translate-x-full md:translate-x-0': !sidebarOpen && !sidebarCollapsed,
           'translate-x-0': sidebarOpen,
           'w-[58px] min-w-[58px] max-w-[58px]': sidebarCollapsed
       }">

    {{-- ── Expanded Rail ── --}}
    <div x-show="!sidebarCollapsed"
         class="flex flex-col h-full py-6 px-3.5 justify-between w-[260px] box-border">

        <div class="flex flex-col gap-4 flex-1 min-h-0">

            {{-- Header --}}
            <div class="flex items-center justify-between">
                <a href="{{ route('mentai.index') }}" class="flex items-center gap-2 hover:opacity-80 transition-opacity">
                    <img src="{{ asset('assets/mini_logo.png') }}" alt="Curhatorium" class="w-6 h-6 object-contain" />
                    <span class="font-bricolage font-bold text-[16px] leading-6 tracking-[-0.02em] text-[#1E1E1E]">Curhatorium</span>
                </a>
                <div class="flex items-center gap-1.5">
                    <button type="button" @click="openSearchModal()" title="Cari percakapan (Ctrl+K)"
                            class="w-[30px] h-[30px] rounded-lg bg-white max-md:bg-transparent border-0 flex items-center justify-center cursor-pointer text-zinc-900 hover:text-[#00BBA7] hover:bg-black/5 transition-colors">
                        <svg class="w-4 h-4" viewBox="0 0 16 16" fill="none" xmlns="http://www.w3.org/2000/svg">
                            <path d="M7.667 14.5C3.9 14.5.834 11.433.834 7.667.834 3.9 3.9.833 7.667.833c3.767 0 6.833 3.067 6.833 6.834 0 3.766-3.066 6.833-6.833 6.833zm0-12.667C4.447 1.833 1.834 4.453 1.834 7.667c0 3.213 2.613 5.833 5.833 5.833 3.22 0 5.834-2.62 5.834-5.833C13.5 4.453 10.887 1.833 7.667 1.833z" fill="currentColor"/>
                            <path d="M14.666 15.167a.664.664 0 0 1-.473-.2l-1.333-1.334a.669.669 0 0 1 0-.946.669.669 0 0 1 .946 0l1.333 1.333a.669.669 0 0 1-.473 1.147z" fill="currentColor"/>
                        </svg>
                    </button>
                    <button type="button"
                            @click="if (window.innerWidth < 768) { sidebarOpen = false; } else { sidebarCollapsed = true; }"
                            class="w-[30px] h-[30px] rounded-lg bg-white max-md:bg-transparent border-0 flex items-center justify-center cursor-pointer text-zinc-900 hover:text-[#00BBA7] hover:bg-black/5 transition-colors"
                            title="Tutup sidebar">
                        <svg class="w-4 h-4" viewBox="0 0 16 16" fill="none" xmlns="http://www.w3.org/2000/svg">
                            <path d="M14.646 10V6c0-3.333-1.333-4.667-4.667-4.667H5.98C2.646 1.333 1.313 2.667 1.313 6v4c0 3.333 1.333 4.667 4.666 4.667h3.98C13.313 14.667 14.646 13.333 14.646 10z" stroke="currentColor" stroke-linecap="round" stroke-linejoin="round"/>
                            <path d="M5.313 1.333v13.334" stroke="currentColor" stroke-linecap="round" stroke-linejoin="round"/>
                            <path d="m9.98 6.293-1.706 1.707 1.706 1.707" stroke="currentColor" stroke-linecap="round" stroke-linejoin="round"/>
                        </svg>
                    </button>
                </div>
            </div>

            {{-- New Chat Button --}}
            <a href="{{ route('mentai.index') }}"
               class="flex items-center justify-center gap-2 bg-[#00BBA7] hover:bg-[#009e8d] text-white py-2.5 px-4 rounded-lg font-medium text-[15px] transition-all">
                <img src="{{ asset('assets/mentai/new_chat_icon.svg') }}" alt="New Chat" class="w-5 h-5 filter brightness-0 invert" />
                <span>New chat</span>
            </a>

            {{-- Conversation List --}}
            <div class="flex-1 overflow-y-auto scrollbar-none flex flex-col gap-4 pr-1"
                 @scroll.passive="onSidebarScroll($event)">

                {{-- Spinner: only when no data yet --}}
                <div x-show="loadingConversations && conversations.length === 0"
                     class="py-4 text-center text-xs text-[#71717A]">
                    <svg class="animate-spin h-4 w-4 text-[#00BBA7] mx-auto mb-2" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
                        <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                        <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                    </svg>
                    Memuat riwayat...
                </div>

                {{-- Empty state --}}
                <div x-show="!loadingConversations && filteredConversations().length === 0"
                     class="py-6 px-2 text-center text-xs text-[#71717A]">
                    <span>Belum ada percakapan</span>
                </div>

                {{-- Groups --}}
                @foreach ([
                    ['label' => 'Hari ini',         'method' => 'todayConversations'],
                    ['label' => 'Kemarin',           'method' => 'yesterdayConversations'],
                    ['label' => '7 Hari terakhir',   'method' => 'last7DaysConversations'],
                    ['label' => 'Lebih lama',        'method' => 'olderConversations'],
                ] as $i => $group)
                <div x-show="{{ $group['method'] }}().length > 0"
                     class="flex flex-col gap-1 pb-3 {{ $i < 3 ? 'border-b border-[#E4E4E7]' : '' }}">
                    <div class="px-2 py-1 text-[12px] font-normal text-[#71717A]">{{ $group['label'] }}</div>
                    <template x-for="conv in {{ $group['method'] }}()" :key="conv.id">
                        <a :href="`/mental-support-chatbot/${conv.id}`"
                           @if($spaNav) @click.prevent="navigateTo(conv)" @endif
                           class="group flex items-center px-2 py-1.5 rounded-lg text-[14px] transition-all truncate"
                           :class="@if($spaNav) conv.id === conversationId @else false @endif ? 'bg-white font-medium text-[#09090B]' : 'font-medium text-[#374151] hover:bg-white hover:text-[#09090B] max-md:hover:bg-zinc-100'"
                           :title="conv.title || 'Percakapan baru'">
                            <span class="truncate" x-text="conv.title || 'Percakapan baru'"></span>
                        </a>
                    </template>
                </div>
                @endforeach

                {{-- Lazy-load spinner --}}
                <div x-show="loadingMoreConversations" class="py-2 text-center">
                    <svg class="animate-spin h-3.5 w-3.5 text-[#00BBA7] mx-auto" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
                        <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                        <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                    </svg>
                </div>
            </div>
        </div>

        {{-- Back to Dashboard --}}
        <div class="pt-4 border-t border-[#E4E4E7]">
            <a href="{{ route('dashboard') }}"
               class="flex items-center gap-2 px-2 py-2 rounded-lg text-[#71717A] hover:text-[#1E1E1E] hover:bg-white max-md:hover:bg-zinc-100 text-[14px] font-medium transition-all">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"/>
                </svg>
                <span>Kembali ke dashboard</span>
            </a>
        </div>
    </div>

    {{-- ── Collapsed Rail ── --}}
    <div x-show="sidebarCollapsed"
         class="flex flex-col items-center justify-between h-full py-6 w-full"
         style="display:none;">
        <div class="flex flex-col items-center gap-4">
            <a href="{{ route('dashboard') }}" title="Curhatorium">
                <img src="{{ asset('assets/mini_logo.png') }}" alt="Curhatorium" class="w-6 h-6 object-contain" />
            </a>
            <button type="button" @click="openSearchModal()"
                    class="w-[30px] h-[30px] rounded-lg bg-white border-0 flex items-center justify-center cursor-pointer text-zinc-900 hover:text-[#00BBA7] transition-colors"
                    title="Cari percakapan (Ctrl+K)">
                <svg class="w-4 h-4" viewBox="0 0 16 16" fill="none" xmlns="http://www.w3.org/2000/svg">
                    <path d="M7.667 14.5C3.9 14.5.834 11.433.834 7.667.834 3.9 3.9.833 7.667.833c3.767 0 6.833 3.067 6.833 6.834 0 3.766-3.066 6.833-6.833 6.833zm0-12.667C4.447 1.833 1.834 4.453 1.834 7.667c0 3.213 2.613 5.833 5.833 5.833 3.22 0 5.834-2.62 5.834-5.833C13.5 4.453 10.887 1.833 7.667 1.833z" fill="currentColor"/>
                    <path d="M14.666 15.167a.664.664 0 0 1-.473-.2l-1.333-1.334a.669.669 0 0 1 0-.946.669.669 0 0 1 .946 0l1.333 1.333a.669.669 0 0 1-.473 1.147z" fill="currentColor"/>
                </svg>
            </button>
            <button type="button" @click="sidebarCollapsed = false"
                    class="w-[30px] h-[30px] rounded-lg bg-white border-0 flex items-center justify-center cursor-pointer text-zinc-900 hover:text-[#00BBA7] transition-colors"
                    title="Buka sidebar">
                <svg class="w-4 h-4 rotate-180" viewBox="0 0 16 16" fill="none" xmlns="http://www.w3.org/2000/svg">
                    <path d="M14.646 10V6c0-3.333-1.333-4.667-4.667-4.667H5.98C2.646 1.333 1.313 2.667 1.313 6v4c0 3.333 1.333 4.667 4.666 4.667h3.98C13.313 14.667 14.646 13.333 14.646 10z" stroke="currentColor" stroke-linecap="round" stroke-linejoin="round"/>
                    <path d="M5.313 1.333v13.334" stroke="currentColor" stroke-linecap="round" stroke-linejoin="round"/>
                    <path d="m9.98 6.293-1.706 1.707 1.706 1.707" stroke="currentColor" stroke-linecap="round" stroke-linejoin="round"/>
                </svg>
            </button>
            <a href="{{ route('mentai.index') }}"
               class="w-[30px] h-[30px] rounded-lg bg-white border-0 flex items-center justify-center cursor-pointer text-[#00BBA7] hover:bg-zinc-100 transition-colors"
               title="New Chat">
                <svg class="w-4 h-4 text-[#00BBA7]" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M12 4v16m8-8H4"/>
                </svg>
            </a>
        </div>
        <a href="{{ route('dashboard') }}"
           class="w-[30px] h-[30px] rounded-lg bg-white border-0 flex items-center justify-center cursor-pointer text-zinc-500 hover:text-zinc-900 transition-colors"
           title="Dashboard">
            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"/>
            </svg>
        </a>
    </div>

</aside>

{{-- ── Search Modal Popup (Figma #1231:2616) ── --}}
@include('ai.partials._search-modal', ['spaNav' => $spaNav])
