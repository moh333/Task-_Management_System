<?php

namespace App\Repositories\Contracts;

use App\Models\Task;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;

interface TaskRepositoryInterface
{
    /**
     * Get paginated tasks belonging to a user (and optionally a specific project) with filters.
     *
     * @param array<string, mixed> $filters
     * @return LengthAwarePaginator<Task>
     */
    public function paginate(int $userId, ?int $projectId = null, array $filters = [], int $perPage = 10): LengthAwarePaginator;

    /**
     * Find a task belonging to a specific user by task ID.
     */
    public function find(int $userId, int $taskId): ?Task;

    /**
     * Create a task for a project.
     *
     * @param array<string, mixed> $data
     */
    public function create(array $data): Task;

    /**
     * Update an existing task.
     *
     * @param array<string, mixed> $data
     */
    public function update(Task $task, array $data): Task;

    /**
     * Soft delete a task.
     */
    public function delete(Task $task): bool;
}
