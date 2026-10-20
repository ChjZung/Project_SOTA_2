<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
    }

    public function boot(): void
    {
        \Illuminate\Support\Facades\View::composer(['components.header','components.footer','components.storefront-drawers','pages.checkout','pages.order-success','pages.contact'], function ($view) {
            $view->with('storeSettings', \App\Models\Setting::pluck('value','key')->all());
            $view->with('storeCategories', \App\Models\Category::orderBy('sort_order')->get());
        });
    }

}
