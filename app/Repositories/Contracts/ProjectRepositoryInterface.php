<?php

namespace App\Repositories\Contracts;

use App\Models\Project;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;

interface ProjectRepositoryInterface
{
    /**
     * Get paginated projects belonging to a specific user.
     *
     * @return LengthAwarePaginator<Project>
     */
    public function paginate(int $userId, int $perPage = 10): LengthAwarePaginator;

    /**
     * Find a project belonging to a specific user by ID.
     */
    public function find(int $userId, int $projectId): ?Project;

    /**
     * Create a project for a specific user.
     *
     * @param array<string, mixed> $data
     */
    public function create(int $userId, array $data): Project;

    /**
     * Update an existing project.
     *
     * @param array<string, mixed> $data
     */
    public function update(Project $project, array $data): Project;

    /**
     * Delete a project.
     */
    public function delete(Project $project): bool;
}
