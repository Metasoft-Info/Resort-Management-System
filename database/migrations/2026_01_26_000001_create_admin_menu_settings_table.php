<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('admin_menu_settings', function (Blueprint $table) {
            $table->id();
            $table->string('menu_key')->unique(); // e.g., 'dashboard', 'rooms', 'bookings'
            $table->string('menu_label'); // Display name
            $table->string('menu_icon')->default('fas fa-circle'); // Font Awesome icon
            $table->string('route_name'); // Laravel route name
            $table->string('route_pattern')->nullable(); // For highlighting (e.g., 'admin.rooms.*')
            $table->string('group_name')->nullable(); // Group header (e.g., 'Room Management')
            $table->integer('order')->default(0);
            $table->boolean('is_active')->default(true); // Can be disabled by superadmin
            $table->boolean('is_system')->default(false); // System menus can't be deleted
            $table->timestamps();
        });

        // Add permissions column to users if not exists
        if (!Schema::hasColumn('users', 'permissions')) {
            Schema::table('users', function (Blueprint $table) {
                $table->json('permissions')->nullable()->after('role');
            });
        }
        
        // Add logo fields to resort_info
        if (!Schema::hasColumn('resort_info', 'header_logo')) {
            Schema::table('resort_info', function (Blueprint $table) {
                $table->string('header_logo')->nullable();
                $table->string('footer_logo')->nullable();
                $table->string('favicon')->nullable();
                $table->string('admin_logo')->nullable();
            });
        }
    }

    public function down(): void
    {
        Schema::dropIfExists('admin_menu_settings');
        
        if (Schema::hasColumn('resort_info', 'header_logo')) {
            Schema::table('resort_info', function (Blueprint $table) {
                $table->dropColumn(['header_logo', 'footer_logo', 'favicon', 'admin_logo']);
            });
        }
    }
};
