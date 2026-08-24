<?php

namespace App\Providers;

use Illuminate\Pagination\Paginator;
use Illuminate\Support\Facades\Blade;
use Illuminate\Support\ServiceProvider;
use App\Models\Department;
use Illuminate\Support\Facades\View;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     *
     * @return void
     */
    public function register()
    {
        //
    }

    /**
     * Bootstrap any application services.
     *
     * @return void
     */
    public function boot()
    {
        Paginator::useBootstrap();

        Blade::directive('svg', function ($value) {
            return "
                <?php
                    echo $value + 1;
                ?>
            ";
        });
       // Only load departments when rendering the frontend navigation
           View::composer('layouts.front.top', function ($view) {
            $view->with('departments', Department::all());
        });
    }
   
}
