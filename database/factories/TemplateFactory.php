<?php

declare(strict_types=1);

namespace Database\Factories;

use App\Models\Template;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<Template>
 */
final class TemplateFactory extends Factory
{
    protected $model = Template::class;

    /**
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'name' => fake()->words(3, true),
            'meeting_title_default' => fake()->optional()->sentence(3),
            'agenda_template' => fake()->optional()->paragraph(),
            'notes_template' => fake()->optional()->paragraph(),
            'decisions_template' => fake()->optional()->paragraph(),
            'followup_template' => fake()->optional()->paragraph(),
            'tasks_template' => null,
            'created_by' => User::factory(),
            'updated_by' => User::factory(),
        ];
    }
}
