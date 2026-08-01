<?php

namespace App\Jobs;

use App\Enums\TaskStatus;
use App\Models\Task;
use App\Notifications\TaskOverdueNotification;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;

class CheckOverdueTasksJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    /**
     * Execute the job to check overdue tasks and send notifications.
     */
    public function handle(): void
    {
        $today = now()->toDateString();

        Task::query()
            ->with(['project.user'])
            ->where('status', '!=', TaskStatus::Done)
            ->whereNotNull('due_date')
            ->where('due_date', '<', $today)
            ->each(function (Task $task) {
                $user = $task->project?->user;
                if ($user) {
                    $user->notify(new TaskOverdueNotification($task));
                }
            });
    }
}
