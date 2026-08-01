<?php

namespace App\Repositories;

use App\Models\Task;
use App\Repositories\Contracts\TaskRepositoryInterface;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;

class TaskRepository implements TaskRepositoryInterface
{
    /**
     * Get paginated tasks belonging to a user (and optionally a specific project) with filters.
     *
     * @param array<string, mixed> $filters
     * @return LengthAwarePaginator<Task>
     */
    public function paginate(int $userId, ?int $projectId = null, array $filters = [], int $perPage = 10): LengthAwarePaginator
    {
        return Task::query()
            ->whereHas('project', fn($query) => $query->where('user_id', $userId))
            ->when($projectId, fn($query, $pid) => $query->where('project_id', $pid))
            ->when($filters['status'] ?? null, fn($query, $status) => $query->where('status', $status))
            ->when($filters['priority'] ?? null, fn($query, $priority) => $query->where('priority', $priority))
            ->when($filters['search'] ?? null, fn($query, $search) => $query->where('title', 'like', "%{$search}%"))
            ->latest()
            ->paginate($perPage);
    }

    /**
     * Find a task belonging to a specific user by task ID.
     */
    public function find(int $userId, int $taskId): ?Task
    {
        /** @var Task|null */
        return Task::query()
            ->whereHas('project', fn($query) => $query->where('user_id', $userId))
            ->where('id', $taskId)
            ->first();
    }

    /**
     * Create a task for a project.
     *
     * @param array<string, mixed> $data
     */
    public function create(array $data): Task
    {
        /** @var Task */
        return Task::query()->create($data);
    }

    /**
     * Update an existing task.
     *
     * @param array<string, mixed> $data
     */
    public function update(Task $task, array $data): Task
    {
        $task->update($data);
        return $task->refresh();
    }

    /**
     * Soft delete a task.
     */
    public function delete(Task $task): bool
    {
        return (bool) $task->delete();
    }
}
