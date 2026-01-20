<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use Illuminate\Support\Facades\View;
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
        // Share resort info, navbar links, and footer sections with all views
        View::composer('*', function ($view) {
            $resortInfo = ResortInfo::first();
            $navbarLinks = NavbarLink::where('is_active', true)->orderBy('order')->get();
            $footerSections = FooterSection::with('links')
                ->where('is_active', true)
                ->orderBy('order')
                ->get();
            
            $view->with([
                'resortInfo' => $resortInfo,
                'navbarLinks' => $navbarLinks,
                'footerSections' => $footerSections
            ]);
        });
    }
}
