<?php

namespace App\View\Components;

use Closure;
use Illuminate\Contracts\View\View;
use Illuminate\View\Component;

class Calendar extends Component
{
    /**
     * Create a new component instance.
     */
    public function __construct(
        public string $eventsUrl = '',
        public string $initialView = 'dayGridMonth',
        public string $id = 'calendar'
    ) {
    }

    /**
     * Get the view / contents that represent the component.
     */
    public function render(): View|Closure|string
    {
        return view('components.calendar');
    }
}
