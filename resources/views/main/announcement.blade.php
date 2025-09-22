@php
    /** @var ?\App\Models\Announcement $announcement */
@endphp

@if(isset($announcement) && $announcement)
{{-- <section class="bg-yellow-50 border-l-4 py-4 items-center flex text-center" role="region" aria-label="Announcement">
    <div role="alert" class="flex flex-col items-center border">
        <div class="max-w-3xl mx-auto px-4 flex flex-col items-center">
            <div class="inline-block bg-yellow-400 text-white text-xs font-semibold px-3 py-1 rounded mb-2">Pengumuman</div>
            <h3 class="text-lg font-bold text-yellow-900 mb-2">{{ $announcement->title }}</h3>
            <div class="prose prose-sm text-yellow-800">{!! $announcement->body !!}</div>
        </div>
    </div>
</section> --}}
<section class="bg-yellow-50 w-full h-fit flex flex-col items-center text-center justify-center p-4 shadow-inner">
    <div class="md:w-[60vw] lg:w-[50vw] w-full flex flex-col items-center justify-center gap-2">
        <p class="px-5 py-1 bg-yellow-400 rounded-full text-yellow-700 mb-3 w-fit text-sm">Informasi</p>
        <h2 class="text-2xl font-bold text-stone-600 m-0">{{ $announcement->title }}</h2>
        <p class="text-stone-600 text-balance">{!! $announcement->body !!}</p>
    </div>
</section>
@endif
