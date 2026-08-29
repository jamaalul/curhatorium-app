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
     class="mentai-search-modal-backdrop"
     style="display: none;">

    <div x-show="searchModalOpen"
         x-transition:enter="transition ease-out duration-100"
         x-transition:enter-start="opacity-0 scale-98"
         x-transition:enter-end="opacity-100 scale-100"
         x-transition:leave="transition ease-in duration-75"
         x-transition:leave-start="opacity-100 scale-100"
         x-transition:leave-end="opacity-0 scale-98"
         @click.outside="closeSearchModal()"
         class="mentai-search-modal-card">

        {{-- Top Search Bar (Figma #1231:2617) --}}
        <div class="mentai-search-modal-header">
            <div class="mentai-search-modal-input-wrap">
                <input type="text"
                       x-ref="searchModalInput"
                       x-model="modalSearchQuery"
                       @input="searchSelectedIndex = -1"
                       @keydown.down.prevent="navigateModalResults(1)"
                       @keydown.up.prevent="navigateModalResults(-1)"
                       @keydown.enter.prevent="selectActiveModalResult({{ $spaNav ? 'true' : 'false' }})"
                       placeholder="Telusuri..."
                       class="mentai-search-modal-input" />
            </div>

            <button type="button"
                    @click="closeSearchModal()"
                    class="mentai-search-modal-close-btn"
                    title="Tutup (Esc)">
                <svg style="width:20px;height:20px;" viewBox="0 0 24 24" fill="none" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                </svg>
            </button>
        </div>

        {{-- Results / Recent Section (Figma #1231:2627) --}}
        <div style="display:flex;flex-direction:column;gap:8px;">
            {{-- Caption (Figma #1231:2628) --}}
            <div class="mentai-search-modal-caption"
                 x-text="modalSearchQuery.trim() ? 'Hasil pencarian' : 'Percakapan terbaru'">
                Percakapan terbaru
            </div>

            {{-- List Container (Figma #1231:2629) --}}
            <div style="display:flex;flex-direction:column;gap:4px;">

                {{-- Empty state --}}
                <div x-show="modalFilteredConversations().length === 0"
                     style="padding:16px 8px;text-align:center;font-size:13px;color:#71717A;">
                    <span x-text="modalSearchQuery.trim() ? 'Tidak ada percakapan yang cocok dengan &quot;' + modalSearchQuery + '&quot;' : 'Belum ada percakapan sebelumnya'"></span>
                </div>

                {{-- Conversation Items (Max 5 items, Figma #1231:2630 ~ #1231:2646) --}}
                <template x-for="(conv, idx) in modalFilteredConversations()" :key="conv.id">
                    <a :href="`/mental-support-chatbot/${conv.id}`"
                       @click="@if($spaNav) $event.preventDefault(); navigateTo(conv); @endif closeSearchModal()"
                       @mouseenter="searchSelectedIndex = idx"
                       @mouseleave="searchSelectedIndex = -1"
                       class="mentai-search-modal-item"
                       :class="searchSelectedIndex === idx ? 'is-selected' : ''">

                        {{-- Icon (Figma #1231:2631) --}}
                        <div class="mentai-search-modal-item-icon">
                            <svg style="width:24px;height:24px;" viewBox="0 0 24 24" fill="none" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.8" d="M8 12h.01M12 12h.01M16 12h.01M21 12c0 4.418-4.03 8-9 8a9.863 9.863 0 01-4.255-.949L3 20l1.395-3.72C3.512 15.042 3 13.574 3 12c0-4.418 4.03-8 9-8s9 3.582 9 8z" />
                            </svg>
                        </div>

                        {{-- Title (Figma #1231:2633) --}}
                        <span class="mentai-search-modal-item-title"
                              x-text="conv.title || 'Percakapan baru'"></span>
                    </a>
                </template>
            </div>
        </div>

    </div>
</div>
