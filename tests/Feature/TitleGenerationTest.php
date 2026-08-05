<?php

namespace Tests\Feature;

use App\Ai\Agents\TitleGenerator;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Str;
use Tests\TestCase;

class TitleGenerationTest extends TestCase
{
    use RefreshDatabase;

    public function test_title_generation_updates_conversation_title(): void
    {
        TitleGenerator::fake([
            ['title' => 'Curhat Soal Kerjaan'],
        ]);

        $user = User::factory()->create();
        $conversation = $user->conversations()->create([
            'id' => (string) Str::uuid(),
            'title' => 'Percakapan baru',
        ]);

        $response = $this->actingAs($user)->postJson("/ai/{$conversation->id}/generate-title", [
            'message' => 'Aku lagi stress banget soal kerjaan',
            'response' => 'Aku dengar kamu. Mau cerita lebih lanjut tentang apa yang bikin stress?',
        ]);

        $response->assertOk()
            ->assertJsonStructure(['title'])
            ->assertJsonPath('title', 'Curhat Soal Kerjaan');

        $this->assertDatabaseHas('agent_conversations', [
            'id' => $conversation->id,
            'title' => 'Curhat Soal Kerjaan',
        ]);

        TitleGenerator::assertPrompted(fn ($prompt) => str_contains(is_string($prompt) ? $prompt : $prompt->prompt, 'stress banget soal kerjaan'));
    }

    public function test_title_generation_requires_authentication(): void
    {
        $user = User::factory()->create();
        $conversation = $user->conversations()->create([
            'id' => (string) Str::uuid(),
            'title' => 'Percakapan baru',
        ]);

        $response = $this->postJson("/ai/{$conversation->id}/generate-title", [
            'message' => 'Hello',
            'response' => 'Hi there',
        ]);

        $response->assertUnauthorized();
    }

    public function test_title_generation_forbidden_for_other_users_conversation(): void
    {
        TitleGenerator::fake();

        $owner = User::factory()->create();
        $otherUser = User::factory()->create();

        $conversation = $owner->conversations()->create([
            'id' => (string) Str::uuid(),
            'title' => 'Percakapan baru',
        ]);

        $response = $this->actingAs($otherUser)->postJson("/ai/{$conversation->id}/generate-title", [
            'message' => 'Hello',
            'response' => 'Hi there',
        ]);

        $response->assertForbidden();
    }

    public function test_title_generation_validates_required_fields(): void
    {
        TitleGenerator::fake();

        $user = User::factory()->create();
        $conversation = $user->conversations()->create([
            'id' => (string) Str::uuid(),
            'title' => 'Percakapan baru',
        ]);

        $response = $this->actingAs($user)->postJson("/ai/{$conversation->id}/generate-title", []);

        $response->assertUnprocessable()
            ->assertJsonValidationErrors(['message', 'response']);
    }
}
