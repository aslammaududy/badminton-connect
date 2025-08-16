<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\GameMatch>
 */
class GameMatchFactory extends Factory
{
    protected $model = \App\Models\GameMatch::class;

    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        $start = fake()->dateTimeBetween('+3 days', '+1 week');
        $end = (clone $start)->modify('+90 minutes');
        return [
            'organizer_id' => \App\Models\User::factory(),
            'tournament_id' => null,
            'court_id' => \App\Models\Court::factory(),
            'start_time' => $start,
            'end_time' => $end,
            'status' => 'scheduled',
        ];
    }
}

