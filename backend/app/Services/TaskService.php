<?php
// app/Services/TaskService.php

namespace App\Services;

use App\Models\Task;
use App\Repositories\Contracts\TaskRepositoryInterface;
use App\Repositories\Contracts\ProjectRepositoryInterface;
use Illuminate\Database\Eloquent\Collection;

class TaskService
{
    public function __construct(
        private TaskRepositoryInterface $taskRepository,
        private ProjectRepositoryInterface $projectRepository
    ) {}

    public function getAllByProject(int $projectId, int $userId, array $filters = []): Collection
    {
        // Verify project belongs to user
        $project = $this->projectRepository->findByIdAndUser($projectId, $userId);
        if (!$project) {
            throw new \Exception('Project not found', 404);
        }

        return $this->taskRepository->getAllByProject($projectId, $filters);
    }

    public function create(array $data, int $projectId, int $userId): Task
    {
        // Verify project belongs to user
        $project = $this->projectRepository->findByIdAndUser($projectId, $userId);
        if (!$project) {
            throw new \Exception('Project not found', 404);
        }

        $data['project_id'] = $projectId;
        return $this->taskRepository->create($data);
    }

    public function findById(int $id, int $userId): ?Task
    {
        $task = $this->taskRepository->findById($id);

        if (!$task || $task->project->user_id !== $userId) {
            return null;
        }

        return $task;
    }

    public function update(int $id, array $data, int $userId): Task
    {
        $task = $this->findById($id, $userId);

        if (!$task) {
            throw new \Exception('Task not found', 404);
        }

        $this->taskRepository->update($task, $data);
        return $task->fresh();
    }

    public function delete(int $id, int $userId): bool
    {
        $task = $this->findById($id, $userId);

        if (!$task) {
            throw new \Exception('Task not found', 404);
        }

        return $this->taskRepository->delete($task);
    }

    public function updateOrder(array $tasks, int $userId): bool
    {
        // Verify all tasks belong to user
        foreach ($tasks as $taskData) {
            $task = $this->findById($taskData['id'], $userId);
            if (!$task) {
                throw new \Exception('Task not found: ' . $taskData['id'], 404);
            }
        }

        return $this->taskRepository->updateOrder($tasks);
    }
}
