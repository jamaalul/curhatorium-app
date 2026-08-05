<!-- Related Ebooks Section (Produk Lainnya) -->
@if(isset($relatedEbooks) && $relatedEbooks->isNotEmpty())
    <div style="padding-top: 48px; border-top: 1px solid #E4E4E7; display: flex; flex-direction: column; gap: 24px; width: 100%;">
        
        <h2 style="font-family: 'Bricolage Grotesque', sans-serif; font-size: 20px; font-weight: 600; line-height: 28px; color: #141414; margin: 0;">
            Produk Lainnya
        </h2>

        <!-- 4 Columns Grid on Desktop, 2 Columns on Tablet & Mobile -->
        <div class="related-ebooks-grid">
            @foreach($relatedEbooks as $rel)
                @php
                    $relCoverUrl = 'https://placehold.co/300x400';
                    if ($rel->cover_image) {
                        if (str_starts_with($rel->cover_image, 'http://') || str_starts_with($rel->cover_image, 'https://')) {
                            $relCoverUrl = $rel->cover_image;
                        } else {
                            $relCoverUrl = \Illuminate\Support\Facades\Storage::url($rel->cover_image);
                        }
                    }
                @endphp

                <a href="{{ route('ebooks.show', $rel) }}" 
                   class="group bg-white rounded-2xl p-3 pb-4 border border-[#E4E4E7] shadow-xs flex flex-col justify-between gap-3 transition-all duration-300 hover:shadow-md hover:no-underline cursor-pointer block"
                   style="display: flex; flex-direction: column; justify-content: space-between; gap: 12px; background: #FFF; border-radius: 16px; border: 1px solid #E4E4E7; padding: 12px 12px 16px; box-sizing: border-box; width: 100%; text-decoration: none;">
                    
                    <!-- Ebook Cover Banner -->
                    <div class="ebook-card-banner">
                        <img src="{{ $relCoverUrl }}" alt="{{ $rel->title }}" style="width: 100%; height: 100%; object-fit: cover;" />
                    </div>

                    <!-- Ebook Info -->
                    <div style="display: flex; flex-direction: column; gap: 8px; width: 100%;">
                        <!-- Category Badge -->
                        <span style="color: #71717A; font-size: 12px; font-family: 'DM Sans', sans-serif; text-transform: uppercase; letter-spacing: 0.05em;">
                            {{ strtoupper($rel->category?->name ?? 'REFLEKSI') }}
                        </span>
                        
                        <!-- Title & Description Container with Bottom Divider -->
                        <div style="padding-bottom: 12px; border-bottom: 1px solid #E4E4E7; display: flex; flex-direction: column; gap: 4px; width: 100%;">
                            <h3 style="color: #141414; font-size: 16px; font-weight: 500; font-family: 'DM Sans', sans-serif; line-height: 28px; margin: 0; white-space: nowrap; overflow: hidden; text-overflow: ellipsis;">
                                {{ $rel->title }}
                            </h3>
                            <p style="color: #71717A; font-size: 15px; font-family: 'DM Sans', sans-serif; line-height: 28px; margin: 0; display: -webkit-box; -webkit-line-clamp: 2; -webkit-box-orient: vertical; overflow: hidden; min-height: 56px;">
                                {{ Str::limit(strip_tags($rel->description), 120) }}
                            </p>
                        </div>
                    </div>

                    <!-- Ebook Footer (Price) -->
                    <div style="display: flex; justify-content: space-between; align-items: center; padding-top: 4px; width: 100%;">
                        <span style="color: #171717; font-size: 16px; font-weight: 500; font-family: 'DM Sans', sans-serif;">
                            Rp{{ number_format((float) $rel->price < 1000 ? $rel->price * 1000 : $rel->price, 0, ',', '.') }}
                        </span>
                    </div>
                </a>
            @endforeach
        </div>

    </div>
@endif
