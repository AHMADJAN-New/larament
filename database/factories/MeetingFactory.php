<?php

declare(strict_types=1);

namespace Database\Factories;

use App\Models\Meeting;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<Meeting>
 */
final class MeetingFactory extends Factory
{
    protected $model = Meeting::class;

    /**
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'meeting_no' => fake()->unique()->numberBetween(1, 100_000),
            'title' => fake()->sentence(4),
            'date' => fake()->dateTimeBetween('-30 days', '+30 days')->format('Y-m-d'),
            'time' => fake()->time(),
            'location' => fake()->city(),
            'agenda' => fake()->paragraph(),
            'notes' => fake()->paragraph(),
            'decisions_text' => fake()->paragraph(),
            'followup_text' => fake()->paragraph(),
            'is_confidential' => fake()->boolean(),
            'created_by' => User::factory(),
            'updated_by' => User::factory(),
        ];
    }
}
