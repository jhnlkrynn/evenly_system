<?php

namespace Database\Seeders;

use App\Models\Barangay;
use App\Models\ServiceCategory;
use App\Support\MockData;
use Illuminate\Database\Seeder;

/** Service categories and Nasugbu barangays (lookup lists). */
class ReferenceDataSeeder extends Seeder
{
    public const CATEGORIES = [
        'Decoration' => '🌸',
        'Serving Staff' => '🍽',
        'Dishwashing' => '🫧',
        'Catering' => '🥘',
        'Photography' => '📸',
        'Audio/Lights' => '🔊',
        'Full Package' => '🎁',
    ];

    public function run(): void
    {
        foreach (self::CATEGORIES as $name => $icon) {
            ServiceCategory::create(['name' => $name, 'icon' => $icon]);
        }

        foreach (MockData::BARANGAYS as $name) {
            Barangay::create(['name' => $name]);
        }
    }
}
