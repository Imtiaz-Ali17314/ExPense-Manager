<?php

namespace Database\Factories;

use App\Models\Item;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<Item>
 */
class ItemFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        $currentPrice = fake()->randomFloat(2, 50, 2000);

        return [
            'name' => ucfirst(fake()->words(2, true)),
            'unit' => fake()->randomElement(['kg', 'pcs', 'liter', 'box', 'meter', 'pack']),
            'current_price' => $currentPrice,
            'previous_price' => fake()->optional(0.7)->randomFloat(2, 40, $currentPrice),
            'average_price' => fake()->optional(0.8)->randomFloat(2, 45, $currentPrice),
            'description' => fake()->sentence(),
            'is_active' => true,
        ];
    }
}
