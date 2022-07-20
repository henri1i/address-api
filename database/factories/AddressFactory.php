<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Address>
 */
class AddressFactory extends Factory
{
    public function definition()
    {
        return [
            'house_number'    => "{$this->faker->numberBetween(1, 999)}",
            'reference_point' => $this->faker->sentence(4),
        ];
    }
}
