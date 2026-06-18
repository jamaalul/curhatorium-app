@props(['title' => 'Curhatorium', 'bodyClass' => ''])

<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0, user-scalable=no, maximum-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    
    <title>{{ $title }}</title>
    
    <link rel="stylesheet" href="{{ asset('css/global.css') }}">

    <!-- Page-specific head elements (e.g., specific CSS) -->
    {{ $head ?? '' }}
</head>
<body class="{{ $bodyClass }}">
    {{ $slot }}

    {{ $scripts ?? '' }}
</body>
</html>
