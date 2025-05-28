<?php
// app/Services/ProjectService.php

namespace App\Services;

use App\Models\Project;
use App\Repositories\Contracts\ProjectRepositoryInterface;
use Illuminate\Pagination\LengthAwarePaginator;

class ProjectService
{
    public function __construct(
        private ProjectRepositoryInterface $projectRepository
    ) {}

    public function getAllByUser(int $userId, array $filters = []): LengthAwarePaginator
    {
        return $this->projectRepository->getAllByUser($userId, $filters);
    }

    public function create(array $data, int $userId): Project
    {
        $data['user_id'] = $userId;
        return $this->projectRepository->create($data);
    }

    public function findById(int $id): ?Project
    {
        return $this->projectRepository->findById($id);
    }

    public function findByIdAndUser(int $id, int $userId): ?Project
    {
        return $this->projectRepository->findByIdAndUser($id, $userId);
    }

    public function update(int $id, array $data, int $userId): Project
    {
        $project = $this->projectRepository->findByIdAndUser($id, $userId);

        if (!$project) {
            throw new \Exception('Project not found', 404);
        }

        $this->projectRepository->update($project, $data);
        return $project->fresh();
    }

    public function delete(int $id, int $userId): bool
    {
        $project = $this->projectRepository->findByIdAndUser($id, $userId);

        if (!$project) {
            throw new \Exception('Project not found', 404);
        }

        return $this->projectRepository->delete($project);
    }
}
