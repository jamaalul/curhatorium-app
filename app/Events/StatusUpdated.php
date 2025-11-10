<?php

namespace App\Events;

use Illuminate\Broadcasting\Channel;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Broadcasting\PresenceChannel;
use Illuminate\Broadcasting\PrivateChannel;
use Illuminate\Contracts\Broadcasting\ShouldBroadcast;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class StatusUpdated implements ShouldBroadcast
{
    use Dispatchable, InteractsWithSockets, SerializesModels;

    public $room;
    public $statusType;
    public $status;
    public $consultation;

    /**
     * Create a new event instance.
     */
    public function __construct(string $room, string $statusType, string $status, \App\Models\Consultation $consultation)
    {
        $this->room = $room;
        $this->statusType = $statusType;
        $this->status = $status;
        $this->consultation = $consultation;
    }

    /**
     * Get the channels the event should broadcast on.
     *
     * @return array<int, \Illuminate\Broadcasting\Channel>
     */
    public function broadcastOn(): array
    {
        return [
            new Channel('chat.' . $this->room),
        ];
    }

    public function broadcastAs(): string
    {
        return 'StatusUpdated';
    }

    public function broadcastWith(): array
    {
        return [
            'room' => $this->room,
            'status_type' => $this->statusType,
            'status' => $this->status,
            'consultation' => [
                'id' => $this->consultation->id,
                'facilitator_status' => $this->consultation->facilitator_status,
                'client_status' => $this->consultation->client_status,
            ]
        ];
    }
}
