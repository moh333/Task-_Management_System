<?php

namespace Tests\Feature;

use App\Enums\TaskStatus;
use App\Jobs\CheckOverdueTasksJob;
use App\Models\Project;
use App\Models\Task;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Queue;
use Laravel\Sanctum\Sanctum;
use Tests\TestCase;

class OverdueTaskNotificationApiTest extends TestCase
{
    use RefreshDatabase;

    public function test_artisan_command_dispatches_check_overdue_tasks_job(): void
    {
        Queue::fake();

        $this->artisan('tasks:check-overdue')
            ->expectsOutput('Overdue tasks check job dispatched successfully.')
            ->assertExitCode(0);

        Queue::assertPushed(CheckOverdueTasksJob::class);
    }

    public function test_check_overdue_tasks_job_sends_database_notification(): void
    {
        $user = User::factory()->create();
        $project = Project::factory()->create(['user_id' => $user->id]);

        $overdueTask = Task::factory()->create([
            'project_id' => $project->id,
            'title'      => 'Fix Production Bug',
            'status'     => TaskStatus::Todo,
            'due_date'   => now()->subDays(3)->toDateString(),
        ]);

        // Dispatch Job
        CheckOverdueTasksJob::dispatchSync();

        // Assert Notification stored in Database
        $this->assertDatabaseHas('notifications', [
            'notifiable_id'   => $user->id,
            'notifiable_type' => User::class,
        ]);

        $notification = $user->notifications()->first();
        $this->assertNotNull($notification);
        $this->assertEquals($overdueTask->id, $notification->data['task_id']);
        $this->assertEquals("Task 'Fix Production Bug' is overdue!", $notification->data['message']);
    }

    public function test_user_can_list_notifications_via_api(): void
    {
        $user = User::factory()->create();
        $project = Project::factory()->create(['user_id' => $user->id]);

        Task::factory()->create([
            'project_id' => $project->id,
            'status'     => TaskStatus::InProgress,
            'due_date'   => now()->subDays(2)->toDateString(),
        ]);

        CheckOverdueTasksJob::dispatchSync();

        Sanctum::actingAs($user);

        $response = $this->getJson('/api/notifications');

        $response->assertStatus(200)
            ->assertJson([
                'success' => true,
                'message' => 'Notifications retrieved successfully',
            ])
            ->assertJsonCount(1, 'data.data');
    }

    public function test_user_can_mark_notification_as_read(): void
    {
        $user = User::factory()->create();
        $project = Project::factory()->create(['user_id' => $user->id]);

        Task::factory()->create([
            'project_id' => $project->id,
            'status'     => TaskStatus::Todo,
            'due_date'   => now()->subDays(1)->toDateString(),
        ]);

        CheckOverdueTasksJob::dispatchSync();

        Sanctum::actingAs($user);

        $notification = $user->unreadNotifications()->first();
        $this->assertNotNull($notification);

        $response = $this->getJson('/api/notification/' . $notification->id . '/read');

        $response->assertStatus(200)
            ->assertJson([
                'success' => true,
                'message' => 'Notification marked as read',
            ]);

        $this->assertEquals(0, $user->unreadNotifications()->count());
    }

    public function test_user_can_mark_all_notifications_as_read(): void
    {
        $user = User::factory()->create();
        $project = Project::factory()->create(['user_id' => $user->id]);

        Task::factory()->count(2)->create([
            'project_id' => $project->id,
            'status'     => TaskStatus::Todo,
            'due_date'   => now()->subDays(2)->toDateString(),
        ]);

        CheckOverdueTasksJob::dispatchSync();

        Sanctum::actingAs($user);

        $this->assertEquals(2, $user->unreadNotifications()->count());

        $response = $this->getJson('/api/notifications/read-all');

        $response->assertStatus(200)
            ->assertJson([
                'success' => true,
                'message' => 'All notifications marked as read',
            ]);

        $this->assertEquals(0, $user->unreadNotifications()->count());
    }
}
