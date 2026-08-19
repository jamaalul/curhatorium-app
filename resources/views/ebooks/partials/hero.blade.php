<!-- Ebook Hero Section (Banner + Details) -->
<div class="ebook-hero-container">
    
    <!-- Left Column: Ebook Cover Banner Container (Full Height Stretch) -->
    <div class="ebook-hero-left">
        <div class="ebook-cover-banner">
            @if($coverUrl)
                <img src="{{ $coverUrl }}" alt="{{ $ebook->title }}" style="width: 100%; height: 100%; object-fit: contain; border-radius: 8px; filter: drop-shadow(0 10px 15px rgba(0,0,0,0.1));" />
            @else
                <div style="display: flex; flex-direction: column; align-items: center; gap: 8px; color: #A1A1AA;">
                    <svg style="width: 64px; height: 64px;" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5s3.332.477 4.5 1.253v13C19.832 18.477 18.246 18 16.5 18s-3.332.477-4.5 1.253" />
                    </svg>
                    <span style="font-family: 'DM Sans', sans-serif; font-size: 14px;">No Cover Available</span>
                </div>
            @endif
        </div>
    </div>

    <!-- Right Column: Ebook Details & Purchase Action -->
    <div class="ebook-hero-right">
        <!-- Top Content Block -->
        <div style="display: flex; flex-direction: column; gap: 20px; width: 100%;">
            
            <!-- Header & Category Frame -->
            <div style="padding-bottom: 24px; border-bottom: 1px solid #E4E4E7; display: flex; flex-direction: column; gap: 12px; width: 100%;">
                <!-- Category Badge -->
                <span style="color: #71717A; font-size: 13px; font-family: 'DM Sans', sans-serif; font-weight: 500; text-transform: uppercase; letter-spacing: 0.05em;">
                    {{ strtoupper($ebook->category?->name ?? 'REFLEKSI') }}
                </span>
                
                <!-- Ebook Title -->
                <h1 class="ebook-title-heading">
                    {{ $ebook->title }}
                </h1>

                <!-- Price & Page Count Row -->
                <div style="display: flex; align-items: center; gap: 14px; margin-top: 4px;">
                    <span style="font-family: 'Bricolage Grotesque', sans-serif; font-size: 26px; font-weight: 600; color: #141414;">
                        Rp{{ number_format((float) $ebook->price < 1000 ? $ebook->price * 1000 : $ebook->price, 0, ',', '.') }}
                    </span>
                    
                    @if($ebook->page_count)
                        <div style="width: 1px; height: 20px; background-color: #E4E4E7;"></div>
                        <span style="font-family: 'DM Sans', sans-serif; font-size: 15px; color: #71717A;">
                            {{ $ebook->page_count }} halaman
                        </span>
                    @endif
                </div>
            </div>

            <!-- Description Section with 3-Line Clamp & Read More Toggle -->
            <div x-data="{ expanded: false }" style="padding-bottom: 24px; border-bottom: 1px solid #E4E4E7; display: flex; flex-direction: column; gap: 8px; width: 100%;">
                <h3 style="color: #141414; font-size: 16px; font-weight: 500; font-family: 'DM Sans', sans-serif; margin: 0;">
                    Deskripsi
                </h3>

                @php
                    $plainDescription = strip_tags($ebook->description);
                    $isLongDescription = strlen($plainDescription) > 100;
                @endphp

                <div style="position: relative; width: 100%;">
                    <div :style="expanded ? 'max-height: none;' : 'display: -webkit-box; -webkit-line-clamp: 3; -webkit-box-orient: vertical; overflow: hidden; max-height: 84px;'"
                         style="color: #71717A; font-size: 15px; font-family: 'DM Sans', sans-serif; line-height: 28px; transition: all 0.3s ease;">
                        {!! nl2br(e($plainDescription)) !!}
                    </div>
                </div>

                @if($isLongDescription)
                    <button @click="expanded = !expanded" 
                            type="button"
                            style="color: #00BBA7; font-size: 14px; font-weight: 500; font-family: 'DM Sans', sans-serif; background: none; border: none; padding: 0; margin-top: 4px; cursor: pointer; display: inline-flex; align-items: center; gap: 4px; text-align: left;">
                        <span x-text="expanded ? 'Sembunyikan' : 'Baca selengkapnya'">Baca selengkapnya</span>
                        <svg style="width: 14px; height: 14px; transition: transform 0.2s;" :class="expanded ? 'rotate-180' : ''" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M19 9l-7 7-7-7" />
                        </svg>
                    </button>
                @endif
            </div>

        </div>

        <!-- Figma Component Set 953:1215 (Button / ebook) Implementation -->
        <div style="width: 100%; margin-top: 12px;">
            @if($hasPurchased)
                <!-- Variant: "baca" -> "Baca sekarang" (Solid #00BBA7 background, White text) -->
                <a href="{{ route('ebooks.read', $ebook) }}" 
                   style="display: flex; justify-content: center; align-items: center; gap: 10px; width: 100%; background: #00BBA7; color: #FFFFFF; font-family: 'DM Sans', sans-serif; font-size: 16px; font-weight: 500; line-height: 16px; padding: 20px; border-radius: 12px; text-decoration: none; transition: background 0.15s ease; box-sizing: border-box;"
                   class="hover:bg-[#009B8A] hover:no-underline">
                    <span>Baca sekarang</span>
                </a>
            @else
                @auth
                    <!-- Variant: "beli" -> "Beli sekarang" (Solid #00BBA7 background, White text) -->
                    <form method="POST" action="{{ route('ebooks.checkout', $ebook) }}" style="width: 100%;">
                        @csrf
                        <button type="submit" 
                                style="display: flex; justify-content: center; align-items: center; gap: 10px; width: 100%; background: #00BBA7; color: #FFFFFF; font-family: 'DM Sans', sans-serif; font-size: 16px; font-weight: 500; line-height: 16px; padding: 20px; border-radius: 12px; border: none; cursor: pointer; transition: background 0.15s ease; box-sizing: border-box;"
                                class="hover:bg-[#009B8A]">
                            <span>Beli sekarang</span>
                        </button>
                    </form>
                @else
                    <!-- Variant: "login" -> "Login untuk membeli" (Outlined 1px #00BBA7 border, #00BBA7 text) -->
                    <a href="{{ route('login') }}" 
                       style="display: flex; justify-content: center; align-items: center; gap: 10px; width: 100%; background: #FFFFFF; border: 1px solid #00BBA7; color: #00BBA7; font-family: 'DM Sans', sans-serif; font-size: 16px; font-weight: 500; line-height: 16px; padding: 20px; border-radius: 12px; text-decoration: none; transition: all 0.15s ease; box-sizing: border-box;"
                       class="hover:bg-[#F0FDF4] hover:no-underline">
                        <span>Login untuk membeli</span>
                    </a>
                @endauth
            @endif
        </div>

    </div>

</div>
