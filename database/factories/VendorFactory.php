<?php

namespace Database\Factories;

use App\Models\Vendor;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<Vendor>
 */
class VendorFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'name' => fake()->company(),
            'phone' => fake()->phoneNumber(),
            'email' => fake()->unique()->safeEmail(),
            'address' => fake()->address(),
            'bank_name' => fake()->company().' Bank',
            'account_title' => fake()->name(),
            'account_number' => fake()->bankAccountNumber(),
            'iban' => fake()->iban(),
            'is_active' => true,
        ];
    }
}
