<!-- Reviews & Comments Section (Ulasan & Komentar) -->
<div id="reviews" style="padding-top: 48px; border-top: 1px solid #E4E4E7; display: flex; flex-direction: column; gap: 24px; width: 100%;">
    
    <h2 style="font-family: 'Bricolage Grotesque', sans-serif; font-size: 32px; font-weight: 600; line-height: 36px; letter-spacing: -0.01em; color: #111827; margin: 0;">
        Ulasan & Komentar
    </h2>

    <!-- Flash Messages -->
    @if(session('success'))
        <div style="background-color: #ECFDF5; border: 1px solid #A7F3D0; border-radius: 12px; padding: 16px; color: #065F46; font-family: 'DM Sans', sans-serif; font-size: 14px;">
            {{ session('success') }}
        </div>
    @endif

    @if(session('error'))
        <div style="background-color: #FEF2F2; border: 1px solid #FCA5A5; border-radius: 12px; padding: 16px; color: #991B1B; font-family: 'DM Sans', sans-serif; font-size: 14px;">
            {{ session('error') }}
        </div>
    @endif

    <!-- Review Form for Authenticated Purchasers -->
    @if($hasPurchased && !$hasReviewed)
        <div style="background: #FFFFFF; border: 1px solid #E4E4E7; border-radius: 12px; padding: 20px; display: flex; flex-direction: column; gap: 16px; width: 100%; box-sizing: border-box;">
            <h3 style="font-family: 'DM Sans', sans-serif; font-size: 16px; font-weight: 600; color: #18181B; margin: 0;">Tulis Ulasan Anda</h3>
            <form action="{{ route('ebooks.review', $ebook) }}" method="POST" style="display: flex; flex-direction: column; gap: 16px; width: 100%;">
                @csrf
                <textarea name="content" rows="3" 
                          style="width: 100%; border: 1px solid #E4E4E7; border-radius: 8px; padding: 12px; font-family: 'DM Sans', sans-serif; font-size: 15px; color: #18181B; box-sizing: border-box; outline: none;"
                          placeholder="Bagaimana pendapat Anda tentang ebook ini? (Min. 3 karakter)" required minlength="3" maxlength="500"></textarea>
                @error('content')
                    <p style="color: #EF4444; font-size: 13px; font-family: 'DM Sans', sans-serif; margin: 0;">{{ $message }}</p>
                @enderror
                <div style="display: flex; justify-content: flex-end;">
                    <button type="submit" style="background: #00BBA7; color: #FFFFFF; font-family: 'DM Sans', sans-serif; font-size: 15px; font-weight: 500; padding: 10px 24px; border-radius: 9999px; border: none; cursor: pointer; transition: background 0.15s ease;">
                        Kirim Ulasan
                    </button>
                </div>
            </form>
        </div>
    @endif

    <!-- Comments List -->
    @if($comments->isNotEmpty())
        <div style="display: flex; flex-direction: column; gap: 16px; width: 100%;">
            @foreach($comments as $comment)
                <div style="background: #FFFFFF; border: 1px solid #F0F0F1; border-radius: 12px; padding: 20px; display: flex; flex-direction: column; gap: 16px; width: 100%; box-sizing: border-box;">
                    <!-- User Info Row -->
                    <div style="display: flex; align-items: center; gap: 12px;">
                        <!-- User Avatar Circle -->
                        <div style="width: 40px; height: 40px; border-radius: 9999px; background: #E4E4E7; display: flex; align-items: center; justify-content: center; font-family: 'DM Sans', sans-serif; font-weight: 600; font-size: 16px; color: #09090B; flex-shrink: 0;">
                            {{ strtoupper(substr($comment->user?->name ?? 'A', 0, 1)) }}
                        </div>
                        <div style="display: flex; flex-direction: column; gap: 2px;">
                            <span style="font-family: 'DM Sans', sans-serif; font-size: 16px; font-weight: 500; color: #111827;">{{ $comment->user?->name ?? 'Anonim' }}</span>
                            <span style="font-family: 'DM Sans', sans-serif; font-size: 12px; color: #71717A;">{{ $comment->created_at->diffForHumans() }}</span>
                        </div>
                    </div>

                    <!-- Comment Body -->
                    <p style="font-family: 'DM Sans', sans-serif; font-size: 16px; line-height: 28px; color: #3F3F46; margin: 0;">
                        {{ $comment->content }}
                    </p>
                </div>
            @endforeach
        </div>

        <!-- Pagination Footer (Centered on Mobile, Space-Between on Desktop) -->
        <div class="comments-pagination-wrapper">
            <span style="font-family: 'DM Sans', sans-serif; font-size: 14px; color: #71717A;">
                Menampilkan <strong style="color: #18181B; font-weight: 500;">{{ $comments->firstItem() }}</strong> sampai <strong style="color: #18181B; font-weight: 500;">{{ $comments->lastItem() }}</strong> dari <strong style="color: #18181B; font-weight: 500;">{{ $comments->total() }}</strong> ulasan
            </span>
            <div>
                {{ $comments->fragment('reviews')->links('ebooks.partials.pagination') }}
            </div>
        </div>
    @else
        <div style="display: flex; flex-direction: column; align-items: center; justify-content: center; padding: 48px 20px; background: #FFFFFF; border-radius: 12px; border: 1px dashed #D4D4D8; text-align: center; width: 100%; box-sizing: border-box;">
            <svg style="width: 48px; height: 48px; color: #A1A1AA; margin-bottom: 12px;" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M8 12h.01M12 12h.01M16 12h.01M21 12c0 4.418-4.03 8-9 8a9.863 9.863 0 01-4.255-.949L3 20l1.395-3.72C3.512 15.042 3 13.574 3 12c0-4.418 4.03-8 9-8s9 3.582 9 8z" />
            </svg>
            <p style="font-family: 'DM Sans', sans-serif; font-size: 16px; font-weight: 500; color: #18181B; margin: 0;">Belum ada ulasan</p>
            <p style="font-family: 'DM Sans', sans-serif; font-size: 14px; color: #71717A; margin-top: 4px;">Jadilah yang pertama memberikan ulasan untuk ebook ini.</p>
        </div>
    @endif

</div>
