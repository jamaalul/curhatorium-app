<?php

namespace Tests\Feature;

use App\Ai\Agents\MentAI;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Str;
use Tests\TestCase;

class AiConversationTest extends TestCase
{
    use RefreshDatabase;

    public function test_start_creates_new_conversation(): void
    {
        $user = User::factory()->create();

        $response = $this->actingAs($user)->postJson('/ai/start');

        $response->assertOk()
            ->assertJsonStructure(['conversationId']);

        $conversationId = $response->json('conversationId');

        $this->assertDatabaseHas('agent_conversations', [
            'id' => $conversationId,
            'user_id' => $user->id,
            'title' => 'Percakapan baru',
        ]);
    }

    public function test_start_requires_authentication(): void
    {
        $response = $this->postJson('/ai/start');

        $response->assertUnauthorized();
    }

    public function test_index_lists_user_conversations(): void
    {
        $user = User::factory()->create();
        $otherUser = User::factory()->create();

        $userConversation = $user->conversations()->create([
            'id' => (string) Str::uuid(),
            'title' => 'User Conversation',
        ]);

        $otherUser->conversations()->create([
            'id' => (string) Str::uuid(),
            'title' => 'Other Conversation',
        ]);

        $response = $this->actingAs($user)->getJson('/ai/conversations');

        $response->assertOk()
            ->assertJsonCount(1, 'data')
            ->assertJsonPath('data.0.id', $userConversation->id);
    }

    public function test_show_messages_returns_conversation_messages(): void
    {
        $user = User::factory()->create();
        $conversation = $user->conversations()->create([
            'id' => (string) Str::uuid(),
            'title' => 'Test Conversation',
        ]);

        $conversation->messages()->create([
            'id' => (string) Str::uuid(),
            'agent' => MentAI::class,
            'role' => 'user',
            'content' => 'Hello',
            'attachments' => [],
            'tool_calls' => [],
            'tool_results' => [],
            'usage' => [],
            'meta' => [],
        ]);

        $response = $this->actingAs($user)->getJson("/ai/{$conversation->id}/messages");

        $response->assertOk()
            ->assertJsonCount(1)
            ->assertJsonPath('0.content', 'Hello');
    }

    public function test_show_messages_forbidden_for_other_users(): void
    {
        $owner = User::factory()->create();
        $otherUser = User::factory()->create();

        $conversation = $owner->conversations()->create([
            'id' => (string) Str::uuid(),
            'title' => 'Secret Conversation',
        ]);

        $response = $this->actingAs($otherUser)->getJson("/ai/{$conversation->id}/messages");

        $response->assertForbidden();
    }

    public function test_send_message_validates_required_fields(): void
    {
        MentAI::fake();

        $user = User::factory()->create();
        $conversation = $user->conversations()->create([
            'id' => (string) Str::uuid(),
            'title' => 'Test Conversation',
        ]);

        $response = $this->actingAs($user)->postJson("/ai/{$conversation->id}/message", []);

        $response->assertUnprocessable()
            ->assertJsonValidationErrors(['message']);
    }
}
