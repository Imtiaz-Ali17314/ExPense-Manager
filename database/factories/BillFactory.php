<?php

namespace Database\Factories;

use App\Models\Bill;
use App\Models\User;
use App\Models\Vendor;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<Bill>
 */
class BillFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        $subtotal = fake()->randomFloat(2, 500, 15000);

        return [
            'user_id' => User::factory(),
            'vendor_id' => Vendor::factory(),
            'bill_number' => 'INV-'.fake()->unique()->numberBetween(10000, 99999),
            'bill_date' => fake()->dateTimeBetween('-30 days', 'now')->format('Y-m-d'),
            'subtotal' => $subtotal,
            'grand_total' => $subtotal,
            'status' => fake()->randomElement(['paid', 'unpaid', 'pending']),
        ];
    }
}
