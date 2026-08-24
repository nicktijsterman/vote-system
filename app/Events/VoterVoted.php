<?php

namespace App\Events;

use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Broadcasting\PrivateChannel;
use Illuminate\Contracts\Broadcasting\ShouldBroadcast;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Collection;

class VoterVoted implements ShouldBroadcast
{
    use Dispatchable, InteractsWithSockets, SerializesModels;

    /** @psalm-suppress PossiblyUnusedProperty Serialized into the broadcast payload by
     * ShouldBroadcast and read by the frontend (LiveApplicationControls.vue) - Psalm can't
     * see either consumer. */
    public float $timestamp;

    /** @psalm-suppress PossiblyUnusedProperty See $timestamp above - same reason. */
    public function __construct(public Collection $results)
    {
        $this->timestamp = microtime(true);
    }

    #[\Override]
    public function broadcastOn(): PrivateChannel
    {
        return new PrivateChannel('results');
    }
}
