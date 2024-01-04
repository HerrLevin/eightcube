<?php

namespace Database\Factories;

use App\Models\Venue;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<Venue>
 */
class VenueFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'osm_id' => $this->faker->randomNumber(),
            'osm_type' => $this->faker->randomElement(['node', 'way', 'relation']),
            'name' => $this->faker->company(),
            'venue_type' => $this->faker->randomElement(['pub', 'restaurant', 'cafe']),
        ];
    }
}
