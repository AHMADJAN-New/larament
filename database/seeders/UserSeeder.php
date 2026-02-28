<?php

declare(strict_types=1);

namespace Database\Seeders;

use App\Enums\UserRole;
use App\Models\User;
use Illuminate\Database\Seeder;

final class UserSeeder extends Seeder
{
    public function run(): void
    {
        User::query()->updateOrCreate(
            ['username' => 'amin'],
            [
                'name' => 'amin',
                'email' => 'amin@suloom.org',
                'password' => 'amin123',
                'role' => UserRole::Admin->value,
            ]
        );
    }
}
