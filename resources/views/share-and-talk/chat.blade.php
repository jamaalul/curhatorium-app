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
    {{-- <div class="container">
        <div class="row">
            <div class="col-md-8 col-md-offset-2">
                <div class="panel panel-default border-1 w-full">
                    <div class="panel-heading">
                        <h4>Chat Room: {{ $room }}</h4>
                    </div>
                    <div class="panel-body">
                        <div id="chat-messages"></div>
                    </div>
                    <div class="panel-footer">
                        <div class="input-group">
                            <input id="chat-input" type="text" class="form-control" placeholder="Type your message here...">
                            <span class="input-group-btn">
                                <button id="chat-send" class="btn btn-primary">Send</button>
                            </span>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div> --}}
    <div class="w-0 md:w-1/5 md:min-w-80 h-full bg-stone-200"></div>
    <div class="w-full h-full bg-white rounded-2xl p-6 shadow-md flex flex-col gap-4">
        <div id="chat-messages" class="w-full h-full overflow-auto"></div>
        <div class="w-full h-fit bg-none flex gap-2">
            <input id="chat-input" type="text" class="form-control w-full rounded-full border h-fit text-2xl py-3 px-6 shadow-md" placeholder="Type your message here...">
            <button id="chat-send" class="bg-[#49a6a6] h-full w-fit pl-6 pr-5 rounded-full shadow-md hover:bg-[#3b8c8c] flex items-center justify-center">
                <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="#ffffff" class="size-8">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M6 12 3.269 3.125A59.769 59.769 0 0 1 21.485 12 59.768 59.768 0 0 1 3.27 20.875L5.999 12Zm0 0h7.5" />
                </svg>
            </button>
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
                .addClass('rounded-2xl p-3 max-w-[70%] break-words shadow-sm');

            // set alignment & colors depending on sender
            if (data.userId == '{{ auth()->id() }}') {
                wrapper.addClass('justify-end');
                bubble.addClass('bg-[#49a6a6] text-white');
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
                $.post("{{ route('pusher.sendMessage') }}", {
                    _token: '{{ csrf_token() }}',
                    message: message,
                    room: '{{ $room }}'
                }).done(function() {
                    $('#chat-input').val('');
                }).fail(function() {
                    alert('Message could not be sent.');
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
    </script>
</body>
</html>
