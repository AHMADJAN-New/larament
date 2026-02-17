<?php

declare(strict_types=1);

namespace Database\Factories;

use App\Enums\TaskPriority;
use App\Enums\TaskStatus;
use App\Models\Meeting;
use App\Models\MeetingTask;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<MeetingTask>
 */
final class MeetingTaskFactory extends Factory
{
    protected $model = MeetingTask::class;

    /**
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'meeting_id' => Meeting::factory(),
            'title' => fake()->sentence(5),
            'owner' => fake()->name(),
            'due_date' => fake()->optional()->dateTimeBetween('today', '+30 days')->format('Y-m-d'),
            'priority' => fake()->randomElement(TaskPriority::cases())->value,
            'status' => fake()->randomElement(TaskStatus::cases())->value,
            'description' => fake()->optional()->paragraph(),
            'created_by' => User::factory(),
            'updated_by' => User::factory(),
            'closed_at' => null,
        ];
    }
}
