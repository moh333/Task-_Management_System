<?php

namespace Database\Seeders;

use App\Models\Project;
use App\Models\User;
use Illuminate\Database\Seeder;

class ProjectSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Seed projects for existing users if any exist without projects
        User::all()->each(function (User $user) {
            if ($user->projects()->count() === 0) {
                Project::factory(3)->create([
                    'user_id' => $user->id,
                ]);
            }
        });
    }
}
