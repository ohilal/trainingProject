<?php

namespace App\Providers;

use Illuminate\Pagination\Paginator;
use Illuminate\Support\Facades\Blade;
use Illuminate\Support\ServiceProvider;
use App\Models\Department;

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
       // Only load departments when rendering the specific frontend layout
        // Replace 'contents.front.layouts.app' with the actual path to your top.blade.php's parent layout
        View::composer('contents.front.layouts.app', function ($view) {
            $view->with('departments', Department::all());  

    }
   
}
