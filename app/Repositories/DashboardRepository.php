<?php

namespace App\Repositories;

use App\Enums\ProjectStatus;
use App\Enums\TaskStatus;
use App\Models\Project;
use App\Models\Task;
use App\Repositories\Contracts\DashboardRepositoryInterface;
use Illuminate\Support\Facades\Concurrency;

class DashboardRepository implements DashboardRepositoryInterface
{
    /**
     * Get aggregated dashboard metrics for a user using Laravel Concurrency Pool.
     *
     * @return array<string, int>
     */
    public function getMetrics(int $userId): array
    {
        $now = now()->toDateString();

        $closures = [
            fn () => Project::query()->where('user_id', $userId)->count(),
            fn () => Project::query()->where('user_id', $userId)->where('status', ProjectStatus::Active)->count(),
            fn () => Task::query()->whereHas('project', fn ($q) => $q->where('user_id', $userId))->count(),
            fn () => Task::query()->whereHas('project', fn ($q) => $q->where('user_id', $userId))->where('status', TaskStatus::Done)->count(),
            fn () => Task::query()->whereHas('project', fn ($q) => $q->where('user_id', $userId))->where('status', '!=', TaskStatus::Done)->count(),
            fn () => Task::query()->whereHas('project', fn ($q) => $q->where('user_id', $userId))->where('status', '!=', TaskStatus::Done)->where('due_date', '<', $now)->count(),
        ];

        try {
            $driver = app()->environment('testing') ? 'sync' : null;
            [$totalProjects, $activeProjects, $totalTasks, $completedTasks, $pendingTasks, $overdueTasks] = Concurrency::driver($driver)->run($closures);
        } catch (\Throwable $e) {
            [$totalProjects, $activeProjects, $totalTasks, $completedTasks, $pendingTasks, $overdueTasks] = Concurrency::driver('sync')->run($closures);
        }

        return [
            'total_projects'  => (int) $totalProjects,
            'active_projects' => (int) $activeProjects,
            'total_tasks'     => (int) $totalTasks,
            'completed_tasks' => (int) $completedTasks,
            'pending_tasks'   => (int) $pendingTasks,
            'overdue_tasks'   => (int) $overdueTasks,
        ];
    }
}
