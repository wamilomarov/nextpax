<?php

namespace Database\Factories;

use App\Models\Price;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Carbon;

class PriceFactory extends Factory
{
    protected $model = Price::class;

    public function definition(): array
    {
        return [
            'property_id' => $this->faker->uuid(),
            'duration' => $this->faker->numberBetween(1, 7),
            'amount' => $this->faker->randomNumber(),
            'currency' => $this->faker->currencyCode,
            'persons' => implode('|', $this->faker->randomElements([1, 2, 3, 4, 5, 6])),
            'weekdays' => implode('|', $this->faker->randomElements([0, 1, 2, 3, 4, 5, 6])),
            'minimum_stay' => $this->faker->numberBetween(1, 21),
            'maximum_stay' => $this->faker->numberBetween(1, 21),
            'extra_person_price' => $this->faker->randomNumber(),
            'extra_person_price_currency' => $this->faker->currencyCode,
            'period_from' => $this->faker->dateTimeThisMonth,
            'period_till' => $this->faker->dateTimeThisMonth,
            'version' => $this->faker->randomNumber(),
        ];
    }
}
