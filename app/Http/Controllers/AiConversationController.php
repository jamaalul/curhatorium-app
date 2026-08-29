<?php

namespace App\Http\Controllers;

use App\Ai\Agents\MentAI;
use App\Ai\Agents\TitleGenerator;
use App\Http\Requests\AiGenerateTitleRequest;
use App\Http\Requests\AiSendMessageRequest;
use App\Services\AiTokenWindowService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Support\Str;
use Illuminate\View\View;
use Laravel\Ai\Models\Conversation;
use Laravel\Ai\Responses\StreamedAgentResponse;

class AiConversationController extends Controller
{
    /**
     * Show the new AI chat interface.
     */
    public function create(Request $request): View
    {
        $paginator = $request->user()->conversations()->latest('updated_at')->paginate(20);

        return view('ai.index', [
            'initialConversations' => $this->formatConversations($paginator),
        ]);
    }

    /**
     * Show a specific AI conversation.
     */
    public function show(Request $request, Conversation $conversation): View
    {
        if ($conversation->user_id !== $request->user()->id) {
            abort(403);
        }

        $messages = $conversation->messages()
            ->select(['id', 'conversation_id', 'role', 'content', 'created_at'])
            ->oldest()
            ->get();
        $paginator = $request->user()->conversations()->latest('updated_at')->paginate(20);

        return view('ai.show', [
            'conversation' => $conversation,
            'initialMessages' => $messages,
            'initialConversations' => $this->formatConversations($paginator),
        ]);
    }

    /**
     * Format paginator into a safe plain array for JS injection.
     *
     * @param  LengthAwarePaginator  $paginator
     * @return array{data: array<mixed>, current_page: int, next_page_url: string|null}
     */
    private function formatConversations($paginator): array
    {
        return [
            'data' => $paginator->items(),
            'current_page' => $paginator->currentPage(),
            'next_page_url' => $paginator->nextPageUrl(),
        ];
    }

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

        try {
            $resolved = $windowService->resolveWindowOrFail($user);
            $window = $resolved['window'];
        } catch (\Throwable $e) {
            \Illuminate\Support\Facades\Log::warning('MentAI resolveWindowOrFail failed', [
                'user_id' => $user->id,
                'error' => $e->getMessage(),
            ]);
            throw $e;
        }

        try {
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
        } catch (\Throwable $e) {
            \Illuminate\Support\Facades\Log::error('MentAI Stream Initiation Error', [
                'user_id' => $user->id,
                'conversation_id' => $conversation->id,
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString(),
            ]);
            throw $e;
        }
    }

    /**
     * Generate a conversation title from the first exchange.
     */
    public function generateTitle(AiGenerateTitleRequest $request, Conversation $conversation): JsonResponse
    {
        if ($conversation->user_id !== $request->user()->id) {
            abort(403);
        }

        $userMsg = $request->validated('message');
        $assistantMsg = $request->validated('response');

        try {
            $prompt = 'User: '.Str::limit($userMsg, 200)."\nAssistant: ".Str::limit($assistantMsg, 200);

            $result = (new TitleGenerator)->prompt($prompt);

            $title = $result->structured['title'] ?? json_decode($result->text, true)['title'] ?? null;
        } catch (\Throwable) {
            $title = null;
        }

        if (! $title || trim($title) === '') {
            $title = Str::limit($userMsg, 40, '...');
        }

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

        return $conversation->messages()
            ->select(['id', 'conversation_id', 'role', 'content', 'created_at'])
            ->oldest()
            ->get();
    }
}
