<?php

namespace App\Providers;

use Illuminate\Support\Facades\View;
use Illuminate\Support\ServiceProvider;
use Illuminate\Support\Facades\DB;

class ViewServiceProvider extends ServiceProvider
{
    public function boot(): void
    {
        // Глобально подключаем showreel ко всем Blade-шаблонам
        View::composer('*', function ($view) {
            $row = DB::table('home_projects_grid')
                ->where('row_number', 0)
                ->where('col_number', 0)
                ->first();

            $media = $row ? json_decode($row->media, true) : null;
            $view->with('showreel', $media);
        });
    }
}

