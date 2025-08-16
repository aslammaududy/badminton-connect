<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\PartnerRequest>
 */
class PartnerRequestFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'requester_id' => \App\Models\User::factory(),
            'responder_id' => null,
            'status' => 'open',
            'message' => fake()->optional()->sentence(10),
        ];
    }
}
