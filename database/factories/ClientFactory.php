<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Client>
 */
class ClientFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'name' => fake('fr_FR')->company(),
            'contact_name' => fake()->firstName().' '.fake()->lastName(),
            'email' => fake()->email(),
            'phone' => fake('fr_FR')->phoneNumber(),
            'description' => fake('fr_FR')->paragraphs(2, true),
            'address' => '9 Bis rue de la République',
            'zip_code' => '81500',
            'city' => 'Lavaur',
        ];
    }
}
