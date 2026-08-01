<?php

namespace App\Services;

use App\Models\Task;
use App\Repositories\Contracts\ProjectRepositoryInterface;
use App\Repositories\Contracts\TaskRepositoryInterface;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;

class TaskService
{
    public function __construct(
        protected TaskRepositoryInterface $taskRepository,
        protected ProjectRepositoryInterface $projectRepository
    ) {}

    /**
     * Get paginated tasks for a user with optional project_id and filters.
     *
     * @param array<string, mixed> $filters
     * @return LengthAwarePaginator<Task>|null
     */
    public function getPaginatedUserTasks(int $userId, ?int $projectId = null, array $filters = [], int $perPage = 10): ?LengthAwarePaginator
    {
        if ($projectId !== null) {
            $project = $this->projectRepository->find($userId, $projectId);
            if (!$project) {
                return null;
            }
        }

        return $this->taskRepository->paginate($userId, $projectId, $filters, $perPage);
    }

    /**
     * Find a task for a user.
     */
    public function getTaskForUser(int $userId, int $taskId): ?Task
    {
        return $this->taskRepository->find($userId, $taskId);
    }

    /**
     * Create a task for a user's project.
     *
     * @param array<string, mixed> $data
     */
    public function createTask(int $userId, array $data): Task
    {
        return $this->taskRepository->create($data);
    }

    /**
     * Update a task.
     *
     * @param array<string, mixed> $data
     */
    public function updateTask(Task $task, array $data): Task
    {
        return $this->taskRepository->update($task, $data);
    }

    /**
     * Soft delete a task.
     */
    public function deleteTask(Task $task): bool
    {
        return $this->taskRepository->delete($task);
    }
}
