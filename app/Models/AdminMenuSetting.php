<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class AdminMenuSetting extends Model
{
    protected $fillable = [
        'menu_key',
        'menu_label',
        'menu_icon',
        'route_name',
        'route_pattern',
        'group_name',
        'order',
        'is_active',
        'is_system',
    ];

    protected $casts = [
        'is_active' => 'boolean',
        'is_system' => 'boolean',
    ];

    /**
     * Get all active menus grouped by group_name
     */
    public static function getActiveMenus()
    {
        return static::where('is_active', true)
            ->orderBy('order')
            ->get()
            ->groupBy('group_name');
    }

    /**
     * Get all menus grouped by group_name
     */
    public static function getAllMenusGrouped()
    {
        return static::orderBy('order')
            ->get()
            ->groupBy('group_name');
    }

    /**
     * Get menus accessible by a user based on their permissions
     */
    public static function getMenusForUser($user)
    {
        $activeMenus = static::where('is_active', true)
            ->orderBy('order')
            ->get();

        // Superadmin, admin, and owner see all active menus
        if (in_array($user->role, ['superadmin', 'admin', 'owner'])) {
            return $activeMenus->groupBy('group_name');
        }

        // Filter by user permissions
        $userPermissions = $user->permissions ?? [];
        
        return $activeMenus->filter(function ($menu) use ($userPermissions) {
            return in_array($menu->menu_key, $userPermissions) || $menu->menu_key === 'dashboard';
        })->groupBy('group_name');
    }

    /**
     * Seed default menus
     */
    public static function seedDefaultMenus()
    {
        $menus = [
            ['menu_key' => 'dashboard', 'menu_label' => 'Dashboard', 'menu_icon' => 'fas fa-chart-line', 'route_name' => 'admin.dashboard', 'route_pattern' => 'admin.dashboard', 'group_name' => null, 'order' => 1, 'is_system' => true],
            ['menu_key' => 'todays_summary', 'menu_label' => "Today's Summary", 'menu_icon' => 'fas fa-calendar-day', 'route_name' => 'admin.todays-summary', 'route_pattern' => 'admin.todays-summary', 'group_name' => null, 'order' => 2, 'is_system' => false],
            
            ['menu_key' => 'rooms', 'menu_label' => 'Rooms', 'menu_icon' => 'fas fa-bed', 'route_name' => 'admin.rooms.index', 'route_pattern' => 'admin.rooms.*', 'group_name' => 'Rooms Management', 'order' => 10, 'is_system' => false],
            ['menu_key' => 'room_types', 'menu_label' => 'Room Types', 'menu_icon' => 'fas fa-door-open', 'route_name' => 'admin.room-types.index', 'route_pattern' => 'admin.room-types.*', 'group_name' => 'Rooms Management', 'order' => 11, 'is_system' => false],
            
            ['menu_key' => 'search_book_room', 'menu_label' => 'Search & Book', 'menu_icon' => 'fas fa-search-plus', 'route_name' => 'admin.premium-booking.index', 'route_pattern' => 'admin.premium-booking.*', 'group_name' => 'Room Bookings', 'order' => 20, 'is_system' => false],
            ['menu_key' => 'all_bookings', 'menu_label' => 'All Bookings', 'menu_icon' => 'fas fa-list', 'route_name' => 'admin.bookings.index', 'route_pattern' => 'admin.bookings.*', 'group_name' => 'Room Bookings', 'order' => 21, 'is_system' => false],
            
            ['menu_key' => 'search_book_hall', 'menu_label' => 'Search & Book Hall', 'menu_icon' => 'fas fa-search-plus', 'route_name' => 'admin.premium-convention.index', 'route_pattern' => 'admin.premium-convention.*', 'group_name' => 'Convention Halls', 'order' => 30, 'is_system' => false],
            ['menu_key' => 'all_hall_bookings', 'menu_label' => 'All Hall Bookings', 'menu_icon' => 'fas fa-list', 'route_name' => 'admin.convention-bookings.index', 'route_pattern' => 'admin.convention-bookings.*', 'group_name' => 'Convention Halls', 'order' => 31, 'is_system' => false],
            ['menu_key' => 'manage_halls', 'menu_label' => 'Manage Halls', 'menu_icon' => 'fas fa-building', 'route_name' => 'admin.convention-halls.index', 'route_pattern' => 'admin.convention-halls.*', 'group_name' => 'Convention Halls', 'order' => 32, 'is_system' => false],
            
            ['menu_key' => 'addon_services', 'menu_label' => 'Addon Services', 'menu_icon' => 'fas fa-plus-circle', 'route_name' => 'admin.addon-services.index', 'route_pattern' => 'admin.addon-services.*', 'group_name' => 'Services', 'order' => 40, 'is_system' => false],
            ['menu_key' => 'food_packages', 'menu_label' => 'Food Packages', 'menu_icon' => 'fas fa-utensils', 'route_name' => 'admin.food-packages.index', 'route_pattern' => 'admin.food-packages.*', 'group_name' => 'Services', 'order' => 41, 'is_system' => false],
            ['menu_key' => 'extra_charge_categories', 'menu_label' => 'Extra Charges', 'menu_icon' => 'fas fa-tags', 'route_name' => 'admin.extra-charge-categories.index', 'route_pattern' => 'admin.extra-charge-categories.*', 'group_name' => 'Services', 'order' => 42, 'is_system' => false],
            
            ['menu_key' => 'hero_slides', 'menu_label' => 'Hero Slides', 'menu_icon' => 'fas fa-images', 'route_name' => 'admin.hero-slides.index', 'route_pattern' => 'admin.hero-slides.*', 'group_name' => 'Website', 'order' => 50, 'is_system' => false],
            
            ['menu_key' => 'room_reports', 'menu_label' => 'Room Bookings Report', 'menu_icon' => 'fas fa-file-alt', 'route_name' => 'admin.reports.room-bookings', 'route_pattern' => 'admin.reports.room-bookings', 'group_name' => 'Reports', 'order' => 60, 'is_system' => false],
            ['menu_key' => 'convention_reports', 'menu_label' => 'Convention Bookings Report', 'menu_icon' => 'fas fa-chart-bar', 'route_name' => 'admin.reports.convention-bookings', 'route_pattern' => 'admin.reports.convention-bookings', 'group_name' => 'Reports', 'order' => 61, 'is_system' => false],
            
            ['menu_key' => 'users', 'menu_label' => 'User Management', 'menu_icon' => 'fas fa-users', 'route_name' => 'admin.users.index', 'route_pattern' => 'admin.users.*', 'group_name' => 'System', 'order' => 70, 'is_system' => true],
            ['menu_key' => 'activity_logs', 'menu_label' => 'Activity Logs', 'menu_icon' => 'fas fa-history', 'route_name' => 'admin.activity-logs.index', 'route_pattern' => 'admin.activity-logs.*', 'group_name' => 'System', 'order' => 71, 'is_system' => false],
            ['menu_key' => 'settings', 'menu_label' => 'Settings', 'menu_icon' => 'fas fa-cog', 'route_name' => 'admin.settings.index', 'route_pattern' => 'admin.settings.*', 'group_name' => 'System', 'order' => 72, 'is_system' => true],
        ];

        foreach ($menus as $menu) {
            static::updateOrCreate(
                ['menu_key' => $menu['menu_key']],
                array_merge($menu, ['is_active' => true])
            );
        }
    }
}
