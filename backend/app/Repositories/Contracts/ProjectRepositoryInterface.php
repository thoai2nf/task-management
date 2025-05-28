<?php

namespace App\Repositories\Contracts;

use App\Models\Project;
use Illuminate\Pagination\LengthAwarePaginator;

interface ProjectRepositoryInterface
{
    public function getAllByUser(int $userId, array $filters = []): LengthAwarePaginator;
    public function create(array $data): Project;
    public function findById(int $id): ?Project;
    public function update(Project $project, array $data): bool;
    public function delete(Project $project): bool;
    public function findByIdAndUser(int $id, int $userId): ?Project;
}
