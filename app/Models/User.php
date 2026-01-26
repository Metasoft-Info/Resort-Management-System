<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

class User extends Authenticatable
{
    /** @use HasFactory<\Database\Factories\UserFactory> */
    use HasFactory, Notifiable;

    /**
     * The attributes that are mass assignable.
     *
     * @var list<string>
     */
    protected $fillable = [
        'name',
        'email',
        'password',
        'role',
        'permissions',
        'is_active',
    ];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var list<string>
     */
    protected $hidden = [
        'password',
        'remember_token',
    ];

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'password' => 'hashed',
            'permissions' => 'array',
            'is_active' => 'boolean',
        ];
    }

    public function bookings()
    {
        return $this->hasMany(Booking::class, 'created_by_id');
    }

    public function conventionBookings()
    {
        return $this->hasMany(ConventionBooking::class, 'created_by_id');
    }

    public function activityLogs()
    {
        return $this->hasMany(ActivityLog::class);
    }

    /**
     * Check if user is superadmin
     */
    public function isSuperAdmin(): bool
    {
        return $this->role === 'superadmin';
    }

    /**
     * Check if user is admin or superadmin
     */
    public function isAdmin(): bool
    {
        return in_array($this->role, ['admin', 'superadmin']);
    }

    /**
     * Check if user has permission to access a menu
     */
    public function hasMenuPermission(string $menuKey): bool
    {
        // Superadmin and admin have all permissions
        if ($this->isAdmin()) {
            return true;
        }

        // Dashboard is always accessible
        if ($menuKey === 'dashboard') {
            return true;
        }

        $permissions = $this->permissions ?? [];
        return in_array($menuKey, $permissions);
    }

    /**
     * Get all menu keys user has access to
     */
    public function getMenuPermissions(): array
    {
        if ($this->isAdmin()) {
            return AdminMenuSetting::where('is_active', true)->pluck('menu_key')->toArray();
        }

        return $this->permissions ?? [];
    }

    /**
     * Set menu permissions for user
     */
    public function setMenuPermissions(array $menuKeys): void
    {
        $this->permissions = $menuKeys;
        $this->save();
    }
}
