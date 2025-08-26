@php
    /** @var ?\App\Models\Announcement $announcement */
@endphp

@if(isset($announcement) && $announcement)
<link rel="stylesheet" href="{{ asset('css/main/announcement.css') }}">
<section class="announcement" role="region" aria-label="Announcement">
    <div role="alert">
        <div class="container">
            <div class="badge">Pengumuman</div>
            <h3>{{ $announcement->title }}</h3>
            <div class="body">{!! $announcement->body !!}</div>
        </div>
    </div>
</section>
@endif


