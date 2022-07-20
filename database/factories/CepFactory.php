<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Cep>
 */
class CepFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition()
    {
        return [
            'number'   => $this->faker->numerify('########'),
            'street'   => $this->faker->streetName(),
            'district' => $this->faker->sentence(2),
            'city'     => $this->faker->city(),
            'state'    => $this->faker->lexify('??'),
        ];
    }
}
