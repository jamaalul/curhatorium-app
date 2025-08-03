<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>Curhatorium | Main</title>
    <link rel="stylesheet" href="{{ asset('css/global.css') }}">
    <link rel="stylesheet" href="https://unpkg.com/intro.js/minified/introjs.min.css">
    <style>
        .introjs-tooltip {
            background-color: #ffffff !important;
            color: #333333 !important;
            border-radius: 12px !important;
            font-size: 15px;
            padding: 20px;
            box-shadow: 0 10px 15px -3px rgba(0, 0, 0, 0.1), 0 4px 6px -2px rgba(0, 0, 0, 0.05);
            border: 1px solid rgba(142, 203, 207, 0.1);
        }
        
        .introjs-tooltiptext {
            font-family: 'FigtreeReg', sans-serif;
            color: #333333;
        }
        
        .introjs-helperLayer {
            border: 2px solid #8ecbcf !important;
            box-shadow: 0 0 0 4px rgba(142, 203, 207, 0.3);
        }
        
        .introjs-button {
            background-color: #8ecbcf !important;
            color: #fff !important;
            border: none;
            border-radius: 8px;
            font-weight: 600;
            transition: all 0.3s ease;
            text-shadow: none !important;
        }
        
        .introjs-button:hover {
            background-color: #7ab8bd !important;
            transform: translateY(-1px);
        }
        
        .introjs-tooltip-title {
            font-weight: 600;
            color: #ffcd2d;
            font-family: 'FigtreeBold', sans-serif;
        }
        
        .introjs-progressbar {
            background-color: #ffcd2d !important;
        }
        </style>
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
    @include('main.xp-redemption')
    @include('main.features')
    @include('main.agenda')
    @include('components.footer')

    <meta name="onboarding-completed" content="{{ auth()->check() && auth()->user()->onboarding_completed ? 'true' : 'false' }}">
    <script src="https://unpkg.com/intro.js/minified/intro.min.js"></script>
    <script src="/js/app.js"></script>
    <script src="https://unpkg.com/@lottiefiles/dotlottie-wc@0.6.2/dist/dotlottie-wc.js" type="module"></script>
</body>
</html>