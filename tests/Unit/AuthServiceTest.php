<?php

namespace Tests\Unit;

use App\Models\User;
use App\Repositories\Contracts\UserRepositoryInterface;
use App\Services\AuthService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Hash;
use Tests\TestCase;

class AuthServiceTest extends TestCase
{
    use RefreshDatabase;

    public function test_auth_service_can_register_user(): void
    {
        $repository = $this->app->make(UserRepositoryInterface::class);
        $service = new AuthService($repository);

        $result = $service->register([
            'name' => 'John Service',
            'email' => 'john.service@example.com',
            'password' => 'password123',
        ]);

        $this->assertArrayHasKey('user', $result);
        $this->assertArrayHasKey('access_token', $result);
        $this->assertEquals('john.service@example.com', $result['user']->email);
        $this->assertDatabaseHas('users', ['email' => 'john.service@example.com']);
    }

    public function test_auth_service_can_login_user(): void
    {
        $user = User::factory()->create([
            'email' => 'login.test@example.com',
            'password' => Hash::make('secret123'),
        ]);

        $repository = $this->app->make(UserRepositoryInterface::class);
        $service = new AuthService($repository);

        $result = $service->login([
            'email' => 'login.test@example.com',
            'password' => 'secret123',
        ]);

        $this->assertNotNull($result);
        $this->assertEquals($user->id, $result['user']->id);
        $this->assertNotEmpty($result['access_token']);
    }

    public function test_auth_service_login_fails_with_invalid_credentials(): void
    {
        User::factory()->create([
            'email' => 'login.fail@example.com',
            'password' => Hash::make('secret123'),
        ]);

        $repository = $this->app->make(UserRepositoryInterface::class);
        $service = new AuthService($repository);

        $result = $service->login([
            'email' => 'login.fail@example.com',
            'password' => 'wrongpassword',
        ]);

        $this->assertNull($result);
    }
}
