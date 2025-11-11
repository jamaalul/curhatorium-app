<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Reschedule Error</title>
    @vite('resources/css/app.css')
    <link rel="stylesheet" href="{{ asset('css/global.css') }}">
</head>

<body class="bg-gray-50 text-gray-800">
    <div class="max-w-xl mx-auto p-4">
        <div class="text-center mb-8 py-5">
            <div class="text-2xl font-bold text-[#48A6A6] mb-2">Curhatorium</div>
        </div>

        <div class="bg-white rounded-lg p-5 shadow-md mb-5 text-center">
            <div class="w-16 h-16 bg-red-100 rounded-full flex items-center justify-center mx-auto mb-4">
                <svg xmlns="http://www.w3.org/2000/svg" class="h-8 w-8 text-red-500" fill="none" viewBox="0 0 24 24"
                    stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                        d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                </svg>
            </div>

            <h1 class="text-2xl font-bold text-gray-800 mb-2">Error</h1>
            <p class="text-gray-600 mb-6">{{ $message ?? 'An error occurred while processing your request.' }}</p>

            <p class="text-gray-600">Please try again later or contact our support team if the issue persists.</p>
        </div>

        <div class="text-center text-gray-500 text-xs mt-8 pt-5 border-t border-gray-200">
            <p>&copy; {{ date('Y') }} Curhatorium. All rights reserved.</p>
        </div>
    </div>
</body>

</html>
