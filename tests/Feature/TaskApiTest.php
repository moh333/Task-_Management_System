<?php

namespace Tests\Feature;

use App\Enums\TaskPriority;
use App\Enums\TaskStatus;
use App\Models\Project;
use App\Models\Task;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Laravel\Sanctum\Sanctum;
use Tests\TestCase;

class TaskApiTest extends TestCase
{
    use RefreshDatabase;

    public function test_user_can_create_task_with_valid_owned_project_id(): void
    {
        $user = User::factory()->create();
        $project = Project::factory()->create(['user_id' => $user->id]);
        Sanctum::actingAs($user);

        $payload = [
            'project_id'  => $project->id,
            'title'       => 'Setup Authentication API',
            'description' => 'Implement Sanctum token authentication',
            'priority'    => 'High',
            'status'      => 'In Progress',
            'due_date'    => '2026-09-01',
        ];

        $response = $this->postJson('/api/task/create', $payload);

        $response->assertStatus(201)
            ->assertJson([
                'success' => true,
                'message' => 'Task created successfully',
                'data'    => [
                    'project_id'  => $project->id,
                    'title'       => 'Setup Authentication API',
                    'description' => 'Implement Sanctum token authentication',
                    'priority'    => 'High',
                    'status'      => 'In Progress',
                    'due_date'    => '2026-09-01',
                ],
            ]);

        $this->assertDatabaseHas('tasks', [
            'project_id' => $project->id,
            'title'      => 'Setup Authentication API',
            'status'     => 'In Progress',
        ]);
    }

    public function test_user_cannot_create_task_with_another_users_project_id(): void
    {
        $userA = User::factory()->create();
        $userB = User::factory()->create();
        $projectB = Project::factory()->create(['user_id' => $userB->id]);

        Sanctum::actingAs($userA);

        $payload = [
            'project_id'  => $projectB->id,
            'title'       => 'Unauthorized Task Creation',
            'priority'    => 'High',
            'status'      => 'Todo',
            'due_date'    => '2026-09-01',
        ];

        $response = $this->postJson('/api/task/create', $payload);

        $response->assertStatus(422)
            ->assertJsonValidationErrors(['project_id']);
    }

    public function test_cannot_create_task_with_invalid_status_or_priority(): void
    {
        $user = User::factory()->create();
        $project = Project::factory()->create(['user_id' => $user->id]);
        Sanctum::actingAs($user);

        $payload = [
            'project_id' => $project->id,
            'title'      => 'Invalid Task',
            'priority'   => 'SuperHigh',
            'status'     => 'UnknownStatus',
        ];

        $response = $this->postJson('/api/task/create', $payload);

        $response->assertStatus(422)
            ->assertJsonValidationErrors(['priority', 'status']);
    }

    public function test_user_can_show_task(): void
    {
        $user = User::factory()->create();
        $project = Project::factory()->create(['user_id' => $user->id]);
        $task = Task::factory()->create([
            'project_id' => $project->id,
            'title'      => 'Write Documentation',
            'status'     => TaskStatus::Todo,
        ]);
        Sanctum::actingAs($user);

        $response = $this->getJson("/api/task/show/{$task->id}");

        $response->assertStatus(200)
            ->assertJson([
                'success' => true,
                'data'    => [
                    'id'    => $task->id,
                    'title' => 'Write Documentation',
                ],
            ]);
    }

    public function test_user_can_update_task(): void
    {
        $user = User::factory()->create();
        $project = Project::factory()->create(['user_id' => $user->id]);
        $task = Task::factory()->create([
            'project_id' => $project->id,
            'status'     => TaskStatus::Todo,
        ]);
        Sanctum::actingAs($user);

        $payload = [
            'title'  => 'Updated Task Title',
            'status' => 'Done',
        ];

        $response = $this->patchJson("/api/task/update/{$task->id}", $payload);

        $response->assertStatus(200)
            ->assertJson([
                'success' => true,
                'message' => 'Task updated successfully',
                'data'    => [
                    'title'  => 'Updated Task Title',
                    'status' => 'Done',
                ],
            ]);

        $this->assertDatabaseHas('tasks', [
            'id'     => $task->id,
            'title'  => 'Updated Task Title',
            'status' => 'Done',
        ]);
    }

    public function test_user_can_soft_delete_task(): void
    {
        $user = User::factory()->create();
        $project = Project::factory()->create(['user_id' => $user->id]);
        $task = Task::factory()->create(['project_id' => $project->id]);
        Sanctum::actingAs($user);

        $response = $this->deleteJson("/api/task/delete/{$task->id}");

        $response->assertStatus(200)
            ->assertJson([
                'success' => true,
                'message' => 'Task deleted successfully',
            ]);

        $this->assertSoftDeleted('tasks', ['id' => $task->id]);
    }

    public function test_user_can_list_and_filter_tasks_by_status(): void
    {
        $user = User::factory()->create();
        $project = Project::factory()->create(['user_id' => $user->id]);
        Sanctum::actingAs($user);

        Task::factory()->create(['project_id' => $project->id, 'status' => TaskStatus::Todo]);
        Task::factory()->create(['project_id' => $project->id, 'status' => TaskStatus::InProgress]);
        Task::factory()->create(['project_id' => $project->id, 'status' => TaskStatus::Done]);

        $response = $this->getJson("/api/tasks?project_id={$project->id}&status=In Progress");

        $response->assertStatus(200)
            ->assertJsonCount(1, 'data.data')
            ->assertJsonPath('data.data.0.status', 'In Progress');
    }

    public function test_user_can_filter_tasks_by_priority(): void
    {
        $user = User::factory()->create();
        $project = Project::factory()->create(['user_id' => $user->id]);
        Sanctum::actingAs($user);

        Task::factory()->create(['project_id' => $project->id, 'priority' => TaskPriority::Low]);
        Task::factory()->create(['project_id' => $project->id, 'priority' => TaskPriority::High]);

        $response = $this->getJson("/api/tasks?project_id={$project->id}&priority=High");

        $response->assertStatus(200)
            ->assertJsonCount(1, 'data.data')
            ->assertJsonPath('data.data.0.priority', 'High');
    }

    public function test_user_can_search_tasks_by_title(): void
    {
        $user = User::factory()->create();
        $project = Project::factory()->create(['user_id' => $user->id]);
        Sanctum::actingAs($user);

        Task::factory()->create(['project_id' => $project->id, 'title' => 'Fix Payment Bug']);
        Task::factory()->create(['project_id' => $project->id, 'title' => 'Design Landing Page']);

        $response = $this->getJson("/api/tasks?project_id={$project->id}&search=Payment");

        $response->assertStatus(200)
            ->assertJsonCount(1, 'data.data')
            ->assertJsonPath('data.data.0.title', 'Fix Payment Bug');
    }

    public function test_user_cannot_access_tasks_of_another_user(): void
    {
        $userA = User::factory()->create();
        $userB = User::factory()->create();

        $projectB = Project::factory()->create(['user_id' => $userB->id]);
        $taskB = Task::factory()->create(['project_id' => $projectB->id]);

        Sanctum::actingAs($userA);

        // Try viewing User B's task
        $this->getJson("/api/task/show/{$taskB->id}")
            ->assertStatus(404)
            ->assertJson([
                'success' => false,
                'message' => 'Task not found',
            ]);

        // Try updating User B's task
        $this->patchJson("/api/task/update/{$taskB->id}", ['title' => 'Hacked'])
            ->assertStatus(404);

        // Try deleting User B's task
        $this->deleteJson("/api/task/delete/{$taskB->id}")
            ->assertStatus(404);
    }
}
