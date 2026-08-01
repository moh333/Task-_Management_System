<?php

namespace Tests\Feature;

use App\Enums\ProjectStatus;
use App\Enums\TaskPriority;
use App\Enums\TaskStatus;
use App\Models\Project;
use App\Models\Task;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Laravel\Sanctum\Sanctum;
use Tests\TestCase;

class DashboardApiTest extends TestCase
{
    use RefreshDatabase;

    public function test_user_can_get_dashboard_metrics(): void
    {
        $user = User::factory()->create();
        Sanctum::actingAs($user);

        // Project 1 (Active)
        $projectActive = Project::factory()->create([
            'user_id' => $user->id,
            'status'  => ProjectStatus::Active,
        ]);

        // Project 2 (Completed)
        $projectCompleted = Project::factory()->create([
            'user_id' => $user->id,
            'status'  => ProjectStatus::Completed,
        ]);

        // Tasks for Project 1
        // 1. Completed Task
        Task::factory()->create([
            'project_id' => $projectActive->id,
            'status'     => TaskStatus::Done,
            'due_date'   => now()->addDays(5)->toDateString(),
        ]);

        // 2. Pending Task (In Progress, Future Due Date)
        Task::factory()->create([
            'project_id' => $projectActive->id,
            'status'     => TaskStatus::InProgress,
            'due_date'   => now()->addDays(5)->toDateString(),
        ]);

        // 3. Overdue Task (Todo, Past Due Date)
        Task::factory()->create([
            'project_id' => $projectActive->id,
            'status'     => TaskStatus::Todo,
            'due_date'   => now()->subDays(5)->toDateString(),
        ]);

        // Create data for another user (to verify user isolation)
        $otherUser = User::factory()->create();
        $otherProject = Project::factory()->create(['user_id' => $otherUser->id, 'status' => ProjectStatus::Active]);
        Task::factory()->create(['project_id' => $otherProject->id, 'status' => TaskStatus::Done]);

        $response = $this->getJson('/api/dashboard');

        $response->assertStatus(200)
            ->assertJson([
                'success' => true,
                'message' => 'Dashboard metrics retrieved successfully',
                'data'    => [
                    'total_projects'  => 2,
                    'active_projects' => 1,
                    'total_tasks'     => 3,
                    'completed_tasks' => 1,
                    'pending_tasks'   => 2,
                    'overdue_tasks'   => 1,
                ],
            ]);
    }
}
