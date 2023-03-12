<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Task>
 */
class TaskFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        $status = collect(['WAITING', 'IN_PROGRESS', 'TERMINATED', 'PAID'])->random();

        return [
            'name' => fake()->words(3, true),
            'description' => fake()->paragraphs(2, true),
            'estimated_duration' => 30,
            'status' => $status,
            'duration' => in_array($status, ['TERMINATED', 'PAID']) ? 35 : null,
            'started_at' => in_array($status, ['TERMINATED', 'PAID', 'IN_PROGRESS']) ? now()->subDays(2) : null,
            'ended_at' => in_array($status, ['TERMINATED', 'PAID']) ? now() : null,
        ];
    }
}
