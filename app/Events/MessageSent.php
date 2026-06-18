<?php

namespace App\Events;

use App\Models\ConsultationMessage;
use Illuminate\Broadcasting\Channel;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Contracts\Broadcasting\ShouldBroadcast;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class MessageSent implements ShouldBroadcast
{
    use Dispatchable, InteractsWithSockets, SerializesModels;

    public $message;

    public $room;

    /**
     * Create a new event instance.
     */
    public function __construct(ConsultationMessage $message)
    {
        $this->message = $message;
        $this->room = $message->consultation->room ?? 'default';
    }

    /**
     * Get the channels the event should broadcast on.
     *
     * @return array<int, Channel>
     */
    public function broadcastOn()
    {
        return new Channel('chat.'.$this->room);
    }

    /**
     * The event's broadcast name.
     *
     * @return string
     */
    public function broadcastAs()
    {
        return 'MessageSent';
    }

    /**
     * Get the data to broadcast.
     *
     * @return array<string, mixed>
     */
    public function broadcastWith()
    {
        return [
            'message' => [
                'id' => $this->message->id,
                'message' => $this->message->message,
                'sender' => [
                    'id' => $this->message->sender->id,
                    'type' => $this->message->sender_type,
                ],
                'created_at' => $this->message->created_at->toIso8601String(),
            ],
            'room' => $this->room,
        ];
    }
}
