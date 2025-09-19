<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Chat Room: {{ $room }}</title>
    <link rel="stylesheet" href="{{ asset('css/global.css') }}">
    <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.7/css/bootstrap.min.css">
    <style>
        /* body { padding-top: 20px; }
        #chat-messages {
            padding: 10px;
            margin-bottom: 10px;
        } */
    </style>
    @vite('resources/css/app.css')
</head>
<body class="bg-stone-200 flex items-center h-screen justify-center w-screen p-0 md:p-2">
    <div class="w-0 md:w-1/5 md:min-w-80 h-full bg-stone-200 overflow-hidden p-0 md:p-4 flex flex-col gap-2 items-center">
        <div class="flex gap-2 items-center justify-center">
            <img src="{{ asset('assets/mini_logo.png') }}" alt="mini_logo" class="h-14 w-14">
            <h1 class="text-[#222222] text-4xl font-bold">Curhatorium</h1>
        </div>
        <p class="text-2xl text-bold text-[#222222] text-center">Your Safest Place</p>
        <button class="end-session-button px-6 py-4 text-2xl rounded-2xl bg-none w-full hover:bg-red-300 mt-auto border-red-300 border-2 transition-all duration-100 text-[#222222] font-bold">
            Akhiri Sesi
        </button>
    </div>
    <div class="w-full h-full bg-white md:rounded-3xl p-3 shadow-md flex flex-col">
        <div class="justify-between items-center flex h-fit md:h-0 md:p-0 p-2 w-full overflow-hidden">
            <div class="flex gap-2 items-center justify-center">
                <img src="{{ asset('assets/mini_logo.png') }}" alt="mini_logo" class="size-10">
                <h1 class="text-[#222222] text-2xl font-bold">Curhatorium</h1>
            </div>
            <button class="end-session-button text-xl rounded-xl bg-none w-fit mt-auto transition-all duration-100 text-red-400 font-bold">
                Akhiri Sesi
            </button>
        </div>
        <div id="chat-messages" class="w-full h-full overflow-auto" style="scrollbar-width: none; -ms-overflow-style: none;"></div>
        <div class="w-full h-fit bg-none flex gap-2">
            <input id="chat-input" type="text" class="form-control w-full rounded-full border border-stone-400 h-fit text-2xl py-3 px-6 shadow-md" placeholder="Type your message here...">
            <button id="chat-send" class="bg-[#49a6a6] h-full w-fit pl-6 pr-5 rounded-full shadow-md hover:bg-[#3b8c8c] flex items-center justify-center">
                <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="#ffffff" class="size-8">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M6 12 3.269 3.125A59.769 59.769 0 0 1 21.485 12 59.768 59.768 0 0 1 3.27 20.875L5.999 12Zm0 0h7.5" />
                </svg>
            </button>
        </div>
    </div>

    <div id="confirmation-modal" class="fixed inset-0 bg-black backdrop-blur-sm bg-opacity-50 flex items-center justify-center z-50 hidden">
        <div class="bg-white rounded-lg p-8 shadow-lg w-full md:w-1/3">
            <h2 class="text-2xl font-bold mb-4">Konfirmasi Akhiri Sesi</h2>
            <p class="mb-6">Apakah Anda yakin ingin mengakhiri sesi ini?</p>
            <div class="flex justify-end gap-4">
                <button id="cancel-button" class="px-4 py-2 bg-gray-300 text-black rounded hover:bg-gray-400">Batal</button>
                <form id="end-session-form" action="" method="POST">
                    @csrf
                    <button id="confirm-button" type="submit" class="px-4 py-2 bg-red-500 text-white rounded hover:bg-red-600">Akhiri Sesi</button>
                </form>
            </div>
        </div>
    </div>

    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script src="https://js.pusher.com/7.0/pusher.min.js"></script>
    <script>
        // Enable pusher logging - don't include this in production
        Pusher.logToConsole = true;

        var pusher = new Pusher('{{ env("PUSHER_APP_KEY") }}', {
            cluster: '{{ env("PUSHER_APP_CLUSTER") }}',
            encrypted: true
        });

        var channel = pusher.subscribe('chat.{{ $room }}');
        channel.bind('App\\Events\\MessageSent', function(data) {
            // build wrapper
            const wrapper = $('<div>').addClass('w-full mb-2').css('display','flex');
            const bubble = $('<div>')
                .addClass('rounded-2xl p-3 max-w-[70%] break-words shadow-sm text-2xl');
            
            // set alignment & colors depending on sender
            if (data.userId == '{{ auth()->id() }}') {
                wrapper.addClass('justify-end hidden');
                bubble.addClass('bg-[#48a6a6] text-white');
            } else {
                wrapper.addClass('justify-start');
                bubble.addClass('bg-stone-200 text-black');
            }

            // set text safely (this escapes any HTML)
            // optionally convert newlines to <br> while staying safe:
            const safeHtml = $('<div>').text(data.message).html().replace(/\n/g, '<br>');
            bubble.html(safeHtml); // safe because we escaped via .text() above

            wrapper.append(bubble);
            $('#chat-messages').append(wrapper);
            $('#chat-messages').scrollTop($('#chat-messages')[0].scrollHeight);
        });

        function sendMessage() {
            var message = $('#chat-input').val();
            if (message) {
                // Optimistic UI: Show message immediately
                const tempId = 'temp_' + Date.now();
                const wrapper = $('<div>').addClass('w-full mb-2 flex justify-end').attr('id', tempId);
                const bubble = $('<div>').addClass('rounded-2xl p-3 max-w-[70%] break-words shadow-sm text-2xl bg-[#48a6a6] text-white flex flex-col items-end gap-2');
                const safeHtml = $('<div>').text(message).html().replace(/\n/g, '<br>');
                const clockIcon = `<svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" class="size-5">
                                        <path stroke-linecap="round" stroke-linejoin="round" d="M12 6v6h4.5m4.5 0a9 9 0 1 1-18 0 9 9 0 0 1 18 0Z" />
                                    </svg>`;
                bubble.html(safeHtml + ' ' + clockIcon);
                wrapper.append(bubble);
                $('#chat-messages').append(wrapper);
                $('#chat-messages').scrollTop($('#chat-messages')[0].scrollHeight);

                $.post("{{ route('pusher.sendMessage') }}", {
                    _token: '{{ csrf_token() }}',
                    message: message,
                    room: '{{ $room }}'
                }).done(function(response) {
                    $('#chat-input').val('');
                    // On success, find the temp message and update its icon
                    const sentBubble = $('#' + tempId).find('div');
                    const checkIcon = `<svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" class="size-5">
                                            <path stroke-linecap="round" stroke-linejoin="round" d="m4.5 12.75 6 6 9-13.5" />
                                        </svg>`;
                    sentBubble.html(safeHtml + ' ' + checkIcon);
                }).fail(function() {
                    alert('Message could not be sent.');
                    // Optionally remove the optimistic message on failure
                    $('#' + tempId).remove();
                });
            }
        }

        $('#chat-send').on('click', function() {
            sendMessage();
        });

        $('#chat-input').on('keypress', function(e) {
            if (e.which == 13) {
                e.preventDefault(); // Prevents form submission
                sendMessage();
            }
        });

        $('.end-session-button').on('click', function() {
            $('#confirmation-modal').removeClass('hidden');
        });

        $('#cancel-button').on('click', function() {
            $('#confirmation-modal').addClass('hidden');
        });
    </script>
</body>
</html>
