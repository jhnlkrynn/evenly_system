<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
use Database\Factories\UserFactory;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

class User extends Authenticatable
{
    /** @use HasFactory<UserFactory> */
    use HasFactory, Notifiable;

    public const ROLES = ['customer', 'vendor', 'admin', 'superadmin'];

    /** Dashboard route for each role (routes/web.php). */
    public const DASHBOARD_ROUTES = [
        'customer' => 'customer.dashboard',
        'vendor' => 'vendor.dashboard',
        'admin' => 'admin.dashboard',
        'superadmin' => 'superadmin.dashboard',
    ];

    /** Roles a visitor can pick when signing up (shown with the RoleSelector design). */
    public const REGISTRATION_ROLES = [
        ['id' => 'customer', 'icon' => '🎉', 'label' => 'Customer', 'desc' => 'Book services for my events'],
        ['id' => 'vendor', 'icon' => '🏪', 'label' => 'Vendor', 'desc' => 'List my event business'],
    ];

    protected $fillable = [
        'name',
        'email',
        'phone',
        'password',
        'role',
        'is_active',
    ];

    protected $hidden = [
        'password',
        'remember_token',
    ];

    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'password' => 'hashed',
            'is_active' => 'boolean',
        ];
    }

    /** Business profile, for users with the vendor role. */
    public function vendor(): HasOne
    {
        return $this->hasOne(Vendor::class);
    }

    /** Bookings made as a customer. */
    public function bookings(): HasMany
    {
        return $this->hasMany(Booking::class, 'customer_id');
    }

    public function reviews(): HasMany
    {
        return $this->hasMany(Review::class, 'customer_id');
    }

    public function activityLogs(): HasMany
    {
        return $this->hasMany(ActivityLog::class);
    }

    public function hasRole(string ...$roles): bool
    {
        return in_array($this->role, $roles, true);
    }

    public function dashboardUrl(): string
    {
        return route(self::DASHBOARD_ROUTES[$this->role] ?? 'home');
    }

    /**
     * False when the URL sits in another role's area (/customer, /vendor, /admin, /superadmin),
     * so a stale "intended" URL can't bounce someone into a 403 after logging in.
     */
    public function canVisit(string $url): bool
    {
        $area = explode('/', trim((string) parse_url($url, PHP_URL_PATH), '/'))[0];

        return ! in_array($area, self::ROLES, true) || $area === $this->role;
    }

    /** "Maria Santos" → "MS" */
    public function initials(): string
    {
        preg_match_all('/\b\p{L}/u', $this->name, $letters);

        return mb_strtoupper(implode('', array_slice($letters[0], 0, 2))) ?: '?';
    }

    public function scopeRole(Builder $query, string $role): void
    {
        $query->where('role', $role);
    }
}
