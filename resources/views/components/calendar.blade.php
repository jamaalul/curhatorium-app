

<div
    x-data="calendarWidget(@js($eventsUrl), @js($initialView))"
    x-init="initCalendar"
    {{ $attributes->merge(['class' => 'flex flex-col']) }}
>
    <div class="flex-1 min-h-0" x-ref="calendarEl" id="{{ $id }}"></div>
</div>