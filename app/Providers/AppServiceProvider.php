<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use Illuminate\Support\Facades\View;
use App\Models\Setting;

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
        View::composer('*', function ($view) {
            $setting = Setting::first();

            $base_config = [
                'googleTm'     => $setting->col_google_tm ?? '',
                'jivochat'     => (bool) $setting->col_jivochat ?? false,
                'jivochat_id'  => $setting->col_jivochat_id ?? '',
            ];

            $view->with('base_config', $base_config);
        });
    }
}
