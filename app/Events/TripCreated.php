<?php

namespace App\Events;

use Illuminate\Broadcasting\Channel;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Broadcasting\PresenceChannel;
use Illuminate\Broadcasting\PrivateChannel;
use Illuminate\Contracts\Broadcasting\ShouldBroadcast;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;
use App\Models\Trip;
use App\Models\User;
use Illuminate\Support\Facades\Log;


class TripCreated implements ShouldBroadcast
{
    use Dispatchable, InteractsWithSockets, SerializesModels;

    public $trip;
    private $user;
    /**
     * Create a new event instance.
     */
    public function __construct(Trip $trip, User $user)
    {
        $this->trip = $trip;
        $this->user = $user;

        \Log::info('TripCreated event dispatched', [
        'trip_id' => $trip->id,
        'user_id' => $user->id,
    ]);
    }

    public function broadcastWith(): array
    {
        return [
            'trip' => $this->trip,
            'user' => $this->user,
        ];
    }
 
    public function broadcastOn(): array
    {
        \Log::info('Broadcasting TripCreated to driver channel');
        return [
            new Channel('driver'),
        ];
    }
}
