<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\AdminMenuSetting;

class ReorganizeReportsMenuSeeder extends Seeder
{
    public function run(): void
    {
        // Update Room Reports group (moved from Reports)
        AdminMenuSetting::where('menu_key', 'room_reports')->update([
            'group_name' => 'Room Reports',
            'order' => 60
        ]);
        
        AdminMenuSetting::where('menu_key', 'advance_reports')->update([
            'group_name' => 'Room Reports',
            'order' => 61
        ]);
        
        AdminMenuSetting::where('menu_key', 'unpaid_reports')->update([
            'group_name' => 'Room Reports',
            'order' => 62
        ]);
        
        AdminMenuSetting::where('menu_key', 'combined_reports')->update([
            'group_name' => 'Room Reports',
            'order' => 63
        ]);
        
        // Update Convention Reports group (moved from Reports)
        AdminMenuSetting::where('menu_key', 'convention_reports')->update([
            'group_name' => 'Convention Reports',
            'order' => 64
        ]);
        
        $this->command->info('Menu groups updated successfully!');
    }
}
