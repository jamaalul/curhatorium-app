@php($media = $getRecord())

@if ($media->media_type === 'image')
    <a href="{{ $media->media_url }}" target="_blank" rel="noopener noreferrer">
        <img
            src="{{ $media->media_url }}"
            alt="Preview media urutan {{ $media->order_number }}"
            width="120"
            height="80"
            loading="lazy"
            class="rounded-lg object-cover"
        >
    </a>
@elseif ($media->media_type === 'video')
    <video controls preload="metadata" width="180" class="rounded-lg">
        <source src="{{ $media->media_url }}">
        Browser tidak mendukung preview video.
    </video>
@endif
