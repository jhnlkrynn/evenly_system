<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    /** Password for every seeded account. */
    public const PASSWORD = 'password123';

    public function run(): void
    {
        self::account('Super Admin', 'superadmin@eventlynk.com', 'superadmin');
        self::account('Owner / Admin', 'admin@eventlynk.com', 'admin');

        $this->call([
            ReferenceDataSeeder::class,
            VendorSeeder::class,
            BookingSeeder::class,
        ]);
    }

    /** Create a verified, active login. */
    public static function account(string $name, string $email, string $role, ?string $phone = null, bool $active = true): User
    {
        $user = new User;
        $user->forceFill([
            'name' => $name,
            'email' => $email,
            'phone' => $phone,
            'password' => self::PASSWORD,
            'role' => $role,
            'is_active' => $active,
            'email_verified_at' => now(),
        ])->save();

        return $user;
    }
}
