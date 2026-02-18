<?php

declare(strict_types=1);

namespace Database\Seeders;

use App\Enums\UserRole;
use App\Models\User;
// use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

final class DatabaseSeeder extends Seeder
{
    public function run(): void
    {
        User::query()->firstOrCreate(
            ['email' => config('app.default_user.email')],
            [
                'name' => config('app.default_user.name'),
                'password' => bcrypt(config('app.default_user.password')),
                'role' => UserRole::Admin->value,
            ]
        );

        $this->call(MeetingDataSeeder::class);
    }
}
