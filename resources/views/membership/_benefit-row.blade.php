{{-- Benefit row partial: pass $label (string) and $value ('check' for checkmark, or text) --}}
<div class="w-full inline-flex justify-between items-center">
    <span class="text-base-500 text-xl font-medium font-dm leading-9">{{ $label }}</span>
    @if ($value === 'check')
        <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="#222222" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
            <polyline points="20 6 9 17 4 12"></polyline>
        </svg>
    @else
        <span class="text-base-900 text-xl font-medium font-dm leading-9">{{ $value }}</span>
    @endif
</div>
