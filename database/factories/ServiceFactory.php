<?php

namespace Database\Factories;

use App\Models\Service;
use App\Models\ServiceCategory;
use App\Models\Vendor;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<Service>
 */
class ServiceFactory extends Factory
{
    public function definition(): array
    {
        $perStaff = fake()->boolean(30);

        return [
            'vendor_id' => Vendor::factory(),
            'service_category_id' => fn () => ServiceCategory::query()->inRandomOrder()->value('id')
                ?? ServiceCategory::create(['name' => 'Decoration', 'icon' => '🌸'])->id,
            'name' => fake()->words(3, true),
            'description' => fake()->sentence(12),
            'price' => $perStaff ? fake()->randomElement([500, 750, 1200]) : fake()->numberBetween(8, 30) * 1000,
            'price_unit' => $perStaff ? 'staff' : 'event',
            'image' => null,
            'is_active' => true,
        ];
    }
}
