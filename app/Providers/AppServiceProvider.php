<?php

namespace App\Providers;

use App\Models\SiteAnnouncement;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\View;
use Illuminate\Support\ServiceProvider;

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
        Schema::defaultStringLength(191);

        View::composer('partials.figma-header', function ($view) {
            $headerAnnouncements = collect();
            if (Schema::hasTable('site_announcements')) {
                $headerAnnouncements = SiteAnnouncement::forHeader();
            }
            $view->with('headerAnnouncements', $headerAnnouncements);
        });
    }
}
