<?php

declare(strict_types=1);

namespace Database\Factories;

use App\Models\Meeting;
use App\Models\ShareLink;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Str;

/**
 * @extends Factory<ShareLink>
 */
final class ShareLinkFactory extends Factory
{
    protected $model = ShareLink::class;

    /**
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'meeting_id' => Meeting::factory(),
            'token' => Str::random(64),
            'expires_at' => fake()->optional()->dateTimeBetween('now', '+10 days'),
            'password_hash' => null,
            'created_by' => User::factory(),
            'created_at' => now(),
        ];
    }
}
