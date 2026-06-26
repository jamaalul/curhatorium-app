{{-- Benefit row partial: pass $label (string) and $value ('check' for checkmark, or text) --}}
<div class="inline-flex justify-between items-center w-full">
    <span class="font-dm font-medium text-base-500 text-xl leading-9">{{ $label }}</span>
    @if ($value === 'check')
        <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor"
            stroke-width="2" stroke-linecap="round" stroke-linejoin="round"
            class="size-5 lucide lucide-infinity-icon lucide-infinity">
            <path d="M6 16c5 0 7-8 12-8a4 4 0 0 1 0 8c-5 0-7-8-12-8a4 4 0 1 0 0 8" />
        </svg>
    @else
        <span class="font-dm font-medium text-base-900 text-xl leading-9">{{ $value }}</span>
    @endif
</div>