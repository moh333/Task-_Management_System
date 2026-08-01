<?php

namespace App\Services;

use App\Models\Project;
use App\Repositories\Contracts\ProjectRepositoryInterface;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Database\Eloquent\Collection;

class ProjectService
{
    public function __construct(
        protected ProjectRepositoryInterface $projectRepository
    ) {}

    /**
     * Get all projects belonging to the user.
     *
     * @return Collection<int, Project>
     */
    public function getUserProjects(int $userId): Collection
    {
        return $this->projectRepository->all($userId);
    }

    /**
     * Get paginated projects belonging to the user.
     *
     * @return LengthAwarePaginator<Project>
     */
    public function getPaginatedUserProjects(int $userId, int $perPage = 10): LengthAwarePaginator
    {
        return $this->projectRepository->paginate($userId, $perPage);
    }

    /**
     * Find a user's project by ID.
     */
    public function getUserProject(int $userId, int $projectId): ?Project
    {
        return $this->projectRepository->find($userId, $projectId);
    }

    /**
     * Create a project for the user.
     *
     * @param array<string, mixed> $data
     */
    public function createProject(int $userId, array $data): Project
    {
        return $this->projectRepository->create($userId, $data);
    }

    /**
     * Update a user project.
     *
     * @param array<string, mixed> $data
     */
    public function updateProject(Project $project, array $data): Project
    {
        return $this->projectRepository->update($project, $data);
    }

    /**
     * Delete a user project.
     */
    public function deleteProject(Project $project): bool
    {
        return $this->projectRepository->delete($project);
    }
}
