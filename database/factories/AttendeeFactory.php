<?php

declare(strict_types=1);

namespace Database\Factories;

use App\Models\Attendee;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<Attendee>
 */
final class AttendeeFactory extends Factory
{
    protected $model = Attendee::class;

    /**
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'name' => fake()->name(),
            'user_id' => null,
        ];
    }

    public function forUser(User $user): static
    {
        return $this->state(fn (array $attributes): array => [
            'user_id' => $user->getKey(),
            'name' => $user->name,
        ]);
    }
}
