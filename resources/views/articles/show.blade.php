<!DOCTYPE html>
<html lang="id">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0" />
  <title>{{ $article->title }} - Artikel</title>
  <link rel="stylesheet" href="{{ asset('css/global.css') }}?v={{ filemtime(public_path('css/global.css')) }}" />
  <style>
    :root {
      --text: #0f172a; /* slate-900 */
      --muted: #64748b; /* slate-500 */
      --chip-bg:#e8f6f6; --chip-border:#c7e7e8; --chip-text:#256e6e; /* teal palette */
    }
    body { background: #ffffff; }
    .container { max-width: 860px; margin: 48px auto 72px; padding: 0 20px; }
    .page-header { margin-bottom: 18px; }
    .title { font-size: 2.25rem; font-weight: 800; color: var(--text); margin: 0 0 10px; line-height: 1.2; }
    .meta { display:flex; align-items:center; gap:10px; color: var(--muted); font-size: .95rem; }
    .chip { background: var(--chip-bg); border:1px solid var(--chip-border); color: var(--chip-text); padding:4px 10px; border-radius:999px; font-weight:700; font-size:.8rem; }
    .hero { width:100%; aspect-ratio: 16/9; border-radius:16px; overflow:hidden; background:#f3f4f6; margin: 18px 0 26px; box-shadow: 0 6px 18px rgba(0,0,0,0.08); }
    .hero img { width:100%; height:100%; object-fit: cover; display:block; }
    .content { color:#0f172a; font-size:1.075rem; line-height:1.85; text-align:justify; }
    .content h2 { font-size:1.5rem; margin:1.25rem 0 .5rem; }
    .content h3 { font-size:1.25rem; margin:1rem 0 .5rem; }
    .content p { margin: 0 0 1rem; }
    .content ul, .content ol { margin: 0 0 1rem 1.25rem; }
    .content blockquote { border-left: 4px solid #c7e7e8; padding: .5rem 1rem; margin: 1rem 0; color:#374151; background:#f8fcfc; border-radius:8px; }
    .back { display:inline-block; margin-top:28px; color:#256e6e; text-decoration:none; font-weight:600; }
  </style>
</head>
<body>
  <div class="container">
    <header class="page-header">
      <h1 class="title">{{ $article->title }}</h1>
      <div class="meta">
        <span class="chip">{{ $article->category ?? 'Umum' }}</span>
        <span>•</span>
        <span>{{ optional($article->created_at)->translatedFormat('l, d F Y') }}</span>
      </div>
    </header>
    @if($article->image)
      <div class="hero">
        <img src="{{ asset('storage/' . $article->image) }}" alt="{{ $article->title }}" />
      </div>
    @endif
    <article class="content">
      {!! $article->content !!}
    </article>
    <a class="back" href="/dashboard">← Kembali</a>
  </div>
  @include('components.footer', ['publicVariant' => true])
</body>
</html>


