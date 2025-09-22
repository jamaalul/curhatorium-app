@props(['xpAwarded', 'message'])

@if($xpAwarded > 0)
<div id="xp-notification" class="fixed top-4 right-4 bg-gradient-to-r from-green-400 to-blue-500 text-white p-4 rounded-lg shadow-lg z-50 transform transition-all duration-500 translate-x-full">
    <div class="flex items-center space-x-3">
        <div class="text-2xl">🎉</div>
        <div>
            <div class="font-bold text-lg">+{{ $xpAwarded }} XP!</div>
            <div class="text-sm opacity-90">{{ $message ?? 'XP awarded successfully!' }}</div>
        </div>
    </div>
</div>

<script>
document.addEventListener('DOMContentLoaded', function() {
    const notification = document.getElementById('xp-notification');
    if (notification) {
        // Show notification
        setTimeout(() => {
            notification.classList.remove('translate-x-full');
        }, 100);
        
        // Hide notification after 3 seconds
        setTimeout(() => {
            notification.classList.add('translate-x-full');
        }, 3000);
    }
});
</script>
@endif 