{{--
    Shared MentAI Input Box
    Props (via @include):
      $inputClass      – CSS class for textarea (default: Tailwind inline classes from show page)
      $sendBtnClass    – CSS class for send button wrapper
      $useMentaiClass  – bool – true = use .mentai-* CSS classes (index), false = inline Tailwind (show)
--}}
@php $useMentaiClass = $useMentaiClass ?? false; @endphp

<div class="{{ $useMentaiClass ? 'mentai-input-outer-box' : 'bg-[#CCFBF1] border border-[#96F7E4] rounded-2xl p-1.5 pb-2.5 flex flex-col gap-2 transition-all' }}">

    <form @submit.prevent="sendMessage"
          class="{{ $useMentaiClass ? 'mentai-input-inner-form' : 'bg-white rounded-xl p-3 sm:p-4 flex items-center justify-between gap-3 transition-colors' }}">

        <textarea x-model="input" x-ref="messageInput"
                  @keydown.enter.prevent="if(!loading) { sendMessage(); }"
                  @input="autoResize()"
                  placeholder="Kirim pesan ke MentAI..."
                  rows="1"
                  :disabled="loading"
                  class="{{ $useMentaiClass ? 'mentai-textarea' : 'bg-transparent text-[15px] sm:text-[16px] font-medium text-[#09090B] placeholder-[#A1A1AA] leading-6 border-0 focus:ring-0 focus:outline-none w-full max-h-[140px] overflow-y-auto resize-none p-0 custom-scrollbar' }}"></textarea>

        <button type="button"
                @click="loading ? stopGeneration() : sendMessage()"
                class="{{ $useMentaiClass ? 'mentai-send-btn' : 'flex-shrink-0 w-9 h-9 sm:w-10 sm:h-10 rounded-full flex items-center justify-center transition-colors duration-200 focus:outline-none cursor-pointer' }}"
                :class="loading ? 'is-stop bg-[#00BBA7] hover:bg-[#009e8d] text-white' : (input.trim() === '' ? 'bg-gray-100 text-gray-300 cursor-not-allowed' : 'bg-[#00BBA7] hover:bg-[#009e8d] text-white')"
                :disabled="!loading && input.trim() === ''"
                :title="loading ? 'Hentikan jawaban' : 'Kirim pesan'">
            {{-- Stop square icon when generating/thinking --}}
            <template x-if="loading">
                <svg style="width:12px;height:12px;" viewBox="0 0 24 24" fill="currentColor">
                    <rect x="4" y="4" width="16" height="16" rx="3" />
                </svg>
            </template>
            {{-- Send arrow icon when idle --}}
            <template x-if="!loading">
                <img src="{{ asset('assets/mentai/send_icon.svg') }}" alt="Send"
                     class="{{ $useMentaiClass ? '' : 'w-5 h-5 filter brightness-0 invert' }}"
                     style="{{ $useMentaiClass ? 'width:20px;height:20px;object-fit:contain;' : '' }}" />
            </template>
        </button>
    </form>

    {{-- Bottom info row --}}
    <div class="{{ $useMentaiClass ? '' : 'flex items-center gap-1.5 px-3 pt-0.5' }}"
         style="{{ $useMentaiClass ? 'display:flex;align-items:center;gap:6px;padding:2px 8px 0;color:#00BBA7;font-size:11.5px;font-weight:500;' : '' }}">
        <img src="{{ asset('assets/mentai/history_icon.svg') }}" alt="History"
             class="{{ $useMentaiClass ? '' : 'w-3.5 h-3.5' }}"
             style="{{ $useMentaiClass ? 'width:14px;height:14px;object-fit:contain;' : '' }}" />
        <span class="{{ $useMentaiClass ? '' : 'font-medium text-[12px] text-[#00BBA7]' }}">Riwayat Tersimpan</span>
    </div>
</div>

{{-- Disclaimer --}}
<p class="{{ $useMentaiClass ? '' : 'text-center text-[12px] leading-4 text-[#71717A]' }}"
   style="{{ $useMentaiClass ? 'text-align:center;font-size:11.5px;line-height:16px;color:#71717A;margin:2px 0 0 0;' : '' }}">
    @if(!$useMentaiClass)
        <span class="text-[#00BBA7] font-medium">MentAI</span> dapat
    @else
        MentAI dapat
    @endif
    membuat kesalahan. Harap pertimbangkan untuk memverifikasi informasi penting.
</p>
