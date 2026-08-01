<?php

namespace App\Notifications;

use App\Models\Task;
use Carbon\Carbon;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Notification;

class TaskOverdueNotification extends Notification implements ShouldQueue
{
    use Queueable;

    public function __construct(
        public Task $task
    ) {}

    /**
     * Get the notification's delivery channels.
     *
     * @return array<int, string>
     */
    public function via(object $notifiable): array
    {
        return ['database'];
    }

    /**
     * Get the array representation of the notification for database storage.
     *
     * @return array<string, mixed>
     */
    public function toDatabase(object $notifiable): array
    {
        return [
            'task_id'    => $this->task->id,
            'title'      => $this->task->title,
            'project_id' => $this->task->project_id,
            'due_date'   => $this->task->due_date ? Carbon::parse($this->task->due_date)->format('Y-m-d') : null,
            'message'    => "Task '{$this->task->title}' is overdue!",
        ];
    }
}
