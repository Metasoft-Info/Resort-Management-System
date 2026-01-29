<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use Illuminate\Support\Facades\View;
use Illuminate\Support\Facades\Cache;
use App\Models\ResortInfo;
use App\Models\NavbarLink;
use App\Models\FooterSection;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        //
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        // Share resort info, navbar links, and footer sections with all views (cached for 1 hour)
        View::composer('*', function ($view) {
            $resortInfo = Cache::remember('global_resort_info', 3600, function () {
                return ResortInfo::first();
            });
            
            $navbarLinks = Cache::remember('global_navbar_links', 3600, function () {
                return NavbarLink::where('is_active', true)->orderBy('order')->get();
            });
            
            $footerSections = Cache::remember('global_footer_sections', 3600, function () {
                return FooterSection::with('links')
                    ->where('is_active', true)
                    ->orderBy('order')
                    ->get();
            });
            
            $view->with([
                'resortInfo' => $resortInfo,
                'navbarLinks' => $navbarLinks,
                'footerSections' => $footerSections
            ]);
        });
    }
}
