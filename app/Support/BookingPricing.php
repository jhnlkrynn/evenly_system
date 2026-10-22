<?php

namespace App\Support;

/**
 * Booking prices — the same rules as the Figma BookingPage (SERVICE_PRICES):
 * decoration ₱5,000 flat, serving staff ₱750 each, dishwashing ₱500 each,
 * and a 30% reservation fee that secures the date.
 *
 * The booking form's JavaScript reads these numbers from the page, so the
 * estimate the customer sees and the total the server stores always agree.
 */
final class BookingPricing
{
    public const DECORATION = 5000;

    public const SERVING_PER_STAFF = 750;

    public const DISHWASHING_PER_STAFF = 500;

    public const RESERVATION_RATE = 0.3;

    public static function total(bool $decoration, int $serving, int $dishwashing): int
    {
        return ($decoration ? self::DECORATION : 0)
            + $serving * self::SERVING_PER_STAFF
            + $dishwashing * self::DISHWASHING_PER_STAFF;
    }

    /** Math.round(total * 0.3) in the prototype. */
    public static function reservationFee(float $total): int
    {
        return (int) round($total * self::RESERVATION_RATE);
    }

    /**
     * Invoice lines for a booking's services. Bookings priced before this rule
     * existed (the seeded sample data) get an adjustment line so the lines
     * always add up to the stored total.
     *
     * @return array<int, array{label: string, detail: string, amount: float}>
     */
    public static function lines(bool $decoration, int $serving, int $dishwashing, float $storedTotal): array
    {
        $lines = [];

        if ($decoration) {
            $lines[] = ['label' => 'Decoration', 'detail' => 'Full setup · flat rate', 'amount' => (float) self::DECORATION];
        }
        if ($serving > 0) {
            $lines[] = ['label' => 'Serving staff', 'detail' => $serving.' × ₱'.number_format(self::SERVING_PER_STAFF), 'amount' => (float) $serving * self::SERVING_PER_STAFF];
        }
        if ($dishwashing > 0) {
            $lines[] = ['label' => 'Dishwashing', 'detail' => $dishwashing.' × ₱'.number_format(self::DISHWASHING_PER_STAFF), 'amount' => (float) $dishwashing * self::DISHWASHING_PER_STAFF];
        }

        $difference = round($storedTotal - array_sum(array_column($lines, 'amount')), 2);
        if ($difference != 0) {
            $lines[] = ['label' => 'Package rate adjustment', 'detail' => 'Vendor package pricing', 'amount' => $difference];
        }

        return $lines;
    }

    /** Values the booking form's JavaScript uses for the live estimate. */
    public static function forJavaScript(): array
    {
        return [
            'decoration' => self::DECORATION,
            'serving' => self::SERVING_PER_STAFF,
            'dishwashing' => self::DISHWASHING_PER_STAFF,
            'reservationRate' => self::RESERVATION_RATE,
        ];
    }
}
