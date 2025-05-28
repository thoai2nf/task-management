<?php
// app/Repositories/Contracts/TaskRepositoryInterface.php

namespace App\Repositories\Contracts;

use App\Models\Task;
use Illuminate\Database\Eloquent\Collection;

interface TaskRepositoryInterface
{
    public function getAllByProject(int $projectId, array $filters = []): Collection;
    public function create(array $data): Task;
    public function findById(int $id): ?Task;
    public function update(Task $task, array $data): bool;
    public function delete(Task $task): bool;
    public function updateOrder(array $tasks): bool;
}
