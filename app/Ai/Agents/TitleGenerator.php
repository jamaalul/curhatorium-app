<?php

namespace App\Ai\Agents;

use Illuminate\Contracts\JsonSchema\JsonSchema;
use Laravel\Ai\Attributes\Model;
use Laravel\Ai\Attributes\Provider;
use Laravel\Ai\Contracts\Agent;
use Laravel\Ai\Contracts\HasStructuredOutput;
use Laravel\Ai\Promptable;

class TitleGenerator implements Agent, HasStructuredOutput
{
    use Promptable;

    /**
     * Get the configured provider and model.
     *
     * @return array<string, string>
     */
    public function provider(): array
    {
        return [
            'gemini' => 'gemini-3.1-flash-lite',
        ];
    }

    /**
     * Get the instructions that the agent should follow.
     */
    public function instructions(): string
    {
        return <<<'EOT'
        You generate short, descriptive conversation titles (max 4 words).
        Given a user message and an AI response, produce a concise title
        that captures the main topic. Write the title in the same language
        as the user message. Do not use quotes or punctuation at the end.
        EOT;
    }

    /**
     * Get the agent's structured output schema definition.
     */
    public function schema(JsonSchema $schema): array
    {
        return [
            'title' => $schema->string()->required(),
        ];
    }
}
