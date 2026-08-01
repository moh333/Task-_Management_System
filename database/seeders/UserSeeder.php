<?php

namespace Database\Seeders;

use App\Models\Project;
use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class UserSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Default test user
        $testUser = User::factory()->create([
            'name' => 'Mohamed Abdelateef',
            'email' => 'mohamedabdelateef25@example.com',
            'password' => Hash::make('password@123'),
        ]);

        // Projects for test user
        Project::factory(5)->create([
            'user_id' => $testUser->id,
        ]);

        // Seed 10 additional users with 3 to 6 projects each using factory relationships
        User::factory(10)
            ->has(Project::factory()->count(5))
            ->create();
    }
}
