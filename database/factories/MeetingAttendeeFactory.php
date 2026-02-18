<?php

declare(strict_types=1);

namespace Database\Factories;

use App\Enums\AttendanceStatus;
use App\Models\Meeting;
use App\Models\MeetingAttendee;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<MeetingAttendee>
 */
final class MeetingAttendeeFactory extends Factory
{
    protected $model = MeetingAttendee::class;

    /**
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'meeting_id' => Meeting::factory(),
            'name' => fake()->name(),
            'status' => fake()->randomElement(AttendanceStatus::cases())->value,
            'reason' => fake()->optional()->sentence(4),
        ];
    }
}
