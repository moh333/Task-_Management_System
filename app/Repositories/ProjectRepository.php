<?php

namespace App\Repositories;

use App\Models\Project;
use App\Repositories\Contracts\ProjectRepositoryInterface;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Database\Eloquent\Collection;

class ProjectRepository implements ProjectRepositoryInterface
{
    /**
     * Get all projects belonging to a specific user.
     *
     * @return Collection<int, Project>
     */
    public function all(int $userId): Collection
    {
        return Project::query()
            ->where('user_id', $userId)
            ->latest()
            ->get();
    }

    /**
     * Get paginated projects belonging to a specific user.
     *
     * @return LengthAwarePaginator<Project>
     */
    public function paginate(int $userId, int $perPage = 10): LengthAwarePaginator
    {
        return Project::query()
            ->where('user_id', $userId)
            ->latest()
            ->paginate($perPage);
    }

    /**
     * Find a project belonging to a specific user by ID.
     */
    public function find(int $userId, int $projectId): ?Project
    {
        /** @var Project|null */
        return Project::query()
            ->where('user_id', $userId)
            ->where('id', $projectId)
            ->first();
    }

    /**
     * Create a project for a specific user.
     *
     * @param array<string, mixed> $data
     */
    public function create(int $userId, array $data): Project
    {
        /** @var Project */
        return Project::query()->create(array_merge($data, [
            'user_id' => $userId,
        ]));
    }

    /**
     * Update an existing project.
     *
     * @param array<string, mixed> $data
     */
    public function update(Project $project, array $data): Project
    {
        $project->update($data);
        return $project->refresh();
    }

    /**
     * Delete a project.
     */
    public function delete(Project $project): bool
    {
        return (bool) $project->delete();
    }
}
