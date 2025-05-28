<?php

use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return view('welcome');
});

// routes/web.php
Route::get('/test-pusher-localhost', function() {
    try {
        \Illuminate\Support\Facades\Log::info("Testing Pusher connection from localhost...");

        $startTime = microtime(true);

        // Test với task thật
        $task = \App\Models\Task::first();
        if (!$task) {
            return 'No tasks found. Create a task first.';
        }

        // Dispatch event
        broadcast(new \App\Events\TaskUpdated($task));

        $endTime = microtime(true);
        $duration = ($endTime - $startTime) * 1000;

        \Illuminate\Support\Facades\Log::info("Pusher broadcast completed in {$duration}ms");

        return [
            'success' => true,
            'message' => 'Event broadcasted successfully',
            'duration_ms' => round($duration, 2),
            'task_id' => $task->id,
            'project_id' => $task->project_id,
        ];

    } catch (\Exception $e) {
        \Illuminate\Support\Facades\Log::error("Pusher error: " . $e->getMessage());

        return [
            'success' => false,
            'error' => $e->getMessage(),
            'suggestion' => 'Check network connection or try using BROADCAST_DRIVER=log for development'
        ];
    }
});
