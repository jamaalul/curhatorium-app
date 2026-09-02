{{--
    Shared MentAI Input Box
--}}
<div class="bg-[#CCFBF1] border border-[#96F7E4] rounded-2xl p-1.5 pb-2 flex flex-col gap-1.5 transition-all w-full box-border">

    <form @submit.prevent="sendMessage"
          class="bg-white rounded-xl p-3 sm:py-3 sm:px-3.5 flex items-center justify-between gap-3 transition-colors box-border">

        <textarea x-model="input" x-ref="messageInput"
                  @keydown.enter.prevent="if(!loading) { sendMessage(); }"
                  @input="autoResize()"
                  placeholder="Kirim pesan ke MentAI..."
                  rows="1"
                  :disabled="loading"
                  class="bg-transparent text-[15px] sm:text-[16px] font-normal text-zinc-900 placeholder-zinc-400 leading-6 border-0 focus:ring-0 focus:outline-none w-full max-h-[140px] overflow-y-auto resize-none p-0 scrollbar-none"></textarea>

        <button type="button"
                @click="loading ? stopGeneration() : sendMessage()"
                class="shrink-0 w-9 h-9 min-w-9 rounded-full flex items-center justify-center transition-all duration-200 focus:outline-none cursor-pointer p-0 border-0"
                :class="loading ? 'bg-[#00BBA7] hover:bg-[#009e8d] text-white cursor-pointer' : (input.trim() === '' ? 'bg-zinc-200 text-zinc-400 cursor-not-allowed' : 'bg-[#00BBA7] hover:bg-[#009e8d] text-white')"
                :disabled="!loading && input.trim() === ''"
                :title="loading ? 'Hentikan jawaban' : 'Kirim pesan'">
            {{-- Stop square icon when generating/thinking --}}
            <template x-if="loading">
                <svg class="w-3 h-3 fill-current" viewBox="0 0 24 24">
                    <rect x="4" y="4" width="16" height="16" rx="3" />
                </svg>
            </template>
            {{-- Send arrow icon when idle --}}
            <template x-if="!loading">
                <img src="{{ asset('assets/mentai/send_icon.svg') }}" alt="Send"
                     class="w-5 h-5 object-contain filter brightness-0 invert" />
            </template>
        </button>
    </form>

    {{-- Bottom info row --}}
    <div class="flex items-center gap-1.5 px-2 pt-0.5 text-[#00BBA7] text-[11.5px] font-medium">
        <img src="{{ asset('assets/mentai/history_icon.svg') }}" alt="History"
             class="w-3.5 h-3.5 object-contain" />
        <span>Riwayat Tersimpan</span>
    </div>
</div>

{{-- Disclaimer --}}
<p class="text-center text-[11.5px] leading-4 text-zinc-500 mt-0.5 mb-0">
    <span class="text-[#00BBA7] font-medium">MentAI</span> dapat membuat kesalahan. Harap pertimbangkan untuk memverifikasi informasi penting.
</p>
