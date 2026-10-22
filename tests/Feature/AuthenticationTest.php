<?php

namespace Tests\Feature;

use App\Models\ActivityLog;
use App\Models\User;
use App\Models\Vendor;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Hash;
use PHPUnit\Framework\Attributes\DataProvider;
use Tests\TestCase;

class AuthenticationTest extends TestCase
{
    use RefreshDatabase;

    public static function roleDashboards(): array
    {
        return [
            'customer' => ['customer', '/customer/dashboard'],
            'vendor' => ['vendor', '/vendor/dashboard'],
            'admin' => ['admin', '/admin'],
            'superadmin' => ['superadmin', '/superadmin'],
        ];
    }

    private function account(string $role, array $attributes = []): User
    {
        $user = User::factory()->role($role)->create(['password' => 'password123', ...$attributes]);

        if ($role === 'vendor') {
            Vendor::factory()->for($user)->create();
        }

        return $user;
    }

    #[DataProvider('roleDashboards')]
    public function test_each_role_is_sent_to_its_dashboard_after_login(string $role, string $dashboard): void
    {
        $user = $this->account($role);

        $this->post('/login', ['email' => $user->email, 'password' => 'password123'])
            ->assertRedirect($dashboard);

        $this->assertAuthenticatedAs($user);
        $this->get($dashboard)->assertOk();
        $this->assertDatabaseHas('activity_logs', ['user_id' => $user->id, 'action' => 'login']);
    }

    #[DataProvider('roleDashboards')]
    public function test_other_roles_are_forbidden_from_a_dashboard(string $role, string $dashboard): void
    {
        foreach (array_diff(User::ROLES, [$role]) as $other) {
            $this->actingAs($this->account($other))->get($dashboard)->assertForbidden();
        }
    }

    public function test_guests_are_sent_to_login(): void
    {
        $this->get('/customer/dashboard')->assertRedirect('/login');
        $this->get('/profile')->assertRedirect('/login');
    }

    public function test_wrong_password_is_rejected(): void
    {
        $user = $this->account('customer');

        $this->from('/login')->post('/login', ['email' => $user->email, 'password' => 'wrong-password'])
            ->assertRedirect('/login')
            ->assertSessionHasErrors(['email' => __('auth.failed')]);

        $this->assertGuest();
    }

    public function test_deactivated_users_cannot_log_in(): void
    {
        $user = $this->account('customer', ['is_active' => false]);

        $this->from('/login')->post('/login', ['email' => $user->email, 'password' => 'password123'])
            ->assertRedirect('/login')
            ->assertSessionHasErrors(['email' => __('auth.deactivated')]);

        $this->assertGuest();
    }

    public function test_users_deactivated_mid_session_are_signed_out(): void
    {
        $user = $this->account('customer');
        $this->actingAs($user);
        $user->update(['is_active' => false]);

        $this->get('/customer/dashboard')->assertRedirect('/login');
        $this->assertGuest();
    }

    public function test_intended_url_in_another_roles_area_is_ignored(): void
    {
        $user = $this->account('customer');

        $this->get('/admin')->assertRedirect('/login');
        $this->post('/login', ['email' => $user->email, 'password' => 'password123'])
            ->assertRedirect('/customer/dashboard');
    }

    public function test_logout_is_logged_and_returns_home(): void
    {
        $user = $this->account('vendor');

        $this->actingAs($user)->post('/logout')->assertRedirect('/');

        $this->assertGuest();
        $this->assertDatabaseHas('activity_logs', ['user_id' => $user->id, 'action' => 'logout']);
    }

    public function test_customer_can_register(): void
    {
        $this->post('/register', [
            'name' => 'Juan dela Cruz',
            'email' => 'Juan@Example.com',
            'phone' => '0917 123 4567',
            'password' => 'password123',
            'password_confirmation' => 'password123',
            'role' => 'customer',
            'terms' => '1',
        ])->assertRedirect('/customer/dashboard');

        $user = User::where('email', 'juan@example.com')->firstOrFail();
        $this->assertSame('customer', $user->role);
        $this->assertSame('09171234567', $user->phone);
        $this->assertTrue(Hash::check('password123', $user->password));
        $this->assertNull($user->vendor);
        $this->assertAuthenticatedAs($user);
    }

    public function test_vendor_registration_creates_an_unverified_vendor(): void
    {
        $this->post('/register', [
            'name' => 'Ana Reyes',
            'email' => 'ana@events.ph',
            'phone' => '+63 918 765 4321',
            'password' => 'password123',
            'password_confirmation' => 'password123',
            'role' => 'vendor',
            'terms' => '1',
        ])->assertRedirect('/vendor/dashboard');

        $user = User::where('email', 'ana@events.ph')->firstOrFail();
        $this->assertSame('vendor', $user->role);
        $this->assertSame('09187654321', $user->phone);
        $this->assertFalse($user->vendor->is_verified);
        $this->assertSame('Ana Reyes', $user->vendor->business_name);
    }

    public function test_registration_is_validated(): void
    {
        User::factory()->create(['email' => 'taken@email.com']);

        $this->from('/register')->post('/register', [
            'name' => '',
            'email' => 'taken@email.com',
            'phone' => '12345',
            'password' => 'short',
            'password_confirmation' => 'different',
            'role' => 'admin',
        ])->assertRedirect('/register')->assertSessionHasErrors(['name', 'email', 'phone', 'password', 'role', 'terms']);

        $this->assertDatabaseCount('users', 1);
    }

    public function test_profile_name_and_phone_can_be_updated(): void
    {
        $user = $this->account('customer');

        $this->actingAs($user)->from('/profile')
            ->put('/profile', ['name' => 'Maria S. Reyes', 'phone' => '0917-000-1111'])
            ->assertRedirect('/profile')
            ->assertSessionHas('profile_status');

        $this->assertSame('Maria S. Reyes', $user->fresh()->name);
        $this->assertSame('09170001111', $user->fresh()->phone);

        $this->from('/profile')->put('/profile', ['name' => 'X', 'phone' => '9999'])
            ->assertSessionHasErrorsIn('profile', ['phone']);
    }

    public function test_password_can_be_changed_with_the_current_password(): void
    {
        $user = $this->account('admin');

        $this->actingAs($user)->from('/profile')->put('/profile/password', [
            'current_password' => 'not-it',
            'password' => 'newpassword1',
            'password_confirmation' => 'newpassword1',
        ])->assertSessionHasErrorsIn('password', ['current_password']);

        $this->from('/profile')->put('/profile/password', [
            'current_password' => 'password123',
            'password' => 'newpassword1',
            'password_confirmation' => 'newpassword1',
        ])->assertRedirect('/profile')->assertSessionHas('password_status');

        $this->assertTrue(Hash::check('newpassword1', $user->fresh()->password));
        $this->assertSame(1, ActivityLog::where(['user_id' => $user->id, 'action' => 'password_changed'])->count());
    }

    public function test_landing_navbar_shows_the_user_menu_when_signed_in(): void
    {
        $this->get('/')->assertSee('Sign up free')->assertDontSee('user-menu-toggle');

        $user = $this->account('customer', ['name' => 'Rica Tan']);
        $this->actingAs($user)->get('/')
            ->assertSee('Rica Tan')
            ->assertSee('/customer/dashboard')
            ->assertSee('/profile')
            ->assertDontSee('Sign up free');
    }

    public function test_signed_in_users_visiting_login_go_to_their_dashboard(): void
    {
        $this->actingAs($this->account('admin'))->get('/login')->assertRedirect('/admin');
    }
}
