<style>
    @import url('https://fonts.googleapis.com/css2?family=Figtree:ital,wght@0,300..900;1,300..900&display=swap');
</style>

<?php
    if ($stats['totalXP'] < 1000) {
        $badge = 'Kindle';
        $badgeColor = '#8E805D';
        $badgeImage = asset('img/kindle.svg');
    } elseif ($stats['totalXP'] >= 1000 && $stats['totalXP'] < 2000) {
        $badge = 'Torch';
        $badgeColor = '#865A5A';
        $badgeImage = asset('img/torch.svg');
    } elseif ($stats['totalXP'] >= 2000 && $stats['totalXP'] < 3000) {
        $badge = 'Beacon';
        $badgeColor = '#4E42A6';
        $badgeImage = asset('img/beacon.svg');
    } elseif ($stats['totalXP'] >= 3000 && $stats['totalXP'] < 4000) {
        $badge = 'Inferno';
        $badgeColor = '#7220C5';
        $badgeImage = asset('img/inferno.svg');
    } elseif ($stats['totalXP'] >= 4000) {
        $badge = 'Nova';
        $badgeColor = '#FF00D4';
        $badgeImage = asset('img/nova.svg');
    } 
?>

<nav style="transform: translateY(-60px);position: fixed;width: calc(100% - 40px);height: 60px;background-color: #fff;display: flex;box-shadow: 5px -2px 10px 1px rgba(0, 0, 0, 0.2);padding: 0px 20px 0px 20px;align-items: center;z-index: 2;">
    <h1 style="font-size: 1.5em;margin: 0;display: flex;align-items: center;gap: 10px;font-family: 'Figtree', sans-serif;font-weight: 400;color: #595959;"><img src="{{ asset('sub_logo.svg') }}" alt="sub" id="sub" style="width: 35px;height: 35px;">Curhatorium</h1>
    <div id="profile_box" style="display: flex;height: 46px;align-items: center;margin-left: auto;border-radius: 23px;background-color: #222;">
        <div style="width: fit-content;height: 100%;display: flex;background-color: #9ACBD0;border-radius: 23px;align-items: center;padding-right: 15px;gap: 5px;font-family: 'Figtree', sans-serif;font-weight: 400;color: #222;">
            <div style="width: fit-content;height: 100%;display: flex;background-color: {{ $badgeColor }};border-radius: 23px;align-items: center;padding: 0px 20px 0px 20px;margin-right: 5px;font-family: 'Figtree', sans-serif;font-weight: 600;color: #fff;">
                <img src="{{ $badgeImage }}" alt="profile" style="height: 64%;width: auto;margin-right: 15px;">
                {{ $badge }}
            </div>
            {{ $stats['totalXP'] }} XP
        </div>
        <p style="font-family: 'Figtree', sans-serif;color: #fff; font-weight: 500; margin-left: 8px;">{{ $stats['username'] }}</p>
        <img src="{{ asset('profile_pict.svg') }}" alt="profile" style="height: 30px;width: auto;margin: 8px;">
    </div>
</nav>