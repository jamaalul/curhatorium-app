<?php

namespace App\Http\Controllers;

use App\Ai\Agents\MentAI;
use App\Ai\Agents\TitleGenerator;
use App\Http\Requests\AiGenerateTitleRequest;
use App\Http\Requests\AiSendMessageRequest;
use App\Services\AiTokenWindowService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use Laravel\Ai\Models\Conversation;
use Laravel\Ai\Responses\StreamedAgentResponse;

class AiConversationController extends Controller
{
    /**
     * Start a new AI conversation for the authenticated user.
     */
    public function start(Request $request): JsonResponse
    {
        $conversation = $request->user()->conversations()->create([
            'id' => (string) Str::uuid(),
            'title' => 'Percakapan baru',
        ]);

        return response()->json([
            'conversationId' => $conversation->id,
        ]);
    }

    /**
     * Send a message in an AI conversation and stream the response.
     */
    public function sendMessage(
        AiSendMessageRequest $request,
        Conversation $conversation,
        AiTokenWindowService $windowService,
    ) {
        $user = $request->user();

        $resolved = $windowService->resolveWindowOrFail($user);
        $window = $resolved['window'];

        return (new MentAI)
            ->continue($conversation->id, as: $user)
            ->stream($request->validated('message'))
            ->then(function (StreamedAgentResponse $response) use ($windowService, $window) {
                if ($response->usage) {
                    $windowService->recordTokenUsage(
                        $window,
                        $response->usage->promptTokens,
                        $response->usage->completionTokens,
                    );
                }
            });
    }

    /**
     * Generate a conversation title from the first exchange.
     */
    public function generateTitle(AiGenerateTitleRequest $request, Conversation $conversation): JsonResponse
    {
        if ($conversation->user_id !== $request->user()->id) {
            abort(403);
        }

        $prompt = "User: {$request->validated('message')}\nAssistant: {$request->validated('response')}";

        $result = (new TitleGenerator)->prompt($prompt);

        $title = $result->structured['title'] ?? json_decode($result->text, true)['title'] ?? 'Percakapan baru';

        $conversation->update(['title' => Str::limit($title, 60)]);

        return response()->json(['title' => $conversation->title]);
    }

    /**
     * List the authenticated user's AI conversations.
     */
    public function index(Request $request)
    {
        return $request->user()->conversations()->latest('updated_at')->paginate(20);
    }

    /**
     * Fetch messages for a specific conversation.
     */
    public function showMessages(Request $request, Conversation $conversation)
    {
        if ($conversation->user_id !== $request->user()->id) {
            abort(403);
        }

        return $conversation->messages()->oldest()->get();
    }
}
