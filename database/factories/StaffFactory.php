<?php

namespace Database\Factories;

use App\Models\Staff;
use App\Models\Vendor;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<Staff>
 */
class StaffFactory extends Factory
{
    public function definition(): array
    {
        return [
            'vendor_id' => Vendor::factory(),
            'full_name' => fake()->name(),
            'role' => fake()->randomElement(Staff::ROLES),
            'is_active' => true,
        ];
    }

    /** Eventlynk's own crew (not tied to a vendor). */
    public function platform(): static
    {
        return $this->state(fn (array $attributes) => ['vendor_id' => null]);
    }
}
