<?php

namespace Database\Factories;

use App\Enums\TaskPriority;
use App\Enums\TaskStatus;
use App\Models\Task;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<Task>
 */
class TaskFactory extends Factory
{
    protected $model = Task::class;

    public function definition(): array
    {
        return [
            'title' => fake()->sentence(4),
            'description' => fake()->paragraph(),
            'priority' => fake()->randomElement(TaskPriority::cases())->value,
            'status' => fake()->randomElement(TaskStatus::cases())->value,
            'due_date' => fake()->optional()->dateTimeBetween('now', '+30 days'),
            'assigned_to' => User::factory(),
        ];
    }
}
