<?php
// app/Events/TaskCreated.php

namespace App\Events;

use App\Models\Task;
use Illuminate\Broadcasting\Channel;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Broadcasting\PresenceChannel;
use Illuminate\Broadcasting\PrivateChannel;
use Illuminate\Contracts\Broadcasting\ShouldBroadcast;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class TaskCreated implements ShouldBroadcast
{
    use Dispatchable, InteractsWithSockets, SerializesModels;

    public function __construct(
        public Task $task
    ) {}

    public function broadcastOn(): array
    {
        return [
            new Channel('project.' . $this->task->project_id),
        ];
    }

    public function broadcastWith(): array
    {
        return [
            'task' => $this->task->toArray(),
            'action' => 'created'
        ];
    }

    public function broadcastAs(): string
    {
        return 'task.created';
    }
}
