<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <title>Curhatorium | Main</title>
    <link rel="stylesheet" href="{{ asset('css/global.css') }}">
</head>
<body>
    @include('components.navbar')
    @if ($errors->has('msg'))
        <div id="toast-error" style="position: fixed; top: 24px; right: 24px; z-index: 9999; background: #f87171; color: white; padding: 16px 24px; border-radius: 8px; box-shadow: 0 2px 8px rgba(0,0,0,0.15); font-size: 1rem;">
            {{ $errors->first('msg') }}
        </div>
        <script>
            setTimeout(function() {
                var toast = document.getElementById('toast-error');
                if (toast) toast.style.display = 'none';
            }, 4000);
        </script>
    @endif
    @include('main.hero')
    @include('main.qotd')
    @include('main.stats')
    @include('main.cta')
    @include('main.features')
    @include('main.agenda')
    @include('components.footer')

    <script src="/js/main.js"></script>
</body>
</html>