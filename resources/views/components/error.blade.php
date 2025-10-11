@vite('resources/css/app.css')

<div>
    @isset($msg)
        @if ($msg->any())
            <div class="fixed inset-0 flex items-center justify-center z-50 w-screen h-screen bg-black bg-opacity-50">
                <div class="relative bg-white border border-red-400 text-red-700 px-6 py-4 rounded shadow-lg flex flex-col" role="alert">
                    <strong class="font-bold">Mohon maaf ada kesalahan</strong>
                    <ul class="mt-2 list-disc list-inside">
                        @foreach ($msg->all() as $error)
                        <li>{{ $error }}</li>
                        @endforeach
                    </ul>
                    <div class="w-full flex justify-end">
                        <button
                            class="bg-red-600 text-white hover:bg-red-700 mt-4 rounded-sm w-fit px-4 py-1"
                            type="button"
                            onclick="this.parentElement.parentElement.parentElement.parentElement.style.display='none';"
                        >
                            Tutup
                        </button>
                    </div>
                </div>
            </div>

            <script>
                console.log('Error message displayed');
            </script>
        @else
            <script>
                console.log('No error message to display');
            </script>
        @endif
    @endisset
</div>
