<?php

namespace Database\Factories;

use App\Models\Checkin;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<Checkin>
 */
class CheckinFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'user_id' => UserFactory::new(),
            'venue_id' => VenueFactory::new(),
            'status' => $this->faker->randomElement(['visited', 'planned', 'wishlist']),
            'visibility' => $this->faker->randomElement([0, 1]),
        ];
    }
}
