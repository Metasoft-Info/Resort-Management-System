<?php

namespace Database\Seeders;

use App\Models\AdminMenuSetting;
use Illuminate\Database\Seeder;

class AdminMenuSettingSeeder extends Seeder
{
    public function run(): void
    {
        AdminMenuSetting::seedDefaultMenus();
    }
}
