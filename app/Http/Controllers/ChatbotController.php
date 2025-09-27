<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Services\ChatbotService;
use App\Http\Requests\ChatbotMessageRequest;
use App\Models\ChatbotChatMessageV2;
use App\Models\ChatbotChatV2;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
use Gemini\Data\Content;
use Gemini\Data\Part;
use Gemini\Enums\Role;
use Gemini\Laravel\Facades\Gemini;
use Symfony\Component\HttpFoundation\StreamedResponse;

class ChatbotController extends Controller
{
    public function __construct(
        private ChatbotService $chatbotService
    ) {}

    public function index(Request $request) {
        $user = Auth::user();
        $chats = ChatbotChatV2::where('user_id', $user->id)
            ->orderBy('created_at', 'desc')
            ->get(['title', 'identifier']);

        return view('chatbot.index', compact('user', 'chats'));
    }

    public function chat(Request $request, $identifier) {
        $user = Auth::user();
        $chats = ChatbotChatV2::where('user_id', $user->id)
            ->orderBy('created_at', 'desc')
            ->get(['title', 'identifier']);
        $activeChat = ChatbotChatV2::where('identifier', $identifier)->where('user_id', $user->id)->firstOrFail();
        $messages = $activeChat->messages()->orderBy('created_at', 'asc')->get();

        return view('chatbot.chat', compact('user', 'chats', 'activeChat', 'messages'));
    }

    public function send(Request $request, $identifier) {
        $user = Auth::user();
        $message = $request->input('message');
        $chat = ChatbotChatV2::where('identifier', $identifier)->where('user_id', $user->id)->firstOrFail();

        $chat->messages()->create([
            'message' => $message,
            'role' => 'user',
        ]);

        $history = $this->buildChatHistory($chat);

        $geminiChat = Gemini::chat(model: 'gemini-2.0-flash')
            ->startChat(history: $history);

        $response = $geminiChat->sendMessage($message);

        $responseText = $response->text();

        $chat->messages()->create([
            'message' => $responseText,
            'role' => 'assistant',
        ]);

        return response()->json($responseText);
    }

    public function stream(Request $request, $identifier) {
        $user = Auth::user();
        $message = $request->input('message');
        $chat = ChatbotChatV2::where('identifier', $identifier)->where('user_id', $user->id)->firstOrFail();

        $chat->messages()->create([
            'message' => $message,
            'role' => 'user',
        ]);

        $history = $this->buildChatHistory($chat);

        $history[] = Content::parse(part: $message, role: Role::USER);

        $stream = Gemini::generativeModel('gemini-2.0-flash')
            ->streamGenerateContent(...$history);

        $response = new StreamedResponse(function () use ($stream) {
            if (ob_get_level() > 0) {
                ob_end_clean();
            }

            foreach ($stream as $response) {
                echo "data: " . json_encode(['text' => $response->text()]) . "\n\n";
                flush();
            }

            echo "data: " . json_encode(['done' => true]) . "\n\n";
            flush();
        });

        $response->headers->set('Content-Type', 'text/event-stream');
        $response->headers->set('X-Accel-Buffering', 'no');
        $response->headers->set('Cache-Control', 'no-cache, private');
        $response->headers->set('Connection', 'keep-alive');

        return $response;
    }

    public function saveMessage(Request $request, $identifier) {
        $user = Auth::user();
        $message = $request->input('message');
        $chat = ChatbotChatV2::where('identifier', $identifier)->where('user_id', $user->id)->firstOrFail();

        $chat->messages()->create([
            'message' => $message,
            'role' => 'assistant',
        ]);

        return response()->json(['success' => true]);
    }

    public function createSend(Request $request)
    {
        $user = Auth::user();
        $message = $request->input('message');
        $identifier = uniqid('_', true);
        $mode = $request->input('mode', 'friendly');
        $title = Gemini::generativeModel(model: 'gemini-2.0-flash')
            ->generateContent(
                'Buat satu frasa singkat maksimal 3 kata yang merepresentasikan obrolan berikut: ' . $message
            )
            ->text();

        $chat = ChatbotChatV2::create([
            'user_id' => $user->id,
            'title' => $title,
            'identifier' => $identifier,
            'mode' => $mode,
        ]);

        $chat->messages()->create([
            'message' => $message,
            'role' => 'user',
        ]);

        $chatInstance = Gemini::chat(model: 'gemini-2.0-flash')->startChat();
        $response = $chatInstance->sendMessage($message);

        $chat->messages()->create([
            'message' => $response->text(),
            'role' => 'assistant',
        ]);

        return redirect()->route('chatbot.chat', ['identifier' => $identifier]);
    }

    private function buildChatHistory(ChatbotChatV2 $chat): array
    {
        $previousMessages = $chat->messages()->orderBy('created_at', 'asc')->get();
        $history = [];

        foreach ($previousMessages as $prevMessage) {
            $role = $prevMessage->role === 'assistant' ? Role::MODEL : Role::USER;
            $history[] = Content::parse(part: $prevMessage->message, role: $role);
        }

        return $history;
    }
}
