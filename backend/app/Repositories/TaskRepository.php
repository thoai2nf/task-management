<?php
// app/Repositories/TaskRepository.php

namespace App\Repositories;

use App\Models\Task;
use App\Repositories\Contracts\TaskRepositoryInterface;
use Illuminate\Database\Eloquent\Collection;

class TaskRepository implements TaskRepositoryInterface
{
    public function getAllByProject(int $projectId, array $filters = []): Collection
    {
        $query = Task::where('project_id', $projectId);

        if (isset($filters['status'])) {
            $query->where('status', $filters['status']);
        }

        if (isset($filters['priority'])) {
            $query->where('priority', $filters['priority']);
        }

        if (isset($filters['search'])) {
            $query->where(function ($q) use ($filters) {
                $q->where('title', 'like', '%' . $filters['search'] . '%')
                    ->orWhere('description', 'like', '%' . $filters['search'] . '%');
            });
        }

        $sortField = $filters['sort_by'] ?? 'order';
        $sortDirection = $filters['sort_direction'] ?? 'asc';
        $query->orderBy($sortField, $sortDirection);

        return $query->get();
    }

    public function create(array $data): Task
    {
        // Auto increment order if not provided
        if (!isset($data['order'])) {
            $lastTask = Task::where('project_id', $data['project_id'])
                ->orderBy('order', 'desc')
                ->first();
            $data['order'] = $lastTask ? $lastTask->order + 1 : 1;
        }

        return Task::create($data);
    }

    public function findById(int $id): ?Task
    {
        return Task::with('project')->find($id);
    }

    public function update(Task $task, array $data): bool
    {
        return $task->update($data);
    }

    public function delete(Task $task): bool
    {
        return $task->delete();
    }

    public function updateOrder(array $tasks): bool
    {
        foreach ($tasks as $taskData) {
            Task::where('id', $taskData['id'])
                ->update(['order' => $taskData['order']]);
        }
        return true;
    }
}
