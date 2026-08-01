<?php

namespace Tests\Feature;

use App\Enums\ProjectStatus;
use App\Models\Project;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Laravel\Sanctum\Sanctum;
use Tests\TestCase;

class ProjectApiTest extends TestCase
{
    use RefreshDatabase;

    public function test_user_can_list_their_projects(): void
    {
        $user = User::factory()->create();
        Sanctum::actingAs($user);

        Project::factory()->count(3)->create(['user_id' => $user->id]);

        $response = $this->getJson('/api/projects');

        $response->assertStatus(200)
            ->assertJson([
                'success' => true,
                'message' => 'Projects retrieved successfully',
            ])
            ->assertJsonStructure([
                'success',
                'message',
                'data' => [
                    'data' => [
                        '*' => ['id', 'name', 'description', 'status', 'created_at', 'updated_at'],
                    ],
                    'meta' => ['current_page', 'per_page', 'total'],
                    'links' => ['first', 'last'],
                ],
            ]);
    }

    public function test_user_can_create_a_project(): void
    {
        $user = User::factory()->create();
        Sanctum::actingAs($user);

        $payload = [
            'name' => 'Website Redesign',
            'description' => 'Redesigning company website',
            'status' => 'Active',
        ];

        $response = $this->postJson('/api/project/create', $payload);

        $response->assertStatus(201)
            ->assertJson([
                'success' => true,
                'message' => 'Project created successfully',
                'data' => [
                    'name' => 'Website Redesign',
                    'description' => 'Redesigning company website',
                    'status' => 'Active',
                ],
            ]);

        $this->assertDatabaseHas('projects', [
            'user_id' => $user->id,
            'name' => 'Website Redesign',
            'status' => 'Active',
        ]);
    }

    public function test_user_cannot_create_project_with_invalid_status(): void
    {
        $user = User::factory()->create();
        Sanctum::actingAs($user);

        $payload = [
            'name' => 'Invalid Project',
            'status' => 'unknown_status',
        ];

        $response = $this->postJson('/api/project/create', $payload);

        $response->assertStatus(422)
            ->assertJsonValidationErrors(['status']);
    }

    public function test_user_can_show_their_own_project(): void
    {
        $user = User::factory()->create();
        Sanctum::actingAs($user);

        $project = Project::factory()->create([
            'user_id' => $user->id,
            'name' => 'Mobile App',
            'status' => ProjectStatus::Completed,
        ]);

        $response = $this->getJson('/api/project/show/' . $project->id);

        $response->assertStatus(200)
            ->assertJson([
                'success' => true,
                'data' => [
                    'id' => $project->id,
                    'name' => 'Mobile App',
                    'status' => 'Completed',
                ],
            ]);
    }

    public function test_user_can_update_their_own_project(): void
    {
        $user = User::factory()->create();
        Sanctum::actingAs($user);

        $project = Project::factory()->create([
            'user_id' => $user->id,
            'status' => ProjectStatus::Active,
        ]);

        $payload = [
            'name' => 'Updated Name',
            'status' => 'Archived',
        ];

        $response = $this->patchJson('/api/project/update/' . $project->id, $payload);

        $response->assertStatus(200)
            ->assertJson([
                'success' => true,
                'message' => 'Project updated successfully',
                'data' => [
                    'name' => 'Updated Name',
                    'status' => 'Archived',
                ],
            ]);

        $this->assertDatabaseHas('projects', [
            'id' => $project->id,
            'name' => 'Updated Name',
            'status' => 'Archived',
        ]);
    }

    public function test_user_can_delete_their_own_project(): void
    {
        $user = User::factory()->create();
        Sanctum::actingAs($user);

        $project = Project::factory()->create(['user_id' => $user->id]);

        $response = $this->deleteJson('/api/project/delete/' . $project->id);

        $response->assertStatus(200)
            ->assertJson([
                'success' => true,
                'message' => 'Project deleted successfully',
            ]);

        $this->assertSoftDeleted('projects', ['id' => $project->id]);
    }

    public function test_user_cannot_access_or_modify_another_users_project(): void
    {
        $userA = User::factory()->create();
        $userB = User::factory()->create();

        $projectOfUserB = Project::factory()->create(['user_id' => $userB->id]);

        Sanctum::actingAs($userA);

        // Try viewing User B's project
        $this->getJson('/api/project/show/' . $projectOfUserB->id)
            ->assertStatus(404)
            ->assertJson([
                'success' => false,
                'message' => 'Project not found',
            ]);

        // Try updating User B's project
        $this->patchJson('/api/project/update/' . $projectOfUserB->id, ['name' => 'Hacked'])
            ->assertStatus(404);

        // Try deleting User B's project
        $this->deleteJson('/api/project/delete/' . $projectOfUserB->id)
            ->assertStatus(404);
    }
}
