<?php

namespace Database\Factories;

use App\Models\Booking;
use App\Models\Review;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<Review>
 */
class ReviewFactory extends Factory
{
    public function definition(): array
    {
        return [
            'booking_id' => Booking::factory()->state(['status' => 'Completed', 'payment_status' => 'Paid']),
            'customer_id' => fn (array $attributes) => Booking::find($attributes['booking_id'])->customer_id,
            'vendor_id' => fn (array $attributes) => Booking::find($attributes['booking_id'])->vendor_id,
            'rating' => fake()->numberBetween(4, 5),
            'comment' => fake()->sentence(15),
        ];
    }
}
