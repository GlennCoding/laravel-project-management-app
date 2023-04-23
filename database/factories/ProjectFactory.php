<?php

namespace Database\Factories;

use App\Models\Project;
use App\Models\Task;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<Project>
 */
class ProjectFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'title' => fake()->sentence(),
            'description' => fake()->paragraph(),
            'owner_id' => User::factory(),
        ];
    }

    /**
     * Create tasks for the project.
     *
     * @param int $count Number of tasks to create
     * @return $this
     */
    public function withTasks(int $count = 1): ProjectFactory
    {
        return $this->afterCreating(function (Project $project) use ($count) {
            Task::factory()
                ->count($count)
                ->for($project)
                ->create();
        });
    }
}
