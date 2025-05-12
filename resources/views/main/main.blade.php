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
    @include('main.hero')
    @include('main.qotd')
    @include('main.stats')
    @include('main.cta')
    @include('main.features')
    @include('main.agenda')

    <script src="/js/main.js"></script>
</body>
</html>