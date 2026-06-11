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
        'dashboard_mode', // 'resort', 'convention', or 'both'
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

    /**
     * Check if user has resort access
     */
    public function hasResortAccess(): bool
    {
        if ($this->isAdmin() || $this->role === 'owner') {
            return true;
        }
        
        $permissions = $this->permissions ?? [];
        $resortPermissions = ['rooms', 'room_types', 'search_book_room', 'all_bookings'];
        
        return !empty(array_intersect($resortPermissions, $permissions));
    }

    /**
     * Check if user has convention access
     */
    public function hasConventionAccess(): bool
    {
        if ($this->isAdmin() || $this->role === 'owner') {
            return true;
        }
        
        $permissions = $this->permissions ?? [];
        $conventionPermissions = ['search_book_hall', 'all_hall_bookings', 'manage_halls'];
        
        return !empty(array_intersect($conventionPermissions, $permissions));
    }

    /**
     * Get current dashboard mode
     */
    public function getDashboardMode(): string
    {
        // If user has specific dashboard_mode set, use it
        if ($this->dashboard_mode && in_array($this->dashboard_mode, ['resort', 'convention'])) {
            // But only if they have access to that mode
            if ($this->dashboard_mode === 'resort' && $this->hasResortAccess()) {
                return 'resort';
            }
            if ($this->dashboard_mode === 'convention' && $this->hasConventionAccess()) {
                return 'convention';
            }
        }
        
        // Default: if has both, default to resort
        if ($this->hasResortAccess() && $this->hasConventionAccess()) {
            return $this->dashboard_mode ?? 'resort';
        }
        
        // Only resort access
        if ($this->hasResortAccess()) {
            return 'resort';
        }
        
        // Only convention access
        if ($this->hasConventionAccess()) {
            return 'convention';
        }
        
        // Fallback
        return 'resort';
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
     * Check if user is owner
     */
    public function isOwner(): bool
    {
        return $this->role === 'owner';
    }

    /**
     * Check if user can approve discounts
     */
    public function canApproveDiscounts(): bool
    {
        return in_array($this->role, ['owner', 'superadmin', 'admin']);
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
