<?php

namespace Database\Factories;

use App\Models\Booking;
use App\Models\Service;
use App\Models\User;
use App\Support\MockData;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<Booking>
 */
class BookingFactory extends Factory
{
    public function definition(): array
    {
        $decoration = fake()->boolean(70);
        $serving = fake()->numberBetween(0, 8);
        $dishwashing = fake()->numberBetween(0, 4);
        $total = ($decoration ? 5000 : 0) + $serving * 750 + $dishwashing * 500 ?: 5000;
        $barangay = fake()->randomElement(MockData::BARANGAYS);

        return [
            'booking_code' => fn () => 'BK-'.str_pad((string) (Booking::max('id') + 1), 3, '0', STR_PAD_LEFT),
            'customer_id' => User::factory(),
            'service_id' => Service::factory(),
            'vendor_id' => fn (array $attributes) => Service::find($attributes['service_id'])->vendor_id,
            'event_type' => fake()->randomElement(['Birthday', 'Wedding', 'Debut', 'Corporate Event', 'Christening']),
            'event_date' => fake()->dateTimeBetween('+1 week', '+6 months'),
            'start_time' => '17:00',
            'end_time' => '22:00',
            'venue' => "Brgy. {$barangay}, Nasugbu, Batangas",
            'barangay' => $barangay,
            'pax' => fake()->numberBetween(5, 25) * 10,
            'needs_decoration' => $decoration,
            'serving_count' => $serving,
            'dishwashing_count' => $dishwashing,
            'special_requests' => null,
            'total_amount' => $total,
            'reservation_fee' => round($total * 0.3),
            'amount_paid' => 0,
            'status' => 'Pending',
            'payment_status' => 'Unpaid',
        ];
    }
}
