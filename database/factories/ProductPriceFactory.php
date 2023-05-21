<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;

class ProductPriceFactory extends Factory
{
    public function definition(): array
    {
        return [
            'value' => fake()->randomNumber(5, true),
            'precision' => 2,
        ];
    }
}
