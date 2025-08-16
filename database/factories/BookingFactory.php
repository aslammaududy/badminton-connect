<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Booking>
 */
class BookingFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        $start = fake()->dateTimeBetween('+1 day', '+2 days');
        $end = (clone $start)->modify('+2 hours');
        return [
            'user_id' => \App\Models\User::factory(),
            'court_id' => \App\Models\Court::factory(),
            'start_time' => $start,
            'end_time' => $end,
            'status' => fake()->randomElement(['pending','confirmed','cancelled']),
            'price' => fake()->randomFloat(2, 10, 100),
        ];
    }
}
