<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Curhatorium | Booking Terkirim</title>
    @vite('resources/css/app.css')
    <link rel="stylesheet" href="{{ asset('css/global.css') }}">
    <script src="https://cdnjs.cloudflare.com/ajax/libs/html2canvas/1.4.1/html2canvas.min.js"></script>
</head>
<body class="bg-gray-100 flex flex-col items-center min-h-screen justify-center gap-4">
    <div class="container px-4 py-12">
        <div class="max-w-lg mx-auto bg-white p-8 rounded-lg shadow-lg">
            <div class="text-center mb-8">
                <svg class="w-16 h-16 mx-auto text-green-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                <h1 class="text-2xl font-bold text-gray-800 mt-4">Permintaan Booking Terkirim</h1>
                <p class="text-gray-600 mt-2">Mohon tunggu konfirmasi dari fasilitator.</p>
            </div>

            <div class="border-t border-b border-gray-200 py-6">
                <h2 class="text-lg font-semibold text-gray-700 mb-4">Detail Reservasi</h2>
                <div class="space-y-4">
                    <div class="flex justify-between">
                        <span class="text-gray-500">Fasilitator:</span>
                        <span class="font-semibold text-gray-800">{{ $bookedSlot->professional->name }}</span>
                    </div>
                    <div class="flex justify-between">
                        <span class="text-gray-500">Jadwal:</span>
                        <span class="font-semibold text-gray-800">{{ \Carbon\Carbon::parse($bookedSlot->slot_start_time)->locale('id')->translatedFormat('l, j F Y, H:i') }}</span>
                    </div>
                    <div class="flex justify-between">
                        <span class="text-gray-500">Status:</span>
                        <span class="font-semibold text-yellow-500">Menunggu Konfirmasi</span>
                    </div>
                </div>
            </div>

            <div class="mt-6 text-center">
                <p class="text-sm text-gray-600">
                    Kami akan mengirimkan update konfirmasi ke nomor WhatsApp Anda setelah fasilitator menyetujui jadwal.
                </p>
            </div>
        </div>
    </div>

    <!-- Hidden receipt for download -->
    <div id="downloadable-receipt" class="hidden w-[350px] bg-white p-6 font-sans">
        <div class="text-center mb-6">
            <h1 class="text-2xl font-bold">Curhatorium</h1>
            <p class="text-sm">Bukti Booking Sesi</p>
        </div>
        <div class="border-t border-b border-dashed border-gray-400 py-4">
            <div class="flex justify-between mb-2">
                <span class="text-gray-600">Fasilitator:</span>
                <span class="font-semibold text-right">{{ $bookedSlot->professional->name }}</span>
            </div>
            <div class="flex justify-between mb-2">
                <span class="text-gray-600">Jadwal:</span>
                <span class="font-semibold text-right">{{ \Carbon\Carbon::parse($bookedSlot->slot_start_time)->locale('id')->translatedFormat('j M Y, H:i') }}</span>
            </div>
            <div class="flex justify-between">
                <span class="text-gray-600">Status:</span>
                <span class="font-semibold text-right">Menunggu Konfirmasi</span>
            </div>
        </div>
        <div class="mt-6 text-center">
            <p class="text-xs text-gray-500">
                Terima kasih telah menggunakan layanan kami. Mohon tunggu konfirmasi dari fasilitator.
            </p>
        </div>
    </div>


    <div class="flex flex-col items-center gap-4">
        <button id="download-receipt" class="bg-[#48a6a6] text-white py-3 px-6 rounded-md hover:bg-[#357979] transition-colors duration-200 font-semibold">
            Download Bukti
        </button>
        <a href="{{ route('dashboard') }}" class="flex items-center gap-2 text-[#48a6a6] py-3 px-6 rounded-md hover:text-[#357979] transition-colors duration-200 font-semibold">
            <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="size-6">
                <path stroke-linecap="round" stroke-linejoin="round" d="M10.5 19.5 3 12m0 0 7.5-7.5M3 12h18" />
            </svg>
            Kembali ke Dashboard
        </a>
    </div>

    <script>
        document.getElementById('download-receipt').addEventListener('click', function() {
            const receiptElement = document.getElementById('downloadable-receipt');
            
            // Temporarily make it visible for capture
            receiptElement.classList.remove('hidden');

            html2canvas(receiptElement, {
                scale: 2, // Increase scale for better quality
            }).then(canvas => {
                // Hide it again after capture
                receiptElement.classList.add('hidden');

                const link = document.createElement('a');
                link.download = 'bukti-booking-curhatorium.png';
                link.href = canvas.toDataURL('image/png');
                link.click();
            });
        });
    </script>
</body>
</html>