<?php

namespace Database\Factories;

use App\Models\Availability;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Carbon;

class AvailabilityFactory extends Factory
{
    protected $model = Availability::class;

    public function definition(): array
    {
        return [
            'property_id' => $this->faker->uuid,
            'date' => $this->faker->dateTimeThisMonth,
            'quantity' => 1,
            'arrival_allowed' => $this->faker->boolean(),
            'departure_allowed' => $this->faker->boolean(),
            'minimum_stay' => $this->faker->randomNumber(1, 21),
            'maximum_stay' => $this->faker->numberBetween(1, 21),
            'version' => $this->faker->randomNumber(),
        ];
    }
}
