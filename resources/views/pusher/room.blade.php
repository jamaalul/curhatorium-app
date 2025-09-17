<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>Pusher Chat - Room {{ $room }}</title>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="bg-gray-100">
    <div class="container mx-auto p-4">
        <div class="flex justify-between items-center mb-4">
            <h1 class="text-4xl font-bold">Chat Room: {{ $room }}</h1>
            <form action="{{ route('pusher.terminate', ['room' => $room]) }}" method="POST">
                @csrf
                <button type="submit" class="bg-red-500 hover:bg-red-700 text-white font-bold py-2 px-4 rounded">
                    Terminate Session
                </button>
            </form>
        </div>

        <div class="mb-4">
            <p>Share this link to invite someone:</p>
            <input type="text" value="{{ route('pusher.room', ['room' => $room]) }}" id="invitation-link" readonly class="w-full p-2 border rounded">
            <button onclick="copyLink()" class="bg-blue-500 hover:bg-blue-700 text-white font-bold py-2 px-4 rounded mt-2">
                Copy Link
            </button>
        </div>

        <div id="chat-box" class="w-full h-96 bg-white border rounded p-4 overflow-y-auto mb-4">
            <!-- Messages will appear here -->
        </div>

        <form id="message-form">
            <div class="flex">
                <input type="text" id="message-input" class="w-full p-2 border rounded-l" placeholder="Type your message...">
                <button type="submit" class="bg-green-500 hover:bg-green-700 text-white font-bold py-2 px-4 rounded-r">
                    Send
                </button>
            </div>
        </form>
    </div>

    <script>
        function copyLink() {
            const link = document.getElementById('invitation-link');
            link.select();
            document.execCommand('copy');
            alert('Invitation link copied to clipboard!');
        }

        document.addEventListener('DOMContentLoaded', () => {
            const chatBox = document.getElementById('chat-box');
            const messageForm = document.getElementById('message-form');
            const messageInput = document.getElementById('message-input');
            const room = '{{ $room }}';

            window.Echo.private(`chat.${room}`)
                .listen('MessageSent', (e) => {
                    const messageElement = document.createElement('p');
                    messageElement.innerText = e.message;
                    chatBox.appendChild(messageElement);
                    chatBox.scrollTop = chatBox.scrollHeight;
                    console.log(e);
                });

            messageForm.addEventListener('submit', (e) => {
                e.preventDefault();
                const message = messageInput.value;
                if (message.trim() === '') return;

                fetch('{{ route('pusher.sendMessage') }}', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
                    },
                    body: JSON.stringify({ message, room })
                });

                messageInput.value = '';
            });
        });
    </script>
</body>
</html>