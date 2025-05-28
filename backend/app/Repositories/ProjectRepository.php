<?php
// app/Repositories/ProjectRepository.php

namespace App\Repositories;

use App\Models\Project;
use App\Repositories\Contracts\ProjectRepositoryInterface;
use Illuminate\Pagination\LengthAwarePaginator;

class ProjectRepository implements ProjectRepositoryInterface
{
    public function getAllByUser(int $userId, array $filters = []): LengthAwarePaginator
    {
        $query = Project::where('user_id', $userId)->with('tasks');

        if (isset($filters['status'])) {
            $query->where('status', $filters['status']);
        }

        if (isset($filters['search'])) {
            $query->where(function ($q) use ($filters) {
                $q->where('title', 'like', '%' . $filters['search'] . '%')
                    ->orWhere('description', 'like', '%' . $filters['search'] . '%');
            });
        }

        $sortField = $filters['sort_by'] ?? 'created_at';
        $sortDirection = $filters['sort_direction'] ?? 'desc';
        $query->orderBy($sortField, $sortDirection);

        return $query->paginate($filters['per_page'] ?? 15);
    }

    public function create(array $data): Project
    {
        return Project::create($data);
    }

    public function findById(int $id): ?Project
    {
        return Project::with('tasks')->find($id);
    }

    public function update(Project $project, array $data): bool
    {
        return $project->update($data);
    }

    public function delete(Project $project): bool
    {
        return $project->delete();
    }

    public function findByIdAndUser(int $id, int $userId): ?Project
    {
        return Project::where('id', $id)
            ->where('user_id', $userId)
            ->with('tasks')
            ->first();
    }
}
