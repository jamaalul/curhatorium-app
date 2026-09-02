{{--
    Search Modal Popup (MentAI Conversation Search)
    Figma Design: #1231:2616 (Curhatorium - Redesign)
    Props:
      $spaNav - bool - true = SPA navigation, false = full page link
--}}
@php $spaNav = $spaNav ?? false; @endphp

<div x-show="searchModalOpen"
     x-transition:enter="transition-opacity ease-out duration-100"
     x-transition:enter-start="opacity-0"
     x-transition:enter-end="opacity-100"
     x-transition:leave="transition-opacity ease-in duration-75"
     x-transition:leave-start="opacity-100"
     x-transition:leave-end="opacity-0"
     @click.self="closeSearchModal()"
     @keydown.escape.window="closeSearchModal()"
     @keydown.window="if ((($event.metaKey || $event.ctrlKey) && $event.key.toLowerCase() === 'k')) { $event.preventDefault(); openSearchModal(); }"
     class="fixed inset-0 z-50 flex items-start justify-center p-4 pt-10 md:pt-[max(40px,calc(50vh-170px))] bg-zinc-950/45 max-md:p-0 max-md:pt-0 max-md:items-stretch max-md:bg-white max-md:w-screen max-md:h-screen max-md:h-[100dvh]"
     style="display: none;">

    <div x-show="searchModalOpen"
         x-transition:enter="transition ease-out duration-100"
         x-transition:enter-start="opacity-0 scale-95"
         x-transition:enter-end="opacity-100 scale-100"
         x-transition:leave="transition ease-in duration-75"
         x-transition:leave-start="opacity-100 scale-100"
         x-transition:leave-end="opacity-0 scale-95"
         @click.outside="closeSearchModal()"
         class="w-full max-w-[622px] bg-zinc-100 rounded-xl p-4 flex flex-col gap-5 shadow-2xl border border-zinc-200 box-border max-md:max-w-full max-md:h-full max-md:rounded-none max-md:border-0 max-md:shadow-none max-md:p-5 max-md:bg-white max-md:overflow-y-auto">

        {{-- Top Search Bar (Figma #1231:2617) --}}
        <div class="flex items-center justify-between pb-3 border-b border-zinc-200 gap-3">
            <div class="flex items-center gap-3 flex-1">
                <input type="text"
                       x-ref="searchModalInput"
                       x-model="modalSearchQuery"
                       @input="searchSelectedIndex = -1"
                       @keydown.down.prevent="navigateModalResults(1)"
                       @keydown.up.prevent="navigateModalResults(-1)"
                       @keydown.enter.prevent="selectActiveModalResult({{ $spaNav ? 'true' : 'false' }})"
                       placeholder="Telusuri..."
                       class="w-full bg-transparent border-0 focus:ring-0 focus:outline-none text-zinc-900 text-base font-medium leading-7 placeholder-zinc-500 p-0" />
            </div>

            <button type="button"
                    @click="closeSearchModal()"
                    class="p-1 rounded-md text-zinc-500 hover:text-zinc-900 hover:bg-zinc-200/70 transition-colors cursor-pointer border-0 bg-transparent flex items-center justify-center"
                    title="Tutup (Esc)">
                <svg class="w-5 h-5" viewBox="0 0 24 24" fill="none" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                </svg>
            </button>
        </div>

        {{-- Results / Recent Section (Figma #1231:2627) --}}
        <div class="flex flex-col gap-2">
            {{-- Caption (Figma #1231:2628) --}}
            <div class="text-xs text-zinc-500 font-normal leading-4"
                 x-text="modalSearchQuery.trim() ? 'Hasil pencarian' : 'Percakapan terbaru'">
                Percakapan terbaru
            </div>

            {{-- List Container (Figma #1231:2629) --}}
            <div class="flex flex-col gap-1">

                {{-- Empty state --}}
                <div x-show="modalFilteredConversations().length === 0"
                     class="py-4 px-2 text-center text-xs text-zinc-500">
                    <span x-text="modalSearchQuery.trim() ? 'Tidak ada percakapan yang cocok dengan &quot;' + modalSearchQuery + '&quot;' : 'Belum ada percakapan sebelumnya'"></span>
                </div>

                {{-- Conversation Items (Max 5 items, Figma #1231:2630 ~ #1231:2646) --}}
                <template x-for="(conv, idx) in modalFilteredConversations()" :key="conv.id">
                    <a :href="`/mental-support-chatbot/${conv.id}`"
                       @click="@if($spaNav) $event.preventDefault(); navigateTo(conv); @endif closeSearchModal()"
                       @mouseenter="searchSelectedIndex = idx"
                       @mouseleave="searchSelectedIndex = -1"
                       class="group flex items-center gap-3 px-3 py-2 rounded-lg cursor-pointer transition-all w-full text-zinc-700 hover:bg-white hover:text-zinc-900 hover:shadow-xs max-md:hover:bg-zinc-100"
                       :class="searchSelectedIndex === idx ? 'bg-white text-zinc-900 shadow-xs max-md:bg-zinc-100' : ''">

                        {{-- Icon (Figma #1231:2631) --}}
                        <div class="w-6 h-6 shrink-0 flex items-center justify-center text-zinc-500 group-hover:text-[#00BBA7] transition-colors"
                             :class="searchSelectedIndex === idx ? 'text-[#00BBA7]' : ''">
                            <svg class="w-5 h-5" viewBox="0 0 24 24" fill="none" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.8" d="M8 12h.01M12 12h.01M16 12h.01M21 12c0 4.418-4.03 8-9 8a9.863 9.863 0 01-4.255-.949L3 20l1.395-3.72C3.512 15.042 3 13.574 3 12c0-4.418 4.03-8 9-8s9 3.582 9 8z" />
                            </svg>
                        </div>

                        {{-- Title (Figma #1231:2633) --}}
                        <span class="text-[15px] font-medium leading-7 text-zinc-700 group-hover:text-zinc-900 truncate flex-1 transition-colors"
                              :class="searchSelectedIndex === idx ? 'text-zinc-900' : ''"
                              x-text="conv.title || 'Percakapan baru'"></span>
                    </a>
                </template>
            </div>
        </div>

    </div>
</div>
