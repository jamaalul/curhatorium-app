<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0" />
  <title>Artikel</title>
  <link rel="stylesheet" href="{{ asset('css/global.css') }}?v={{ filemtime(public_path('css/global.css')) }}" />
  <link rel="stylesheet" href="{{ asset('css/main/agenda.css') }}?v={{ filemtime(public_path('css/main/agenda.css')) }}" />
  <script src="{{ asset('js/main.js') }}?v={{ filemtime(public_path('js/main.js')) }}" defer></script>
</head>
<body>
  @include('components.navbar')
  <section class="agenda-section">
    <h2>Artikel</h2>
    <div class="articles-grid"></div>
    <div class="pagination"></div>
  </section>
  @include('components.footer')
</body>
</html>


