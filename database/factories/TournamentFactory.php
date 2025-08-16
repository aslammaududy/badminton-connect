<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Tournament>
 */
class TournamentFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        $start = fake()->dateTimeBetween('+1 week', '+1 month');
        $end = (clone $start)->modify('+2 days');
        return [
            'name' => 'Tournament '.fake()->unique()->word(),
            'location' => fake()->city(),
            'start_date' => $start,
            'end_date' => $end,
            'description' => fake()->sentence(12),
            'status' => 'upcoming',
        ];
    }
}
