<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Curhatorium | Booking Terkirim</title>
    @vite('resources/css/app.css')
    <link rel="stylesheet" href="{{ asset('css/global.css') }}">
</head>
<body class="p-8 w-full h-screen overflow-x-hidden bg-gray-50 flex items-center justify-center">
    <div class="container mx-auto px-4 py-16 text-center">
        <div class="max-w-md mx-auto bg-white p-8 rounded-lg shadow-md">
            <svg class="w-16 h-16 mx-auto text-green-500" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
            <h1 class="text-2xl font-bold text-gray-800 mt-4">Permintaan Booking Terkirim!</h1>
            <p class="text-gray-600 mt-2">
                Permintaan sesi Anda telah berhasil dikirimkan kepada fasilitator.
            </p>
            <p class="text-gray-600 mt-2">
                Kami akan segera mengirimkan update konfirmasi ke nomor WhatsApp Anda setelah fasilitator menyetujui jadwal.
            </p>
            <a href="{{ route('dashboard') }}" class="mt-8 inline-block bg-[#48A6A6] text-white py-3 px-6 rounded-md hover:bg-[#357979] transition-colors duration-200 font-semibold">
                Kembali ke Dashboard
            </a>
        </div>
    </div>
</body>
</html>