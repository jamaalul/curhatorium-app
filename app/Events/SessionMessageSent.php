<?php

namespace App\Events;

use App\Models\Message;
use Illuminate\Broadcasting\Channel;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Contracts\Broadcasting\ShouldBroadcast;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class SessionMessageSent implements ShouldBroadcast
{
    use Dispatchable, InteractsWithSockets, SerializesModels;

    public Message $message;

    public string $sessionId;

    public function __construct(Message $message, string $sessionId)
    {
        $this->message = $message;
        $this->sessionId = $sessionId;
    }

    /**
     * @return array<int, Channel>
     */
    public function broadcastOn(): array
    {
        return [
            new Channel('session.'.$this->sessionId),
        ];
    }

    public function broadcastAs(): string
    {
        return 'SessionMessageSent';
    }

    /**
     * @return array<string, mixed>
     */
    public function broadcastWith(): array
    {
        return [
            'session_id' => $this->sessionId,
            'message' => [
                'id' => $this->message->id,
                'message' => $this->message->message,
                'sender_type' => $this->message->sender_type,
                'sender_id' => $this->message->sender_id,
                'created_at' => $this->message->created_at?->toIso8601String(),
            ],
        ];
    }
}
