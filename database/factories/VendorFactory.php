<?php

namespace Database\Factories;

use App\Models\User;
use App\Models\Vendor;
use App\Support\MockData;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<Vendor>
 */
class VendorFactory extends Factory
{
    public function definition(): array
    {
        $name = fake()->unique()->company().' Events';

        return [
            'user_id' => User::factory()->vendor(),
            'business_name' => $name,
            'short_name' => strtok($name, ' '),
            'description' => fake()->sentence(14),
            'barangay' => fake()->randomElement(MockData::BARANGAYS),
            'contact_number' => '09'.fake()->numerify('#########'),
            'logo' => null,
            'is_verified' => true,
        ];
    }

    public function unverified(): static
    {
        return $this->state(fn (array $attributes) => ['is_verified' => false]);
    }
}
