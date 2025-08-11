<link rel="stylesheet" href="{{ asset('css/main/agenda.css') }}?v={{ filemtime(public_path('css/main/agenda.css')) }}">
<script src="{{ asset('js/main.js') }}?v={{ filemtime(public_path('js/main.js')) }}" defer></script>

<body>
  <section class="agenda-section">
    <h2>Artikel</h2>
      <div class="articles-grid"></div>
      <div class="pagination"></div>
  </section>
</body>