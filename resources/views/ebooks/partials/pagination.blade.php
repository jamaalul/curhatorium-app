@if ($paginator->hasPages())
    @php
        $total = $paginator->lastPage();
        $current = $paginator->currentPage();
        
        $pageRange = [];
        if ($total <= 4) {
            for ($i = 1; $i <= $total; $i++) {
                $pageRange[] = $i;
            }
        } else {
            if ($current <= 2) {
                $pageRange = [1, 2, '...', $total];
            } elseif ($current >= $total - 1) {
                $pageRange = [1, '...', $total - 1, $total];
            } else {
                $pageRange = [1, '...', $current, '...', $total];
            }
        }
    @endphp

    <div style="display: flex; align-items: center; justify-content: center; gap: 6px;">
        {{-- Previous Page Link --}}
        @if ($paginator->onFirstPage())
            <div style="width: 36px; height: 36px; border-radius: 10px; background: #FFFFFF; border: 1px solid #E4E4E7; display: flex; align-items: center; justify-content: center; opacity: 0.4; cursor: not-allowed;" aria-disabled="true">
                <svg style="width: 16px; height: 16px; color: #52525B;" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M15 19l-7-7 7-7" />
                </svg>
            </div>
        @else
            <a href="{{ $paginator->previousPageUrl() }}" rel="prev" style="width: 36px; height: 36px; border-radius: 10px; background: #FFFFFF; border: 1px solid #E4E4E7; display: flex; align-items: center; justify-content: center; color: #52525B; text-decoration: none; transition: all 0.15s ease;" class="hover:bg-[#F4F4F5]">
                <svg style="width: 16px; height: 16px;" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M15 19l-7-7 7-7" />
                </svg>
            </a>
        @endif

        {{-- Page Numbers & Ellipsis --}}
        @foreach ($pageRange as $item)
            @if ($item === '...')
                <div style="width: 36px; height: 36px; display: flex; align-items: center; justify-content: center; color: #A1A1AA; font-family: 'DM Sans', sans-serif; font-size: 14px; font-weight: 400;" aria-disabled="true">
                    ...
                </div>
            @elseif ($item == $current)
                <div style="width: 36px; height: 36px; border-radius: 10px; background: #00BBA7; border: 1px solid #00BBA7; display: flex; align-items: center; justify-content: center; color: #FFFFFF; font-family: 'DM Sans', sans-serif; font-size: 14px; font-weight: 500;" aria-current="page">
                    {{ $item }}
                </div>
            @else
                <a href="{{ $paginator->url($item) }}" style="width: 36px; height: 36px; border-radius: 10px; background: #FFFFFF; border: 1px solid #E4E4E7; display: flex; align-items: center; justify-content: center; color: #52525B; font-family: 'DM Sans', sans-serif; font-size: 14px; text-decoration: none; transition: all 0.15s ease;" class="hover:bg-[#F4F4F5]">
                    {{ $item }}
                </a>
            @endif
        @endforeach

        {{-- Next Page Link --}}
        @if ($paginator->hasMorePages())
            <a href="{{ $paginator->nextPageUrl() }}" rel="next" style="width: 36px; height: 36px; border-radius: 10px; background: #FFFFFF; border: 1px solid #E4E4E7; display: flex; align-items: center; justify-content: center; color: #52525B; text-decoration: none; transition: all 0.15s ease;" class="hover:bg-[#F4F4F5]">
                <svg style="width: 16px; height: 16px;" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M9 5l7 7-7 7" />
                </svg>
            </a>
        @else
            <div style="width: 36px; height: 36px; border-radius: 10px; background: #FFFFFF; border: 1px solid #E4E4E7; display: flex; align-items: center; justify-content: center; opacity: 0.4; cursor: not-allowed;" aria-disabled="true">
                <svg style="width: 16px; height: 16px; color: #52525B;" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M9 5l7 7-7 7" />
                </svg>
            </div>
        @endif
    </div>
@endif
