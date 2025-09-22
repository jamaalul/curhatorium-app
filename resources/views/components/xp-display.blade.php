@props(['user'])

@php
    $xpProgress = $user->getXpProgress();
    $dailySummary = $user->getDailyXpSummary();
@endphp

<div class="xp-display bg-gradient-to-r from-purple-500 to-pink-500 text-white p-4 rounded-lg shadow-lg">
    <div class="flex items-center justify-between mb-3">
        <h3 class="text-lg font-semibold">XP Progress</h3>
        <div class="text-right">
            <div class="text-2xl font-bold">{{ number_format($user->total_xp) }}</div>
            <div class="text-sm opacity-90">Total XP</div>
        </div>
    </div>
    
    <!-- Progress towards psychologist access -->
    <div class="mb-4">
        <div class="flex justify-between text-sm mb-1">
            <span>Progress to Psychologist Access</span>
            <span>{{ number_format($xpProgress['progress_percentage'], 1) }}%</span>
        </div>
        <div class="w-full bg-white bg-opacity-20 rounded-full h-2">
            <div class="bg-white h-2 rounded-full transition-all duration-300" 
                 style="width: {{ min(100, $xpProgress['progress_percentage']) }}%"></div>
        </div>
        <div class="text-xs mt-1 opacity-90">
            {{ number_format($xpProgress['current_xp']) }} / {{ number_format($xpProgress['target_xp']) }} XP
        </div>
    </div>
    
    <!-- Daily XP Summary -->
    <div class="border-t border-white border-opacity-20 pt-3">
        <div class="flex justify-between text-sm">
            <span>Today's XP</span>
            <span>{{ $dailySummary['daily_xp_gained'] }} / {{ $dailySummary['max_daily_xp'] }}</span>
        </div>
        <div class="w-full bg-white bg-opacity-20 rounded-full h-1.5 mt-1">
            <div class="bg-white h-1.5 rounded-full transition-all duration-300" 
                 style="width: {{ min(100, $dailySummary['daily_progress_percentage']) }}%"></div>
        </div>
    </div>
    
    @if($user->canAccessPsychologist())
        <div class="mt-3 p-2 bg-green-500 bg-opacity-20 rounded text-center text-sm font-medium">
            🎉 You can now access psychologist consultations!
        </div>
    @endif
</div> 