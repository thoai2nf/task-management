<?php
// app/Events/TaskUpdated.php

namespace App\Events;

use App\Models\Task;
use Illuminate\Broadcasting\Channel;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Broadcasting\PresenceChannel;
use Illuminate\Broadcasting\PrivateChannel;
use Illuminate\Contracts\Broadcasting\ShouldBroadcast;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Log;

class TaskUpdated implements ShouldBroadcast
{
    use Dispatchable, InteractsWithSockets, SerializesModels;

    public function __construct(
        public Task $task
    ) {}

    public function broadcastOn(): array
    {
        Log::info("Task Updated task project ".$this->task->project_id);
        return [
            new PrivateChannel('project.' . $this->task->project_id),
        ];
    }

    public function broadcastWith(): array
    {
        return [
            'task' => $this->task->toArray(),
            'action' => 'updated'
        ];
    }

    public function broadcastAs(): string
    {
        return 'task.updated';
    }
}
