<?php

namespace App\Repositories;

use App\Models\User;
use App\Repositories\Contracts\UserRepositoryInterface;
use Illuminate\Support\Facades\Hash;

class UserRepository implements UserRepositoryInterface
{
    /**
     * Create a new user record in the database.
     *
     * @param array<string, mixed> $data
     */
    public function create(array $data): User
    {
        /** @var User */
        return User::query()->create([
            'name' => $data['name'],
            'email' => $data['email'],
            'password' => Hash::make((string) $data['password']),
        ]);
    }

    /**
     * Find a user by email address.
     */
    public function findByEmail(string $email): ?User
    {
        /** @var User|null */
        return User::query()->where('email', $email)->first();
    }
}
