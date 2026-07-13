<?php

namespace App\Events;

use Illuminate\Broadcasting\Channel;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Contracts\Broadcasting\ShouldBroadcast;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class SessionForceEnded implements ShouldBroadcast
{
    use Dispatchable, InteractsWithSockets, SerializesModels;

    public string $sessionId;

    public string $message;

    public function __construct(string $sessionId, string $message = 'Sesi telah berakhir secara otomatis karena batas waktu.')
    {
        $this->sessionId = $sessionId;
        $this->message = $message;
    }

    public function broadcastOn(): array
    {
        return [
            new Channel('session.'.$this->sessionId),
        ];
    }

    public function broadcastAs(): string
    {
        return 'SessionForceEnded';
    }

    public function broadcastWith(): array
    {
        return [
            'session_id' => $this->sessionId,
            'message' => $this->message,
        ];
    }
}
